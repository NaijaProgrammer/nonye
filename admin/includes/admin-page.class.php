<?php
class AdminPage
{
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
		$theme = isset($opts['theme']) ? $opts['theme'] : 'default';
		$this->set_theme_dir($theme);
		$this->set_theme_url($theme);
	}
	
	public function get_theme_dir()
	{
		return $this->theme_dir;
	}
	
	public function get_theme_url()
	{
		return $this->theme_url;
	}
	
	public function set_theme_dir($theme)
	{
		if(!is_dir(ADMIN_VIEWS_DIR. '/'. $theme))
		{
			trigger_error('Theme "'. $them. '" directory does not exist', E_USER_ERROR);
		}
		else
		{
			$this->theme_dir = ADMIN_VIEWS_DIR. '/'. $theme;
		}
	}
	
	public function set_theme_url($theme)
	{
		if(!is_dir(ADMIN_VIEWS_DIR. '/'. $theme))
		{
			trigger_error('Theme "'. $them. '" directory does not exist', E_USER_ERROR);
		}
		else
		{
			$this->theme_url = ADMIN_VIEWS_URL. '/'. $theme;
		}
	}
	
	public function load_header($header = 'default', $data = array() )
	{
		$header = !empty($header) ? $header : 'default';
		$this->load_template_part($this->theme_dir. '/headers/'. $header. '.php', $data);
	}

	public function load_nav($nav = 'main-navigation', $data = array() )
	{
		$nav = !empty($nav) ? $nav : 'main-navigation';
		$this->load_template_part($this->theme_dir. '/nav/'. $nav. '.php', $data);
	}

	public function load_footer($footer = 'default', $data = array() )
	{
		$footer = !empty($footer) ? $footer : 'default';
		$this->load_template_part($this->theme_dir. '/footers/'. $footer. '.php', $data);
	}
	
	public function load_form($form, $data = array() )
	{
		$this->load_template_part($this->theme_dir. '/forms/'. $form. '.php', $data);
	}
	
	public function load_sidebar($sidebar, $data = array())
	{
		$sidebar = !empty($sidebar) ? $sidebar : 'default';
		$this->load_template_part($this->theme_dir. '/sidebars/'. $sidebar. '.php', $data);
	}

	public function load_fragment($fragment, $data = array())
	{
		$this->load_template_part($this->theme_dir. '/fragments/'. $fragment. '.php', $data);
	}
	
	private function load_template_part($template_part, $options)
	{
		//define variables that every template-part gets access to
		$options['theme_url'] = $this->get_theme_url();
		$options['current_user'] = UserModel::get_user_instance(UserModel::get_current_user_id());
		$options['page_instance'] = $this;
		
		extract($options);
		include($template_part);
	}
	
	public function close_page()
	{
		
	}
}