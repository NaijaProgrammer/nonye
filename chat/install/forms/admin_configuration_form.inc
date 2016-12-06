<?php
 
 $admin_email = '';
 $admin_pass = '';
 $confirm_admin_pass = '';

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $admin_email = $_POST['admin_email'];
  $admin_pass = $_POST['admin_pass'];
  $confirm_admin_pass = $_POST['confirm_admin_pass'];
 }

?>
<html>
<head>
 <title><?php echo APP_NAME; ?> Installation</title>
 <link rel="stylesheet" type="text/css" href="styles/config_style.css">
</head>
<body>
 <fieldset>
  <legend>Chat Central Administrator Details Form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div>
    <label for="admin_email">Enter Administrator Email : </label>
    <input type="text" name="admin_email" value="<?php echo($admin_email);?>">
   </div>
   <div>
    <label for="admin_pass">Enter Administrator Password : </label>
    <input type="password" name="admin_pass" value="<?php echo($admin_pass);?>">
   </div>
   <div>
    <label for="confirm_admin_pass">Confirm Administrator Password : </label>
    <input type="password" name="confirm_admin_pass" value="<?php echo($confirm_admin_pass);?>">
   </div>

   <div>
    <label for="admin_level">Specify Admin Access Level : </label>
    <select name="admin_level">
     <option value="super">Super</option>
    </select>
   </div>
   
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Next"></div>
   </form>
   <span style="color: red;"><?php echo (isset($error_message) ? $error_message : '');?></span>
 </fieldset>
</body>
</html>