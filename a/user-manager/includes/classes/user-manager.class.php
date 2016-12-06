<?php

/**
* enable (backward) compatibility for scripts written using mysql, 
* rather than mysqli or our custom mysql class
*/
$connection_id = @mysql_connect(DB_SERVER, DB_USER, DB_PASS);
if($connection_id) 
{ 
	@mysql_select_db(DB_NAME); 
}

class UserManager
{
	/* SESSION STARTER 
	 * This should be used to start the session,
	 * since it calls the gc() method of UserManagerSessionManager to properly logout expired sessions before deleting (and not just to delete) them
	 */
	public static function start_session($session_lifetime = 0)
	{   
		if(self::get_db_object()->table_exists(self::get_tables_prefix(). 'app_settings'))
		{ 
			$session_lifetime = !empty($session_lifetime) ? $session_lifetime : self::get_app_setting('session-lifetime');
		}
		
		$session = UserManagerSessionManager::get_instance($session_lifetime);
		
		
		/*
		* Create a cron-job to do this
		* as leaving it here will slow the page down when you have a lot of users
		*/
		$session->handle_expired_sessions(); //time() - (60 * 60 * 24) only delete (from the database session data) that have expired since at least a day
		
		return $session;
	}
	
	
	//PUBLIC UTILITY METHODS
	public static function get_db_object()
	{
		return Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}
	
	public static function hash_password($string)
	{
		return StringManipulator::hash_string($string, $hash_algorithm = 'md5');
	}
	
	public static function get_basic_user_data_types()
	{
		return self::get_db_object()->get_table_columns( self::get_tables_prefix(). "users");
	}
	
	public static function get_data_from_meta_table($meta_key)
	{
		$meta_key = trim($meta_key);
		
		$sql = "SELECT `meta_value` FROM ". self::get_tables_prefix(). "user_meta WHERE `meta_key` = '$meta_key'";
		
		$db_object = self::get_db_object();
		$db_object->execute_query($sql);
		
		if( $db_object->num_rows() <= 0 )
		{
			return '';
		}
		else
		{
			$rows = $db_object->get_rows();
			$meta_value = Util::unstringify($rows['meta_value']);
			return $meta_value;
		}
	}
	
	public static function get_app_setting($setting_key = '')
	{
		return AppSettings::get_setting($setting_key);
	}
	
	//USER DATA METHODS
	public static function get_user_instance($user_id)
	{
		return new UserInstance($user_id);
	}
	
	/**
	* register a user and optionally login the user on success
	* @param array associative array of key-value pairs of data of user to register
	*        the array must contain 'login' and 'password' keys for a successful registration
	* @return boolean|integer false on failure or id of the newly registered user on success
	* @author Michael Orji
	*
	* NOTE: To insert a user into the database, this method must be called 
	*/
	public static function user_successfully_registered($registration_data)
	{
		$default_array = array( 'login'=>'', 'password'=>'' );
        $inserter_opts = ArrayManipulator::copy_array($default_array, $registration_data);
		foreach($inserter_opts AS $key => $value)
		{
			$$key = is_string($value) ? trim($value) : $value;
		}
      
		$registrar     = new UserDataInserter($inserter_opts);
		$registrant_id = $registrar->insert_new_record();

		//if the registration was successful
		if( $registrant_id )
		{ 
        	$registrant_name  = isset($firstname) ? $firstname : 'User';
            $registrant_email = isset($email)     ? $email     : $login;
        	$registrant_pass  = $password;
        
        	if( self::get_app_setting('login_on_successful_registration') == 'T' )
			{
         		UserLogin::login($registrant_email, $registrant_pass);
        	}

			return $registrant_id;
		}
		
		return false;
	}
	
	public static function user_exists($user_login)
	{
		return UserDataReader::user_exists($user_login);
	}
	
	public static function user_meta_exists($user_id, $meta_key)
	{
		return UserDataReader::user_meta_exists($user_id, $meta_key);
	}
	
	public static function get_user_id($user_login)
	{
		return UserDataReader::get_user_id($user_login);
	}
	
	public static function get_current_user_id()
	{
		return ( (self::user_is_logged_in()) ? $_SESSION['current_user_id'] : 0 );
	}
	
	public static function get_all_users_id()
	{
		return UserDataReader::get_all_users_id();
	}
	
	/*
   	* returns user data as an array 
   	*/
   	public static function get_user_data($user_id, $data_key='')
	{  
    	return $user_id ? UserDataReader::get_user_data($user_id, $data_key) : array();
   	}
	
	/** 
	* loads the data of the currently logged in user (the one whose session is active). if user is not logged in, an empty array is returned
	* @param string $data_key optional argument to specify the particular data field to return, if empty, the user's entire data is returned
	* @return
	* @author Michael Orji
	*/
	public static function get_current_user_data($data_key='')
	{ 
      	if( !self::user_is_logged_in() )
		{ 
       		return array();
      	}
      
    	UserAuthentication::set_auto_login_session_cookie(); //helps us auto-login user if they (mistakenly) closed the browser window and re-open it while their session hasn't expired
    	$user_data = self::get_user_data( self::get_current_user_id(), $data_key );
    	return $user_data;
   	}
	
   	/**
   	* Possible members of the $opts array:
   	* $matrix e.g = array('data_key'='', 'data_value'='', 'overwrite'=true), ('data_key2'=>'', 'data_value2'=>'') 
	*/
   	public static function update_user_data($user_id, $opts)
	{	
    	$updater = new UserDataUpdater($user_id);
    	return $updater->update_user_data($opts);
   	}
	
	public static function delete_user($user_id, $opts=array() )
	{
		$user_id = is_string($user_id) ? trim($user_id) : $user_id;
		
		if( empty($user_id) || intval($user_id) <= 0 )
		{
			return false;
		}
		
		$user_exists = self::get_user_data($user_id, 'login');
		
		if(!$user_exists)
		{
			return false;
		}
		
		$default_opts = array('remove_records' => false);
		
		ArrayManipulator::copy_array($default_opts, $opts);
		
		foreach($default_opts AS $key => $value)
		{
			$$key = is_string($value) ? trim($value) : $value;
		}
		
		if(!$remove_records)
		{
			return self::_set_user_as_deleted($user_id);
		}
		
		$db_obj = self::get_db_object();
		$delete = $db_obj->delete_records( self::get_tables_prefix(). "users", array('id'=>$user_id), $limit = '');
		
		if($delete)
		{
			$db_obj->delete_records( self::get_tables_prefix(). "user_meta", array('user_id'=>$user_id), $limit = '');
			$db_obj->delete_records( self::get_tables_prefix(). "user_logins", array('user_id'=>$user_id), $limit = '');
			$db_obj->delete_records( self::get_tables_prefix(). "user_relationships", array('sender_id'=>$user_id), $limit = '');
			$db_obj->delete_records( self::get_tables_prefix(). "user_relationships", array('receiver_id'=>$user_id), $limit = '');
			return true;
		}
		
		return false;
	}
	
	private static function _set_user_as_deleted($user_id)
	{
		return self::update_user_data( $user_id, array('data_key'=>'deleted', 'data_value'=>true) );
	}
	
	public static function get_users_query_string($conditions = array(), $orders = array(), $limit = '')
	{
		return self::get_users($conditions, $orders, $limit, true);
	}
	
	public static function get_users( $conditions = array(), $orders = array(), $limit = '', $return_query_string = false )
	{
		$db_object       = self::get_db_object();
		$user_table      = self::get_tables_prefix(). "users";
		$user_meta_table = self::get_tables_prefix(). "user_meta";
		$user_table_cols = $db_object->get_table_columns($user_table);
		$meta_table_cols = $db_object->get_table_columns($user_meta_table);
		$matrix          = array();
		
		if( !is_array($conditions) || empty($conditions) )
		{
			$ids_sql = "SELECT id FROM ". $user_table;
		}
		
		else
		{
			$counter = 0;
			
			$arr_keys = array_keys($conditions);
			$first_condition_key   = array_shift($arr_keys); //array_shift(array_keys($conditions));
			$first_condition_value = $conditions[$first_condition_key];
			$first_condition_value = Util::is_scalar($first_condition_value) ? $first_condition_value : Util::stringify($first_condition_value);
			
			$ids_sql = "SELECT user_id FROM ". $user_table. ", ". $user_meta_table. " WHERE ( ";
			
			
			//if($first_condition_key == 'login')
			if( in_array( $first_condition_key, $user_table_cols ) )
			{
				$ids_sql .= "({$user_table}.`$first_condition_key` = ". "'". $first_condition_value. "') ";
			}
			else
			{
				$ids_sql .= "({$user_meta_table}. meta_key  = '$first_condition_key' ";
				$ids_sql .= " AND {$user_meta_table}. meta_value = '$first_condition_value') ";
			}
			
			foreach($conditions AS $condition => $value)
			{
				if($condition != 'data_to_get')
				{
					if($condition != $first_condition_key )
					{
						//if($condition == 'login')
						if( in_array( $condition, $user_table_cols ) )
						{
							$ids_sql .= "AND {$user_table}.`$condition` = ".  "'". $value. "' ";
						}
						else
						{
							$value = DataSanitizer::sanitize_data_for_db_query($value);
							$value = is_numeric($value) ? intval($value) : $value; 
							$value = Util::is_scalar($value) ? $value : Util::stringify($value);
							$ids_sql  .= " OR ({$user_meta_table}.meta_key  = '${condition}' ";
							$ids_sql  .= " AND {$user_meta_table}.meta_value = '$value')";
						}
					}
					
					++$counter;
				}
         	}
			
			$ids_sql .= " )";
			$ids_sql .= "AND ( {$user_meta_table}.`user_id` = {$user_table}.`id`) ";
			
			if($counter > 0)
			{
				$ids_sql .= " GROUP BY `user_id` having count(*) = $counter";
			}
		}
		
		if(!empty($orders) && is_array($orders))
		{ 
			$order_by_clause = " ORDER BY";
			foreach($orders AS $key => $value)
			{
				if( (strtolower($key) != 'id') && (strtolower($key) != 'name') )
				{
					$order_by_clause .= " `$key` $value,";
				}
			}
				
			$order_by_clause = substr($order_by_clause, 0, -1); // remove trailing ,
				
			$ids_sql .= $order_by_clause;
		}
			
		if(!empty($limit))
		{
			$ids_sql .= " LIMIT ". $limit;
		}
		
		if($return_query_string)
		{
			return $ids_sql;
		}
		
		$db_obj = self::get_db_object();
		$db_obj->execute_query($ids_sql);
		$ids = $db_obj->return_result_as_matrix();
		
		$id_key = is_array($conditions) && !empty($conditions) ? 'user_id' : 'id'; //either we selected from the user-meta table or the users table
		$ids = ArrayManipulator::reduce_redundant_matrix_to_array($ids, $id_key);
		
		for($i = 0; $i < count($ids); $i++)
		{
			$current_id = $ids[$i];
				
			if( !empty($conditions['data_to_get']) && is_array($conditions['data_to_get']) )
			{
				foreach($conditions['data_to_get'] AS $the_key)
				{
					$the_matrix[$i][$the_key] = self::get_user_data($current_id, $the_key);
				}
					
				if(count($conditions['data_to_get']) == 1)
				{
					$matrix = ArrayManipulator::reduce_redundant_matrix_to_array($the_matrix, $conditions['data_to_get'][0]);
				}
				else
				{
					$matrix = $the_matrix;
				}
			}
				
			else
			{	
				$matrix[$i] = self::get_user_data($current_id);
				$matrix[$i]['id']    = $current_id;
					
				$login_arr   = self::get_user_data($current_id, 'login');
				$matrix[$i]['login'] = $login_arr['login'];
			}
		}
		
		return $matrix;
	}
	
	
	//LOGIN AND LOGOUT METHODS
	/**
	* @param array $form_data the data submitted from the login form_data
	* members include: user_login, user_password, remember_user, 
	*				   redirect_on_successful_login, login_landing_page,
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
		return UserLogin::do_user_login($form_data);
	}
	
	/*
	* $opts data members:
	* logout_type int
	* redirect_page string
	* onbefore_redirect_message string
	* redirect_delay int
	* logout_session_token string a logout session token that is added to session array when user has successfully logged out
    * you can test for the presence of this in the session array if you want to perform an extra action after a successful user logout
	* NOTE: remember to unset the token from the session array when user has logged out
	*/
	public static function logout_user( $opts = array() )
	{
		$logout_opts['login_id']             = SessionManipulator::get_session_value('login_id');
		$logout_opts['logout_page']          = UrlInspector::get_referrer_page();
		$logout_opts['logout_type']          = 1;
		$logout_opts['logout_session_token'] = isset($_GET['logout_session_token']) ? $_GET['logout_session_token'] : '';
		UserLogout::logout($logout_opts);
		
		if( isset($opts['redirect_page']) )
		{
			if( isset($opts['onbefore_redirect_message']) && !empty($opts['onbefore_redirect_message']) )
			{
				$delay = (isset($opts['redirect_delay']) && intval($opts['redirect_delay']) > 0) ? $opts['redirect_delay'] : 5;
				UrlManipulator::redirect($opts['redirect_page'], 302, $delay, $opts['onbefore_redirect_message']);
			}
			else
			{
				UrlManipulator::redirect($opts['redirect_page']);
			}
		}
	}
	
	public static function user_is_logged_in()
	{
		self::_load_auto_login_module(); //makes sure the 'current_user_id' session is set before authenticating
      	return UserAuthentication::user_is_logged_in();
   	}
	
	/*
	* $arr ex: arr( 'orders'=>array('ipaddress'=>'ASC', 'date_added'=>'DESC'), 'limit'=>'' )
	*/
	public static function get_user_logins($user_id=0, $arr=array())
	{
		extract($arr);
		
		$sql = "SELECT * FROM ". self::get_tables_prefix(). "user_logins WHERE true=true";
		
		$sql .= ( (!empty($user_id) && (int)$user_id > 0) ? " AND user_id=". $user_id : "" );
		
		if( !empty($orders) && is_array($orders) )
		{
			$sql .= " ORDER BY ";
			foreach($orders AS $order_key => $order_value)
			{
				$sql .= $order_key. " ". $order_value. ", ";
			}
			$sql = substr($sql, 0, -1); //remove trailing ','
		}
		
		$sql .= !empty($limit)    ? " LIMIT ".   $limit    : "";
		
		$db_obj = self::get_db_object();
		$db_obj->execute_query($sql);
		return $db_obj->return_result_as_matrix();
	}
	
	public static function get_tables_prefix()
	{
		return TABLES_PREFIX;
	}
	
	/*
    * try to log user in if they :
    * 1. mistakenly closed their browser and their session is still active OR
    * 2. enabled the 'keep me logged in' (remember me) option
    */
   	private static function _load_auto_login_module()
	{
    	$opts['referrer']        = UrlInspector::get_referrer_page();
    	$opts['session_object']  = UserManagerSessionManager::get_instance();
    	$opts['user_ip_address'] = $_SERVER['REMOTE_ADDR']; 
    	UserAuthentication::try_auto_login($opts);
   	}
}