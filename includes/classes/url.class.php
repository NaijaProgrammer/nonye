<?php
require_once SITE_DIR. '/lib/simplehtmldom/simple_html_dom.php';	
class Url
{
	private $dom;
	
	public function __construct()
	{
		
	}
	
	public function create_from_string($str)
	{
		$this->set( str_get_html($str) );
	}
	
	public function create_from_url($url)
	{
		$this->set( file_get_html($url) );
	}
	
	public function set($d)
	{
		$this->dom = $d;
	}
	
	public function title()
	{
		$title = is_object($this->dom) ? $this->dom->find('title', 0) : null;
		return is_object($title) ? $title->plaintext : '';
	}
	
	public function image($index=0)
	{
		if($index < 0)
		{
			return is_object($this->dom) ? $this->dom->find('img') : null;
		}
		else
		{
			$image     = is_object($this->dom) ? $this->dom->find('img', $index) : null;
			$image_src = is_object($image)     ? $image->src : '';
			return $image_src;
		}
	}
	
	/*
	public function tag($tag_name, $index=0, $attribute='')
	{
		if($index < 0)
		{
			return is_object($this->dom) ? $this->dom->find($tag_name) : null;
		}
		else
		{
			$tag = is_object($this->dom) ? $this->dom->find($tag_name, $index) : null;
			return is_object($tag) ? $tag->{$attribute} : '';
		}
	}
	*/
	
	public function meta($meta_key='')
	{
		$metas = is_object($this->dom) ? $this->dom->find('meta') : array();
		
		if( empty($meta_key) )
		{
			return $metas;
		}
		
		else
		{
			foreach($metas AS $meta)
			{   
				if( isset($meta->name) && (strtolower($meta->name) == strtolower($meta_key)) )
				{
					return $meta->content;
				}
			}
			
			return '';
		}
	}
	
	public function description()
	{
		return $this->meta('description');
		/*
		$metas = is_object($this->dom) ? $this->dom->find('meta') : array();
		
		foreach($metas AS $meta)
		{   
			if( isset($meta->name) && (strtolower($meta->name) == 'description') )
			{
				return $meta->content;
			}
		}
		
		return '';
		*/
	}
	
	public function excerpt()
	{
		$excerpt = is_object($this->dom) ? $this->dom->find('p', 0) : null;
		return is_object($excerpt) ? $excerpt->plaintext : '';
	}
	
	/*
	// Function to submit form using cURL POST method
	public function post($url, $post_data, $success_string)
	{
		$useragent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';	// Setting user agent of a popular browser
		$cookie    = 'cookie.txt';	// Setting a cookie file to store cookie
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);	// Setting URL to POST to
		curl_setopt($ch, CURLOPT_POST, TRUE);	// Setting method as POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data)); // Setting POST fields as array
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	// Prevent cURL from verifying SSL certificate
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);	// Script should fail silently on error
		curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);	// Use cookies
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);	// Follow Location: headers
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	// Returning transfer as a string
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);	// Setting cookiefile
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);	// Setting cookiejar
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);	// Setting useragent
			
		$results = curl_exec($ch);
		curl_close($ch);
		
		// Checking if login was successful by checking existence of string
		if (strpos($results, $success_string))
		{
			return $results;
		} 
		else
		{
			return FALSE;
		}
	}
	*/
}