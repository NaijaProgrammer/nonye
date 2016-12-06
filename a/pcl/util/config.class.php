<?php

class Config
{
	private static $instance;
	private $vars = array();

	public static function get_instance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new self;
		}
		return self::$instance;
	}
	public function is_local_server()
	{
		return (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168'));
	}
	public function get_env()
	{
		return ( $this->is_local_server() ? 'development' : 'production' );
	}
	public function __set($key, $value)
	{
		$this->vars[$key] = $value;	
	}
	public function __get($key)
	{
		return $this->vars[$key];
	}
}