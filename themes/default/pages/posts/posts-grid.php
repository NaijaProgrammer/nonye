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
<script src="<?php echo SITE_URL; ?>/js/lib/holder/holder.js"></script>
<div class="view-switcher-box"><?php include __DIR__. '/common/view-switcher.php'; ?></div>
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
 <div id="posts-grid-container" class="col-lg-9" style="border:none; padding:0;">
  <?php foreach($posts AS $post_id): ?>
  <?php $post = PostModel::get_post_instance($post_id); ?>
  <?php $post_id = $post->get('id'); ?>
  <?php $post_url = get_post_url($post_id); ?>
  <?php $post_title = $post->get('title'); ?>
  <?php $forum = ForumModel::get_forum_instance( $post->get_forums()[0] ); ?>
  <?php $forum_url = generate_url(array('controller'=>'posts', 'action'=>'forum', 'qs'=>array($forum->get('name')))); ?>
  <?php $category = CategoryModel::get_category_instance( $post->get_categories()[0] ); ?>
  <?php $category_url = generate_url(array('controller'=>'posts', 'action'=>'category', 'qs'=>array($category->get('name')))); ?>
  <?php $post_author = UserModel::get_user_instance( $post->get('author_id') ); ?>
  <div class="item col-xs-6 col-sm-4 col-md-3 col-lg-3 post-summary">
   <div class="thumbnail">
    <a href="<?php echo sanitize_html_attribute($post_url); ?>">
	 <?php $default_img_src = 'holder.js/205x110?text='. get_substring( $post_title ); ?>
	 <img class="group list-group-image" src="<?php echo sanitize_html_attribute( get_post_image_url($post_id, $default_img_src) ); ?>" alt="" />
	</a>
    <div class="caption">
     <h4 class="group inner list-group-item-heading post-title">
	  <a href="<?php echo sanitize_html_attribute($post_url); ?>" title="<?php echo sanitize_html_attribute($post_title); ?>" class="post-title">
	   <?php echo get_substring( $post_title ); ?>
	  </a>
	 </h4>
	 <div class="row">
	  <div class="col-xs-12 col-md-12" style="margin-top:-3px; margin-bottom:-3px;">
	   <a class="post-author" href="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>"><?php echo $post_author->get('username'); ?></a>
	   <span class="date" title="<?php echo sanitize_html_attribute(format_date($post->get('date_added'))); ?>"><?php echo get_time_elapsed($post->get('date_added')). ' ago'; ?></span>
	  </div>
	 </div>
	 <?php /*
	 <div class="row">
	  <div class="col-xs-12 col-md-6 post-forum">
	   <a class="float-left" href="<?php echo sanitize_html_attribute($forum_url); ?>" title="Forum: <?php echo $forum->get('name'); ?>">
	    <?php echo get_substring( $forum->get('name'), 11, '' ); ?>
	   </a>
	  </div>
      <div class="col-xs-12 col-md-6 post-category">
	   <a class="pull-right" href="<?php echo sanitize_html_attribute($category_url); ?>" title="Category: <?php echo $category->get('name'); ?>">
	    <?php echo get_substring( $category->get('name'), 11, '' ); ?>
	   </a>
	  </div>
	 </div>
	 */?>
     <div class="row">
      <div class="col-xs-12 col-md-12">
	   <span class="post-views" title="<?php echo sanitize_html_attribute($post->get_view_data($count=true)); ?> views">
	    <?php echo format_count($post->get_view_data($count=true)); ?> views
	   </span>
       <span class="post-comments" title="<?php echo sanitize_html_attribute($post->get_comments($count=true)); ?> replies">
	    <?php echo format_count($post->get_comments($count=true)); ?> replies
	   </span>
	  </div>
     </div>
    </div>
   </div>
  </div>
  <?php endforeach; ?>
 </div>
 
 <div class="clear">&nbsp;</div>
 <?php include __DIR__. '/common/older-posts-load-button.php'; ?>
  
</div>
<div class="clear">&nbsp;</div>

<script>
function createPostHTML(post)
{
	//return createGridViewHTML(post);
	
	var forum    = post.forum;
	var category = post.category;
	var tags     = post.tags;
	var author   = post.author;
	var postImg  = post.imageURL;
	var tagsHTML = '';

	for(var i = 0, len = tags.length; i < len; i++)
	{
		var tag = tags[i];
		tagsHTML += assembleTagHtml(tag);
	}

	return [
		'<div class="item col-xs-3 col-lg-3 post-summary">',
	     '<div class="thumbnail">',
		  '<img class="group list-group-image" src="' + post.imageURL + '" alt="" />',
		  '<div class="caption">',
		   '<h4 class="group inner list-group-item-heading post-title"><a href="' + post.url + '" title="' + post.title + '" class="post-title">' + post.fTitle + '</a></h4>',
		   '<div class="row">',
		    '<div class="col-xs-12 col-md-12" style="margin-top:-3px; margin-bottom:-3px;">',
		     '<a class="post-author" href="' + author.url + '">' + author.username + '</a>',
		     '<span class="date" title="' + post.fDateCreated + '">' + post.dateCreated + ' ago</span>',
		    '</div>',
		   '</div>',
		   '<div class="row">',
		    '<div class="col-xs-12 col-md-12">',
		     '<span class="post-views" title="' + post.viewCount + '">' + post.fViewCount + ' views</span>',
		     '<span class="post-comments" title="' + post.commentCount + '">' + post.fCommentCount + ' replies</span>',
		    '</div>',
		   '</div>',
		  '</div>',
	     '</div>',
	    '</div>'
	].join('');

	function assembleTagHtml(tag)
	{
		return '<a href="' + tag.url + '" title="view posts tagged ' + tag.name + '"><small class="float-left post-tag">' + tag.name + '</small></a>';
	}
}
</script>
<?php $post_html_creator = 'createPostHTML'; ?>
<?php $posts_container   = "$('#posts-grid-container')"; ?>
<?php include __DIR__. '/common/posts-loader-script.php'; ?>

<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>