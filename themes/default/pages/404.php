<?php
$page_instance = Page::get_instance(); 
$page_instance->add_header(array(
	'page_title'       => 'Page not found',
	'page_keywords'    => '',
	'page_description' => 'Requested resource not found. Browse other pages on '. get_site_name(),
	'robots_value'     => '',
	'stylesheets'      => array(),
	'current_user'     => UserModel::get_user_instance(UserModel::get_current_user_id()),
	'open_graph_data'  => array(
		'url'          => '',
		'title'        => 'Page not found',
		'description'  => 'Requested resource not found. Browse other pages on '. get_site_name(),
		'content-type' => 'website'
	)));
$page_instance->add_nav(); 
?>
<div class="container">
 <div class="row">
  <div class="col-md-12">
  <center>
   <p>
	<span style="font-weight:400; font-size:150px; color:#900;">Oops!!!</span><br />
	<span>The page you are looking for was not found</span>
   </p>
   <p><a href="<?php echo SITE_URL; ?>">Return to Home page</a></p>
   </center>
  </div>
 </div>
</div>
<?php $page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>
