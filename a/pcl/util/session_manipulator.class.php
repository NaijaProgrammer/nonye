<?php
/*
* @author Michael Orji
*/
class SessionManipulator
{
	public static function session_started()
	{
		/*if( class_exists('SessionExtended') )
		{
			return SessionExtended::session_already_started();
		}*/
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
		return true;
	}

	public static function start_session()
	{
		if(!self::session_started())
		{
			session_start();
		}
		else
		{
			session_regenerate_id();
		}
	}

	public static function in_session_array($key)
	{
		return self::get_session_value($key) !== false;
	}

	public static function get_session_variable($key)
	{
		if(isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		return false;
	}

	public static function get_session_variables()
	{
		return $_SESSION;
	}

	public static function set_session_variables($arr)
	{
		foreach($arr AS $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}

	public static function unset_session_variables($arr)
	{
		if(count($arr) < 1)
		{
			$_SESSION = array();
			session_destroy();
			return;
		}
		foreach($arr AS $key)
		{
			if(isset($_SESSION[$key]))
			{
				unset($_SESSION[$key]);
			}
		}
	}
	
	public static function set_session_values($arr)
	{
		self::set_session_variables($arr);
	}
	
	public static function get_session_values()
	{
		return self::get_session_variables();
	}
	
	public static function unset_session_values($arr)
	{
		return self::unset_session_variables($arr);
	}
	
	public static function get_session_value($key)
	{
		return self::get_session_variable($key);
	}
}