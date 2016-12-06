<?php
$admin_email = $_SESSION['admin_email'];
$admin_pass = $_SESSION['admin_pass'];

 if(empty($admin_email) || empty($admin_pass)){
  header("location: ". APP_HTTP_PATH . "admin/");
  exit;
 }

 if(!is_admin($admin_email, $admin_pass)){
  header("location: ". APP_HTTP_PATH . "admin/");
  exit;
 }
?>