<?php
include('request-validator.php');
$response_data = array();

if(isset($_POST['create']))
{  
	$author_id   = $current_user_id; //$_POST['creator_id'];
	$title       = $_POST['title'];
	$excerpt     = isset($_POST['excerpt']) ? $_POST['excerpt'] : '';
	$content     = $_POST['content'];
	$status      = $_POST['status'];
	$forum_id    = $_POST['forum'];
	$category_id = $_POST['category'];
	$tags        = json_decode(trim($_POST['tags']));
	$featured_image_url = isset($_POST['featured-image-url']) ? trim($_POST['featured-image-url']) : '';
	
	$require_post_title    = get_app_setting('require-post-title-field', true);
	$require_post_forum    = get_app_setting('require-post-forum-field', true);
	$require_post_category = get_app_setting('require-post-category-field', true);
	$require_post_body     = get_app_setting('require-post-body-field', true);
	$min_post_tags         = get_app_setting('minimum-post-tags');
	
	$validate = Validator::validate(array(
		array('error_condition'=>!$user_is_logged_in, 'error_message'=>'Login to create a new post', 'error_type'=>'unauthenticatedUser'),
		array('error_condition'=>$require_post_title && empty($title), 'error_message'=>'Enter a title for your post', 'error_type'=>'emptyTitle'),
		array('error_condition'=>$require_post_forum && empty($forum_id), 'error_message'=>'Choose a forum to post to', 'error_type'=>'emptyForum'),
		array('error_condition'=>$require_post_category && empty($category_id), 'error_message'=>'Select your post category', 'error_type'=>'emptyCategory'),
		array('error_condition'=>empty($status), 'error_message'=>'Please choose a status for the post', 'error_type'=>'emptyPostStatus'),
		array('error_condition'=>$require_post_body && empty($content), 'error_message'=>'Enter the content of your post', 'error_type'=>'emptyContent'),
		array('error_condition'=>count($tags) < $min_post_tags, 'error_message'=>'Add at least '. count($tags). ' tag(s) to your post', 'error_type'=>'tagCountIncomplete')
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	else {
		import_admin_functions();
		for($i = 0, $len = count($tags); $i < $len; $i++) {
			$tag_name = $tags[$i];
			
			if( !tag_exists($tag_name) ) {
				if( user_can('Create Tags') ) {
					TagModel::create( array('creator_id'=>$current_user_id, 'name'=>strtolower($tag_name), 'description'=>'') );
				}
				else {
					unset($tags[$i]);
				}
			}
		}
		
		$post = PostModel::create(array(
			'title'       => $title,
			'content'     => $content,
			'author_id'   => $author_id,
			'forum_id'    => $forum_id,
			'category_id' => $category_id,
			'tags'        => $tags,
			'status'      => $status
		));
		
		if( is_object($post) ){
			
			if( $status == 'published' ) {
				$post->set_publish_date();
			}
			
			if( !empty($featured_image_url) ) {
				$post->update(array(
				    'featured-image-url' => array('value'=>$featured_image_url, 'overwrite'=>true)
				));
			}
			
			$post_id     = $post->get('id');
			$activity_id =  ActivityManager::create_activity(array(
				'object_id'      => $post_id,
				'object_type'    => 'post',
				'subject_id'     => $post->get('author_id'), 
				'subject_action' => 'create',
				'description'    => ''
			));
			
			create_short_url($post_id);
			$response_data = array('success'=>true, 'postID'=>$post->get('id'));
		}
		else {
			$response_data = array('error'=>true, 'message'=>'An unexpected error occurred', 'errorType'=>'internalSystemError');
		}
	}
}
else if(isset($_POST['update']))
{ 
    import_admin_functions();
	
	$post_id     = trim( $_POST['id'] );
	$title       = trim( $_POST['title'] );
	$excerpt     = isset($_POST['excerpt']) ? trim( $_POST['excerpt'] ) : '';
	$content     = trim( $_POST['content'] );
	$status      = trim( $_POST['status'] );
	$featured_image_url = isset($_POST['featured-image-url']) ? trim($_POST['featured-image-url']) : '';
	
	$post         = !empty($post_id) ? PostModel::get_post_instance($post_id) : null;
	$author_id    = !empty($post) ? $post->get('author_id') : 0;
	$require_post_title = get_app_setting('require-post-title-field', true);
	$require_post_body  = get_app_setting('require-post-body-field', true);
	
	$validate = Validator::validate(array(
		array('error_condition'=>!$user_is_logged_in, 'error_message'=>'Login to edit a post', 'error_type'=>'unauthenticatedUser'),
		array('error_condition'=>( !is_super_admin() && ($author_id != $current_user_id) ), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
		array('error_condition'=>empty($post_id), 'error_message'=>'Please specify a post to edit', 'error_type'=>'noPostSelected'),
		array('error_condition'=>$require_post_title && empty($title), 'error_message'=>'Enter a title for your post', 'error_type'=>'emptyTitle'),
		array('error_condition'=>empty($status), 'error_message'=>'Please choose a status for the post', 'error_type'=>'emptyPostStatus'),
		array('error_condition'=>$require_post_body && empty($content), 'error_message'=>'Enter the content of your post', 'error_type'=>'emptyContent'),
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	else {

		if( is_object($post) ){
			
	        $prev_title   = !empty($post) ? $post->get('title') : '';
			$prev_status  = !empty($post) ? $post->get('status') : '';
	        $prev_content = !empty($post) ? $post->get('content') : '';
	        $prev_excerpt = !empty($post) ? $post->get('excerpt') : '';
			
			$post->update(array(
		        'title'   => $title,
			    'content' => $content,
		        'status'  => $status,
			    'excerpt' => $excerpt
		    ));
			
			if( ($prev_status != 'published') && ($status == 'published') ) {
				$post->set_publish_date();
			}
			
			if( !empty($featured_image_url) ) {
				$post->update(array(
				    'featured-image-url' => array('value'=>$featured_image_url, 'overwrite'=>true)
				));
			}
			
			$post->update( array(
			    'last-modified' => array('value'=>time(), 'overwrite'=>true),
				'modified-by'   => array('value'=>$current_user_id, 'overwrite'=>true)
			) );
			
			$revision = new PostRevision($post_id);
			$revision->update(array(
			    'date-modified' => array('value'=>time(), 'overwrite'=>false),
				'editor-id'     => array('value'=>$current_user_id, 'overwrite'=>false),
				'prev-title'    => array('value'=>$prev_title, 'overwrite'=>false),
				'new-title'     => array('value'=>$title, 'overwrite'=>false),
				'prev-status'   => array('value'=>$prev_status, 'overwrite'=>false),
				'new-status'    => array('value'=>$status, 'overwrite'=>false),
				'prev-content'  => array('value'=>$prev_content, 'overwrite'=>false),
				'new-content'   => array('value'=>$content, 'overwrite'=>false),
				'prev-excerpt'  => array('value'=>$prev_excerpt, 'overwrite'=>false),
				'new-excerpt'   => array('value'=>$excerpt, 'overwrite'=>false)
			));
			
			$activity_id =  ActivityManager::create_activity(array(
				'object_id'      => $post_id,
				'object_type'    => 'post',
				'subject_id'     => $post->get('author_id'), 
				'subject_action' => 'update',
				'description'    => ''
			));
			
			delete_short_url($post_id);
			create_short_url($post_id);
			$response_data = array('success'=>true, 'postID'=>$post_id);
		}
		else {
			$response_data = array('error'=>true, 'message'=>'An unexpected error occurred', 'errorType'=>'internalSystemError');
		}
	}
}

else if(isset($_POST['reply']))
{
	/*
		$_POST members:
		parent_id
		content
		creator_id
	*/
	$parent_id    = $_POST['parent_id'];
	$author_id    = $current_user_id; //$_POST['creator_id'];
	$author_name  = isset($_POST['commenter-name']) ? trim($_POST['commenter-name']) : '';
	$author_email = isset($_POST['commenter-email']) ? trim($_POST['commenter-email']) : '';
	$content      = $_POST['content'];
	
	$email_field_was_sent = isset($_POST['commenter-email']);
	$email_field_is_valid = ( $email_field_was_sent && is_valid_email($author_email) );
	
	$validate = Validator::validate(array(
	    array('error_condition'=>(!$user_is_logged_in && !$email_field_was_sent), 'error_message'=>'Login to reply to this post', 'error_type'=>'unauthenticatedUser'),
		array('error_condition'=>(!$user_is_logged_in && !$email_field_is_valid), 'error_message'=>'Please enter your email or Login to reply to this post', 'error_type'=>'emailFieldEmpty'),
		array('error_condition'=>empty($content), 'error_message'=>'Enter the content of your post', 'error_type'=>'emptyContent'),
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	else {
		
		if( email_exists($author_email) ) {
			$author_id = get_user_id($author_email);
		}
		else {
			
			$fullname  = $author_name;
		    $name_data = explode(' ', $fullname);
		    $firstname = !empty($name_data[0]) ? trim($name_data[0]) : '';
		    $lastname  = !empty($name_data[1]) ? trim($name_data[1]) : '';
			
			$registrant_data = array( 
			    'firstname' => $firstname,
				'lastname'  => $lastname,
			    'email'     => $author_email, 
				'password'  => generate_random_string('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz123456789', 9) 
			);
			
			$registration_data = UserAuth::register_user( $registrant_data, $send_success_mail = false );
			
			if( is_array($registration_data) && isset($registration_data['success']) ) {
				$author_id = $registration_data['userID'];
				update_user_last_seen_data( $author_id, get_post_url($parent_id) );
			}
		}
		
		$post = PostModel::create(array(
			'title'     => '',
			'parent_id' => $parent_id,
			'content'   => $content,
			'author_id' => $author_id,
		));
		
		if( is_object($post) ) {
			$post_id     = $post->get('id');
			$activity_id =  ActivityManager::create_activity(array(
				'object_id'      => $post_id,
				'object_type'    => 'post',
				'subject_id'     => $post->get('author_id'), 
				'subject_action' => 'create',
				'description'    => ''
			));
			
			if($activity_id) {
				publish_notification($activity_id);
			}
			
			create_short_url($post_id);
			$response_data = array('success'=>true, 'postID'=>$post->get('id'));
		}
		else {
			$response_data = array('error'=>true, 'message'=>'An unexpected error occurred', 'errorType'=>'internalSystemError');
		}
	}
}
else if(isset($_GET['get-posts']))
{  
	$tp           = TABLES_PREFIX;
	$dbh          = Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME)->get_connection();
	$post_ids     = array();
	$last_post_id = $_GET['id'];
	$comparator   = ( isset($_GET['get-posts']) && $_GET['get-posts'] == 'older' ) ? '<' : '>';
	
	if(isset($_GET['forum']))
	{
		$sql      = "SELECT `post_id` FROM {$tp}forum_posts WHERE `forum_id` = ? AND `post_id` $comparator ?";
		$stmt     = $dbh->prepare( $sql );
		$forum_id = get_forum_id($_GET['forum']);
		$stmt->bind_param( 'ii', $forum_id, $last_post_id );
	}
	
	else if(isset($_GET['category']))
	{
		$sql         = "SELECT `post_id` FROM {$tp}category_posts WHERE `category_id` = ? AND `post_id` $comparator ?";
		$stmt        = $dbh->prepare( $sql );
		$category_id = get_category_id($_GET['category']);
		$stmt->bind_param( 'ii', $category_id, $last_post_id );
	}
	
	else if(isset($_GET['tagged']))
	{
		$sql    = "SELECT `post_id` FROM {$tp}tag_posts WHERE `tag_id` = ? AND `post_id` $comparator ?";
		$stmt   = $dbh->prepare( $sql );
		$tag_id = get_tag_id($_GET['tagged']);
		$stmt->bind_param( 'ii', $tag_id, $last_post_id );
	}
	
	else
	{
		$sql  = "SELECT `id` FROM {$tp}posts WHERE `parent_id` = 0 AND `id` $comparator ?";
		$sql .= isset($_GET['author']) ? " AND `author_id` = ?" : "";
		$stmt = $dbh->prepare( $sql );
		
		if(isset($_GET['author']))
		{
			$author_id = get_user_id($_GET['author']);
			$stmt->bind_param( 'ii', $last_post_id, $author_id );
		}
		else
		{
			$stmt->bind_param( 'i', $last_post_id );
		}
	}
	
	$stmt->execute();	
	$stmt->bind_result($post_id);
	
	while ($stmt->fetch())
	{
		//we can't use this because we'd get mysql "Commands out of sync; you can't run this command now" error.
		//because "The MySQL client does not allow you to execute a new query where there are still rows to be fetched from an in-progress query.(http://stackoverflow.com/a/3632320/1743192)"
		//see also : http://dev.mysql.com/doc/refman/5.7/en/commands-out-of-sync.html
		//$response_data[] = get_post_data($post_id); 
		
		//As a fix, I had to first let the $stmt->fetch error finish executing, while storing the result in $post_ids array.
		//Afterwards, I loop through the array and for each ID, get the post data
		$post_ids[] = $post_id;
	}
	
	foreach($post_ids AS $post_id)
	{
		$response_data[] = get_post_data($post_id);
	}
}

//Get the most recent comment on the site
else if(isset($_GET['most-recent-comment']))
{
	$rc_id = PostModel::get_reply_posts(array(), array('date_created'=>'DESC'), $limit = 1 );
	$rc_id = ( is_array($rc_id) && !empty($rc_id) ) ? $rc_id[0] : 0;
	$rc    = ($rc_id) ? PostModel::get_post_instance($rc_id) : null;
	
	if(is_object($rc))
	{
		$rc_author = UserModel::get_user_instance( $rc->get('author_id') );
		$rc_parent = PostModel::get_post_instance( get_top_level_parent_post($rc_id) );
		$date_created = $rc->get('date_created');
		
		$response_data = array(
			'id'             => $rc_id,
			'content'        => $rc->get('content'),
			'url'            => get_post_url($rc_id),
			'creationDate'   => format_date($date_created),
			'elapsedTime'    => get_time_elapsed_intelligent(format_date($date_created)),
			'author'         => $rc_author->get('username'),
			'authorURL'      => get_user_profile_url($rc_author->get('id')),
			'authorImageURL' => $rc_author->get('image-url', get_app_setting('default-user-image-url')),
			'parentTitle'    => $rc_parent->get('title'),
		);
		//var_dump($response_data);
	}
}

//Get recent comments for specified post
else if(isset($_GET['recent-comments']))
{
	$tp             = TABLES_PREFIX;
	$dbh            = Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME)->get_connection();
	$post_ids       = array();
	$last_post_id   = $_GET['id'];
	$parent_post_id = isset( $_GET['parent-id'] ) ? $_GET['parent-id'] : 0;
	
	$sql  = "SELECT `id` FROM {$tp}posts WHERE `parent_id` = ? AND `id` > ?";
	$sql .= isset($_GET['author']) ? " AND `author_id` = ?" : "";
	$sql .= " ORDER BY `id` DESC";
	$stmt = $dbh->prepare( $sql );
	
	if(isset($_GET['author']))
	{
		$author_id = get_user_id($_GET['author']);
		$stmt->bind_param( 'iii', $parent_post_id, $last_post_id, $author_id );
	}
	else
	{
		$stmt->bind_param( 'ii', $parent_post_id, $last_post_id );
	}
	
	$stmt->execute();	
	$stmt->bind_result($post_id);
	
	while ($stmt->fetch())
	{
		//we can't use this because we'd get mysql "Commands out of sync; you can't run this command now" error.
		//because "The MySQL client does not allow you to execute a new query where there are still rows to be fetched from an in-progress query.(http://stackoverflow.com/a/3632320/1743192)"
		//see also : http://dev.mysql.com/doc/refman/5.7/en/commands-out-of-sync.html
		//$response_data[] = get_post_data($post_id); 
		
		//As a fix, I had to first let the $stmt->fetch error finish executing, while storing the result in $post_ids array.
		//Afterwards, I loop through the array and for each ID, get the post data
		$post_ids[] = $post_id;
	}
	
	foreach($post_ids AS $post_id)
	{
		$comment_id = $post_id;
		$comment = PostModel::get_post_instance($comment_id);
		
		if(is_object($comment))
		{
			$comment_author = UserModel::get_user_instance( $comment->get('author_id') );
			$comment_parent = PostModel::get_post_instance( get_top_level_parent_post($comment_id) );
			$date_created = $comment->get('date_created');
			
			$response_data[] = array(
				'id'             => $comment_id,
				'content'        => $comment->get('content'),
				'url'            => get_post_url($comment_id),
				'creationDate'   => format_date($date_created),
			    'elapsedTime'    => get_time_elapsed_intelligent(format_date($date_created)),
				'shortURL'       => get_post_short_url($comment_id),
				'author'         => $comment_author->get('username'),
				'authorURL'      => get_user_profile_url($comment_author->get('id')),
				'authorImageURL' => $comment_author->get('image-url', get_app_setting('default-user-image-url')),
				'authorLastSeen' => get_time_elapsed_intelligent(format_date(format_time($comment_author->get('last-seen-time')))),
				'authorJoinDate' => format_date($comment_author->get('date_registered'), 'F d, Y'),
				'authorLocation' => $comment_author->get('location'),
				'parentTitle'    => $comment_parent->get('title'),
			);
		}

		//$response_data[] = get_post_data($post_id); //this won't work cos it's designed to get only top level posts
	}
}
else if(isset($_GET['get-embed-code']))
{
	$embedly_api_key = 'd3b0372a109c4a47817821f61ddb8d73';
	
	/*
	$urls = $_GET['urls'];
	//var_dump($urls); exit;
	//$urls = json_decode($urls, true);
	
	for($i = 0, $len = count($urls); $i < $len; $i++)
	{
		$url = $urls[$i];
		$response = make_remote_request('http://api.embed.ly/1/oembed?url='. urlencode($url). '&key=:'. $embedly_api_key, array(CURLOPT_SSL_VERIFYPEER=>0,CURLOPT_SSL_VERIFYHOST=>0));
		//var_dump( json_decode($response, true) );
		
		$response_data[] = array('index'=>$i, 'url'=>$url, 'html'=>parse_data(json_decode($response)));
	}
	*/
	
	$url = $_GET['url'];
	$response = make_remote_request('http://api.embed.ly/1/oembed?url='. urlencode($url). '&key=:'. $embedly_api_key, array(CURLOPT_SSL_VERIFYPEER=>0,CURLOPT_SSL_VERIFYHOST=>0));
	if( is_string($response) ) {
		$response_data = array('url'=>$url, 'html'=>parse_data(json_decode($response)));
	}
	else {
		$response_data = array('url'=>$url, 'html'=>'');
	}
}
else if(isset($_GET['search-posts']))
{ 
	//extract($_GET); //title, authors, forums, categories, tags
	$author_ids    = array();
	$forum_ids     = array();
	$category_ids  = array();
	$tag_ids       = array();
	$filter_data   = array();
	$response_data = array();
	
	$orders       = array();
	$limit        = 0;
	
	$keywords     = trim($_GET['keywords']);
	$authors      = json_decode(trim($_GET['authors']));
	$forums       = isset($_GET['forums']) ? json_decode(trim($_GET['forums'])) : array();
	$categories   = isset($_GET['categories']) ? json_decode(trim($_GET['categories'])) : array();
	$tags         = isset($_GET['tags']) ? json_decode(trim($_GET['tags'])) : array();
	
	if( empty($keywords) && empty($authors) && empty($forums) && empty($categories) && empty($tags) ) {
		echo json_encode($response_data, true);
		exit;
	}
	
	foreach($authors AS $username) {
		$author_ids[] = get_user_id(trim($username));
	}
	foreach($forums AS $forum) {
		$forum_ids[] = get_forum_id(trim($forum));
	}
	foreach($categories AS $category) {
		$category_ids[] = get_category_id(trim($category));
	}
	foreach($tags AS $tag) {
		$tag_ids[] = get_tag_id(trim($tag));
	}
	
	if(!empty($forum_ids)) {
		$filter_data['forum'] = $forum_ids;
	}
	if(!empty($category_ids)) {
		$filter_data['category'] = $category_ids;
	}
	if(!empty($tag_ids)) {
		$filter_data['tag'] = $tag_ids;
	}
	
	$post_ids = PostModel::search($keywords, $author_ids, $filter_data, $orders, $limit);
	
	foreach($post_ids AS $post_id) {
		$response_data[] = get_post_data($post_id);
	}
}

function parse_data($data)
{
	//extract($data);
	/*
	all: type, url, title, description, provider_url, provider_name, thumbnail_url, thumbnail_width, thumbnail_height,
	photo, video, rich : author_name, author_url, width, height,
	video : html
	*/
	$return  = '';
	$width   = !empty($data->thumbnail_width)  && is_numeric($data->thumbnail_width)  ? $data->thumbnail_width  : 150;
	$height  = !empty($data->thumbnail_height) && is_numeric($data->thumbnail_height) ? $data->thumbnail_height : 300;
	$title   = !empty( $data->title ) && is_string( $data->title ) ? $data->title : '';
	$img_url = !empty($data->thumbnail_url) ? $data->thumbnail_url : '';
	$return  = '';
			
	if ( !empty($data->url) && is_string($data->url) )
	{
			$return .= ''.
			'<aside class="inline-url-embed-content '. $data->type. '-embed-content '. $data->provider_name. '-embed-content">'.
				'<div class="provider-info">'.
				 '<a href="'. sanitize_html_attribute( $data->url ). '" rel="external" target="_blank">'. rtrim(substr( $data->provider_url, strpos($data->provider_url, '://')+3 ), '/'). '</a>'.
				'</div>'.
				'<div class="resource-url"><a href="' . sanitize_html_attribute( $data->url ). '" rel="external" target="_blank">'. $title. '</a></div>'.
				'<img class="resource-image" src="'. htmlspecialchars($img_url, ENT_QUOTES, 'UTF-8' ). 
				'" alt="'.    htmlspecialchars($title, ENT_QUOTES, 'UTF-8'). 
				'" width="'.  htmlspecialchars($width, ENT_QUOTES, 'UTF-8'). 
				'" height="'. htmlspecialchars($height, ENT_QUOTES, 'UTF-8'). '" />'.
				'<div class="resource-description">'. $data->description. '</div>'.
			 '<div class="resource-clear"></div>';
	}

	switch($data->type)
	{
		case 'photo' :
		case 'link'  : break;

		case 'video' :           
		case 'rich'  :
			if ( !empty($data->html) && is_string($data->html) )
			{
				$return .= '<div class="resource-media">'. $data->html. '</div>';
			}
			break;
	}

	if( !empty($data->url) && is_string($data->url) )
	{
		$return .= '</aside>';
	}
			
	return $return;
}

echo json_encode($response_data, true);
exit;