<?php

/*
* @author Michael Orji
* @dependencies: io/FileInspector, util/System
*/
class MultimediaEncoder
{
	private static $ffmpeg_path; 

	public static function encode_multimedia($params=array())
	{
 		//self::_detect_and_load_ffmpeg_dll();

 		$file_to_encode = $params['file'];
 		$orig_media_path = $params['orig_media_path'];
 		$encoded_media_path = $params['encoded_media_path'];
 		$media_img_path = $params['media_img_path'];
 		$enc_file_ext = $params['encoded_file_extenxn'];
 		$media_img_ext = $params['media_image_extenxn'];
 		$ffmpeg_path = $params['ffmpeg_path'];
 		$filename = FileInspector::get_file_name($file_to_encode); //filename($file_to_encode);
 		$extenxn =  FileInspector::get_file_extension($file_to_encode); //file_extension($file_to_encode);
		
		self::_set_ffmpeg_path($ffmpeg_path);
		
		$ffmpeg_path   = self::_get_ffmpeg_path();
 		$srcFile       = "\"".$orig_media_path. $filename. ".". $extenxn."\""; 
 		$destFile      = "\"".$encoded_media_path. $filename. ".". $enc_file_ext. "\"";
 		$ffmpegPath    = $ffmpeg_path ? "\"". $ffmpeg_path. "ffmpeg". "\"" : FFMPEG_LIBRARY; // link to the above defined ffmpeg executable 
 		$destImageName = "\"".$media_img_path. $filename. ".". $media_img_ext. "\"";
 		$srcFile       = escapeshellcmd($srcFile); 

   		if(FileInspector::is_audio_file($file_to_encode))
		{
				//midi, mp3 and wav files do not need to be encoded
      			if ($extenxn != 'mid' && $extenxn != 'midi' && $extenxn != 'mp3' && $extenxn != 'wav' && $extenxn != $enc_file_ext)
				{
       				//$cmd = system($ffmpegPath ." -i ". $srcFile. " -ab 128 -ac 2 -f mp3 ". $destFile, $cmd_status);
					$cmd = system("$ffmpegPath -i $srcFile -ab 128 -ac 2 -f mp3 $destFile", $cmd_status);
       				return $cmd_status == 1; //the ffmpeg binary used here returns 1 for success, others may return 0;
      			}
   		}

   		else if(FileInspector::is_video_file($file_to_encode))
		{
      			if($extenxn != "flv" && $extenxn != $enc_file_ext){//flv files don't need encoding
       
       				//$cmd = system($ffmpegPath. " -i ". $srcFile. " -b 4000k -maxrate 4000k -bufsize 1835k ". $destFile, $cmd_status);
					$cmd = system("$ffmpegPath -i $srcFile -b 4000k -maxrate 4000k -bufsize 1835k $destFile", $cmd_status);
                    
	     			if($cmd_status == 1)
					{ //only produce image if video encoding was successful, the ffmpeg binary used here returns 1 for success, others may return 0;

          				self::_grab_image_from_video($srcFile, $destImageName);
						return true; // return true even if the image was not successfully encoded, (successful image encoding will give $cmd_status2 = 0);
					}
      			}

	  		else
			{//if flv file, just create the image file
				self::_grab_image_from_video($srcFile, $destImageName);
	   			return true; // return true even if the image was not successfully encoded, (successful image encoding will give $cmd_status2 = 0);
	  		}
   		}

 		return false; //encoding failed
	}
	
	protected static function _detect_and_load_ffmpeg_dll($path_to_ffmpeg_dll = null)
	{
   		if($path_to_ffmpeg_dll == null)
		{
    		$path_to_ffmpeg_dll = PHP_EXTENSION_DIR;
   		}

 		ini_set("ffmpeg.allow_persistent",1);
 		ini_set( "max_execution_time", "3600");
 		$extension = "ffmpeg";
 		$extension_soname = $extension . "." . PHP_SHLIB_SUFFIX; //in windows. gives us ".dll" in linux we get ".so"
 		$extension_fullname = $path_to_ffmpeg_dll . $extension_soname;

 		// Locate Extension
 		/* 
 		* MY NOTE:
 		* this defines the path to the ffmpeg executable
 		* either use this path in the call to 'ffmpeg', or copy the executable
 		* from this location to a more convenient location on your system
 		*/
 		define('FFMPEG_LIBRARY', '/usr/local/bin/ffmpeg');

   		// load extension
   		if(!extension_loaded($extension)) 
		{
    		dl($extension_soname) or die("Can't load extension $extension_fullname\n");
  		}
	}
	
	private static function _set_ffmpeg_path($ffmpeg_path = '')
	{
		$ffmpeg_path = ( is_string($ffmpeg_path) ? trim($ffmpeg_path) : '' );
		self::$ffmpeg_path = ( !empty($ffmpeg_path) ? $ffmpeg_path : dirname(__FILE__). '/ffmpeg/' );
	}
	private static function _get_ffmpeg_path()
	{
		return self::$ffmpeg_path;
	}
	
	private static function _grab_image_from_video($video, $image)
	{
		$ffmpeg = "\"". self::_get_ffmpeg_path(). "ffmpeg\"";
		
		if(System::os_is_windows())
		{
			//$ffmpeg = str_replace('/', '\\', $ffmpeg);
			//$video  = str_replace('/', '\\', $video);
			//$image  = str_replace('/', '\\', $image);
		}
		/*
	    // default time to get the image
		$second = 15;
		
		// get the duration and a random place within that
		//$cmd = "\"$ffmpeg\" -i \"$video\" 2>&1";
		$cmd = "$ffmpeg -i $video 2>&1";
		exec($cmd, $status);
		
		if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
			$total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
			$second = rand(1, ($total - 1));
		}

		// get the screenshot
		$cmd = "$ffmpeg  -itsoffset -$second  -i $video -vcodec mjpeg -vframes 1 -an -f rawvideo -s 150x84 $image";
		//$return = `$cmd`;
		exec($cmd, $output);
		*/
		
		$cmd2 = system("$ffmpeg -i $video -an -ss 00:00:04 -t 00:00:01 -r 1 -y -s 500x300 -vframes 1 -f mjpeg $image", $cmd_status2);
	}
}