<?php
/* This file contains general-purpose (my)sql utility functions */


/******************************************* 
 a database connection abstraction function
 @author: Michael Orji
 @date: July 26, 2010
 @return: the connection id
********************************************/
function db_connect($config_array){

$server = (isset($config_array['host_server'])) ? $config_array['host_server'] : "localhost";
$username = (isset($config_array['db_user_name'])) ? $config_array['db_user_name'] : "root";
$password = (isset($config_array['db_password'])) ? $config_array['db_password'] : "";
$database = (isset($config_array['database'])) ? $config_array['database'] : "site database";

$connexn_id = @mysql_connect($server, $username, $password);

   if (!$connexn_id) {
    echo("<p> Unable to connect to host server. </p>");
    exit();
   }

   if (( $database != "") && (!@mysql_select_db($database, $connexn_id)) ){
    echo ("<p> The site database appears to be temporarily down. </p>");
    exit();
   }

 return $connexn_id;

}

/*
* general-purpose function for updating any column in any table
* @author: michael orji
* @date: April 13, 2012
* @return: a boolean value indicating whether the value already exists(true) or not(false)
*/
function update_table_column($table_name, $option_column_name, $option_value){
  $sql = "UPDATE $table_name SET ".
         "$option_column_name = '" . safe_escape($option_value). "'";
  $query = mysql_query($sql);
   if(!$query){
    return false;
   }
 return true;
}


/*
* determines whether a (unique) value already exists in a table
* @author: michael orji
*/
function in_table($table, $unique_field_name, $unique_field_value){

 $sql = "SELECT * FROM $table ".
           "WHERE $unique_field_name = '". $unique_field_value. "' ";

$query = mysql_query($sql);
$num = mysql_num_rows($query);
return ($num > 0);
    
}

/* 
* A function that returns the result rows of an sql query as a multi-dimensional array
*
* @author: Michael Orji
* @date: June 4, 2010
* @API syntax: matrix return_query_results_as_matrix(resource_id query);
* @query: the sql result resource from a mysql_query() call
*/
function return_query_results_as_matrix($query){
  $result_array = array();
  $row_count = mysql_num_rows($query);
   if($row_count <= 0 ){return;}
   for($i = 0; $i < $row_count; $i++){
      $pointer = mysql_data_seek($query, $i);
      $result_array[$i] = mysql_fetch_assoc($query);
   }
 return $result_array;
}

?>