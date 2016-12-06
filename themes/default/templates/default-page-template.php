<?php //$page_instance = Page::get_instance(); ?>
<?php
$page_instance->add_header(array(
	'page_title'       => $page_title, 
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => isset($robots_value) ? $robots_value : 'all',
	'open_graph_data'  => $open_graph_data,
	'current_user'     => $current_user, //coming from the app-controller class
)); 

$page_instance->add_nav(); 
?>
<?php $page_instance->add_nav('secondary-navigation'); ?>
<div class="container main-container">
 <div class="row">
  <?php echo $page_content; ?>
 </div>
</div>
<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>