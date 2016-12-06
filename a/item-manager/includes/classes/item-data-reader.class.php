<?php
/**
* @author : Michael Orji
* @date: Oct 21, 2014
*/
class ItemDataReader
{
	public static function get_item_ids_by_name($item_name)
	{
		return self::_item_name_exists($item_name);
	}

	public static function item_meta_exists($item_id, $meta_key)
	{
		return self::_item_meta_exists($item_id, $meta_key);
	}
	
	public static function get_item_data($item_id, $data_key='')
	{
    	$data_key = trim($data_key);
		return empty($data_key) ? self::_get_item_full_data($item_id) : self::_get_item_single_data($item_id, $data_key);
   	}
	
	private static function _item_name_exists($item_name)
	{
		$sql = "SELECT `item_id` from ". ItemManager::get_tables_prefix(). "item_meta WHERE `name` = '". DataSanitizer::sanitize_data_for_db_query($item_name). "'";
		$db_obj = self::_get_db_object();
		$db_obj->execute_query($sql);
		$matrix = $db_obj->return_result_as_matrix();
		$rows   = ArrayManipulator::reduce_redundant_matrix_to_array($matrix, 'id');
		if(empty($rows))
		{
			return 0;
		}
		return count($rows) > 1 ? $rows : $rows[0];
	}
	
	/**
	* @author Michael Orji
	*/
	private static function _get_item_single_data($item_id, $data_key)
	{
		$items_table = ItemManager::get_tables_prefix(). "items";
		$umeta_table = ItemManager::get_tables_prefix(). "item_meta";
		$db_object   = self::_get_db_object();
		
		$is_basic_data = $db_object->column_exists_in_table($items_table, $data_key, DB_NAME);
		$is_meta_data  = self::_item_meta_exists($item_id, $data_key);
		
		if( !$is_basic_data && !$is_meta_data )
		{
			return false;
		}
		if($is_meta_data)
		{ 
			return self::_get_item_meta($item_id, $data_key);
		}
		
		$db_object->execute_query("SELECT $data_key FROM $items_table WHERE id = $item_id");
		$row = $db_object->get_rows();
		return $row[$data_key];
	}
	
	/*
	* @author Michael Orji
	*/
	private static function _get_item_full_data($item_id)
	{ 
		$data   = array();
		
		$db_obj = self::_get_db_object();
		$db_obj->execute_query( "SELECT * FROM ". ItemManager::get_tables_prefix(). "items WHERE id = ". DataSanitizer::sanitize_data_for_db_query($item_id). " LIMIT 1" );
    
		$row       = $db_obj->get_rows(); 
		$item_meta = self::_get_item_meta($item_id);
		
		if( !empty($row) )
		{
			foreach($row AS $key => $value)
			{
				$data[$key] = $value;
			}
		}
		
		if( !empty($item_meta) )
		{
			foreach($item_meta AS $meta_array)
			{  
				$data['item_id']      = $meta_array['item_id'];
				$data['item_meta_id'] = $meta_array['id'];
				
				foreach($meta_array AS $key => $value)
				{
					if( ($key != 'id') && ($key != 'item_id') )
					{
						$dkey = $meta_array['meta_key'];
						$data[$dkey] = $meta_array['meta_value'];
					}
				}
				
			}
		}
		
       	return $data;
	}
	
	private static function _item_meta_exists($item_id, $meta_key)
	{  
		$db_object = self::_get_db_object();
		$db_object->execute_query("SELECT `item_id` FROM ". ItemManager::get_tables_prefix(). "item_meta WHERE item_id = $item_id AND meta_key = '$meta_key'");
		return $db_object->num_rows() > 0;
	}
	
	private static function _get_item_meta($item_id, $meta_key='')
	{
		$meta_key = trim($meta_key);
		if( !empty($meta_key) && !self::_item_meta_exists($item_id, $meta_key))
		{ 
			return false;
		}
		$sql  = "SELECT * FROM ". ItemManager::get_tables_prefix(). "item_meta WHERE item_id = ". DataSanitizer::sanitize_data_for_db_query($item_id). "";
		$sql .= !empty($meta_key) ? " AND meta_key = '". DataSanitizer::sanitize_data_for_db_query($meta_key). "'" : "" ;
		
		$db_object = self::_get_db_object();
		$db_object->execute_query($sql);
		
		if(!empty($meta_key))
		{
			$rows = $db_object->get_rows();
			
			$meta_value = Util::is_stringified($rows['meta_value']) ? Util::unstringify($rows['meta_value']) : $rows['meta_value'];
			return $meta_value;
		}
		else
		{
			$mat  = array();
			$rows = $db_object->return_result_as_matrix();
			
			foreach($rows AS $row)
			{
				$meta_value = Util::is_stringified($row['meta_value']) ? Util::unstringify($row['meta_value']) : $row['meta_value'];
				$mat[] = array('id'=>$row['id'], 'item_id'=>$row['item_id'], 'meta_key'=>$row['meta_key'], 'meta_value'=>$meta_value);
			}
			
			return $mat;
		}
	}
	private static function _get_db_object()
	{
		return ItemManager::get_db_object();
	}
}