<?php
//$page_instance->add_header('post-view', array(
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
<?php $user_is_logged_in = ($current_user != null); ?>
<?php $page_instance->add_nav('secondary-navigation'); ?>
<?php 
/*

<a id="question-vote-up-btn" class="cursor-pointer vote-up-<?php echo $vote_up_state; ?>"
 <?php if(!UserModel::user_is_logged_in()): ?>
 <?php $unauthenticated_user_vote_msg = "You must be <a href=\'$login_prompt_url\'><span class=\'white\' style=\'text-decoration:underline;\'>logged in</span></a> to perform this operation"; ?>
  onclick="notify('<?php echo $unauthenticated_user_vote_msg; ?>'); return false;"
 <?php endif; ?>>
</a>

*/
?>
<?php $user_is_logged_in = UserModel::user_is_logged_in(); ?>
<?php $post_short_url  = get_post_short_url($post_id); ?>
<?php $post_title      = $post->get('title'); ?>
<?php $post_content    = $post->get('content'); ?>
<?php $icons_url       = $theme_url. '/images/social-icons'; ?>
<?php $post_author     = UserModel::get_user_instance( $post->get('author_id') ); ?>
<?php $post_date_added = $post->get('date_created'); ?>
<?php $default_img_url = get_app_setting('default-user-image-url'); ?>
<div class="container main-container">
 <div class="col-lg-3 hidden-xs" style="border:none;1px solid #ccc; padding-left:0">
  <?php if( get_app_setting('show-post-forum-field', true) ): $page_instance->add_sidebar('forums'); endif; ?>
  <?php if( get_app_setting('show-post-category-field', true) ): $page_instance->add_sidebar('categories'); endif; ?>
  <?php $page_instance->add_sidebar('popular-links'); ?>
 </div>
 <div id="post-stream" class="col-lg-9">
 <div class="row">
  <div class="col-lg-12">
   <h1 id="post-title" ><a href="<?php echo get_post_url($post_id); ?>" title="<?php echo sanitize_html_attribute($post_title); ?>"><?php echo $post_title; ?></a></h1>
  </div>
  <div class="col-lg-12">
   <ul class="topic-meta">
    <li class="text-center">
	 <span class="topic-meta-key"><small><i class="fa fa-edit"></i>&nbsp;</small>Created</span><br>
	 <img id="topic-author-image" class="user-card-trigge" src="<?php echo get_user_image_url($post_author->get('id')); ?>" 
	  data-url="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>"
	  data-username="<?php echo sanitize_html_attribute($post_author->get('username')); ?>"
	  data-user-image="<?php echo sanitize_html_attribute(get_user_image_url($post_author->get('id'))); ?>"
	  data-last-seen="<?php echo sanitize_html_attribute(get_time_elapsed_intelligent(format_date(format_time($post_author->get('last-seen-time'))))); ?>"
	  data-join-date="<?php echo sanitize_html_attribute(format_date($post_author->get('date_registered')), 'F d, Y'); ?>"
	  data-location="<?php echo sanitize_html_attribute($post_author->get('location')); ?>"
	 />
	 <!--<span class="topic-meta-value date-created" title="Saturday July 02, 2016 09:AM">June 14, 2016</span>-->
	 <span class="topic-meta-value date-created" title="<?php echo sanitize_html_attribute(format_date($post_date_added), 'F d, Y'); ?>">
	  <?php echo get_time_elapsed_intelligent(format_date($post_date_added)); ?>
	 </span>
    </li>
	<li class="text-center">
	 <span class="topic-meta-key"><small><i class="fa fa-eye"></i>&nbsp;</small>Views</span><br>
	 <span class="topic-meta-value"><?php echo format_count($post->get_view_data($count=true)); ?></span>
	</li>
	<li class="text-center">
	 <span class="topic-meta-key"><small><i class="fa fa-comments"></i>&nbsp;</small>Responses</span><br>
	 <span class="topic-meta-value"><?php echo format_count($post->get_comments($count=true)); ?></span>
	</li>
	<li class="text-center">
	 <span class="topic-meta-key"><small><i class="fa fa-users"></i>&nbsp;</small>Participants</span><br>
	 <span class="topic-meta-value"><?php echo count( $post->get_participants() ); ?></span>
	</li>
	<?php /* 
	<li class="text-center">
	 <span class="topic-meta-key"><small><i class="fa fa-heart"></i>&nbsp;</small>likes</span><br>
	 <span class="topic-meta-value">100</span>
	</li>
	*/?>
   </ul>
  </div>
 </div>  

 <article>
  <div class="row">
   <div class="col-lg-2 text-center">
    <a href="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>">
	 <img class="post-author-image user-card-trigger" src="<?php echo sanitize_html_attribute( get_user_image_url($post_author->get('id')) ); ?>" 
	  data-url="<?php echo sanitize_html_attribute(get_user_profile_url($post_author->get('id'))); ?>"
	  data-username="<?php echo sanitize_html_attribute($post_author->get('username')); ?>"
	  data-user-image="<?php echo sanitize_html_attribute(get_user_image_url($post_author->get('id'))); ?>"
	  data-last-seen="<?php echo sanitize_html_attribute(get_time_elapsed_intelligent(format_date(format_time($post_author->get('last-seen-time'))))); ?>"
	  data-join-date="<?php echo sanitize_html_attribute(format_date($post_author->get('date_registered'), 'F d, Y')); ?>"
	  data-location="<?php echo sanitize_html_attribute($post_author->get('location')); ?>"
	 />
	 <p class="post-author"><?php echo $post_author->get('username'); ?></p>
	</a>
   </div>
   <div class="col-lg-10">
	<div class="post-content">
	 <?prettify?>
	 <?php echo $post_content; ?>
	 <div class="clear"></div>
	 <div style="margin:5px; 0">
		  <?php $ptags = $post->get_tags(); ?>
		  <?php for($i=0, $len=count($ptags); $i < $len; $i++): ?>
		  <?php $tag = TagModel::get_tag_instance($ptags[$i]); ?>
		  <?php $tag_url = generate_url(array('controller'=>'posts', 'action'=>'tagged', 'qs'=>array($tag->get('name')))); ?>
		  <a href="<?php echo sanitize_html_attribute($tag_url); ?>" title="view posts tagged <?php echo sanitize_html_attribute($tag->get('name')); ?>">
		   <small class="float-left post-tag selectable-tag"><?php echo $tag->get('name'); ?></small>
		  </a>
		  <?php endfor; ?>
	 </div>
	 <div class="clear"></div>
	 <div class="share-icons">
	    <a class="share-icon" title="share on Facebook" onclick="shareOnFb('<?php echo $post_short_url; ?>');"><img src="<?php echo $icons_url; ?>/fb-icon.png" alt="share on facebook" /></a>
	    <a class="share-icon" title="share on Google+" onclick="shareOnGPlus('<?php echo urlencode($post_short_url); ?>')"><img src="<?php echo $icons_url; ?>/gplus-icon.png" alt="share on google-plus" /></a>
	    <a class="share-icon" title="share on LinkedIn" onclick="shareOnLinkedIn('<?php echo urlencode($post_short_url); ?>', '<?php echo urlencode($post_title); ?>', '<?php echo urlencode(get_substring($post_content)); ?>')"><img src="<?php echo $icons_url; ?>/linked-in-icon.png" alt="share on linked-in" /></a>
		<a class="share-icon" title="share on Twitter" href="https://twitter.com/intent/tweet?text=<?php echo urlencode($post_title); ?>&url=<?php echo urlencode($post_short_url); ?>"><img src="<?php echo $theme_url; ?>/images/social-icons/twitter-icon.png" alt="share on twitter" /></a>
	    <a class="share-icon" title="permalink to this post" onclick="slideToggle('post-permalink')" style="top:10px;"><i class="fa fa-icon fa-2x glyphicon glyphicon-link"></i></a>
	    <div id="post-permalink" class="post-permalink" style="display:none;"><?php echo $post_short_url; ?></div>
	 </div>
	 <a id="<?php echo $user_is_logged_in ? 'post-reply-form-opener' : ''; ?>" class="<?php echo $user_is_logged_in ? 'post-editor-opener' : 'user-auth-btn'; ?> reply-link cursor-pointer" data-parent-id="<?php echo sanitize_html_attribute($post_id); ?>" title="reply">
	  <i class="fa fa-icon fa-reply" data-parent-id="<?php echo sanitize_html_attribute($post_id); ?>"></i>
	 </a>
	</div> 
   </div>	
  </div>
 </article>
 
 
 <div id="post-comments-container">
 
 <?php $comments = $post->get_comments($count=false); ?>
 <?php foreach($comments AS $comment_id): ?>
  <?php $comment = PostModel::get_post_instance($comment_id); ?>
  <a id="post-response-<?php echo $comment_id; ?>" name="post-response-<?php echo $comment_id; ?>"></a>
  <article>
  <div class="row">
   <?php $comment_author = UserModel::get_user_instance( $comment->get('author_id') ); ?>
   <div class="col-lg-2 text-center">
    <a href="<?php echo sanitize_html_attribute(get_user_profile_url($comment_author->get('id'))); ?>">
	 <img class="post-author-image user-card-trigger" src="<?php echo sanitize_html_attribute( get_user_image_url($comment_author->get('id')) ); ?>" 
	  data-url="<?php echo sanitize_html_attribute(get_user_profile_url($comment_author->get('id'))); ?>"
	  data-username="<?php echo sanitize_html_attribute($comment_author->get('username')); ?>"
	  data-user-image="<?php echo sanitize_html_attribute(get_user_image_url($comment_author->get('id'))); ?>"
	  data-last-seen="<?php echo sanitize_html_attribute(get_time_elapsed_intelligent(format_date(format_time($comment_author->get('last-seen-time'))))); ?>"
	  data-join-date="<?php echo sanitize_html_attribute(format_date($comment_author->get('date_registered'), 'F d, Y')); ?>"
	  data-location="<?php echo sanitize_html_attribute($comment_author->get('location')); ?>"
	 />
	 <p class="post-author"><?php echo $comment_author->get('username'); ?></p>
	</a>
   </div>
   <div class="col-lg-10">
	<div class="post-content">
	 <?prettify?>
	 <?php echo $comment->get('content'); ?>
	 <?php $comment_short_url = get_post_short_url($comment_id); ?>
	 <div class="share-icons">
	    <a class="share-icon" title="share on Facebook" onclick="shareOnFb('<?php echo $comment_short_url; ?>');"><img src="<?php echo $theme_url; ?>/images/social-icons/fb-icon.png" alt="share on facebook" /></a>
	    <a class="share-icon" title="share on Google+" onclick="shareOnGPlus('<?php echo urlencode($comment_short_url); ?>')"><img src="<?php echo $theme_url; ?>/images/social-icons/gplus-icon.png" alt="share on google-plus" /></a>
	    <a class="share-icon" title="share on LinkedIn" onclick="shareOnLinkedIn('<?php echo urlencode($comment_short_url); ?>', '<?php echo urlencode('Response to '. $post_title); ?>', '<?php echo urlencode(get_substring($comment->get('content'))); ?>')"><img src="<?php echo $icons_url; ?>/linked-in-icon.png" alt="share on linked-in" /></a>	    
		<a class="share-icon" title="share on Twitter" href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Response to '. $post_title); ?>&url=<?php echo urlencode($comment_short_url); ?>"><img src="<?php echo $theme_url; ?>/images/social-icons/twitter-icon.png" alt="share on twitter" /></a>
	    <a class="share-icon" title="permalink to this post" onclick="slideToggle('response-<?php echo $comment_id; ?>-permalink')" style="top:10px;"><i class="fa fa-icon fa-2x glyphicon glyphicon-link"></i></a>
	    <div id="response-<?php echo $comment_id; ?>-permalink" class="post-permalink" style="display:none;"><?php echo $comment_short_url; ?></div>
	 </div>
	 <!--<a class="<?php //echo $user_is_logged_in ? 'post-editor-opener' : 'user-auth-btn'; ?> reply-link cursor-pointer" data-parent-id="<?php //echo $comment->get('id'); ?>" title="reply"><i class="fa fa-icon fa-reply"></i></a>-->
	</div> 
   </div>	
  </div>
 </article>
 <?php endforeach; ?>
 
 </div>
 </div>

</div>

<div style="margin-top:20px;"></div>

<!--<script src="<?php //echo $site_url; ?>/js/lib/jquery-ui/jquery-ui.min.js"></script>-->

<script>
initUserCard();
function initUserCard(){
	$('.user-card-trigger').on('mouseover', function(event){

		var d = document.createElement('div');
		var evtTarget = event.target;
		var $evtTarget = $(event.target);
		
		setTimeout(function(){
			d.innerHTML = createUserCard({
				userUrl       : $evtTarget.attr('data-url'), //'http://twitter.com',
				userName      : $evtTarget.attr('data-username'), //'mikky',
				userImage     : $evtTarget.attr('data-user-image'), //'images/loveth.jpg',
				userLastSeen  : $evtTarget.attr('data-last-seen'), //'5 mins ago',
				userJoinDate  : $evtTarget.attr('data-join-date'), //'July 5, 2016'
				userLocation  : $evtTarget.attr('data-location'), //
			});
			
			$(d).hide().appendTo($evtTarget.parent()).fadeIn(); //credits: http://stackoverflow.com/a/847557
			//$evtTarget.parent().append(d);
			
			activateMouseout()
			
		}, 1);
		
		
		function activateMouseout(){
			//$(d).on('mouseout', function(){
			$('.UserCard').parent().on('mouseout', function(){
				setTimeout(function(){
					//$evtTarget.parent().remove(d);
					$(d).fadeOut(function(){$(d).remove();});
				}, 45);
			});
			
			/*
			$(document).on('click', function(e){
			if( (e.target.className.indexOf('tag-editor') == -1) && ( e.target.className.indexOf('selectable-tag') == -1 ) && ( e.target.id != 'tags-container') )
			{
				
			}*/
		}
	});
}
</script>

<script>
(function(){
	
	var commentIds = [];
	//var maxCommentId = 0;
	var parentPostId = <?php echo $post_id; ?>;
	
	<?php foreach($comments AS $comment_id): ?>
	commentIds.push(<?php echo $comment_id; ?>);
	<?php endforeach; ?>
	
	//maxCommentId = getMaxNumber(commentIds);
	//commentIds = [];
	
	function getMaxCommentId()
	{
		var maxCommentId = getMaxNumber(commentIds);
		return maxCommentId;
	}
	
	setTimeout(function getRecentComments(){
	
		$.ajax(ajaxURL, {
			method : 'GET',
			cache  : false,
			data   : { p: 'posts', 'recent-comments': true, 'parent-id': parentPostId, 'id': getMaxCommentId() },
			error : function(jqXHR, status, error){
				
			},
			success  : function(data, status, jqXHR){ 
				parsedData = JSON.parse(data);
				
				for(var i = 0; i < parsedData.length; i++)
				{
					currComment = parsedData[i];
					commentIds.push(currComment.id);
					assembleRecentComment(currComment);
				}
				
				console.log(data, 'recent comments');
				initUserCard();
			},
			complete : function(jqXHR, status)
			{
				setTimeout(getRecentComments, 1000 * 5);
			}
		})
		
		/*
			'id'             
			'content'  
			'url' 
			'shortURL'
			'author'
			'authorURL' 
			'authorImageURL'
			'authorLastSeen'
			'authorJoinDate'
			'authorLocation'
			'parentTitle'
		*/
		function assembleRecentComment(data)
		{
			var themeUrl = '<?php echo $theme_url; ?>'; 
			var responseTitle = 'Response to ' + Site.Util.escapeHtml(data.parentTitle);
			
			var str = [
			'<a id="post-response-' + data.id + '" name="post-response-' + data.id + '"></a>',
		    '<article>',
		     '<div class="row">',
		      '<div class="col-lg-2 text-center">',
			   '<a href="' + data.authorURL + '">',
			    '<img class="post-author-image user-card-trigger" src="' + data.authorImageURL + '"',
			    'data-url="' + data.authorURL + '"',
			    'data-username="' + data.author + '"',
			    'data-user-image="' + data.authorImageURL + '"',
			    'data-last-seen="' + data.authorLastSeen + '"',
			    'data-join-date="' + data.authorJoinDate + '"',
			    'data-location="' + data.authorLocation + '"',
			    '/>',
			    '<p class="post-author">' + data.author + '</p>',
			   '</a>',
		      '</div>',
		      '<div class="col-lg-10">',
			   '<div class="post-content">' + data.content + '',
			    '<div class="share-icons">',
				  '<a class="share-icon" title="share on Facebook" onclick="shareOnFb(\'' + data.shortURL  + '\');">',
				   '<img src="' + themeUrl + '/images/social-icons/fb-icon.png" alt="share on facebook" />',
				  '</a>',
				  '<a class="share-icon" title="share on Google+" onclick="shareOnGPlus(\'' + data.shortURL  + '\')">',
				   '<img src="' + themeUrl + '/images/social-icons/gplus-icon.png" alt="share on google-plus" />',
				  '</a>',
				  '<a class="share-icon" title="share on LinkedIn" ',
				    'onclick="shareOnLinkedIn(\'' + data.shortURL  + '\', \'' + responseTitle + '\', \'' + Site.Util.escapeHtml(data.content) + '\')">',
				   '<img src="' + themeUrl + '/images/social-icons/linked-in-icon.png" alt="share on linked-in" />',
				  '</a>',	    
				  '<a class="share-icon" title="share on Twitter" ',
				   'href="https://twitter.com/intent/tweet?text=' + responseTitle + '&url=' + data.shortURL + '">',
				   '<img src="' + themeUrl + '/images/social-icons/twitter-icon.png" alt="share on twitter" />',
				  '</a>',
				  '<a class="share-icon" title="permalink to this post" ',
				    'onclick="slideToggle(\'response-' + data.id + '-permalink\')" style="top:10px;">',
				   '<i class="fa fa-icon fa-2x glyphicon glyphicon-link"></i>',
				  '</a>',
				  '<div id="response-' + data.id + '-permalink" class="post-permalink" style="display:none;">' + data.shortURL + '</div>',
			     '</div>',
			    '</div>',
		       '</div>',
		      '</div>',
		     '</article>'
			].join('');
			
			$O('post-comments-container').innerHTML += str;
		}
	}, 1000);
})();
</script>

<style>
#post-editor-wrapper, 
#comment-poster-data-field { position:relative !important;  width:50% !important; margin-left:auto !important; margin-right:auto !important; }
#comment-poster-name-field, #comment-poster-email-field { display:inline-block !important; width:49.7% !important; }
#preview-window-wrapper { display:none; }
#editor-window-wrapper { width:100% !important; }
#post-create-btn { position: relative;  bottom: 15px; left:312px; z-index:999; /*right: 225px;*/ }
#status-message { position:relative; right:327px; bottom:10px; }

@media screen and (max-width: 767px){
	#post-editor-wrapper, #comment-poster-data-field { width:100% !important; }
	#comment-poster-name-field, #comment-poster-email-field { display:block !important; width:98% !important; margin-left:auto; margin-right:auto;}
	#comment-poster-name-field { margin-bottom:5px; }
	#editor-collapser { }
	#post-create-btn { position:relative; left:0; right:15px; }
	#status-message { position:relative; right:15px; bottom:10px; }
}
@media screen and (max-width: 500px){
	
}
@media screen and (max-width: 450px){
	
}
</style>

<!--<script>
$(document).ready(function(){
	/*
	if( typeof document.getElementById('post-reply-form-opener') !== 'undefined' ){
		$('#post-reply-form-opener').trigger('click');
	}
	*/
	showPostEditor(<?php echo $post_id; ?>);
});
</script>-->

<?php if(!$user_is_logged_in): ?>
<div id="comment-poster-data-field" class="form-group" style="margin-bottom:10px;">
 <input id="comment-poster-name-field" class="form-control" type="text" placeholder="Enter your name"/>
 <input id="comment-poster-email-field" class="form-control" type="email" placeholder="Enter your email (required)" />
 <script>
  var onBeforeCommentSubmit = function() {
	var name  = $('#comment-poster-name-field').val();
	var email = $('#comment-poster-email-field').val();
	
	if( !Site.Util.isValidEmail(email) ) { 
		return { 'error':true, 'message': 'Login or enter your email' }
	}
	
	return { 'commenter-name': name, 'commenter-email': email }
  }
 </script>
</div>
<?php endif; ?>
<?php 
get_post_editor( array(
   'parent_post_id'   =>  $post_id, 
   'placeholder'      => 'Enter Your comment', 
   'value'            => '', 
   'auto_display'     => true, 
   'on_before_submit' => !$user_is_logged_in ? 'onBeforeCommentSubmit' : 'function(){ return true; }'
) ); 
?>

<?php //$page_instance->add_footer('post-view'); ?>
<?php $page_instance->close_page( array('display_post_editor'=>true, 'parent_post_id'=>$post_id, 'header_title'=>'Add comment') ); ?>