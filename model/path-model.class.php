<?php
class PathModel extends BaseModel
{
	private $parts = array();
	public function __construct($include_query_string = true)
	{
		if (isset($_SERVER['PATH_INFO']))
		{
			$path = (substr($_SERVER['PATH_INFO'], -1) == "/") ? substr($_SERVER['PATH_INFO'], 0, -1) : $_SERVER['PATH_INFO'];
		}
		else
		{ 
			$path = (substr($_SERVER['REQUEST_URI'], -1) == "/") ? substr($_SERVER['REQUEST_URI'], 0, -1) : $_SERVER['REQUEST_URI'];
		}
		$path = $include_query_string ? $path : substr($path, 0, stripos($path, '?'));//remove query string from the path
		$bits = explode("/", substr($path, 1));
		
		if( stristr(UrlInspector::get_base_url(), 'localhost') !== FALSE )
		{ 
			array_shift($bits); //if on localhost, then the leading path is the folder containing the site, so remove that
			array_shift($bits); //on my system where we have /sites/site_folder_name
		}
		
		$controller = array_shift($bits);
		$action     = array_shift($bits);
		
		if( stristr($controller, '?') !== FALSE): $controller = substr($controller, 0, strpos($controller, '?')); endif;
		if( stristr($action, '?')     !== FALSE): $action     = substr($action,     0,  strpos($action,    '?')); endif;
		
		$parsed['controller'] = $parsed['directory'] = $parsed[] = $controller;
		$parsed['action']     = $parsed['page']      = $parsed[] = $action;
		$parts_size = sizeof($bits);
		
		if ($parts_size % 2 != 0)
		{
			$parts_size -= 1;
		}
		
		for ($i = 0; $i < $parts_size; $i+=2)
		{
			$parsed[$bits[$i]] = $bits[$i+1];
			$parsed[]          = $bits[$i+1];
		}
		
		if (sizeof($bits) % 2 != 0)
		{
			$parsed[] = array_pop($bits);
		}
		
		$this->parts = $parsed;
	}
	
	public function get_parts()
	{
		return $this->parts;
	}
	
	public function __get($key)
	{
		return $this->parts[$key];
	}
	
	public function __set($key, $value)
	{
		$this->parts[$key] = $value;
	}
	
	public function __isset($key)
	{
		return isset($this->parts[$key]);
	}
	
	/*
	* @author Michael Orji
	* @date Nov. 2, 2015 23:33 
	* Enables you to construct a pretty URL using this class e.g:
	* $pm = new PathModel();
	* $pm->controller = 'users';
	* $pm->action     = 'edit';
	* $pm->user_id    = 5;
	* echo SITE_URL. $pm; //will give something like : http://examplesite.com/users/edit/user_id/5
	*/
	public function __toString()
	{
		/*
		* TO DO:
		* implement in such a way that /controller/contrller_name/action/action_name/
		* become /controller_name/action_name/
		* to do that, you might use $this->parts[0] as controller, $this->parts[1] as action or use their string indices
		* then append the others to them
		*/
		$arr = array();
		
		
		/*
		* avoid returning duplicate parts like : users/users/users/5/5/5/mikky-mouse
		* by using only the numeric parts, removing paths like 'controller', 'action', 'page', etc
		*/
		foreach($this->parts AS $key => $value)
		{
			if(is_numeric($key))
			{
				$arr[$key] = $value;
			}
		}
		return implode("/", $arr);
		//return implode("/", $this->parts); //returns duplicate parts like : users/users/users/5/5/5/mikky-mouse
	}
}
