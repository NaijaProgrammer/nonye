<?php PageModel::authenticate_user( array('redirect_url'=>'', 'authentication_action'=>'login') ); ?>
<?php include(SITE_DIR. '/lib/image-cropper/image-cropper.php'); ?>
<?php
if( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['action']) )
{
	foreach($_POST AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
		
		/*if($key == 'tags')
		{
			$$key = json_decode($value, true);
		}
		*/
	}
	
	if( $action == 'update_user_data' )
	{
		$user_is_logged_in = UserModel::user_is_logged_in();
		
		$validate = Validator::validate(array(
			array( 'error_condition'=>!$user_is_logged_in,   'error_message'=>'User not logged in' ),
			array( 'error_condition'=>!EmailValidator::is_valid_email($email), 'error_message'=>'Invalid email specified' ),
			array( 'error_condition'=>( $user->get('email') != $email ) && email_exists($email), 'error_message'=>'This email address is already taken' ),
			array( 'error_condition'=>empty($username), 'error_message'=>'The username field is empty' ),
			array( 'error_condition'=>( $user->get('username') != $username ) && username_exists($username), 'error_message'=>'This username is already taken' )
		));
		
		if($validate['error'])
		{
			$data = array('error'=>true, 'message'=>$validate['status_message']);
			
			if(!$user_is_logged_in)
			{
				$data['errorType'] = 'UnauthenticatedUserError';
			}
		}
		
		else
		{
			update_user_data(UserModel::get_current_user_id(), array(
				'firstname'        => $firstname, 
				'lastname'         => $lastname, 
				'username'         => $username, 
				'login'            => $email, 
				'email'            => $email, 
				'email-visibility' => $email_visibility,
				'location'         => $location, 
				'profession'       => $profession, 
				'about'            => $about
			) );
			
			$data = array('success'=>true);
		}
	}
	
	else if( $action == 'update_education_data' )
	{
		$edu_data          = json_decode( urldecode($data), true );
		$user_is_logged_in = UserModel::user_is_logged_in();
		
		$validate = Validator::validate(array(
			array( 'error_condition'=>!$user_is_logged_in,   'error_message'=>'User not logged in' ),
		));
		
		if($validate['error'])
		{
			$data = array('error'=>true, 'message'=>$validate['status_message']);
			
			if(!$user_is_logged_in)
			{
				$data['errorType'] = 'UnauthenticatedUserError';
			}
		}
		
		else
		{ 
			//for($i = 0, $len = count($edu_data); $i < $len; $i++)
			foreach($edu_data AS $curr_data)
			{
				//$curr_data = $edu_data[$i];
				$html_field_id = trim($curr_data['field_id']);
				$institution   = trim($curr_data['institution']);
				$certification = trim($curr_data['certification']);
				$start_date    = trim($curr_data['start_date']);
				$end_date      = trim($curr_data['end_date']);
				
				$record_id = isset($curr_data['record_id']) ? trim($curr_data['record_id']) : 0; //used for update, if the user has previously added education info
				
				$updated_record_id = update_user_education_data(UserModel::get_current_user_id(), array(
					'record_id'     => $record_id, 
					'institution'   => $institution,
					'certification' => $certification,
					'start_date'    => $start_date,
					'end_date'      => $end_date
				));
				
				if($updated_record_id)
				{
					$fields_and_records[] = array('fieldID'=>$html_field_id, 'recordID'=>$updated_record_id);
					$record_ids[] = $updated_record_id;
				}
			}
			
			if( empty($record_ids) )
			{
				$data = array('error'=>true, 'message'=>'Unable to update education data', 'errorType'=>'SystemError');
			}
			else
			{
				$data = array( 'success'=>true, 'records'=>DataSanitizer::escape_output_string(json_encode($fields_and_records, true)) );
			}
		}
	}
	
	else if( $action == 'update_experience_data' )
	{ 
		$work_data         = json_decode( urldecode($data), true ) ;
		$user_is_logged_in = UserModel::user_is_logged_in();
		
		$validate = Validator::validate(array(
			array( 'error_condition'=>!$user_is_logged_in,   'error_message'=>'User not logged in' ),
		));
		
		if($validate['error'])
		{
			$data = array('error'=>true, 'message'=>$validate['status_message']);
			
			if(!$user_is_logged_in)
			{
				$data['errorType'] = 'UnauthenticatedUserError';
			}
		}
		
		else
		{ 
			foreach($work_data AS $curr_data)
			{
				$html_field_id = trim($curr_data['field_id']);
				$employer      = trim($curr_data['employer']);
				$job_title     = trim($curr_data['job_title']);
				$start_date    = trim($curr_data['start_date']);
				$end_date      = trim($curr_data['end_date']);
				
				$record_id = isset($curr_data['record_id']) ? trim($curr_data['record_id']) : 0; //used for update, if the user has previously added education info
				
				$updated_record_id = update_user_work_experience_data(UserModel::get_current_user_id(), array(
					'record_id'  => $record_id, 
					'employer'   => $employer,
					'job_title'  => $job_title,
					'start_date' => $start_date,
					'end_date'   => $end_date
				));
				
				if($updated_record_id)
				{
					$fields_and_records[] = array('fieldID'=>$html_field_id, 'recordID'=>$updated_record_id);
					$record_ids[] = $updated_record_id;
				}
			}
			
			if( empty($record_ids) )
			{
				$data = array('error'=>true, 'message'=>'Unable to update experience data', 'errorType'=>'SystemError');
			}
			else
			{
				$data = array( 'success'=>true, 'records'=>DataSanitizer::escape_output_string(json_encode($fields_and_records, true)) );
			}
		}
	}
	
	else if($action == 'update_user_social_data')
	{
		update_user_data(UserModel::get_current_user_id(), array(
			'website-url'     => $website_url,
			'facebook-url'    => $facebook_url,
			'google-plus-url' => $google_plus_url,
			'instagram-url'   => $instagram_url,
			'linkedin-url'    => $linkedin_url,
			'twitter-url'     => $twitter_url,
			'youtube-url'     => $youtube_url
		));
		
		$data = array('success'=>true);
	}
	
	else if($action == 'update_user_password')
	{
		$curr_user_pass = get_array_member_value($user_data, 'password');
		$validate       = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'User not logged in', 'error_type'=>'UnauthenticatedUserError' ),
			array( 'error_condition'=>empty($current_password), 'error_message'=>'The current password field is empty'),
			array( 'error_condition'=>UserModel::hash_password($current_password) != $curr_user_pass, 'error_message'=>'The current password you have entered does not match the one in our records'),
			array( 'error_condition'=>empty($new_password), 'error_message'=>'The new password field is empty'),
			array( 'error_condition'=>strlen($new_password) < PASSWORD_MIN_LENGTH, 'error_message'=>'Passwords cannot be less than six characters'),
			array( 'error_condition'=>$new_password != $new_password_confirm, 'error_message'=>'Your new password and confirm new password do not match')
		));
		
		if($validate['error'])
		{
			$data = array('error'=>true, 'message'=>$validate['status_message']);
			if( isset($validate['error_type']) )
			{
				$data['errorType'] = $validate['error_type'];
			}
		}
		else
		{
			update_user_data( UserModel::get_current_user_id(), array('password'=>$new_password) );
			UserModel::logout_user( array() );
			$data = array('success'=>true, 'reauthenticateUser'=>true);
		}
	}
	
	create_json_string($data, true);
	exit;
}

else if( ($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_FILES['file']['name']) )
{
	$curr_user_id = UserModel::get_current_user_id();
	$rel_dir      = 'resources/uploads/users/user-'. $curr_user_id;
	$target_dir   = rtrim(SITE_DIR, '/'). '/'. $rel_dir. '/';
	
	//use short-circuiting to check if directory exists, and possibly make a new directory if it doesnt
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
		
		update_user_data( $curr_user_id, array('profile_image_url'=>$image_url ) );
		
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
	$posted_image  = $_POST['img_src'];
	$curr_user_id  = UserModel::get_current_user_id();
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
		
	update_user_data( $curr_user_id, array('profile_image_url'=>$thumbnail_url ) );
	update_user_data( $curr_user_id, array('mini_image_url'=>$mini_image_url ) );
	
	$return_data = array( 'success'=>true, 'thumbnailUrl'=>$thumbnail_url, 'miniImageUrl'=>$mini_image_url );
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php 
PageModel::add_page_header( 'default', array( 
	'page_title'       => $page_title,
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => $robots_value,
	'stylesheets'      => array(CURRENT_THEME_URL. '/css/users.css'),
	'current_user'     => $current_user, //coming from the app-controller class
	'open_graph_data'  => $open_graph_data
)); 

PageModel::add_navigation('default', array(
	'current_user' => $current_user //coming from the app-controller class
)); 
?>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/animator.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/slider.js"></script>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/js/lib/dropzone/dropzone.css" />
<script src="<?php echo SITE_URL; ?>/js/lib/dropzone/dropzone.js"></script>
<script>function setProfilePixUrl(imgUrl){$O('user-photo').src = imgUrl;}</script>
<script>function setMainNavUserPixUrl(imgUrl){$O('main-nav-user-photo').src = imgUrl;}</script>
<div class="container">
<div class="row">
 <div class="col-md-3"></div>
 <div class="col-md-6">
  <h3 class="data-header text-centered mb20" style="border-bottom:none;">Edit your account details</h3>
 </div>
 <div class="col-md-3"></div>
</div>

<div class="row">

 <div class="col-md-9">
  <div class="col-md-3 no-border no-padding">
   <div class="text-centered" id="user-photo-container" style="border:none; 1px solid #ccc;">
    <img src="<?php echo $current_user->get('profile_image_url'); ?>" 
		class="user-photo" 
		id="user-photo"
		style="width:<?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?>px; height:<?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?>px;"/>
	<div id="image-crop-preview" style="margin-left:17px; margin-top:10px;"></div>
   </div>
   <div class="text-centered" style="border:1px solid #eee;">
    <span id="profile-pix-changer" class="cursor-pointer">Change Image</span><br>
    <span id="profile-pix-processing">&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span id="upload-image-button" class="cursor-pointer" style="visibility:hidden;">Upload</span>
    <script>
	function handleCropProcessing(submitBtnID)
	{
		Site.Util.addClassTo(submitBtnID, 'btn btn-primary bg-right bg-no-repeat pr25 pl25');
		disable(submitBtnID);
		showProcessing(submitBtnID);
	}
	function handleCropSuccessCallback(response, submitBtnID)
	{
		response = Site.Util.parseAjaxResponse(response);
		setProfilePixUrl(response.thumbnailUrl);
		setMainNavUserPixUrl(response.miniImageUrl);
		$Style('image-crop-preview').display = 'none';
		hideProcessing('profile-pix-processing');
		hideProcessing(submitBtnID);
		Site.Util.removeClassFrom(submitBtnID, 'btn btn-primary bg-right bg-no-repeat pr25 pl25');
		enable(submitBtnID);
	}
    </script>
    <?php
    $cropper_unique_id_prefix = 'mike-test';
    echo ImageCropper::get_image_cropper(array(
		'plugin_url'                   => SITE_URL. '/lib/image-cropper',
		'unique_id_prefix'             => $cropper_unique_id_prefix,
		'action_page'                  => '', 
		'include_thumb_scale_details'  => false, 
		'crop_button_value'            => 'Save',
		'crop_processing_callback'     => 'handleCropProcessing',
		'crop_success_callback'        => 'handleCropSuccessCallback'
	));
	?>
   </div>
  
   <script>
	/*
	* because the Dropzone constructor function uses document.querySelector internally to get the dropzone element, 
	* you have to pass in div#my-dropzone, rather than my-dropzone
	* otherwise, you will get an "invalid dropzone element" error
	*/
	var myDropzone = new Dropzone("#profile-pix-changer", { 
		method                       : "post", //"put" is also allowed
		url                          : '', //"<?php echo get_user_profile_url($user_id); ?>",
		paramName                    : "file", // The name that will be used to transfer the file
        maxFilesize                  : 1, // MB
		maxFiles                     : null, //if not null defines how many files this Dropzone handles.
		uploadMultiple               : false,
		acceptedFiles                : 'image/*',
		autoProcessQueue             : false,
		dictResponseError            : 'Error from server with status {{statusCode}}',
		dictInvalidFileType          : 'Error invalid file type',
		dictFileTooBig               : 'Error file too big. file size is {{filesize}}, max allowed upload size is {{maxFilesize}}',
		addRemoveLinks               : false,
		dictCancelUpload             : 'Cancel this upload', //If addRemoveLinks is true, the text to be used for the cancel upload link.
		dictCancelUploadConfirmation : 'Are you sure you wana cancel this upload?', //If addRemoveLinks is true, the text to be used for confirmation when cancelling upload.
		dictRemoveFile               : 'Remove this file', //If addRemoveLinks is true, the text to be used to remove a file.
		parallelUploads              : 1, //How many file uploads to process in parallel 
		
		/*
		resize: function(file)
		{
			return { srcX : '', srcY : '', srcWidth : '', srcHeight : '' }
		},
		*/
		
		processing(file)
		{
			showProcessing('profile-pix-processing');
		},
		
        accept: function(file, done)
		{ 
			/*
			* discovered - while trying to auto-submit the image/form -
			* that done() must first be called for it(or possibly anything, for that matter) to work.
			*
			* Alternatively, you can make use of the event queue to delay the trigger
			* for the form submission, e.g using addEventListener or setTimeout.
			* That way, done() is run first, before the event is processed.
			* See the Site.Event.attachListener() call below
			* @date May 7, 2016 15:10 hrs
			*/
			done(); 
			
			/*
			if (file.name == "something.jpg")
			{
				done("Naha, you don't.");
			}
			else 
			{ 
				done(); 
			}
			*/
			//console.log(file);
			//$Style('upload-image-button').visibility = 'visible';
			//Site.Event.attachListener('upload-image-button', 'click', function(){  myDropzone.processQueue() });
			
			//hide the image the dropzone area that shows the selected image
			//this is just to create an effect/experience
			//see reason for this in the 'onsuccess()' callback
			hideDropZonePreview(); 
			
			//auto-submit the form, see reason for this in the 'onsuccess()' callback
			myDropzone.processQueue();
		},
		
		//credits : http://stackoverflow.com/a/32481251/1743192
		error: function(file, errorMessage)
		{
			myDropzone.errors = true;
			myDropzone.errorMsg = errorMessage;
			
			hideDropZonePreview();
			hideDropZoneErrorAndSuccessMarks();
		},
		
		/*
		* success is called on each file processed, 
		* Once the backend server returns a response,
		* it matters not whether the backend server response is positive or negative (i.e, file uploaded or not).
		* It is called to indicate that the file has been handed on to the backend processor
		* (and the processor has returned)
		*/
		//this is commented out, since we are using the dropzone.on('success', function(){}), which is an alias for this
		/*
		success: function(file, response){
			
			//onqueuecomplete() doesn't take the file and response parameters
			//so, manually create the flags as part of "this" dropzone object
			if(response.error)
			{
				myDropzone.error = true;
				myDropzone.message = response.message;
				myDropzone.errorType = response.errorType;
			}
			else
			{
				myDropzone.error = false;
				console.log(response);
				$Style('upload-image-button').visibility = 'hidden';
				
				/*
				* don't set the images here, let them be set on successful cropping of the image
				* so that the user doesn't first see this, and then - when cropping is complete - the cropped version
				* an effect that is not so nice, since user is supposed to see the operation as one (atomic) operation
				* not as a two-step upload first, and crop later, 
				* although - under the hood - that is what takes place.
				*
				* For similar reasons, we auto-submit the form,
				* rather than let the user click on the 'upload' button.
				* See the accept() method for more.
				*/
				//setProfilePixUrl(response.imageUrl);
				//setMainNavUserPixUrl(response.imageUrl);
	/*			
				activateImageResizer({
					'imageUrl'                 : response.imageUrl, 
					'uniqueIDPrefix'           : '<?php echo $cropper_unique_id_prefix; ?>',
					'displayCropWindowAsPopup' : false, //true,
					'maxWidth'                 : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?>,
					'maxHeight'                : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?>,
					'minWidth'                 : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?>,
					'minHeight'                : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?>,
					'popupWidth'               : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?> + 280,
					'popupHeight'              : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?> + 260,
					'previewContainerID'       : 'image-crop-preview' //'user-photo-container'
				});
			} 
			
			hideDropZonePreview();
		}
		*/
		
		queuecomplete: function()
		{
			if(myDropzone.errors) 
			{
				if( myDropzone.errorType == 'invalid_upload_directory' )
				{
					displayImageUploadStatusMessage('Unable to create image directory. Please try again later');
				}
				else
				{
					displayImageUploadStatusMessage(myDropzone.errorMsg);
				}
			}
			else
			{
				//displayImageUploadStatusMessage('Image successfully uploaded');
			}
		}
	});
	
	/*
	* success is called on each file processed, 
	* Once the backend server returns a response,
	* it matters not whether the backend server response is positive or negative (i.e, file uploaded or not).
	* It is called to indicate that the file has been handed on to the backend processor
	* (and the processor has returned)
	*/
	myDropzone.on('success', function(file, response){ 
		//onqueuecomplete() doesn't take the file and response parameters
		//so, manually create the flags as part of "this" dropzone object
		if(response.error)
		{
			myDropzone.error = true;
			myDropzone.message = response.message;
			myDropzone.errorType = response.errorType;
		}
		else
		{
			myDropzone.error = false;
			console.log(response);
			$Style('upload-image-button').visibility = 'hidden';
			
			/*
			* don't set the images here, let them be set on successful cropping of the image
			* so that the user doesn't first see this, and then - when cropping is complete - the cropped version
			* an effect that is not so nice, since user is supposed to see the operation as one (atomic) operation
			* not as a two-step upload first, and crop later, 
			* although - under the hood - that is what takes place.
			*
			* For similar reasons, we auto-submit the form,
			* rather than let the user click on the 'upload' button.
			* See the accept() method for more.
			*/
			//setProfilePixUrl(response.imageUrl);
			//setMainNavUserPixUrl(response.imageUrl);
			
			activateImageResizer({
				'imageUrl'                 : response.imageUrl, 
				'uniqueIDPrefix'           : '<?php echo $cropper_unique_id_prefix; ?>',
				'displayCropWindowAsPopup' : true,
				'maxWidth'                 : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?>,
				'maxHeight'                : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?>,
				'minWidth'                 : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?>,
				'minHeight'                : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?>,
				'popupWidth'               : <?php echo PROFILE_IMAGE_WIDTH_STANDARD; ?> + 280,
				'popupHeight'              : <?php echo PROFILE_IMAGE_HEIGHT_STANDARD; ?> + 268,
				'previewContainerID'       : 'image-crop-preview' //'user-photo-container'
			});
		} 
		
		hideDropZonePreview();
	});
	
	function displayImageUploadStatusMessage(msg)
	{
		alert(msg);
	}
	
	function hideDropZonePreview()
	{
		document.querySelectorAll('.dz-preview')[0].style.display = 'none';
	}
	
	function hideDropZoneErrorAndSuccessMarks()
	{
		document.querySelectorAll('.dz-success-mark')[0].style.display = 'none';
		document.querySelectorAll('.dz-error-mark')[0].style.display = 'none';
	}
	
   </script>
   <style>.dz-preview, .dz-success-mark, .dz-error-mark{display:none;}</style>
  </div>
 
  <div class="col-md-9">
   <div class="row tab" id="personal-info-tab">
    <h4 class="tab-header text-centered pb10">Personal Info</h4>
    <div class="tab-content">
     <form>
	  <div class="form-group">
	   <label>Firstname</label>
	   <input class="form-control" id="user-firstname-field" placeholder="Enter your Firstname" value="<?php echo get_array_member_value( $user_data, 'firstname', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>Lastname</label>
	   <input class="form-control" id="user-lastname-field" placeholder="Enter your Lastname" value="<?php echo get_array_member_value( $user_data, 'lastname', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	
	  <div class="form-group">
	   <label>Username</label>
	   <input class="form-control" id="username-field" placeholder="Enter your username" value="<?php echo $current_user->get('username'); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>Email</label>
	   <input class="form-control" id="user-email-field" placeholder="Enter your email" value="<?php echo get_array_member_value( $user_data, 'email', '' ); ?>" type="email">
	   <span class="pull-right">
	    <input type="checkbox" id="email-visibility-modifier" style="position:relative; top:2px;" 
	     <?php echo ( get_array_member_value( $user_data, 'email-visibility') == 'public' ? 'checked="checked"' : ''); ?>>Make my email publicly visible
	   </span>
	  </div>
	  <div class="clear">&nbsp;</div>
	  
	  <div class="form-group">
	   <label>Location</label>
	   <input class="form-control" id="user-location-field" placeholder="Enter your location here" value="<?php echo get_array_member_value( $user_data, 'location', '' ); ?>"/>
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>Profession</label>
	   <input class="form-control" id="user-profession-field" placeholder="Enter your profession" value="<?php echo get_array_member_value( $user_data, 'profession', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>Description</label>
	   <?php 
	   PageModel::add_template_fragment( 'markdown-editor', array(
		'id_postfix'       => '', //'-second',
		'placeholder'      => 'Give a description of yourself',
		'value'            => get_array_member_value( $user_data, 'about'),
		'cols'             => '80',
		'style'            => 'width:620px;',
		'button_bar_style' => 'width:620px;'
	   ) ); 
	  ?>
	  <?php PageModel::add_template_fragment( 'markdown-preview', array('style'=>'width:620px;') ); ?>
	  <!--<textarea class="form-control resize-vertical" id="user-description-field" rows="7" placeholder="Say something about yourself"><?php echo get_array_member_value( $user_data, 'about', '' ); ?></textarea>-->
	  </div>
     </form>
    </div>
   </div>
  
   <div class="row tab no-display" id="education-info-tab">
    <h4 class="tab-header text-centered pb10">Education info</h4>
    <div class="tab-content">
     <form id="education-info-data-container">
	  <?php $edu_section_counter = 1; ?>
	 
	  <?php $user_education_data = get_user_education_data($user_id); ?>
	  <?php if( !empty($user_education_data) ): ?>
	 
	  <?php foreach($user_education_data AS $edu_data): ?>
	  <section id="edu-data-<?php echo $edu_section_counter; ?>-container">
	   <div class="form-group">
	    <label>Name of Institution</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-institution-name-field" value="<?php echo $edu_data['institution']; ?>" />
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>Certification received</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-certification-field" value="<?php echo $edu_data['certification']; ?>" />
	   </div>
	   <div class="clear">&nbsp;</div>
	
	   <div class="form-group">
	    <label>Start Date</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-start-date-field" type="date" value="<?php echo $edu_data['start_date']; ?>" />
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>End Date</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-end-date-field" type="date" value="<?php echo $edu_data['end_date']; ?>" />
	   </div>
	   <input type="hidden" data-info-id="<?php echo $edu_section_counter; ?>" id="education-record-id-field-<?php echo $edu_section_counter; ?>" class="education-record-id-field" value="<?php echo $edu_data['record_id']; ?>" />
	  </section>
	 
	  <hr class="data-info-separator"/>
	 
	  <?php ++$edu_section_counter; ?>
	  <?php endforeach; ?>
	 
	  <?php else: ?>
	
	  <section id="edu-data-<?php echo $edu_section_counter; ?>-container">
	   <div class="form-group">
	    <label>Name of Institution</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-institution-name-field" placeholder="" value="">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>Certification received</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-certification-field" placeholder="" value="">
	   </div>
	   <div class="clear">&nbsp;</div>
	
	   <div class="form-group">
	    <label>Start Date</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-start-date-field" placeholder="" value="" type="date">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>End Date</label>
	    <input data-info-id="<?php echo $edu_section_counter; ?>" class="form-control education-end-date-field" placeholder="" value="" type="date">
	   </div>
	  </section>
	 
	  <?php endif; ?>
     </form>
	 <div style="min-height:20px;"></div>
	 <button class="btn btn-primary float-right" title="Add More" onclick="createEducationDataFields();">+</button>
	 <div class="clear">&nbsp;</div>
    </div>
   </div>
  
   <div class="row tab no-display" id="experience-info-tab">
    <h4 class="tab-header text-centered pb10">Experience info</h4>
    <div class="tab-content">
     <form id="work-info-data-container">
	  <?php $work_section_counter = 1; ?>
	  <?php $user_work_data = get_user_work_experience_data($user_id); ?>
	  <?php if( !empty($user_work_data) ): ?>
	 
	  <?php foreach($user_work_data AS $work_data): ?>
	  <section id="work-data-<?php echo $work_section_counter; ?>-container">
	   <div class="form-group">
	    <label>Name of Employer</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-employer-name-field" value="<?php echo $work_data['employer']; ?>">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>Job Title</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-job-title-field" value="<?php echo $work_data['job_title']; ?>">
	   </div>
	   <div class="clear">&nbsp;</div>
	
	   <div class="form-group">
	    <label>Start Date</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-start-date-field" type="date" value="<?php echo $work_data['start_date']; ?>">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>End Date</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-end-date-field" type="date" value="<?php echo $work_data['start_date']; ?>">
	   </div>
	   <input type="hidden" data-info-id="<?php echo $work_section_counter; ?>" id="work-record-id-field-<?php echo $work_section_counter; ?>" class="work-record-id-field" value="<?php echo $work_data['record_id']; ?>" />
	  </section>
	 
	  <hr class="data-info-separator"/>
	 
	  <?php ++$work_section_counter; ?>
	  <?php endforeach; ?>
	  
	  <?php else: ?>
	 
	  <section id="work-data-<?php echo $work_section_counter; ?>-container">
	   <div class="form-group">
	    <label>Name of Employer</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-employer-name-field" placeholder="" value="">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>Job Title</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-job-title-field" placeholder="" value="">
	   </div>
	   <div class="clear">&nbsp;</div>
	
	   <div class="form-group">
	    <label>Start Date</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-start-date-field" placeholder="" value="" type="date">
	   </div>
	   <div class="clear">&nbsp;</div>
	 
	   <div class="form-group">
	    <label>End Date</label>
	    <input data-info-id="<?php echo $work_section_counter; ?>" class="form-control work-end-date-field" placeholder="" value="" type="date">
	   </div>
	  </section>
	 
	  <?php endif; ?>
     </form>
	 <div style="min-height:20px;"></div>
	 <button class="btn btn-primary float-right" title="Add More" onclick="createExperienceDataFields();">+</button>
	 <div class="clear">&nbsp;</div>
    </div>
   </div>
  
   <!--
   <div class="row tab no-display" id="skills-info-tab">
    <h3 class="tab-header text-centered">Skills info</h3>
    <div class="tab-content">
   
    </div>
   </div>
   -->
  
   <div class="row tab no-display" id="social-info-tab">
    <h4 class="tab-header text-centered pb10">Social Profiles</h4>
    <div class="tab-content">
     <form>
	  <div class="form-group">
	   <label>Website</label>
	   <input class="form-control" id="website-url-field" placeholder="http://mywebsite.com" value="<?php echo get_array_member_value( $user_data, 'website-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
      <div class="form-group">
	   <label>Facebook Page</label>
	   <input class="form-control" id="facebook-url-field" placeholder="http://facebook.com/profile-url" value="<?php echo get_array_member_value( $user_data, 'facebook-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>Google+ Page</label>
	   <input class="form-control" id="google-plus-url-field" placeholder="plus.google.com/profile-url" value="<?php echo get_array_member_value( $user_data, 'google-plus-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	
	  <div class="form-group">
	   <label>Instagram Page</label>
	   <input class="form-control" id="instagram-url-field" placeholder="http://instagram.com/profile-url" value="<?php echo get_array_member_value( $user_data, 'instagram-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>LinkedIn Page</label>
	   <input class="form-control" id="linkedin-url-field" placeholder="http://linkedin.com/profile-url" value="<?php echo get_array_member_value( $user_data, 'linkedin-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	
	  <div class="form-group">
	   <label>Twitter Page</label>
	   <input class="form-control" id="twitter-url-field" placeholder="@twitter-handle" value="<?php echo get_array_member_value( $user_data, 'twitter-url', '' ); ?>">
	  </div>
	  <div class="clear">&nbsp;</div>
	 
	  <div class="form-group">
	   <label>YouTube Page</label>
	   <input class="form-control" id="youtube-url-field" placeholder="http://youtube.com/profile-url" value="<?php echo get_array_member_value( $user_data, 'youtube-url', '' ); ?>">
	  </div>
     </form>
    </div>
   </div>
   
   <div class="row tab no-display" id="password-info-tab">
    <h4 class="tab-header text-centered pb10">Change your password</h4>
    <div class="tab-content">
     <form>
	  <div class="form-group">
	   <label>Current Password</label>
	   <input class="form-control" id="current-password-field" placeholder="Enter you current password" type="password">
	  </div>
	  <div class="clear">&nbsp;</div>
	  
	  <div class="form-group">
	   <label>New Password</label>
	   <input class="form-control" id="new-password-field" placeholder="Enter your new password" type="password">
	  </div>
	  <div class="clear">&nbsp;</div>
	  
	  <div class="form-group">
	   <label>Confirm new password</label>
	   <input class="form-control" id="new-password-confirmation-field" placeholder="Re-enter your new password" type="password">
	  </div>
	  <div class="clear">&nbsp;</div>
	 </form>
    </div>
   </div>
   
   <div class="row" style="margin-top:10px;">
    <div class="col-md-4" style="padding-left:0;"><button class="btn float-left" id="previous-tab-button" title="Back to previous tab">Previous</button></div>
    <div class="col-md-4 text-centered">	
     <button class="btn btn-primary mr5 bg-right bg-no-repeat pr25 pl25" id="save-and-continue-button" title="Save settings and continue to next tab">Save and Continue</button>
	 <span class="position-relative processing-bg bg-no-repeat bg-right" style="top:5px; visibility:hidden;"></span>
	</div>
    <div class="col-md-4"><button class="btn float-right" id="skip-current-tab-button" title="Skip to next tab">Skip</button></div>
   </div>
   <div class="row" style="margin-top:10px;">
    <div class="col-md-4"></div>
	<div class="col-md-4 text-centered" id="status-message"></div>
	<div class="col-md-4"></div>
   </div>
   
  </div>
 </div>

 <div class="col-md-3">
   <div class="row no-display" style="margin-left:10px; margin-right:0; border:none; 1px solid #eee;">
    <h4 class="data-header text-centered pb10 mb10" style="color:#555; border:1px solid #eee; border-bottom:1px solid #ccc;">Easy Navigation</h4>
   </div>
   <div class="row no-border" id="tab-nav-row" style="position:fixed; margin-left:10px; margin-right:10px;">
    <div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="personal-info-tab">Personal Info</div>
    <div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="education-info-tab">Education</div>
    <div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="experience-info-tab">Work Experience</div>
    <!--<div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="skills-info-tab">Skills</div>-->
    <div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="social-info-tab">Social Profiles</div>
	<div class="col-md-12 tab-nav text-centered theme-blue cursor-pointer" data-tab-content="password-info-tab">Password update</div>
   </div>
 </div>
 
</div> 
</div> 

<script src="<?php echo SITE_URL; ?>/js/lib/tab-manager/tab-manager.js"></script>
<script>
(function(){

var statusMsgField = 'status-message';
autoSelectTab();
Site.Event.attachListener('tab-nav-row', 'click', function(e){
	TabManager.updateTabs(Site.Event.getEventTarget(e));
	updateUrl();
});
Site.Event.attachListener(window, 'popstate', autoSelectTab); //handle clicking of browser's 'back' button
Site.Event.attachListener('skip-current-tab-button', 'click', function(e){ TabManager.next(); updateUrl(); });
Site.Event.attachListener('previous-tab-button', 'click', function(e){ TabManager.previous(); updateUrl(); })
Site.Event.attachListener('save-and-continue-button', 'click', function(e){
	
	Site.Event.cancelDefaultAction(e);
	disable('save-and-continue-button');
	showProcessing('save-and-continue-button');
	
	var activeTab = TabManager.getActiveTab();
	
	switch(activeTab)
	{
		case 'personal-info-tab'   : savePersonalInfo();   break;
		case 'education-info-tab'  : saveEducationInfo();  break;
		case 'experience-info-tab' : saveExperienceInfo(); break;
		case 'skills-info-tab'     : saveSkillsInfo();     break;
		case 'password-info-tab'   : savePasswordInfo();   break;
		case 'social-info-tab'     : saveSocialInfo();     break;
	}
});

function updateUrl()
{
	var activeTab = TabManager.getActiveTab();
	var selectedTabName = 'personal';
	
	switch( activeTab )
	{
		case 'education-info-tab'  : selectedTabName = 'education';  break;
		case 'experience-info-tab' : selectedTabName = 'experience'; break;
		case 'social-info-tab'     : selectedTabName = 'social';     break;
		case 'password-info-tab'   : selectedTabName = 'password';   break;
		default                    : selectedTabName = 'personal';   break;
	}
	
	history.pushState(null, "", '?tab=' + selectedTabName);
}

function autoSelectTab()
{
	var selectedTabNum  = 0;
	
	switch( Site.Util.getQueryStringParameterValue('tab') )
	{
		case 'education'  : selectedTabNum = 1; break;
		case 'experience' : selectedTabNum = 2; break;
		case 'social'     : selectedTabNum = 3; break;
		case 'password'   : selectedTabNum = 4; break;
		default           : selectedTabNum = 0; break;
	}
	TabManager.moveToTab( selectedTabNum );
}

function savePersonalInfo()
{
	var firstname       = $O('user-firstname-field').value;
	var lastname        = $O('user-lastname-field').value;
	var username        = $O('username-field').value;
	var email           = $O('user-email-field').value;
	var emailVisibility = $O('email-visibility-modifier').checked ? 'public' : 'private'
	var userLocation    = $O('user-location-field').value;
	var profession      = $O('user-profession-field').value;
	//var description   = $O('user-description-field').value;
	var description     = $O('wmd-input').value;
	
	var postData = 'action=update_user_data' +
	'&firstname='        + firstname       +
	'&lastname='         + lastname        +
	'&username='         + username        +
	'&email='            + email           +
	'&email_visibility=' + emailVisibility +
	'&location='         + userLocation    +
	'&profession='       + profession      +
	'&about='            + description
	
	Site.Util.runAjax({
		requestMethod        : 'POST',
		requestURL           : '',
		requestData          : postData,
		timeoutAfter         : 60,
		debugCallback        : function(reply){ console.log(reply); },
		readyStateCallback   : function(){},
		errorCallback        : function(xhrObject, aborted)
		{ 
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		},
		successCallback      : function(reply){
			var response = Site.Util.parseAjaxResponse(reply.parsedValue);
			if(response.error)
			{
				displayStatusMessage(statusMsgField, response.message, 'error');
				if(response.errorType == 'UnauthenticatedUserError')
				{
					location.reload();
				}
			}
			else if(response.success)
			{
				TabManager.next(); //TabManager.updateTabs( TabManager.getTabNavs()[1] );
				updateUrl();
			}
			
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		}
	});
}

function saveEducationInfo()
{ 
	var institutionNames = document.querySelectorAll('.education-institution-name-field');
	var certifications   = document.querySelectorAll('.education-certification-field');
	var startDates       = document.querySelectorAll('.education-start-date-field');
	var endDates         = document.querySelectorAll('.education-end-date-field');
	var recordIDS        = document.querySelectorAll('.education-record-id-field'); //only available after first creation of data
	
	var educationData = [];
	
	for(var i = 0, len = institutionNames.length; i < len; i++)
	{
		/* We don't use this because the user could add more fields, and delete them
		* and add more, making the incrementing-number system out of sync, 
		* thus affecting the final educationData 
		var fieldsCounter = i + 1;
		educationData.push({
			'institution'   : $O('edu-data-' + fieldsCounter + '-institution-name-field').value,
			'certification' : $O('edu-data-' + fieldsCounter + '-certification-field').value,
			'start_date'    : $O('edu-data-' + fieldsCounter + '-start-date-field').value,
			'end_date'      : $O('edu-data-' + fieldsCounter + '-end-date-field').value
		});
		*/
		
		var fieldsCounter = institutionNames[i].getAttribute('data-info-id');
		var institution   = institutionNames[i].getAttribute('data-info-id') == fieldsCounter ? Site.Util.trim(institutionNames[i].value) : '';
		var certification = certifications[i].getAttribute('data-info-id')   == fieldsCounter ? Site.Util.trim(certifications[i].value)   : '';
		var startDate     = startDates[i].getAttribute('data-info-id')       == fieldsCounter ? Site.Util.trim(startDates[i].value)       : '';
		var endDate       = endDates[i].getAttribute('data-info-id')         == fieldsCounter ? Site.Util.trim(endDates[i].value)         : '';
		var recordID      = 0;
		
		if( Site.Util.getObjectLength(recordIDS)  > 0 )
		{   
			recordID = ( (recordIDS[i] && recordIDS[i].getAttribute('data-info-id') == fieldsCounter) ? Site.Util.trim(recordIDS[i].value) : 0 );
		}
	
		educationData.push({
			'field_id'      : fieldsCounter, 
			'institution'   : institution, 
			'certification' : certification, 
			'start_date'    : startDate, 
			'end_date'      : endDate,
			'record_id'     : recordID
		});
	}
	
	//console.log(educationData); enableSaveAndContinueButton(); return;
	
	Site.Util.runAjax({
		requestMethod        : 'POST',
		requestURL           : '',
		requestData          : 'action=update_education_data' + '&data=' + encodeURIComponent( JSON.stringify(educationData) ),
		timeoutAfter         : 60,
		debugCallback        : function(reply){ console.log(reply); },
		readyStateCallback   : function(){},
		errorCallback        : function(xhrObject, aborted)
		{ 
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		},
		successCallback      : function(reply)
		{
			var response = Site.Util.parseAjaxResponse(reply.parsedValue);
			
			if(response.error)
			{
				displayStatusMessage(statusMsgField, response.message, 'error');
				if(response.errorType == 'UnauthenticatedUserError')
				{
					location.reload();
				}
			}
			else if(response.success)
			{   
				var records = JSON.parse(response.records);
				
				for(var x in records)
				{
					//add the record-id hidden input field to each education data section
					// so that on subsequent submits, the data is updated, rather than duplicating the addition
					currFieldID  = records[x]['fieldID'];
					currRecordID = records[x]['recordID'];
					
					var hiddenFieldID     = 'education-record-id-field-' + currFieldID;
					
					if( !$O(hiddenFieldID) )
					{
						var hiddenField       = document.createElement('input');
						hiddenField.type      = 'hidden';
						hiddenField.id        = hiddenFieldID;
						hiddenField.className = 'education-record-id-field';
						hiddenField.setAttribute('data-info-id', currFieldID);
						hiddenField.value = currRecordID;
						
						$O('edu-data-' + currFieldID + '-container').appendChild(hiddenField);
					}
				}
				
				TabManager.next(); //TabManager.updateTabs( TabManager.getTabNavs()[2] );
				updateUrl();
			}
			
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		}
	});
}

function saveExperienceInfo()
{ 
	var employers   = document.querySelectorAll('.work-employer-name-field');
	var jobTitles   = document.querySelectorAll('.work-job-title-field');
	var startDates  = document.querySelectorAll('.work-start-date-field');
	var endDates    = document.querySelectorAll('.work-end-date-field');
	var recordIDS   = document.querySelectorAll('.work-record-id-field'); //only available after first creation of data
	
	var experienceData = [];
	
	for(var i = 0, len = employers.length; i < len; i++)
	{
		var fieldsCounter = employers[i].getAttribute('data-info-id');
		var employer      = employers[i].getAttribute('data-info-id')  == fieldsCounter ? Site.Util.trim(employers[i].value)  : '';
		var jobTitle      = jobTitles[i].getAttribute('data-info-id')  == fieldsCounter ? Site.Util.trim(jobTitles[i].value)  : '';
		var startDate     = startDates[i].getAttribute('data-info-id') == fieldsCounter ? Site.Util.trim(startDates[i].value) : '';
		var endDate       = endDates[i].getAttribute('data-info-id')   == fieldsCounter ? Site.Util.trim(endDates[i].value)   : '';
		var recordID      = 0;
		
		if( Site.Util.getObjectLength(recordIDS)  > 0 )
		{   
			recordID = ( (recordIDS[i] && recordIDS[i].getAttribute('data-info-id') == fieldsCounter) ? Site.Util.trim(recordIDS[i].value) : 0 );
		}
	
		experienceData.push({
			'field_id'   : fieldsCounter, 
			'employer'   : employer, 
			'job_title'  : jobTitle, 
			'start_date' : startDate, 
			'end_date'   : endDate,
			'record_id'  : recordID
		});
	}
	
	//console.log(experienceData); enableSaveAndContinueButton(); return;
	
	Site.Util.runAjax({
		requestMethod        : 'POST',
		requestURL           : '',
		requestData          : 'action=update_experience_data' + '&data=' + encodeURIComponent( JSON.stringify(experienceData) ),
		timeoutAfter         : 60,
		debugCallback        : function(reply){ console.log(reply); },
		readyStateCallback   : function(){},
		errorCallback        : function(xhrObject, aborted)
		{ 
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		},
		successCallback      : function(reply)
		{
			var response = Site.Util.parseAjaxResponse(reply.parsedValue);
			
			if(response.error)
			{
				displayStatusMessage(statusMsgField, response.message, 'error');
				if(response.errorType == 'UnauthenticatedUserError')
				{
					location.reload();
				}
			}
			else if(response.success)
			{   
				var records = JSON.parse(response.records);
				
				for(var x in records)
				{
					//add the record-id hidden input field to each education data section
					// so that on subsequent submits, the data is updated, rather than duplicating the addition
					currFieldID  = records[x]['fieldID'];
					currRecordID = records[x]['recordID'];
					
					var hiddenFieldID     = 'work-record-id-field-' + currFieldID;
					
					if( !$O(hiddenFieldID) )
					{
						var hiddenField       = document.createElement('input');
						hiddenField.type      = 'hidden';
						hiddenField.id        = hiddenFieldID;
						hiddenField.className = 'work-record-id-field';
						hiddenField.setAttribute('data-info-id', currFieldID);
						hiddenField.value = currRecordID;
						
						$O('work-data-' + currFieldID + '-container').appendChild(hiddenField);
					}
				}
				
				TabManager.next();
				updateUrl();
			}
			
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		}
	});
}

function saveSocialInfo()
{
	var postData = 'action=update_user_social_data'          +
	'&website_url='      + $O('website-url-field').value     +
	'&facebook_url='     + $O('facebook-url-field').value    +
	'&google_plus_url='  + $O('google-plus-url-field').value +
	'&instagram_url='    + $O('instagram-url-field').value   +
	'&linkedin_url='     + $O('linkedin-url-field').value    +
	'&twitter_url='      + $O('twitter-url-field').value     +
	'&youtube_url='      + $O('youtube-url-field').value
	
	Site.Util.runAjax({
		requestMethod        : 'POST',
		requestURL           : '',
		requestData          : postData,
		timeoutAfter         : 60,
		debugCallback        : function(reply){ console.log(reply); },
		readyStateCallback   : function(){},
		errorCallback        : function(xhrObject, aborted)
		{
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		},
		successCallback      : function(reply){
			var response = Site.Util.parseAjaxResponse(reply.parsedValue);
			if(response.error)
			{
				displayStatusMessage(statusMsgField, response.message, 'error');
				if(response.errorType == 'UnauthenticatedUserError')
				{
					location.reload();
				}
			}
			else if(response.success)
			{
				TabManager.next();
				updateUrl();
			}
			
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		}
	});
}

function savePasswordInfo()
{
	var currPass = $O('current-password-field').value;
	var newPass  = $O('new-password-field').value;
	var newPass2 = $O('new-password-confirmation-field').value;
	
	var postData = ''             + 
	'action=update_user_password' +
	'&current_password='          + currPass  +
	'&new_password='              + newPass   +
	'&new_password_confirm='      + newPass2
	
	Site.Util.runAjax({
		requestMethod        : 'POST',
		requestURL           : '',
		requestData          : postData,
		timeoutAfter         : 60,
		debugCallback        : function(reply){ console.log(reply); },
		readyStateCallback   : function(){},
		errorCallback        : function(xhrObject, aborted)
		{ 
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		},
		successCallback      : function(reply){
			var response = Site.Util.parseAjaxResponse(reply.parsedValue);
			if(response.error)
			{
				displayStatusMessage(statusMsgField, response.message, 'error');
				if(response.errorType == 'UnauthenticatedUserError')
				{
					location.reload();;
				}
			}
			else if(response.success)
			{
				//TabManager.next();
				//updateUrl();
				var msg = 'Password successfully updated';
				
				if( (typeof response.reauthenticateUser !== 'undefined') && (response.reauthenticateUser) )
				{
					msg += '<br>You will be logged out in a moment so that you can login again';
					setTimeout( function(){ location.reload(); }, 5000 );
				}
				
				displayStatusMessage(statusMsgField, msg);
			}
			
			hideProcessing('save-and-continue-button');
			enable('save-and-continue-button');
		}
	});
}

function createEducationDataFields()
{
	if(typeof createEducationDataFields.fieldsCounter === 'undefined')
	{
		createEducationDataFields.fieldsCounter = parseInt('<?php echo $edu_section_counter; ?>');
	}
	
	var fieldsCounter = createEducationDataFields.fieldsCounter;
	
	var htmlStr = [
	
	//'<h3>' + fieldsCounter + '</h3>',
	//'<section id="edu-data-' + fieldsCounter + '-container">',
	 '<hr class="data-info-separator" id="edu-data-' + fieldsCounter + '-separator">',
	 '<span class="pull-right cursor-pointer edu-container-dismisser" data-counter="' + fieldsCounter + '" title="remove" id="edu-data-' + fieldsCounter + '-container-dismisser" style="border:1px solid #ccc; padding:2px 5px; display:inline-block;">x</span>',
	 '<div class="clear"></div>',
	 
	 '<div class="form-group">',
	  '<label>Name of Institution</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control education-institution-name-field" placeholder="">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	 
	 '<div class="form-group">',
	  '<label>Certification received</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control education-certification-field" placeholder="">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	
	 '<div class="form-group">',
	  '<label>Start Date</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control education-start-date-field" placeholder="" type="date">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	 
	 '<div class="form-group">',
	  '<label>End Date</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control education-end-date-field" placeholder="" type="date">',
	 '</div>',
	//'</section>'
	].join('');
	 
	var section = document.createElement('section');
	section.id = 'edu-data-' + fieldsCounter + '-container';
	section.innerHTML = htmlStr;
	$O('education-info-data-container').appendChild(section);
	
	/* this removes already entered data in form fields: 
	// $O('education-info-data-container').innerHTML += htmlStr; is equivalent to 
	// $O('education-info-data-container').innerHTML = $O('education-info-data-container').innerHTML + htmlStr;
	// effectively recreating the innerHTML of the $O('education-info-data-container') element, 
	// but this newly created innerHTML does not retain the value typed in by the user in the form field, 
	// since that value isn't part of the innerHTML of the $O('education-info-data-container') element
	// so we use append method of the $O('education-info-data-container') element instead
	//$O('education-info-data-container').innerHTML += htmlStr;
	*/
	
	/* This wasn't working previously for the same reason above for why already entered data in form fields are cleared:
	* because on each call to $O('education-info-data-container').innerHTML += htmlStr, the previous handlers are removed, 
	* (
	* So, I had to resort to the longer/more-complicated "listener" function below 
	* because it leads to attaching a listener multiple times to a single remove button, 
	* resulting in the error: "Failed to execute 'removeChild' on 'Node': parameter 1 is not of type 'Node'."),
	* since onclick, it calls the function that does the removal the number of times the click event listener was added
	* but given that the node has already been removed the first time the listener function was called, we get the error above on subsequent
	* calls to remove an already removed Node.
	* )
	* However, now using the append fixes it.
	* @date : April 17, 2016 17:03 hrs
	*/
	(function(fieldID){ 
		Site.Event.attachListener('edu-data-' + fieldID + '-container-dismisser', 'click', function(){
			$O('education-info-data-container').removeChild($O('edu-data-' + fieldID + '-container'));
		});
		
	})(fieldsCounter);
	
	/*
	(function listener(){
		
		var dismissers = document.querySelectorAll('.edu-container-dismisser');
		
		if(typeof listener.listenForClick !== 'function')
		{
			listener.listenForClick = function(dismisser)
			{
				var fieldID = dismisser.getAttribute('data-counter');
			
				Site.Event.attachListener('edu-data-' + fieldID + '-container-dismisser', 'click', function(){
					$O('education-info-data-container').removeChild($O('edu-data-' + fieldID + '-container'));
				});
			}
		}
		
		for(var i = 0, len = dismissers.length; i < len; i++)
		{
			listener.listenForClick(dismissers[i]); 
		}
		
	})();
	*/
	
	++createEducationDataFields.fieldsCounter;
}

function createExperienceDataFields()
{
	if(typeof createExperienceDataFields.fieldsCounter === 'undefined')
	{
		createExperienceDataFields.fieldsCounter = parseInt('<?php echo $work_section_counter; ?>');
	}
	
	var fieldsCounter = createExperienceDataFields.fieldsCounter;
	
	var htmlStr = [
	 '<hr class="data-info-separator" id="work-data-' + fieldsCounter + '-separator">',
	 '<span class="pull-right cursor-pointer work-container-dismisser" data-counter="' + fieldsCounter + '" title="remove" id="work-data-' + fieldsCounter + '-container-dismisser" style="border:1px solid #ccc; padding:2px 5px; display:inline-block;">x</span>',
	 '<div class="clear"></div>',
	 
	 '<div class="form-group">',
	  '<label>Name of Employer</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control work-employer-name-field" placeholder="">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	 
	 '<div class="form-group">',
	  '<label>Job Title</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control work-job-title-field" placeholder="">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	
	 '<div class="form-group">',
	  '<label>Start Date</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control work-start-date-field" placeholder="" type="date">',
	 '</div>',
	 '<div class="clear">&nbsp;</div>',
	 
	 '<div class="form-group">',
	  '<label>End Date</label>',
	  '<input data-info-id="' + fieldsCounter + '" class="form-control work-end-date-field" placeholder="" type="date">',
	 '</div>',
	
	].join('');
	 
	var section = document.createElement('section');
	section.id = 'work-data-' + fieldsCounter + '-container';
	section.innerHTML = htmlStr;
	$O('work-info-data-container').appendChild(section);
	
	(function(fieldID){ 
		Site.Event.attachListener('work-data-' + fieldID + '-container-dismisser', 'click', function(){
			$O('work-info-data-container').removeChild($O('work-data-' + fieldID + '-container'));
		});
		
	})(fieldsCounter);
	
	++createExperienceDataFields.fieldsCounter;
}

})();
</script>

<?php PageModel::add_page_footer(); ?>
<?php PageModel::close_page(); ?>