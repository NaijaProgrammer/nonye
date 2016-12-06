<?php 
 $new_chat_link_container_id = '';
 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $new_chat_link_container_id = $_POST['new_chat_link_container_id'];
 }
?>
<html>
<head>
 <title></title>
 <link rel="stylesheet" type="text/css" href="styles/general_form_style.css">
</head>
<body>
 <fieldset>
  <legend><?php echo APP_NAME; ?> Chat Link Container Changer Form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div>
    <label for="new_chat_link_container_id">Select New Chat Link Container Id : </label>
    <input type="text" name="new_chat_link_container_id" value="<?php echo($new_chat_link_container_id);?>">
   </div>
   <input type="hidden" name="optn" value="<?php echo($opt); ?>">
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Change"></div>
   </form>
   <span style="color: red;"><?php echo (isset($message) ? $message : '');?></span>
 </fieldset>
</body>
</html>
