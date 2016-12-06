<?php

class UserLogin
{
	/**
	* @param array $form_data the data submitted from the login form_data
	* members include: 
	* 					string user_login the user's login id, e.g email or username, 
	* 					string user_password the user's password, 
	* 					boolean remember_user should the user be remembered on subsequent visits from computer if they didn't logout explicitly, 
	* 					string login_landing_page page to redirect user on successful login,
	*                  	string empty_login_field_error message to display if login field is empty, 
	*					string empty_password_field_error message to display if password field is empty, 
	*					string unverified_account_error message to display if user entered invalid credentials
	*
	* @return void | array : on successful login, user is redirected to login_landing_page and no value is returned
	*                        on error an array is returned with members: 
	*                        login_error boolean to indicate that an error occurred
	*                        login_error_message string the error message returned
	* Bottom line is: if a value is returned, then an error occurred
	*
	* @author Michael Orji
	* @date Oct. 25, 2014
	*/ 
	public static function do_user_login($form_data=array())
	{
		$default_data = array( 
			'user_login'                   => '', 
			'user_password'                => '', 
			'remember_user'                => '', 
			'redirect_on_successful_login' => false, 
			'login_landing_page'           => '',
		    'empty_login_field_error'      => 'Please enter your login data', 
			'empty_password_field_error'   => 'Please fill in the password field', 
			'unverified_user_error'        => 'Invalid account details entered'
		);
		
		ArrayManipulator::copy_array($default_data, $form_data);
		ArrayManipulator::copy_array($form_data, $default_data);
		
		foreach($form_data AS $key => $value)
		{
			$$key = is_string($value) ? trim($value) : $value;
		}

		/** 
		* if a login landing page has been specified, the user is redirected there
		* else, they are taken to the page they requested, from which they are logging in
		*/
		$login_success_page = $login_landing_page ? $login_landing_page : UrlInspector::get_referrer_page($strip_qs=false);
		$referrer           = $_SERVER['HTTP_REFERER'];
		$user_ip_address    = $_SERVER['REMOTE_ADDR'];
		$redirect_page      = UrlInspector::get_referrer_page($strip_qs=false); 

		$user_login     = trim($user_login);
		$pass           = trim($user_password);
		$remember_user  = trim($remember_user);

		$matrix[0]['error_condition'] = empty($user_login);
		$matrix[0]['error_message']   = $empty_login_field_error;
		$matrix[1]['error_condition'] = empty($pass);
		$matrix[1]['error_message']   = $empty_password_field_error;
		$matrix[2]['error_condition'] = !UserAuthentication::is_verified_user($user_login, $pass);
		$matrix[2]['error_message']   = $unverified_user_error;

		$check = Validator::validate($matrix);

		if($check['error'] == true)
		{   
			if($check['status_message'] == $unverified_user_error) //possible hacker
			{
				LoginAttemptsHandler::add_login_attempt($user_ip_address);
			}
			
			return array( 'login_error'=>true, 'login_error_message'=>$check['status_message']);
		}

		$opts_array['remember_user'] = $remember_user;
		$opts_array['ip_address']    = $user_ip_address;
		$opts_array['login_page']    = $referrer;
		$opts_array['login_type']    = 1;

		LoginAttemptsHandler::clear_login_attempts($user_ip_address);
		self::login($user_login, $pass, $opts_array);
		
		if($redirect_on_successful_login)
		{
			UrlManipulator::redirect($login_success_page);
		}
	}
	
	public static function login($user_login, $pass, $opts = array() ){
 
		$remember   = !empty($opts['remember_user']) ? true : false;
		$user_id    = UserManager::get_user_id($user_login); 
		$login_id   = self::register_login($user_id, $opts); //register the login id to be used for the logout query

		SessionManipulator::set_session_values( array('login_id'=>$login_id, 'current_user_id'=>$user_id, 'current_user_login'=>$user_login, 'current_user_password'=>$pass) );
		
		if($remember)
		{
			UserAuthentication::set_auto_login_cookie($user_login, $pass);
		}
	}

	private static function register_login($userid, $opts = array() )
	{
		$ip_address = isset($opts['ip_address']) ? $opts['ip_address'] : $_SERVER['REMOTE_ADDR'];
		$loginpage  = isset($opts['login_page']) ? $opts['login_page'] : 'base_url';
		$logintype  = isset($opts['login_type']) ? $opts['login_type'] : 1;
		$db_obj     = self::_get_db_object();
		
		$db_obj->insert_records( UserManager::get_tables_prefix(). "user_logins", array(
			"user_id"    => $userid,
			"ip_address" => $ip_address,
			"login_page" => $loginpage,
			"login_type" => $logintype,
			"login_time" => $db_obj->sql_term("NOW()")
		));
		
		return $db_obj->last_insert_id();
	}
	
	private static function _get_db_object()
	{
		return UserManager::get_db_object();
	}
}