<?php
if( ($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_FILES['file']['name']) )
{
	$target_dir = dirname( __FILE__ ). '/uploads/';
	$tmp_dir    = $target_dir. '/tmp/';
	
	is_dir($tmp_dir) || mkdir($tmp_dir, 0777, $recursive = true);

	if(!is_dir($tmp_dir))
	{
		$return_data = array('error'=>true, 'message'=>'Error creating the storage directory.', 'errorType'=>'invalid_upload_directory');
	}

	else
	{
		$curr_file_name = $_FILES['file']['name'];
		$temp_file      = $_FILES['file']['tmp_name']; 
		$target_file    = $target_dir. '/'. $curr_file_name;
		
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
			
			$return_data = array('success'=>true, 'imageUrl'=>$image_url);
		}
	}
	
	create_json_string($return_data, true);
	exit;
}

else if( isset($_POST['operation']) && ($_POST['operation'] == 'create_image_thumbnail') )
{
	$image   = $_POST['img_src'];
	$rel_dir = '/resources/uploads/';
	$thumb   = 'thumb_'. $image;
	
	//create thumbnail
	ImageCropper::create_image_thumbnail(array('thumbnail_directory'=>$rel_dir, 'thumbnail_name'=>$thumb, 'save_original_image'=>true));
	
	//crop the image to specified dimensions beginning at specified co-ordinates
	ImageCropper::resize_image(array(
		'source_image'           => $rel_dir. '/'. $thumbnail,
		'destination_directory'  => $rel_dir,
		'destination_image_name' =>'mini_.'. $posted_image,
		'width'                  => PROFILE_IMAGE_WIDTH_MINI,
		'height'                 => PROFILE_IMAGE_HEIGHT_MINI,
		'x'                      => $_POST['x'], 
		'y'                      => $_POST['y'],
		'save_source_image'      => true
	));
	
	$return_data = array('success'=>true);
	
	create_json_string($return_data, true);
	exit;
}