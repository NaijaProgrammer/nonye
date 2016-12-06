<?php
//utf-8

function get_chat_window_closed_state($window_id, $curr_userid)
{
 $sql = "SELECT closed FROM chat_window_state_tracker ".
        "WHERE window_id = '". safe_escape($window_id). "'  AND id ='". safe_escape($window_id. "_". $curr_userid). "' ";

 $query = mysql_query($sql);
 $row = mysql_fetch_array($query);
 return $row['closed']; 
}

function delete_closed_chat_window($win_id, $curr_userid){
 $sql = "DELETE FROM chat_window_state_tracker ".
            "WHERE id = '" . $win_id. "_". $curr_userid. "' ";
 $query = mysql_query($sql);
}

function record_chat_window_state($arr)
{

 $win_state = get_chat_window_state($arr['window_id'], $arr['starter_id']);
   if($win_state['closed'] == 'T'){
    delete_closed_chat_window($arr['window_id'], $arr['starter_id']);
   }

 $sql = "INSERT INTO chat_window_state_tracker ".
        "SET ".
        "id = '". $arr['window_id']. "_". $arr['starter_id']. "', ".
        "window_id = '" . $arr['window_id']. "', ".
        "window_title = '" . $arr['window_title']. "', ".
        "window_type = '" . $arr['window_type']. "', ".
        "last_message_id = '". $arr['last_message_id']. "', ".
        "starter_id = '". $arr['starter_id']. "', ".
        "starter_name = '". $arr['starter_name']. "', ".
        "receiver_id = '". $arr['receiver_id']. "', ".
        "receiver_name = '". $arr['receiver_name']. "', ".
        "focused = '". $arr['focused']. "', ".
        "open_time = now(), ".
        //"open_time = '". $arr['open_time']. "', ".
        "is_visible = '". $arr['is_visible']. "', ".
        "width = '". $arr['width']. "', ".
        "height = '". $arr['height']. "', ".
        "position_left = '". $arr['position_left']. "', ".
        "position_top = '". $arr['position_top']. "', ".
        "position_right = '". $arr['position_right']. "', ".
        "position_bottom = '". $arr['position_bottom']. "', ".
        "position_style = '". $arr['position_style']. "' ";

$query = mysql_query($sql);

   //since only one window can have focus at a time, 
   if($arr['focused'] == 'T'){
    update_chat_window_focused_state_to_false($arr['window_id']. "_". $arr['starter_id']);
   }
}

function update_chat_window_state($arr)
{
 $sql = "UPDATE chat_window_state_tracker ".
        "SET ".
        "last_message_id = '". $arr['last_message_id']. "', ".
        "is_visible = '". $arr['is_visible']. "', ".
        "focused ='". $arr['focused']. "', ".
        "closed = '". $arr['closed']. "', ".
        "close_time = now(), ".
        //"close_time = '". $arr['close_time']. "', ".
        "closed_by = '". $arr['closed_by']. "', ".
        "minimized = '". $arr['minimized']. "', ".
        "maximized = '". $arr['maximized']."', ".
        "width = '". $arr['width']. "', ".
        "height = '". $arr['height']. "', ".
        "position_left = '". $arr['position_left']. "', ".
        "position_top = '". $arr['position_top']. "', ".
        "position_right = '". $arr['position_right']. "', ".
        "position_bottom = '". $arr['position_bottom']. "', ".
        "position_style = '". $arr['position_style']. "' ";

 //we use the condition 'closed = "F" to make sure we don't update any closed windows if they're still in the database
 $sql.= "WHERE id = '" . $arr['window_id']. "_".  $arr['starter_id']. "' AND closed = 'F'";

 $query = mysql_query($sql);

   //since only one window can have focus at a time, 
   if($arr['focused'] == 'T'){
    update_chat_window_focused_state_to_false($arr['window_id']. "_". $arr['starter_id'], true);
   }
}

function update_current_chat_room($arr)
{
 $sql = "UPDATE chat_window_state_tracker ".
            "SET ".
            "last_message_id = '". $arr['last_message_id']. "', ".
            "room = '". $arr['room']. "' ";

 //we use the condition 'closed = "F" to make sure we don't update any closed windows if they're still in the database
 $sql.= "WHERE id = '" . $arr['window_id']. "_".  $arr['starter_id']. "' AND closed = 'F'";
 $query = mysql_query($sql);
 
}

function get_chat_window_state($window_id, $curr_userid)
{
 $sql = "SELECT * FROM chat_window_state_tracker ".
        "WHERE window_id = '". safe_escape($window_id). "'  AND id ='". safe_escape($window_id. "_". $curr_userid). "' ";

$query = mysql_query($sql);
$window = array();

   while($row = mysql_fetch_array($query)){
    $window['id'] = $row['id'];
    $window['window_id'] = $row['window_id'];
    $window['window_title'] = $row['window_title'];
    $window['window_type'] = $row['window_type'];
    $window['last_message_id'] = $row['last_message_id']; 
    $window['room'] = $row['room'];
    $window['starter_id'] = $row['starter_id'];
    $window['starter_name'] = $row['starter_name'];
    $window['receiver_id'] = $row['receiver_id'];
    $window['receiver_name'] = $row['receiver_name'];
    $window['is_visible'] = $row['is_visible'];
    $window['open_time'] = $row['open_time'];
    $window['focused'] = $row['focused'];
    $window['closed'] = $row['closed'];
    $window['close_time'] = $row['close_time'];
    $window['closed_by'] = $row['closed_by'];
    $window['minimized'] = $row['minimized'];
    $window['maximized'] = $row['maximized'];
    $window['width'] = $row['width'];
    $window['height'] = $row['height'];
    $window['position_left'] = $row['position_left'];
    $window['position_top'] = $row['position_top'];
    $window['position_right'] = $row['position_right'];
    $window['position_bottom'] = $row['position_bottom'];
    $window['position_style'] = $row['position_style'];

   }
 return $window;
}

function get_chat_windows($curr_userid = 0, $open=false, $closed=false)
{

 $sql = "SELECT window_id FROM chat_window_state_tracker WHERE window_id != 'undefined'";
 $sql .= ( ($curr_userid > 0) ? " AND starter_id = '" . safe_escape($curr_userid) . "' OR receiver_id = '" . safe_escape($curr_userid). "' " : "");
 $sql.= ( ($open) ? " AND closed = 'F'" : ( ($closed) ? " AND closed = 'T'" : "") ) ;

 $query = mysql_query($sql);
 $i = 0;
 $win = array();

// return return_query_results_as_matrix($query, "chat_functions.php: get_chat_windows");

   while($row = mysql_fetch_array($query)){
    $win[$i] = $row['window_id'];
    $i++;
   }

 return $win;
}

function update_chat_window_focused_state_to_false($exempt_window_id, $is_update){

 $sql = "UPDATE chat_window_state_tracker ".
        "SET focused = 'F' ".
        "WHERE id != '". $exempt_window_id. "'";

   $sql .= ( ($is_update) ? " AND closed = 'F'" : ""); //make sure we don't update any closed windows if they're in the database
 
 $query = mysql_query($sql);
}

?>