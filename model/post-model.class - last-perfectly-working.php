<?php
class PostModel extends BaseModel
{
	/*
	* parent_id [optional for new posts, required for replies to posts]
	* title [optional for a reply to a post]
	* content
	* author_id
	* forum_id [optional for a reply to a post],
	* category_id [optional for a reply to a post]
	* tags [optional (for a reply to a post)]
	*/
	public static function create($data)
	{
		if( !isset($data['parent_id']) )
		{
			$data['parent_id'] = 0;
		}
		
		extract($data);
		$tp  = self::get_tables_prefix();
		
		//It's not a parameter of the query, in that you don't have to supply a value to MySQL.
		//http://stackoverflow.com/a/9497221/1743192
		try
		{
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}posts (`parent_id`, `author_id`, `title`, `content`, `date_added`) VALUES (?, ?, ?, ?, NOW())" );
			$stmt->bind_param("iiss", $parent_id, $author_id, $title, $content);
			$stmt->execute();
			
			if($stmt->affected_rows)
			{
				$post = self::get_post_instance($stmt->insert_id);
				
				if( isset($forum_id) )
				{
					$post->add_to_forum($forum_id);
				}
				
				if( isset($category_id) )
				{
					$post->add_to_category($category_id);
				}
				
				if( isset($tags) && is_array($tags) )
				{
					array_walk( $tags, array($post, 'tag') );
					/*
					foreach($tags AS $tag)
					{
						$post->tag($tag);
					}
					*/
				}
				
				return $post;
			}
			
			$stmt->close();
			//$dbh->close();
		}
		catch(Exception $e)
		{
			if( is_development_server() )
			{
				die( $e->getMessage() );
			}
			return null;
		}
	}
	
	public static function get_post_instance($post_id)
	{
		return new Post($post_id);
	}
	
	public static function get_posts( $ids_only = true, $where=array(), $order=array(), $limit = 0 )
	{
		$tp      = self::get_tables_prefix();
		$sel_str = $ids_only ? "`id`" : "`id`, `parent_id`, `name`, `description`, `creator_id`, `date_added`";
		$select  = "SELECT $sel_str FROM {$tp}posts";
		$posts   = array();
		$where_data = parent::parse_where_data($where);
		
		if( !empty($where_data['where_clause']) )
		{
			$select .= " WHERE ". $where_data['where_clause'];
		}
		
		/*
		if( !empty($limit) )
		{
			$select .= " LIMIT ?";
		}
		*/
		
		try
		{
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( $select );
			
			if( !empty($where_data['where_values']) )
			{
				$stmt = parent::bind_statement_replacement_values( $stmt, parent::parse_replacement_values($where_data['where_values']) );
			}
			
			$stmt->execute();
			
			if($ids_only)
			{
				$stmt->bind_result($post_id);
				while ($stmt->fetch())
				{
					$posts[] = $post_id;
				}
			}
			else
			{
				$stmt->bind_result($post_id, $parent_id, $name, $description, $creator_id, $date_added);
				while ($stmt->fetch())
				{
					$posts[] = array('id'=>$post_id, 'parent_id'=>$parent_id, 'name'=>$name, 'description'=>$description, 'creator_id'=>$creator_id, 'date_added'=>$date_added);
				}
			}
			
			return $posts;
		}
		catch(Exception $e)
		{
			if( is_development_server() )
			{
				die( $e->getMessage() );
			}
			return array();
		}
	}
}


//See notes on Forum class definition
class Post extends PostModel
{
	private $post_id;
	
	public function __construct($id)
	{
		parent::__construct();
		$this->set_id($id);
	}
	
	/*
	* category_id
	* author_id
	* title
	* content
	*/
	public function update($update_data)
	{
		$udata = array();
		$where = array( 'id'=>$this->get_id() );
		
		foreach( $update_data AS $key => $value )
		{
			if( ($key != 'id') && ($key != 'parent_id') && ($key != 'date_added') )
			{
				$udata[$key] = $value;
			}
		}
		
		return $this->update_table( self::get_tables_prefix(). "posts", $udata, $where );
	}
	
	public function record_view($viewer_id)
	{
		$tp  = self::get_tables_prefix();
		$pid = $this->get_id();
		
		try
		{
			$dbh  = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}post_views (`post_id`, `viewer_id`, `date_viewed`) VALUES ($pid, $viewer_id, NOW())" );
			$stmt->execute();
			
			if($stmt->affected_rows)
			{
				return $stmt->insert_id;
			}
			
			$stmt->close();
			//$dbh->close();
		}
		catch(Exception $e)
		{
			if( is_development_server() )
			{
				die( $e->getMessage() );
			}
			return null;
		}
	}
	
	public function get_view_data($count=true)
	{
		$tp  = self::get_tables_prefix();
		$dbh = self::get_db_connection();
		$str = $count ? "COUNT(*) AS view_count" : "`post_id`, `viewer_id`, `date_viewed`";
		
		try
		{
			$stmt = $dbh->prepare( "SELECT $str FROM  {$tp}post_views WHERE `post_id` = ". $this->get_id() );
			$stmt->execute();
			
			if($count)
			{
				$stmt->bind_result($view_count);
				$stmt->fetch();
				return $view_count;
			}
			else
			{
				$result = $stmt->get_result();
				return $result->fetch_assoc();
			}
		}
		catch(Exception $e)
		{
			if( is_development_server() )
			{
				die( $e->getMessage() );
			}
			return array();
		}
	}
	
	public function get($data='')
	{
		$tp  = self::get_tables_prefix();
		$dbh = self::get_db_connection();
		
		if( empty($data) )
		{
			$sel_str = "*";
		}
		else
		{
			if( is_string($data) )
			{
				$sel_str = "`$data`";
			}
			else if( is_array($data) )
			{
				$sel_str = '';
				foreach($data AS $data_key)
				{
					$sel_str .= "`$data_key`, ";
				}
				$sel_str = rtrim( trim($sel_str), ',' );
			}
		}
		
		try
		{
			$stmt = $dbh->prepare( "SELECT $sel_str FROM  {$tp}posts WHERE `id` = ". $this->get_id() );
			$stmt->execute();
			
			if( empty($data) || is_array($data) )
			{
				$result = $stmt->get_result();
				return $result->fetch_assoc();
			}
			else if(is_string($data))
			{
				$stmt->bind_result($$data);
				$stmt->fetch();
				return $$data;
			}
		}
		catch(Exception $e)
		{
			if( is_development_server() )
			{
				die( $e->getMessage() );
			}
			return '';
		}
	}
	
	public function delete()
	{
		return $this->delete_from_table( self::get_tables_prefix(). 'posts', $this->get_id() );
	}
	
	public function add_to_forum($forum)
	{
		if( empty($forum) )
		{
			return false;
		}
		
		return $this->insert( self::get_tables_prefix(). 'forum_posts', array('forum_id'=>$this->get_forum_id($forum), 'post_id'=>$this->get_id()) );
	}
	
	public function add_to_category($category)
	{
		if( empty($category) )
		{
			return false;
		}
		
		return $this->insert( self::get_tables_prefix(). 'category_posts', array('category_id'=>$this->get_category_id($category), 'post_id'=>$this->get_id()) );
	}
	
	public function tag($tag)
	{
		if( empty($tag) )
		{
			return false;
		}
		
		return $this->insert( self::get_tables_prefix(). 'tag_posts', array('tag_id'=>$this->get_tag_id($tag), 'post_id'=>$this->get_id()) );
	}
	
	public function remove_from_forum($forum)
	{
		if( empty($forum) )
		{
			return false;
		}
		
		$db = self::get_db();
		
		$db->delete_records( self::get_tables_prefix(). 'forum_posts', array('forum_id'=>$this->get_forum_id($forum), 'post_id'=>$this->get_id()) );
	}
	
	public function remove_from_category($category)
	{
		if( empty($category) )
		{
			return false;
		}
		
		$db = self::get_db();
		
		$db->delete_records( self::get_tables_prefix(). 'category_posts', array('category_id'=>$this->get_category_id($category), 'post_id'=>$this->get_id()) );
	}
	
	public function untag($tag)
	{
		if( empty($tag) )
		{
			return false;
		}
		
		$db = self::get_db();
		
		$db->delete_records( self::get_tables_prefix(). 'tag_posts', array('tag_id'=>$this->get_tag_id($tag), 'post_id'=>$this->get_id()) );
	}
	
	public function get_forums()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$forum_ids = array();
		
		$db->execute_query( "SELECT `forum_id` FROM {$tp}forum_posts WHERE `post_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$forum_ids[] = $row['forum_id'];
		}
		
		return $forum_ids;
	}
	
	public function get_categories()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$post_ids = array();
		
		$db->execute_query( "SELECT `category_id` FROM {$tp}category_posts WHERE `post_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$category_ids[] = $row['category_id'];
		}
		
		return $category_ids;
	}
	
	public function get_tags()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$post_ids = array();
		
		$db->execute_query( "SELECT `tag_id` FROM {$tp}tag_posts WHERE `post_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$post_ids[] = $row['tag_id'];
		}
		
		return $post_ids;
	}
	
	public function get_parent()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$db->execute_query( "SELECT `parent_id` FROM {$tp}posts WHERE `id` = ". $this->get_id() );
		$row = $db->get_rows();
		return $row['parent_id'];
	}
	
	public function get_comments($count=true)
	{
		/*
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$post_ids = array();
		$db->execute_query( "SELECT `id` FROM {$tp}posts WHERE `parent_id` = ". $this->get_id() );
		while( $row = $db->get_rows() )
		{
			$post_ids[] = $row['id'];
		}
		
		return $post_ids;
		*/
		$comments = parent::get_posts(true, array('parent_id'=>$this->get_id()));
		return $count ? count($comments) : $comments;
	}
	
	public function belongs_to_forum($forum)
	{
		if(empty($forum))
		{
			return false;
		}
		
		return in_array( $this->get_forum_id($forum), $this->get_forums() );
	}
	
	public function belongs_to_category($category)
	{
		if(empty($category))
		{
			return false;
		}
		
		return in_array( $this->get_category_id($category), $this->get_categories() );
	}
	
	public function is_tagged($tag)
	{
		if(empty($tag))
		{
			return false;
		}
		
		return in_array( $this->get_tag_id($tag), $this->get_tags() );
	}
	
	private function get_id()
	{
		return $this->post_id;
	}
	
	private function set_id($id)
	{
		$this->post_id = $id;
	}
	
	private function get_forum_id($forum)
	{
		return ( (is_integer($forum) || is_numeric($forum)) ? $forum : get_forum_id($forum) );
	}
	
	private function get_category_id($category)
	{
		return ( (is_integer($category) || is_numeric($category)) ? $category : get_category_id($category) );
	}
	
	private function get_tag_id($tag)
	{
		return ( (is_integer($tag) || is_numeric($tag)) ? $tag : get_tag_id($tag) );
	}
}
