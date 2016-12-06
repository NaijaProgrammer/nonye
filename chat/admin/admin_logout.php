<?php
 session_start();
 require_once('../includes/common.php');

 unset($_SESSION['admin_email']);
 unset($_SESSION['admin_pass']);
 $_SESSION = array();
 session_destroy();

 header("location: ". APP_HTTP_PATH . "admin/");
 exit;
?>
