<?php
$user_ids  = UserModel::get_all_users_id();
$num_users = count($user_ids);

$pagination_page        = (int)(isset($_GET["page"]) ? $_GET["page"] : 1);
$pagination_page        = ( ($pagination_page <= 0) ? 1 : $pagination_page );
$per_page               = 40;
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
 <div class="col-md-8"><h4><strong>Users</strong></h4></div>
 <div class="col-md-4"><input type="text" class="form-control" title="Type to find users" placeholder="Type to find users: "></input></div>
</div>
<hr/>
<?php $num_rows = ceil($num_users / 4); ?>
<?php $col = 0; ?>
<?php for($i = 0; $i < $num_rows; $i++): ?>
<div class="container">
 <div class="row stylish-panel">
  <?php while($col < $num_users): ?>
  <?php $curr_user_id    = $user_ids[$col]; ?>
  <?php $curr_user       = UserModel::get_user_instance($curr_user_id); ?>
  <div class="col-md-3" style="padding:0;">
   <div>
    <a href="<?php echo sanitize_html_attribute(get_user_profile_url($curr_user_id)); ?>" style="text-decoration:none;">
	 <img class="im-circle img-thumbnail float-left" src="<?php echo sanitize_html_attribute(get_user_image_url($curr_user_id)); ?>" alt="" style="width:80px; margin-right:8px;">
	</a>
    <a href="<?php echo sanitize_html_attribute(get_user_profile_url($curr_user_id)); ?>" style="text-decoration:none;">
	 <span title="Display name"><span class="fa fa-icon fa-user"></span>&nbsp;<?php echo $curr_user->get('username'); ?></span>
	</a><br/>
	<?php if(!empty($curr_user->get('location'))): ?><span title="Location"><span class="fa fa-icon fa-globe"></span>&nbsp;<?php echo $curr_user->get('location'); ?></span><br/><?php endif; ?>
	<span title="Signup date"><span class="fa fa-icon fa-history"></span>&nbsp;<?php echo format_date($curr_user->get('date_registered'), 'F d, Y'); ?></span></span><br/>
	<span title="Last seen">
	<span class="fa fa-icon fa-clock-o"></span>&nbsp;<?php echo get_time_elapsed_intelligent(format_date(format_time($curr_user->get('last-seen-time')))); ?></span>
	</span><br/>
	<?php if(empty($curr_user->get('location'))): ?><span></span><br/><?php endif; ?>
	
   </div>
   
  </div>
  <?php $col++; ?>
  <?php if($col % 4 == 0 ) break; ?>
  <?php endwhile; ?>
 </div>
</div>
<?php endfor; ?>

<?php echo $pagination_links; ?>