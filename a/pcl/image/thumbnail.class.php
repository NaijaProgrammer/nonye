<?php 

require_once('image_resizer.class.php');

class Thumbnail extends ImageResizer 
{ 
	private $image; 
	private $width; 
	private $height; 
	private $thumb_path;
	private $thumb_suffix;

	public function __construct($config) 
	{ 
 		$image        = $config['image'];
 		$width        = $config['width'];
 		$height       = $config['height'];
 		$thumb_path   = $config['thumb_path'];
 		$thumb_suffix = $config['thumb_suffix'];

 		parent::set_img($image); 
 		parent::set_quality(80); 
 		parent::set_size($width,$height); 

 		$this->thumb_path = $thumb_path;
 		$dest = $this->_create_dir($this->thumb_path). "/";

 		$this->thumbnail = $dest.pathinfo($image, PATHINFO_FILENAME). '_tn_'. $thumb_suffix. '.'. pathinfo($image, PATHINFO_EXTENSION); 
 
   		if(!file_exists($this->thumbnail))
		{
    		parent::save_img($this->thumbnail); 
   		}

 		parent::clear_cache(); 
	} 


	public function __toString()
	{ 
 		return $this->thumbnail; 
	} 
	
	protected function _create_dir($dir_name)
	{
   		if(!is_dir($dir_name))
		{
   			mkdir($dir_name, 0777);
   		}

		return $dir_name;
	}
} 

?>