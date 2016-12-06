<?php
//utf-8
require_once('../includes/common.php');

$request = $_GET['request'];

if($request != 'get_rooms'){
 exit;
}

$avail_rooms = get_rooms();
$rooms_length = count($avail_rooms);

for($i = 0; $i < $rooms_length; $i++){
 $room_id = $avail_rooms[$i]['id'];
 $rooms_array[$room_id] = $avail_rooms[$i]['room_name'];
 $rooms_id_array[] = $avail_rooms[$i]['id'];
}

$rooms = '{';
$rooms.= '"rooms": [ ';
   
if($rooms_length > 0){   
 asort($rooms_array);

   while(list($room_key, $room_val) = each($rooms_array)){
    $rooms.= '{"room_id": "'. $room_key. '", "room_name": "'. $room_val. '"}, '; 
   }
}

$rooms.= ']';
$rooms.= '}';

header('Content-Type: text/json; charset=utf-8');  
echo($rooms); 
exit;

?>