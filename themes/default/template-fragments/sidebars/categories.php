<div class="module">
 <h3 class="module-header">Categories</h3>
 <div>
  <ul>
   <?php $category_ids = CategoryModel::get_categories(true, array(), array('name'=>'ASC'), 0); ?>
   <?php foreach($category_ids AS $category_id): ?>
   <?php $category = CategoryModel::get_category_instance( $category_id ); ?>
   <?php $category_url = generate_url(array('controller'=>'posts', 'action'=>'category', 'qs'=>array($category->get('name')))); ?>
   <?php $category_posts_count = $category->get_posts_count(); ?>
   <li>
	<a href="<?php echo sanitize_html_attribute($category_url); ?>" title="view <?php echo sanitize_html_attribute($category->get('name')); ?> category posts">
	 <?php echo $category->get('name'); ?>
	</a>
	<small><span title="<?php echo sanitize_html_attribute($category_posts_count); ?> posts">(<?php echo $category_posts_count; ?>)</span></small>
   </li>
   <?php endforeach; ?>
  </ul>
 </div>
</div>
