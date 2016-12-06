<?php

class SessionExtended extends Session
{
	private static $instance = null;
	
	//returns the current session object instance
	public static function get_instance($gc_maxlifetime = "", $gc_probability = "", $gc_divisor = "", $securityCode = "sEcUr1tY_c0dE")
	{
		if(!isset(self::$instance))
		{
			self::$instance = new self($gc_maxlifetime, $gc_probability , $gc_divisor, $securityCode);
		}
    	return self::$instance;
	}

	public function __construct($gc_maxlifetime = "", $gc_probability = "", $gc_divisor = "", $securityCode = "sEcUr1tY_c0dE")
	{  
		parent::__construct($gc_maxlifetime, $gc_probability, $gc_divisor, $securityCode);

		/*
		* if -- else statement, added on Nov 1, 2012
		* if I start experiencing session errors, delete the statement
		* and uncomment 'session_start' in the parent class 'Session'
		*/
		//avoid conflict if session has previously been started, especially with 'session_start', (e.g start_session_if_not_yet_started()) before class has been initialised
		if(!self::session_already_started($securityCode))
		{
			session_start();
		}
		else
		{
			session_regenerate_id();
		}
	}

	/*
	* read session data of the currently active (non-expired) session
	* @credits: Onur Yerlikaya < http://www.witkey.org > 19-Jul-2006 03:21 
	* PHP MANUAL: session_encode (User Contributed Notes)
	* @date: Sept. 30, 2012
	*/
	public function read_current_session_data()
	{
		# boolean type : $_SESSION['logged']  = true;
		# string type  : $_SESSION['name']    = "Onur Yerlikaya";
		# integer type : $_SESSION['age']     = 17;
		//stored as: logged|b:1;name|s:14:"Onur Yerlikaya";age|i:17;
		
		/* Returned as :
		 Array(
			[logged] => 1
			[name] => Onur Yerlikaya
			[age] => 17
		 )
		*/

		$encodedData = session_encode();
		return $this->parse_session_string($encodedData);
	} 
	
	/*
	*
	*/
	public function read_values($key='', $expired = false)
	{
		$data  = array();
		$i     = 0;
		$sql   = "SELECT data FROM ". Session::get_tables_prefix(). "sessions WHERE expiry";
		$sql  .= ( ($expired) ?  " < " : " >= ");
		$sql  .= time();
		$query = mysql_query($sql);
    
		while($row = mysql_fetch_assoc($query))
		{  
			$sess_data = $row['data']; //returns a serialized, base 64 encoded stored session data (cf. parent::write() )
			$unserialized_data = $this->unserialize_data($sess_data); //returns a decoded, unserialized version of $data
			
			//if the unserialized session data is an object, then just decode it and use the resulting serialized string, else use the full unserialized data
			$arr = is_object($unserialized_data) ? $this->parse_session_string(base64_decode($sess_data)) : $this->parse_session_string( $unserialized_data );
			
			if( empty($key) )
			{
				$data[] = $arr;
			}
			
			else
			{
				$data[] = isset($arr[$key]) ? $arr[$key] : ''; 
			}
		}
    
		return $data;
	}

	/*
	* checks to see if (our custom ) session has already been started
	* Helps us avoid re-starting a session when one has already started
	* @date: Nov. 1, 2012
	* incase I start having errors, remove this function
	*/
	public static function session_already_started($security_code = "sEcUr1tY_c0dE")
	{
		if( version_compare(PHP_VERSION, '5.4.0', '>=') )
		{
			if ( session_status() == PHP_SESSION_NONE ) 
			{
				return false;
			}
			return true;
		}
		if( session_id() == "" )
		{
			return false;
		}
		if( (self::$instance == null) )
		{
			return false;
		}
		return true;
	}

	private function parse_session_string($str)
	{
		$explodeIt  = explode(";",$str); 
		$sessName   = array();
		$sessData   = array();

		for($i=0;$i<count($explodeIt)-1;$i++)
		{
			$sessGet = explode("|",$explodeIt[$i]);
        
			if(count($sessGet) > 1)
			{ //avoid the error of 'undefined offset 1'
				$sessName[$i] = $sessGet[0];
				if(substr($sessGet[1],0,2) == "s:")
				{
					$sessData[$i] = str_replace("\"","",strstr($sessGet[1],"\""));
				} 
				
				else 
				{
					$sessData[$i] = substr($sessGet[1],2);
				} 
			}
		}
      
		$result = array_combine($sessName,$sessData);
		return $result;
	}
}