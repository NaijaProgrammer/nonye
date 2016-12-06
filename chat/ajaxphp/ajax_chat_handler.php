<?php
require_once('../includes/common.php'); 

// retrieve the operation to be performed
$request = $_POST['request'];
$room = '';
  
// default the last chat id to 0
$chat_id = 0;

// create a new Chat instance
$chat = new Chat();

if($request == 'get_rooms'){
 header("location: ajax_get_rooms.php?request=".$request); 
 exit;
}

if($request == 'get_buddies'){
 $user_id = $_POST['user_id'];  
 $status = $_POST['status'];
 header("location: ajax_get_buddies.php?request=".$request. "&user_id=". $user_id. "&status=". $status); 
 exit;
}

if($request == 'get_user_data'){
 $user_id = $_POST['user_id'];
 $get_full_data = $_POST['get_full_data'];
 header("location: ajax_get_user_data.php?request=".$request. "&user_id=". $user_id. "&get_full_data=". $get_full_data); 
 exit;
}

if($request == 'join')
{
$chat_id = $_POST['chat_id'];
$user_id = $_POST['joiner_id'];
$user_data = get_user_data($user_id);
$user_name = $user_data['name'];
$curr_room = $_POST['curr_room'];
$room_to_join = $_POST['room_to_join'];
$receiver_id = "";
$receiver_name = "";
$font = "Tahoma";
$fontsize = 12;
$fontcolour = "red";
$fontweight = "bold";
$fontstyle = "normal";
$textdecoration = "none";

   $chat->join_room($user_id, $user_name, $receiver_id, $receiver_name, $curr_room, $room_to_join, $font, $fontsize, $fontcolour, 
                    $fontweight, $fontstyle, $textdecoration);

}

/*
* only called by leaveRoom() in js/core/client/init/chatRoomWinInitialiser.js
* when the user closes the chat room window
* 
*@date: July 15, 2011
*/
if($request == 'leave_room')
{
 $chat_id = $_POST['chat_id'];
 $room_leaver_id = $_POST['room_leaver_id'];
 $room_leaver_data = get_user_data($room_leaver_id);
 $room_leaver_name = $room_leaver_data['name'];
 $room_to_leave = $_POST['room_to_leave'];
 $message = "$room_leaver_name has left the $room_to_leave room";
 $receiver_id = "";
 $receiver_name = "";
 $font = "Tahoma";
 $fontsize = 12;
 $fontcolour = "red";
 $fontweight = "bold";
 $fontstyle = "normal";
 $textdecoration = "none";

 $chat->leave_room($room_leaver_id, $room_leaver_name, $receiver_id, $receiver_name, $room_to_leave, $message, $font, $fontsize, $fontcolour, $fontweight, $fontstyle, $textdecoration);
 
}

if($request == 'get_room_members'){
 $chat_id = $_POST['chat_id'];
 $room = $_POST['room'];
}

if($request == 'send')
{
  $chat_id = $_POST['chat_id'];
  $user_id = $_POST['user_id'];  
  $user_data = get_user_data($user_id);
  $user_name = $user_data['name'];
  $receiver_id = $_POST['receiver_id']; 
  $receiver_data = get_user_data($receiver_id);
  $receiver_name = $receiver_data['name']; 
  $room = $_POST['room'];
  $message = $_POST['message'];
  $font = $_POST['font'];
  $fontsize = $_POST['fontsize'];
  $fontcolour = $_POST['fontcolour'];
  $fontweight = $_POST['fontweight'];
  $fontstyle = $_POST['fontstyle'];
  $textdecoration = $_POST['textdecoration'];

   // check if we have valid values
   if ($message != '')
   {
    // post the message to the database
    $chat->postMessage($user_id, $user_name, $receiver_id, $receiver_name, $room, $message, $font, $fontsize, $fontcolour, 
                      $fontweight, $fontstyle, $textdecoration);
   }
}

else if($request == 'retrieve')
{
 // get the id of the last message retrieved by the client
 $chat_id = $_POST['chat_id'];
 $user_id = $_POST['user_id'];
 $room = $_POST['room'];
}

if($request == 'update_read_status'){
  $chat_id = $_POST['chat_id'];
  $chat->updateReadStatus($chat_id);
}

// Clear the output
if(ob_get_length()){
 ob_clean();
}

// Set the MIME-Type of the header to text/json since we're 
//sending the response in JSON format
header('Content-Type: text/json; charset=utf-8');

//get and send the messages
if($request == 'get_room_members'){
 echo( $chat->getMessages($user_id, $room, $chat_id). $chat->get_room_members($room) );
}

else{
 echo $chat->getMessages($user_id, $room, $chat_id);
}

?>