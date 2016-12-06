<?php 
 $chatrooms = '';
 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $chatrooms = $_POST['chatrooms'];
 }
?>
<html>
<head>
 <title></title>
 <link rel="stylesheet" type="text/css" href="styles/general_form_style.css">
</head>
<body>
 <fieldset>
  <legend><?php echo APP_NAME; ?> Add New Rooms Form</legend>
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div><label for="chatrooms">Enter chatroom names (seperated by commas) : </label><textarea name="chatrooms" cols="45"><?php echo($chatrooms);?></textarea></div>
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Add Rooms"></div>
   </form>
   <span style="color: red;"><?php echo (isset($message) ? $message : '');?></span>
 </fieldset>
</body>
</html>
