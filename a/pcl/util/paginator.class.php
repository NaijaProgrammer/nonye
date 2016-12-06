<?php

/**
* @author: Michael Orji
*/
class Paginator
{
	private $res_per_page           = 0;
 	private $sql_query_string       = ''; //the sql query string to be executed
 	private $sql_query_string_count = 0;
 	private $url                    = ''; 
 	private $qs                     = ''; //the query string of the url, e.g: "?m=m"
 	private $par_id                 = '';
 	private $max_visible_links      = 0;

	private $num_of_pages           = 1;
	private $current_query_resource = '';
	private $links                  = '';
	
	private $current_page_sql_query_string = '';
	
	/**
	* callback function that processes the results
	*/
	private $results_processor = '';

	public function __construct($params)
	{
		$this->res_per_page           = $params['res_per_page'];
 		$this->sql_query_string       = $params['sql_query_string'];
 		$this->sql_query_string_count = $params['sql_query_count'];
 		$this->url                    = $params['url'];
 		$this->qs                     = $params['qs'];
 		$this->par_id                 = $params['par_id'];
 		$this->max_visible_links      = $params['max_visible_links'];
		
		$this->results_processor      = $params['results_processor'];
		
		$this->init();
	}
	
	public function get_data()
	{
		$processor_function = $this->results_processor;
		return array
		(
			'processed_data'  => $processor_function( array('query_string'=>$this->get_current_sql_query_string()) ),
			'paginated_links' => $this->get_links(),
			'number_of_pages' => $this->get_number_of_pages(),
			'query_string'    => $this->get_query_string() 
		);
	}
	
	public function get_sql_query_string()
	{
		return $this->sql_query_string;
	}

	public function get_links()
	{
		return $this->links;
	}

	public function get_number_of_pages()
	{
		return $this->num_of_pages;
	}

	public function get_current_sql_query_string()
	{
		return $this->current_page_sql_query_string;
	}
	
	public function get_current_query_resource()
	{
		return $this->current_query_resource;
	}

	public function get_url($full = false)
	{
		return ( ($full) ? $this->url. $this->qs : $this->url );
	}
	
	public function get_query_string()
	{
		return $this->qs;
	}

	protected function init()
	{
		$res_per_page = $this->res_per_page;
		$this->determine_number_of_pages();

   		$sql  = $this->sql_query_string;
   		$sql .= ( ($res_per_page) ? ' LIMIT '. $this->get_start_limit(). ', '. $res_per_page : '' );

		$this->links                  = $this->paginate();
		$this->current_page_sql_query_string = $sql;
		$this->current_query_resource = mysql_query($sql);
	}

	protected function determine_number_of_pages()
	{
   		if (isset($_GET['num_pages']) && is_numeric($_GET['num_pages']) && ($_GET['num_pages'] > 0) ) 
		{ // Already been determined
   			$this->num_of_pages = $_GET['num_pages'];
   		} 

   		else 
		{
      		if ($this->sql_query_string_count > $this->res_per_page)
			{ // More than 1 page.
       			$this->num_of_pages = ceil ($this->sql_query_string_count / $this->res_per_page);
      		} 
      		else
			{
      			$this->num_of_pages = 1;
      		}
   		}
	}

	protected function paginate()
	{
		$qs               = $this->get_query_string();
		$url              = $this->get_url();
		$num_pages        = $this->get_number_of_pages();
		$start_limit      = $this->get_start_limit();
		$res_per_page     = $this->res_per_page;
		$current_page     = ($start_limit / $res_per_page) + 1;
		$num_lnx_per_page = $this->max_visible_links;
        
		$pages_par      = '<p id="'. $this->par_id. '">';
		$pages_par     .= $this->get_previous_button($current_page); 
		$numbered_pages = '';

		if ( $num_pages == 1 )
		{
			return '';
		}
      		
      		//Make all the numbered pages
      		for ($i = 1; $i <= $num_pages; $i++)
			{  
       			$linked_page   = $url. $qs.'&start_limit='. (($res_per_page * ($i - 1))). '&num_pages='. $num_pages;
				//$linked_page   = $url. '&start_limit='. (($res_per_page * ($i - 1))). '&num_pages='. $num_pages;
       			$inserted_link = $this->get_link_number_button($current_page, $i, $linked_page). ' ';
       
       			$link_array[$i] = $inserted_link;
      		} 
      
      		$numbered_pages .= $this->num_links_per_page($current_page, $num_lnx_per_page, $num_pages, $link_array);
      		$pages_par      .= $numbered_pages; //concatenate the numbered pages to the return paragraph which will be displayed to the user
       		$pages_par      .= $this->get_next_button($current_page);
      		$pages_par.= '</p>';

		return $pages_par;
	}

	protected function get_start_limit()
	{
		if(isset($_GET['start_limit']) && is_numeric($_GET['start_limit']) && ($_GET['start_limit'] > 0) )
		{ // Determine where in the database to start returning results...
    		return $_GET['start_limit'];
   		} 
   		else
		{
    		return  0;
   		}
	}

	protected function get_next_button($current_page)
	{
		if ($current_page == $this->get_number_of_pages())
		{ 
			return '<span id="next" class="pages_no_link">Next</span>';
      	}
		
		$num_pages = $this->get_number_of_pages();

		// If it's not the last page, make a Next button
      	return '<a id="next" href="'. $this->get_url(true). '&start_limit='. ($this->get_start_limit() + $this->res_per_page). '&num_pages='. $num_pages. '">Next</a>';
	}

	protected function get_previous_button($current_page)
	{
		if($current_page == 1)
		{
       		return '<span id="prev" class="pages_no_link prev_no_link">Prev</span>';
      	}
		
		$num_pages = $this->get_number_of_pages();

		// If it's not the first page, make a 'Previous' link
       	return '<a id="prev" href="'. $this->get_url(true). '&start_limit='. ($this->get_start_limit() - $this->res_per_page). '&num_pages='. $num_pages. '">Prev</a>';
	}

	protected function get_link_number_button($current_page_num, $link_num, $linked_page)
	{
   		if($link_num != $current_page_num)
		{
    		return '<a href="'. $linked_page. '">'. $link_num. '</a>';
   		}
  		else
		{
    		return '<span class="pages_no_link">'. $link_num. '</span>';
   		}
	}

	
	protected function num_links_per_page($current_page, $num_links_to_show, $total_num_pages, $link_array)
	{
   		if($num_links_to_show >= $total_num_pages)
		{
			$num_links_to_show = $total_num_pages;
		}

   		if($current_page == 1)
		{
    		$str = $link_array[$current_page];

      		for($i = $current_page + 1; $i <= $num_links_to_show; $i++)
			{
       			$str .= $link_array[$i];
      		}
   		}
   		else if($current_page == $total_num_pages)
		{ 
    		$start_indx = ($total_num_pages - $num_links_to_show) + 1;
    		$str        = $link_array[$start_indx];

      		for($i = $start_indx+1 ;  $i <= $total_num_pages; $i++)
			{
       			$str .= $link_array[$i];
      		}
   		}
   		else
		{
    		$lower_str             = '';
    		$upper_str             = '';
    		$lower_str_upper_limit = $current_page - 1;
    		$upper_str_start_indx  = $current_page + 1;
    		$num_pages_before = $lower_str_upper_limit;
    		$num_pages_after  = $total_num_pages - $current_page;

      		if($current_page < $num_links_to_show)
			{
       			$lower_str_start_indx  = ( ($lower_str_upper_limit == 1) ? 1 : $lower_str_upper_limit - 1); 
       			$disp_pages_before     = $current_page - $lower_str_start_indx;  
       			$disp_pages_after      = $num_links_to_show - $disp_pages_before - 1;
       			$loop_end_limit        = $current_page + $disp_pages_after;
      		}
      		else if($current_page == $num_links_to_show)
			{
       			$lower_str_start_indx = ceil($num_pages_before / 2) + 1;
       			$loop_end_limit       = ($num_links_to_show + $lower_str_start_indx) - 1;
      		}
      		else if($current_page > $num_links_to_show)
			{
         		if($this->is_even($num_links_to_show))
				{
          			$disp_pages_before    = ceil($num_links_to_show / 2) - 1;
          			$disp_pages_after     = $disp_pages_before + 1;
         		}
         		else
				{
          			$disp_pages_before = $disp_pages_after = ($num_links_to_show - 1) / 2; 
         		}

         		$loop_end_limit = $current_page + $disp_pages_after;

         		if($loop_end_limit > $total_num_pages)
				{
          			$loop_end_limit    = $total_num_pages;
          			$disp_pages_after  = $total_num_pages - $current_page;
          			$disp_pages_before = $num_links_to_show - $disp_pages_after - 1;  
         		}

        		$lower_str_start_indx = $current_page - $disp_pages_before;
      		} 
      
      		for($i = $lower_str_start_indx; $i <= $lower_str_upper_limit; $i++ )
			{
       			$lower_str .= $link_array[$i];
      		}

      		for($j = $upper_str_start_indx; $j <= $loop_end_limit; $j++)
			{
       			$upper_str .= $link_array[$upper_str_start_indx++];
     		}

    		$str = $lower_str. $link_array[$current_page]. $upper_str;
   		}

 		return $str; 
	}

	private function is_even($num)
	{
 		return ($num % 2 == 0);
	}
}

/**** Pagination processor example ****/
function paginator_processor( $supplied_opts = array() )
	{
		$default_opts = array('query_string'=>'', 'query_data'=>'');
		$opts = ArrayManipulator::copy_array($default_opts, $supplied_opts);
		extract($opts);
		
		$db_obj = get_db_object();
		$db_obj->execute_query($query_string);
		$ids = $db_obj->return_result_as_matrix();
		$ids = ArrayManipulator::reduce_redundant_matrix_to_array($ids, 'item_id');
			
		for($i = 0; $i < count($ids); $i++)
		{
				$current_id = $ids[$i];
				$name_arr   = ItemModel::get_item_data($current_id, 'name');
				
				$matrix[$i]['id']   = $current_id;
				$matrix[$i]['name'] = $name_arr['name'];
				$matrix[$i] = ItemModel::get_item_data($current_id);
		}
			
		$matrix['num_of_rows'] = count($matrix);
		$matrix['sql_query_string'] = $query_string;
			
		return $matrix;
	}

/*** Example Usage */
$user_activities = UserModel::get_user_activities( UserModel::get_current_user_id(), array('orders'=>array(), 'limit'=>0) );
			$top_pagination = new Paginator
			(
				array
				(
					'res_per_page'       => 10,
					'sql_query_string'   => $user_activities['sql_query_string'],
					'sql_query_count'    => $user_activities['num_of_rows'],
					'url'                => SITE_URL. '/user-history/',
					'qs'                 => '?m=m',
					'par_id'             => 'top-pagination',
					'max_visible_links'  => 10,
					'results_processor'  => 'paginator_processor'
				)
			);
			
			$pagination_data = $top_pagination->get_data();
			
			$user_activities = $pagination_data['processed_data'];
			$paginated_links = $pagination_data['paginated_links'];
			$number_of_pages = $pagination_data['number_of_pages'];
			$query_string    = $pagination_data['query_string'];
			
			echo $paginated_links; //display the links
			
/** loop through $user_activities, and do what you wish to do with it */
foreach($user_activities AS $user_activity)
{
	//do something useful here
}



/**** NEW (and better) PAGINATION **/
//@credits: http://www.otallu.com/tutorials/simple-php-mysql-pagination/
//also check out : http://www.freezecoders.com/
//use above sites as examples of tutorials to create
function pagination($query,$per_page=10,$page=1,$url='?'){   
    global $conDB; 
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
    $row = mysqli_fetch_array(mysqli_query($conDB,$query));
    $total = $row['num'];
    $adjacents = "2"; 
      
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
    $lastlabel = "Last &rsaquo;&rsaquo;";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1; // //last page minus 1
      
    $pagination = "";
    if($lastpage > 1){   
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
              
            if ($page > 1) $pagination.= "<li><a href='{$url}page={$prev}'>{$prevlabel}</a></li>";
              
        if ($lastpage < 7 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                else
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
            }
          
        } elseif($lastpage > 5 + ($adjacents * 2)){
              
            if($page < 1 + ($adjacents * 2)) {
                  
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'>...</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";  
                      
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";      
                  
            } else {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
            }
        }
          
            if ($page < $counter - 1) {
                $pagination.= "<li><a href='{$url}page={$next}'>{$nextlabel}</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
            }
          
        $pagination.= "</ul>";        
    }
      
    return $pagination;
}

/** USAGE **/
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;
 
$statement = "`records` ORDER BY `id` ASC"; // Change `records` according to your table name.
  
$results = mysqli_query($conDB,"SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
 
if (mysqli_num_rows($results) != 0) {
     
    // displaying records.
    while ($row = mysqli_fetch_array($results)) {
        echo $row['name'] . '<br>';
    }
  
} else {
     echo "No records are found.";
}
 
 // displaying paginaiton.
echo pagination($statement,$per_page,$page,$url='?');
