<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}

	if($action == 'delete_category')
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Categories'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($category_id), 'error_message'=>'No Category Specified', 'error_type'=>'emptyCategoryID'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			if( CategoryModel::get_category_instance($category_id)->delete() )
			{
				$return_data = array('success'=>true, 'message'=>'Category deleted successfully');
			}
			else
			{
				$return_data = array('error'=>true, 'message'=>'Delete operation failed', 'errorType'=>'deleteOperationFailureError');
			}
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Categories'); ?>
<?php $categories_len = count( CategoryModel::get_categories() ); ?>
<?php //if(!empty($categories_len)) : ?>
<?php
$pagination_page = (int)(isset($_GET["page"]) ? $_GET["page"] : 1);
$pagination_page = ( ($pagination_page <= 0) ? 1 : $pagination_page );
$per_page        = 10;
$startpoint      = ($pagination_page * $per_page) - $per_page;
$limit           = "{$startpoint} , {$per_page}";
$pagination_links = paginate(array(
	'query_count'                 => $categories_len, 
	'per_page'                    => $per_page, 
	'current_page_number'         => $pagination_page, 
	'url'                         => '?dir=categories&',
	'page_detection_query_string' => 'page',
	'ellipsis_class'              => 'page-numbers dots',
	'container_class'             => 'pager fr',
	'page_item_class'             => 'page-numbers',
	'current_page_class'          => 'page-numbers current-pagination-page',
));

$categories     = CategoryModel::get_categories();
$categories_len = count($categories);

?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'View Available Categories'));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading('Available Categories'); ?>
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
    <?php for($i = 0; $i < $categories_len; $i++): ?>
    <?php $serial_no = $i+1; ?>
    <?php $curr_category = CategoryModel::get_category_instance( $categories[$i] ); ?>
	 <?php $row_class = ( ($serial_no % 2) ? 'odd-row' : 'even-row' ); ?>
	<tr id="category-<?php echo $curr_category->get('id'); ?>-row" class="<?php echo $row_class; ?>">
     <td class="text-centered"><?php echo $serial_no; ?></td>
     <td class="text-centered"><?php echo $curr_category->get('name'); ?></td>
     <td class="text-centered"><?php echo $curr_category->get('description'); ?></td>
     <td class="text-centered"><?php echo $curr_category->get_posts_count(); ?></td>
     <td class="text-centered">
	  <ul class="list-inline">
	  <li>
	   <form>
	   <select name="action" class="form-control inline-block bg-no-repeat" style="width:180px; background-position:140px 8px;"
			onchange="applyAction(this, '<?php echo $curr_category->get('id'); ?>', '<?php echo urlencode($curr_category->get('name')); ?>')">
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
function applyAction(eventTarget, categoryID, categoryName)
{
	var action = form.getSelectElementSelectedValue(eventTarget).toLowerCase();

	switch(action)
	{
		case "edit"      : redirectTo('edit');   break;
        case "delete"    : if( confirm('Are you sure you want to delete the ' + categoryName + ' category?') ) { deleteCategory(categoryID, categoryName, eventTarget); }
						   else { eventTarget.form.reset(); }
						   break;
	}
	
	function redirectTo(page)
	{  
		location.href= '<?php echo ADMIN_URL; ?>/?dir=categories&page=' + page + '&category=' + categoryID;
	}
	
	function deleteCategory(categoryID, categoryName, evtTarget)
	{
		disable(evtTarget);
		showProcessing(evtTarget);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : 'action=delete_category&category_id=' + categoryID,
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
					slideUp('category-' + categoryID + '-row')
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