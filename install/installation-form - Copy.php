<!DOCTYPE html>
<html>
 <head>
  <title>Michael Orji's Apps Installer</title>
  <style>
     form div{ margin-bottom: 20px; }
	 form div label{ float: left; width: 250px; }
     form input[type="text"], form input[type="password"]{ width: 450px; height: 20px;}
  </style>
 </head>
 <body>
  <div><?php echo !empty($status_message) ? $status_message : ''; ?></div>
  <form method="post" action="">
   <div><input type="text" name="site_name"  value="<?php echo $site_name; ?>"    placeholder="Site Name"/></div>
   <div><input type="text" name="db_server"  value="<?php echo $db_server; ?>"    placeholder="Database Server"/></div>
   <div><input type="text" name="db_user"    value="<?php echo $db_user;   ?>"    placeholder="Database User"/></div>
   <div><input type="text" name="db_pass"    value="<?php echo $db_pass;   ?>"    placeholder="Database User Password"/></div>
   <div><input type="text" name="db_name"    value="<?php echo $db_name;   ?>"    placeholder="Database Name"/></div>
   <div><input type="text" name="tables_prefix" value="<?php echo $tables_prefix; ?>" placeholder="Prefix of Database Tables"/></div>
   <div><input type="submit" value="Run Installer"/></div>
  </form>
 </body>
</html>