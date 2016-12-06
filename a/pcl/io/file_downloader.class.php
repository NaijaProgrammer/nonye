<?php

require_once('file_reader.class.php');

/*
* @author Michael Orji
*/
class FileDownloader extends FileReader
{
	public function download()
	{
   		if ($this->is_readable()) 
		{
			$file = $this->get_file();

    			header('Content-Description: File Transfer');
    			header('Content-Type: application/octet-stream');
    			header('Content-Length: ' . filesize($file));
    			header('Content-Disposition: attachment; filename='.basename($file));
    			header('Content-Transfer-Encoding: binary');
    
    			return @readfile($file);
   		}

 		return false;
	}
}