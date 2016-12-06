<?php

//require_once('template_loader.class.php');

Class RegistryTemplateLoader extends TemplateLoader 
{
	/*
 	* @the registry
 	* @access protected
 	*/
	protected $registry;

	/**
 	*
 	* @constructor
 	* @access public
 	* @return void
 	*/
	public function __construct($registry, $template_file) 
	{
        $this->registry = $registry;
		parent::__construct($template_file);
	}
	
	public function load_extra_template($template_file)
	{
		$tpl = new parent($template_file);
		return $tpl;
	}
}