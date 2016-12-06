<?php

/**
* @author: Michael Orji
* @dependencies: UrlInspector, DirectoryManipulator, io/FileInspector, StringManipulator
*/

class DirectoryInspector
{
	private static $initial_supplied_directory = '';

	public static function contains_root_directory_path($dir_name, $document_root = '')
	{
		$root_directory = empty($document_root) ? $_SERVER['DOCUMENT_ROOT'] : $document_root;

		$directory_name = str_replace('\\', '/', $dir_name); 
		
		$ret = strpos($directory_name, $root_directory); 

		if($ret === false)
		{
			return false;
		}
		return true;
	}
	
	/**
	* strips the directory path, if any, from the path to a (web) resource and returns the url of the resource
	* @param: resource_path:String -- (directory path to the resource, e.g 'C:/wamp/www/application/image.jpg')
	* @param: parent_app_url:String -- (url path to the parent app of the resource, if any, for resources inside an app-folder, e.g 'http://localhost/application')
	* @return: resource_url: String -- (url of resource)
	* @author:Michael Orji
	* @date: Dec. 12, 2013
	*/
	public static function get_resource_url($resource_path, $parent_app_url = '')
	{
		if(self::contains_root_directory_path($resource_path))
		{   
			return UrlInspector::get_base_url(). '/'. DirectoryManipulator::strip_root_directory_from_directory_path_name($resource_path);	
		}
		else if(!empty($parent_app_url))
		{  
			return $parent_app_url. '/'. $resource_path;
		}
		return $resource_path;
	}
	
	/**
	* Get files of a specific type, that is files with a given extension_loaded, in a directory
	*
	* @param string $directory the directory where the files are stored
	* @param mixed $file_extensions a comma separated string or an array of file extensions
	* @param boolean $recursive boolean flag to determine whether to search within sub-folders as well or just the current folder, default is false
	*
	* @return array an array of files matching the specific extension(s)
	* @date August 3, 2013
	*/
	public static function get_specific_type_files($directory, $file_extensions = '', $recursive = false)
	{
		if(!is_string($file_extensions) && !is_array($file_extensions))
		{
			return $files;
		}
		if(is_string($file_extensions))
		{
			$file_extensions = trim($file_extensions);
		}
		if(empty($file_extensions))
		{
			return $files;
		}
		
		if(is_string($file_extensions))
		{
			$exts_array = explode(',', $file_extensions);
		}
		else
		{
			$exts_array = $ext;
		}
		
		$ret   = array();
		$files = self::get_directory_contents($directory, 'FILES_ONLY', $recursive);
		
		foreach($files As $file)
		{
			foreach($exts_array AS $ext)
			{
				$ext = trim($ext);
				if( strtolower(FileInspector::get_file_extension($file)) == strtolower($ext) )
				{
					$ret[] = $file;
				}
			}
		}
		return $ret;
	}

	public static function get_directory_contents($directory, $mode = 'BOTH', $recursive = false)
	{
		switch($mode)
		{
			case 'BOTH'             : self::set_initial_supplied_directory($directory); return array_merge(self::get_directory_directories($directory, $recursive), self::get_directory_files($directory));
			case 'FILES_ONLY'       : return self::get_directory_files($directory); 
			case 'DIRECTORIES_ONLY' : self::set_initial_supplied_directory($directory); return self::get_directory_directories($directory, $recursive); 
			default                 : self::set_initial_supplied_directory($directory); return array_merge(self::get_directory_directories($directory, $recursive), self::get_directory_files($directory));
		}
	}

	// A function to create an array of all the files in target folder.
	protected static function get_directory_files($folder)
	{ //TO DO: add a second parameter to determine the types of files to get

		$images = array();

		if (is_dir($folder))
		{
			$files = scandir ($folder);

			foreach ($files as $file)
			{
				$path = $folder . '/' . $file;

				if (is_file($path)) 
				{
					$images[] = $file;
				}
			}
		}

		return $images;
	}

	// A function to create an array of all the directories in target folder.
	protected static function get_directory_directories($folder, $recursive = false)
	{
		$images = array();

		if (is_dir($folder))
		{
			$files = scandir ($folder);

			foreach ($files as $file)
			{
				if( ($file != '.') && ($file != '..') )
				{
					$path  = $folder;
					$path .= ( (StringManipulator::get_last_character_in_string($folder) == '/') ? $file : '/'. $file );				
				
					if (is_dir($path))
					{
						if(self::is_initial_supplied_directory($folder))
						{
							$images[] = $file; 
						}
						else 
						{
							$str = str_replace(self::$initial_supplied_directory. '/', '', $path);
							$images[] = $str;
						}
					}

					if($recursive && is_dir($path) )
					{
						if(self::contains_directory($path))
						{
							$dirs[] = self::get_directory_directories($path, $recursive);
							$images = array_merge($images, self::break_down_array($dirs)); 
						} 
					}
				}
			}
		}

		return $images;
	}

	protected static function set_initial_supplied_directory($directory_name)
	{
		if(StringManipulator::get_last_character_in_string($directory_name) == '/')
		{
			$directory_name = substr($directory_name, 0, strlen($directory_name) -1);
		}

		self::$initial_supplied_directory = $directory_name;
	}

	protected static function contains_directory($folder)
	{
		$folder_contents = scandir($folder);
		foreach($folder_contents AS $content)
		{
			$dir = $folder. '/'. $content;

			if( is_dir($dir) && ($content !== '.') && ($content !== '..') )
			{ 
				return true;
			}
		}
		
		return false;
	}	

	protected static function is_initial_supplied_directory($directory)
	{
		return (self::$initial_supplied_directory == $directory);
	}

	protected static function break_down_array($arr)
	{
		$ret = array();

		foreach($arr AS $value)
		{
			if(is_array($value))
			{
				$nr  = self::break_down_array($value);
				$ret = array_merge($ret, $nr);
			}

			else
			{
				$ret[] = $value;
			}
		}

		return $ret;
    }
}