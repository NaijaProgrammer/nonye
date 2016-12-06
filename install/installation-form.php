<!DOCTYPE html>
<html>
 <head>
  <title>Michael Orji's Apps Installer</title>
  <style>
    form div{ margin-bottom:15px; }
	form div label{ display:block; margin-bottom:5px; }
    input[type="text"], input[type="password"]{ width:450px; height:20px; padding:10px 15px; border-radius:3px; }
	input[type="submit"] { float:right; margin-right:15px; padding:10px 25px; border:none; border-radius:4px; cursor:pointer; color:#eee; background:linear-gradient(to bottom, #51bbd2 0%, #08c 100%);}
	.outer{ display:table; position:absolute; height:100%; width:100%; }
	.middle{ display:table-cell; vertical-align:middle; }
	.inner{ margin-left:auto; margin-right:auto; width:500px; /*whatever width you want*/; }
  </style>
 </head>
 <body>
  <div class="outer">
   <div class="middle">
    <div class="inner">
     <!--<center>-->
      <div style="color:#900; font-size:20px; font-weight:500; text-align:center; margin-bottom:15px;"><?php echo !empty($status_message) ? $status_message : ''; ?></div>
      <form method="post" action="">
      <div>
	   <label for="site name">Site Name</label>
	   <input type="text" name="site_name" value="<?php echo $site_name; ?>" placeholder="Enter your site name"/>
	  </div>
      <div>
	   <label for="database server name">Database Server Name</label>
	   <input type="text" name="db_server" value="<?php echo $db_server; ?>" placeholder="e.g localhost"/>
	  </div>
      <div>
	   <label for="database user name">Database User Name</label>
	   <input type="text" name="db_user" value="<?php echo $db_user; ?>" placeholder="e.g root"/>
	  </div>
      <div>
	   <label for="database user password">Database User Password</label>
	   <input type="text" name="db_pass" value="<?php echo $db_pass; ?>" placeholder="Enter your database user password"/>
	  </div>
      <div>
	   <label for="database name">Database Name</label>
	   <input type="text" name="db_name" value="<?php echo $db_name; ?>" placeholder="Enter your database name"/>
	  </div>
      <div>
	   <label for="database tables prefix">Database tables prefix</label>
	   <input type="text" name="tables_prefix" value="<?php echo $tables_prefix; ?>" placeholder="Enter a prefix for your database tables"/>
	  </div>
      <div><input type="submit" value="Install"/></div>
      </form>
	 <!--</center>-->
    </div>
   </div>
  </div>
 </body>
</html>