<?php

/*
* @author Michael Orji
* @dependencies: FileManipulator, FileInspector, util/ArrayManipulator, util/DirectoryManager
*/
class FileUploader
{
	private $file               = NULL;
	private $upload_directory   = '';
	private $allowed_mime_types = array();
	private $allowed_extensions = array();
	private $error_code         = 0;
        private $error_message      = '';

	private $uploaded_file_path      = '';
       	private $uploaded_filename       = ''; 
       	private $uploaded_file_extension = '';

	public function __construct($params)
	{
		$default_opts = array('file'=>NULL, 'upload_directory'=>'uploads/', 'allowed_mime_types'=>array(), 'allowed_extensions'=>array());
		$setup_opts   = ArrayManipulator::copy_array($default_opts, $params);

		$this->set_file($setup_opts['file']);
		$this->set_upload_directory($setup_opts['upload_directory']);
		$this->set_allowed_mime_types($setup_opts['allowed_mime_types']);
		$this->set_allowed_extensions($setup_opts['allowed_extensions']);
	}

	public function set_file($file)
	{
		//make sure file was uploaded via HTTP POST
		if(is_uploaded_file($file['tmp_name']) )
		{ 
			$this->file = $file;
   		}	
	}

	public function get_file_to_upload()
	{
		return $this->file;
	}

	public function set_upload_directory($dir_name)
	{
		if(!empty($dir_name))
		{
			if(!is_dir($dir_name))
			{
				DirectoryManipulator::create_directory($dir_name);
			}
			
			$this->upload_directory = $dir_name;
		}
	}

	public function get_upload_directory()
	{
		return $this->upload_directory;
	}

	public function set_allowed_mime_types($mime_types)
	{
		if(is_string($mime_types))
		{
			$mime_types = explode(',', $mime_types);
		}

		$this->allowed_mime_types = ( is_array($mime_types) ? $mime_types : array() );
	}

	public function get_allowed_mime_types()
	{
		return $this->allowed_mime_types;
	}

	public function set_allowed_extensions($extensions)
	{
		if(is_string($extensions))
		{
			$extensions = explode(',', $extensions);
		}

		$this->allowed_extensions = ( is_array($extensions) ? $extensions : array() );

	}

	public function get_allowed_extensions()
	{
		return $this->allowed_extensions;
	}

	public function get_error_code()
	{
		return $this->error_code;
	}

	public function get_error_message()
	{
		return $this->error_message;
	}

	public function set_configurations($config_array)
	{
		$default_config = array(
					'safe_mode'=>'off', 'max_execution_time'=>'1000', 'max_input_time'=>'1000', 'register_argc_argv'=>'On',  
					'open_basedir'=>'', 'upload_max_filesize'=>'200M', 'post_max_size'=>'200M'
					);

		$setup_config = ArrayManipulator::copy_array($default_config, $config_array);

		foreach($setup_config AS $key => $value)
		{
			ini_set($key, $value);
		}	
	}

	public function get_uploaded_file_name()
	{
		return $this->uploaded_filename;
	}

	public function get_uploaded_file_extension()
	{
		return $this->uploaded_file_extension;
	}

	public function get_uploaded_file_path()
	{
		return $this->uploaded_file_path;
	}	

	public function upload()
	{ 
		$current_file = $this->get_file_to_upload();

		if(!$current_file || !$this->is_valid_file_type())
		{
			$this->error_code    = 5;
    		$this->error_message = "Invalid file type";
			return;
   		}

		if ($current_file['error'] > 0)
		{ 
       			$this->error_code    = $current_file['error'];
       			$this->error_message = $this->upload_error_message($current_file['error']); 
       			return;
      	}

		$destination_dir   = $this->get_upload_directory();
		$file_properties   = FileInspector::get_file_properties($current_file);
		$file_extension    = $file_properties['file_extension']; 
 		$unique_name       = FileManipulator::create_unique_filename($file_properties['file_fullname'], $destination_dir);
		$file_parts        = explode(".". $file_extension, $unique_name);
 		$filename          = FileInspector::get_file_name($unique_name); //$file_parts[0];
 		$final_destination = $destination_dir. $unique_name;

      		if (move_uploaded_file($current_file['tmp_name'], $final_destination)) 
			{
				$this->uploaded_file_path      = $final_destination;
       			$this->uploaded_filename       = $filename; 
       			$this->uploaded_file_extension = $file_extension;               
      		} 
                    
      		if (file_exists($current_file['tmp_name']) && is_file($current_file['tmp_name']))
			{
       			unlink($current_file['tmp_name']);
      		}
	} 

	
	protected function is_valid_file_type()
	{
		$file = $this->get_file_to_upload();

 		return ( in_array(FileInspector::get_file_mime_type($file), $this->get_allowed_mime_types()) || in_array(FileInspector::get_file_extension($file), $this->get_allowed_extensions()) );
	} 

	
	protected function upload_error_message($error_code)
	{
   		switch ($error_code) 
		{
    			case 1:  return "File size exceeds the upload_max_filesize setting in php.ini";
    			case 2:  return "Fiie size exceeds the MAX_FILE_SIZE setting in the HTML form";
    			case 3:  return "File was only partially uploaded";
    			case 4:  return "No file was uploaded";
    			case 6:  return "No temporary folder was available";
    			case 7:  return "Unable to write to the disk";
    			case 8:  return "File upload stopped";
    			default: return "An (unknown) system error occurred";
   		} 
	}
}