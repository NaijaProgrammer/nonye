<?php //verify_user_can('Manage Forums'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Create Forums Cache'));
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
   " * File name: forums-cache.php". NL. 
   " * A static cache of forums, to be updated whenever a new forum is created or whenever admin gives the command to from the admin page". NL.
   " * This helps prevent too much calls to database for the forums, and makes retrieving and processing forums data faster". NL. 
   " *". NL.
   " * Format:". NL. 
   " * \$forums_cache = array(". NL. 
   " *	  array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    ...". NL.
   " * )". NL.
   " */". NL. NL.
   "\$forums_cache = array(". NL;
  $forum_ids  = ForumModel::get_forums(true, array(), array('name'=>'ASC'), 0); 
  foreach($forum_ids AS $forum_id)
  {
	$forum       = ForumModel::get_forum_instance( $forum_id );
	$creator_id  = $forum->get('creator_id');
	$name        = escape_output_string($forum->get('name'));
	$description = escape_output_string($forum->get('description'));
	$date_added  = $forum->get('date_added');
	$posts_count = $forum->get_posts_count();
	$output_str .= "   array('id'=>'$forum_id', 'creator_id'=>'$creator_id', 'name'=>'$name', 'description'=>'$description', 'posts_count'=>'$posts_count', 'date_added'=>'$date_added'),". NL;
  }
  $output_str .= ");";
  $cache_file = new FileWriter(SITE_DIR. '/cache/forums-cache.php', 'WRITE_ONLY');
  $cache_file->write($output_str);
  ?>
  
  File written successfully. File contents:<br><?php echo str_ireplace("\r\n", "<br>", $output_str); ?>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>