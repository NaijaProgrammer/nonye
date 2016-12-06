<?php //verify_user_can('Manage Forums'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Create Users Cache'));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading(''); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 
 <div class="inline-block main-content">
  <?php 
   defined("NL") or define("NL", "\r\n");
   $output_str = "<?php". NL.
   "/**". NL. 
   " * File name: users-cache.php". NL. 
   " * A static cache of forums, to be updated whenever a new user is created or whenever admin gives the command to from the admin page". NL.
   " * This helps prevent too much calls to database for the users, and makes retrieving and processing users data faster". NL. 
   " *". NL.
   " * Format:". NL. 
   " * \$users_cache = array(". NL. 
   " *	  array(id, email, username, firstname, lastname, date_registered),". NL.
   " *    array(id, email, username, firstname, lastname, date_registered),". NL.
   " *    ...". NL.
   " * )". NL.
   " */". NL. NL.
   "\$users_cache = array(". NL;
  $user_ids  = UserModel::get_all_users_id(); 
  foreach($user_ids AS $user_id)
  {
	$user            = UserModel::get_user_instance( $user_id );
	$email           = escape_output_string($user->get('email'));
	$username        = escape_output_string($user->get('username'));
	$firstname       = escape_output_string($user->get('firstname', 'N/A'));
	$lastname        = escape_output_string($user->get('lastname', 'N/A'));
	$date_registered = $user->get('date_registered');
	$output_str .= "   array('id'=>'$user_id', 'email'=>'$email', 'username'=>'$username', 'firstname'=>'$firstname', 'lastname'=>'$lastname', 'date_registered'=>'$date_registered'),". NL;
  }
  $output_str .= ");";
  $cache_file = new FileWriter(SITE_DIR. '/cache/users-cache.php', 'WRITE_ONLY');
  $cache_file->write($output_str);
  ?>
  
  File written successfully. File contents:<br><?php echo str_ireplace("\r\n", "<br>", $output_str); ?>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>