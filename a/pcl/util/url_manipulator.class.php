<?php

class UrlManipulator
{
	/*
	* @author: Michael Orji
	* @date: July 17, 2012
	*/
	public static function redirect($location, $status=302, $delay=0, $message='')
	{
		$location = str_replace( array('&amp;', "\n", "\r"), array('&', '', ''), $location ) ; 

		header('Status: ' . $status);

   		if( is_numeric($delay) && (intval($delay) > 0) )
		{
    		header("Refresh: $delay; url=$location");
    		print '<p>'. $message. '</p>';
   		} 
   		else
		{
    		header("location: $location");
   		}
 		exit();
	}
}