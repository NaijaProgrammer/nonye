<?php

/*
* @author Michael Orji
*/
class MediaPlayer
{
 	private $media_file;
 	private $media_image;
 	private $media_link;
 	private $player;
 	private $player_path;
 	private $file_format;
 	private $width;
 	private $height;
 	private $object_id;
 	private $flash_vars;
 	private $path_to_player;
	private $auto_start;

   	public function __construct($config)
	{
    		$this->media_file     = $config['media_file'];
    		$this->media_image    = $config['media_image'];
    		$this->media_link     = $config['media_link'];
    		$this->player         = $config['media_player'];
    		$this->player_path    = $config['media_player_path'];
    		$this->file_format    = $this->get_file_extension(); //$config['file_format'];
    		$this->width          = $config['width'];
    		$this->height         = $config['height'];
    		$this->object_id      = $config['object_id'];
    		$this->path_to_player = $this->player_path. $this->player;
			$this->auto_start     = $config['auto_start'];
   	}

	public function exec_code()
	{
    	return $this->generate_code($is_external_embed_code = false);
   	}
   
   	public function embed_code()
	{
    	return "'". $this->generate_code($is_external_embed_code = true). "'";
   	}
   
   	public function play()
	{
    	echo $this->exec_code();
   	}

   	public function set_options($config = array())
	{
    		$media_file  = $this->media_file;
    		$media_image = $this->media_image;
    		$media_link  = $this->media_link;
			$auto_start  = $this->auto_start;

    		$file_or_path = FileInspector::is_video_file($media_file) ? "file" : "path";

    		$flash_vars     = "$file_or_path=$media_file&image=$media_image&link=$media_link";
    		$default_config = array(
                      'autostart'     => $auto_start, 
                      'repeat'        => 'false',
                      'showfsbutton'  => 'false',
                      'overstretch'   => 'false',
                      'largecontrols' => 'false',
                      'showdownload'  => 'true',
                      'showdigits'    => 'true',
                      'smoothing'     => 'false',
                      'backcolor'     => '0xffffff',
                      'frontcolor'    => '0x000000',
                      'lightcolor'    => '0x000000',
                      'fsreturnpage'  => '',
                      'bufferlength'  => 4
                     );

      		foreach($config AS $key => $value)
			{ //overwrite default configuration settings with the one provided by user
       			$default_config[$key] = $value;
      		}

      		foreach($default_config AS $key => $value)
			{ // after copying config properties from the provided one, use them to set the flash variables
       			$flash_vars .= "&{$key}={$value}";
      		}

    		$this->flash_vars = $flash_vars;
   	}

	protected function generate_code($is_external_embed_code = false)
	{
    		$width          = $this->width;
    		$height         = $this->height;
    		$object_id      = $this->object_id;
    		$path_to_player = $this->path_to_player;
    		$file_format    = $this->file_format;
    		$media_file     = $this->media_file;
    		$flash_vars     = $this->flash_vars;
    		$movie_value    = $is_external_embed_code ? $media_file : "$path_to_player?$file_format=$media_file";

      		if ((strtolower($file_format) == 'mid') || (strtolower($file_format) == 'midi') || (strtolower($file_format) == 'wav'))
			{
       			$obj = '<embed src="'.$media_file.'" autostart="true" loop="false" volume="100" hidden="false" showcontrols="true" mastersound />';
       			$obj.= '<noembed><bgsound src="'.$media_file.'"></noembed>';
      		}

      		else
			{
       			$obj  = "<object ";
       			$obj .= $is_external_embed_code ? "" : "classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0' ";
       			$obj .= "width='$width' height='$height' id='$object_id' name='$object_id'>".
               			"<param name='movie' value='$movie_value'>".
               			"<param name='allowfullscreen' value='true'>".
               			"<param name='allowscriptaccess' value='always'>". //or value='sameDomain';
               			"<param name='wmode' value='transparent'>".
               			"<param name='bgcolor' value='#ffffff'>";
       			$obj .= $is_external_embed_code ? "" : "<param name='flashvars' value='$flash_vars'>";
       			$obj .= "<embed id='$object_id' name='$object_id' src='$movie_value' quality='high' width='$width' height='$height' bgcolor='#ffffff' ".
              			"allowscriptaccess='always' allowfullscreen='true' ";
       			$obj .= $is_external_embed_code ? "" : "flashvars='$flash_vars' ";
       			$obj .= "type='application/x-shockwave-flash' ".
               			"pluginspage='http://www.macromedia.com/go/getflashplayer' />".
               			"</object>";
      		}

    		return $obj;
   	}

	private function get_file_extension()
	{
    	$ext = substr($this->media_file, strrpos($this->media_file, '.') + 1);
    	return $ext;
   	}
}