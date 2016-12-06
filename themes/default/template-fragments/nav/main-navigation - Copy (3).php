<?php $user_is_logged_in = UserModel::user_is_logged_in(); ?>
<!--<nav class="navbar navbar-inverse navbar-static-top" role="navigation">-->
<nav id="main-navigation" class="navbar navbar-inverse navbar-fixed-top main-navigation-bottom-border" role="navigation">
 <div class="container">
  <div class="navbar-header">
   <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
   </button>
   <a class="navbar-brand" href="<?php //echo sanitize_html_attribute(get_site_url()); ?>"><?php //echo get_site_name(); ?></a>
  </div>         
  <div class="collapse navbar-collapse" id="navbarCollapse">
   <?php /*
   <ul class="nav navbar-nav">
    <li><a href="#">About</a></li>
	<li><a href="#">Blog</a></li>
	<li><a href="#">Contact</a></li>
    <li><a href="#">FAQ</a></li>
   </ul>
   */?>
   <ul class="nav navbar-nav">
    <?php if($user_is_logged_in): ?>
	<li class="dropdown cursor-pointer authenticated-user-menu">
	 <a class="dropdown-toggle glyphicon glyphicon-user" data-toggle="dropdown" aria-expanded="false"></a>
	 <ul class="dropdown-menu" role="menu">
	  <li title="My account"><a href="<?php echo get_user_profile_url(); ?>"><span class="fa fa-icon fa-th-list"></span>&nbsp;My account</a></li>
	  <li title="Registered users"><a href="<?php echo get_site_url(); ?>/users"><span class="fa fa-icon fa-users"></span>&nbsp; Users</a></li>
	  <li title="Sign out"><a href="<?php echo get_site_url(); ?>/logout"><span class="fa fa-icon fa-sign-out"></span>&nbsp; Sign out</a></li>
	 </ul>
	</li>
	<?php else: ?>
	<li class="user-auth-btn" title="Login or Signup"><a class="glyphicon glyphicon-user cursor-pointer"></a></li>
	<?php endif; ?>
   </ul>
   <ul class="nav navbar-nav full-effect">
	<li class="hidden-xs hidden-sm">
	 <div id="user-notification-dropdown-container" class="dropdown dropdown-lg">
	  <span class="notification-counter"></span>
	  <span id="notifications-toggler" class="glyphicon glyphicon-bell cursor-pointer"  data-toggle="dropdown" aria-expanded="false"></span>
	  <div class="dropdown-menu dropdown-menu-left" role="menu">
	   <div>
	   <ul id="notifications"><ul>
	   </div>
	  </div>
	 </div>
	</li>
   </ul>
   <ul class="nav navbar-nav pull-right">
	<li class="hidden-xs hidden-sm">
     <div class="input-group" id="adv-search">
      <input id="post-search-keyword" type="text" class="form-control post-filter" placeholder="Find posts (Enter post title or keyword to search for)" />
      <div class="input-group-btn">
       <div class="btn-group" role="group">
        <div class="dropdown dropdown-lg">
         <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
         <div class="dropdown-menu dropdown-menu-right" role="menu">
          <form id="post-search-form" class="form-horizontal" role="form">
		   <?php /*
           <div class="form-group">
            <label for="filter">Filter by</label>
            <select class="form-control">
             <option value="0" selected>All Posts</option>
             <option value="1">Featured</option>
             <option value="2">Most popular</option>
             <option value="3">Top rated</option>
             <option value="4">Most commented</option>
            </select>
           </div>
		   */?>
		   
           <div class="form-group">
            <label for="authors">Authors</label>
            <input id="post-search-filter-authors" class="form-control post-filter" type="text" placeholder="Type to filter by authors' display name" />
           </div>
           <div class="form-group">
            <label for="forums">Forums</label>
            <input id="post-search-filter-forums" class="form-control post-filter" type="text" placeholder="Type to filter by forums"/>
           </div>
		   <div class="form-group">
            <label for="categories">Categories</label>
            <input id="post-search-filter-categories" class="form-control post-filter" type="text" placeholder="Type to filter by categories" />
           </div>
		   <div class="form-group">
            <label for="tags">Tags</label>
            <input id="post-search-filter-tags" class="form-control post-filter" type="text" placeholder="Type to filter by tags" />
           </div>
		   <div id="post-search-status-message" class="text-center"></div>
           <button type="submit" class="btn btn-primary pull-right pl25 pr25 post-search-btn" style="border-radius:3px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
          </form>
         </div>
        </div>
        <button type="button" class="btn btn-primary pr25 post-search-btn"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
       </div>
      </div>
     </div>
	 
	 <div id="post-search-quick-results" style="position:absolute; display:none; width:500px; max-height:319px; overflow:auto; padding:5px; padding-bottom:3px; border:1px solid #ddd; background:#eee;"></div>
	 
	</li>
	<li>
	 <button class="<?php echo $user_is_logged_in ? 'post-editor-opener' : 'user-auth-btn'; ?> cursor-pointer btn btn-primary btn-small widget-button" data-parent-id="0" title="Start a new topic">
	 + New Topic
	 </button>
	</li>
   </ul>
  </div>
 </div>
</nav>
<div id="notice-bar">
 <span id="notice-bar-content-area"></span>
 <span id="notice-bar-dismisser" class="float-right cursor-pointer ml10 mr10" onclick="slideUp('notice-bar');" title="Dismiss">&times;</span>
</div>
<script>
function notify(msg,opts)
{
	opts     = opts || {};
	duration = opts.duration || 5;
	$Html('notice-bar-content-area', msg);
	
	if( $Style('notice-bar').display != 'block' )
	{
		slideDown('notice-bar', {
			'onSlideDownEnd' : function(){
				setTimeout( function(){slideUp('notice-bar')}, duration * 1000 );
			}
		});
	}
}
</script>


<link rel="stylesheet" href="<?php echo SITE_URL; ?>/js/lib/jquery-tag-editor/jquery.tag-editor.css">
<script src="<?php echo SITE_URL; ?>/js/lib/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jquery-tag-editor/jquery.caret.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jquery-tag-editor/jquery.tag-editor.min.js"></script>
<?php //see: http://stackoverflow.com/a/15088879/1743192 ?>

<?php if(file_exists(SITE_DIR. '/cache/users-cache.php')): ?>
<?php include SITE_DIR. '/cache/users-cache.php'; ?>
<?php
$users = array();
foreach($users_cache AS $user_data)
{  
	$users[] = $user_data['username'];
}

$users_str = json_encode($users, true);
?>
<script>
var availUsers = JSON.parse('<?php echo $users_str; ?>');
$('#post-search-filter-authors').tagEditor({
	forceLowercase : false,
	placeholder    : 'Type to filter by authors\' display name',
	maxTags        : 5,
	autocomplete   : {
		delay    : 0, // show suggestions immediately
		position : { collision: 'flip' }, // automatic menu position up/down
		source   : availUsers
	},
	beforeTagSave : function(field, editor, tags, tag, val)
	{
		//prevent user from entering forums that don't exist
		if(!Site.Util.inArray(val, availUsers))
		{
			return false;
		}

		return val;
	}
});
</script>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/forums-cache.php')): ?>
<?php include SITE_DIR. '/cache/forums-cache.php'; ?>
<?php
$forums = array();
foreach($forums_cache AS $forum_data)
{  
	$forums[] = $forum_data['name'];
}

$forums_str = json_encode($forums, true);
?>
<script>
var availForums = JSON.parse('<?php echo $forums_str; ?>');
$('#post-search-filter-forums').tagEditor({
	forceLowercase : false,
	placeholder    : 'Type to filter by forums',
	maxTags        : 5,
	autocomplete   : {
		delay    : 0, // show suggestions immediately
		position : { collision: 'flip' }, // automatic menu position up/down
		source   : availForums
	},
	beforeTagSave : function(field, editor, tags, tag, val)
	{
		//prevent user from entering forums that don't exist
		if(!Site.Util.inArray(val, availForums))
		{
			return false;
		}

		return val;
	}
});
</script>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/categories-cache.php')): ?>
<?php include SITE_DIR. '/cache/categories-cache.php'; ?>
<?php
$categories = array();
foreach($categories_cache AS $category_data)
{  
	$categories[] = $category_data['name'];
}

$categories_str = json_encode($categories, true);
?>
<script>
var availCategories = JSON.parse('<?php echo $categories_str; ?>');
$('#post-search-filter-categories').tagEditor({
	forceLowercase : false,
	placeholder    : 'Type to filter by categories',
	maxTags        : 5,
	autocomplete   : {
		delay    : 0, // show suggestions immediately
		position : { collision: 'flip' }, // automatic menu position up/down
		source   : availCategories
	},
	beforeTagSave : function(field, editor, tags, tag, val)
	{
		//prevent user from entering forums that don't exist
		if(!Site.Util.inArray(val, availCategories))
		{
			return false;
		}

		return val;
	}
});
</script>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/tags-cache.php')): ?>
<?php include SITE_DIR. '/cache/tags-cache.php'; ?>
<?php
$tags = array();
foreach($tags_cache AS $tag_data)
{  
	$tags[] = $tag_data['name'];
}

$tags_str = json_encode($tags, true);
?>
<script>
var availTags = JSON.parse('<?php echo $tags_str; ?>');
$('#post-search-filter-tags').tagEditor({
	forceLowercase : false,
	placeholder    : 'Type to filter by tags',
	maxTags        : 5,
	autocomplete   : {
		delay    : 0, // show suggestions immediately
		position : { collision: 'flip' }, // automatic menu position up/down
		source   : availTags
	},
	beforeTagSave : function(field, editor, tags, tag, val)
	{
		//prevent user from entering forums that don't exist
		if(!Site.Util.inArray(val, availTags))
		{
			return false;
		}

		return val;
	}
});
</script>
<?php endif; ?>
<script>
(function(){
$('.post-filter').on('keyup', function(e){
	
	$('#post-search-keyword').addClass('bg-right bg-no-repeat');
	setAsProcessing('post-search-keyword');
		
	searchPosts(function(posts){
		
		var postsStr = '';
	
		for(var i = 0, len=posts.length; i < len; i++)
		{
			var currPost = posts[i];
			postsStr += assembleHTML(currPost);
		}
		
		var display = (postsStr == '') ? 'none' : 'block';
		
		$('#post-search-quick-results').html(postsStr);
		$('#post-search-quick-results').css('display', display);
		unsetAsProcessing('post-search-keyword');
	});
	
	function assembleHTML(post)
	{
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
		'<div style="background:#fff; padding:5px 8px; margin-bottom:2px;">',
	     '<h5 style="font-weight:700; margin-top:3px; margin-bottom:2px;"><a href="' + post.url + '" title="' + post.title + '" class="post-title">' + post.title + '</a></h5>',
         '<a style="float:left; margin-right:7px;" href="' + post.url + '"><img style="width:85px; height:85px; "src="' + post.imageURL + '"></a>',
	     '<span>Author: <a class="post-author" href="' + author.url + '">' + author.username + '</a></span><br>',
	     '<span class="date" title="' + post.dateCreated + '">Date created: ' + post.fDateCreated + '</span><br>',
	     '<span class="post-views" title="View posts filed under ' + post.forum.name + ' forum">Forum: <a href="'  + post.forum.url    + '">' + post.forum.name    + '</a></span><br>',
		 '<span class="date" title="View posts filed under ' + post.category.name + ' category">Category : <a href="' + post.category.url + '">' + post.category.name + '</a></span><br>',
		 tagsHTML,
	     //'<span class="post-views" title="' + post.viewCount + '">' + post.fViewCount + '</span><br>',
         //'<span class="post-comments" title="' + post.commentCount + '">' + post.fCommentCount + '</span>',
	     '<div class="clear"></div>',
	    '</div>'
		].join('')

		function assembleTagHtml(tag)
		{
			return '<a href="' + tag.url + '" title="view posts tagged ' + tag.name + '"><small class="float-left post-tag selectable-tag">' + tag.name + '</small></a>';
		}
	}
});
$('.post-search-btn').on('click', function(e){
	e.preventDefault();
	
	var title      = $('#post-search-keyword').val();
	var authors    = $('#post-search-filter-authors').tagEditor('getTags')[0].tags; //.val();
	var forums     = $('#post-search-filter-forums').tagEditor('getTags')[0].tags; //.val();
	var categories = $('#post-search-filter-categories').tagEditor('getTags')[0].tags; //.val();
	var tags       = $('#post-search-filter-tags').tagEditor('getTags')[0].tags; //.val();
	
	searchPosts(function(data){
		if(data.length <= 0)
		{
			return;
		}
			
		var viewType = ( (Site.Util.getQueryStringParameterValue('v') == 'list') ? 'list' : 'grid' );
		var newURL   = siteURL + '/posts/search/'       +
		'?title='      + encodeURIComponent(title)      + 
		'&authors='    + encodeURIComponent(authors)    + 
		'&forums='     + encodeURIComponent(forums)     + 
		'&categories=' + encodeURIComponent(categories) + 
		'&tags='       + encodeURIComponent(tags);
			
		history.pushState(null, "", newURL );
		displayPosts(data, viewType, 'overwrite');
	});
});
$(document).on('click', function(e){
	if( (e.target.className.indexOf('post-filter') == -1) && (e.target.className.indexOf('caret') == -1) )
	{
		$('#post-search-quick-results').css('display', 'none');
	}
});
function searchPosts(callback)
{
	var statusMsgFieldID = 'post-search-status-message';
	
	var keywords   = $('#post-search-keyword').val();
	var authors    = $('#post-search-filter-authors').tagEditor('getTags')[0].tags; //.val();
	var forums     = $('#post-search-filter-forums').tagEditor('getTags')[0].tags; //.val();
	var categories = $('#post-search-filter-categories').tagEditor('getTags')[0].tags; //.val();
	var tags       = $('#post-search-filter-tags').tagEditor('getTags')[0].tags; //.val();
	
	$.ajax(ajaxURL, {
		method : 'GET',
		cache  : true,
		data   : { 
			'p'            : 'posts', 
			'search-posts' : true, 
			'keywords'     : keywords,
			'authors'      : JSON.stringify(authors),
			'forums'       : JSON.stringify(forums),
			'categories'   : JSON.stringify(categories),
			'tags'         : JSON.stringify(tags)
		},
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Post search filter attempt status : ' + status + '\r\nerror : ' + error );
			}
			
			displayStatusMessage(statusMsgFieldID, 'An unknown error occurred. Please try again.', 'error');
		},
		success  : function(data, status, jqXHR){ console.log(data);
		
			if(isDevServer)
			{
				console.log( 'Post search filter attempt status : ' + status + '\r\nsuccess : ' + data );
			}
			
			data = JSON.parse(data);
			callback(data);
			
		},
		complete : function(jqXHR, status)
		{
			//unsetAsProcessing(btnID);
			//enable(btnID);
		}
	})
}
})();
</script>