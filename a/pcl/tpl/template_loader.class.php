<?php

//require_once('template.class.php');

/*
* @author Michael Orji
*/

Class TemplateLoader  
{
	protected $tpl_file = '';
	
	/*
 	* @Variables array
 	* @access private
 	*/
	protected $vars = array();

	/**
 	*
 	* @constructor
 	* @access public
 	* @return void
 	*/
	function __construct($tpl_file) 
	{
		$this->set_template_file($tpl_file);
	}
	
	public function set_template_file($tpl_file)
	{
		$this->tpl_file = $tpl_file;
	}
	
	public function get_template_file()
	{
		return $this->tpl_file;
	}


 	/**
 	*
 	* @set undefined vars
 	* @param string $index
 	* @param mixed $value
 	* @return void
 	*/
 	public function __set($index, $value)
	{
        $this->vars[$index] = $value;
 	}


	public function show() 
	{
		$this->_display(true);
	}

	public function load()
	{
		return $this->_display(false);
	}

	protected function _display($output_directly) 
	{
		$template = new Template($this->get_template_file());

        	// Load variables
        	foreach ($this->vars as $key => $value)
			{
				$template->$key = $value; //using magic __set method
                //$template->set($key, $value); //alternate method
        	}

		if(!$output_directly)
		{
			return $template->output();
		}
		
		echo $template->output();
	}
}