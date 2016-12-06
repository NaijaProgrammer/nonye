<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');
 require_once('includes/admin_authenticator.php');

 if( ($_SERVER['REQUEST_METHOD'] == 'GEsT') && empty($_GET['opt'])){
  header("location: chat_edit.php");
  exit;
 }

 require_once('includes/admin_links.php');

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $opt = trim($_POST['optn']);
    if($opt == 'cct'){
     $new_chat_theme = safe_escape($_POST['new_chat_theme']);
       if(update_chat_config_option("chat_theme", $new_chat_theme)){
        $message = 'Your Chat theme has been updated successfully';
       }
       else{
        $message = 'There was an error updating the chat theme. Try again later';
       }
     require_once('includes/forms/chat_theme_changer_form.inc');
    }
    else if($opt == 'cccid'){
     $new_chat_link_container_id = safe_escape($_POST['new_chat_link_container_id']);
       if(update_chat_config_option("chat_container_id", $new_chat_link_container_id)){
        $message = 'Your Chat Link Container has been updated successfully';
       }
       else{
        $message = 'There was an error updating the chat link container. Try again later';
       }
     require_once('includes/forms/chat_container_id_changer_form.inc');
    }

    else if($opt == 'csn'){
     $new_site_name = safe_escape($_POST['new_site_name']);
       if(update_chat_config_option("site_name", $new_site_name)){
        $message = 'Your Chat title has been updated successfully';
       }
       else{
        $message = 'There was an error updating the chat title. Try again later';
       }
     require_once('includes/forms/site_name_changer_form.inc');
    }
  exit;
 }

 $opt = trim($_GET['opt']);
 if($opt == 'cct'){
  require_once('includes/forms/chat_theme_changer_form.inc');
 }
 else if($opt == 'cccid'){
  require_once('includes/forms/chat_container_id_changer_form.inc');
 }
 else if($opt == 'csn'){
  require_once('includes/forms/site_name_changer_form.inc');
 }
  
 ?>