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
	    <ul id="notifications"></ul>
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
           <button type="submit" id="secondary-post-search-btn" class="btn btn-primary pull-right pl25 pr25 post-search-btn" style="border-radius:3px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
          </form>
         </div>
        </div>
        <button type="button" id="primary-post-search-btn" class="btn btn-primary pl25 pr25 post-search-btn"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
       </div>
      </div>
     </div>
	 
	 <div id="post-search-quick-results" class="post-search-quick-results"></div>
	 <div id="post-search-parameters" class="post-search-parameters">
	  <h4 id="post-search-parameters-header" class="post-search-parameters-header text-centered" style="font-weight:700; margin-top:3px; margin-bottom:2px;"><a style="text-decoration:none;">Search Parameters</a></h4>
	  <div class="post-search-parameters-body" style="background:#fff; padding:5px 8px; margin-bottom:2px;">
	   <span class="post-search-parameters-item">Keywords:</span>&nbsp;<span id="param-post-search-keywords" class="post-search-parameters-item-value"></span><br/>
	   <span class="post-search-parameters-item">Authors:</span>&nbsp;<span id="param-post-search-authors" class="post-search-parameters-item-value"></span><br/>
	   <span class="post-search-parameters-item">Forums:</span>&nbsp;<span id="param-post-search-forums" class="post-search-parameters-item-value"></span><br/>
	   <span class="post-search-parameters-item">Categories:</span>&nbsp;<span id="param-post-search-categories" class="post-search-parameters-item-value"></span><br/>
	   <span class="post-search-parameters-item">Tags:</span>&nbsp;<span id="param-post-search-tags" class="post-search-parameters-item-value"></span>
	  </div>
	 </div>

	 
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

<?php $users = array(); ?>
<?php $forums = array(); ?>
<?php $categories = array(); ?>
<?php $tags = array(); ?>
<?php if(file_exists(SITE_DIR. '/cache/users-cache.php')): ?>
<?php include SITE_DIR. '/cache/users-cache.php'; ?>
<?php foreach($users_cache AS $user_data){ $users[] = $user_data['username']; } ?>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/forums-cache.php')): ?>
<?php include SITE_DIR. '/cache/forums-cache.php'; ?>
<?php foreach($forums_cache AS $forum_data){ $forums[] = $forum_data['name']; } ?>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/categories-cache.php')): ?>
<?php include SITE_DIR. '/cache/categories-cache.php'; ?>
<?php foreach($categories_cache AS $category_data){ $categories[] = $category_data['name']; }?>
<?php endif; ?>
<?php if(file_exists(SITE_DIR. '/cache/tags-cache.php')): ?>
<?php include SITE_DIR. '/cache/tags-cache.php'; ?>
<?php foreach($tags_cache AS $tag_data){ $tags[] = $tag_data['name']; }?>
<?php endif; ?>
<?php $users_str = json_encode($users, true); ?>
<?php $forums_str = json_encode($forums, true); ?>
<?php $categories_str = json_encode($categories, true); ?>
<?php $tags_str = json_encode($tags, true); ?>
<script>
(function(){
	var availUsers      = JSON.parse('<?php echo $users_str; ?>');
	var availForums     = JSON.parse('<?php echo $forums_str; ?>');
	var availCategories = JSON.parse('<?php echo $categories_str; ?>');
	var availTags       = JSON.parse('<?php echo $tags_str; ?>');
	$('#post-search-keyword').on('focus', function(e){
		if(!Site.Util.isEmpty( $O('post-search-keyword').value ))
		{
			runSearch( 'post-search-keyword' );
		}
	});
	$('#post-search-keyword').on('keyup', function(e){
		runSearch( 'post-search-keyword' );
	});
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
		},
		onChange : function(field, editor, tags)
		{
			/*
			* field is the (hidden) original field, 
			* editor is the editor's DOM element (an <ul> list of tag elements), and 
			* tags contains the list of current tags.
			*/
			runSearch( editor );
		}
	});
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
		},
		onChange : function(field, editor, tags)
		{
			runSearch( editor );
		}
	});
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
		},
		onChange : function(field, editor, tags)
		{
			runSearch( editor );
		}
	});
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
		},
		onChange : function(field, editor, tags)
		{
			runSearch( editor );
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
		var searchBoundsElems = [
			'post-filter', 'caret', 'tag-editor', 'tag-editor-spacer', 'tag-editor-tag', 'tag-editor-delete', 'post-search-quick-results', 'post-search-result', 'post-search-result-item', 
			'post-search-parameters', 'post-search-parameters-header', 'post-search-parameters-body', 'post-search-parameters-item', 'post-search-parameters-item-value',
			'ui-autocomplete', 'ui-front', 'ui-menu', 'ui-menu-item', 'ui-widget-content', 'ui-widget-header'
		];
		for(var i = 0; i < searchBoundsElems.length; i++)
		{
			//the parentNode test is for '<a> tag of the post-search-parameters-header, the ui-menu-item <a> and the ui-widget-content <a>
			if( (e.target.className.indexOf( searchBoundsElems[i]) != -1 ) || (e.target.parentNode.className.indexOf( searchBoundsElems[i]) != -1 ) )
			{
				return;
			}
		}
		/*if( (e.target.className.indexOf('post-filter') == -1) && (e.target.className.indexOf('caret') == -1) && (e.target.className.indexOf('tag-editor') == -1) )
		{
			$('#post-search-quick-results').css('display', 'none');
			hideSearchParametersBox();
		}*/
		$('#post-search-quick-results').css('display', 'none');
		hideSearchParametersBox();
	});

	function setSearchParametersBoxValues()
	{
		$('#param-post-search-keywords').html(getKeywordsHTML());
		$('#param-post-search-authors').html(getAuthorsHTML());
		$('#param-post-search-forums').html(getForumsHTML());
		$('#param-post-search-categories').html(getCategoriesHTML());
		$('#param-post-search-tags').html(getTagsHTML());
		
		function getKeywordsHTML()
		{
			var str      = '';
			var keywords = getSearchParameter('keywords').split(' ');
			
			for(var i = 0; i < keywords.length; i ++)
			{
				if( trim(keywords[i]) != '')
				{
					str += assembleParameterHTML(keywords[i], 'keyword');
				}
			}
			
			return str;
			//return getSearchParameter('keywords');
		}
		function getAuthorsHTML()
		{
			var str = '';
			var authors = getSearchParameter('authors');
			
			for(var i = 0; i < authors.length; i ++)
			{
				str += assembleParameterHTML(authors[i], 'author');
			}
			
			return str;
		}
		function getForumsHTML()
		{
			var str = '';
			var forums = getSearchParameter('forums');
			
			for(var i = 0; i < forums.length; i ++)
			{
				str += assembleParameterHTML(forums[i], 'forum');
			}
			
			return str;
		}
		function getCategoriesHTML()
		{
			var str        = '';
			var categories = getSearchParameter('categories');
			
			for(var i = 0; i < categories.length; i ++)
			{
				str += assembleParameterHTML(categories[i], 'category');
			}
			
			return str;
		}
		function getTagsHTML()
		{
			var str  = '';
			var tags = getSearchParameter('tags');
			
			for(var i = 0; i < tags.length; i ++)
			{
				str += assembleParameterHTML(tags[i], 'tag');
			}
			
			return str;
		}
		function assembleParameterHTML(paramName, paramType)
		{
			var baseUrl = siteURL;
			var paramPath = '';
			
			switch(paramType.toLowerCase())
			{
				case 'keywords' : paramPath = ''; break;
				case 'author'   : paramPath = '/users/' + paramName; break;
				case 'forum'    : paramPath = '/forums/' + paramName; break;
				case 'category' : paramPath = '/categories/' + paramName; break;
				case 'tags'     : paramPath = '/tags/' + paramName; break;
			}
			
			var paramUrl = baseUrl + paramPath;
			return '<a  title="' + paramName + '"><small class="post-tag selectable-tag">' + paramName + '</small></a>';
			//return '<a href="' + paramUrl + '" title="' + paramName + '"><small class="post-tag selectable-tag">' + paramName + '</small></a>';
		}
	}
	function showSearchParametersBox()
	{
		var resultsBox           = $O('post-search-quick-results');
		var resultsBoxDimensions = size(resultsBox);
		var resultsBoxHeight     = parseInt(resultsBoxDimensions.height) || parseInt(getStyleValue(resultsBox, 'height'));
		$('#post-search-parameters').css( 'top', (resultsBoxHeight + 40) + 'px');
		$('#post-search-parameters').css('display', 'block');
	}
	function hideSearchParametersBox()
	{
		$('#post-search-parameters').css('display', 'none');
	}
	function getSearchParameter(paramName)
	{
		switch( paramName.toLowerCase() )
		{
			case 'keywords'   : return $('#post-search-keyword').val();
			case 'authors'    : return $('#post-search-filter-authors').tagEditor('getTags')[0].tags;
			case 'forums'     : return $('#post-search-filter-forums').tagEditor('getTags')[0].tags;
			case 'categories' : return $('#post-search-filter-categories').tagEditor('getTags')[0].tags;
			case 'tags'       : return $('#post-search-filter-tags').tagEditor('getTags')[0].tags;
		}
	}
	function runSearch(focusedElement)
	{
		focusedElement = getElementAsDOM(focusedElement);
		setAsProcessing(focusedElement);
		setAsProcessing('primary-post-search-btn');
		setAsProcessing('secondary-post-search-btn');
		setAsProcessing('post-search-parameters-header');
		searchPosts(function(posts){
			displaySuggestedPosts(posts);
			unsetAsProcessing('post-search-parameters-header');
			unsetAsProcessing('secondary-post-search-btn');
			unsetAsProcessing('primary-post-search-btn');
			unsetAsProcessing(focusedElement);
		});
		
		function getElementAsDOM(element)
		{
			if(typeof element === 'string')
			{
				return element; //element is an HTML element ID string
			}
			if(typeof element.className != 'undefined')
			{
				return element; //element is normal DOM element, returned either by getElementById or getElementsByClassname
			}
			else
			{
				console.log(element);
				return element[0]; //element is probably a jquery object, so get the DOM element from it
			}
		}
	}
	function displaySuggestedPosts(posts)
	{
		var postsStr = '';
		
		for(var i = 0, len=posts.length; i < len; i++)
		{
			var currPost = posts[i];
			postsStr += assembleHTML(currPost);
		}

		var display = (postsStr == '') ? 'none' : 'block';
			
		$('#post-search-quick-results').html(postsStr);
		$('#post-search-quick-results').css('display', display);
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
			'<div class="post-search-result" style="background:#fff; padding:5px 8px; margin-bottom:2px;">',
				'<h5 style="font-weight:700; margin-top:3px; margin-bottom:2px;"><a href="' + post.url + '" title="' + post.title + '" class="post-title">' + post.title + '</a></h5>',
				'<a style="float:left; margin-right:7px;" href="' + post.url + '"><img style="width:105px; height:105px; "src="' + post.imageURL + '"></a>',
				'<span class="post-search-result-item">Author: <a class="post-author" href="' + author.url + '">' + author.username + '</a></span><br>',
				'<span class="post-search-result-item" title="' + post.fDateCreated + '">Created: ' + post.dateCreated + ' ago</span><br>',
				'<span class="post-search-result-item" title="View posts filed under ' + post.forum.name + ' forum">Forum: <a href="'  + post.forum.url    + '">' + post.forum.name    + '</a></span><br>',
				'<span class="post-search-result-item" title="View posts filed under ' + post.category.name + ' category">Category : <a href="' + post.category.url + '">' + post.category.name + '</a></span><br>',
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
	}
	function searchPosts(callback)
	{
		var statusMsgFieldID = 'post-search-status-message';
		
		var keywords   = getSearchParameter('keywords');
		var authors    = getSearchParameter('authors');
		var forums     = getSearchParameter('forums');
		var categories = getSearchParameter('categories');
		var tags       = getSearchParameter('tags');
		/*
		var keywords   = $('#post-search-keyword').val();;
		var authors    = $('#post-search-filter-authors').tagEditor('getTags')[0].tags; //.val();
		var forums     = $('#post-search-filter-forums').tagEditor('getTags')[0].tags; //.val();
		var categories = $('#post-search-filter-categories').tagEditor('getTags')[0].tags; //.val();
		var tags       = $('#post-search-filter-tags').tagEditor('getTags')[0].tags; //.val();
		*/
		
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
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'Post search filter attempt status : ' + status + '\r\nsuccess : ' + data );
				}
				
				data = JSON.parse(data);
				setSearchParametersBoxValues();
				callback(data);
				
				if(data.length > 0)
				{
					showSearchParametersBox();
				}
				else
				{
					hideSearchParametersBox();
				}
				
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