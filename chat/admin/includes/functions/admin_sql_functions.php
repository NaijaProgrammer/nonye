<?php
function insert_admin_details($arr){
 $admin_email = $arr['admin_email'];
 $admin_pass = $arr['admin_pass'];
 $admin_level = $arr['admin_level'];
 $sql = "INSERT INTO admin SET ".
        "admin_email = '". safe_escape($admin_email). "', ".
        "admin_pass = '". hash_string(safe_escape($admin_pass)). "', ".
        "admin_level = '". safe_escape($admin_level). "', ".
        "date_added = now()";
 $query = mysql_query($sql);
   if(!$query){
    return false;
   }
return true;

}

function update_admin_detail($column_name, $column_value){
 return update_table_column("admin", $column_name, $column_value);
}

function is_admin($admin_email, $admin_pass){
 $sql = "SELECT COUNT(*) FROM admin ".
        "WHERE admin_email = '". safe_escape($admin_email). "' ".
        "AND admin_pass = '". hash_string(safe_escape($admin_pass)). "'";
 $query = mysql_query($sql);
 $num_rows = mysql_num_rows($query);
   if($num_rows == 1){
    return true;
   }
 return false; 
}

function get_admin_details($admin_email, $admin_pass){
 $sql = "SELECT * FROM admin ".
        "WHERE admin_email = '". safe_escape($admin_email). "' ".
        "AND admin_pass = '". hash_string(safe_escape($admin_pass)). "'";
 $query = mysql_query($sql);
 $row = mysql_fetch_array($query);
 return $row;
}
?>