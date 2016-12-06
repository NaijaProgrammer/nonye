<?php
require SITE_DIR. '/a/session-manager/session-manager.php';
require SITE_DIR. '/a/user-manager/user-manager.php';

class UserModel extends BaseModel
{
	public static function start_session()
	{
		return UserManager::start_session();
	}
	
	public static function hash_password($string)
	{
		return UserManager::hash_password($string);
	}
	
	
	/****** USER DATA METHODS ****/
	public static function get_user_instance($user_id)
	{
		return UserManager::get_user_instance($user_id);
	}
	
	public static function get_users_query_string($conditions = array(), $orders = array(), $limit = '')
	{
		return UserManager::get_users_query_string($conditions, $orders, $limit);
	}
	
	public static function get_users( $conditions = array(), $orders = array(), $limit = '' )
	{
		return UserManager::get_users($conditions, $orders, $limit);
	}
	
	public static function process_user_registration($registration_data)
	{
		$registrant_id = self::_user_successfully_registered_($registration_data);
		if($registrant_id)
		{
			foreach($registration_data AS $key => $value)
			{
				$$key = is_string($value) ? trim($value) : $value;
			}
			
			return $registrant_id;
		}
		
		else
		{
			return false;
		}
	}
	
	/**
	* To insert a user into the database, this method must be called
	* The two required members of the $registration_data array are:
	* login string the unique user login
	* password string the user password
	* Other key/value pairs may be added as required,
	*/
	private static function _user_successfully_registered_($registration_data)
	{
		return UserManager::user_successfully_registered($registration_data);
	}
	
	public static function user_exists($user_login)
	{
		return UserManager::user_exists($user_login);
	}
	
	public static function user_meta_exists($user_id, $meta_key)
	{
		return UserManager::user_meta_exists($user_id, $meta_key);
	}
	
	public static function get_user_id($user_login)
	{
		return UserManager::get_user_id($user_login);
	}
	
	public static function get_current_user_id()
	{
		return UserManager::get_current_user_id();
	}
	
	public static function get_all_users_id()
	{
		return UserManager::get_all_users_id();
	}
	
	/*
   	* returns user data as an array or as an object
   	*/
   	public static function get_user_data($user_id, $data_key='', $get_as_obj = false)
	{
		$user_data = UserManager::get_user_data($user_id, $data_key);
		
		if($get_as_obj)
		{
			$obj = new StdClass();
			if(is_array($user_data))
			{
				foreach($user_data AS $key => $value)
				{
					$obj->{$key} = $value;
				}
			}
			else
			{
				$obj->{$data_key} = $user_data;
			}
			return $obj;
		}
		return $user_data;
    	//return UserManager::get_user_data($user_id, $data_key);
   	}
	
	/** 
	* loads the data of the currently logged in user (the one whose session is active). if user is not logged in, an empty array is returned
	* @param string $data_key optional argument to specify the particular data field to return, if empty, the user's entire data is returned
	* @return
	* @author Michael Orji
	*/
	public static function get_current_user_data($data_key='', $get_as_obj = false)
	{ 
		$curr_user_data = UserManager::get_current_user_data($data_key);
		
		if($get_as_obj)
		{
			$obj = new StdClass();
			if(is_array($curr_user_data))
			{
				foreach($curr_user_data AS $key => $value)
				{
					$obj->{$key} = $value;
				}
			}
			else
			{
				$obj->{$data_key} = $curr_user_data;
			}
			return $obj;
		}
		return $curr_user_data;
      	//return UserManager::get_current_user_data($data_key);
   	}
	
   	/**
   	* Possible members of the $opts array:
   	* $matrix e.g = array ( array('data_key'='', 'data_value'='', 'overwrite'=true), array('data_key'=>'', 'data_value'=>'') )
	*/
   	public static function update_user_data($user_id, $opts)
	{   
    	return UserManager::update_user_data($user_id, $opts);
   	}
	
	/**
	* possible members of the $opts array: 'remove_records': boolean 
	*/
	public static function delete_user($user_id, $opts=array() )
	{
		return UserManager::delete_user($user_id, $opts);
	}
	
	
	/***** LOGIN AND LOGOUT METHODS ****/
	public static function user_is_logged_in()
	{
      	return UserManager::user_is_logged_in();
   	}
	
	/**
	* @param array $form_data the data submitted from the login form_data
	* members include: user_login, user_password, remember_user, redirect_on_successful_login, login_landing_page,
	*                  empty_login_field_error, empty_password_field_error, unverified_account_error
	*
	* @return void | array on successful login, user is redirected to login_landing_page and no value is returned
	*                      on error an array is returned with members: 
	*                      login_error boolean to indicate that an error occurred
	*                      login_error_message string the error message returned
	* Bottom line is, if a value is returned, then an error occurred
	*
	* @author Michael Orji
	* @date Oct. 25, 2014
	*/
	public static function login_user($form_data)
	{
		return UserManager::login_user($form_data);
	}
	
	public static function logout_user( $opts = array() )
	{
		UserManager::logout_user($opts);
	}
	
	/*** SITE-SPECIFIC METHODS (Methods not directly defined in UserManager class) */
	public static function username_already_exists($username)
	{
		$prev_uname = UserManager::get_data_from_meta_table('username');
		return ( strtolower($prev_uname) == strtolower($username) );
	}
	
	public static function generate_password_recovery_nonce($user_email)
	{
		$nonce   = NonceModel::generate_nonce();
		$user_id = UserModel::get_user_id($user_email);
		
		UserModel::update_user_data($user_id, array( array('data_key'=>'pnonce', 'data_value'=>$nonce, 'overwrite'=>true) ) );
		
		$nonce_array = array('user_email'=>$user_email, 'pnonce'=>$nonce);
		$stringified_nonce = Util::stringify($nonce_array);
		return $stringified_nonce;
	}
	
	public static function extract_password_recovery_nonce_data($stringified_nonce)
	{
		$nonce_array = Util::unstringify($stringified_nonce);
		return $nonce_array;
	}
	
	public static function verify_password_recovery_nonce($stringified_nonce)
	{
		$nonce_array = Util::unstringify($stringified_nonce);
		
		$user_email  = $nonce_array['user_email'];
		$nonce       = $nonce_array['pnonce'];
		
		$user_id     = self::get_user_id($user_email);
		
		/**
		* make sure this is a valid user
		*/
		if(!$user_id)
		{
			return false;
		}
		
		/**
		* make sure the nonce belongs to this user
		*/
		$is_user_nonce = self::get_user_data($user_id, 'pnonce');
		
		if(!$is_user_nonce)
		{
			return false;
		}
		
		$user_nonce = $is_user_nonce;
		
		$is_valid_nonce = NonceModel::check_nonce($user_nonce);
		
		if($is_valid_nonce)
		{
			return $stringified_nonce; 
		}
		
		return false;
	}
}