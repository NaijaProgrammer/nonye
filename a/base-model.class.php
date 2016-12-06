<?php
/*
 * Class BaseModel
 * @author Michael Orji
*/
abstract class BaseModel
{
	/*
	 * The db object
	*/
	protected $db; 
	
	public function __construct()
	{
		$this->db = Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}
	
	public function insert($table, $data)
	{
		$dbh           = self::get_db_connection();
		$bind_str      = '';
		$fields        = '';
		$placeholders  = '';
		$values        = array();
		
		if ( empty($table) || empty($data) )
		{
			return false;
		}
			
		foreach( $data AS $column => $value )
		{
			$fields .= "`$column`,";
			$placeholders .= "?,"; //http://stackoverflow.com/a/14781240/1743192
			$values[] = $value;
		}
		
		foreach($values AS $value)
		{
			switch(gettype($value))
			{
				case 'integer' : $bind_str .= 'i'; break;
				case 'double'  : $bind_str .= 'd'; break;
				case 'string'  : $bind_str .= 's'; break;
			}
		}
		
		array_unshift($values, $bind_str);
		
		$fields = rtrim( trim($fields), ',' );
		$placeholders = rtrim( trim($placeholders), ',' );
		
		$stmt = $dbh->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");
		
		call_user_func_array( array($stmt, 'bind_param'), self::reference_values($values) );
			
		$stmt->execute();
			
		return $stmt->affected_rows ?  $stmt->insert_id : false;
	}
	
	protected function update_table($table, $update_data, $where='')
	{
		$tp            = self::get_tables_prefix();
		$dbh           = self::get_db_connection();
		$placeholders  = '';
		$where_clause  = '';
		$bind_str       = '';
		$update_values = array();
		$where_values  = array();
		$count         = 0;
		
		foreach( $update_data AS $column => $value )
		{
			$placeholders .= "`$column` = ?, "; //http://stackoverflow.com/a/14781240/1743192
			$update_values[] = $value;
		}
		
		//replace this loop with the new method 
		//$where_data = self::parse_where_data($where)
		//$where_clause = $where_data['where_clause']
		//$where_values = $where_data['where_values']
		foreach ( $where as $field => $value )
		{
			if ( $count > 0 )
			{
				$where_clause .= " AND ";
			}
				
			$where_clause .= "`$field` = ?";
			$where_values[] = $value;
				
			$count++;
		}
		
		$placeholders = rtrim( trim($placeholders), ',' );
		$stmt = $dbh->prepare( "UPDATE {$table} SET {$placeholders} WHERE {$where_clause}" );
		$replacement_values = array_merge($update_values, $where_values);
		
		//replace this loop and the array_unshift()with the new method:
		//self::parse_replacement_values($replacement_values)
		foreach($replacement_values AS $value)
		{
			switch(gettype($value))
			{
				case 'integer' : $bind_str .= 'i'; break;
				case 'double'  : $bind_str .= 'd'; break;
				case 'string'  : $bind_str .= 's'; break;
			}
		}
		
		array_unshift($replacement_values, $bind_str);
		
		//replace the call_user_func_array() with the new method:
		//$stmt = self::bind_statement_replacement_values(&$stmt, &$replacement_values);
		call_user_func_array( array($stmt, 'bind_param'), self::reference_values($replacement_values) );
		
		$stmt->execute();
		$affected_rows = $stmt->affected_rows;
		$stmt->close();
		
		
		// Since every class shares the same instance,
		// when we close the connection, we get a warning of:
		// mysqli::query(): Couldn't fetch mysqli in C:\wamp\www\sites\zamaju-forums\a\pcl\sql\my_sql.class.php on line ...
		// and as a result, the UserManagerSessionManager is not able to successfully delete expired sessions
		//$dbh->close();
		
		return $affected_rows;
	}
	
	protected function delete_from_table($table, $id)
	{
		$tp   = self::get_tables_prefix();
		$dbh  = self::get_db_connection();
		$stmt = $dbh->prepare("DELETE FROM {$table} WHERE id = ?");
			
		$stmt->bind_param('d', $id);
		$stmt->execute();
		$affected_rows = $stmt->affected_rows;
		$stmt->close();

		return $affected_rows;
	}
	
	/*
	* returns a MySqlExtended object
	*/
	public static function get_db()
	{
		return Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}
	
	/*
	* returns the native MySqli connection object
	*/
	public static function get_db_connection()
	{
		return self::get_db()->get_connection();
	}
	
	public static function get_tables_prefix()
	{
		return TABLES_PREFIX;
	}
	
	/*
	* @return array with members:
	* where_clause string to use in a sql query
	* where_values array of values to use as replacement for placeholders ( to bind to params ) in a prepared statement
	*/
	protected static function parse_where_data($where=array())
	{
		$count        = 0;
		$where_values = array();
		$where_clause = '';
	
		if(empty($where))
		{
			return array();
		}
		
		foreach ( $where as $field => $value )
		{
			if ( $count > 0 )
			{
				$where_clause .= " AND ";
			}
				
			$where_clause .= "`$field` = ?";
			$where_values[] = $value;
				
			$count++;
		}
		
		return array('where_clause'=>$where_clause, 'where_values'=>$where_values);
	}
	
	protected static function parse_order_data($order_data, $eligible_cols)
	{
		$order_str = '';
		
		if( is_array($order_data) && !empty($order_data) )
		{
			$eligible_orders = array('asc', 'desc');
			$order_str = " ORDER BY ";

			foreach($order_data AS $col => $value)
			{
				if( in_array($col, $eligible_cols) && in_array(strtolower($value), $eligible_orders) )
				{
					$order_str .= "`$col` $value,";
				}
			}

			$order_str = rtrim($order_str, ',');
		}

		return $order_str;
	}
	
	/*
	* Takes an array
	* Returns an array with a param-binding string as its first element
	* You can then use the returned $replacement_values array like this: 
	* call_user_func_array( array($stmt, 'bind_param'), $replacement_values );
	*/
	protected static function parse_replacement_values($replacement_values)
	{
		$bind_str = '';
		
		if(empty($replacement_values))
		{
			return array();
		}
		
		foreach($replacement_values AS $value)
		{
			switch(gettype($value))
			{
				case 'integer' : $bind_str .= 'i'; break;
				case 'double'  : $bind_str .= 'd'; break;
				case 'string'  : $bind_str .= 's'; break;
			}
		}
		
		array_unshift($replacement_values, $bind_str);
		return $replacement_values;
	}
	
	protected static function bind_statement_replacement_values(&$stmt, $replacement_values)
	{
		call_user_func_array( array($stmt, 'bind_param'), self::reference_values($replacement_values) );
		return $stmt;
	}
	
	private static function reference_values($array)
	{
		$refs = array();
		foreach ($array as $key => $value)
		{
			$refs[$key] = &$array[$key]; 
		}
		
		return $refs; 
	}
}