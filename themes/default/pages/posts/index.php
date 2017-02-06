<?php $page_instance = Page::get_instance(); ?>
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
<div  class="view-switcher-box"><?php include __DIR__. '/common/view-switcher.php'; ?></div>
<?php $page_instance->add_nav('secondary-navigation'); ?>
<div class="container posts-listing main-container">
  
 <div class="clear"></div>
 <?php include __DIR__. '/common/new-posts-alert.php'; ?>
 <div class="clear" style="margin-bottom:5px;"></div>
 <div class="col-lg-3 hidden-xs" style="border:none;1px solid #ccc; padding-left:0">
  <?php $page_instance->add_sidebar('recent-comments'); ?>
  <?php if( get_app_setting('show-post-forum-field', true) ): $page_instance->add_sidebar('forums'); endif; ?>
  <?php if( get_app_setting('show-post-category-field', true) ): $page_instance->add_sidebar('categories'); endif; ?>
  <?php $page_instance->add_sidebar('popular-links'); ?>
 </div>

 <?php 
 if( isset($_GET['v'])&& ($_GET['v'] == 'list') ) {
	include(dirname(__FILE__). '/posts-list.php'); 
 } 
 else {
	include(dirname(__FILE__). '/posts-grid.php'); 
 }
 ?>

 <div class="clear">&nbsp;</div>
 <?php include __DIR__. '/common/older-posts-load-button.php'; ?>
  
</div>
<div class="clear">&nbsp;</div>

<?php import_admin_functions(); ?>
<?php if( user_can('Create Posts') ) : ?>
 <h3 class="post-editor-header-title text-centered">Create New Post</h3>
 <?php get_post_editor( $opts = array('parent_post_id'=>0, 'placeholder'=>'Enter Post', 'value'=>'', 'auto_display'=>true) ); ?>
<?php endif; ?>

<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>