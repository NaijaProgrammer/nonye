function createGridViewHTML(post)
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

function createListViewHTML(post)
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

/*
* viewType string list|grid
* operation string append|prepend|overwrite
*/
function displayPosts(posts, viewType, operation)
{
	var postsStr  = '';
	var container = getPostsContainer(viewType);
	 
	for(var i = 0, len=posts.length; i < len; i++)
	{
		var currPost = posts[i];
		postsStr += assemblePostHTML(currPost, viewType);
	}

	switch(operation)
	{
		case 'append'    : container.html( container.html() + postsStr ); break;
		case 'prepend'   : container.html( postsStr + container.html() ); break;
		case 'overwrite' :
		default          : container.html( postsStr ); break;
	}
	
	function assemblePostHTML(post, viewType)
	{
		return viewType == 'list' ? createListViewHTML(post) : createGridViewHTML(post);
	}
	function getPostsContainer(viewType)
	{
		return viewType == 'list' ? $('#topics-list table tbody') : $('#posts-grid-container');
	}
}
