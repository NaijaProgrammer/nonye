<?php
/* This file contains global utility functions */

/*
* A string hashing function
* @author: michael orji
* @date: April 13, 2012
* @params: String string to hash, Mixed hashing algorithm to use
* @return value: the hashed string
*/
function hash_string($string, $hash_algorithm = 'md5'){
 return $hash_algorithm($string);
}

/*************
* function that gets (url) path (relative to document / server root) to the 
* (parent) directory (of current directory) 
* containing currently executing script
*
* possible values for currently executing script ($curr_script): 
* $_SERVER['SCRIPT_FILENAME'] to get its absolute pathname e.g 'server/system root/parent_of_current_dir/(current_dir/)'
* $_SERVER['SCRIPT_NAME'] to get its current path e.g '/parent_of_current_dir/(current_dir/)'
*
* @date: Feb 17, 2012
***************/
function get_rel_dir_path($curr_script, $use_http=false, $get_curr_dir=false){
 $prepend = '';
   if($use_http){$prepend = "http://". $_SERVER['HTTP_HOST'];}   
   if($get_curr_dir){return $prepend. dirname($_SERVER[$curr_script]). '/';}
 return $prepend. dirname(dirname($_SERVER[$curr_script])). '/';
}

#replaces white-space with characters specified by you (default is the underscore(_) character)
function replace_whitespace($string, $replacement_char = "_") 
{ 
//$pattern = "[ \n\t\r\f]";
$pattern = " ";
$replacement = $replacement_char;
$string = ereg_replace($pattern, $replacement, $string);
$string = trim($string); 
return $string;
} 

function replace_special_chars($string, $chars="[^a-zA-Z]", $replacement="") 
{ 
$pattern = $chars;
$string = ereg_replace($pattern, $replacement, $string);
$string = trim($string); 
return $string;
} 

#remove excessive white-space from inside strings
function remove_whitespace($string) 
{ 
$string = preg_replace('/\s+/', ' ', $string); 
$string = trim($string); 
return $string;
} 

#safely escape strings (for insertion into the database)
function safe_escape($string, $allowed_tags="")
{
$string = trim($string);
$string = htmlspecialchars_decode($string);
$string = remove_whitespace($string);
   
   if( ($allowed_tags != "") && ($allowed_tags != null) ){
    $string = strip_tags($string, $allowed_tags);
   }
   else{
    $string = strip_tags($string);
   }
   if(function_exists('mysql_real_escape_string')){
   $string = mysql_real_escape_string($string);
   }
   else if(function_exists('mysql_escape_string')){
   $string = mysql_escape_string($string);
   }
   else{
    $string = addslashes($string);
   }
   if( ($allowed_tags != "") && ($allowed_tags != null) ){
    $string = htmlspecialchars($string);
   }
  return trim($string);
}

#make strings safe for browser display
function safe_display($string, $decode_html_special_chars = false, $use_nl2br = false){

   if($decode_html_special_chars){
    $string = htmlspecialchars_decode($string);
   }
   if($use_nl2br){
    $string = nl2br($string);
   }
 return $string;
}

function strip_slashes($array){
 $array = is_array($array)?array_map('strip_slashes', $array):stripslashes($array);
 return $array;
}

function set_displayed_text_length($text, $max_length){
 $displayedText = ( substr($text, 0, $max_length) . ( strlen($text) > $max_length ? '...' : '') );
 return $displayedText;
}
?>