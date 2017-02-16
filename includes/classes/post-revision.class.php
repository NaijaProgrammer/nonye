<?php

class PostRevision
{
	private $id;
	private $post_id;
	
	public function __construct($post_id)
	{
		$this->set_post_id($post_id);
		if( $id = $this->create() ) {
			$this->set_id($id);
		}
	}
	
	/*
	* meta data e.g featured-image-url and other values not in the courses table
	* meta data should be added as array in format: meta_key =>array('value'=>your_value, 'overwrite'=>boolean
	* e.g update( array(
	* 'featured-image' => array('value'=>'theUrl', 'overwrite'=>true)
	* ) )
	* The meta data 'overwrite' parameter is optional, defaults to false
	*/
	public function update($update_data)
	{
		$meta_data = array();
		$where = array( 'id'=>$this->get_id() );
		
		foreach( $update_data AS $key => $value ) {
			if( ($key != 'id') && ($key != 'post_id') && ($key != 'date') ) {

				if( is_array($value) ){
					$meta_data[$key] = $value;
				}
			}
		}
		
		if( !empty($meta_data) ){
			$this->update_meta_data($meta_data); 
		}
	}
	
	public function get($data='')
	{
		$tp  = BaseModel::get_tables_prefix();
		$dbh = BaseModel::get_db_connection();
		
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
		
		try {
			$stmt = $dbh->prepare( "SELECT $sel_str FROM  {$tp}post_revisions WHERE `id` = ". $this->get_id() );
			
			$stmt->execute();
			
			if( empty($data) || is_array($data) ) {
				$result = $stmt->get_result();
				return $result->fetch_assoc();
			}
			else if(is_string($data)) {
				$stmt->store_result(); //solves the problem of Allowed memory size of 134217728 bytes exhausted. credits http://stackoverflow.com/questions/5052870/mysqli-bind-result-allocates-too-much-memory
				$stmt->bind_result($$data);
				$stmt->fetch();
				return $$data;
			}
		}
		catch(Exception $e) {
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return '';
		}
	}
	
	public function get_meta($meta_key, $order = array(), $limit = '')
	{
		$tp       = BaseModel::get_tables_prefix();
		$meta_key = trim($meta_key);
		$limit    = trim($limit);
		$post_revision_id  = $this->get_id();
		$values   = array();
		
		try {
			$dbh  = BaseModel::get_db_connection();
			$str  = "SELECT `meta_value` FROM {$tp}post_revision_meta WHERE `post_revision_id` = $post_revision_id AND `meta_key` = ?";
			$str .= parent::parse_order_data($order, array('post_revision_id', 'meta_value', 'date'));
			$str .= !empty($limit) ? " LIMIT $limit" : "";
			$stmt = $dbh->prepare( $str );
			
			$stmt->bind_param("s", $meta_key);
			
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
			return ( $num_rows == 1 || $limit == '1' ? $values[0] : $values );
		}
		catch(Exception $e) {
			if( is_development_server() ) {
				die( $e->getMessage() );
			}
			return null;
		}
	}
	
	private function insert_meta_data($meta_key, $meta_value)
	{
		$tp  = BaseModel::get_tables_prefix();
		$post_revision_id = $this->get_id();
		
		try {
			$dbh  = BaseModel::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}post_revision_meta (`post_revision_id`, `meta_key`, `meta_value`, `date`) VALUES (?, ?, ?, UTC_TIMESTAMP())" );
			$stmt->bind_param("iss", $post_revision_id, $meta_key, $meta_value);
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
		$tp      = BaseModel::get_tables_prefix();
		$post_revision_id = $this->get_id();
		
		foreach($data_array AS $meta_key => $val_array){
			/*
			if( $this->meta_exists($meta_key) && !empty($val_array['overwrite']) ){
				$where = array('post_revision_id'=>$post_revision_id, 'meta_key'=>$meta_key);
				$this->update_table( BaseModel::get_tables_prefix(). "post_revisiono_meta", array('meta_value'=>$val_array['value']), $where);
			}
			else{*/
				$this->insert_meta_data($meta_key, $val_array['value']);
			//}
		}
	}
	
	private function meta_exists($meta_key)
	{
		$tp      = BaseModel::get_tables_prefix();
		$post_revision_id = $this->get_id();
		$ids     = array();
		
		try {
			$dbh  = BaseModel::get_db_connection();
			$stmt = $dbh->prepare( "SELECT `id` FROM {$tp}post_revision_meta WHERE `post_revision_id` = $post_revision_id AND `meta_key` = ? LIMIT 1" );
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
	
	private function create()
	{
		$tp = BaseModel::get_tables_prefix();
		$post_id = $this->get_post_id();
		
		//It's not a parameter of the query, in that you don't have to supply a value to MySQL.
		//http://stackoverflow.com/a/9497221/1743192
		try {
			$dbh = BaseModel::get_db_connection();
			$stmt = $dbh->prepare( "INSERT INTO {$tp}post_revisions (`post_id`, `date`) VALUES (?, UTC_TIMESTAMP())" );
			$stmt->bind_param("i", $post_id);
			$stmt->execute();
			
			if($stmt->affected_rows){
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
	private function get_post_id()
	{
		return $this->post_id;
	}
	private function set_post_id($post_id)
	{
		$this->post_id = $post_id;
	}
	private function get_id()
	{
		return $this->id;
	}
	private function set_id($id)
	{
		$this->id = $id;
	}
}
