<?php

class IndexController extends AppController
{
	public function execute()
	{
		//$this->get_controller('posts')->execute();
		//exit;
		
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
		$this->pages_dir     = 'index';
		
		switch($this->page)
		{
			default : $page_file =  'index.php'; 
					  $container_template = '';
			          $page_data = array(
						'page_title'       => '',
						'page_keywords'    => '',
						'page_description' => '',
						'robots_value'     => 'all',
						'open_graph_data'  => array(
							'url'          => generate_url(array('controller'=>'')),
							'title'        => '',
							'description'  => '',
							'image-url'    => '',
							'content-type' => 'website',
						)
					  );
		}
		
		$this->_display($page_file, $page_data, $container_template);
	}
}