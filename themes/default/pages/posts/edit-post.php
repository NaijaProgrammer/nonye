<?php 
$request   = new PathModel();
$post_id = !empty($request->action) ? $request->action : '';
?>
<?php $curr_url = generate_url(array('controller'=>'course')). '/'. $post_id. '/edit'; ?>
<?php $page_instance->authenticate_user( array('redirect_url'=>$curr_url, 'current_url'=>$curr_url ) ); ?>
<?php //image upload handler
if( ($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_FILES['file']['name']) ) {
	$response_data = array();
	$rel_dir       = 'resources/uploads/posts/featured-images';
	$target_dir    = rtrim(SITE_DIR, '/'). '/'. $rel_dir. '/';
	
	is_dir($target_dir) || mkdir($target_dir, 0777, $recursive = true);

	if( !is_dir($target_dir) ) {
		$response_data = array('error'=>true, 'message'=>'Error creating the image storage folder. Please try again', 'errorType'=>'invalidUploadDirectory');
	}

	$file_names   = $_FILES['file']['name'];
	$file_types   = $_FILES['file']['type'];
	$tmp_names    = $_FILES['file']['tmp_name'];
	$file_errors  = $_FILES['file']['error'];
	$file_sizes   = $_FILES['file']['size'];
	$num_of_files = count($file_names);

	$curr_file_name = $_FILES['file']['name'];
	$temp_file      = $_FILES['file']['tmp_name']; 
	$target_file    = rtrim($target_dir, '/'). '/'. $curr_file_name;
	$image_url      = rtrim(SITE_URL, '/'). '/'. $rel_dir. '/'. $curr_file_name;
	
	if( move_uploaded_file($temp_file, $target_file) ) {
		$response_data = array('success'=>true, 'imageUrl'=>$image_url);
	}
	
	//header("Content-type: application/json");
	//dropzone.js requires text/string, even though the string is in JSON format, 
	//otherwise, we get : Invalid JSON response from server
	echo create_json_string($response_data, false); 
	exit;
}
?>
<?php
$page_instance->add_header(array(
	'page_title'       => $page_title,
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => $robots_value,
	'open_graph_data'  => $open_graph_data,
	'current_user'     => $current_user //coming from the app-controller class
));

$page_instance->add_stylesheets( array() );
$page_instance->add_nav();
?>
<?php 
$post    = PostModel::get_post_instance($post_id);
$title   = $post->get('title');
$desc    = $post->get('excerpt');
$content = $post->get('content');
$status  = $post->get('status');
$post_image_url = get_post_image_url($post_id);
?>
<div class="container main-container" style="width:100%; padding-left:0; padding-right:0;">
 <h2 class="text-centered">Editing post: <br><?php echo $title; ?></h2>
 <?php import_admin_functions(); ?>
<?php if( user_can('Create Posts') ): ?>
 <?php $page_instance->add_fragment('tag-editor-scripts'); ?>

 <link rel="stylesheet" href="<?php echo $site_url; ?>/js/lib/dropzone/dropzone.css" />
 <style>.dz-preview, .dz-success-mark, .dz-error-mark{display:none;}</style>
 <script src="<?php echo $site_url; ?>/js/lib/dropzone/dropzone.js"></script>
 <img src="<?php echo sanitize_html_attribute($post_image_url); ?>" id="post-featured-image" class="block pull-right" style="width:200px; height:auto; margin-bottom:5px; margin-right:5px;"/>
 <div class="clear"></div>
 <button id="featured-image-change-button" class="btn btn-primary cursor-pointer pr25 pl25 pull-right" style="margin-right:5px;">Change featured image</button>
 <div class="clear" style="margin-bottom:5px;"></div>
 <script>
 (function(){

    var btnID = 'featured-image-change-button';
	var myDropzone = new Dropzone("#" + btnID, { 
		method                       : "post",
		url                          : '',
		paramName                    : "file", // The name that will be used to transfer the file
		maxFilesize                  : 1, // MB
		maxFiles                     : null,
		uploadMultiple               : false,
		acceptedFiles                : 'image/*',
		autoProcessQueue             : false,
		dictResponseError            : 'Error from server with status {{statusCode}}',
		dictInvalidFileType          : 'Error invalid file type',
		dictFileTooBig               : 'Error file too big. file size is {{filesize}}, max allowed upload size is {{maxFilesize}}',
		addRemoveLinks               : false,
		dictCancelUpload             : 'Cancel this upload', 
		dictCancelUploadConfirmation : 'Are you sure you wana cancel this upload?',
		dictRemoveFile               : 'Remove this file',
		parallelUploads              : 1, 

		processing(file)
		{
			setAsProcessing(btnID);
			disable(btnID);
		},
		accept: function(file, done)
		{ 
			done(); 
			hideDropZonePreview(); 
			myDropzone.processQueue();
		},
		error: function(file, errorMessage)
		{
			myDropzone.errors = true;
			myDropzone.errorMsg = errorMessage;

			hideDropZonePreview();
			hideDropZoneErrorAndSuccessMarks();

			unsetAsProcessing(btnID);
			enable(btnID);
		},
		queuecomplete: function()
		{
			if(myDropzone.errors) {
				if( myDropzone.errorType == 'invalidUploadDirectory' ) {
					displayImageUploadStatusMessage('Unable to create image directory. Please try again later');
				}
				else {
					displayImageUploadStatusMessage(myDropzone.errorMsg);
				}

				unsetAsProcessing(btnID);
				enable(btnID);
			}
			else {
				//displayImageUploadStatusMessage('Image successfully uploaded');
			}
		}
	});
	
	myDropzone.on('success', function(file, response) {

		console.log(response, 'raw response from server');

		if(typeof response === 'string') {
			response = JSON.parse(response);
			console.log(response, 'response as json object');
		}
		if(response.error) {
			myDropzone.error = true;
			myDropzone.message = response.message;
			myDropzone.errorType = response.errorType;
		}
		else {
			myDropzone.error = false;
			setFeaturedImageSrc(response.imageUrl);
		} 

		hideDropZonePreview();
		unsetAsProcessing(btnID);
		enable(btnID);
	});
	
	function setFeaturedImageSrc(imageSrc)
	{
		$O('post-featured-image').src = imageSrc;
	}

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
 })();
 </script>
 <script>
 var onBeforePostSubmit = function(){
	if( ( $O('post-featured-image').src == '<?php echo $curr_url; ?>') ) {
		$O('post-featured-image').src = '';
		var proceedWithoutFeaturedImage = confirm('You have not set a featured Image. Click ok to submit the form and cancel to set a featured image');
	    if( !proceedWithoutFeaturedImage ) {
		    return false;
	    }
	}
	
	return { 'featured-image-url': $O('post-featured-image').src }
 }
 </script>
 <?php 
 get_post_editor(array(
   'post_id'          => $post_id,
   'parent_post_id'   =>  0, 
   'placeholder'      => 'Create New Post', 
   'value'            => $content, 
   'auto_display'     => true, 
   'on_before_submit' => 'onBeforePostSubmit'
 )); 
 ?>
<?php endif; ?>
   
   
   <div class="text-centered" id="status-message"></div>

</div>

<?php 
/*
* http://api.jqueryui.com/datepicker/
* http://trentrichardson.com/examples/timepicker/
*/
?>


<?php $page_instance->add_fragment('composite-footer'); ?>
<?php $page_instance->close_page(); ?>