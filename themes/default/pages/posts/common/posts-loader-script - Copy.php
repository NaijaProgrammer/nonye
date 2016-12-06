<script>
(function (getPostHTML, postsContainer){
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
			
			//var postsContainer = $('#topics-list table tbody');
			//console.log(oldPostsStr);
			postsContainer.html( postsContainer.html() + oldPostsStr);
		}
	}
	function assemblePostHTML(post)
	{
		return getPostHTML(post);
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
})(createPostHTML, <?php echo $posts_container; ?>);
</script>