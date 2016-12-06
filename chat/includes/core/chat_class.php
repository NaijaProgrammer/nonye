<?php
class Chat
{

   // constructor opens database connection if that has not already been done
   function __construct()
   {

   }

   function join_room($joiner_id, $joiner_name, $receiver_id, $receiver_name, $curr_room, $room_to_join, $font, $fontsize, $fontcolour, $fontweight, $fontstyle, $textdecoration){

    $joiner_id = safe_escape($joiner_id);
    $room_to_join = safe_escape($room_to_join);

    $sql = "SELECT chat_id FROM chat WHERE user_id = '$joiner_id' AND room = '$room_to_join'";
    $que = mysql_query($sql);
    $num = mysql_num_rows($que);

      if($num > 0){
       $message = "you are already in this room"; 
       $receiver_id = $joiner_id;
       $receiver_name = $joiner_name;
       $joiner_id = 0;
       $joiner_name = "";
      }
      else{
       $message = "$joiner_name has joined the $room_to_join room";

       //leave the room you're currently in, b4 joining a new one
       $left_message = "$joiner_name has left the $curr_room room";
       $this->leave_room($joiner_id, $joiner_name, $receiver_id, $receiver_name, $curr_room, $left_message, $font, $fontsize, $fontcolour, $fontweight, $fontstyle, $textdecoration);       
      }
   
    $query = ("INSERT INTO chat 
          SET
          user_id = '$joiner_id',
          user_name = '$joiner_name',
          receiver_id = '$receiver_id',
          receiver_name = '$receiver_name',
          room = '$room_to_join',
          message = '$message',
          font = '$font',
          fontsize = '$fontsize',
          fontcolour = '$fontcolour',
          fontweight = '$fontweight',
          fontstyle = '$fontstyle',    
          textdecoration = '$textdecoration',
          posted_on = now();
         ");

      if(trim($room_to_join) != 'none'){
       $result = mysql_query($query);
      }
   
   }//close the join_room function

   function leave_room($user_id, $user_name, $receiver_id, $receiver_name, $room, $message, $font, $fontsize, $fontcolour, $fontweight, $fontstyle, $textdecoration){

    $user_id = safe_escape($user_id);
    $room = safe_escape($room);
    $message = safe_escape($message);

    $sql = "SELECT chat_id FROM chat " . "WHERE user_id = '$user_id' " . "AND room = '$room' ";

      if(!empty($user_id) && !empty($room)){
       $que = mysql_query($sql);
       $num = mysql_num_rows($que);
      }

      if($num > 0){  
       $row = mysql_fetch_array($que);
       $chat_id = $row['chat_id'];   
       $del_query = ("DELETE FROM chat WHERE chat_id = '$chat_id'");
       $delete = mysql_query($del_query);

         if($delete){     
          $query = ("INSERT INTO chat 
           SET
           user_id = '',
           user_name = '',
           receiver_id = '',
           receiver_name = '',
           room = '$room',
           room_leaver_id = '$user_id',
           room_leaver_name = '$user_name',
           message = '$message',
           font = '$font',
           fontsize = '$fontsize',
           fontcolour = '$fontcolour',
           fontweight = '$fontweight',
           fontstyle = '$fontstyle',    
           textdecoration = '$textdecoration',
           posted_on = now();
          ");
          $result = mysql_query($query);
         }
      }   
   }//close the leave_room function

   function get_room_members($room){
    $room = safe_escape($room);
    $sql = "SELECT user_id, user_name FROM chat WHERE room = '$room'";
    $query = mysql_query($sql);

    $output = '{';

      if(mysql_num_rows($query)){
       $output .= '"users":[ ';

       // loop through all the fetched messages to build the result message
         while ($row = mysql_fetch_array($query)){
            foreach( $row AS $key => $val ){ 
             $$key = $val; #remember to strip or add slashes to this    
            }
          $output .= '{';
          $output .= '"user_id": "'. $user_id. '", ';
          $output .= '"user_name":  "'. $user_name. '" },';
         }//end while loop
       $output .= ']';
      }//end if    
      else {
       //Send an empty response to avoid a Javascript error when we check for response length in the loop.
       $output .= '"users":[]';
      }
    //Close the response
    $output .= '}';
    return $output;
   }//close the get_room_members function

   public function postMessage($user_id, $user_name, $receiver_id, $receiver_name, $room, $message, $font, $fontsize, $fontcolour, $fontweight, $fontstyle, $textdecoration){
    $user_id = safe_escape($user_id);
    $user_name = safe_escape($user_name);
    $receiver_id = safe_escape($receiver_id);
    $receiver_name = safe_escape($receiver_name);
    $room = safe_escape($room);
    $message = safe_escape($message);
    $font = safe_escape($font);
    $fontsize = safe_escape($fontsize);
    $fontcolour = safe_escape($fontcolour);
    $fontweight = safe_escape($fontweight);
    $fontstyle = safe_escape($fontstyle);
    $textdecoration = safe_escape($textdecoration);
    $recipient_online = ( is_online($receiver_id) ? 'true' : 'false' );

    // build the SQL query that adds a new message to the server
    $query = ("INSERT INTO chat 
                    SET
                    user_id = '$user_id',
                    user_name = '$user_name',
                    receiver_id = '$receiver_id',
                    receiver_name = '$receiver_name',
                    recipient_online = '$recipient_online',
                    room = '$room',
                    message = '$message',
                    font = '$font',
                    fontsize = '$fontsize',
                    fontcolour = '$fontcolour',
                    fontweight = '$fontweight',
                    fontstyle = '$fontstyle',    
                    textdecoration = '$textdecoration',
                    posted_on = now();
                  ");
     $result = mysql_query($query);
   }//close postMessage

   /*
   * Retrieves new messages more recent than/by $chat_id 
   * - the $chat_id parameter (sent by the client)
   * represents the id of the last message received by the client. 
   */
   public function getMessages($user_id, $room, $chat_id=0){
    $chat_id = safe_escape($chat_id);
    $room = safe_escape($room);
 
      // retrieve messages newer than $chat_id
     $query = "SELECT chat_id, user_id, user_name, receiver_id, receiver_name, room, room_leaver_id, room_leaver_name, message, read_status, recipient_online, font, fontsize, fontcolour, fontweight, fontstyle, textdecoration, ". 
                    "DATE_FORMAT(posted_on, \"%r\") ".
                    "AS posted_on ". 
                    "FROM chat ".
                    "WHERE chat_id >'". $chat_id."' ".
                    "AND read_status = 'F' ". 
                    "AND ".
                    "(receiver_id = '". $user_id. "' OR room = '". $room."') ".
                    "ORDER BY chat_id ASC";
      if($chat_id == 0){
       $query .= " LIMIT 50"; // on the first load only retrieve the last 50 messages from the server
      }

    $result = mysql_query($query);

    $response = '{';

      // check to see if we have any results
      if(mysql_num_rows($result))
      {
       $response .= '"messages":[ ';

         // loop through all the fetched messages to build the result message
         while ($row = mysql_fetch_array($result))
         {
            foreach( $row AS $key => $val ){ 
             $$key = $val; #remember to strip or add slashes to this    
            }

          $response .= '{';
          $response .= '"chat_id":  "'. $chat_id. '",
                                "user_id": "'. $user_id. '",
                                "user_name": "'. $user_name. '",
                                "receiver_id": "'. $receiver_id. '",
                                "receiver_name": "'. $receiver_name. '",
                                "room": "'. $room. '",
                                "room_leaver_id": "'. $room_leaver_id. '",
                                "room_leaver_name": "'. $room_leaver_name. '",
	                        "posted_on": "'. $posted_on. '",
	                        "message": "'. $message. '",
                                "read_status": "'. $read_status. '",
                                "recipient_online": "'. $recipient_online. '",
                                "font": "'. $font. '",
                                "fontsize": "'. $fontsize. '",
                                "fontcolour": "'. $fontcolour. '",
                                "fontweight": "'. $fontweight. '",
                                "fontstyle": "'. $fontstyle. '",                
                                "textdecoration": "'. $textdecoration. '"
                               },';
         }//end while loop
       $response .= ']';
      } // end if 
      else {
       //Send an empty message to avoid a Javascript error when we check for message length in the client-side loop.
       $response .= '"messages":[]';
      }

    //Close the response
    $response .= '}';
    return $response;
   }//end getMessage

   function updateReadStatus($chat_id){
    $sql = "UPDATE chat SET read_status = 'T' WHERE chat_id = '". safe_escape($chat_id). "'";
    $query = mysql_query($sql);
   }
}//close the class
?>