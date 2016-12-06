<?php
class ForumModel extends BaseModel
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
			$stmt = $dbh->prepare( "INSERT INTO {$tp}forums (`creator_id`, `name`, `description`, `date_added`) VALUES (?, ?, ?, NOW())" );
			$stmt->bind_param("iss", $creator_id, $name, $description);
			$stmt->execute();
			$insert_id = $stmt->insert_id;
			$stmt->close();
			//$dbh->close();
			return self::get_forum_instance($insert_id);
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
	
	public static function get_forum_instance($forum_id)
	{   
		return new Forum($forum_id);
	}
	
	public static function get_forums( $ids_only = true, $where=array(), $order=array(), $limit = 0 )
	{
		$tp      = self::get_tables_prefix();
		$sel_str = $ids_only ? "`id`" : "`id`, `name`, `description`, `creator_id`, `date_added`";
		$select  = "SELECT $sel_str FROM {$tp}forums";
		$forums  = array();
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
				$stmt->bind_result($forum_id);
				while ($stmt->fetch())
				{
					$forums[] = $forum_id;
				}
			}
			else
			{
				$stmt->bind_result($forum_id, $name, $description, $creator_id, $date_added);
				while ($stmt->fetch())
				{
					$forums[] = array('id'=>$forum_id, 'name'=>$name, 'description'=>$description, 'creator_id'=>$creator_id, 'date_added'=>$date_added);
				}
			}
			
			return $forums;
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

// This is supposed to be a private inner Class, but since PHP doesn't support inner classes, 
// I place the file here. It (ordinarily) shouldn't be called directly -- unless there is a compelling reason to.
// To get an instance of it, use the ForumModel::get_forum_instance() method_exists.
//
// It is a model, so it extends ForumModel (and BaseModel). But for personal preferences (and biases), 
// I don't want to have to call it using new ForumModel, it sounds better to me as new Forum.
// But if I can't do that, I'd rather prefer to get it using ForumModel::get_forum_instance()
class Forum extends ForumModel
{
	private $forum_id;
	
	public function __construct($id)
	{
		parent::__construct();
		$this->set_id($id);
	}
	
	/*
	 * name
	 * description
	 * creator_id
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
		
		return $this->update_table( self::get_tables_prefix(). "forums", $udata, $where );
		
		/*
		$tp          = self::get_tables_prefix();
		$dbh         = self::get_db_connection();
		$prepare_str = "UPDATE {$tp}forums SET ";
		$bind_arr    = array();
		$type_str    = '';
		
		foreach( $update_data AS $key => $value )
		{
			if( $key != 'id' )
			{
				$prepare_str .= "`$key` = ?"; //http://stackoverflow.com/a/14781240/1743192
			}
		}
		
		$stmt = $dbh->prepare($prepare_str);
		
		foreach($update_data AS $key => $value)
		{
			if(is_numeric($value))
			{
				$stmt->bind_param('i', $value);
			}
			else
			{
				$stmt->bind_param('s', $value);
			}
		}
		
		$stmt->execute();
		$affected_rows = $stmt->affected_rows;
		$stmt->close();
		$dbh->close();
		
		return $affected_rows;
		*/
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
			$stmt = $dbh->prepare( "SELECT $sel_str FROM  {$tp}forums WHERE `id` = ". $this->get_id() );
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
		return $this->delete_from_table( self::get_tables_prefix(). 'forums', $this->get_id() );
	}
	
	public function get_categories()
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$category_ids = array();
		
		$db->execute_query( "SELECT `category_id` FROM {$tp}forum_categories WHERE `forum_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$category_ids[] = $row['category_id'];
		}
		
		return $category_ids;
	}
	
	public function get_posts($where=array(), $order=array(), $limit = 0)
	{
		$db  = self::get_db();
		$tp  = self::get_tables_prefix();
		$post_ids = array();
		
		if(empty($where))
		{
			$db->execute_query( "SELECT `post_id` FROM {$tp}forum_posts WHERE `forum_id` = ". $this->get_id() );
			
			while( $row = $db->get_rows() )
			{
				$post_ids[] = $row['post_id'];
			}
		}
		else
		{
			$select = "SELECT DISTINCT `post_id` FROM {$tp}forum_posts, {$tp}posts WHERE `forum_id` = ". $this->get_id();
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
		
		$db->execute_query( "SELECT COUNT(*) AS count FROM {$tp}forum_posts WHERE `forum_id` = ". $this->get_id() );
		return $db->get_rows()['count'];
	}
	
	private function get_id()
	{
		return $this->forum_id;
	}
	
	private function set_id($id)
	{
		$this->forum_id = $id;
	}
}
