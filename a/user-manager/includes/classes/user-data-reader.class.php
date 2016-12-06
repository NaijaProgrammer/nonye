<?php

/*
* A static (convenience) class for reading user records
*/
class UserDataReader
{
   	public static function get_current_user_id()
	{
		return isset($_SESSION['current_user_id']) ? $_SESSION['current_user_id'] : 0;
   	}
	
	public static function user_exists($user_identifier)
	{
		return self::get_user_id($user_identifier);
	}
	
	public static function user_meta_exists($user_id, $meta_key)
	{
		$db_object = self::_get_db_object();
		$db_object->execute_query("SELECT `user_id` FROM ". UserManager::get_tables_prefix(). "user_meta WHERE `user_id` = $user_id AND `meta_key` = '$meta_key'");
		return $db_object->num_rows() > 0;
	}
	
   	public static function get_user_id($userlogin)
	{
		$sql = "SELECT id FROM ". UserManager::get_tables_prefix(). "users WHERE login = '". DataSanitizer::sanitize_data_for_db_query($userlogin). "' LIMIT 1";

    	$query = mysql_query($sql);
		
    	if(!$query)
		{ 
			return false; 
		}
		
    	$num = mysql_num_rows($query);
		
    	if($num == 0) 
		{
			return 0;
		}
		
    	$row = mysql_fetch_assoc($query);
		
    	return $row['id'];	
   	}
	
   	public static function get_user_data($user_id, $data_key='')
	{ 
    	$data_key = trim($data_key);
		return empty($data_key) ? self::_get_user_full_data($user_id) : self::_get_user_single_data($user_id, $data_key);
   	}

	public static function get_all_users_id()
	{
    	$sql     = "SELECT id FROM ". UserManager::get_tables_prefix(). "users";
    	$query   = mysql_query($sql);
    	$data    = array();
        $counter = 0;
			
    	if(!$query)
		{
			return $data;
		}
		
    	while($row = mysql_fetch_array($query))
		{ 
			$data[$counter++] = $row['id']; 
		}
    		
    	return $data;
   	}

	public static function get_ids_of_groups_created_by_user($user_id)
	{
		$sql = "SELECT id FROM ". UserManager::get_tables_prefix(). "user_groups ".
			   "WHERE creator_id = ". DataSanitizer::sanitize_data_for_db_query($user_id);
		$db_obj = UserManager::get_db_object();
		$db_obj->execute_query($sql);
		$matrix = $db_obj->return_result_as_matrix();
		$arr    = array();
		
		for($i = 0; $i < count($matrix); $i++)
		{
			$current_array = $matrix[$i];
			foreach($current_array AS $value)
			{
				$arr['result_set'][] = $value;
			}
		}
		
		$arr['sql_query'] = $db_obj->get_executed_query(); //for scripts requiring pagination
		return $arr; 
	}

	public static function get_group_details($group_id)
	{
		$sql = "SELECT * FROM ". UserManager::get_tables_prefix(). "user_groups ".
			   "WHERE id = ". DataSanitizer::sanitize_data_for_db_query($group_id);
		$db_obj = UserManager::get_db_object();
		$db_obj->execute_query($sql);
		return $db_obj->get_rows();
	}
	
	//public static function get_user_relationship_details($user_id, $relationship_type_id_or_name = 0,  $case = 3, $status='P')
	/**
	* @param array $opts: members:
	* int user_id the id of the user whose relationship details we want to retrieve
	* int | string relationship_type_id the id or name of relationship type_details
	* int case where user with user_id is: 1(sender:sent by user), 2(receiver:received by user), 3(sender OR receiver:sent or received by user)
	* string status the status of relationship: 'A'=approved, 'P'=pending, 'R'=rejected, 'ALL'=all relationships
	*/
	public static function get_user_relationship_details($opts = array())
	{
		$opts = (array)$opts;
		if(empty($opts))
		{
			return array();
		}
		$defaults = array('user_id'=>0, 'relationship_type_id'=>'', 'case'=>3, 'status'=>'P');
		ArrayManipulator::copy_array($defaults, $opts);
		foreach($defaults AS $key => $value)
		{
			$$key = is_string($value) ? trim($value) : $value;
		}
		if(empty($user_id))
		{
			return array();
		}
		
		$sql  = "SELECT * FROM ". UserManager::get_tables_prefix(). "user_relationships WHERE TRUE = TRUE";
		$sql .= ( strtolower($status) != 'all' ) ? " AND `status` = '". DataSanitizer::sanitize_data_for_db_query($status). "'" : "";
		
		/** case 1, where current user is sender, case 2: current user is receiver, case 3, all relationships sent or received by current user **/
		switch($case)
		{
			case 1 : $id_str = "`sender_id`   = ". DataSanitizer::sanitize_data_for_db_query($user_id); break;
			case 2 : $id_str = "`receiver_id` = ". DataSanitizer::sanitize_data_for_db_query($user_id); break;
			case 3 :  
			default: $id_str = "(`sender_id`  = ". DataSanitizer::sanitize_data_for_db_query($user_id). " OR `receiver_id` = ".
			                                       DataSanitizer::sanitize_data_for_db_query($user_id); break;
		}
		
		$sql .= " AND ". $id_str;
		$type_details = $relationship_type_id ? UserManager::get_relationship_type_details($relationship_type_id) : array();
	
		if(!empty($type_details))
		{
			$type_id      = $type_details['id'];
			$is_mutual    = ( ($type_details['mutual'] == 'T') ? true : false );
			if( $type_details['active'] == 'F' )
			{
				return false;
			}
			$sql .= ( $type_id   ? " AND type_id = {$type_id}" : '' );
			//$sql .= ( $is_mutual ? " AND status = 'A'" : '' );
		}
		
		$db_obj = UserManager::get_db_object();
		$db_obj->execute_query($sql);
		return $db_obj->return_result_as_matrix();
	}
	
	
	/**
	* @author Michael Orji
	*/
	private static function _get_user_single_data($user_id, $data_key)
	{ 
		$users_table = UserManager::get_tables_prefix(). "users";
		$umeta_table = UserManager::get_tables_prefix(). "user_meta";
		$db_object   = self::_get_db_object();
		
		$is_basic_data = $db_object->column_exists_in_table($users_table, $data_key, DB_NAME);
		$is_meta_data  = self::user_meta_exists($user_id, $data_key);
		
		if( !$is_basic_data && !$is_meta_data )
		{
			return false;
		}
		
		if($is_meta_data)
		{ 
			return self::_get_user_meta($user_id, $data_key);
		}
		
		$db_object->execute_query("SELECT $data_key FROM $users_table WHERE id = $user_id");
		return $db_object->get_rows();
	}
	
	/*
	* @author Michael Orji
	*/
	private static function _get_user_full_data($user_id)
	{
		$data   = array();
		$db_obj = self::_get_db_object();
		
		$db_obj->execute_query( "SELECT * FROM ".UserManager::get_tables_prefix()."users WHERE id = ". DataSanitizer::sanitize_data_for_db_query($user_id). " LIMIT 1" );
    
		$row = $db_obj->get_rows(); //mysql_fetch_array($query, MYSQL_ASSOC);
		foreach($row AS $key => $value)
		{
			$_data[$key] = $value;
      	}
		$user_meta = self::_get_user_meta($user_id);
			
		foreach($user_meta AS $key => $value)
		{
			$_data[$key] = $value;
		}
			
       	return $_data;
	}
	
	protected static function _get_user_meta($user_id, $meta_key='')
	{ 
		$data     = array();
		$meta_key = trim($meta_key);
		
		if( !empty($meta_key) && !self::user_meta_exists($user_id, $meta_key))
		{ 
			return false;
		}
		if(!empty($meta_key))
		{
			$sql = "SELECT `meta_value` FROM ". UserManager::get_tables_prefix(). "user_meta WHERE `user_id` = $user_id AND `meta_key` = '$meta_key'";
		}
		else
		{
			$sql = "SELECT `meta_key`, `meta_value` FROM ". UserManager::get_tables_prefix(). "user_meta WHERE `user_id` = $user_id";
		}
		
		$db_object = self::_get_db_object();
		$db_object->execute_query($sql);
		
		$matrix = $db_object->return_result_as_matrix();
		
		if(!empty($meta_key))
		{ 
			if(count($matrix) == 1)
			{ 
				/* $rows = $db_object->get_rows(); 
				* doesn't work, because by the time $db_object->return_result_as_matrix() finishes executing, 
				* there are no more rows left in the result set.
				*/
				$rows       = $matrix[0];
				$meta_value = Util::is_stringified($rows['meta_value']) ? Util::unstringify($rows['meta_value']) : $rows['meta_value'];
				return $meta_value;
			}
			else if( count($matrix) > 1)
			{
				return ArrayManipulator::reduce_redundant_matrix_to_array($matrix, 'meta_value');
			}
		}
		
		$data = array();
		
		foreach($matrix AS $key => $arr)
		{
			if( array_key_exists($arr['meta_key'], $data) )
			{ 
				if(is_array($data[$arr['meta_key']]))
				{
					array_push( $data[$arr['meta_key']], $arr['meta_value'] );
				}
				
				else
				{
					$data[$arr['meta_key']] = array($data[$arr['meta_key']], $arr['meta_value']);
				}
			}
			
			else
			{
				$data[$arr['meta_key']] = Util::is_stringified($arr['meta_value']) ? Util::unstringify($arr['meta_value']) : $arr['meta_value'];
			}
		}
		
		return $data;
	}
	
	private static function _get_db_object()
	{
		return UserManager::get_db_object();
	}
}