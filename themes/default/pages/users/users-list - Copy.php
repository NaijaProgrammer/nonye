<?php
$user_ids  = UserModel::get_all_users_id();
$num_users = count($user_ids);

$pagination_page        = (int)(isset($_GET["page"]) ? $_GET["page"] : 1);
$pagination_page        = ( ($pagination_page <= 0) ? 1 : $pagination_page );
$per_page               = 1; //40;
$startpoint             = ($pagination_page * $per_page) - $per_page;
$limit                  = "{$startpoint} , {$per_page}";
$pagination_links = paginate(array(
	'query_count'                 => $num_users, 
	'per_page'                    => $per_page, 
	'current_page_number'         => $pagination_page, 
	'url'                         => '?',
	'page_detection_query_string' => 'page',
	'ellipsis_class'              => 'page-numbers dots',
	'container_class'             => 'pager fr',
	'page_item_class'             => 'page-numbers',
	'current_page_class'          => 'page-numbers current',
	/*
	* previous_page_link_label
	* next_page_link_label
	* first_page_link_label
	* last_page_link_label
	* current_page_class string the css class for the <a> holding the current page
	* 
	*/
));

$users     = UserModel::get_users(array(), array(), $limit); 
$num_users = count($users);
?>
<div class="row">
 <div class="col-md-9"><h4><strong>Users</strong></h4></div>
 <div class="col-md-3">Type to find users: <input type="text" class="form-control"></input></div>
</div>

<?php $num_rows = ceil($num_users / 4); ?>
<?php $col = 0; ?>
<?php for($i = 0; $i < $num_rows; $i++): ?>
<div class="container">
 <div class="row stylish-panel">
  <?php while($col < $num_users): ?>
  <?php $curr_user_id    = $user_ids[$col]; ?>
  <?php $curr_user       = UserModel::get_user_instance($curr_user_id); ?>
  <div class="col-md-3">
   <div>
    <a href="<?php echo sanitize_html_attribute(get_user_profile_url($curr_user_id)); ?>" style="text-decoration:none;">
	 <div class="text-centere">
	  <img src="<?php echo sanitize_html_attribute(get_user_image_url($curr_user_id)); ?>" alt="" class="im-circle img-thumbnail" style="width:80px;">
	 </div>
     <span title="display name"><?php echo $curr_user->get('username'); ?></span>&nbsp;<span title="more" class="no-display">»</span>
	</a><br/>
	<span title="posts"><?php echo count(get_user_posts($curr_user_id, array('parent_id'=>0))); ?> posts</span><br/>
	<span title="location"><?php echo $curr_user->get('location'); ?></span>
   </div>
   
  </div>
  <?php $col++; ?>
  <?php if($col % 4 == 0 ) break; ?>
  <?php endwhile; ?>
 </div>
</div>
<?php endfor; ?>

<?php echo $pagination_links; ?>