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
<div class="container main-container">
  
 <?php include __DIR__. '/common/view-switcher.php'; ?>
 <?php include __DIR__. '/common/new-posts-alert.php'; ?>
 
 <div class="col-lg-3 hidden-xs" style="border:none;1px solid #ccc; padding-left:0">
  <?php $page_instance->add_sidebar('recent-comments'); ?>
  <?php $page_instance->add_sidebar('forums'); ?>
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
	    <th class="cell-stat hidden-xs hidden-sm forums">Forum</th>
		<th class="cell-stat hidden-xs hidden-sm categories">Category</th>
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
	    <td class="hidden-xs hidden-sm forums">
		 <?php $forum = ForumModel::get_forum_instance( $post->get_forums()[0] ); ?>
		 <?php $forum_url = generate_url(array('controller'=>'posts', 'action'=>'forum', 'qs'=>array($forum->get('name')))); ?>
		 <a href="<?php echo sanitize_html_attribute($forum_url); ?>" title="view <?php echo sanitize_html_attribute($forum->get('name')); ?> forum posts">
		  <?php echo $forum->get('name'); ?>
		 </a>
		</td>
	    <td class="hidden-xs hidden-sm categories">
		 <?php $category = CategoryModel::get_category_instance( $post->get_categories()[0] ); ?>
		 <?php $category_url = generate_url(array('controller'=>'posts', 'action'=>'category', 'qs'=>array($category->get('name')))); ?>
		 <a href="<?php echo sanitize_html_attribute($category_url); ?>" title="view posts filed under <?php echo sanitize_html_attribute($category->get('name')); ?> category">
		  <?php echo $category->get('name'); ?>
		 </a>
		</td>
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
		 <a class="post-title" href="<?php echo sanitize_html_attribute(get_post_url($post_id)); ?>" title=""><?php echo $post->get('title'); ?></a>
		 <small class="post-date" title="<?php echo sanitize_html_attribute($post->get('date_added')); ?>">
		  <i class="fa fa-edit"></i>&nbsp;<?php echo $post->get('date_added'); ?>
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
		 <a href="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>">
		  <img class="user-image" src="<?php echo $post_author->get('image-url', get_app_setting('default-user-image-url')); ?>" />
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
(function (){
	var newestPostID = <?php echo max($posts); ?>;
	var oldestPostID = <?php echo min($posts); ?>;
	var newPostsCount = 0;
	var newPostsQueue = [];

	setTimeout( function(){ getPosts('newer'); }, 1000 );
	
	$('#old-posts-loader').on('click', function(){
		setAsProcessing('old-posts-loader');
		getPosts( 'older', function(){ unsetAsProcessing('old-posts-loader'); } );
	});
	
	function parseLocation()
	{
		var path = location.pathname; //e.g /sites/zamaju-forums/posts/forum/Arts/
		var crumbs = path.split('/'); 
			crumbs.pop(); //remove the empty array member as a result of the last '/'
		var crumbName;
		var crumbParent;
		if(crumbs.length >= 3)
		{
			crumbName = crumbs[crumbs.length - 1]; //e.g Arts
			crumbParent = crumbs[crumbs.length - 2]; //e.g forum
		}
		
		return {base:crumbParent, endPoint:crumbName, }
	}
	function setNewestPostID(postID)
	{
		newestPostID = postID;
	}
	function setOldestPostID(postID)
	{
		oldestPostID = postID;
	}
	function getNewestPostID()
	{
		return newestPostID;
	}
	function getOldestPostID()
	{
		return oldestPostID;
	}
	function resetNewPostsCount()
	{
		newPostsCount = 0;
	}
	function updateNewPostsCount(count)
	{
		newPostsCount += parseInt(count);
	}
	function getNewPostsCount()
	{
		return newPostsCount;
	}
	function getMinID(posts)
	{
		var postIDS = [];
		
		for(var i = 0, len=posts.length; i < len; i++)
		{
			var currPost = posts[i];
			postIDS.push(currPost.id);
		}
		
		return Math.min.apply(null, postIDS);
	}
	function getMaxID(posts)
	{
		var postIDS = [];
		
		for(var i = 0, len=posts.length; i < len; i++)
		{
			var currPost = posts[i];
			postIDS.push(currPost.id);
		}
		
		return Math.max.apply(null, postIDS);
	}
	function addToNewPostsQueue(posts)
	{
		for(var i = 0, len=posts.length; i < len; i++)
		{
			var currPost = posts[i];
			newPostsQueue.push(currPost);
		}
	}
	function getQueuedPosts()
	{
		return newPostsQueue;
	}
	function resetNewPostsQueue()
	{
		newPostsQueue = [];
	}
	function showPosts(posts, type)
	{
		switch(type)
		{
			case 'older' : showOlderPosts(posts); break;
			case 'newer' :
			default      : showNewerPosts(posts); break;
		}
		function showNewerPosts(posts)
		{
			var newPostsStr = '';
			
			for(var i = 0, len=posts.length; i < len; i++)
			{
				var currPost = posts[i];
				newPostsStr += assemblePostHTML(currPost);
			}
			
			//console.log(newPostsStr);
			var postsContainer = $('#topics-list table tbody');
			postsContainer.html( newPostsStr + postsContainer.html() );
		}

		function showOlderPosts(posts)
		{
			var oldPostsStr = '';
			
			for(var i = 0, len=posts.length; i < len; i++)
			{
				var currPost = posts[i];
				oldPostsStr += assemblePostHTML(currPost);
			}
			
			var postsContainer = $('#topics-list table tbody');
			//console.log(oldPostsStr);
			postsContainer.html( postsContainer.html() + oldPostsStr);
		}
	}
	function assemblePostHTML(post)
	{
		var forum    = post.forum;
		var category = post.category;
		var tags     = post.tags;
		var author   = post.author;
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
	function getPosts(type, completeCallback)
	{  
		var  lastID = ( (type == 'older') ? getOldestPostID() : getNewestPostID() );
		var pathData = parseLocation();
		var requestData = { p:'posts', 'get-posts':type, 'id':lastID };
		
		requestData[pathData.base] = pathData.endPoint; //'forum':'arts', 'category':'celebrities', 'tags':'jquery', 'author':'orjimekwe'
		
		$.ajax(ajaxURL, {
			method : 'GET',
			cache  : false,
			data   : requestData, //{ p:'posts', 'get-posts':type, 'id':lastID },
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( type + ' posts status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				
				if(isDevServer)
				{
					console.log( type + ' posts status : ' + status + '\r\nsuccess : ' + data );
				}
				data = JSON.parse(data);
				if(data.length <= 0)
				{
					return;
				}
				
				if(type == 'newer')
				{
					updateNewPostsCount(data.length);
					setNewestPostID( getMaxID(data) );
					addToNewPostsQueue(data);
					$('#new-posts-alert').html('<a id="show-new-posts" class="cursor-pointer" style="text-decoration:none; color:#fff;">' + getNewPostsCount() + ' new posts</a>');
					$('#new-posts-alert').fadeIn('slow');
					$('#show-new-posts').on('click', function(){
						resetNewPostsCount();
						showPosts( getQueuedPosts(), 'newer' );
						resetNewPostsQueue();
						$('#new-posts-alert').fadeOut('slow');
					});
				}
				else if(type == 'older')
				{
					setOldestPostID( getMinID(data) );
					showPosts(data, 'older');
				}
			},
			complete : function(jqXHR, status)
			{
				if(typeof completeCallback === 'function')
				{
					completeCallback();
				}
				
				setTimeout( function(){getPosts(type, completeCallback)}, 1000 * 30 );
			}
		});
	}
})();
</script>

<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>