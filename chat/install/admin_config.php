<?php
require_once('../includes/common.php');
require_once(APP_DOC_PATH. 'admin/includes/functions/admin_sql_functions.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
 
 $admin_email = safe_escape($_POST['admin_email']);
 $admin_pass = safe_escape($_POST['admin_pass']);
 $confirm_admin_pass = safe_escape($_POST['confirm_admin_pass']);
 $admin_level = safe_escape($_POST['admin_level']);//redundant for now, maybe useful later


   if(empty($admin_email) || empty($admin_pass) || empty($confirm_admin_pass)){
    $error_message = "Please Fill in every field";
    require_once('forms/admin_configuration_form.inc');
    exit;
   }

   if($admin_pass != $confirm_admin_pass){
     $error_message = "The passwords you have specified do not match";
     require_once('forms/admin_configuration_form.inc');
     exit;
   }
   if(strlen($admin_pass) < 6){
    $error_message = "Passwords must be at least six(6) characters long";
    require_once('forms/admin_configuration_form.inc');
    exit;
   }

   if(!insert_admin_details($_POST)){
    $error_message = "Error : Unable to register the administrator";
    require_once('forms/admin_configuration_form.inc');
    exit;
   }

 header("Refresh: 5;url=../chat_test.html");

?>

<html>
<head>
 <title></title>
 <style type="text/css"></style>
</head>
<body>
 <?php
  echo 'chat database created'. "<br>";
  echo 'Redirecting you to the test page'. "<br>";
  echo 'If you\'re not automatically redirected in 5 seconds, click <a href=../chat_test.html>here</a>';
  exit;
 ?>
</body>
</html>

<?php
}// close if ... $_POST
else{
 require_once('forms/admin_configuration_form.inc');
}
?>