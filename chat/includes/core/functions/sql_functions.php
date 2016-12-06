<?php
function insert_chatroom($room_name){
 $sql = "INSERT INTO chatrooms SET ".
            "room_name = '" . safe_escape($room_name). "', ".
            "date_added = now()";
 $query = mysql_query($sql);
   if(!$query){
    return false;
   }
 return true;
}

function delete_chatroom($room_id = 0){
 $sql = "DELETE FROM chatrooms";
 $sql .= ( ($room_id > 0) ? " WHERE id = $room_id" : "");
 $query = mysql_query($sql);
   if(mysql_affected_rows()){
    return true;
   }
 return false;
}

/*
*retrieves rooms from database
* 
* @return value: an array in the form
* arr_name[0]['id'], arr_name[0]['room_name'] ... arr_name[n]['id'], arr_name[n]['room_name']
* function must return values in the format specified above
* values may be different but keys must not change
*/
function get_rooms(){

 $sql = "SELECT * FROM chatrooms";
 $query = mysql_query($sql);
 $rooms = return_query_results_as_matrix($query);
 $num_rows = count($rooms);
 $chatrooms = array();
   if($num_rows > 0){
      for($i = 0; $i < $num_rows; $i++){
       $chatrooms[$i]['id'] = $rooms[$i]['id'];
       $chatrooms[$i]['room_name'] = $rooms[$i]['room_name'];
      }
   }
 return $chatrooms;
}

function insert_chat_config_options($arr){

 $site_name = $arr['site_name'];
 $chat_theme = $arr['chat_theme'];
 $use_stack_view = $arr['use_stack_view'];
 $chat_container_id = $arr['chat_container_id'];

 $sql = "INSERT INTO chat_config_options SET ".
        "site_name = '" . safe_escape($site_name).  "', ". 
        "chat_theme = '". safe_escape($chat_theme). "', ".
        "use_stack_view = '". safe_escape($use_stack_view). "', ".
        "chat_container_id = '". safe_escape($chat_container_id). "'";           

 $query = mysql_query($sql);
   if(!$query){
    return false;
   }
 return true;
}

function update_chat_config_option($option_column_name, $option_value){
 return update_table_column("chat_config_options", $option_column_name, $option_value);
}

function get_chat_config_option($option_column_name){
 $sql = "SELECT $option_column_name FROM chat_config_options";
 $query = mysql_query($sql);
 $row = mysql_fetch_array($query);
 return $row[$option_column_name];
}

?>