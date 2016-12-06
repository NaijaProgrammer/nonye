<?php
include('request-validator.php');
if(isset($_GET['update-user-last-seen']))
{ 
	if( $user_is_logged_in)
	{
		update_user_last_seen_data( $current_user_id, $_GET['url'] );
		
		$last_seen_time = get_user_data($current_user_id, 'last-seen-time');
		$last_seen_url  = get_user_data($current_user_id, 'last-seen-url');
		$response_data  = array('success'=>true, 'lastSeenTime'=>$last_seen_time, 'lastSeenURL'=>$last_seen_url);
		echo json_encode($response_data, true);
	}
	exit;
}

else if(isset($_GET['get-notifications']))
{
	$last_seen_notification_id = $_GET['last-seen-notification-id'];
	$user_id       = $_GET['user-id'];
	$response_data = array();
	$notifications = get_user_notifications($user_id);
	foreach($notifications AS $notification_data)
	{
		$activity_id     = $notification_data['activity_id'];
		$notification_id = $notification_data['id'];

		if($notification_id > $last_seen_notification_id)
		{
			//only get notification actions that are by other users,
			//actions by the (current) user goes to user activities
			if( ActivityManager::get_activity_data($activity_id, 'subject_id') != $user_id )
			{
				$response_data[] = array( 'id'=>$notification_id, 'activity'=>format_activity($activity_id, $user_id) );
			}
		}
	}

	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['set-notification-as-seen']))
{
	$notification_id = $_POST['id'];
	$status = update_notification_status( $notification_id, 'seen' );
	
	$response_data = ($status) ? array('success'=>true, 'id'=>$notification_id) : array('error'=>true, 'id'=>$notification_id);
	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['login-user']))
{   
	header("Content-type:application/json");
	echo json_encode( UserAuth::login_user($_POST, true) ); 
	exit;
}

else if(isset($_POST['user-signup']))
{
	$min_len  = get_app_setting('password-min-length');
	$validate = Validator::validate(array(
		array('error_condition'=>username_exists($_POST['username']), 'error_message'=>'The username has been taken', 'error_type'=>'unavailableUsername'),
		array('error_condition'=>strlen($_POST['password']) < $min_len, 'error_message'=>'Passwords must be at least '. $min_len. ' characters long', 'error_type'=>'passwordTooShort'),
		array('error_condition'=>($_POST['password'] != $_POST['password2']), 'error_message'=>'Your passwords do not match', 'error_type'=>'nonMatchedPasswords')
	));
	
	if($validate['error'])
	{
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	
	else
	{
		$fullname  = $_POST['name'];
		$name_data = explode(' ', $fullname);
		$firstname = !empty($name_data[0]) ? trim($name_data[0]) : '';
		$lastname  = !empty($name_data[1]) ? trim($name_data[1]) : '';
		
		$data = array('firstname'=>$firstname, 'lastname'=>$lastname, 'username'=>$_POST['username'], 'email'=>$_POST['email'], 'password'=>$_POST['password']);
		$response_data = UserAuth::register_user($data);
	}
	
	header("Content-type:application/json");
	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['authorize-with-third-party']))
{
	$auth_provider = $_POST['auth-provider']; //facebook, google, yahoo, twitter, etc
	$email         = $_POST['email'];
	$firstname     = $_POST['firstname'];
	$lastname      = $_POST['lastname'];
	$user_password = Util::stringify($email. $auth_provider);
	
	if( !UserModel::user_exists($email) )
	{
		$signup_data = UserAuth::register_user(array( 
			'signup-type'   => 'third-party-authorization', 
			'auth-provider' => $auth_provider, 
			'email'         => $email, 
			'password'      => $user_password,
			'firstname'     => $firstname, 
			'lastname'      => $lastname
		));
	}
	
	$login_data = UserAuth::login_user(array(
		'loginType'               => 'third-party-authorization',
		'authProvider'            => $auth_provider,
		'userLogin'               => $email, 
		'userPassword'            => $user_password, 
		'rememberUser'            => true, 
		'unverifiedAccountError'  => 'Invalid account details. Please try again',
		'emptyLoginFieldError'    => '', 
		'emptyPasswordFieldError' => ''
	));
	
	header("Content-type:application/json");
	echo json_encode($login_data, true);
	exit;
}

else if(isset($_POST['password-recovery']))
{   
	$response_data = UserAuth::process_password_recovery($_POST['email']);
	
	header("Content-type:application/json");
	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['reset-password']))
{
	$min_len  = get_app_setting('password-min-length');
	$validate = Validator::validate(array(
		array('error_condition'=>empty($_POST['password']), 'error_message'=>'Passwords cannot be empty', 'error_type'=>'emptyPassword'),
		array('error_condition'=>strlen($_POST['password']) < $min_len, 'error_message'=>'Passwords must be at least '. $min_len. ' characters long', 'error_type'=>'passwordTooShort'),
	));
	
	if($validate['error'])
	{
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	else
	{
		$data = array('user_pnonce' => $_POST['nonce'], 'new_password'=>$_POST['password']);
		$response_data = UserAuth::process_password_reset($data);
	}
	
	create_json_string($response_data, true);
	exit;
}

else if(isset($_POST['update-user-data']))
{  
	extract($_POST);
	
	$user     = UserModel::get_user_instance($current_user_id);
	$validate = Validator::validate(array(
		array( 'error_condition'=>!$user_is_logged_in, 'error_message'=>'You must be logged in to perform this operation', 'error_type'=>'unauthenticatedUserError' ),
		array( 'error_condition'=>!EmailValidator::is_valid_email($email), 'error_message'=>'Invalid email specified', 'error_type'=>'' ),
		array( 'error_condition'=>( $user->get('email') != $email ) && email_exists($email), 'error_message'=>'This email address is already taken', 'error_type'=>'' ),
		array( 'error_condition'=>empty($username), 'error_message'=>'The username field is empty', 'error_type'=>'' ),
		array( 'error_condition'=>( $user->get('username') != $username ) && username_exists($username), 'error_message'=>'This username is already taken', 'error_type'=>'' )
	));
		
	if($validate['error'])
	{
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']);
	}

	else
	{
		update_user_data($current_user_id, array(
			'firstname'        => $firstname, 
			'lastname'         => $lastname, 
			'username'         => $username, 
			'login'            => $email, 
			'email'            => $email, 
			'email-visibility' => $email_visibility,
			'mobile-number'    => $mobile_number,
			'location'         => $location
		));

		$response_data = array('success'=>true);
	}
	
	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['update-user-password']))
{
	extract($_POST);
	$user                = UserModel::get_user_instance($current_user_id);
	$curr_user_pass      = $user->get('password'); //get_array_member_value($user_data, 'password');
	$password_min_length = get_app_setting('password-min-length');
	
	$validate = Validator::validate(array(
		array( 'error_condition'=>!$user_is_logged_in, 'error_message'=>'You must be logged in to perform this operation', 'error_type'=>'unauthenticatedUserError' ),
		array( 'error_condition'=>empty($current_password), 'error_message'=>'The current password field is empty', 'error_type'=>''),
		array( 'error_condition'=>UserModel::hash_password($current_password) != $curr_user_pass, 'error_message'=>'The current password you have entered does not match the one in our records', 'error_type'=>''),
		array( 'error_condition'=>empty($new_password), 'error_message'=>'The new password field is empty', 'error_type'=>''),
		array( 'error_condition'=>strlen($new_password) < $password_min_length, 'error_message'=>'Passwords cannot be less than '. $password_min_length. ' characters', 'error_type'=>''),
		array( 'error_condition'=>$new_password != $new_password_confirm, 'error_message'=>'Your new password and confirm new password do not match', 'error_type'=>'')
	));
		
	if($validate['error'])
	{
		$response_data = array( 'error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type'] );
	}
	else
	{
		update_user_data( $current_user_id, array('password'=>$new_password) );
		UserModel::logout_user( array() );
		$response_data = array('success'=>true, 'reauthenticateUser'=>true);
	}
	
	echo json_encode($response_data, true);
	exit;
}

else if(isset($_POST['update-user-online-data']))
{
	extract($_POST);
	
	$validate = Validator::validate(array(
		array( 'error_condition'=>!$user_is_logged_in, 'error_message'=>'You must be logged in to perform this operation', 'error_type'=>'unauthenticatedUserError' )
	));
		
	if($validate['error'])
	{
		$response_data = array( 'error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type'] );
	}
	else
	{
		update_user_data($current_user_id, array(
			'website-url'     => $website_url,
			'facebook-url'    => $facebook_url,
			'google-plus-url' => $google_plus_url,
			'instagram-url'   => $instagram_url,
			'linkedin-url'    => $linkedin_url,
			'twitter-url'     => $twitter_url,
			'youtube-url'     => $youtube_url
		));

		$response_data = array('success'=>true);
	}
	
	echo json_encode($response_data, true);
	exit;
}

else if( isset($_POST['update-profile-image']) && !empty($_FILES['file']['name']) )
{
	include(SITE_DIR. '/lib/image-cropper/image-cropper.php');
	
	$curr_user_id = $current_user_id;
	$rel_dir      = 'resources/uploads/users/user-'. $curr_user_id;
	$target_dir   = rtrim(SITE_DIR, '/'). '/'. $rel_dir. '/';
	
	//use short-circuiting to check if directory exists (and attempt to create it if it doesn't)
	is_dir($target_dir) || mkdir($target_dir, 0777, $recursive = true);

	if(!is_dir($target_dir))
	{
		$return_data = array('error'=>true, 'message'=>'Error creating the image storage folder. Please try again', 'errorType'=>'invalid_upload_directory');
	}

	$file_names  = $_FILES['file']['name'];
	$file_types  = $_FILES['file']['type'];
	$tmp_names   = $_FILES['file']['tmp_name'];
	$file_errors = $_FILES['file']['error'];
	$file_sizes  = $_FILES['file']['size'];

	$num_of_files = count($file_names);

	$curr_file_name = $_FILES['file']['name'];
	$temp_file      = $_FILES['file']['tmp_name']; 
	$target_file    = rtrim($target_dir, '/'). '/'. $curr_file_name;
	$image_url      = rtrim(SITE_URL, '/'). '/'. $rel_dir. '/'. $curr_file_name;
	
	if( move_uploaded_file($temp_file, $target_file) )
	{
		ImageCropper::resize_image(array(
			'source_image'           => $target_file,
			'destination_image_name' => $curr_file_name,
			'destination_directory'  => $target_dir, 
			'width'                  => 400,
			'height'                 => 400,
			'save_source_image'      => false
		));
		
		update_user_data( $curr_user_id, array('image-url'=>$image_url ) );
		
		/*
		* keep track of images uploaded by user
		*/
		ItemModel::add_item(array(
			'category'   => 'user-profile-images',
			'user_id'    => $curr_user_id,
			'image_name' => $curr_file_name, 
			'image_path' => $rel_dir, 
			'image_file' => $curr_file_name
		));
		
		$return_data = array('success'=>true, 'imageUrl'=>$image_url);
	}
	
	create_json_string($return_data, true);
	exit;
}

else if( isset($_POST['operation']) && ($_POST['operation'] == 'create_image_thumbnail') )
{
	include(SITE_DIR. '/lib/image-cropper/image-cropper.php');
	
	define('PROFILE_IMAGE_WIDTH_STANDARD', 160);
	define('PROFILE_IMAGE_HEIGHT_STANDARD', 160);
	define('PROFILE_IMAGE_WIDTH_MINI', 24);
	define('PROFILE_IMAGE_HEIGHT_MINI', 24);
	
	$posted_image  = $_POST['img_src'];
	$curr_user_id  = $current_user_id;
	$rel_dir       = 'resources/uploads/users/user-'. $curr_user_id. '/avatars';
	$image_name    = get_slug(get_file_name($posted_image));
	$image_extension = get_file_extension($posted_image);
	
	$thumbnail_image_name = $image_name. '-'. PROFILE_IMAGE_WIDTH_STANDARD. '-x-'. PROFILE_IMAGE_HEIGHT_STANDARD. '.'. $image_extension;
	$mini_image_name      = $image_name. '-'. PROFILE_IMAGE_WIDTH_MINI. '-x-'. PROFILE_IMAGE_HEIGHT_MINI. '.'. $image_extension;
	
	$thumbnail_url  = rtrim(SITE_URL, '/').  '/'. $rel_dir. '/'. $thumbnail_image_name;
	$mini_image_url = rtrim(SITE_URL, '/').  '/'. $rel_dir. '/'. $mini_image_name;
	
	ImageCropper::create_image_thumbnail(array(
		'thumbnail_directory' => rtrim(SITE_DIR, '/'). '/'. $rel_dir, 
		'thumbnail_name'      => $thumbnail_image_name,
		'save_original_image' => true
	));
	
	ImageCropper::resize_image(array(
		'source_image'           => $thumbnail_url,
		'destination_directory'  => rtrim(SITE_DIR, '/'). '/'. $rel_dir,
		'destination_image_name' => $mini_image_name,
		'width'                  => PROFILE_IMAGE_WIDTH_MINI,
		'height'                 => PROFILE_IMAGE_HEIGHT_MINI,
		'x'                      => $_POST['x'], 
		'y'                      => $_POST['y'],
		'save_source_image'      => true
	));
		
	update_user_data( $curr_user_id, array('image-url'=>$thumbnail_url ) );
	update_user_data( $curr_user_id, array('mini-image-url'=>$mini_image_url ) );
	
	$return_data = array( 'success'=>true, 'thumbnailUrl'=>$thumbnail_url, 'miniImageUrl'=>$mini_image_url );
	
	create_json_string($return_data, true);
	exit;
}