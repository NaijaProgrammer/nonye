<?php

class CsvFileInspector
{
	/**
	* @author: Michael Orji
	* @data: June 2, 2013
	* @param: csv_file : a string representing path to the csv file 
	* OR a csv file resource created using fopen() on a csv file OR
	* an array of the super-global $_FILES uploaded from an html form
	*
	* @param options: an array of optional things to perform on the file
	* e.g where the csv file is an $_FILES array, we may want to supply a path for it to be uploaded to
	*/
	public static function get_file_path_and_file_resource_handle($csv_file, $options=array())
	{

		$setup = array('upload_path'=>'uploads', 'unlink_file'=>false);
		ArrayManipulator::copy_array($setup, $options);

                $upload_path = $setup['upload_path'];
                $unlink_file = $setup['unlink_file'];

		if( is_resource($csv_file) ) //a file already created using fopen($file_handle_name), possibly by a third party
		{
			$csv_file        = '';
			$csv_file_handle = $csv_file;
		}
		else if( is_string($csv_file) ) //a path to the csv file
		{
			$csv_file         = $csv_file;
			$csv_file_handle  = fopen($csv_file, 'r');
		}
		else if( is_array($csv_file) ) //an array of the super-global $_FILES uploaded from an html form
		{
			$csv_manip = new CsvFileManipulator($csv_file, $upload_path);
			$csv_file_handle = $csv_manip->get_file_handle();
			$csv_file        = $csv_manip->get_uploaded_file();
	
			if($csv_manip->error_occured())
			{
				$csv_manip->get_error_message();
				return ; 
			}
			if($unlink_file)
			{
				$csv_manip->unlink_file();
			}
		}
		return array('file_path'=>$csv_file, 'resource_handle'=>$csv_file_handle);
	}
}