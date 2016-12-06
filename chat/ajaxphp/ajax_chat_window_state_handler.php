<?php
//utf-8
require_once('../includes/common.php');

$request = $_POST['request'];
$server_reply = '';
$curr_userid = $_POST['curr_userid'];

if($request == 'register_new_chat_window_state'){

 $window['window_id'] = $_POST['window_id'];
 $window['window_title'] = $_POST['window_title'];
 $window['window_type'] = $_POST['window_type'];
 $window['last_message_id'] = $_POST['last_message_id']; 
 $window['starter_id'] = $_POST['starter_id'];
 $window['starter_name'] = $_POST['starter_name'];
 $window['receiver_id'] = $_POST['receiver_id'];
 $window['receiver_name'] = $_POST['receiver_name'];
 $window['focused'] = ( ($_POST['focused'] == 'true') ? 'T' : 'F');
 $window['open_time'] = $_POST['open_time'];
 $window['is_visible'] = ( ($_POST['is_visible'] == 'true') ? 'T' : 'F');
 $window['width'] = $_POST['width'];
 $window['height'] = $_POST['height'];
 $window['position_left'] = $_POST['position_left'];
 $window['position_top'] = $_POST['position_top'];
 $window['position_right'] = $_POST['position_right'];
 $window['position_bottom'] = $_POST['position_bottom'];
 $window['position_style'] = $_POST['position_style'];

 record_chat_window_state($window);

}

else if($request == 'update_chat_window_state'){

 $arr['window_id'] = $_POST['window_id'];
 $arr['window_title'] = $_POST['window_title'];
 $arr['starter_id'] = $curr_userid;
 $arr['last_message_id'] = $_POST['last_message_id'];
 $arr['is_visible'] = ( ($_POST['is_visible'] == 'true') ? 'T' : 'F');
 $arr['focused'] = ( ($_POST['focused'] == 'true') ? 'T' : 'F');
 $arr['closed'] = ( ($_POST['closed'] == 'true') ? 'T' : 'F');
 $arr['close_time'] = $_POST['close_time'];
 $arr['closed_by'] = $_POST['closed_by'];
 $arr['minimized'] = ( ($_POST['minimized'] == 'true') ? 'T' : 'F');
 $arr['maximized'] = ( ($_POST['maximized'] == 'true') ? 'T' : 'F');
 $arr['width'] = $_POST['width'];
 $arr['height'] = $_POST['height'];
 $arr['position_left'] = $_POST['position_left'];
 $arr['position_top'] = $_POST['position_top'];
 $arr['position_right'] = $_POST['position_right'];
 $arr['position_bottom'] = $_POST['position_bottom'];
 $arr['position_style'] = $_POST['position_style'];
 
 update_chat_window_state($arr);

}

else if($request == 'update_current_chat_room')
{
  $arr['window_id'] = $_POST['window_id'];
  $arr['starter_id'] = $curr_userid;
  $arr['last_message_id'] = $_POST['last_message_id'];
  $arr['room'] = $_POST['room'];
  update_current_chat_room($arr);
}

else if($request == 'delete_closed_chat_window'){
 $window_id = $_POST['window_id'];
 delete_closed_chat_window($window_id, $curr_userid);
}

else if($request == 'get_chat_windows'){

 $win_ids = get_chat_windows($curr_userid, false, false);
 $win_ids_len = count($win_ids);

   $windows = ' [ ';

   if($win_ids_len > 0){
      for($i = 0; $i < $win_ids_len; $i++){
       $win_id = $win_ids[$i];
       $windows.= '\''. $win_id. '\', ';      
      }   
   }

 $windows.= ']';

 $server_reply = $windows;

}

else if($request == 'get_chat_window_closed_state'){
 $win_closed_state = get_chat_window_closed_state($_POST['window_id'], $curr_userid);

  //$reply= '{"closed" : "'.  $win_closed_state. '"}';
  $reply = $win_closed_state;

 header('Content-Type: text/json; charset=utf-8');  
 echo($reply); 
 exit;

}

else if($request == 'get_chat_window_state'){

 $window_state = get_chat_window_state($_POST['window_id'], $curr_userid);

 $id = $window_state['id'];
 $window_id = $window_state['window_id'];
 $window_title = $window_state['window_title'];
 $window_type = $window_state['window_type'];
 $last_message_id = $window_state['last_message_id'];
 $room = $window_state['room']; 
 $is_visible = $window_state['is_visible']; 
 $focused = $window_state['focused'];
 $starter_id = $window_state['starter_id'];
 $starter_name = $window_state['starter_name'];
 $receiver_id = $window_state['receiver_id'];
 $receiver_name = $window_state['receiver_name'];
 $open_time = $window_state['open_time'];
 $close_time = $window_state['close_time'];
 $closed = $window_state['closed'];
 $closed_by = $window_state['closed_by'];
 $minimized = $window_state['minimized'];
 $maximized = $window_state['maximized'];
 $width = $window_state['width'];
 $height = $window_state['height'];
 $position_left = $window_state['position_left'];
 $position_top = $window_state['position_top'];
 $position_right = $window_state['position_right'];
 $position_bottom = $window_state['position_bottom'];
 $position_style = $window_state['position_style'];

$window = '{';

$window.= '"entryId" : "'.  $id. '", ';
$window.= '"windowId" : "'.  $window_id. '", ';
$window.= '"windowTitle" : "'.  $window_title. '", ';
$window.= '"windowType" : "'.  $window_type. '", ';
$window.= '"lastMessageId" : "'.  $last_message_id. '", ';
$window.= '"room" : "'.  $room. '", ';
$window.= '"isVisible" : "'.  $is_visible. '", ';
$window.= '"focused" : "'.  $focused. '", ';
$window.= '"starterId" : "'.  $starter_id. '", ';
$window.= '"starterName" : "'.  $starter_name. '", ';
$window.= '"receiverId" : "'.  $receiver_id. '", ';
$window.= '"receiverName" : "'.  $receiver_name. '", ';
$window.= '"openTime" : "'.  $open_time. '", ';
$window.= '"closeTime" : "'.  $close_time. '", ';
$window.= '"closed" : "'.  $closed. '", ';
$window.= '"closedBy" : "'.  $closed_by. '", ';
$window.= '"minimized" : "'.  $minimized. '", ';
$window.= '"maximized" : "'.  $maximized. '", ';
$window.= '"width" : "'.  $width. '", ';
$window.= '"height" : "'.  $height. '", ';
$window.= '"positionLeft" : "'.  $position_left. '", ';
$window.= '"positionTop" : "'.  $position_top. '", ';
$window.= '"positionRight" : "'.  $position_right. '", ';
$window.= '"positionBottom" : "'.  $position_bottom. '", ';
$window.= '"positionStyle" : "'.  $position_style. '"';

 $window.= '}';

 $server_reply = $window;

}

 header('Content-Type: text/json; charset=utf-8');  
 echo($server_reply); 
 exit;
?>