<?php
/*
* @author Michael Orji
*/
class MultimediaInspector()
{
	function get_media_duration($media_file, $path_to_ffmpeg)
	{
 		ob_start();
 		passthru($path_to_ffmpeg."ffmpeg -i $media_file 2>&1"); 
 		$return_string = ob_get_contents(); 
 		ob_end_clean();

 		$search = '/Duration:(.*?),/';
 		$duration = preg_match($search, $return_string, $matches, PREG_OFFSET_CAPTURE, 3);
 		$duration = $matches[1][0];
 
 		/* 
 		* strip away any digits after: "hh:mm:ss"
 		* this works for now, but try to make it 
 		* more robust
 		*/
 		$duration = substr($duration, 0, 9);
 		return $duration;
	}
}