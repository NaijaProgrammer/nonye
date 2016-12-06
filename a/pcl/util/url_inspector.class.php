<?php

/*
* @author Michael Orji
*
* @dependencies StringManipulator
*/
class UrlInspector
{ 
	public static function get_base_url()
    {
		return self::get_server_protocol(). $_SERVER['HTTP_HOST'];
    }
	
	/**
	* @date; Jan. 8, 2014
	*/
	public static function get_domain_from_subdomain_url($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) 
		{
			return $regs['domain'];
		}
		return false;
	}

	public static function get_server_protocol()
	{
		//$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";

		return $protocol;
	}
	
	/*
	* @date: 28 August, 2012
	*/
	public static function get_path($current_file)
	{ 
 		$docroot = $_SERVER['DOCUMENT_ROOT'];
 		$p = $current_file; //this gets the path to the application's folder from this file, irrespective of whatever page it is included in, this way, we get the path to the application folder consistently
 		$rep = str_replace("\\", "/", $p);
		
		
   		if(StringManipulator::get_last_character_in_string($docroot) == '/')
		{
    		$cut = substr($rep, strlen($docroot)-1, strlen($rep));
   		}

   		else
		{
    		$cut = substr($rep, strlen($docroot), strlen($rep));
   		}
		
		$ahp = self::get_server_protocol(). $_SERVER['HTTP_HOST'] . $cut . '/';
 		$adp = $rep. '/';

 		$arr = array();
 		
 		$arr['dir_path']  = $adp;     //file system directory to the current file
 		$arr['http_path'] = $ahp;    //web (http) directory to the current file

 		return $arr;
	}

	/*
	* @date: Nov. 2, 2012
	*/
	public static function get_referrer_page($strip_query_string = false, $incoming_key = '', $arr_of_keys_to_strip = array())
	{
 		$page = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : self::get_current_page(true);

   		if(!self::url_contains_query_string($page))
		{
   			return $page;
   		}

   		if($strip_query_string)
		{
    		$page = substr($page, 0, strpos($page, '?') );
   		}

   		else
		{
      		foreach($_GET AS $key => $value)
			{
         		if( ($key != $incoming_key) && (!in_array($key, $arr_of_keys_to_strip)) )
				{
          			$page .= "&". $key. "=". urlencode($value);
         		}
      		}
   		}

 		return $page;
	}

	/*
	*@date: 4 August, 2012
	*/
	public static function get_current_base_path()
	{
 		$curr_script = $_SERVER['PHP_SELF'];
 		$current_base_path = substr($curr_script, 0, strrpos($curr_script, '/')+1 );
 		return $current_base_path;
	}

	/*
	*@date: 4 August, 2012
	*/
	public static function get_current_page($include_query_string = false, $incoming_key = '', $arr_of_keys_to_strip = array())
	{
 		$qs          = '';
 		$curr_script = $_SERVER['PHP_SELF'];
 		$filename    = substr($curr_script, strrpos($curr_script, '/')+1 );

   		if($filename == 'index.php')
		{
     		$filename = self::get_current_base_path();
   		}

   		else
		{
    		$filename = self::get_current_base_path(). $filename;
   		}
 
   		if($include_query_string)
		{
    		$qs  = '?s='. time(uniqid());

      		foreach($_GET AS $key => $value)
			{
         		if( ($key != 's') && ($key != $incoming_key) && (!in_array($key, $arr_of_keys_to_strip)) )
				{
          			$qs .= "&". $key. "=". urlencode($value);
         		}
      		}
   		}
 
 		return self::get_server_protocol(). $_SERVER['HTTP_HOST']. $filename. $qs;
	}

	public static function get_current_page_qs()
	{
 		$qs = '';
   		foreach($_GET AS $key => $value)
		{
     		if($key != 's')
			{
       			$qs .= "&". $key. "=". $value;
      		}
   		}
 		return $qs;
	}

	public static function url_contains_query_string($url)
	{
 		return strpos($url, '?');
	}
	
	public static function url_file_found($file_path)
	{
   		if(file_get_contents($file_path))
		{
     		return true;
   		}
 		return false;
	}

	public static function url_exists($url)
	{
  		$parts=parse_url($url);

  		if(!$parts) return false; /* the URL was seriously wrong */
 
  		$ch = curl_init();

  		curl_setopt($ch, CURLOPT_URL, $url);
 
  		/* set the user agent - might help, doesn't hurt */
  		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 
  		/* try to follow redirects */
  		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 
  		/* 
		* timeout after the specified number of seconds. assuming that this script runs 
    	* on a server, 20 seconds should be plenty of time to verify a valid URL.  
		*/
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
 
  		/* don't download the page, just the header (much faster in this case) */
  		curl_setopt($ch, CURLOPT_NOBODY, true);
  		curl_setopt($ch, CURLOPT_HEADER, true);
 
  		/* handle HTTPS links */
  		if($parts['scheme']=='https')
		{
  			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  1);
  			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  		}
 
  		$response = curl_exec($ch);
 		curl_close($ch);
 
  		/*  get the status code from HTTP headers */
  		if(preg_match('/HTTP\/1\.\d+\s+(\d+)/', $response, $matches))
		{
  			$code=intval($matches[1]);
  		} 
		else
		{
  			return false;
  		};
 
  		/* see if code indicates success */
  		return (($code>=200) && ($code<400));	
	}
}