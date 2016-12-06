<?php
require_once('../includes/common.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
 
 $site_name = safe_escape($_POST['site_name']);
 $chat_theme = safe_escape($_POST['chat_theme']);
 $use_stack_view = safe_escape($_POST['use_stack_view']);
 $chat_container_id = safe_escape($_POST['chat_container_id']);
 $chatrooms = safe_escape($_POST['chatrooms']);

   if(empty($site_name) || empty($chat_theme)){
    $error_message = "Please Fill in every required field";
    require_once('forms/configuration_form.inc');
    exit;
   }

   if(!insert_chat_config_options($_POST)){
     $error_message = "Error : Unable to create the chat options";
     require_once('forms/configuration_form.inc');
     exit;
   }

 $chatrooms = explode(",", $chatrooms);
 $rooms_len = count($chatrooms);
   for($i = 0; $i < $rooms_len; $i++){
      if(!in_table("chatrooms", "room_name", $chatrooms[$i])){
       insert_chatroom(safe_escape($chatrooms[$i]));
      }
   }

 header("location: admin_config.php");

}// close if ... $_POST

else{
 require_once('forms/configuration_form.inc');
}
?>