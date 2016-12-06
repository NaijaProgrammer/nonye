<?php

/*
* A class for sanitizing data (especially for insertion into and retrieval from a database table)
* @date: Nov. 2, 2012
*/
class DataSanitizer
{
	public static function sanitize_data_for_db_query($data, $allowed_tags = "")
	{
		if(is_object($data))
		{ 
			return $data; 
		}
		if(is_array($data))
		{ 
			return $data; 
		}

		return is_numeric($data) ? self::_sanitize_integer($data) : self::_sanitize_string_for_db_query($data, $allowed_tags);  
	}

	#replaces white-space with characters specified by you (default is the underscore(_) character)
	public static function replace_whitespace($string, $replacement_char = "_", $pattern = " ")
	{ 
		$replacement = $replacement_char;
		$string = ereg_replace($pattern, $replacement, $string);
		return $string;
	} 

	public static function replace_special_chars($string, $chars="[^a-zA-Z]", $replacement="")
	{ 
		$pattern = $chars;
		$string = ereg_replace($pattern, $replacement, $string); 
		return $string;
	} 

	#remove excessive white-space from inside strings
	public static function remove_internal_whitespace($string) 
	{ 
		$string = preg_replace('/\s+/', ' ', $string); 
		return $string;
	} 

	function strip_slashes($value)
	{
		$value = is_array($value)?array_map('strip_slashes', $value):stripslashes($value);
		return $value;
	}

	#make strings safe for browser display
	/*
	public static function decode_html($string, $decode_html_special_chars = false, $use_nl2br = false)
	{
		if($decode_html_special_chars)
		{
			$string = htmlspecialchars_decode($string);
		}
		if($use_nl2br)
		{
			$string = nl2br($string);
		}
		return $string;
	}
	*/

	/*
	* This is especially useful when outputting from the server-side to the client side during an XHR Request
	* @credits: javascript: the Definitive Guide, 5th Edition, section 20.4. Scripting HTTP with <script> Tags
	* @modified: Michael Orji
	*/
	public static function escape_output_string($str)
	{
		$escaped = str_replace(array("'", "\"", "\n", "\r"), array("\\'", "\\\"", "\\n", "\\r"), $str);
		return $escaped;
	}

	#safely escape strings (for insertion into the database)
	private static function _sanitize_string_for_db_query($string, $allowed_tags="")
	{
		//$string = htmlspecialchars_decode($string);
		//$string = self::remove_internal_whitespace($string);
		//$string = trim($string);
		$string = strip_tags($string, $allowed_tags);
     
		if(function_exists('mysql_real_escape_string'))
		{
			$string = mysql_real_escape_string($string);
		}
		else if(function_exists('mysql_escape_string'))
		{
			$string = mysql_escape_string($string);
		}
		else
		{
			$string = addslashes($string);
		}
      
		/*
		if( ($allowed_tags != "") && ($allowed_tags != null) )
		{
			$string = htmlspecialchars($string);
		}
		*/
		return $string;
	}

	private static function _sanitize_integer($int)
	{
		return $int; //intval($int);
	}
}