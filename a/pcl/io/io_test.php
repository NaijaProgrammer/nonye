<html>
<head>
</head>
<body>
<?php

require_once('file_reader.class.php');
require_once('file_writer.class.php');
require_once('file_manipulator.class.php');
require_once('file_downloader.class.php');
require_once('file_uploader.class.php');

//$downloadable = new FileDownloader('file_manipulator.class.php');
//$downloadable->download();
//FileManager::read_and_write('file_manager.class.php', 'file_manager_copy.txt', $length = 20);

if($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$uploader = new FileUploader( array('file'=>$_FILES['upload_file'], 'upload_directory'=>'uploads/images/', 'allowed_mime_types'=>array(), 'allowed_extensions'=>array('jpg', 'gif', 'png')) );
	$uploader->upload();

	if($uploader->get_error_code() > 0)
	{
		echo $uploader->get_error_message();
	}

	else
	{
		echo 'File Path: '. $uploader->get_uploaded_file_path(). '<br/>';
		echo 'File Name: '. $uploader->get_uploaded_file_name(). '<br/>';
		echo 'File Ext : '. $uploader->get_uploaded_file_extension(). '<br/>';
	}

}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">

<input type="file" name="upload_file"/>
<input type="submit" value="Upload"/>

</form>
</body>
</html>