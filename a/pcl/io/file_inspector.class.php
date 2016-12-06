<?php

/*
* @author Michael Orji
*/
class FileInspector
{
	public static function get_file_properties($file)
	{
		$arr['file_type']      = self::get_file_type($file);
		$arr['file_name']      = self::get_file_name($file);
		$arr['file_extension'] = self::get_file_extension($file);
		$arr['file_mime_type'] = self::get_file_mime_type($file);
		$arr['file_fullname']  = self::get_file_name($file). '.'. self::get_file_extension($file);
		
		return $arr;
	}

	public static function get_file_type($file)
	{
 		if(self::is_audio_file($file))
		{
  			return 'audio';
 		}

 		if(self::is_image_file($file))
		{
  			return 'image';
 		}

 		if(self::is_video_file($file)){
  			return 'video';
 		}
		
		if(self::is_text_file($file))
		{
			return 'text';
		}

 		return 'unknown file type';
	}

	/*
	* returns the filename (without the file extension) of passed file
	* @author: michael orji
	* @date: 11 july, 2012
	* @param: a string representing the filename e.g "myImage.jpg"
	* @return_value: string, filename
	*/
	public static function get_file_name($file)
	{
   		if(is_array($file))
		{
    		return pathinfo($file['name'], PATHINFO_FILENAME);
   		}
   		else if(is_string($file))
		{
    		return pathinfo($file, PATHINFO_FILENAME);
   		} 
	}

	public static function get_file_extension($file)
	{
      		if(is_array($file))
			{
       			return pathinfo($file['name'], PATHINFO_EXTENSION);
      		}

      		else if(is_string($file))
			{
       			return pathinfo($file, PATHINFO_EXTENSION);
      		} 
   	}

   	public  static function get_file_mime_type($file)
	{
		if(is_array($file))
		{
    		return $file['type'];
		}
   	}

	public static function is_audio_file($media_file)
	{
    		$audio_mime_types = array('audio/mp3','audio/midi','audio/mid','audio/wav','audio/wma');
    		$audio_extensions = array('mp3','midi','mid','wav','wma',);
    		return(in_array(self::get_file_mime_type($media_file), $audio_mime_types) || (in_array(self::get_file_extension($media_file), $audio_extensions)));
   	}

	public static function is_image_file($media_file)
	{
		$image_mime_types = array('image/pjpeg','image/jpeg','image/jpg','image/gif','image/png','image/x-png','image/bmp');
		$image_extensions = array('pjpeg','jpeg','jpg','gif','png','bmp');
		return (in_array(self::get_file_mime_type($media_file), $image_mime_types) || (in_array(self::get_file_extension($media_file), $image_extensions)));
	}

   	public static function is_video_file($media_file)
	{
    		$video_mime_types = array('video/mpg','video/mpeg','video/mpe', 'video/avi','video/wmv','video/mov',/*'video/flv',*/'video/mp4','video/3gp','video/rm', 'video/asf');
    		$video_extensions = array('mpg','mpeg','mpe','avi','wmv','mov','flv','mp4','3gp','rm','asf');
    		return (in_array(self::get_file_mime_type($media_file), $video_mime_types) || (in_array(self::get_file_extension($media_file), $video_extensions)));
   	}
	
	public static function is_text_file($media_file)
	{
		$text_mime_types = array('application/pdf', 'application/msword');
		$text_extensions = array('pdf', 'doc', 'dot', 'docx');
		return (in_array(self::get_file_mime_type($media_file), $text_mime_types) || (in_array(self::get_file_extension($media_file), $text_extensions)));
	}
}