<?php //verify_user_can('Manage Tags'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Create Tags Cache'));
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
   " * File name: tags-cache.php". NL. 
   " * A static cache of tags, to be updated whenever a new tag is created or whenever admin gives the command to from the admin page". NL.
   " * This helps prevent too much calls to database for the tags, and makes retrieving and processing tags data faster". NL. 
   " *". NL.
   " * Format:". NL. 
   " * \$tags_cache = array(". NL. 
   " *	  array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    ...". NL.
   " * )". NL.
   " */". NL. NL.
   "\$tags_cache = array(". NL;
  $tag_ids  = TagModel::get_tags(true, array(), array('name'=>'ASC'), 0); 
  foreach($tag_ids AS $tag_id)
  {
	$tag         = TagModel::get_tag_instance( $tag_id );
	$creator_id  = $tag->get('creator_id');
	$name        = escape_output_string($tag->get('name'));
	$description = escape_output_string($tag->get('description'));
	$date_added  = $tag->get('date_added');
	$posts_count = $tag->get_posts_count();
	$output_str .= "   array('id'=>'$tag_id', 'creator_id'=>'$creator_id', 'name'=>'$name', 'description'=>'$description', 'posts_count'=>'$posts_count', 'date_added'=>'$date_added'),". NL;
  }
  $output_str .= ");";
  $cache_file = new FileWriter(SITE_DIR. '/cache/tags-cache.php', 'WRITE_ONLY');
  $cache_file->write($output_str);
  ?>
  
  File written successfully. File contents:<br><?php echo str_ireplace("\r\n", "<br>", $output_str); ?>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>