<?php

function create_db($server, $user, $pass, $db_name){

 $conn_id = @mysql_connect($server, $user, $pass);

 $DB = array();
 $DB['error'] = true;
 $DB['msg'] = '';
 $DB['conn_id'] = $conn_id;

   if (!$connect) {
    $DB['msg'] = 'unable to connect to host server';
   }

   if(mysql_query("CREATE DATABASE IF NOT EXISTS ". $db_name, $conn_id)){
    $DB['error'] = false;
   }
   else{
    $DB['msg'] = 'Error creating database: ' . mysql_error();
   }

 return $DB;
}

function create_db_tables($server, $user, $pass, $db_name){

 $db = create_db($server, $user, $pass, $db_name);

   if($db['error']){
    return $db['msg'];
   }

 // Create tables in database
 mysql_select_db($db_name, $db['conn_id']);

 $tables = array();
 $tables[] = "chat";
 $tables[] = "chat_config_options";
 $tables[] = "chatrooms";
 $tables[] = "members";
 $tables[] = "admin";
 $tables[] = "chat_window_state_tracker";

 $tables_len = count($tables);

   for($i=0; $i < $tables_len; $i++){

     if($tables[$i] == "chat"){
       mysql_query("CREATE TABLE IF NOT EXISTS chat (
       chat_id int NOT NULL auto_increment PRIMARY KEY,
       read_status enum('T', 'F') NOT NULL default 'F',
       user_id int(11) NOT NULL,
       user_name varchar(255) NOT NULL,
       receiver_id int(11) NOT NULL,
       receiver_name varchar(255) NOT NULL,
       recipient_online varchar(7),
       room varchar(255),
       room_leaver_id int(11) NOT NULL default -1,
       room_leaver_name varchar(255) NOT NULL default '',
       posted_on datetime NOT NULL,
       message text NOT NULL,
       font varchar(200),
       fontsize varchar(6),
       fontcolour varchar(20) default '#000000',
       fontweight varchar(10),
       fontstyle varchar(10), 
       textdecoration varchar(15)
       )"); //end sql command
      }

      else if($tables[$i] == "chat_config_options"){
       mysql_query("CREATE TABLE IF NOT EXISTS chat_config_options (
       site_name varchar(50) NOT NULL,
       chat_theme varchar(50) NOT NULL,
       use_stack_view enum('T', 'F') default 'T',
       chat_container_id varchar(25)
       )"); //end sql command
      }

      else if($tables[$i] == "chatrooms"){
       mysql_query("CREATE TABLE IF NOT EXISTS chatrooms (
       id int NOT NULL auto_increment PRIMARY KEY,
       room_name varchar(50) NOT NULL,
       date_added datetime NOT NULL
       )"); //end sql command
      }

      else if($tables[$i] == "members"){
       mysql_query("CREATE TABLE IF NOT EXISTS members (
       id int NOT NULL auto_increment PRIMARY KEY,
       firstname varchar(255) NOT NULL,
       lastname varchar(255) NOT NULL,
       email varchar(255) NOT NULL,
       password varchar(255) NOT NULL,
       sex varchar(15),
       country varchar(255),
       state varchar(255),
       photo varchar(255), 
       registered_on datetime NOT NULL
       )"); //end sql command
      }
     

      else if($tables[$i] == "admin"){
       mysql_query("CREATE TABLE IF NOT EXISTS admin (
       id int NOT NULL auto_increment PRIMARY KEY,
       admin_email varchar(50) NOT NULL,
       admin_pass varchar(41) NOT NULL,
       admin_level varchar(50),
       date_added datetime NOT NULL
       )"); //end sql command
      }

      else if($tables[$i] == "chat_window_state_tracker"){
       mysql_query("CREATE TABLE IF NOT EXISTS chat_window_state_tracker (
       id varchar(255) NOT NULL PRIMARY KEY,
       window_id varchar(255) NOT NULL,
       window_title varchar(255) NOT NULL,
       window_type varchar(255) NOT NULL,
       last_message_id int(11) NOT NULL,
       room varchar(255) NOT NULL default 'undefined',
       starter_id int(11) NOT NULL,
       starter_name varchar(255) NOT NULL,
       receiver_id int(11) NOT NULL,
       receiver_name varchar(255) NOT NULL,
       open_time datetime NOT NULL default '0000-00-00 00:00:00',
       focused enum('T', 'F') NOT NULL default 'F',
       closed enum('T', 'F') NOT NULL default 'F',
       close_time datetime NOT NULL default '0000-00-00 00:00:00',
       closed_by varchar(255) NOT NULL,
       is_visible enum('T', 'F'),
       minimized enum('T', 'F') NOT NULL default 'F',
       maximized enum('T', 'F') NOT NULL default 'F',
       width varchar(100) NOT NULL,
       height varchar(100) NOT NULL,
       position_left varchar(100) NOT NULL,
       position_top varchar(100) NOT NULL,
       position_right varchar(100) NOT NULL,
       position_bottom varchar(100) NOT NULL,
       position_style varchar(255) 
       )"); //end sql command
      }
   }
 return true;
}

?>