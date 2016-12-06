<?php
 $server_host = '';
 $db_name = '';
 $db_user = '';
 $db_pass = '';

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
   $server_host = $_POST['server_host'];
   $db_name = $_POST['db_name'];
   $db_user = $_POST['db_user'];
   $db_pass = $_POST['db_pass'];
 }
?>
<html>
<head>
 <title><?php echo APP_NAME; ?> Installation</title>
 <link rel="stylesheet" type="text/css" href="styles/install_style.css">
</head>
<body>
 <fieldset>
  <legend>Chat Central Installation form</legend>
  <span style="color: orange; display: block; margin-bottom: 10px;">Fields marked with asterisk are required</span>
  <form method="post" action="install.php">
   <div><label for="server_host">Host : </label><input type="text" name="server_host" value="<?php echo($server_host);?>"><span class="req">*</span></div>
   <div><label for="db_name">Database Name : </label><input type="text" name="db_name" value="<?php echo($db_name);?>"><span class="req">*</span></div>
   <div><label for="db_user">Database Username : </label><input type="text" name="db_user" value="<?php echo($db_user);?>"><span class="req">*</span></div>
   <div><label for="db_pass">Database Password : </label><input type="text" name="db_pass" value="<?php echo($db_pass);?>"></div>
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Next"></div>
   </form>
   <span style="color: red;"><?php echo (isset($error_message) ? $error_message : ''); ?></span>
 </fieldset>
</body>
</html>