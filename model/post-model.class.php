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
	* tags [optional for a reply to a post]
	*/
	public static function create($data)
	{
		if( !isset($data['parent_id']) ) {
			$data['parent_id'] = 0;
		}
		
		if( !isset($data['excerpt']) ) {
			$data['excerpt'] = '';
		}
		
		extract($data);
		$tp = self::get_tables_prefix();
		
		//It's not a parameter of the query, in that you don't have to supply a value to MySQL.
		//http://stackoverflow.com/a/9497221/1743192
		try
		{
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}posts (`parent_id`, `author_id`, `title`, `excerpt`, `content`, `status`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())" );
			$stmt->bind_param("iissss", $parent_id, $author_id, $title, $excerpt, $content, $status);
			$stmt->execute();
			
			if($stmt->affected_rows)
			{
				$post = self::get_post_instance($stmt->insert_id);
				
				if( isset($forum_id) ) {
					$post->add_to_forum($forum_id);
				}
				
				if( isset($category_id) ) {
					$post->add_to_category($category_id);
				}
				
				if( isset($tags) && is_array($tags) ) {
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
			if( is_development_server() ) {
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
		$sel_str = $ids_only ? "`id`" : "`id`, `parent_id`, `title`, `excerpt`, `content`, `status`, `creator_id`, `date_created`";
		$select  = "SELECT $sel_str FROM {$tp}posts";
		$posts   = array();
		$where_data = parent::parse_where_data($where);
		$replacement_values = array();
		
		if( !empty($where_data) ) {
			$select .= " WHERE ". $where_data['where_clause'];
			$replacement_values = $where_data['where_values'];
		}
		
		$select .= parent::parse_order_data($order, array('id', 'parent_id', 'title', 'excerpt', 'content', 'status', 'creator_id', 'date_created'));
		
		if( !empty($limit) ) {
			$select .= " LIMIT $limit";
			//array_push($replacement_values, $limit);
		}
		
		try {
			$dbh = self::get_db_connection();
			$stmt = $dbh->prepare( $select );
			
			if( !empty($replacement_values) ) {
				$stmt = parent::bind_statement_replacement_values( $stmt, parent::parse_replacement_values($replacement_values) );
			}
			
			$stmt->execute();
			
			if($ids_only) {
				$stmt->bind_result($post_id);
				while ($stmt->fetch()) {
					$posts[] = $post_id;
				}
			}
			else {
				$stmt->bind_result($post_id, $parent_id, $name, $excerpt, $description, $status, $creator_id, $date_created);
				while ($stmt->fetch()) {
					$posts[] = array('id'=>$post_id, 'parent_id'=>$parent_id, 'name'=>$name, 'excerpt'=>$excerpt, 
						'description'=>$description, 'status'=>$status, 'creator_id'=>$creator_id, 'date_created'=>$date_created);
				}
			}
			
			return $posts;
		}
		catch(Exception $e){
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return array();
		}
	}
	
	public static function get_reply_posts($where=array(), $order=array(), $limit = 0 )
	{
		$tp      = self::get_tables_prefix();
		$select  = "SELECT `id` FROM {$tp}posts WHERE `parent_id` > 0";
		$posts   = array();
		$where_data = parent::parse_where_data($where);
		$replacement_values = array();
		
		if( !empty($where_data) )
		{
			$select .= " AND ". $where_data['where_clause'];
			$replacement_values = $where_data['where_values'];
		}
		
		$select .= parent:: parse_order_data($order, array('id', 'parent_id', 'content', 'creator_id', 'date_created'));
		
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
			$stmt->bind_result($post_id);
			
			while ($stmt->fetch())
			{
				$posts[] = $post_id;
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

	/*
	* $keywords string
	* $authors array of author ids
	* $filter_data members: array( forum=>array(id, id, id...), category=>array(id, id, id...), tag=>array(id, id, id...) )
	* $orders members : array('title'=>'DESC', 'date_created'=>'ASC')
	*/
	public static function search($keywords, $authors=array(), $filter_data=array(), $orders=array(), $limit = 0 )
	{
		$posts = array();
		$select_string = self::assemble_post_search_str($keywords, $authors, $filter_data, $orders, $limit);
		//echo $select_string; exit;
		try
		{
			$dbh  = self::get_db_connection();
			$stmt = $dbh->prepare( $select_string );
			
			$stmt->execute();
			$stmt->bind_result($post_id);
			
			while ($stmt->fetch())
			{
				$posts[] = $post_id;
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
	
	/*
	* $keywords string
	* $authors array of author ids
	* $filter_data members: array( forum=>array(id, id, id...), category=>array(id, id, id...), tag=>array(id, id, id...) )
	* $orders members : array('title'=>'DESC', 'date_created'=>'ASC')
	* $limit string e.g 10, (15, 5)
	*/
	private static function assemble_post_search_str($keywords, $authors=array(), $filter_data=array(), $orders=array(), $limit=0)
	{ 
		$tp               = self::get_tables_prefix();
		$sql_str          = "SELECT DISTINCT `id` FROM {$tp}posts ";
		$tables_whitelist = array('forum', 'category', 'tag');
		$orders_whitelist = array('title', 'date_created');
		$order_vals_wlist = array('ASC', 'DESC');
		
		$filter_str = '';
		if(!empty($filter_data))
		{
			foreach($filter_data AS $key => $ids)
			{
				if(in_array($key, $tables_whitelist))
				{
					$filter_str .= "INNER JOIN {$tp}{$key}_posts ON ";
					
					for($i=0, $len=count($ids); $i < $len; $i++)
					{
						$curr_id = $ids[$i];
						if($i == 0)
						{
							$filter_str .= "{$tp}posts.`id` = {$tp}{$key}_posts.`post_id` AND ( ({$tp}{$key}_posts.`{$key}_id` = $curr_id) "; 
						}
						else
						{
							$filter_str .= "OR ({$tp}{$key}_posts.`{$key}_id` = $curr_id) ";
						}
					}
					
					$filter_str .= ") ";
				}
			}
		}
		
		$sql_str .= $filter_str;
		$sql_str .= "WHERE {$tp}posts.`title` LIKE '%$keywords%' AND  {$tp}posts.`parent_id` = 0 ";
		//$sql_str .= "WHERE ({$tp}posts.`title` LIKE '%$keywords%' OR {$tp}posts.`content` LIKE '%$keywords%') AND  {$tp}posts.`parent_id` = 0";
		
		$authors_str = '';
		if(!empty($authors))
		{
			$authors_str .= "AND (";
			
			for($i = 0, $len = count($authors); $i < $len; $i++)
			{
				$author_id = $authors[$i];
				$authors_str  .= ($i == 0) ? "`author_id` = $author_id " : "OR `author_id` = $author_id ";
			}
			
			$authors_str .= ") ";
		}
		
		$sql_str .= $authors_str;
		
		
		$orders_str = '';
		if(!empty($orders))
		{
			$orders_str .= "ORDER BY ";
			
			foreach($orders AS $key => $value)
			{
				$key   = strtolower($key);
				$value = strtoupper($value);
				
				if( in_array($key, $orders_whitelist) && in_array($value, $order_vals_wlist))
				{
					$orders_str .= "$key $value, ";
				}
			}
			
			$orders_str = rtrim( trim($orders_str, ',') );
		}
		
		$sql_str .= $orders_str. " ";
		
		if(!empty($limit))
		{
			$sql_str .= "LIMIT $limit";
		}
		
		return $sql_str;
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
	* meta data e.g featured-image-url and other values not in the courses table
	* meta data should be added as array in format: meta_key =>array('value'=>your_value, 'overwrite'=>boolean
	* e.g update( array(
	* 'title' => 'course title',
	* 'description' => 'course description',
	* 'featured-image' => array('value'=>'theUrl', 'overwrite'=>true)
	* ) )
	* The meta data 'overwrite' parameter is optional, defaults to false
	*/
	public function update($update_data)
	{
		$udata = array();
		$meta_data = array();
		$where = array( 'id'=>$this->get_id() );
		
		foreach( $update_data AS $key => $value ) {
			if( ($key != 'id') && ($key != 'parent_id') && ($key != 'date_created') ) {

				if( is_array($value) ){
					$meta_data[$key] = $value;
				}
				else{
					$udata[$key] = $value;
				}
			}
		}
		
		if( !empty($meta_data) ){
			$this->update_meta_data($meta_data); 
		}
		if( !empty($udata) ){
			$this->update_table( self::get_tables_prefix(). "posts", $udata, $where );
		}
		
		/*
		foreach( $update_data AS $key => $value )
		{
			if( ($key != 'id') && ($key != 'parent_id') && ($key != 'date_created') ) {
				$udata[$key] = $value;
			}
		}
		
		return $this->update_table( self::get_tables_prefix(). "posts", $udata, $where );
		*/
	}
	
	public function publish()
	{
		$this->update( array('status' => 'published') );
		$this->set_publish_date();
	}
	
	/*
	* $date in format: 'YYYY-mm-dd HH:mm:ss'
	*/
	public function set_publish_date( $date = '' )
	{
		$table = self::get_tables_prefix(). "posts";
		$dbh   = self::get_db_connection();
		
		$update_str  = "UPDATE {$table} SET `date_published` =  ";
		$update_str .= !empty($date) ? $date : "UTC_TIMESTAMP()";
		$update_str .= "WHERE `id` = ". $this->get_id();
		
		$stmt = $dbh->prepare( $update_str );
		
		$stmt->execute();
		$affected_rows = $stmt->affected_rows;
		$stmt->close();
		
		// Since every class shares the same instance,
		// when we close the connection, we get a warning of:
		// mysqli::query(): Couldn't fetch mysqli in C:\wamp\www\sites\zamaju-forums\a\pcl\sql\my_sql.class.php on line ...
		// and as a result, the UserManagerSessionManager is not able to successfully delete expired sessions
		//$dbh->close();
		
		return $affected_rows;
	}
	
	public function record_view($viewer_id)
	{
		$tp  = self::get_tables_prefix();
		$pid = $this->get_id();
		
		try
		{
			$dbh  = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}post_views (`post_id`, `viewer_id`, `date_viewed`) VALUES ($pid, $viewer_id, UTC_TIMESTAMP())" );
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
				$stmt->store_result(); //solves the problem of Allowed memory size of 134217728 bytes exhausted. credits http://stackoverflow.com/questions/5052870/mysqli-bind-result-allocates-too-much-memory
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
	
	public function get_meta($meta_key, $order = array(), $limit = '')
	{
		$tp       = self::get_tables_prefix();
		$meta_key = trim($meta_key);
		$limit    = trim($limit);
		$post_id  = $this->get_id();
		$values   = array();
		//$unique_metas = array('price');
		
		try {
			$dbh  = self::get_db_connection();
			$str  = "SELECT `meta_value` FROM {$tp}post_meta WHERE `post_id` = $post_id AND `meta_key` = ?";
			$str .= parent::parse_order_data($order, array('post_id', 'meta_value', 'date'));
			$str .= !empty($limit) ? " LIMIT ?" : "";
			$stmt = $dbh->prepare( $str );
			
			if( !empty($limit) ) {
				$stmt->bind_param("ss", $meta_key, $limit);
			}
			else {
				$stmt->bind_param("s", $meta_key);
			}
			
			$stmt->execute();
			
			//without this, $stmt->num_rows returns 0, even if there is some result set returned
			$stmt->store_result();
			
			$num_rows = $stmt->num_rows;
			
			//without this, we get: Warning: Course::get_meta(): Couldn't fetch mysqli_stmt in FILE_PATH 
			if($num_rows < 1){
				return '';
			}
			
			$stmt->bind_result($meta_value);
			
			while ($stmt->fetch()) {
				$values[] = $meta_value;
			}
			
			$stmt->close();
			//return ( in_array( $meta_key, $unique_metas ) || $limit == '1' ? $values[0] : $values );
			return ( $num_rows == 1 || $limit == '1' ? $values[0] : $values );
		}
		catch(Exception $e) {
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return null;
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
		$category_ids = array();
		
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
		$tag_ids = array();
		
		$db->execute_query( "SELECT `tag_id` FROM {$tp}tag_posts WHERE `post_id` = ". $this->get_id() );
		
		while( $row = $db->get_rows() )
		{
			$tag_ids[] = $row['tag_id'];
		}
		
		return $tag_ids;
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
	
	public function get_participants()
	{
		$replies_ids = parent::get_reply_posts( array('parent_id'=>$this->get_id()), $order=array(), $limit = 0 );
		$author_ids  = array();
		
		foreach($replies_ids AS $reply_id)
		{
			$reply_post = parent::get_post_instance($reply_id);
			$author_ids[] = $reply_post->get('author_id');
		}
		
		if( !in_array($this->get('author_id'), $author_ids) )
		{
			$author_ids[] = $this->get('author_id');
		}
		
		return array_unique( $author_ids );
	}
	
	private function insert_meta_data($meta_key, $meta_value)
	{
		$tp  = self::get_tables_prefix();
		$post_id = $this->get_id();
		
		try {
			$dbh  = self::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}post_meta (`post_id`, `meta_key`, `meta_value`, `date`) VALUES (?, ?, ?, UTC_TIMESTAMP())" );
			$stmt->bind_param("iss", $post_id, $meta_key, $meta_value);
			$stmt->execute();
			
			if($stmt->affected_rows) {
				return $stmt->insert_id;
			}
			
			$stmt->close();
		}
		catch(Exception $e) {
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return null;
		}
	}
	
	/*
	* $data_array associative array of associative array(s) 
	* format : array(
	* 	key => array('value'=>value, 'overwrite'=>boolean),
	*	key => array('value'=>value, 'overwrite'=>boolean),
	*	...
	* )
	* e.g array(
	*	'featured-image-url' => array('value'=>'http://url/images/src', 'overwrite'=>true)
	*)
	*/
	private function update_meta_data($data_array)
	{
		$tp      = self::get_tables_prefix();
		$post_id = $this->get_id();
		
		foreach($data_array AS $meta_key => $val_array){
			if( $this->meta_exists($meta_key) && !empty($val_array['overwrite']) ){
				$where = array('post_id'=>$post_id, 'meta_key'=>$meta_key);
				$this->update_table( self::get_tables_prefix(). "post_meta", array('meta_value'=>$val_array['value']), $where);
			}
			else{
				$this->insert_meta_data($meta_key, $val_array['value']);
			}
		}
	}
	
	private function meta_exists($meta_key)
	{
		$tp      = self::get_tables_prefix();
		$post_id = $this->get_id();
		$ids     = array();
		
		try {
			$dbh  = self::get_db_connection();
			$stmt = $dbh->prepare( "SELECT `id` FROM {$tp}post_meta WHERE `post_id` = $post_id AND `meta_key` = ? LIMIT 1" );
			$stmt->bind_param("s", $meta_key);
			$stmt->execute();
			
			//without this, $stmt->num_rows returns 0, even if there is some result set returned
			$stmt->store_result();
			
			$num_rows = $stmt->num_rows;
			
			//without this, we get: Warning: Course::get_meta(): Couldn't fetch mysqli_stmt in FILE_PATH 
			if($num_rows < 1){
				return false;
			}
			
			$stmt->bind_result($meta_id);
			
			while ($stmt->fetch()) {
				$ids[] = $meta_id;
			}
			
			$stmt->close();
			return !empty($ids);
		}
		catch(Exception $e) {
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return null;
		}
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
