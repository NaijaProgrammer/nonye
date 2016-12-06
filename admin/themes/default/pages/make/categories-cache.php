<?php //verify_user_can('Manage Categories'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Create Categories Cache'));
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
   " * File name: categories-cache.php". NL. 
   " * A static cache of categories, to be updated whenever a new category is created or whenever admin gives the command to from the admin page". NL.
   " * This helps prevent too much calls to database for the categories, and makes retrieving and processing categories data faster". NL. 
   " *". NL.
   " * Format:". NL. 
   " * \$categories_cache = array(". NL. 
   " *	  array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    array(id, creator_id, name, description, posts_count, date_added),". NL.
   " *    ...". NL.
   " * )". NL.
   " */". NL. NL.
   "\$categories_cache = array(". NL;
  $category_ids  = CategoryModel::get_categories(true, array(), array('name'=>'ASC'), 0); 
  foreach($category_ids AS $category_id)
  {
	$category    = CategoryModel::get_category_instance( $category_id );
	$creator_id  = $category->get('creator_id');
	$name        = escape_output_string($category->get('name'));
	$description = escape_output_string($category->get('description'));
	$date_added  = $category->get('date_added');
	$posts_count = $category->get_posts_count();
	$output_str .= "   array('id'=>'$category_id', 'creator_id'=>'$creator_id', 'name'=>'$name', 'description'=>'$description', 'posts_count'=>'$posts_count', 'date_added'=>'$date_added'),". NL;
  }
  $output_str .= ");";
  $cache_file = new FileWriter(SITE_DIR. '/cache/categories-cache.php', 'WRITE_ONLY');
  $cache_file->write($output_str);
  ?>
  
  File written successfully. File contents:<br><?php echo str_ireplace("\r\n", "<br>", $output_str); ?>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>