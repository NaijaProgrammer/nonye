<?php
 session_start();
 require_once('../includes/common.php');
 require_once('includes/functions/admin_sql_functions.php');
 require_once('includes/admin_authenticator.php');
?>
<html>
<head>
 <title><?php echo APP_NAME; ?> Administrator Page</title>
</head>
<body>
 <?php require_once('includes/admin_links.php'); ?>
</body>
</html>