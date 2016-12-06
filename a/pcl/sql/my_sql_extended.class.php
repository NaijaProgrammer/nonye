<?php 
/**
* @augments the MySql Class
* @author Michael Orji
*/
class MySqlExtended extends MySql 
{
	private static $instance = NULL;
	
	public function __construct( $host = '', $user = '', $password = '', $database = '' )
	{ 
		parent::__construct($host, $user , $password, $database);
	}
	
	public static function get_instance($host = '', $user = '', $password = '', $database = '')
	{ 
		if(!self::$instance)
		{
			self::$instance = new self($host, $user , $password, $database);
		}
		return self::$instance;
	}

	public static function connection_established()
	{
		return !empty(self::$connections);
	}

	/**
	* determines whether a (unique) value already exists in a table
	* @return: a boolean value indicating whether the value already exists(true) or not(false)
	*/
	public function in_table($table, $unique_field_name, $unique_field_value)
	{
 		$sql =  "SELECT * FROM $table ".
        		"WHERE $unique_field_name = '". DataSanitizer::sanitize_data_for_db_query($unique_field_value). "' ";
		$this->execute_query($sql);
		return ( $this->num_rows() > 0 );
	}
	
	/**
	* an improvement over the 'in_table' method
	* checks if a record already exists in a table
	* return true if it does, false otherwise
	* @param: string the name of the table
	* @param: an associative array of conditions
	* @author Michael Orji
	* @date Oct. 14, 2013
	*/
	public function record_exists_in_table($table, $conditions = array() )
	{
		$sql = "SELECT * FROM $table WHERE true=true";
      	if( is_array($conditions) && !empty($conditions) )
		{
         	foreach($conditions AS $condition => $value)
			{
				$value = is_numeric($value) ? $value : "\"$value\"";
          		$sql  .= " AND $condition = $value";
         	}
      	}
		$this->execute_query($sql);
		return ( $this->num_rows() > 0 );
	}

	/*
	* @updates a (specified) column in a table
	* @date: April 13, 2012
	* @made an OOP method on April 11, 2013
	* @return: int
	*/
	public function update_table_column($table_name, $option_column_name, $option_value,  $where_clause_array = array())
	{
		return $this->update_records($table_name, array($option_column_name=>$option_value), $where_clause_array);
	}
	
	/** 
	* @returns the result rows of an sql query as a multi-dimensional array
	* @date: June 4, 2010
	* @converted to OOP method : April 11, 2013
	*/
	public function return_result_as_matrix($mode = MYSQLI_ASSOC){

  		$result_array = array();
  		$row_count = $this->num_rows();
		
   		if($row_count <= 0 ){ 
			return $result_array; 
		}
   		for($i = 0; $i < $row_count; $i++)
		{
      			$pointer = $this->get_last_query_resource_object()->data_seek($i);
      			$result_array[$i] = $this->get_rows($mode);
   		}
  
 		return $result_array;
	}
	
	
	/**
	* @author: Michael Orji
	* @date: Oct 14, 2013
	*/
	public function column_exists_in_table($table, $column, $database = '')
	{
		$database = !empty($database) ? trim($database) : $this->database;
		$sql = "SELECT * FROM information_schema.COLUMNS ".
               "WHERE TABLE_SCHEMA = '". $database. "' ".
               "AND TABLE_NAME = '". $table. "' ". 
               "AND COLUMN_NAME = '". $column. "'";
		$this->execute_query($sql);
		return ( $this->num_rows() > 0 );
	}
	
	/**
	* @author: Michael Orji
	* @date: Nov 5, 2013
	*/
	public function get_table_columns($table, $database = '')
	{
		$database = !empty($database) ? trim($database) : $this->database;
		$sql = "SELECT column_name FROM information_schema.COLUMNS ".
               "WHERE TABLE_SCHEMA = '". $database. "' ".
               "AND TABLE_NAME = '". $table. "'";	   
		$this->execute_query($sql);
		return ArrayManipulator::reduce_redundant_matrix_to_array($this->return_result_as_matrix(), 'column_name');
	}
	
	/**
	* @returns true or false based on whether a table (identified by $table_name) already exists in the current database
	* @author: Michael Orji
	* @date: June 14, 2013
	* @date modified to use execute_query August 25, 2015
	*/
	public function table_exists($table_name)
	{
		$val = $this->execute_query("select 1 from `$table_name`"); //@mysql_query("select 1 from `$table_name`"); //Select 1 from table_name will return false if the table does not exist.
		return ($val !== FALSE);
	}
	
	/**
	* @executes an sql file containing sql queries
	* @author: Michael Orji
	*/
	public function execute_sql_file($file_path)
	{
		$filename = $file_path;
		//@mysql_query("source $file_path");
		//$this->execute_query("source $file_path");
		
		// Temporary variable, used to store current query
		$templine = '';
		
		$lines = file($filename); // Read in entire file
		
		// Loop through each line
		foreach ($lines as $line)
		{
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
			{
				continue;
			}

			$templine .= $line; // Add this line to the current segment
			
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';')
			{
				$this->execute_query($templine);
				mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />'); // Perform the query
				$templine = ''; // Reset temp variable to empty
			}
		}
	}
	
	public function run_sql_file($file_path)
	{ 
		$this->execute_sql_file($file_path); 
	}
}