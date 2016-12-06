<?php

class UserManagerSessionManager extends SessionExtended
{
	private static $instance = null;
	
	public static function get_instance($gc_maxlifetime = "", $gc_probability = "", $gc_divisor = "", $securityCode = "sEcUr1tY_c0dE")
	{
		$gc_maxlifetime = trim($gc_maxlifetime);
		$gc_maxlifetime = empty($gc_maxlifetime) ? 300 : $gc_maxlifetime;
		
		if( !isset(self::$instance) )
		{
			self::$instance = new self($gc_maxlifetime, $gc_probability , $gc_divisor, $securityCode);
		}
		
    	return self::$instance;
	}
	
	/*
	public function get_active_sessions()
	{
		return $this->read_values('login_id', false);
	}
	
	public function get_expired_sessions()
	{
		return $this->read_values('login_id', true);
	}
	*/
	
	/*
	* logs out expired sessions, 
	* then calls the parent function to do the normal garbage collection routine, to delete expired sessions
	*/
	public function handle_expired_sessions()
	{ 
		$this->_logout_expired_sessions();
	}

	private function _logout_expired_sessions()
	{
		$login_ids = $this->read_values('login_id', true);
	
		for($i = 0; $i < count($login_ids); $i++)
		{
			$this->_logout_expired_session($login_ids[$i]);
		}
	}
	
	private function _logout_expired_session($login_id)
	{ 
		if(!$login_id)
		{ 
			return;
		}

		$logout_page = ''; 
		$db_object   = UserManager::get_db_object(); //OR parent::/Session::get_db_object
		
		$db_object->insert_records(UserManager::get_tables_prefix(). "user_logouts", array(
			"login_id"    => $login_id,
			"logout_page" => $logout_page,
			"logout_type" => 2,
			"logout_time" => $db_object->sql_term("NOW()")
		));
	}
}