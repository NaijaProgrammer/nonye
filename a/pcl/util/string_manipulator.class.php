<?php

/*
* @author Michael Orji
*/
class StringManipulator
{
	/*
	* A string hashing function
	* @author: michael orji
	* @date: April 13, 2012
	* @params: String string to hash, Mixed hashing algorithm to use
	* @return value: the hashed string
	*/
	public static function hash_string($string, $hash_algorithm = 'md5')
	{
 		return $hash_algorithm($string);
	}

	/**
	* Creates a random string
	* @return string
	*/
	public static function create_random_string($num_chars = 7)
	{
 		srand((double)microtime() * 1000000);
 		$letters = range ('A','Z');
 		$numbers = range(0,9);
 		$chars = array_merge($letters, $numbers);
 		$randString = '';

   		for ($i=0; $i<$num_chars; $i++)
		{
    		shuffle($chars);
    		$randString .= $chars[0];
   		}

 		return $randString;
	}
	
	public static function set_displayed_text_length($text, $max_length, $fill_chars = '...')
	{
 		$displayedText = ( substr($text, 0, $max_length) . ( strlen($text) > $max_length ? $fill_chars : '') );
 		return $displayedText;
	}

	/**
	* returns the last character in a string
	* @date: May 9, 2012
	*/
	public static function get_last_character_in_string($string)
	{
 		return trim(substr($string, strlen($string)-1));
	}

	/**
	* @date: Feb. 7, 2013
	*/
	public static function is_upper_case($char)
	{
		return ( $char === strtoupper($char) );
	}

	/**
	* @credits: ditto
	*/
	public static function starts_with_upper_case($str)
	{
		$chr = mb_substr($str, 0, 1, "UTF-8");
		return mb_strtolower($chr, "UTF-8") != $chr;
	}
}