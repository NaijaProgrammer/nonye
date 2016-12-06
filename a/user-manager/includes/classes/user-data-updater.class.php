<?php

/*
* A class for updating user records
*/
class UserDataUpdater
{
	private $user_id = 0;
	private $db_object = null;
 
	public function __construct($uid)
	{
		$this->_set_user_id($uid);
		$this->_set_db_object();
	}
	
	public function add_new_user_relationship($sender_id, $relationship_id_or_name )
	{
		$receiver_id = $this->user_id;
		$type_details = UserManager::get_relationship_type_details($relationship_id_or_name);
		$type_id = $type_details['id'];
		$db_obj  = UserManager::get_db_object();
		return $db_obj->insert_records(UserManager::get_tables_prefix(). 'user_relationships', array('type_id'=>$type_id, 'sender_id'=>$sender_id, 'receiver_id'=>$receiver_id, 'date_sent'=>$db_obj->sql_term('NOW()')) );
	}
	
	public function update_user_relationship( $updates = array(), $wheres = array() )
	{
		$updates = (array)$updates;
		$wheres  = (array)$wheres;
		
		$db_object = $this->_get_db_object();
		$updates['date_processed'] = $db_object->sql_term('NOW()');
		
		return $db_object->update_records(UserManager::get_tables_prefix(). 'user_relationships', $updates, $wheres);
	}
	
	/**
	* $matrix e.g = array('data_key'='', 'data_value'='', 'overwrite'=true), ('data_key'=>'', 'data_value'=>'')
	*/
	public function update_user_data($matrix)
	{  
		$basic_types = UserManager::get_basic_user_data_types(); //array('login', 'password');
		
		$meta_data = array();
		
		foreach($matrix AS $opts)
		{
			$data_key    = isset($opts['data_key'])   ? $opts['data_key']   : '';;
			$data_value  = isset($opts['data_value']) ? $opts['data_value'] : '';
			$overwrite   = isset($opts['overwrite'])  ? $opts['overwrite']  : false; //for user_meta table
			$prev_value  = isset($opts['prev_value']) ? $opts['prev_value'] : ''; //for user_meta table
		
			if( in_array($data_key, $basic_types) && ($data_key != 'id') )
			{ 
				$data_value = ( ($data_key == 'password') ? UserManager::hash_password($data_value) : $data_value );
				$basic_data_table = UserManager::get_tables_prefix(). "users";
				$this->_get_db_object()->update_table_column($basic_data_table, $data_key, $data_value, array('id'=>$this->user_id));
			}
			
			else if($data_key != 'id')
			{
				$meta_data[] = array('meta_key'=>$data_key, 'meta_value'=>$data_value, 'overwrite'=>$overwrite, 'prev_value'=>$prev_value);
				
				 /**
				 * we could update each meta data individually here, 
				 * but for efficiency, just be content with building the array here, 
				 * then update all meta data outside of the foreach loop at once
				 * To update each individually, uncomment the code below, and comment out the 'if' block outside the loop
				 */
				//$this->_update_user_meta_matrix($meta_data); 
			}
		}
		
		if(!empty($meta_data))
		{
			$this->_update_user_meta_matrix($meta_data);
		}
	}
   
	/**
	* $matrix e.g = array('meta_key'='', 'meta_value'='', 'overwrite'=true), ('meta_key2'=>'', 'meta_value2'=>'')
	*/
	private function _update_user_meta_matrix($matrix)
	{ 
		foreach($matrix AS $opts)
		{
			$meta_key   = isset($opts['meta_key'])   ? $opts['meta_key']   : ''; //field key: e.g 'matric_no'
			$meta_value = isset($opts['meta_value']) ? $opts['meta_value'] : ''; //field value
			$overwrite     = isset($opts['overwrite'])  ? $opts['overwrite']  : false;
			$prev_value = isset($opts['prev_value']) ? $opts['prev_value'] : '';
			$this->_update_user_meta($meta_key, $meta_value, $overwrite, $prev_value);
		}
	} 
	
	
	/** 
	* Update metadata for current user. If no value already exists for the specified user
    * and metadata key, the metadata will be added.
	*/
	private function _update_user_meta($meta_key, $meta_value, $overwrite, $prev_value='')
	{
		$meta_key   = is_string($meta_key)   ? trim($meta_key)   : $meta_key; //field key: e.g 'matric_no'
		$meta_value = is_string($meta_value) ? trim($meta_value) : $meta_value; //field value
		$prev_value = isset($prev_value)     ? $prev_value       : '';
		$prev_value = is_string($prev_value) ? trim($prev_value) : $prev_value;
		
		//if(empty($meta_key) || empty($meta_value))
		if(empty($meta_key))
		{
			return false;
		}
		
		// Compare existing value to new value if no prev value given and the key exists only once.
		if ( empty($prev_value) )
		{
			$old_value = UserManager::get_user_data($this->user_id, $meta_key);
			
			if ( count($old_value) == 1 ) 
			{  
				if ( is_array($old_value) )
				{
					if ( isset($old_value[0]) )
					{
						$old_value = $old_value[0];
					}
				}
				if ( $old_value === $meta_value ) //if key exists only once, then no need to duplicate same key with same value
				{
					return false;
				}
			}
		}

		// If meta doesn't exist, then and a new meta
		if ( !UserManager::user_meta_exists($this->user_id, $meta_key) )
		{  
			return self::_insert_user_meta($meta_key, $meta_value, $overwrite);
		}
		
		else if( !$overwrite )
		{  
			return self::_insert_user_meta($meta_key, $meta_value, false);
		}

		$where_clause = array( 'user_id'=>$this->user_id, 'meta_key'=>$meta_key );
		
		if ( !empty( $prev_value ) ) 
		{
			$where_clause['meta_value'] = Util::is_scalar($prev_value) ? $prev_value : Util::stringify($prev_value);
		}
		
		$meta_value = Util::is_scalar($meta_value) ? $meta_value : Util::stringify($meta_value);
		return $this->_get_db_object()->update_table_column(UserManager::get_tables_prefix()."user_meta", 'meta_value', $meta_value, $where_clause);
	}
	
	private function _insert_user_meta($meta_key, $meta_value, $overwrite=true)
	{
		$db_obj     = self::_get_db_object();
		$meta_key   = is_string($meta_key)   ? trim($meta_key)   : $meta_key; //field key: e.g 'matric_no'
		$meta_value = is_string($meta_value) ? trim($meta_value) : $meta_value; //field value
		if( empty($meta_key) || empty($meta_value) )
		{
			return false;
		}
		if( $overwrite && UserManager::user_meta_exists($this->user_id, $meta_key) )
		{
			return false;
		}
		
		$meta_value = Util::is_scalar($meta_value) ? $meta_value : Util::stringify($meta_value);
		return $db_obj->insert_records( UserManager::get_tables_prefix(). 'user_meta', array('user_id'=>$this->user_id, 'meta_key'=>$meta_key, 'meta_value'=>$meta_value) );
	}
	
	private function _set_user_id($uid)
	{
		$this->user_id = $uid;
	}
   
	private function _set_db_object()
	{
		$this->db_object = UserManager::get_db_object();
	}

	private function _get_db_object()
	{
		return $this->db_object;
	}
}