<?php
//session_start();
require dirname(__DIR__). '/config.php';
require_once('includes/common.php');

$im_win_title = get_chat_config_option("site_name") . ' chat';
$chat_theme = get_chat_config_option("chat_theme");
$chat_container_id = get_chat_config_option("chat_container_id");
$use_stack_view = ( (get_chat_config_option("use_stack_view") == 'T') ? 'true' : 'false');

/*
* since the get_current_user_id() in its trial, unimplemented state, generates a random id each time it is called
* we ensure here that, within the same browser session, different user ids are not generated
* this helps ensure consistency when loading different tabs within the current browser session
* or when reloading the current browser session
* it also helps the user of the app maintain consistency so that even if their session management system 
* is problematic, this app always ensures that only one user -- as identified by their id -- is currently
* logged in to the app in the current browser session
*/
if(empty($_SESSION['current_user_id'])){
 $current_user_id = get_current_user_id();
 $_SESSION['current_user_id'] = $current_user_id;
}
else{
 $current_user_id = $_SESSION['current_user_id'];
}

$current_user_data = get_user_data($current_user_id);
$current_user_name = $current_user_data['username'];
//$current_user_name = $current_user_data['name'];

$j = '(function(){
 var incLink = document.createElement("script");
 incLink.setAttribute("type", "text/javascript");
 incLink.setAttribute("src", "' . APP_HTTP_PATH . 'js/libraries/include.js");
      if(typeof incLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(incLink);
      }
 var loaderLink = document.createElement("script");
 loaderLink.setAttribute("type", "text/javascript");
 loaderLink.setAttribute("src", "' . APP_HTTP_PATH. 'js/core/loader.js");
      if(typeof loaderLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(loaderLink);
      }
})()';


Header("content-type: application/x-javascript");
echo "\r\nvar appCreator = ".  APP_CREATOR_LINK.  ";\r\nvar webRootPath = '".  APP_HTTP_PATH.  "';\r\nvar uid = ". $current_user_id. ";\r\nvar userName='". $current_user_name. "'\r\nvar IMWinTitle = '". $im_win_title. "';\r\nvar chatTheme = '" . $chat_theme . "';\r\nvar chatContainerId = '". $chat_container_id. "'; \r\nvar useStackView = ". $use_stack_view. ";\r\n\r\n" . $j;
