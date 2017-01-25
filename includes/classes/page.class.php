<?php
class Page
{
	private $theme;
	private $theme_dir;
	private $theme_url;
	private static $instance = null;
	
	public static function get_instance($opts = array())
	{
		if(!self::$instance)
		{
			self::$instance = new self($opts);
		}
		
		return self::$instance;
	}
	
	public function __construct($opts = array())
	{
		//$theme = isset($opts['theme']) ? $opts['theme'] : get_current_theme();
		$theme = get_current_theme();
		$this->set_theme($theme);
	}
	
	public function get_theme()
	{
		return $this->theme;
	}
	
	public function get_theme_dir()
	{
		return $this->theme_dir;
	}
	
	public function get_theme_url()
	{
		return $this->theme_url;
	}
	
	private function set_theme($theme)
	{
		$this->theme = !empty($theme) ? $theme : get_current_theme();
		$this->set_theme_dir( $this->get_theme() );
		$this->set_theme_url( $this->get_theme() );
	}
	
	private function set_theme_dir($theme)
	{
		if(!is_dir(VIEWS_DIR. '/'. $theme))
		{
			trigger_error('Theme "'. $theme. '" directory does not exist', E_USER_ERROR);
		}
		else
		{
			$this->theme_dir = VIEWS_DIR. '/'. $theme;
		}
	}
	
	private function set_theme_url($theme)
	{
		if(!is_dir(VIEWS_DIR. '/'. $theme))
		{
			trigger_error('Theme "'. $theme. '" directory does not exist', E_USER_ERROR);
		}
		else
		{
			//$this->theme_url = VIEWS_URL. '/'. $theme;
			$this->theme_url = SITE_URL. '/themes/'. $theme;
		}
	}
	
	public function add_header()
	{
		$data     = array();
		$header   = 'default';
		$num_args = func_num_args();
		
		if( ($num_args > 0) )
		{
			$first_arg = func_get_arg(0);
			$header = ( !empty($first_arg) && is_string($first_arg) ? $first_arg : 'default' );
			$data   = ( !empty($first_arg) && is_array($first_arg)  ? $first_arg : ( ( $num_args > 1 ) ? func_get_arg(1) : array() ) );
		}
		
		$this->load_template_part('headers/'. $header. '.php', $data);
	}
	
	public function add_footer()
	{
		$data     = array();
		$footer   = 'default';
		$num_args = func_num_args();
		
		if( ($num_args > 0) )
		{
			$first_arg = func_get_arg(0);
			$footer = ( !empty($first_arg) && is_string($first_arg) ? $first_arg : 'default' );
			$data   = ( !empty($first_arg) && is_array($first_arg)  ? $first_arg : ( ( $num_args > 1 ) ? func_get_arg(1) : array() ) );
		}
		
		$this->load_template_part('footers/'. $footer. '.php', $data);
	}
	
	public function add_nav()
	{
		$data     = array();
		$nav      = 'main-navigation';
		$num_args = func_num_args();
		
		if( ($num_args > 0) )
		{
			$first_arg = func_get_arg(0);
			$nav  = ( !empty($first_arg) && is_string($first_arg) ? $first_arg : 'main-navigation' );
			$data = ( !empty($first_arg) && is_array($first_arg)  ? $first_arg : ( ( $num_args > 1 ) ? func_get_arg(1) : array() ) );
		}
		
		$this->load_template_part('nav/'. $nav. '.php', $data);
	}

	public function add_sidebar()
	{
		$data     = array();
		$sidebar  = 'default';
		$num_args = func_num_args();
		
		if( ($num_args > 0) )
		{
			$first_arg = func_get_arg(0);
			$sidebar = ( !empty($first_arg) && is_string($first_arg) ? $first_arg : 'default' );
			$data    = ( !empty($first_arg) && is_array($first_arg)  ? $first_arg : ( ( $num_args > 1 ) ? func_get_arg(1) : array() ) );
		}
		
		$this->load_template_part('sidebars/'. $sidebar. '.php', $data);
	}

	public function add_form($form, $data = array() )
	{
		$this->load_template_part('forms/'. $form. '.php', $data);
	}
	
	public function close_page($data = array())
	{
		$this->add_fragment('page-close', $data);
	}
	
	public function add_fragment($fragment, $data = array())
	{
		$this->load_template_part('fragments/'. $fragment. '.php', $data);
	}
	
	/*
	* Returns the (parsed) contents of the specified template fragment
	*/
	public function get_template_fragment( $fragment, $data = array() )
	{
		$fragment = $this->get_theme_dir(). '/template-fragments/'. $fragment. '.php';
		
		return file_exists($fragment) ? parse_file_contents($fragment, $data) : '';
	}
	
	public function add_stylesheets($styles)
	{  
		$scr = '<script>';
		
		foreach($styles AS $style)
		{
			$scr .= '(function(){';
			$scr .= 'var link = document.createElement("link");';
			$scr .= 'link.rel="stylesheet";';
			$scr .= 'link.href="'. $style. '";';
			$scr .= 'document.getElementsByTagName("head")[0].appendChild(link);';
			$scr .= '})();';
		}
		
		$scr .= '</script>';
		echo $scr;
	}
	
	public function add_scripts($scripts)
	{
		$scr = '<script>';
		
		foreach($scripts AS $script)
		{
			$scr .= '(function(){';
			$scr .= 'var s = document.createElement("script");';
			$scr .= 's.src="'. $script. '";';
			$scr .= 'document.getElementsByTagName("head")[0].appendChild(s);';
			$scr .= '})();';
		}
		
		$scr .= '</script>';
		echo $scr;
	}
	
	public function load_404_page()
	{
		$pages_dir = $this->get_theme_dir(). '/pages';
		
		if( file_exists($pages_dir. '/404.php') )
		{ 
			include $pages_dir. '/404.php';
		}
		
		elseif( file_exists( SITE_DIR. '/404.php') )
		{
			include SITE_DIR. '/404.php';
		}
		
		else
		{
			echo("The Page you are looking for was not found");
		}
	}
	
	/*
	* $opts data members:
	* current_url
	* redirect_url
	*/
	public function authenticate_user( $opts = array() )
	{
	    extract($opts);

		if( UserModel::user_is_logged_in() )
		{
			if($current_url == $redirect_url)
			{
				return;
			}
			if( !empty($redirect_url) )
			{
				header("Location:". $redirect_url);
				exit;
			}
		}
		
		else if(!UserModel::user_is_logged_in())
		{
			if(empty($redirect_url))
			{
				$redirect_url = get_request_url();
			}
			
			include $this->get_theme_dir(). '/pages/unauthenticated-user-page.php';
			exit;
		}
	}
	
	private function load_template_part($template_part, $options)
	{
		//define variables that every template-part gets access to
		$options['site_url']      = SITE_URL;
		$options['theme_url']     = $this->get_theme_url();
		$options['theme_dir']     = $this->get_theme_dir();
		$options['current_user']  = ( UserModel::user_is_logged_in() ? UserModel::get_user_instance(UserModel::get_current_user_id()) : null );
		$options['page_instance'] = $this;
		
		extract($options);
		include( $this->get_theme_dir(). '/template-fragments/'. $template_part );
	}
}