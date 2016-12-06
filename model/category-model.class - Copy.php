<?php
class CategoryModel extends BaseModel
{
	/*
	* creator_id
	* name
	* description
	*/
	public static function create($data)
	{
		extract($data);
		$tp  = self::get_tables_prefix();
		
		//It's not a parameter of the query, in that you don't have to supply a value to MySQL.
		//http://stackoverflow.com/a/9497221/1743192
		try
		{
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}categories (`creator_id`, `name`, `description`, `date_added`) VALUES (?, ?, ?, NOW())" );
			$stmt->bind_param("iss", $creator_id, $name, $description);
			$stmt->execute();
			$insert_id = $stmt->insert_id;
			$stmt->close();
			//$dbh->close();
			return self::get_category_instance($insert_id);
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
	
	public static function get_category_instance($category_id)
	{
		return new Category($category_id);
	}
	
	public static function get_categories( $ids_only = true, $where=array(), $order=array(), $limit = 0 )
	{
		$tp      = self::get_tables_prefix();
		$sel_str = $ids_only ? "`id`" : "`id`, `name`, `description`, `creator_id`, `date_added`";
		$select  = "SELECT $sel_str FROM {$tp}categories";
		$categories = array();
		$where_data = parent::parse_where_data($where);
		$replacement_values = array();
		
		if( !empty($where_data) )
		{
			$select .= " WHERE ". $where_data['where_clause'];
			$replacement_values = $where_data['where_values'];
		}
		
		$select .= parent:: parse_order_data($order, array('id', 'name', 'description', 'creator_id', 'date_added'));
		
		if( !empty($limit) )
		{
			$select .= " LIMIT ?";
			array_push($replacement_values, $limit);
		}
		
		try
		{
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( $select );
			
			if( !empty($replacement_values) )
			{
				$stmt = parent::bind_statement_replacement_values( $stmt, parent::parse_replacement_values($replacement_values) );
			}
			
			$stmt->execute();
			
			if($ids_only)
			{
				$stmt->bind_result($category_id);
				while ($stmt->fetch())
				{
					$categories[] = $category_id;
				}
			}
			else
			{
				$stmt->bind_result($category_id, $name, $description, $creator_id, $date_added);
				while ($stmt->fetch())
				{
					$categories[] = array('id'=>$category_id, 'name'=>$name, 'description'=>$description, 'creator_id'=>$creator_id, 'date_added'=>$date_added);
				}
			}
			
			return $categories;
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

//See notes on Forum Model definition
class Category extends CategoryModel
{
	private $category_id;
	
	public function __construct($id)
	{
		parent::__construct();
		$this->set_id($id);
	}
	
	/*
	* creator_id
	* name
	* description
	*/
	public function update($update_data)
	{
		$udata = array();
		$where = array( 'id'=>$this->get_id() );
		foreach( $update_data AS $key => $value )
		{
			if( ($key != 'id') && ($key != 'date_added') )
			{
				$udata[$key] = $value;
			}
		}
		return $this->update_table( self::get_tables_prefix(). "categories", $udata, $where );
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
			$stmt = $dbh->prepare( "SELECT $sel_str FROM  {$tp}categories WHERE `id` = ". $this->get_id() );
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
		return $this->delete_from_table( self::get_tables_prefix(). 'categories', $this->get_id() );
	}
	
	public function add_to_forum($forum)
	{
		if( empty($forum) )
		{
			return false;
		}
		
		return $this->insert( self::get_tables_prefix(). 'forum_categories', array('forum_id'=>$this->get_forum_id($forum), 'category_id'=>$this->get_id()) );
	}
	
	public function remove_from_forum($forum)
	{
		if( empty($forum) )
		{
			return false;
		}
		
		$db = self::get_db();
		
		$db->delete_records( self::get_tables_prefix(). 'forum_categories', array('forum_id'=>$this->get_forum_id($forum), 'category_id'=>$this->get_id()) );
	}
	
	public function belongs_to_forum($forum)
	{
		if(empty($forum))
		{
			return false;
		}
		
		return in_array( $this->get_forum_id($forum), $this->get_forums() );
	}
	
	public function get_forums()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$forum_ids = array();
		
		$db->execute_query( "SELECT `forum_id` FROM {$tp}forum_categories WHERE `category_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$forum_ids[] = $row['forum_id'];
		}
		
		return $forum_ids;
	}
	
	public function get_posts($where=array(), $order=array(), $limit = 0)
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$post_ids = array();
		
		if(empty($where))
		{
			$db->execute_query( "SELECT `post_id` FROM {$tp}category_posts WHERE `category_id` = ". $this->get_id() );
			
			while( $row = $db->get_rows() )
			{
				$post_ids[] = $row['post_id'];
			}
		}
		else
		{
			$select = "SELECT DISTINCT `post_id` FROM {$tp}category_posts, {$tp}posts WHERE `category_id` = ". $this->get_id();
			$where_data = parent::parse_where_data($where);
			$replacement_values = array();
				
			if( !empty($where_data) )
			{
				$select .= " AND ". $where_data['where_clause'];
				$replacement_values = $where_data['where_values'];
			}
			
			$select .= parent::parse_order_data($order, array('id', 'parent_id', 'title', 'description', 'creator_id', 'date_added'));
				
			if( !empty($limit) )
			{
				$select .= " LIMIT ?";
				array_push($replacement_values, $limit);
			}
				
			$dbh = parent::get_db_connection();
			$stmt = $dbh->prepare( $select );
				
			if( !empty($replacement_values) )
			{
				$stmt = parent::bind_statement_replacement_values( $stmt, parent::parse_replacement_values($replacement_values) );
			}
				
			$stmt->execute();
			$stmt->bind_result($post_id);
			while ($stmt->fetch())
			{
				$post_ids[] = $post_id;
			}
		}
		
		return $post_ids;
	}
	
	public function get_posts_count()
	{
		$db = self::get_db();
		$tp = self::get_tables_prefix();
		
		$db->execute_query( "SELECT COUNT(*) AS count FROM {$tp}category_posts WHERE `category_id` = ". $this->get_id() );
		
		return $db->get_rows()['count'];
	}
	
	private function get_id()
	{
		return $this->category_id;
	}
	
	private function set_id($id)
	{
		$this->category_id = $id;
	}
	
	private function get_forum_id($forum)
	{
		return ( is_integer($forum) || is_numeric($forum) ? $forum : get_forum_id($forum) );
	}
}
