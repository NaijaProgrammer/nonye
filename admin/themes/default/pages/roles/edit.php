<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'create_roles')
	{
		$roles_array = explode(',', $roles); //role1Name:role1Description, role2Name:role2Description,
		$roles_count = count($roles_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Roles'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($roles_array[0]), 'error_message'=>'You must specify at least one role name', 'error_type'=>'noRoleSpecified')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			for($i  = 0; $i < $roles_count; $i++)
			{
				$curr_role_data  = trim($roles_array[$i]); //tag1Name:tag1Description
				$curr_role_array = explode(':', $curr_role_data);
					
				$role_name = isset($curr_role_array[0]) ? trim($curr_role_array[0]) : '';
				$role_desc = isset($curr_role_array[1]) ? trim($curr_role_array[1]) : '';
				
				create_new_role(array('name'=>$role_name, 'description'=>$role_desc));			
			}
			
			$return_data = array('success'=>true, 'message'=>'Roles created');
		}
	}
		
	elseif($action == 'update_role')
	{
		$role_data     = ItemModel::get_item_data($role_id);
		$role_category = isset($role_data['category']) ? $role_data['category'] : '';
		$validate     = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Roles'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($role_category),            'error_message'=>'Invalid Role ID', 'error_type'=>'wrongID'),
			array( 'error_condition'=>$role_category != 'available-user-roles', 'error_message'=>'Invalid Role ID', 'error_type'=>'falseCategory'),
			array( 'error_condition'=>empty($role_name), 'error_message'=>'The role name cannot be empty', 'error_type'=>'emptyRoleName'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			$role_capabilities = json_decode($role_capabilities, true);
			$remove_capabilities = json_decode($remove_capabilities, true);
			
			ItemModel::update_item($role_id, array(
				array('data_key'=>'name', 'data_value'=>$role_name, 'overwrite'=>true),
				array('data_key'=>'description', 'data_value'=>$role_description, 'overwrite'=>true)
			));
			
			foreach($role_capabilities AS $capability)
			{
				grant_capability_to_role($role_name, $capability);
			}
			
			foreach($remove_capabilities AS $capability)
			{
				revoke_role_capability($role_name, $capability);
			}
			
			$return_data = array('success'=>true, 'message'=>'Role updated successfully');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Roles'); ?>
<?php if(isset($_GET['role'])): $role_data = ItemModel::get_item_data($_GET['role']);  endif; ?>
<?php $page_title = ( !empty($role_data['category']) && ($role_data['category'] == 'available-user-roles') ) ? 'Edit '. $role_data['name']. ' role' : 'Create new roles'; ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block float-left main-content">
  <form method="post" action="" class="form-vertical">
   <?php if( !empty($role_data['category']) && ($role_data['category'] == 'available-user-roles') ): ?>
   <div class="form-group">
    <label>Role</label>
    <input id="role-name" type="text" value="<?php echo $role_data['name']; ?>" placeholder="Name" class="form-control" />
   </div>
   <div class="form-group">
    <label>Description</label>
    <input id="role-description" type="text" value="<?php echo (!empty($role_data['description']) ? $role_data['description'] : ''); ?>" placeholder="Description" class="form-control" />
   </div>
   <div class="form-group">
	<label>Specify Role Capabilities</label>
	<?php $i= 0; $avail_capabilities = get_capabilities(); ?>	
	<?php foreach($avail_capabilities AS $avail_capability): ?>
	<?php
		$cap_id   = $avail_capability['id'];
		$cap_name = $avail_capability['name'];
		$i++;
		/*
		* the first check in_array()comes from getting user capabilities from database
		* the second check isset() takes care of when the form is submitted, provide sticky functionality
		*/
		if( role_has_capability($role_data['name'], $cap_name) )
		{
			$checked_value = 'on';
		}
		else
		{
			unset($checked_value);
			$checked_value = '';
		}
		if($i % 2){ echo '<br/>'; }
	?>
	<span style="display:inline-block; width:200px; margin-right:15px;">
	 <input style="vertical-align:top; margin-right:5px;" type="checkbox" class="role-capabilities" value="<?php echo $cap_name; ?>" <?php echo set_as_checked($checked_value); ?> /><?php echo $cap_name; ?>
	</span>
	<?php endforeach; ?>
   </div>
   <input id="role-id" type="hidden" value="<?php echo $role_data['id']; ?>"/>
   <?php else: ?>
    <div class="form-group">
    <label>Role Names and Descriptions</label>
    <textarea id="roles" class="form-control resize-vertical" placeholder="Enter role names and descriptions in the form: role1Name:role1Description, role2Name:role2Description"></textarea>
   </div>
   <?php endif; ?>
   <div id="status-message" class="text-centered status-message"></div>
   <div><button id="submit-btn" class="btn btn-primary processing-bg bg-no-repeat bg-right pull-right">Save</button></div>
  </form>
 </div>
</div>
<script>
(function(){
	var btnID    = 'submit-btn';
	var msgField = 'status-message';
	Site.Event.attachListener(btnID, 'click', function(e){
		Site.Event.cancelDefaultAction(e);
		disable(btnID);
		showProcessing(btnID);
		
		<?php if( !empty($role_data['category']) && ($role_data['category'] == 'available-user-roles') ): ?>
		var roleCaps  = document.querySelectorAll('.role-capabilities');
		var capsArray = [];
		var capsToRemove = [];
		
		for(var i = 0; i < roleCaps.length; i++)
		{
			if(roleCaps[i].checked)
			{
				capsArray.push(roleCaps[i].value);
			}
			else
			{
				capsToRemove.push(roleCaps[i].value);
			}
		}
		
		var params = ''         +
		'action='               + 'update_role'                +
		'&role_id='             + $O('role-id').value          +
		'&role_name='           + $O('role-name').value        +
		'&role_description='    + $O('role-description').value + 
		'&role_capabilities='   + JSON.stringify(capsArray)    +
		'&remove_capabilities=' + JSON.stringify(capsToRemove)
		<?php else: ?>
		var params = '' +
		'action='       + 'create_roles' +
		'&roles='       + $O('roles').value;
		<?php endif; ?>
		
		console.log(params);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : params,
			'debugCallback'      : function(reply){ console.log(reply); },
			'readyStateCallback' : function(){},
			'errorCallback'      : function(xhrObject, aborted)
			{
				if(aborted)
				{
					displayStatusMessage(msgField, 'Sorry. This request timed out. Please try again', 'error');
					hideProcessing(btnID);
					enable(btnID);
				}
			},
			'successCallback' : function(reply)
			{
				var response = Site.Util.parseAjaxResponse(reply.rawValue);
				if(response.error)
				{
					displayStatusMessage(msgField, response.message, 'error');
					switch(response.errorType)
					{
						case 'unauthenticatedUserError'   : setTimeout( function(){ location.reload(); }, 5000 ); break;
						case 'insufficientPrivilegeError' : setTimeout( function(){ location.href='<?php echo ADMIN_URL; ?>'; }, 5000 ); break;
						default : '';
					}
				}
				else if(response.success)
				{
					displayStatusMessage(msgField, response.message, 'success');
					if(response.message.toLowerCase() == 'roles created')
					{
						$O('roles').value = '';
					}
				}
				
				hideProcessing(btnID);
				enable(btnID);
			}
		});
	});
})();
</script>
<?php $page_instance->load_footer( '', array() ); ?>