<?php 
include 'config.php';

//tiny mce post-acceptor.php reference implementation
//https://www.tinymce.com/docs/advanced/php-upload-handler/

/*********************************************
* Change this line to set the upload folder *
*********************************************/
$imageFolder = 'resources/uploads/'; //"images/";

reset ($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name']))
{
	verify_request_origin($die = true, $message='Invalid request origin');

    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name']))
	{
        header("HTTP/1.0 500 Invalid file name.");
        return;
    }

    // Verify extension
    if ( !in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png")) ) 
	{
        header("HTTP/1.0 500 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder. $temp['name'];
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
	
	//On initial upload, we make use of a form, and an Iframe, 
	//so we send output in such a way that we use the iframe's top property to access the parent window
	if( isset($_GET['source']) && $_GET['source'] == 'iframe' )
	{
		printf("<script>top.$('.mce-btn.mce-open').parent().find('.mce-textbox').val('%s').closest('.mce-window').find('.mce-primary').click();</script>", SITE_URL. '/'. $filetowrite);
	}
	
	//After the initial upload, any other change to the image (like resizing, rotating, skewing, etc), is handled by the tinymce editor automatically
	//so, use the output in their default implementation
	else
	{
		//jquery's intelligentGuess sometimes has problem handling JSON replies if you don't specify the content type as application/json.
		//for tinymce's error when the image is rotated of flipped, the reminder came from: http://stackoverflow.com/a/3592284/1743192
		header("Content-type:application/json");
		echo json_encode(array('location' => $filetowrite));
	}
} 
else
{
	// Notify editor that the upload failed
    header("HTTP/1.0 500 Server Error");
}