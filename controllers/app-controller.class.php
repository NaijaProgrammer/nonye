<?php
/*
* Application specific controller functionality goes here
* Specific controllers should extend this controller
*/

class AppController extends BaseController
{
	private $curr_user; //object representing the current logged-in user
	
	public function __construct()
	{
		parent::__construct();
		$this->page_instance = Page::get_instance();
		$this->current_user  = UserModel::user_is_logged_in() ? UserModel::get_user_instance(UserModel::get_current_user_id()) : null;
	}
	
	#@Override
	protected function _display($page, $page_data = array(), $container_template = 'default-page-template')
	{
		$page_data['current_user']  = $this->current_user;
		$page_data['page_instance'] = $this->page_instance;
		$page_data['site_url']      = SITE_URL;
		$page_data['theme_url']     = $this->page_instance->get_theme_url();
		$page_data['theme_dir']     = $this->page_instance->get_theme_dir();
		parent::_display($page, $page_data, $container_template);
	}
}