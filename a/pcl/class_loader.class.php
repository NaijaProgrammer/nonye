<?php 
require_once('util/directory_inspector.class.php');
require_once('util/string_manipulator.class.php');
class ClassLoader()
{
	public static function load_classes()
	{
		spl_autoload_register(load_class);
		function load_class($class_name) 
		{ 
			$directories = DirectoryInspector::get_directory_contents(PHPUTIL_DIR, 'DIRECTORIES_ONLY', true);
			foreach($directories AS $directory)
			{
				if( ($directory != '.') && ($directory != '..') && ($directory !== PHPUTIL_DIR. 'docs') )
				{
					$file_name       = strtolower($class_name[0]);
					$file_name_array = str_split( substr($class_name, 1) ); //don't include first letter since we already got it

					foreach($file_name_array AS $char)
					{
						if(StringManipulator::is_upper_case($char))
						{ 
							$char = '_'. strtolower($char);
						}
						$file_name .= $char;
					}
					$file = PCL_DIR. $directory. '/'. $file_name. '.class.php';
        			 
					if (file_exists($file)) 
					{ 
            			require_once($file); 
            			return true;  
					} 

				}
			}
			return false; 
		} 
	}
}