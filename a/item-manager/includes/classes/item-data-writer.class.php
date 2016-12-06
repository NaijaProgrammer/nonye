<?php
/**
* @author : Michael Orji
* @date: Oct 21, 2014
*/
class ItemDataWriter
{
	/**
	* $opts Array in the form ('name'=>'windows7', 'category'=>'pc', ...)
	*/
	public static function add_item( $opts )
	{
		if( empty($opts) )
		{
			return false;
		}
		
		foreach($opts AS $key => $value)
		{
			$meta_data[] = array('data_key'=>$key, 'data_value'=>$value);
		}
		
		$db_object          = self::_get_db_object();
		$opts['date_added'] = $db_object->sql_term( 'NOW()' );
		$item_id            = $db_object->insert_records( ItemManager::get_tables_prefix(). "items", $opts );
		if( $item_id )
		{
			self::update_item($item_id, $meta_data);
			return $item_id;
		}
	}
	
	/**
	* $matrix e.g = array( array('data_key'='', 'data_value'='', 'overwrite'=true), ('data_key'=>'', 'data_value'=>'') )
	*/
	public static function update_item($item_id, $matrix)
	{
		$db_object        = self::_get_db_object();
		$basic_data_types = $db_object->get_table_columns(ItemManager::get_tables_prefix()."items");
		
		foreach($matrix AS $opts)
		{
			$data_key    = $opts['data_key'];
			$data_value  = $opts['data_value'];
			$unique      = isset($opts['overwrite']) ? $opts['overwrite'] : false; //for meta table
	
			if( in_array( $data_key, $basic_data_types ) && $data_key != 'id' )
			{
				$db_object->update_table_column(ItemManager::get_tables_prefix()."items", $data_key, $data_value, array('id'=>$item_id));
			}
			else
			{
				$meta_data[] = array('meta_key'=>$data_key, 'meta_value'=>$data_value);
				self::_update_item_meta_matrix($item_id, $meta_data);
			}
		}
	}
   
	/**
	* $matrix e.g = array('meta_key'='', 'meta_value'='', 'overwrite'=true), ('meta_key2'=>'', 'meta_value2'=>'')
	*/
	private static function _update_item_meta_matrix($item_id, $matrix)
	{
		foreach($matrix AS $opts)
		{
			$meta_key   = isset($opts['meta_key'])   ? $opts['meta_key']   : ''; //field key: e.g 'matric_no'
			$meta_value = isset($opts['meta_value']) ? $opts['meta_value'] : ''; //field value
			$unique     = isset($opts['overwrite'])  ? $opts['overwrite']  : false;
			$curr_value = isset($opts['curr_value']) ? $opts['curr_value'] : '';
			self::_update_item_meta($item_id, $meta_key, $meta_value, $unique, $curr_value);
		}
	} 
	
	
	/** 
	* Update metadata for item. If no value already exists for the specified item
    * and metadata key, the metadata will be added.
	*/
	private static function _update_item_meta($item_id, $meta_key, $meta_value, $unique, $prev_value='')
	{
		$meta_key   = is_string($meta_key)   ? trim($meta_key)   : $meta_key; //field key: e.g 'matric_no'
		$meta_value = is_string($meta_value) ? trim($meta_value) : $meta_value; //field value
		$prev_value = isset($prev_value)     ? $prev_value       : '';
		$prev_value = is_string($prev_value) ? trim($prev_value) : $prev_value;
		
		//if( empty($meta_key) || empty($meta_value))
		if( empty($meta_key) || $meta_value == '' )
		{
			return false;
		}
		
		// Compare existing value to new value if no prev value given and the key exists only once.
		if ( empty($prev_value) )
		{
			$old_value = ItemManager::get_item_data($item_id, $meta_key);
			if ( count($old_value) == 1 ) 
			{   
		        /*
				if(is_array($old_value))
				{
					if( isset($old_value['meta_value']) )
					{
						$old_value = $old_value['meta_value'];
					}
					elseif( isset($old_value[0]) )
					{
						$old_value = $old_value[0];
					}
				}
				$compare_value = $old_value;
				*/
				
				$compare_value = ( is_array($old_value) ? $old_value['meta_value'] : $old_value );
				
				if ( $compare_value === $meta_value ) //if key exists only once, then no need to duplicate same key with same value
				{
					return false;
				}
			}
		}

		// If meta doesn't exist, then and a new meta
		if ( ! ItemManager::item_meta_exists($item_id, $meta_key) )
		{
			return self::_insert_item_meta($item_id, $meta_key, $meta_value, $unique);
		}

		$where_clause = array( 'item_id'=>$item_id, 'meta_key'=>$meta_key );
		
		if ( !empty( $prev_value ) ) 
		{
			$prev_value = Util::is_scalar($prev_value) ? $prev_value : Util::stringify($prev_value);
			$where_clause['meta_value'] = $prev_value;
		}
		
		$meta_value = Util::is_scalar($meta_value) ? $meta_value : Util::stringify($meta_value);
		return self::_get_db_object()->update_table_column(ItemManager::get_tables_prefix()."item_meta", 'meta_value', $meta_value, $where_clause);
	}
	
	private static function _insert_item_meta($item_id, $meta_key, $meta_value, $unique=true)
	{
		$db_obj     = self::_get_db_object();
		$meta_key   = is_string($meta_key)   ? trim($meta_key)   : $meta_key; //field key: e.g 'matric_no'
		$meta_value = is_string($meta_value) ? trim($meta_value) : $meta_value; //field value
		//if( empty($meta_key) || empty($meta_value) )
		if( empty($meta_key) || $meta_value == '' )
		{
			return false;
		}
		if( $unique && ItemManager::item_meta_exists($item_id, $meta_key) )
		{
			return false;
		}
		
		$meta_value = Util::is_scalar($meta_value) ? $meta_value : Util::stringify($meta_value);
		return $db_obj->insert_records(ItemManager::get_tables_prefix(). 'item_meta', array('item_id'=>$item_id, 'meta_key'=>$meta_key, 'meta_value'=>$meta_value));
	}
	
	private static function _get_db_object()
	{
		return ItemManager::get_db_object();
	}
}