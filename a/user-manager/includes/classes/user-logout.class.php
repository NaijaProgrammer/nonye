<?php

class UserLogout
{
	public static function logout($opts = array())
	{

		$login_id = isset($opts['login_id']) ? $opts['login_id'] : SessionManipulator::get_session_value('login_id');
		UserAuthentication::unset_auto_login_cookie();
		UserAuthentication::unset_auto_login_session_cookie();
		self::register_logout($login_id, $opts);
		SessionManipulator::unset_session_values(array());

		$lst = ( isset($_opts['logout_session_token']) ? $_opts['logout_session_token'] : '' );
		if( $lst )
		{
			$_SESSION[$lst] = true;
		}
	}
	
	private static function register_logout($login_id, $opts = array())
	{ 
		if(!$login_id)
		{ 
			return;
		}

		$logout_page = isset($opts['logout_page']) ? $opts['logout_page'] : '';
		$logout_type = isset($opts['logout_type']) ? $opts['logout_type'] : 1; 
 
		$sql = "INSERT INTO ". UserManager::get_tables_prefix(). "user_logouts SET ".
           "login_id = '".     DataSanitizer::sanitize_data_for_db_query($login_id). "', ".
           "logout_page  = '". DataSanitizer::sanitize_data_for_db_query($logout_page). "', ".
           "logout_type  = '". DataSanitizer::sanitize_data_for_db_query($logout_type). "', ".
           "logout_time  =  NOW()";

		$query = mysql_query($sql);
	}
}