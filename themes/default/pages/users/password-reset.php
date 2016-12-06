<?php if(UserModel::user_is_logged_in()) :header("Location: ". get_user_profile_url()); exit; endif; ?>
<?php
$page_instance->add_header(array(
	'page_title'       => $page_title,
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => $robots_value,
	'open_graph_data'  => $open_graph_data,
	'current_user'     => $current_user //coming from the app-controller class
));

$page_instance->add_stylesheets(array());
$page_instance->add_nav();
?>
<?php $page_instance->add_nav('secondary-navigation'); ?>
<div class="container main-container">
 <div class="col-md-9">
  <?php if(!isset($_GET['nonce'])): ?>
   <p>The operation you have requested is invalid</p>

  <?php else: ?>
  <?php
	/*
	$data       = UserModel::extract_password_recovery_nonce_data($_GET['nonce']); 
	$user_id    = UserModel::get_user_id($data['user_email']);
	$user_nonce = UserModel::get_user_data($user_id, 'pnonce');
	echo $_GET['nonce']. '<br>';
	echo $user_id;
	var_dump( $user_nonce);
	exit;
	*/
	
	$nonce = UserModel::verify_password_recovery_nonce($_GET['nonce']);

	if($nonce)
	{
		echo '<p>Enter your new password in the field below</p>';
		$page_instance->add_form('password-reset-form', array('user_pnonce'=>$nonce));
	}
	else
	{
		echo '<p>This operation has timed out. You must reset your password within 24 hours of receiving our email</p>';
		echo '<p>To make a new password-reset request click <a href="'. generate_url(array('controller'=>'users', 'action'=>'password-retrieve')). '">Here</a></p>';
	}
  ?>
  <?php endif; ?>
 </div>
 
 <?php $page_instance->add_sidebar(); ?>
 
</div>

<?php $page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>