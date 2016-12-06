<?php 
 $new_chat_theme = '';
 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $new_chat_theme = $_POST['new_chat_theme'];
 }
?>
<html>
<head>
 <title></title>
 <link rel="stylesheet" type="text/css" href="styles/general_form_style.css">
</head>
<body>
 <fieldset>
  <legend><?php echo APP_NAME; ?> Theme Changer Form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div>
    <label for="new_chat_theme">Select New Theme : </label>
    <select name="new_chat_theme">
     <option value="dark">Dark</option>
     <option value="light">Light</option>
    </select>
   </div>
   <input type="hidden" name="optn" value="<?php echo($opt); ?>">
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Change Theme"></div>
   </form>
   <span style="color: red;"><?php echo (isset($message) ? $message : '');?></span>
 </fieldset>
</body>
</html>
