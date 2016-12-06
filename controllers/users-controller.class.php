<?php

class UsersController extends AppController
{
	public function execute()
	{ 
		/* 
		 * inherits from base controller: read-only
		 * $this->request ;
		 * $this->page;
		 * $this->current_theme_pages_dir;
		 * $this->current_theme_templates_dir;
		*/
		
		/* 
		 * inherits from base controller: read-write
		*/
		$this->templates_dir;
		$this->pages_dir = 'users';
		
		//if(get_uri_end_point() == 'edit')
		if( stristr( get_uri_end_point( array('ignore_query_string'=>true) ), 'edit' ) !== FALSE )
		{
			//$user_id = $this->page;
			$this->_display('edit.php', array(
				'page_title'       => 'Edit Account Settings',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'open_graph_data'  => array(
					'url'          => PageModel::generate_url(array('controller'=>'users')),
					'title'        => 'Edit account settings',
					'description'  => '',
					'image-url'    => '',
					'content-type' => 'website',
				)
			), $container_template = 'none');
		}
		
		//else if($this->page == 'password-retrieve')
		else if(get_uri_end_point( array('ignore_query_string'=>true) ) == 'password-retrieve')
		{
			$this->_display('forgot-password.php', array(
				'page_title'       => 'Get a link to reset your password',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'open_graph_data'  => array(
					'url'          => '',
					'title'        => '',
					'description'  => '',
					'image-url'    => '',
					'content-type' => '',
				)
			), $container_template = '');
		}
		
		//else if($this->page == 'password-reset')
		else if(get_uri_end_point( array('ignore_query_string'=>true) ) == 'password-reset')
		{ 
			$this->_display('password-reset.php', array(
				'page_title'       => 'Reset your password',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'open_graph_data'  => array(
					'url'          => '',
					'title'        => '',
					'description'  => '',
					'image-url'    => '',
					'content-type' => 'website',
				)
			), $container_template = '');
		}
		
		//e.g http://localhost/sites/naija-so/users/11/{user-display-name}
		else if(is_numeric($this->page))
		{
			$owner_id = $this->page;
			$owner    = UserModel::get_user_instance($owner_id);
			$owner_username = $owner->get('username');
			
			update_user_profile_view_count($owner_id);
			$this->_display('profile.php', array(
				'page_title'       => $owner_username. ' \'s profile',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'owner_id'         => $owner_id, 
				'owner'            => $owner,
				'open_graph_data'  => array(
					'url'          => get_user_profile_url($owner_id),
					'title'        => '',
					'description'  => 'View  '. $owner_username. ' on ',
					'image-url'    => '',
					'content-type' => 'website',
				)
			), $container_template = '');
		}
		
		else
		{ 
			$this->_display('index.php', array(
				'page_title'       => 'Users list',
				'page_keywords'    => '',
				'page_description' => 'Browse users on ',
				'robots_value'     => 'all',
				'open_graph_data'  => array(
					'url'          => SITE_URL. '/users',
					'title'        => 'View all users on ',
					'description'  => 'View users on ',
					'image-url'    => '',
					'content-type' => 'website',
				)
			), $container_template = 'default-page-template');
		}
	}
}