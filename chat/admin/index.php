<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');

 if($_SERVER['REQUEST_METHOD'] != 'POST'){
  require_once('includes/forms/admin_login_form.inc');
  exit;
 }

 $admin_email = $_POST['admin_email'];
 $admin_pass = $_POST['admin_pass'];

 if(empty($admin_email) || empty($admin_pass)){
  $error_message = "Please fill in every field";
  require_once('includes/forms/admin_login_form.inc');
  exit;
 }

 if(!is_admin($admin_email, $admin_pass)){
  $error_message = "Failed Login: ensure the details you entered are correct and try again";
  require_once('includes/forms/admin_login_form.inc');
  exit;
 }

 $_SESSION['admin_email'] = $admin_email;
 $_SESSION['admin_pass'] = $admin_pass;
 header("location: admin_home.php");
 exit;
?>