<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');
 require_once('includes/admin_authenticator.php');

 $job = trim($_GET['req']);


 if($job == 'cco'){
  $opt = $_GET['opt'];
  header("location: chat_options_changer.php?opt=$opt");
  exit;
 }
 else if($job == 'acr'){
  header("location: chat_rooms_add.php");
  exit;
 }
 else if($job == 'dcr'){
  header("location: chat_rooms_delete.php");
  exit;
 }
 ?>
 