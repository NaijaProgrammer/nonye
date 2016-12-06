<?php 

/*
* @author Michael Orji
*/
class ImageResizer 
{ 
 
	private $img_input; 
	private $img_output; 
	private $img_src; 
	private $format; 
	private $quality = 80; 
	private $x_input; 
	private $y_input; 
	private $x_output; 
	private $y_output; 
	private $resize; 


	public function set_img($img)
	{ 

		$ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION)); 

		if(is_file($img) && ($ext == "JPG" OR $ext == "JPEG")) 
		{ 
			$this->format = $ext; 
			$this->img_input = ImageCreateFromJPEG($img); 
			$this->img_src = $img; 
		} 
 
		elseif(is_file($img) && $ext == "PNG")
		{ 
			$this->format = $ext; 
			$this->img_input = ImageCreateFromPNG($img); 
			$this->img_src = $img; 
		} 

		elseif(is_file($img) && $ext == "GIF") 
		{ 
			$this->format = $ext; 
			$this->img_input = ImageCreateFromGIF($img); 
			$this->img_src = $img; 
		} 

		// Get dimensions 
		$this->x_input = imagesx($this->img_input); 
		$this->y_input = imagesy($this->img_input); 
	} 


	public function set_size($max_x = 100,$max_y = 100) { 

		// Resize 
		if($this->x_input > $max_x || $this->y_input > $max_y) 
		{ 
			$a= $max_x / $max_y; 
			$b= $this->x_input / $this->y_input; 

			if ($a<$b)
			{ 
				$this->x_output = $max_x; 
				$this->y_output = ($max_x / $this->x_input) * $this->y_input; 
			} 
			else
			{ 
				$this->y_output = $max_y; 
				$this->x_output = ($max_y / $this->y_input) * $this->x_input; 
			} 

			$this->resize = TRUE; 
		} 

		// Don't resize 
		else
		{ 
			$this->resize = FALSE; 
		} 
	} 


	public function set_quality($quality) 
	{ 
		if(is_int($quality))
		{ 
			$this->quality = $quality; 
		} 
	} 

	// Save image 
	public function save_img($path)
	{ 
		// Resize 
		if($this->resize)
		{ 
			$this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output); 
			ImageCopyResampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input); 
		} 

		if($this->format == "JPG" OR $this->format == "JPEG")
		{ 
			if($this->resize)
			{ 
				imageJPEG($this->img_output, $path, $this->quality); 
			} 
			else
			{ 
				copy($this->img_src, $path); 
			} 
		} 

		elseif($this->format == "PNG")
		{ 
			if($this->resize) 
			{ 
				imagePNG($this->img_output, $path);
			} 
			else 
			{ 
				copy($this->img_src, $path); 
			} 
		} 

		elseif($this->format == "GIF") 
		{ 
			if($this->resize) 
			{ 
				imageGIF($this->img_output, $path); 
			} 
			else
			{ 
				copy($this->img_src, $path); 
			} 
		} 
	} 

	public function get_width() 
	{ 
		return $this->x_input; 
	} 

	public function get_height()
	{ 
		return $this->y_input; 
	} 


	// Clear image cache 
	public function clear_cache() 
	{ 
		@ImageDestroy($this->img_input); 
		@ImageDestroy($this->img_output); 
	} 

}