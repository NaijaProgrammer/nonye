<?php
//called once on installation
function setup_app()
{
	include SITE_DIR. '/app-specific-setup-files/forum-tables-setup.php';
	include SITE_DIR. '/app-specific-setup-files/forum-user-capabilities-setup.php';
	
	create_forum_tables();
	create_forum_user_capabilities();
}

/*
* Application-specific functions
* They are not required, so if you create this file,
* ensure you include it wherever you need to use the functions defined within it,
* alternatively, if you are going to be using them a lot, include the file inside the config.php file
* that way, it becomes globally available
*/

/* 
* Get the post editor from anywhere 
* $opts data members:
* placeholder string
* value string
* show_on_init boolean
*/
function get_post_editor( $opts = array() )
{
	$site_url = get_site_url();
	$page_instance = Page::get_instance();
	$opts['placeholder'] = isset($opts['placeholder']) ? $opts['placeholder'] : 'Type in your post here';
	
	//include VIEWS_DIR. '/'. get_current_theme(). '/template-fragments/fragments/post-editor.php';
	$page_instance->add_fragment( 'post-editor', $opts );
}

/* User Functions */
function get_user_realname($user_id='', $get_fullname = false)
{
	$user_data = UserModel::get_user_data(get_valid_user_id($user_id));
	
	$firstname = !empty($user_data['firstname']) ? $user_data['firstname'] : '';
	$lastname  = !empty($user_data['lastname'])  ? $user_data['lastname']  : '';
	
	if(empty($firstname) && empty($lastname))
	{
		return '';
	}
	if(empty($firstname))
	{
		return $lastname;
	}
	if(empty($lastname))
	{
		return $firstname;
	}
	
	if($get_fullname)
	{
		return $firstname. ' '. $lastname;
	}
}

function get_user_image_url($user_id)
{
	return UserModel::get_user_instance($user_id)->get('image-url', get_app_setting('default-user-image-url'));
}

function get_user_profile_url($user_id='')
{
	$user_id = get_valid_user_id($user_id);
	$user    = UserModel::get_user_instance($user_id);
	return generate_url(array('controller'=>'users', 'qs'=>array($user_id, get_slug($user->get('username')))));
}

function get_user_profile_edit_url($user_id='')
{
	$user_id = get_valid_user_id($user_id);
	$user    = UserModel::get_user_instance($user_id);
	return generate_url(array('controller'=>'users', 'qs'=>array($user_id, get_slug($user->get('username')), 'edit')));
}

function generate_user_profile_page($user_id)
{
/*
	$users_dir    = SITE_DIR. '/users';
	$tmp          = $users_dir. '/'. $user_id. '-temp.html';
	$user_id      = $user_id;
	$user_dir     = $users_dir. '/'. $user_id;
	
	!is_dir($users_dir) && mkdir($users_dir);
	!is_dir($user_dir) && mkdir($user_dir);
	file_exists($tmp) && unlink($tmp);
	
	ob_start();
	include CURRENT_THEME_DIR. '/pages/users/user.php';
	file_put_contents( $tmp, ob_get_contents() );
	ob_end_clean();
	copy($tmp, $user_dir. '/'. get_slug(get_username($user_id)). '.html');
	unlink($tmp);
	
	$user_profile_url = rtrim( generate_url(array('controller'=>'users', 'qs'=>array($user_id, get_slug(get_username($user_id))))), '/' ). '.html';
	UserModel::update_user_data($user_id, array( array('data_key'=>'profile_url', 'data_value'=>$user_profile_url) ));
*/
}

function update_user_profile_view_count($user_id)
{
	$viewer_id   = UserModel::get_current_user_id();
	$target_user = UserModel::get_user_instance($user_id);
	
	record_user_profile_viewer($user_id, $viewer_id);
		
	if( $user_id == $viewer_id )
	{
		return;
	}
	
	$cookie_pre  = empty($viewer_id) ? md5($_SERVER["HTTP_USER_AGENT"]) : $viewer_id;
	$cookie_name = 'agent_'. $cookie_pre. '_has_viewed_user_'. $user_id;
		
	if( isset($_COOKIE[$cookie_name]) )
	{
		return;
	}
		
	else
	{ 
		$curr_view_count = !empty($target_user->get('profile-view-count')) ? $target_user->get('profile-view-count') : 0;
		setcookie($cookie_name, true);	
		return $target_user->update('profile-view-count', $target_user->get('profile-view-count') + 1);
	}
}
	
function record_user_profile_viewer($user_id, $viewer_id = 0)
{
	return empty($viewer_id) ? false : ItemModel::add_item(array('category'=>'user_profile_viewers', 'user_id'=>$user_id, 'viewer_id'=>get_valid_user_id($viewer_id)));
}

/*
* $filters array:
* limit
*/
function get_user_activities($user_id, $filters=array())
{
	$activity_ids = ActivityManager::get_user_activities($user_id, $filters);
	$activities   = array();
	
	foreach($activity_ids AS $activity_id)
	{
		$activities[] = $activity_id;
	}
	
	return $activities;
}

function get_user_posts($user_id, $where_data = array(), $order_data = array(), $limit = 0)
{
	$where_data['author_id'] = $user_id;
	return PostModel::get_posts(true, $where_data, $order_data, $limit);
}

/* Utility Functions */
function get_time_elapsed_intelligent($the_date)
{
	$time_elapsed = get_time_difference($the_date, true);
	return ( (stristr($time_elapsed, 'days') !== FALSE) && ( intval($time_elapsed) > 7 ) ) ? format_date($the_date, 'F d, Y') : $time_elapsed. ' ago';
}

/*
* $user_id the user for whom the activity is being formatted
*/
function format_activity($activity_id, $user_id)
{
	$activity_data = ActivityManager::get_activity_data($activity_id); //object_id, object_type ('post'), subject_id, subject_action ('create', 'like', 'share'), description
	extract($activity_data);
	
	
	if(!empty($description))
	{
		return $description;
	}
	
	$activity_subject = UserModel::get_user_instance($subject_id);
	
	if($object_type == 'post')
	{
		/*
		* Three possible user ids
		* subject_id the creator of the activity
		* post_author_id  the creator of the post ( same as subject id of it's a top-level post, possibly different from subject id if it's a reply/comment post)
		* user_id the user for whom the activity is being formatted (may or may not be same as the other IDS)
		*
		* description: conditions
		* 1. {{username}} created a new post: is_top_level_post {{username}} = post_author_id == $user_id ? 'You' : post_author_username
		* 2. {{username}} commented on/replied to/liked/shared your post with postID :  activity_subject_id != $user_id
		* 3. You commented on/replied to/liked/shared {{username}} post with postID : !is_top_level_post {{username}} = if author_id == $user_id ? 'Your' : post_author_username
		*/
		
		$post        = PostModel::get_post_instance($object_id);
		$post_id     = $object_id;
		$post_title  = is_top_level_post($post_id) ? $post->get('title') : PostModel::get_post_instance( get_top_level_parent_post($post_id) )->get('title');
		$post_url    = sanitize_html_attribute( get_post_url($post->get('id')) );
		$post_link   = "<a href=\"$post_url\">$post_title</a>";
		$author_id   = $post->get('author_id');
		$author      = UserModel::get_user_instance($author_id);
		$author_url  = sanitize_html_attribute( get_user_profile_url($author_id) );
		$author_name = $author->get('username');
		$author_link = "<a href=\"$author_url\">$author_name</a>";
		$desc        = "$date_added : ";
		
		if( is_top_level_post($object_id) )
		{
			$actor = ( $author_id == $user_id ) ? 'You' : $author_link;
			$desc  .= "$actor {$subject_action}d a new post $post_link";
		}
		
		else
		{
			$action = ($subject_action == 'create') ? 'replied to' : $subject_action. 'd';
			
			//must be a reply/like/share of top level post
			if($subject_id != $user_id)
			{
				$subject      = UserModel::get_user_instance($subject_id);
				$subject_name = $subject->get('username');
				$subject_url  = sanitize_html_attribute( get_user_profile_url($subject_id) );
				$subject_link = "<a href=\"$subject_url\">$subject_name</a>";
				$desc .= "$subject_link $action your post $post_link";
			}
			else
			{
				$author_username = ( $author_id == $user_id ) ? 'your' : $author_link;
				$desc .= "You $action $author_username post $post_link";
			}
		}
		
		return $desc;
	}
}

//Post (Forums, Categories, Tags, Posts) functions
function get_post_data($post_id)
{
	$post     = PostModel::get_post_instance($post_id);
	$forum    = ( !empty($post->get_forums())   ? ForumModel::get_forum_instance( $post->get_forums()[0] ) : null );
	$category = ( !empty($post->get_categories()) ? CategoryModel::get_category_instance( $post->get_categories()[0] ) : null );
	$tags     = ( !empty($post->get_tags())        ? $post->get_tags() : null ) ;
	$author   = UserModel::get_user_instance( $post->get('author_id') );
	
	$forum_a    = array();
	$category_a = array();
	$tags_a     = array();
	
	if( !empty($forum) ) {
		$forum_a = array( 'name'=>$forum->get('name'), 'url'=>generate_url(array('controller'=>'posts', 'action'=>'forum', 'qs'=>array($forum->get('name')))) );
	}
	if( !empty($category) ) {
		$category_a = array( 'name'=> $category->get('name'), 'url'=>generate_url(array('controller'=>'posts', 'action'=>'category', 'qs'=>array($category->get('name')))));
	}
	for($i=0, $len=count($tags); $i < $len; $i++) {
		$tag = TagModel::get_tag_instance($tags[$i]); 
		$tags_a[] = array( 'name'=>$tag->get('name'), 'url'=>generate_url(array('controller'=>'posts', 'action'=>'tagged', 'qs'=>array($tag->get('name')))) );
	}
	
	$author_a   = array( 'username'=>$author->get('username'), 'url'=>get_user_profile_url($author->get('id')), 'imageURL'=>$author->get('image-url', get_app_setting('default-user-image-url')) );
	
	$data = array(
		'id'            => $post_id,
		'url'           => get_post_url($post_id),
		'title'         => $post->get('title'),
		'fTitle'        => get_substring($post->get('title')),
		'imageURL'      => get_post_image_url($post_id),
		'dateCreated'   => get_time_difference($post->get('date_created')),
		'fDateCreated'  => format_date( $post->get('date_created') ),
		'viewCount'     => $post->get_view_data($count=true),
		'fViewCount'    => format_count($post->get_view_data($count=true)),
		'commentCount'  => $post->get_comments($count=true),
		'fCommentCount' => format_count($post->get_comments($count=true)),
		'forum'         => $forum_a,
		'category'      => $category_a,
		'tags'          => $tags_a,
		'author'        => $author_a
	);

	return $data;
}

function get_post_image_url($post_id, $default_img_src='')
{
	$post    = PostModel::get_post_instance($post_id);
	$featured_image_url      = trim ( $post->get_meta('featured-image-url') );
	$featured_image_url_path = str_replace( get_site_url(). '/', '', $featured_image_url );
	
	if( !empty($featured_image_url_path) ) {
		return $featured_image_url;
	}
	
	$post_c  = $post->get('content');
	$img_src = '';
	
	if(!empty($post_c));
	{
		include_once INCLUDES_DIR. '/classes/url.class.php';
		$url  = new Url();
		$url->create_from_string($post_c);
		
		$img_src = $url->image(0);
		
		if(is_external_resource($img_src))
		{
			return $img_src;
		}
		
		$placeholder_img = !empty($default_img_src) ? $default_img_src : get_site_url(). '/resources/images/placeholder.png';
		
		$full_path_img_src = rtrim( UrlInspector::get_path($_SERVER['DOCUMENT_ROOT']. $img_src)['http_path'], '/' );
		//$full_path_img_src = rtrim( $img_src, '/' );
		
		switch( strtolower(get_file_extension($img_src)) )
		{
			case 'jpg'   :
			case 'jpeg'  :
			case 'pjpeg' : $img = imagecreatefromjpeg($full_path_img_src); break;
			case 'gif'   : $img = imagecreatefromgif($full_path_img_src); break;
			case 'png'   : $img = imagecreatefrompng($full_path_img_src); break;
		}
		
		if( !empty($img) && is_resource($img) )
		{
			$img_width  = imagesx($img);
			$img_height = imagesy($img);
			
			if($img_width < 205 || $img_height < 110)
			{
				$img_src = $placeholder_img;
				//use default image placeholder, since this image is too small for our dimensions, and resizing it will distor it (e.g the image is a smilie)
			}
			else if($img_width == 205 && $img_height == 110)
			{
				//return the image, it is our preferred dimensions
			}
			else
			{  
				//image larger than our preferred dimensions, resize it accordinglyis_dir($target_dir) || mkdir($target_dir, 0777, $recursive = true);
				$img_name   = get_file_name($img_src);
				$img_ext    = get_file_extension($img_src);
				$target_dir = rtrim(SITE_DIR, '/'). '/resources/uploads/posts/'. $post_id. '/featured-images';
				$final_img  = $target_dir. '/'. $img_name. '.'. $img_ext;

				if( file_exists($final_img) )
				{
					$img_src = $final_img;
				}
				else
				{
					is_dir($target_dir) || mkdir($target_dir, 0777, $recursive = true);
					
					if(!is_dir($target_dir))
					{
						//creating directory failed, so resizing will fail. Return placeholder image
						$img_src = $placeholder_img;
					}
					else
					{
						include_once(SITE_DIR. '/lib/image-cropper/image-cropper.php');
						ImageCropper::resize_image(array(
							'source_image'           => $full_path_img_src,
							'destination_directory'  => $target_dir,
							'destination_image_name' => $img_name. '.'. $img_ext,
							'width'                  => 205,
							'height'                 => 110,
							'x'                      => 0, //(int) $img_width/4, 
							'y'                      => 0, //(int) $img_height/4,
							'save_source_image'      => true
						));
						
						$final_img = rtrim( UrlInspector::get_path($final_img)['http_path'], '/' );
						$img_src = $final_img;
					}
				}
			}
		}
	}
	
	$img_src = trim ( str_ireplace(SITE_DIR, SITE_URL, $img_src) );
	return !empty($img_src) ? $img_src : $placeholder_img; //get_site_url(). '/resources/images/placeholder.png';
}

function is_top_level_post($post_id)
{
	$post = PostModel::get_post_instance($post_id);
	$parent_id = $post->get('parent_id');
	return ( ($parent_id == 0) ? true : false );
	//return !empty( PostModel::get_post_instance($post_id)->get('title') );
}

function get_top_level_parent_post($post_id)
{
	$post = PostModel::get_post_instance($post_id);
	$parent_id = $post->get_parent();
	
	while( !is_top_level_post($parent_id) )
	{
		$parent_id = $post->get_parent();
	}
	
	return $parent_id;
}

function update_post_view($post_id)
{
	$post      = PostModel::get_post_instance($post_id);
	$viewer_id = UserModel::get_current_user_id();
	
	ItemModel::add_item(array(
		'category'   => 'post-views-track', 
		'post-id'    => $post_id, 
		'viewer-id'  => $viewer_id,
		'ip-address' => $_SERVER['REMOTE_ADDR'],
		'referrer'   => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
		'view-time'  => time()
	));
	
	$cookie_pre  = empty($viewer_id) ? md5($_SERVER["HTTP_USER_AGENT"]) : $viewer_id;
	$cookie_name = 'post_'. $post_id. '_viewed_by_'. $cookie_pre;
	
	if( isset($_COOKIE[$cookie_name]) )
	{
		return;
	}
	else
	{ 
		setcookie($cookie_name, true);	
		return $post->record_view($viewer_id);
	}
}

function get_post_excerpt($post_id, $excerpt_length = '')
{
	$post    = PostModel::get_post_instance($post_id);
	$excerpt = trim( $post->get('excerpt') );
	return !empty($excerpt) ? get_substring($excerpt, $excerpt_length) : get_substring( $post->get('content'), $excerpt_length );
}

function get_post_url($post_id)
{
	$post = PostModel::get_post_instance($post_id);
	
	if( is_top_level_post($post_id) )
	{
		return rtrim(generate_url(array('controller'=>'posts')), '/'). '/'. $post_id. '/'. get_slug($post->get('title'));
	}
	else
	{
		//post is a reply/comment to another post
		$mainpost_id  = get_top_level_parent_post($post_id);
		$mainpost     = PostModel::get_post_instance($mainpost_id);
		$mainpost_url = rtrim(generate_url(array('controller'=>'posts')), '/'). '/'. $mainpost_id. '/'. get_slug($mainpost->get('title'));
		return $mainpost_url. '#post-response-'. $post_id; //TO DO: implement this to return the main-post#the-reply
	}
}

function get_post_short_url($post_id)
{
	if( is_top_level_post($post_id) )
	{
		return rtrim(SITE_URL, '/'). '/p/'. $post_id;
	}
	else
	{
		return rtrim(SITE_URL, '/'). '/c/'. $post_id;
	}
}

function create_short_url($post_id)
{
	$post_real_url  = get_post_url($post_id);
	$post_short_url = get_post_short_url($post_id);
	get_url_shortener()->create_short_url($post_real_url, $post_short_url);
}

function delete_short_url($post_id)
{
	$post_short_url = get_post_short_url($post_id);
	$conn           = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	
	mysqli_query( $conn,
		"DELETE FROM `tiny_url_master` ".
		"WHERE `tiny_url` = '". addslashes($post_short_url). "'"
	);
}

function get_url_shortener()
{
	require_once rtrim(SITE_DIR, '/'). '/lib/tiny-url-with-php/tinyurl.class.php';
	return TinyURL::get_instance( array('db_server'=>DB_SERVER, 'db_user'=>DB_USER, 'db_pass'=>DB_PASS, 'db_name'=>DB_NAME) );
}

function detect_and_redirect_short_url()
{
	$real_url = get_url_shortener()->get_long_url( rtrim(SITE_URL, '/'). '/'. new PathModel() ); 
	
	if( !empty($real_url) )
	{
		header("Location:". $real_url);
		exit;
	}
}

function get_forum_id($forum_name)
{
	$tp = get_tables_prefix();
	$db = get_db_instance();
	
	$db->execute_query( "SELECT `id` FROM {$tp}forums WHERE `name` = '$forum_name'" );
	
	$row = $db->get_rows();
	
	return !empty($row['id']) ? $row['id'] : 0;
}

function get_category_id($category_name)
{
	$tp = get_tables_prefix();
	$db = get_db_instance();
	
	$db->execute_query( "SELECT `id` FROM {$tp}categories WHERE `name` = '$category_name'" );
	
	$row = $db->get_rows();
	
	return !empty($row['id']) ? $row['id'] : 0;
}

function get_tag_id($tag_name)
{
	$tp = get_tables_prefix();
	$db = get_db_instance();
	
	$db->execute_query( "SELECT `id` FROM {$tp}tags WHERE `name` = '$tag_name'" );
	
	$row = $db->get_rows();
	
	return !empty($row['id']) ? $row['id'] : 0;
}

function forum_exists($name)
{
	return (get_forum_id($name) > 0);
}

function category_exists($name)
{
	return (get_category_id($name) > 0);
}

function tag_exists($name)
{
	return (get_tag_id($name) > 0);
}

/* Notification (subscriber and publisher) functions */
function publish_notification($activity_id)
{
	$subscriber_ids = get_subscribers($activity_id);
	
	foreach($subscriber_ids AS $subscriber_id)
	{
		ItemModel::add_item(array(
			'category'      => 'user-notifications',
			'subscriber_id' => $subscriber_id,
			'activity_id'   => $activity_id,
			'status'        => 'not-seen'
		));
	}
}

function update_notification_status($notification_id, $new_status)
{
	$status_whitelist = array('not-seen', 'seen');
	$previous_status  = ItemModel::get_item_data($notification_id, 'status');
	if( ($previous_status != $new_status) && in_array($new_status, $status_whitelist) )
	{
		ItemModel::update_item($notification_id, array(array('data_key'=>'status', 'data_value'=>$new_status, 'overwrite'=>true)));
	}
	
	$updated_status = ItemModel::get_item_data($notification_id, 'status');
	return ( $updated_status == $new_status );
}

function get_subscribers($activity_id)
{
	$activity_data  = ActivityManager::get_activity_data($activity_id); //object_id, object_type, subject_id, subject_action, description, time_created
	extract($activity_data);
	
	if($object_type == 'post')
	{
		$post = PostModel::get_post_instance($object_id);
		switch($subject_action)
		{
			case 'create' : return $post->get_participants();
			case 'like'   :
			case 'share'  : return array( $post->get('creator_id') );
		}
	}
}

/*
* $filters array:
* status string seen|not-seen
* limit : int
*/
function get_user_notifications($user_id, $filters = array())
{
	$status = isset($filters['status']) ? $filters['status'] : 'not-seen';
	$limit  = isset($filters['limit'])  ? $filters['limit']  : 0;
	$n_data = ItemModel::get_items( array('category'=>'user-notifications', 'subscriber_id'=>$user_id, 'status'=>$status, 'data_to_get'=>array('id', 'activity_id')), array(), $limit );
	return $n_data;
}