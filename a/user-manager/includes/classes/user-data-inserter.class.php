<?php
/*
* A class for inserting newly registered user's records
* The minimum requirements for adding a new user record is a login and a password
*/
class UserDataInserter
{
	private $id         = 0; //the user id
	private $login      = ''; // the user login
	private $password   = ''; // the user password
	private $date_registered; // the date the user record was added
	
	/**
	* associative array of extra user data, e.g for students you can have 'matric_no', for workers you can have 'position', etc
	* e.g  array( array('meta_key'=>'Matric_no', 'meta_value'=>123456789), array('meta_key'=>'card_number', 'meta_value'=>987654321) )
	*/
	private $user_meta = array();
	

	/**
	* Constructor prepares the user data to be added
	* @param array $data an associative array in the form ('login'=>'', 'password'=>'', 'firstname'=>'', 'matric_no'=>'', ...);
	* @return void
	* @author Michael Orji
	*/
	public function __construct( $data=array() )
	{
		if( !is_array($data) )
		{
			trigger_error('UserDataInserter::single argument constructor expects an array', E_USER_ERROR);
		}
		
		/**
		* A a login and a password is required to insert a new user record
		*/
		if( !isset($data['login']) || !isset($data['password']) ) 
		{
			return false;
		}
		
		$login    = is_string($data['login'])    ? trim($data['login'])    : $data['login'];
		$password = is_string($data['password']) ? trim($data['password']) : $data['password'];
		
		if( empty($login) || empty($password) )
		{
			return false;
		}
		
		foreach($data AS $key => $value)
		{
			$$key = ( is_string($value) ? trim($value) : $value );
			if(!empty($$key))
			{
				$this->$key = $$key; //( is_string($value) ? trim($value) : $value );
			}
		}
	}

	/**
	* Add a new user record to the database
	* @param void
	* @return boolean|integer false on failure or id of the insert operation on success
	* @author Michael Orji
	*/
	public function insert_new_record()
	{
		$basic_data            = array();
		$meta_data             = array();
		$db_object             = self::_db_object();
		$users_table           = UserManager::get_tables_prefix(). "users";
		$this->password        = UserManager::hash_password($this->password);
		$this->date_registered = $db_object->sql_term('NOW()');
		
		$basic_types = UserManager::get_basic_user_data_types(); //array('login', 'password');
		
		foreach($this AS $key => $value)
		{
			if( in_array($key, $basic_types) && ($key != 'id') )
			{
				$basic_data[$key] = $value;
			}
			else if( ($key != 'id') && ($key != 'user_meta') )
			{
				$meta_data[] = array('data_key'=>$key, 'data_value'=>$value);
			}
		}
		
		if( !$this->id = self::_db_object()->insert_records($users_table, $basic_data) )
		{
			return false;
		}
		
		$this->user_meta = $meta_data;
		$this->_insert_user_meta();
		return $this->id;
	}
	
	private function _insert_user_meta()
	{  //var_dump($this->user_meta); exit;
		if(empty($this->user_meta))
		{
			return;
		}
		$updater = new UserDataUpdater($this->id);
		$updater->update_user_data($this->user_meta);
	}
   
	private function _db_object()
	{
		return UserManager::get_db_object();
	}
}