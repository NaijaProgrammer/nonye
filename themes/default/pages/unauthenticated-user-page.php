<?php
//Explicitly get an instance here, since this isn't like other pages that get the page_instance variable directly from app-controller.
//This is because this page isn't loaded by any controller, if it were, it would get the page_instance variable from the controller.
//Rather it -this page- is loaded by the page itself, the page that needs authentication functionality.
$page_instance = Page::get_instance();
$page_instance->add_header(array(
	'page_title'       => 'Login or Signup',
	'page_keywords'    => '',
	'page_description' => '',
	'open_graph_data'  => array(
		'url'          => '',
		'title'        => 'Login or Signup',
		'description'  => 'Login or signup',
		'content-type' => 'website'
	)
)); 

$page_instance->add_stylesheets(array());
$page_instance->add_nav();
?>
<div class="container main-container">
 <div class="row">
  <div>You must be logged in to view this page</div>
 </div>
</div>
<?php $page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>