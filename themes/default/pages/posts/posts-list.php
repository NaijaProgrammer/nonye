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
 <div class="col-lg-9" style="border:1px solid #eee; border-radius:3px; ">

  <?php if( isset($_GET['view']) && ($_GET['view'] == 'forums') ): ?>
  <section id="forums-list">
  <div class="page-header page-heading">
    <h1 class="pull-left">Forums</h1>
    <ol class="breadcrumb pull-right where-am-i">
      <li><a href="#">Forums</a></li>
      <li class="active">List of topics</li>
    </ol>
    <div class="clearfix"></div>
  </div>
  <p class="lead">This is the right place to discuss any ideas, critics, feature requests and all the ideas regarding our website. Please follow the forum rules and always check FAQ before posting to prevent duplicate posts.</p>
 
  <table class="table forum table-striped">
    <thead>
      <tr>
        <th class="cell-stat"></th>
        <th><h3>Forum Name</h3></th>
        <th class="cell-stat  hidden-xs hidden-sm">Topics</th>
        <th class="cell-stat  hidden-xs hidden-sm">Posts</th>
        <th class="cell-stat-2x hidden-xs hidden-sm">Last Post</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class=""><i class="fa fa-question fa-2x text-primary"></i></td>
        <td><h4><a href="#">Category 1</a><br><small>Some description</small></h4></td>
        <td class=" hidden-xs hidden-sm"><a href="#">9 542</a></td>
        <td class=" hidden-xs hidden-sm"><a href="#">89 897</a></td>
        <td class="hidden-xs hidden-sm">by <a href="#">John Doe</a><br><small><i class="fa fa-clock-o"></i> 3 months ago</small></td>
      </tr>
      <tr>
        <td class=""><i class="fa fa-exclamation fa-2x text-danger"></i></td>
        <td><h4><a href="#">Category 2</a><br><small>Category description</small></h4></td>
        <td class=" hidden-xs hidden-sm"><a href="#">6532</a></td>
        <td class=" hidden-xs hidden-sm"><a href="#">152123</a></td>
        <td class="hidden-xs hidden-sm">by <a href="#">Jane Doe</a><br><small><i class="fa fa-clock-o"></i> 1 years ago</small></td>
      </tr>
	  <tr>
        <td class=""><i class="fa fa-exclamation fa-2x text-danger"></i></td>
        <td><h4><a href="#">Category 3</a><br><small>Category description</small></h4></td>
        <td class=" hidden-xs hidden-sm"><a href="#">6532</a></td>
        <td class=" hidden-xs hidden-sm"><a href="#">152123</a></td>
        <td class="hidden-xs hidden-sm">by <a href="#">Jane Doe</a><br><small><i class="fa fa-clock-o"></i> 1 years ago</small></td>
      </tr>
    </tbody>
  </table>
  <table class="table forum table-striped">
    <thead>
      <tr>
        <th class="cell-stat"></th>
        <th>
          <h3>Open discussion</h3>
        </th>
        <th class="cell-stat  hidden-xs hidden-sm">Topics</th>
        <th class="cell-stat  hidden-xs hidden-sm">Posts</th>
        <th class="cell-stat-2x hidden-xs hidden-sm">Last Post</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td></td>
        <td colspan="4" class="center">No topics have been added yet.</td>
      </tr>
    </tbody>
  </table>
  </section>
  
  <?php else: ?>
  <section id="topics-list">
   <table class="table">
    <thead>
      <tr>
	    <?php if( get_app_setting('show-post-forum-field', true) ): ?>
		 <th class="cell-stat hidden-xs hidden-sm forums">Forum</th>
		<?php endif; ?>
		<?php if( get_app_setting('show-post-category-field', true) ): ?>
		 <th class="cell-stat hidden-xs hidden-sm categories">Category</th>
		<?php endif; ?>
        <th class="cell-stat hidden-xs hidden-sm views">Views</th>
		<th class="cell-stat hidden-xs hidden-sm replies">Replies</th>
        <th><h3><!-- Title and Summary --></h3></th>
		<th class="cell-stat hidden-xs authors">Author</th>
      </tr>
    </thead>
    <tbody>
	<?php foreach($posts AS $post_id): ?>
	<?php $post = PostModel::get_post_instance($post_id); ?>
      <tr class="post-summary">
	    <?php if( get_app_setting('show-post-forum-field', true) ): ?>
	    <td class="hidden-xs hidden-sm forums">
		 <?php if( !empty($post->get_forums()) ): ?>
		  <?php $forum = ForumModel::get_forum_instance( $post->get_forums()[0] ); ?>
		  <?php $forum_url = generate_url(array('controller'=>'posts', 'action'=>'forum', 'qs'=>array($forum->get('name')))); ?>
		  <a href="<?php echo sanitize_html_attribute($forum_url); ?>" title="view <?php echo sanitize_html_attribute($forum->get('name')); ?> forum posts">
		   <?php echo $forum->get('name'); ?>
		  </a>
		 <?php endif; ?>
		</td>
		<?php endif; ?>
		
		<?php if( get_app_setting('show-post-category-field', true) ): ?>
	    <td class="hidden-xs hidden-sm categories">
		 <?php if( !empty($post->get_categories()) ): ?>
		  <?php $category = CategoryModel::get_category_instance( $post->get_categories()[0] ); ?>
		  <?php $category_url = generate_url(array('controller'=>'posts', 'action'=>'category', 'qs'=>array($category->get('name')))); ?>
		  <a href="<?php echo sanitize_html_attribute($category_url); ?>" title="view posts filed under <?php echo sanitize_html_attribute($category->get('name')); ?> category">
		   <?php echo $category->get('name'); ?>
		  </a>
		 <?php endif; ?>
		</td>
		<?php endif; ?>
		
        <td class="hidden-xs hidden-sm views">
		 <a href="#" title="<?php echo sanitize_html_attribute($post->get_view_data($count=true)); ?> views">
		  <?php echo format_count($post->get_view_data($count=true)); ?>
		 </a>
		</td>
        <td class="hidden-xs hidden-sm replies">
		 <a href="#" title="<?php echo sanitize_html_attribute($post->get_comments($count=true)); ?> replies">
		  <?php echo format_count($post->get_comments($count=true)); ?>
		 </a>
		</td>
        <td class="post-header">
		 <a class="post-title" href="<?php echo sanitize_html_attribute(get_post_url($post_id)); ?>" title="<?php echo sanitize_html_attribute($post->get('title')); ?>">
		  <?php echo $post->get('title'); ?>
		 </a>
		 <small class="post-date" title="Posted on <?php echo sanitize_html_attribute(format_date($post->get('date_created'))); ?>">
		  <i class="fa fa-edit"></i>&nbsp;<?php echo format_date($post->get('date_created'), 'F d, Y'); ?>
		 </small>
		 <!--<div class="summary-text"><?php //echo substr($post->get('content'), 0, 140); ?></div>-->
		 <div style="margin-top:5px;">
		  <?php $ptags = $post->get_tags(); ?>
		  <?php for($i=0, $len=count($ptags); $i < $len; $i++): ?>
		  <?php $tag = TagModel::get_tag_instance($ptags[$i]); ?>
		  <?php $tag_url = generate_url(array('controller'=>'posts', 'action'=>'tagged', 'qs'=>array($tag->get('name')))); ?>
		  <a href="<?php echo sanitize_html_attribute($tag_url); ?>" title="view posts tagged <?php echo sanitize_html_attribute($tag->get('name')); ?>">
		   <small class="float-left post-tag"><?php echo $tag->get('name'); ?></small>
		  </a>
		  <?php endfor; ?>
		 </div>
		</td>
		<td class="post-author hidden-xs position-relative">
		 <?php $post_author = UserModel::get_user_instance( $post->get('author_id') ); ?>
		 <a href="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>" title="<?php echo sanitize_html_attribute($post_author->get('username')); ?>">
		  <img class="user-image" src="<?php echo sanitize_html_attribute(get_user_image_url($post->get('author_id'))); ?>" />
		 </a>
		</td>
      </tr>
    <?php endforeach; ?>
	</tbody>
  </table>
  </section>
  <?php endif; ?>
  
 </div>
 
 <div>&nbsp;</div>
 <?php include __DIR__. '/common/older-posts-load-button.php'; ?>
 
</div>
<div class="clear">&nbsp;</div>

<script>
function createPostHTML(post)
{
	//return createListViewHTML(post);
	
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
		'<tr class="post-summary">',
		 '<td class="hidden-xs hidden-sm forums"><a href="' + forum.url + '" title="view ' + forum.name + ' forum posts">' + forum.name + '</a></td>',
		 '<td class="hidden-xs hidden-sm categories"><a href="' + category.url + '" title="view posts filed under ' + category.name + ' category">' + category.name + '</a></td>',
		 '<td class="hidden-xs hidden-sm views"><a href="#" title="' + post.viewCount + ' views">' + post.fViewCount + '</a></td>',
		 '<td class="hidden-xs hidden-sm replies"><a href="#" title="' + post.commentCount + ' replies">' + post.fCommentCount + '</a></td>',
		 '<td class="post-header">',
		  '<a class="post-title" href="' + post.url + '" title="' + post.title + '">' + post.title + '</a>',
		  '<small class="post-date" title="' + post.dateCreated + '"><i class="fa fa-edit"></i>&nbsp;' + post.fDateCreated + '</small>',
		  '<div style="margin-top:5px;">' + tagsHTML + '</div>',
		 '</td>',
		 '<td class="post-author hidden-xs position-relative"><a href="' + author.url + '"><img class="user-image" src="' + author.imageURL + '" /></a></td>',
		'</tr>'
	].join('');

	function assembleTagHtml(tag)
	{
		return '<a href="' + tag.url + '" title="view posts tagged ' + tag.name + '"><small class="float-left post-tag">' + tag.name + '</small></a>';
	}
}
</script>
<?php $post_html_creator = 'createPostHTML'; ?>
<?php $posts_container   = "$('#topics-list table tbody')"; ?>
<?php include __DIR__. '/common/posts-loader-script.php'; ?>

<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>