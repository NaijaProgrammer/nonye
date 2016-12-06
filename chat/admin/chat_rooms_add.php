<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');
 require_once('includes/admin_authenticator.php');

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $chatrooms = $_POST['chatrooms'];
  $chatrooms = explode(",", $chatrooms);
  $rooms_len = count($chatrooms);
    for($i = 0; $i < $rooms_len; $i++){
       if(!in_table("chatrooms", "room_name", $chatrooms[$i])){
        insert_chatroom(safe_escape($chatrooms[$i]));
       }
    } 
   $message = "The new chatrooms have been added";
 }
 require_once('includes/admin_links.php');
 require_once('includes/forms/add_chat_rooms_form.inc');
 
 ?>
