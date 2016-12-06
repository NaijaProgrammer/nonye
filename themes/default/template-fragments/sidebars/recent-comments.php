<?php $rc_id = PostModel::get_reply_posts(array(), array('date_added'=>'DESC'), $limit = 1 ); ?>
<?php $rc_id = ( is_array($rc_id) && !empty($rc_id) ) ? $rc_id[0] : 0; ?>
<?php $rc    = ($rc_id) ? PostModel::get_post_instance($rc_id) : null; ?>
<?php if(is_object($rc)) : ?>
<?php $rc_author = UserModel::get_user_instance( $rc->get('author_id') ); ?>
<?php $rc_parent = PostModel::get_post_instance( get_top_level_parent_post($rc_id) ); ?>
<div class="panel panel-default">
 <div class="panel-heading-fd"><h3 class="panel-title-fd">Recent comments</h3></div>
 <div class="panel-body">
  <div class="media">
   <span id="comment-author-data">
   <a class="pull-left" href="<?php echo sanitize_html_attribute(get_user_profile_url($rc_author->get('id'))); ?>">
    <img class="media-object" style="width:64px; height:64px; margin-right:10px;" title="<?php echo $rc_author->get('username'); ?>" src="<?php echo $rc_author->get('image-url', get_app_setting('default-user-image-url')); ?>" />
   </a>
   </span>
   <div class="media-body">
    <span id="comment-body-data">
    <!--<h4 class="media-heading"><a href="https://www.facebook.com/FadselTechnologies"><?php //echo $rc_parent->get('title'); ?></a></h4>-->
    <a style="color:#9d9d9d; text-decoration:none;" title="reply to <?php echo sanitize_html_attribute($rc_parent->get('title')); ?>" href="<?php echo sanitize_html_attribute(get_post_url($rc_id)); ?>">
	 <?php echo $rc->get('content'); ?>
	</a>
	</span>
   </div>
  </div>         
 </div>
</div>
<?php endif; ?>
<script>
setTimeout(function getRecentComment(){
	$.ajax(ajaxURL, {
		method : 'GET',
		cache  : false,
		data   : { p : 'posts', 'most-recent-comment' : true },
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Most recent comment status : ' + status + '\r\nerror : ' + error );
			}
		},
		success  : function(data, status, jqXHR){ 
			if(isDevServer)
			{
				console.log( 'Most recent comment status : ' + status + '\r\nsuccess : ' + data );
			} 
			assembleRecentComment(JSON.parse(data));
		},
		complete : function(jqXHR, status)
		{
			setTimeout(getRecentComment, 1000 * 5);
		}
	})
	
	/*
		'id'             
		'content'  
		'url' 
		'author'
		'authorURL' 
		'authorImageURL'
		'parentTitle'
	*/
	function assembleRecentComment(data)
	{
		$('#comment-author-data').html('');
		$('#comment-body-data').html('');
		
		var authorDataStr = '<a class="pull-left" href="' + data.authorURL + '">' + 
		 '<img class="media-object" style="width:64px; height:64px; margin-right:10px;" title="' + data.author + '" src="' + data.authorImageURL + '" />' +
		'</a>';
		
		var commentDataStr = '<a style="color:#9d9d9d; text-decoration:none;" title="reply to ' + data.parentTitle + '" href="' + data.url + '">' + 
		data.content +
		'</a>';
		
		$('#comment-author-data').html(authorDataStr);
		$('#comment-body-data').html(commentDataStr);
	}
}, 1000);
</script>