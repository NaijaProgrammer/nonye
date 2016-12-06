<?php

/**
* @author Michael Orji
*/
class FileWriter
{
	private $file         = '';
	private $file_pointer = null;
	private $mode         = 'APPEND_ONLY';

	function __construct($file, $mode = 'APPEND_ONLY')
	{
		$this->set($file);
		$this->set_mode($mode);
		$this->open();
	}

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function get_mode()
	{
		switch($this->mode)
		{
			case 'WRITE_ONLY'      : $mode = 'w';  break;
			case 'APPEND_ONLY'     : $mode = 'a';  break;
			default                : $mode = 'a';  break;
		}

		return $mode;
	}

	public function is_writable()
	{
		return ( $this->get_file_pointer() && is_writable($this->get_file()) );
	}	

	public function write($content, $length = 0)
	{
		$file_pointer = $this->get_file_pointer();

		if( $this->is_writable() )
		{
			if($length > 0)
			{
				return fwrite($file_pointer, $content, $length);
			}

			return fwrite($file_pointer, $content);
		}

		return false;
	}

	public function close()
	{
		if($this->file_pointer)
		{
			return fclose($this->file_pointer);
		}
	}

	public function get_file()
	{
		return $this->file;
	}

	public function get_file_pointer()
	{
		return $this->file_pointer;
	}

	protected function set($file)
	{
		if(is_string($file))
		{
			$file       = trim($file);
			$this->file = $file;
		}		
	}

	protected function open()
	{
		if(!empty($this->file))
		{
			$this->file_pointer = fopen($this->file, $this->get_mode());
			return true;
		}

		return false;
	}
}