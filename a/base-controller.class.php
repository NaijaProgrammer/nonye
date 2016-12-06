<?php
/*
 * Class BaseController
 * @author Michael Orji
*/
abstract class BaseController
{
	/*
	 * The request object containing the request parameters
	*/
	protected $request; 
	
	/*
	* Array containing the various parts of the request
	*/
	protected $request_parts;
	
	/*
	 * the currently requested page
	 * one of the member parameters of the request object
	*/
	protected $page; 
	
	/*
	 * the current theme directory template path
	 * The directory where the all template files for the currently selected theme reside
	 * @access read-only
	*/
	protected $current_theme_templates_dir; 
	
	/*
	 * the current theme directory page path
	 * The directory where the all page files for the currently selected theme reside
	 * @access read-only
	*/
	protected $current_theme_pages_dir;
	
	/*
	 * the directory where templates reside, 
	 * this is set by individual controllers.
	 * The default is the index directory
	*/
	protected $templates_dir  = 'index';
	
	/*
	 * the directory where the pages to display reside, 
	 * this is set by individual controllers.
	 * The default is the index directory
	*/
	protected $pages_dir  = 'index';
	
	/*
	 * Constructor function
	*/
	public function __construct()
	{
		$this->request = new PathModel();
		$this->request_parts = $this->request->get_parts();
		$this->page    = !empty($this->request->action) ? $this->request->action : '';
		$this->current_theme_pages_dir     = VIEWS_DIR. '/'. get_current_theme(). '/pages';
		$this->current_theme_templates_dir = VIEWS_DIR. '/'. get_current_theme(). '/templates';
	}
	
	protected function get_controller($controller)
	{
		$controller_name = $controller. '-controller';
		$controller_file = $controller_name. '.class.php';
			
		if(file_exists(CONTROLLERS_DIR. '/'. $controller_file))
		{  
			$controller_class = explode('-', $controller_name);
			$controller_class = implode(' ', $controller_class);
			$controller_class = UCWORDS($controller_class);
			$controller_class = explode(' ', $controller_class);
			$controller_class = implode('', $controller_class);
				
			require_once(CONTROLLERS_DIR. '/'. $controller_file);
			$controller_object = new $controller_class();
			return $controller_object;
		}
		
		return null;
	}
	
	/*
	 * Function _display 
	 * Retrieves, parses and displays the supplied page file
	 * @access protected
	*/
	protected function _display($page, $page_data = array(), $container_template = 'default-page-template')
	{ 
		extract($page_data);
		
		if( empty($container_template) )
		{ 
			include $this->current_theme_pages_dir. '/'. $this->pages_dir. '/'. $page;
		}
		
		elseif( !empty($container_template) )
		{
			$page_content = $this->_parse_page_file_($this->current_theme_pages_dir. '/'. $this->pages_dir. '/'. $page, $page_data);
			
			if(file_exists($this->current_theme_templates_dir. '/'. $container_template. '.php'))
			{
				include $this->current_theme_templates_dir. '/'. $container_template. '.php';
			}
			
			else
			{
				include $this->current_theme_templates_dir. '/default-page-template.php';
			}
		}
	}
	
	/*
	 * Function _parse_page_file_
	 * Parse any PHP codes present in the template file, and return the template file's output
	 * @credits: http://stackoverflow.com/a/2061047
	 * $data = data to be passed to the template file, in the form of php variables
	 * @access private
	*/
	private function _parse_page_file_($file, $page_data=array())
	{ 
		ob_start(); // start output buffer
		extract($page_data);
		include $file;
		
		$parsed_content = ob_get_contents(); // get contents of buffer
		
		ob_end_clean();
		return $parsed_content;
	}
}