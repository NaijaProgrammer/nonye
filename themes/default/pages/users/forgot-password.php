<?php if(UserModel::user_is_logged_in()): header("Location: ". get_user_profile_url()); exit;  endif; ?>
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
 <div class="col-md-9"><?php $page_instance->add_form('forgot-password-form'); ?></div>
 <?php echo $page_instance->add_sidebar(); ?>
</div>
<?php $page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>