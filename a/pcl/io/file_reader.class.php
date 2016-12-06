<?php

/*
* @author Michael Orji
*/
class FileReader
{
	private $file         = '';
	private $file_pointer = null;

	public function __construct($file)
	{
		$this->set($file);
		$this->open();
	}

	public function is_readable()
	{
		return ( $this->get_file_pointer() && is_readable($this->get_file()) );
	}	

	public function read($length = 0)
	{
		if( $this->is_readable() )
		{
			$file_pointer = $this->get_file_pointer();

			if($length > 0)
			{
				return fread($file_pointer, $length);
			}

			return fread($file_pointer, filesize($this->get_file()));
		}

		return false;
	}

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

	public function get_file()
	{
		return $this->file;
	}

	public function get_file_pointer()
	{
		return $this->file_pointer;
	}

	public function close($kill = false)
	{
		if($kill && $this->get_file())
		{
			$this->_unset();	
		}

		if($this->file_pointer)
		{
			return fclose($this->file_pointer);
		}
	}

	protected function set($file)
	{
		if(is_string($file))
		{
			$file       = trim($file);
			$this->file = $file;
		}		
	}

	protected function _unset()
	{
		$this->file = '';
	}

	protected function open()
	{
		$file = $this->get_file();

		if(!empty($file) && file_exists($file) )
		{
			$this->file_pointer = fopen($file, 'r');
			return true;
		}

		die('<p>Error!!! Unable to locate File : '. $file. ' </p>');
		return false;
	}
}