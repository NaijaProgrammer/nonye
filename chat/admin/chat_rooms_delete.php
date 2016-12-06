<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');
 require_once('includes/admin_authenticator.php');

 if(!empty($_GET['rmid'])){
   $room_id = trim($_GET['rmid']);
   $room_name = trim($_GET['rm']);
    if($room_id == 'all'){
       if(delete_chatroom()){
        $message = "You have successfully deleted every chatroom";
       }
    }
    else{
       if(delete_chatroom($room_id)){
        $message = "$room_name successfully deleted";
       }
    }
 }

 $avail_rooms = get_rooms();
 $rooms_length = count($avail_rooms);
 $rooms_array = array();

 for($i = 0; $i < $rooms_length; $i++){
  $room_id = $avail_rooms[$i]['id'];
  $rooms_array[$room_id] = $avail_rooms[$i]['room_name'];
  //$rooms_id_array[] = $avail_rooms[$i]['id'];
 }
?>
<html>
<head>
<title></title>
</head>
<body>

 <?php require_once('includes/admin_links.php'); ?>

 <h2>Available Rooms</h2>

 <?php
  $rooms_to_del = '';

  foreach($rooms_array as $key => $value){
   $rooms_to_del .= '<div><label for="'. $value. '" style="float: left; width:200px;">'. $value.  '</label><a href="'. $_SERVER['PHP_SELF']. '?rmid=' . $key. '&rm='. $value. '">Delete</a></div>'. NL;
  }
  
  $rooms_to_del .= '';
  echo($rooms_to_del);

 ?>

 <a href="<?php echo($_SERVER['PHP_SELF']);?>?rmid=all&rm=">Delete All</a>

 <p style="color:red;"><?php echo (isset($message) ? $message : ''); ?></p>
</body>
</html>
 