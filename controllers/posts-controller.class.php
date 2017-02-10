<?php

class PostsController extends AppController
{
	public function execute()
	{
		/* 
		 * inherits from base controller: read-only
		 * $this->request;
		 * $this->request_parts;
		 * $this->page;
		 * $this->current_theme_pages_dir;
		 * $this->current_theme_templates_dir;
		*/
		
		/* 
		 * inherits from base controller: read-write
		*/
		$this->templates_dir;
		$this->pages_dir     = 'posts';

		if($this->page == 'new') {
			$page_data = array(
				'page_title'       => 'Create new post',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'open_graph_data'  => array(
					'url'          => SITE_URL. '/'. $this->request,
					'title'        => 'Create new post',
					'description'  => '',
					'image-url'    => '',
					'content-type' => 'website',
			));
			
			$this->_display('create.php', $page_data, $container_template='');
		}
		//e.g http://localhost/sites/naija-so/posts/11/
		else if(is_numeric($this->page)) {
			$post_id  = $this->page;
			$post     = PostModel::get_post_instance($post_id);
			$post_title = $post->get('title');
			$post_content = $post->get('content');
			
			$tags     = $post->get_tags(); 
			$keywords = '';
			for($i=0, $len=count($tags); $i < $len; $i++): 
				$tag = TagModel::get_tag_instance($tags[$i]);
				$keywords .= $tag->get('name'). ',';
			endfor;
			
			if(get_uri_end_point( array('ignore_query_string'=>true) ) == 'edit') {
				$page_file = 'edit-post.php';
			}
			else {
				update_post_view($post_id);
				$page_file = 'view.php';
			}
			
			$page_data = array(
				'page_title'       => sanitize_html_attribute( $post_title ),
				'page_keywords'    => $keywords,
				'page_description' => sanitize_html_attribute( $post_content ),
				'robots_value'     => 'all',
				'post_id'          => $post_id,
				'post'             => $post,
				'open_graph_data'  => array(
					'url'          => get_post_url($post_id),
					'title'        => sanitize_html_attribute( $post_title ),
					'description'  => sanitize_html_attribute( $post_content ),
					'image-url'    => get_post_image_url($post_id),
					'content-type' => 'article',
			));
			
			$this->_display($page_file, $page_data, $container_template='');
		}
		//else if ( $this->page == 'forum' || $this->page == 'category' )
		else { 
			$part  = ( $this->page == 'forum' || $this->page == 'category' || $this->page == 'tagged' || $this->page == 'author') ? urldecode( $this->request_parts[2] ) : '';
			$limit = 20;
			$order_data = array('date_created'=>'DESC');
			$image_url  = SITE_URL. '/logo-large.png';
			
			switch($this->page)
			{
				case 'forum' :
						$forum_id   = (is_integer($part) || is_numeric($part)) ? $part : get_forum_id($part);
						$forum      = ForumModel::get_forum_instance( $forum_id );
						//$post_ids   = $forum->get_posts(array('parent_id'=>0), $order_data, $limit);
						
						$categories = $forum->get_categories();
						$page_title = $forum->get('name'). ' forum posts';
						$page_description = 'View '. $forum->get('name'). ' forum posts';
						$page_keywords = '';
						for($i=0, $len=count($categories); $i < $len; $i++): 
							$category = CategoryModel::get_category_instance($categories[$i]);
							$page_keywords .= $category->get('name'). ',';
						endfor;
						break;
						
				case 'category' : 
						$category_id = (is_integer($part) || is_numeric($part)) ? $part : get_category_id($part);
						$category    = CategoryModel::get_category_instance( $category_id );
						//$post_ids    = $category->get_posts(array('parent_id'=>0), $order_data, $limit);
						
						$page_title = $category->get('name'). ' category posts';
						$page_description = 'View '. $category->get('name'). ' category posts';
						$page_keywords = '';
						break;
						
				case 'tagged':
						$tag_id   = (is_integer($part) || is_numeric($part)) ? $part : get_tag_id($part);
						$tag      = TagModel::get_tag_instance( $tag_id );
						//$post_ids = $tag->get_posts(array('parent_id'=>0), $order_data, $limit);
						
						$page_title = $tag->get('name'). ' tag posts';
						$page_description = 'View posts tagged '. $tag->get('name');
						$page_keywords = '';
						break;
						
				case 'author' : 
						$author_id = get_user_id($part);
						$author    = UserModel::get_user_instance( $author_id );
						//$post_ids  = get_user_posts($author_id, array('parent_id'=>0), $order_data, $limit);
						
						$page_title = 'Posts by '. $author->get('username');
						$page_description = 'View posts created by '. $author->get('username');
						$page_keywords = $author->get('username'). '';
						break;
				
				default : //index page
						//$post_ids = PostModel::get_posts( true, array('parent_id'=>0), $order_data, $limit ); //get only top-level posts
						$page_title = 'Recent posts';
						$page_description = '';
						$page_keywords    = '';
			}
			
			$page_data = array(
				'page_title'       => $page_title,
				'page_keywords'    => $page_keywords,
				'page_description' => $page_description,
				'robots_value'     => 'all',
				//'posts'            => $post_ids,
				'open_graph_data'  => array(
					'url'          => SITE_URL. '/'. $this->request,
					'title'        => $page_title,
					'description'  => $page_description,
					'image-url'    => $image_url,
					'content-type' => 'website',
			));
			
			$this->_display('index.php', $page_data, $container_template='');
		}
		
		/*
		else
		{
			$page_data = array(
				'page_title'       => '',
				'page_keywords'    => '',
				'page_description' => '',
				'robots_value'     => 'all',
				'posts'            => PostModel::get_posts( true, array('parent_id'=>0), array('date_created'=>'DESC'), 20 ), //get only top-level posts
				'open_graph_data'  => array(
					'url'          => generate_url(array('controller'=>'')),
					'title'        => '',
					'description'  => '',
					'image-url'    => '',
					'content-type' => 'website',
			));
			
			$this->_display('index.php', $page_data, $container_template='');
		}
		*/
	}
}