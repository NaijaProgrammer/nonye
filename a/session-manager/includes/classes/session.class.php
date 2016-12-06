<?php
/**
* enable (backward) compatibility for scripts written using mysql
*/

$connection_id = @mysql_connect(DB_SERVER, DB_USER, DB_PASS);
if($connection_id) 
{ 
	@mysql_select_db(DB_NAME); 
}

class Session
{
	private $life_time; 
 
	public static function get_db_object()
	{
		return Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}

	public function __construct($gc_maxlifetime = "", $gc_probability = "", $gc_divisor = "", $securityCode = "sEcUr1tY_c0dE")
	{
		if ($gc_maxlifetime != "" && is_integer($gc_maxlifetime))
		{
			@ini_set('session.gc_maxlifetime', $gc_maxlifetime);
		}
		
		if ($gc_probability != "" && is_integer($gc_probability))
		{
			@ini_set('session.gc_probability', $gc_probability);
		}
		
		if ($gc_divisor != "" && is_integer($gc_divisor))
		{
			@ini_set('session.gc_divisor', $gc_divisor);
		}

		$this->life_time    = ini_get("session.gc_maxlifetime"); // get session lifetime
		$this->securityCode = $securityCode; // we'll use this later on in order to try to prevent HTTP_USER_AGENT spoofing
        
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc') 
		);

		register_shutdown_function('session_write_close');
		//session_start();

	}

	//regenerates the sessionid
	public function refresh()
	{
		$old_id = session_id();
		session_regenerate_id();
		$this->destroy($old_id);
	}

	public function open($save_path, $sess_name)
	{
		return true;
	}

	public function close()
	{
		$this->gc();   
		return true;
	}

	public function read($sess_id)
	{
		$sql_dbo = MySqlExtended::get_instance();
	
		$sql = "SELECT * FROM ". self::get_tables_prefix(). "sessions ".
           "WHERE id      = '". DataSanitizer::sanitize_data_for_db_query($sess_id)."' ".
           "AND useragent = '". DataSanitizer::sanitize_data_for_db_query(md5($_SERVER["HTTP_USER_AGENT"]. $this->securityCode)). "' ".
           "AND expiry    > '". time(). "' ".
           "LIMIT 1";
		   
		$result = mysql_query($sql);

		if( ($result) && (mysql_num_rows($result) > 0) )
		{
			$sess_info = mysql_fetch_array($result);
			return $this->unserialize_data($sess_info['data']);
		}   
   
		return "";
	}

	public function write($sess_id, $sess_data)
	{
		if(!isset($this->start_time))
		{
			$this->start_time = time();
		}

		$query = "INSERT INTO ". self::get_tables_prefix(). "sessions SET ".
             "id        = '". DataSanitizer::sanitize_data_for_db_query($sess_id). "', ".
             "data      = '". $this->serialize_data($sess_data)."', ".
             "useragent = '". DataSanitizer::sanitize_data_for_db_query(md5($_SERVER["HTTP_USER_AGENT"]. $this->securityCode))."', ". 
             "starttime = '". $this->start_time. "', ".
             "lastused  = '". time(). "', ".
             "expiry    = '". (time() + $this->life_time)."'".
              
             "ON DUPLICATE KEY UPDATE ".
             "data     = '". $this->serialize_data($sess_data). "', ".
             "lastused = '". time(). "', ".
             "expiry   = '". (time() + $this->life_time). "'"; //i.e, lastused + $this->life_time

		$insert = mysql_query($query);

		if($insert)
		{ 
			if(mysql_affected_rows() > 1)
			{ //row was updated
				return true;
			}
			
			else
			{ //row was inserted
				return "";
			}
		}
		
		return false;
	} 

	public function destroy($sess_id)
	{
		$query  = "DELETE FROM ". self::get_tables_prefix(). "sessions WHERE id = '".DataSanitizer::sanitize_data_for_db_query($sess_id)."'";
		
		$db_object = self::get_db_object();
		$db_object->execute_query($query);
		
		return $db_object->affected_rows() ? true : false;
	}

	//cleans up inactive, expired sessions
	public function gc( $garbage_collectible_time='' )
	{ 
		$garbage_collectible_time = !empty($garbage_collectible_time) ? $garbage_collectible_time : time();
		$query = "DELETE FROM ". self::get_tables_prefix(). "sessions WHERE expiry < ". $garbage_collectible_time. ""; 
		
		$db_object = self::get_db_object();
		$db_object->execute_query($query);
		
		return $db_object->affected_rows() ? true : false;
	} 
   
	protected function serialize_data($sess_data)
	{
		return base64_encode(serialize($sess_data));
	}
   
	protected function unserialize_data($sess_data)
	{
		return unserialize(base64_decode($sess_data));
	}
	
	protected static function get_tables_prefix()
	{
		return TABLES_PREFIX;
	}
}// end of class definition