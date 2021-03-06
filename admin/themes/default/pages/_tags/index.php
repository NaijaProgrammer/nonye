<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}

	if($action == 'delete_tag')
	{
		$tag_data     = TagModel::get_tag_data($tag_id);
		$tag_category = isset($tag_data['category']) ? $tag_data['category'] : '';
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Tags'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>$tag_category != 'tags',         'error_message'=>'Invalid Tag ID', 'error_type'=>'falseCategory')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			$tag_name      = TagModel::get_tag_data($tag_id, 'name');
			$delete_status = ItemModel::delete_item( $tag_id, array('remove_records'=>true) );
			
			$return_data = array('success'=>true, 'message'=>'Tag '. $tag_data['name']. ' has been deleted');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Tags'); ?>
<?php $tags_len = TagModel::get_tags_count(); ?>
<?php //if(!empty($tags_len)) : ?>
<?php
$pagination_page = (int)(isset($_GET["page"]) ? $_GET["page"] : 1);
$pagination_page = ( ($pagination_page <= 0) ? 1 : $pagination_page );
$per_page        = 10;
$startpoint      = ($pagination_page * $per_page) - $per_page;
$limit           = "{$startpoint} , {$per_page}";
$pagination_links = paginate(array(
	'query_count'                 => $tags_len, 
	'per_page'                    => $per_page, 
	'current_page_number'         => $pagination_page, 
	'url'                         => '?dir=tags&',
	'page_detection_query_string' => 'page',
	'ellipsis_class'              => 'page-numbers dots',
	'container_class'             => 'pager fr',
	'page_item_class'             => 'page-numbers',
	'current_page_class'          => 'page-numbers current-pagination-page',
));

$tags     = TagModel::get_tags('', array(), $limit);
$tags_len = count($tags);
?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'View Available tags'));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading('Available Tags'); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 
 <div class="inline-block main-content">
  <?php echo $pagination_links; ?>
  <table class="table table-bordered table-hover table-responsive">
   <thead>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Name</th>
     <th class="text-centered">Description</th>
     <th class="text-centered">Posts count</th>
     <th class="text-centered">Actions</th>
    </tr>
   </thead>
   <tfoot>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Name</th>
     <th class="text-centered">Description</th>
     <th class="text-centered">Posts count</th>
     <th class="text-centered">Actions</th>
    </tr>
   </tfoot> 
   <tbody>
    <?php for($i = 0; $i < $tags_len; $i++): ?>
    <?php $serial_no = $i+1; ?>
    <?php $curr_tag = $tags[$i]; ?>
    <?php $curr_tag['posts_count'] = TagModel::get_posts_count($curr_tag['id']); ?>
	 <?php $row_class = ( ($serial_no % 2) ? 'odd-row' : 'even-row' ); ?>
	<tr class="<?php echo $row_class; ?>">
     <td class="text-centered"><?php echo $serial_no; ?></td>
     <td class="text-centered"><?php echo isset($curr_tag['name']) ? $curr_tag['name'] : ''; ?></td>
     <td class="text-centered"><?php echo isset($curr_tag['description']) ? $curr_tag['description'] : ''; ?></td>
     <td class="text-centered"><?php echo $curr_tag['posts_count']; ?></td>
     <td class="text-centered">
	  <ul class="list-inline">
	  <li>
	   <form>
	   <select name="action" class="form-control inline-block bg-no-repeat" style="width:180px; background-position:140px 8px;"
			onchange="applyAction(this, '<?php echo $curr_tag['id']; ?>', '<?php echo isset($curr_tag['name']) ? $curr_tag['name'] : ''; ?>')">
		<option value="">Select Action</option>
        <option value="edit">Edit</option>
        <option value="delete">Delete</option>
       </select>
	   </form>
	  </li>
	 </ul>
	 </td>
    </tr>
    <?php endfor; ?>
   </tbody>
  </table> 
  <?php echo $pagination_links; ?>
 </div>
</div>
<script>
function applyAction(eventTarget, tagID, tagName)
{
	var action = form.getSelectElementSelectedValue(eventTarget).toLowerCase();

	switch(action)
	{
		case "edit"      : redirectTo('edit');   break;
        case "delete"    : if( confirm('Are you sure you want to delete the ' + tagName + ' tag?') ) { deleteTag(tagID, tagName, eventTarget); }
						   else { eventTarget.form.reset(); }
						   break;
	}
	
	function redirectTo(page)
	{  
		location.href= '<?php echo ADMIN_URL; ?>/?dir=tags&page=' + page + '&tag=' + tagID;
	}
	
	function deleteTag(tagID, tagName, evtTarget)
	{
		disable(evtTarget);
		showProcessing(evtTarget);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : 'action=delete_tag&tag_id=' + tagID,
			'debugCallback'      : function(reply){ console.log(reply); },
			'readyStateCallback' : function(){},
			'errorCallback'      : function(xhrObject, aborted)
			{
				if(aborted)
				{
					alert('Sorry. This request timed out. Please try again');
					hideProcessing(evtTarget);
					enable(evtTarget);
				}
			},
			'successCallback' : function(reply)
			{
				var response = Site.Util.parseAjaxResponse(reply.rawValue);
				if(response.error)
				{
					alert(response.message);
					switch(response.errorType)
					{
						case 'unauthenticatedUserError'   : setTimeout( function(){ location.reload(); }, 5000 ); break;
						case 'insufficientPrivilegeError' : setTimeout( function(){ location.href='<?php echo ADMIN_URL; ?>'; }, 5000 ); break;
						default : '';
					}
				}
				else if(response.success)
				{
					alert(response.message);
					//location.reload();
				}
				
				hideProcessing(evtTarget);
				enable(evtTarget);
			}
		});
	}
}
</script>
<?php $page_instance->load_footer('', array()); ?>