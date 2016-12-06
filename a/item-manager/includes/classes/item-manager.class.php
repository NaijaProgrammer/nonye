<?php
/**
* @author : Michael Orji
* @date: Oct 14, 2013
*/
class ItemManager
{
	public static function get_tables_prefix()
	{
		return TABLES_PREFIX;
	}
	
	public static function get_tables()
	{
		$tp = self::get_tables_prefix();
		return array( 'items_table'=>$tp. 'items', 'item_meta_table'=>$tp. 'item_meta' );
	}
	
	public static function get_db_object()
	{
		return Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}
	
	public static function execute_query($query_type, $query_str)
	{
		$db_obj = self::get_db_object();
		$db_obj->execute_query($query_str);
		
		switch(strtolower($query_type))
		{
			case 'insert' : return $db_obj->last_insert_id();
			case 'update' : 
			case 'delete' : return $db_obj->affected_rows();
			case 'select' : return ( ($db_obj->num_rows() == 1) ? $db_obj->get_rows() : $db_obj->return_result_as_matrix() );
			default       : return $db_obj;
		}
	}
	
	/*
	* $col_names e.g array('id', 'item_id', 'item_category', 'firstname', 'surname', 'age')
	*
	* $where_data matrix: e.g [ 
	*	array('key'=>'firstname', 'operator'=>'=',    'value'=>'james'),
	*	array('key'=>'surname',   'operator'=>'like', 'value'=>'%m'),
	*	array('key'=>'age',       'operator'=>'>',    'value'=>15)
	*	...
	* ]
	*
	* $order_data = array('date_added'=>'DESC', 'firstname'=>'ASC', 'lastname'=>'DESC', ...)
	*
	* This method enables greater flexibility in retrieving than get_items items, as it works like SQL itself.
	* For example, below are two example queries to retrieve the 'id', 'date' and 'item_category' WHERE the item_category == questions
	* 1. Using the select() method:
		$query = ItemManager::select(
			$query_data = array('id', 'date_added', 'item_category'), 
			$where_data = array( array('key'=>'item_category', 'operator'=>'=', 'value'=>'questions') ), 
			$order_data = array( 'date_added'=>'DESC' ), 
			$limit = 0
		);

	* 2. Using the get_items() method
		$query = ItemManager::get_items(
			$conditions = array('item_category'=>'questions', 'data_to_get'=>array('id', 'date_added', 'item_category')), 
			$orders = array(), 
			$limit = ''
		);
	
	* The get_items() method only allows selection by 'equals (=)' comparison, 
	* The select() method allows selection by any mysql comparison operator (=, like, > %m m%, %m%, ...)
	* 
	* The get_items() method doesn't allow ordering by date_added or by any column that is not present in the items_meta_table
	* The select() method allows ordering by any column in either the items or item_meta_table
	* 
	* The get_items_method() uses only the 'AND' combinator for sql queries 
	* The select() method lets the user specify whether is an AND or OR or any other combinator
	*
	* @return: 
	* string for a query yielding a single result-set on a single column select, e.g 'title' column select with a single result will give : 'here is the title'
	* numeric-array for a query yielding multiple result-set on a single column [0=>'here is title 1', 1=>'here is title 2', ...]
	* associative-array for a query yielding a single result-set on multiple column select ['id'=>2, 'date_added'=>'yyyy-mm-dd HH:mm:ss', 'title'=>'the title']
	* multidimensianal-array for a query yielding multiple result-set on multiple column select [
	* 	0 => array['id'=>2, 'date_added'=>'yyyy-mm-dd HH:mm:ss', 'title'=>'the first title']
	*	1 => array['id'=>3, 'date_added'=>'yyyy-mm-dd HH:mm:ss', 'title'=>'the second title']
	*	...
	* ]
	*/
	public static function select($col_names, $where_data = array(), $order_data = array(), $limit = 0)
	{ 
		$return_data   = array();
		$returned_data = self::execute_query( 'select', self::build_select_query($col_names, $where_data, $order_data, $limit) );
		
		if( !empty($col_names) && is_array($col_names) )
		{
			if( count($returned_data) == 1 )
			{
				$returned_id = $returned_data['id'];
				
				foreach($col_names AS $col_name)
				{
					$return_data[$col_name] = self::get_item_data($returned_id, $col_name);
				}
				
				if( count($return_data) == 1 )
				{
					$return_data = $return_data[$col_names[0]];	
				}
			}
			
			else
			{
				$returned_ids  = ArrayManipulator::reduce_redundant_matrix_to_array($returned_data, 'id');
				
				for($i = 0, $len = count($returned_ids); $i < $len; $i++)
				{
					$curr_id = $returned_ids[$i];
					
					foreach($col_names AS $col_name)
					{   
						$return_data[$i][$col_name] = self::get_item_data($curr_id, $col_name);
					}
				}
				
				if( count($col_names) == 1)
				{   
					$return_data = ArrayManipulator::reduce_redundant_matrix_to_array($return_data, $col_names[0]);
				}
			}
		}
		
		return $return_data;
	}
	
	/*
	* $query_data e.g array('id', 'item_id', 'item_category', 'firstname', 'surname', 'age')
	*
	* $where_data matrix: e.g [ 
	*	array('key'=>'firstname', 'operator'=>'=',    'value'=>'james'),
	*	array('key'=>'surname',   'operator'=>'like', 'value'=>'%m'),
	*	array('key'=>'age',       'operator'=>'>',    'value'=>15)
	*	...
	* ]
	*
	* $order_data = array('date_added'=>'DESC', 'firstname'=>'ASC', 'lastname'=>'DESC', ...)
	*/
	private static function build_select_query($query_data, $where_data = array(), $order_data = array(), $limit = 0)
	{
		$db_object           = self::get_db_object();
		$item_table          = self::get_tables_prefix(). "items"; 
		$item_meta_table     = self::get_tables_prefix(). "item_meta";
		$item_table_cols     = $db_object->get_table_columns(self::get_tables_prefix()."items");
		$meta_table_cols     = $db_object->get_table_columns(self::get_tables_prefix()."item_meta");
		$sql                 = "SELECT ";
		$selection_statement = '';
		
		if( !empty($query_data['is_aggregate_query']) )
		{
			for($i = 0; $i < 2; $i++)
			{
				if($query_data[$i] != 'is_aggregate_query')
				{
					$selection_statement .= $query_data[$i]; //enables *, COUNT(*), COUNT(*) AS count
					break;
				}
			}
		}
		
		else
		{
			$selection_statement .= "{$item_table}.`id`";
		}
		
		$sql .= $selection_statement. " ";
		$sql .= "FROM ". $item_table. ", ". $item_meta_table. " ";

		if( !empty($where_data) && is_array($where_data) )
		{
			$where_clause      = "WHERE (";
			$first_where_key   = $where_data[0]['key'];
			$first_where_value = $where_data[0]['value'];
			
			if( in_array( $first_where_key, $item_table_cols ) )
			{
				$where_clause .= " ({$item_table}.`$first_where_key` ". $where_data[0]['operator']. " '". $first_where_value. "') ";
			}
			else
			{
				$where_clause .= "({$item_meta_table}.`meta_key` = '".   $first_where_key.   "' ";
				$where_clause .= "AND {$item_meta_table}.`meta_value` ". $where_data[0]['operator']. " '". $first_where_value. "') ";
			}
		
			foreach($where_data AS $curr_data)
			{
				$data_key   = $curr_data['key'];
				$operator   = $curr_data['operator'];
				$data_value = $curr_data['value'];
				
				if($data_key != $first_where_key)
				{
					if( in_array( $data_key, $item_table_cols ) )
					{
						$where_clause .= "AND {$item_table}.`$data_key` ". $operator. " '". $data_value. "' ";
					}
					else
					{		
						$where_clause .= "OR ({$item_meta_table}.`meta_key` = '". $data_key. "' ";
						$where_clause .= "AND {$item_meta_table}.`meta_value` ".  $operator. "'". $data_value. "') ";
					}
				}
			}
			
			$where_clause .= ") ";
			$where_clause .= "AND ( {$item_meta_table}.`item_id` = {$item_table}.`id`) ";
			$where_clause .= "GROUP BY `item_id` having count(*) = ". count($where_data);
			
			$sql .= $where_clause. " ";
		}
		
		if( !empty($order_data) && is_array($order_data) )
		{
			$order_by_clause = "ORDER BY ";
			
			foreach($order_data AS $key => $value)
			{
				if( in_array( $key, $item_table_cols ) )
				{
					$order_by_clause .= "{$item_table}.`$key` $value, ";
				}
				else
				{
					$order_by_clause .= "{$item_meta_table}.`$key` $value, ";
				}
			}
				
			$order_by_clause = rtrim(trim($order_by_clause), ','); //substr($order_by_clause, 0, -1); // remove trailing ,
			$sql .= $order_by_clause. " ";
		}
		
		if( !empty($limit) )
		{
			$sql .= "LIMIT ". $limit;
		}
		
		return trim($sql);
	}
	
	public static function meta_value_exists($meta_key, $meta_value)
	{
		$sql = "SELECT item_id FROM ". self::get_tables_prefix(). "item_meta ".
		       "WHERE meta_key = '". DataSanitizer::sanitize_data_for_db_query($meta_key). "' ".
			   "AND meta_value = '". ( Util::is_scalar($meta_value) ? $meta_value : Util::stringify($meta_value) ). "'";
			   
		$db_obj = self::get_db_object();
		$db_obj->execute_query($sql);
		
		return ($db_obj->num_rows() > 0);
	}
	
	public static function get_items_count($conditions = array())
	{
		$item_table      = self::get_tables_prefix(). "items";
		$item_meta_table = self::get_tables_prefix(). "item_meta";
		
		$ids_sql = "SELECT COUNT(*) as `num` FROM ". $item_meta_table;
		
		if(!empty($conditions))
		{
			$counter = 0;
			
			$arr_keys = array_keys($conditions);
			$first_condition_key   = array_shift($arr_keys); //array_shift(array_keys($conditions));
			$first_condition_value = $conditions[$first_condition_key];
			$stringified_first_condition_value = Util::stringify($first_condition_value);
			$first_condition_value = Util::is_scalar($first_condition_value) ? $first_condition_value : Util::stringify($first_condition_value);
			
			$ids_sql .= " WHERE ";
			
			$ids_sql .= "(";
			
			$ids_sql .= "({$item_meta_table}. meta_key  = '$first_condition_key' ";
			$ids_sql .= " AND {$item_meta_table}. meta_value = '". $first_condition_value. "')";
			
			$ids_sql .= "OR ({$item_meta_table}. meta_key  = '$first_condition_key' ";
			$ids_sql .= " AND {$item_meta_table}. meta_value = '". $stringified_first_condition_value. "')";
			
			$ids_sql .= ")";
			
			
			
			foreach($conditions AS $condition => $value)
			{
				if($condition != $first_condition_key)
				{
					$value = DataSanitizer::sanitize_data_for_db_query($value);
					$value = is_numeric($value) ? intval($value) : $value; 
					$stringified_value = Util::stringify($value);
					$value = Util::is_scalar($value) ? $value : Util::stringify($value);
					
					$ids_sql .= "AND";
					
					$ids_sql .= "(";
					
					$ids_sql  .= " ({$item_meta_table}.meta_key  = '${condition}' ";
					$ids_sql  .= " AND {$item_meta_table}.meta_value = '$value')";
					
					
					$ids_sql .= " OR ( {$item_meta_table}.meta_key  = '${condition}' ";
					$ids_sql .= " AND {$item_meta_table}.meta_value = '$value')";
					
					$ids_sql .= ")";
					
					//++$counter;
				}
				++$counter;
			}
				
			if($counter > 0)
			{
				$ids_sql .= " GROUP BY `item_id` having count(*) = $counter";
			}
		}
		
		$db_obj = self::get_db_object();
		$db_obj->execute_query($ids_sql);
		$row = $db_obj->get_rows();
		
		return $db_obj->num_rows(); //$row['num'];
	}
	
	public static function get_items( $conditions = array(), $orders = array(), $limit = '' )
	{	
		return self::_get_items( $conditions, $orders, $limit );
	}
	
	public static function get_item_ids_by_name($item_name)
	{
		return ItemDataReader::get_item_ids_by_name($item_name);
	}
	
	public static function item_meta_exists($item_id, $meta_key)
	{
		return ItemDataReader::item_meta_exists($item_id, $meta_key);
	}
	
	public static function get_item_data($item_id, $data_key='')
	{
		return ItemDataReader::get_item_data($item_id, $data_key);
	}
	
	/**
	* $opts Array in the form ('name'=>'windows7', 'category'=>'pc', ...)
	*/
	public static function add_item( $opts = array() )
	{
		return ItemDataWriter::add_item($opts);
	}
	
	/**
	* $matrix e.g = array('data_key'='', 'data_value'='', 'overwrite'=true), ('data_key2'=>'', 'data_value2'=>'')
	*/
	public static function update_item($item_id, $matrix)
	{
		ItemDataWriter::update_item($item_id, $matrix);
	}
	
	public static function get_items_by_category($category, $orders=array())
	{
		return self::_get_items( array('category'=>$category), $orders );
	}
	
	public static function delete_item($item_id, $opts=array() )
	{
		$item_id = is_string($item_id) ? trim($item_id) : $item_id;
		
		if( empty($item_id) || intval($item_id) <= 0 )
		{
			return false;
		}
		$item_exists = self::get_item_data($item_id);
		//if(!$item_exists)
		if(empty($item_exists))
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
			return self::_set_item_as_deleted($item_id);
		}
		
		$db_obj = self::get_db_object();
		$delete = $db_obj->delete_records( self::get_tables_prefix(). "items", array('id'=>$item_id), $limit = '');
		
		if($delete)
		{
			$db_obj->delete_records( self::get_tables_prefix(). "item_meta", array('item_id'=>$item_id), $limit = '');
			return true;
		}
		
		return false;
	}
	
	private static function _set_item_as_deleted($item_id)
	{
		return self::update_item( $item_id, array( array('data_key'=>'deleted', 'data_value'=>true) ) );
	}
	
	/**
	* array( 'conditions'=>array('category'), 'order_by'=>array() )
	*/
	public static function get_items_as_dropdown_menu_options( $opts = array() )
	{
		$options_str  = '';
		
		$conditions = isset($opts['conditions']) && is_array($opts['conditions']) ? $opts['conditions'] : array();
		$order_by   = isset($opts['order_by'])   && is_array($opts['order_by'])   ? $opts['order_by']   : array();
		$select_element_name = isset($opts['select_element_name']) ? $opts['select_element_name'] : '';
		global $$select_element_name;
		
		$items        = self::_get_items( $conditions, $order_by ); 
		$num_of_items = count($items);
		if($num_of_items > 0)
		{
			for($i = 0; $i < $num_of_items; $i++)
			{
				$item_id   = $items[$i]['id'];
				$item_name = $items[$i]['name'];
				$options_str .= '<option value="'. $item_id. '" '. self::_set_as_selected_option($item_id, $$select_element_name). '>'. $item_name. '</option>';
			}
		}
		
		return $options_str;
	}
	
	private static function _set_as_selected_option($option_value, $selected_value)
	{
		return $option_value == $selected_value ? 'selected="selected"' : '';
	}
	
	/**
	* E.g $conditions = array('category'=>'users', 'sub_category'=>'nigerians', 'data_to_get'=>array('firstname', 'lastname'))
	* $orders = array('date_added'=>'DESC'),
	* $limit = '10, 20'
	*/
	private static function _get_items( $conditions = array(), $orders = array(), $limit = '' )
	{
		$item_table      = self::get_tables_prefix(). "items";
		$item_meta_table = self::get_tables_prefix(). "item_meta";
		
		if(!is_array($conditions) || empty($conditions) )
		{
			$sql = "SELECT * FROM $item_table";
			
			if(!empty($orders) && is_array($orders))
			{ 
				$order_by_clause = " ORDER BY";
				foreach($orders AS $key => $value)
				{
					$order_by_clause .= " `$key` $value,";
				}
				// remove trailing ,
				$order_by_clause = substr($order_by_clause, 0, -1);
				$sql .= $order_by_clause;
			}
			
			$db_obj = self::get_db_object();
			$db_obj->execute_query($sql);
			return $db_obj->return_result_as_matrix();
		}
		
		else
		{
			$matrix = array();
			$counter = 0;
			
			$arr_keys = array_keys($conditions);
			$first_condition_key   = array_shift($arr_keys); //array_shift(array_keys($conditions));
			$first_condition_value = $conditions[$first_condition_key];
			$stringified_first_condition_value = Util::stringify($first_condition_value);
			$first_condition_value = Util::is_scalar($first_condition_value) ? $first_condition_value : Util::stringify($first_condition_value);
			
			
			$ids_sql = "SELECT item_id FROM ". $item_meta_table. " WHERE ";
			
			$ids_sql .= "(";
			
			$ids_sql .= "({$item_meta_table}. meta_key  = '$first_condition_key' ";
			$ids_sql .= " AND {$item_meta_table}. meta_value = '". $first_condition_value. "')";
			
			$ids_sql .= "OR ({$item_meta_table}. meta_key  = '$first_condition_key' ";
			$ids_sql .= " AND {$item_meta_table}. meta_value = '". $stringified_first_condition_value. "')";
			
			$ids_sql .= ")";
			
			foreach($conditions AS $condition => $value)
			{
				if($condition != 'data_to_get')
				{
					if($condition != $first_condition_key)
					{
						$value = DataSanitizer::sanitize_data_for_db_query($value);
						$value = is_numeric($value) ? intval($value) : $value; 
						$stringified_value = Util::stringify($value);
						$value = Util::is_scalar($value) ? $value : Util::stringify($value);
						$ids_sql  .= " OR ({$item_meta_table}.meta_key  = '${condition}' ";
						$ids_sql  .= " AND {$item_meta_table}.meta_value = '$value')";
						
						
						$ids_sql  .= " OR ({$item_meta_table}.meta_key  = '${condition}' ";
						$ids_sql  .= " AND {$item_meta_table}.meta_value = '$stringified_value')";
						//++$counter;
					}
					
					++$counter;
				}
         	}
			
			if($counter > 0)
			{
				$ids_sql .= " GROUP BY `item_id` having count(*) = $counter";
			}
			
			if(!empty($orders) && is_array($orders))
			{ 
				$order_by_clause = " ORDER BY";
				foreach($orders AS $key => $value)
				{
					$order_by_clause .= " `$key` $value,";
				}
				
				$order_by_clause = substr($order_by_clause, 0, -1); // remove trailing ,
				$ids_sql .= $order_by_clause;
			}
			
			if(!empty($limit))
			{
				$ids_sql .= " LIMIT ". $limit;
			}
			//echo $ids_sql; exit;
			$db_obj = self::get_db_object();
			$db_obj->execute_query($ids_sql);
			$ids = $db_obj->return_result_as_matrix();
			$ids = ArrayManipulator::reduce_redundant_matrix_to_array($ids, 'item_id');
			
			for($i = 0; $i < count($ids); $i++)
			{
				$current_id = $ids[$i];
				
				if( !empty($conditions['data_to_get']) && is_array($conditions['data_to_get']) )
				{
					foreach($conditions['data_to_get'] AS $the_key)
					{
						$the_matrix[$i][$the_key] = self::get_item_data($current_id, $the_key);
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
					$matrix[$i]['id']   = $current_id;
					$matrix[$i] = self::get_item_data($current_id);
				}
			}
		
			return $matrix;
		}
	}
	
	public static function load_dropdown_list( $opts = array() )
	{
		return ItemDropDownLoader::load_dropdown_list($opts);
	}
	
	public static function load_dropdown_list_option( $opts = array() )
	{
		return ItemDropDownLoader::load_dropdown_list_option($opts);
	}
	
	private static function _set_db_object()
	{
		self::$db_object = new MySqlExtended();
		MySqlExtended::set_active_connection( self::$db_object->connect(ITEM_MANAGER_DB_SERVER, ITEM_MANAGER_DB_USER, ITEM_MANAGER_DB_PASS, ITEM_MANAGER_DB_NAME) );
		//self::$db_object = MySqlExtended::get_instance( ITEM_MANAGER_DB_SERVER, ITEM_MANAGER_DB_USER, ITEM_MANAGER_DB_PASS, ITEM_MANAGER_DB_NAME );
	}
}
