<?php

/*
* @author Michael Orji
*/
class CsvFileManipulator
{
	private $upload_file_field_name;
	private $uploaded_file;
	private $upload_directory;
	private $error;
	private $error_message;

	function __construct($upload_file_field_name, $upload_directory='uploads/')
	{
		$this->upload_file_field_name = $upload_file_field_name;
		$this->upload_directory       = $upload_directory;
	}
	public function get_file_handle()
	{

		$uploader = new FileUploader( array( 'file'=>$this->upload_file_field_name, 'upload_directory'=>$this->upload_directory, 'allowed_mime_types'=>array('application/vnd.ms-excel'), 'allowed_extensions'=>array('csv') ) );
		$uploader->upload(); 

		if($uploader->get_error_code())
		{
		  	$this->error = true; 
			$this->error_message = $uploader->get_error_message();
			return;
		}

		$this->uploaded_file = $uploader->get_uploaded_file_path();
		$csv_file_handle     = fopen($this->uploaded_file, 'r');

		if (!$csv_file_handle)
		{
			$this->error = true; 
			$this->error_message = 'Invalid file format. File must be a csv file';
			return;
		}

		return $csv_file_handle;
	}
	public function error_occured()
	{
		return $this->error;
	}
	public function get_error_message()
	{
		return $this->error_message;
	}
	public function get_uploaded_file()
	{
		return $this->uploaded_file;
	}

	public function unlink_file()
	{
		unlink($this->get_uploaded_file());
	}

}