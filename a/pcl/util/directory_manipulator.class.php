<?php

class DirectoryManipulator
{
	public static function create_directory($dir_name, $mode = 0777, $recursive = true)
	{
   		if(!is_dir($dir_name))
		{
     		mkdir($dir_name, $mode, $recursive);
   		}
   		return $dir_name;
	}

	/**
	* @date   : Oct. 26, 2012
	*/
	public static function copy_directory($source, $destination)
	{
    		if(!is_dir($destination))
			{
        		$oldumask = umask(0); 
        		mkdir($destination, 0757); // so you get the sticky bit set 
        		umask($oldumask);
    		}

    		$dir_handle = opendir($source) or die("Unable to open");

   		 while ($file = readdir($dir_handle)) 
		 {
        	if($file != "." && $file != ".." && !is_dir("$source/$file"))
			{ 
            	copy("$source/$file","$destination/$file");
        	}

        	if($file != "." && $file != ".." && is_dir("$source/$file"))
			{
            		self::copy_directory("$source/$file","$destination/$file");
        	}
    	}

    	closedir($dir_handle);
	}

	public static function strip_root_directory_from_directory_path_name($dir_name, $document_root = '')
	{
		$root_directory = empty($document_root) ? $_SERVER['DOCUMENT_ROOT'] : $document_root;
		
		$directory_name = str_replace('\\', '/', $dir_name);
		
		return str_replace($root_directory, '', $directory_name);
	}
}