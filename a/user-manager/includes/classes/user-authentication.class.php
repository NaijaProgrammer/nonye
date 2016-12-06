<?php

//class userAuthentication
class UserAuthentication
{
	public static function user_exists($user_login)
	{
		return UserManager::user_exists($user_login);
	}

	public static function user_is_logged_in()
	{
		return isset($_SESSION['current_user_id']);
	}

	public static function is_verified_user($user_login, $pass)
	{
		$user_id = UserManager::get_user_id($user_login);  

		$sql = "SELECT id FROM ".   UserManager::get_tables_prefix(). "users ".
				"WHERE id = '".     DataSanitizer::sanitize_data_for_db_query($user_id). "' ".
				"AND password = '". DataSanitizer::sanitize_data_for_db_query(UserManager::hash_password($pass)) . "'";
		
		$db_object = self::_get_db_object();
		$db_object->execute_query($sql);
		return $db_object->num_rows() > 0;
		/*
		$query = mysql_query($sql);
		if(!$query){return false;}
		return (mysql_num_rows($query) == 1);
		*/
	}

	public static function set_auto_login_cookie($user_login, $password)
	{
		$expires = time() + 60 * 60 * 24 * 100;
		$path = "/";
		#$domain = "";
		#$secure = 1;
		#$http_only = 1;
		
		setcookie("remember_me",           "yes",          $expires, $path);
		setcookie("auto_login_data",       $user_login,    $expires, $path);
		setcookie("auto_login_password",   $password,      $expires, $path);   
	}

	public static function unset_auto_login_cookie(){
		$expires = time() - 60 * 60 * 24 * 100;
		$path = "/";
		#$domain = "";
		#$secure = 1;
		#$http_only = 1;
	  
		setcookie("remember_me",         "", $expires, $path);
		setcookie("auto_login_data",     "", $expires, $path);
		setcookie("auto_login_password", "", $expires, $path);  
	}

	public static function auto_login_cookie_set()
	{
		return isset($_COOKIE['remember_me']);
	}

	/* 
	* stores the current session id as a cookie in the user's pc, used in conjunction with 'restore_user_session()'.
	* It enables us to retrieve the session id so we can auto-log a user in if they re-open their browser window after (mistakingly) closing it 
	*/
	public static function set_auto_login_session_cookie()
	{
		$session_lifetime = UserManager::get_app_setting('session_lifetime');
		setcookie("auto_login_sid", session_id(), time() + $session_lifetime, "/");
	}

	public static function unset_auto_login_session_cookie()
	{
		$session_lifetime = UserManager::get_app_setting('session_lifetime');
		setcookie("auto_login_sid", "", time() - $session_lifetime, "/");
	}

	public static function auto_login_session_cookie_set()
	{
		return isset($_COOKIE['auto_login_sid']);
	}

	public static function restore_user_session($session_object)
	{
		$session   = self::sanitize_session_object($session_object);    
		$sess_id   = $_COOKIE['auto_login_sid'];
		$sess_data = $session->read($sess_id);  
		$session->destroy($sess_id); //destroy the previous session, 
		session_decode($sess_data);  //then copy its contents into the current one, so we don't have duplicate sessions
		$session->refresh(); //refresh the session. Added Nov. 3, 2012: incase I start having errors, remove this             
	}

	public static function try_auto_login($opts = array())
	{
		$referrer        = isset($opts['referrer'])        ? trim($opts['referrer'])        : get_referrer_page();
		$user_ip_address = isset($opts['user_ip_address']) ? trim($opts['user_ip_address']) : $_SERVER['REMOTE_ADDR']; 
		$session_object  = isset($opts['session_object'])  ? $opts['session_object']        : UserManagerSessionManager::get_instance(); 
		
		if(self::user_is_logged_in())
		{
			return; //if the user is currently logged in, no need to try to log them in
		}
		 
		$user_login = $pass  = '';

		//session still active after browser (mistakenly) closed
		if(self::auto_login_session_cookie_set())
		{
			self::restore_user_session($session_object);
			return;
		}
		  
		//if user has enabled the 'remember me' option
		if(self::auto_login_cookie_set())
		{ 
			$user_login = trim($_COOKIE['auto_login_data']);
			$pass  = trim($_COOKIE['auto_login_password']);
			$login_type = 2;
		}

		if( !self::is_verified_user($user_login, $pass) )
		{      
			unset($user_login);
			unset($pass);
			return;
		}

		$opts_array['remember_user'] = self::auto_login_cookie_set(); //renews the remember_me feature, so it lasts until whenever the user manually logs out
		$opts_array['ip_address']    = $user_ip_address;
		$opts_array['login_page']    = $referrer;
		$opts_array['login_type']    = $login_type;

		UserLogin::login($user_login, $pass, $opts_array);
	}

	private static function sanitize_session_object($session_object)
	{
		return $session_object;
	}
   
	private static function _get_db_object()
	{
		return UserManager::get_db_object();
	}
}