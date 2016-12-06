<?php 
 $new_site_name = '';
 if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $new_site_name = $_POST['new_site_name'];
 }
?>
<html>
<head>
 <title></title>
 <link rel="stylesheet" type="text/css" href="styles/general_form_style.css">
</head>
<body>
 <fieldset>
  <legend><?php echo APP_NAME; ?> Chat Title Changer Form</legend>
  
  <form method="post" action="<?php echo($_SERVER['PHP_SELF']);?>">
   <div>
    <label for="new_site_name">Specify New Chat Title : </label>
    <input type="text" name="new_site_name" value="<?php echo($new_site_name);?>">
   </div>
   <input type="hidden" name="optn" value="<?php echo($opt); ?>">
   <div><label for="submit_button">&nbsp;</label><input type="submit" value="Change"></div>
   </form>
   <span style="color: red;"><?php echo (isset($message) ? $message : '');?></span>
 </fieldset>
</body>
</html>
