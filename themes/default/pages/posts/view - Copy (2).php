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
<?php $post_date_added = $post->get('date_added'); ?>
<?php $default_img_url = get_app_setting('default-user-image-url'); ?>
<div class="container main-container">
 <div class="col-lg-3 hidden-xs" style="border:none;1px solid #ccc; padding-left:0">
  <?php $page_instance->add_sidebar('forums'); ?>
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
	 <a class="<?php echo $user_is_logged_in ? 'post-editor-opener' : 'user-auth-btn'; ?> reply-link cursor-pointer" data-parent-id="<?php echo sanitize_html_attribute($post_id); ?>" title="reply">
	  <i class="fa fa-icon fa-reply" data-parent-id="<?php echo sanitize_html_attribute($post_id); ?>"></i>
	 </a>
	</div> 
   </div>	
  </div>
 </article>
 
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
	  data-location="<?php echo sanitize_html_attribute($post_author->get('location')); ?>"
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

<script src="<?php echo $site_url; ?>/js/lib/jquery-ui/jquery-ui.min.js"></script>
<script>
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
</script>

<?php //$page_instance->add_footer('post-view'); ?>
<?php $page_instance->close_page(); ?>