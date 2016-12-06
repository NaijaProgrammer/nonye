<div class="module">
 <h3 class="module-header">Forums</h3>
 <div style="padding:5px 15px;">
  <ul style="list-style-type:none; padding:0;">
   <?php $forum_ids = ForumModel::get_forums(true, array(), array('name'=>'ASC'), 0); ?>
   <?php foreach($forum_ids AS $forum_id): ?>
   <?php $forum = ForumModel::get_forum_instance( $forum_id ); ?>
   <?php $forum_url = generate_url(array('controller'=>'posts', 'action'=>'forum', 'qs'=>array($forum->get('name')))); ?>
   <?php $forum_posts_count = $forum->get_posts_count(); ?>
   <li style="display:inline-block; margin-right:15px;">
	<a href="<?php echo sanitize_html_attribute($forum_url); ?>" title="view <?php echo sanitize_html_attribute($forum->get('name')); ?> forum posts">
	 <?php echo $forum->get('name'); ?>
	</a>
	<small><span title="<?php echo sanitize_html_attribute($forum_posts_count); ?> posts">(<?php echo $forum_posts_count; ?>)</span></small>
	
   </li>
   <?php endforeach; ?>
  </ul>
 </div>
</div>
