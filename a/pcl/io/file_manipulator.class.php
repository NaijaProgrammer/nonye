<?php

/*
* @author Michael Orji
* @dependencies: FileReader, FileWriter, DirectoryManager
*/
class FileManipulator
{
	public static function read_and_write($source, $destination, $length = 0)
	{
		$src  = new FileReader($source);
		$dest = new FileWriter($destination);

		while(!feof($src->get_file_pointer()))
		{
			$dest->write( $src->read($length) );
		}
	}

	/*
	*This is to ensure that uploaded filenames are unique by assigning
	*an incrementing number value to new files with already existing names
	*
	* @author: michael orji
	* @date: 26 sept, 2010
	* @time: 12:57
	*/
	public static function create_unique_filename($file_name, $dir = "")
	{
		$counter = 0;
		$filename = pathinfo($file_name, PATHINFO_FILENAME);
		$extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$tmp_filename = pathinfo($file_name, PATHINFO_FILENAME);

   		if("" != $dir)
		{
   			$dir = DirectoryManipulator::create_directory($dir);
   		}

		$handle = opendir($dir);

   		if(!empty($handle))
		{
      		while($f = readdir($handle))
			{
       			$fname = pathinfo($f, PATHINFO_FILENAME);

         		if($filename == $fname)
				{
          			$counter++;
          			$filename = $tmp_filename. $counter;
         		}
      		}
   		}

     	return ($extension) ? $filename. '.'. $extension : $filename;
	}

	//Call this function with argument = absolute path of file or directory name.
	public static function compress_file($src, $new_name = '')
	{
        	if(substr($src,-1)==='/')
			{
         		$src=substr($src,0,-1);
        	}

        	$arr_src=explode('/',$src);
        	$filename=end($arr_src);
        	unset($arr_src[count($arr_src)-1]);
        	$f=explode('.',$filename);
        	$filename=$f[0];
        	$filename = ( ($new_name!='') ? $new_name.'.zip' : $filename.'.zip');

        	$zip = new ZipArchive;
        	$res = $zip->open($filename, ZipArchive::CREATE);

        	if($res !== TRUE)
			{
           		echo 'Error: Unable to create zip file';
           		exit;
        	}

        	if(is_file($src))
			{
         		$zip->addFile($src);
        	}

        	else
			{

                if(!is_dir($src))
				{
                    $zip->close();
                    @unlink($filename);
                    echo 'Error: File not found';
                    exit;
				}

         		self::recurse_zip($src, $zip);
			}

        	$zip->close();
        	header("Location: $filename");
        	exit;
	}

	protected static function recurse_zip($src, &$zip) 
 	{
        $dir = opendir($src);

        while(false !== ( $file = readdir($dir)) ) 
		{

            		if (( $file != '.' ) && ( $file != '..' ))
					{
                		if ( is_dir($src . '/' . $file) ) 
						{
                    		self::recurse_zip($src . '/' . $file, $zip);
                		}
                		else 
						{
                    		$zip->addFile($src . '/' . $file);
                		}
            		}
        }

        closedir($dir);
	}
}