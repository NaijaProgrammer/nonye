<?php
 
 $admin_email = '';
 $admin_pass = '';

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $admin_email = $_POST['admin_email'];
  $admin_pass = $_POST['admin_pass'];
 }

?>
<html>
<head>
 <title><?php echo APP_NAME; ?> Admin Login</title>
 <link rel="stylesheet" type="text/css" href="styles/admin_login_form_style.css">
</head>
<body>
 <fieldset>
  <legend>Chat Central Administrator Login Form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div>
    <label for="admin_email">Enter Administrator Email : </label>
    <input type="text" name="admin_email" value="<?php echo($admin_email);?>">
   </div>
   <div>
    <label for="admin_pass">Enter Administrator Password : </label>
    <input type="password" name="admin_pass" value="<?php echo($admin_pass);?>">
   </div>
   
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Login"></div>
   </form>
   <span style="color: red;"><?php echo (isset($error_message) ? $error_message : '');?></span>
 </fieldset>
</body>
</html>