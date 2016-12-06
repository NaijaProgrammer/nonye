<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}

	if($action == 'delete_role')
	{
		$role_data     = ItemModel::get_item_data($role_id);
		$role_category = isset($role_data['category']) ? $role_data['category'] : '';
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(),  'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Roles'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>$role_category != 'available-user-roles', 'error_message'=>'Invalid Role ID', 'error_type'=>'falseCategory')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			$delete_status = ItemModel::delete_item( $role_id, array('remove_records'=>true) );
			$return_data   = array('success'=>true, 'message'=>'Role '. $role_data['name']. ' has been deleted');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Roles'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Available roles'));
$page_instance->load_nav();
?>
<?php $roles = ItemModel::get_items(array('category'=>'available-user-roles')); ?>
<div class="container">
 <?php echo do_page_heading('Available Roles'); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block float-left main-content">
  <table class="table table-bordered table-hover table-responsive">
   <thead>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Name</th>
	 <th class="text-centered">Description</th>
	 <th class="text-centered">Capabilities</th>
     <th class="text-centered">Actions</th>
    </tr>
   </thead>
   <tfoot>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Name</th>
	 <th class="text-centered">Description</th>
	 <th class="text-centered">Capabilities</th>
     <th class="text-centered">Actions</th>
    </tr>
   </tfoot>
   <tbody>
    <?php $serial_no = 1; ?>
    <?php foreach($roles AS $role): ?>
	<?php $row_class = ( ($serial_no % 2) ? 'odd-row' : 'even-row' ); ?>
	<tr class="<?php echo $row_class; ?>">
	 <td class="text-centered"><?php echo $serial_no; ?></td>
     <td class="text-centered"><?php echo isset($role['name']) ? $role['name'] : ''; ?></td>
	 <td class="text-centered"><?php echo isset($role['description']) ? $role['description'] : ''; ?></td>
	 <td class="text-centered">
	  <?php if(isset($role['capabilities'])): ?>
	   <?php foreach($role['capabilities'] AS $curr_capability): echo $curr_capability. ', '; endforeach; ?>
	  <?php endif; ?>
	 </td>
     <td class="text-centered">
	  <ul class="list-inline">
	   <li>
	    <form>
	    <select name="action" class="form-control inline-block bg-no-repeat" style="width:180px; background-position:140px 8px;"
			onchange="applyAction(this, '<?php echo $role['id']; ?>', '<?php echo isset($role['name']) ? $role['name'] : 'null'; ?>')">
	     <option value="">Select Action</option>
         <option value="edit">Edit</option>
         <option value="delete_role">Delete</option>
        </select>
		</form>
	   </li>
	  </ul>
	 </td>
	</tr>
	<?php ++$serial_no; ?>
	<?php endforeach; ?>
   </tbody>
  </table>
 </div>
</div>
<script>
function applyAction(eventTarget, roleID, roleName)
{
	var action = form.getSelectElementSelectedValue(eventTarget).toLowerCase();

	switch(action)
	{
		case "edit"           : redirectTo('edit');   break;
        case "delete_role"    : if( confirm('Are you sure you want to delete the ' + roleName + ' role?') ) { deleteRole(roleID, roleName, eventTarget); } 
								else { eventTarget.form.reset(); }
								break;
	}
	
	function redirectTo(page)
	{  
		location.href= '<?php echo ADMIN_URL; ?>/?dir=roles&page=' + page + '&role=' + roleID;
	}
	
	function deleteRole(roleID, roleName, evtTarget)
	{
		disable(evtTarget);
		showProcessing(evtTarget);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : 'action=delete_role&role_id=' + roleID,
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