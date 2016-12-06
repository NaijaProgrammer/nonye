<?php
//utf-8
require_once('../includes/common.php');

$request = $_GET['request'];
$user_id = $_GET['user_id'];

if($request != 'get_buddies'){
 exit;
}

$avail_friends = get_friends($user_id);
$friends_length = count($avail_friends);

$friends = '{';
$friends.= '"friends": [ ';

if($friends_length > 0){   

   for($i = 0; $i < $friends_length; $i++){
    $friend_id = $avail_friends[$i]['id'];
    $friend_name = $avail_friends[$i]['name'];
    $f_status = $avail_friends[$i]['login_status'];

      switch($f_status){

       case 0: 
       case 'offline' : $friend_status = 'offline';
       break;

       case 1:  
       case 'online' : $friend_status = 'online';
       break;

       case 2: 
       case 'idle':
       case 'away': $friend_status = 'idle'; //session expiry: i.e, idle, away
       break;

      }

    $friends.= '{"friend_id": "'. $friend_id. '", "friend_status": "'. $friend_status. '", "friend_name": "'. $friend_name. '"}, '; 
   }
}

$friends.= ']';
$friends.= '}';

header('Content-Type: text/json; charset=utf-8');  
echo($friends); 
exit;

?>