<?php
 
 $site_name = '';
 $chatrooms = '';
 $chat_container_id = '';

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $site_name = $_POST['site_name'];
  $chatrooms = $_POST['chatrooms'];
  $chat_container_id = $_POST['chat_container_id'];
 }

?>
<html>
<head>
 <title><?php echo APP_NAME; ?> Installation</title>
 <link rel="stylesheet" type="text/css" href="styles/config_style.css">
</head>
<body>
 <fieldset>
  <legend>Chat Central Configuration form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div><label for="site_name">Enter Site Name : </label><input type="text" name="site_name" value="<?php echo($site_name);?>"><span class="help" style="cursor: help;"> ? <span style="visibility: visible">(this will be used as the title of the chat window)</span></span></div>
   <div>
    <label for="chat_container_id">Enter the ID of the HTML Element that will hold the chat link/button (optional)</label>
    <input type="text" name="chat_container_id" value="<?php echo($chat_container_id); ?>">
   </div>
   <div>
    <label for="chat_theme">Choose a theme: </label>
    <select name="chat_theme">
     <option value="dark">Dark</option>
     <option value="light">Light</option>
    </select>
   </div>
   <div>
    <label for="use_stack_view">Select a view: </label>
    <select name="use_stack_view">
     <option value="T">Stack (Layered)</option>
     <option value="F">Independent (Isolated)</option>
    </select>
   </div>
   <div><label for="chatrooms">Enter chatroom names (seperated by commas) : </label><textarea name="chatrooms" cols="45"><?php echo($chatrooms);?></textarea></div>
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Next"></div>
   </form>
   <span style="color: red;"><?php echo (isset($error_message) ? $error_message : '');?></span>
 </fieldset>
</body>
</html>