<?php
//utf-8
require_once('../includes/common.php');
$theme = get_chat_config_option("chat_theme");

$request = $_GET['request'];
$user_id = $_GET['user_id'];
$full_data = $_GET['get_full_data'];

if($request != 'get_user_data'){
 exit;
}

$user_data = get_user_data($user_id);

$user_id = $user_data['id'];
$name = $user_data['name'];
$email = $user_data['email'];
$sex = $user_data['sex'];
$age = $user_data['age'];
$country = $user_data['country'];
$state = $user_data['state'];
$state = ($state) ? $state. ', ' : '';
$photo = $user_data['photo'];
$login_status = $user_data['login_status'];
$user_profile_path = $user_data['profile_path'];

$reply = '';

   if($full_data == 'true'){
    $reply.= '{';
    $reply.= '"userId" : "'. $user_id. '", ';
    $reply.= '"name" : "'. $name. '", ';
    $reply.= '"email" : "'. $email. '", ';
    $reply.= '"sex" : "'. $sex. '", ';
    $reply.= '"age" : "'. $age. '", ';
    $reply.= '"country" : "'. $country. '", ';
    $reply.= '"state" : "'. $state. '", ';
    $reply.= '"loginStatus" : "'. $login_status. '", ';
    $reply.= '"photo" : "'. $photo. '", ';
    $reply.= '}';

    header('Content-Type: text/JSON; charset=utf-8');
     
   }

   else{
      if($state || $country){
        $location = $state. $country;
      }
      else{
       $location = 'none specified';
      }
      if($theme == 'dark'){
       $bg_img = 'bg.gif';
       $bg_color = 'black';
       $txt_color = 'white';
      }
      else if($theme == 'light'){
       $bg_img = 'grad_texture.jpg';
       $bg_color = 'silver';
       $txt_color = 'black';
      }
      else{
       $bg_img = 'bg.gif';
       $bg_color = 'black';
       $txt_color = 'white';
      }
      
    $reply.= '<div id="userProfileWin_' . $user_id. '" class="dynamic_user_profile_div" style="position:relative; font-family:serif; background-color: '. $bg_color. '; background-image: url('. APP_HTTP_PATH. 'resources/images/'. $bg_img. '); padding:5px; padding-top: 10px;">';
    $reply.= '<a style="position:absolute; top:2px; right:2px; border:none;" href="" onclick="unloadUserProfile('. $user_id. ', event); return false">';
    $reply.= '<img src="'. APP_HTTP_PATH. 'resources/images/close.png" width="16px" height="16px" style="border:none;" alt="close" title="close"></a>';
    $reply.= '<img id="dupimg_'. $user_id. '" class="dupimg" src="'. $photo. '" width="100px" height="100px" style="position:relative;" \>'. NL;
    $reply.= '<span style="color: '. $txt_color. ';" title="'. $name. '">Name: '. set_displayed_text_length($name, 10). '</span>'. NL;
    $reply.= '<span style="color: '. $txt_color. ';" title="'. $sex. '">Sex: '. $sex. '</span>'. NL;
    $reply.= '<span style="color: '. $txt_color. ';" title="'. $age. '">Age: '. $age. '</span>'.NL;
    $reply.= '<span style="color: '. $txt_color. ';" title="'. $location. '">Location: '. ( (strtolower($location) == 'none specified') ? $location : set_displayed_text_length($location, 10) ). '</span>'.NL;
    $reply.= '<hr align="center" width="100px" size="1px" color="silver" noshade="noshade">';
    $reply.= '<span style="position: relative; left: 20px;">';
    $reply.= '<a style="position:relative; font-weight: bold; color: silver; text-decoration:none;" id="dupa_'. $user_id. '" class="dupa" href="'. $user_profile_path. '" target="_blank" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'silver\'">View Full Profile</a>'. NL;
    $reply.= '<a style="font-weight: bold; color: silver; text-decoration:none;" href="" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'silver\'" onclick="verifyAndLoadPMWin('. $user_id. '); return false">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ping</a>'.NL;
    $reply.= '</span>';
    $reply.= '</div>';
    header('Content-Type: text/html; charset=utf-8');
     
   }

echo($reply); exit;

?>