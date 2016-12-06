<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'create_capabilities')
	{
		$capabilities_array = explode(',', $capabilities); //cap1Name:cap1Description, cap2Name:cap2Description,
		$capabilities_count = count($capabilities_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Capabilities'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($capabilities_array[0]), 'error_message'=>'You must specify at least one capability name', 'error_type'=>'noCapabilitySpecified')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			for($i  = 0; $i < $capabilities_count; $i++)
			{
				$curr_capability_data  = trim($capabilities_array[$i]); //cap1Name:cap1Description
				$curr_capability_array = explode(':', $curr_capability_data);
					
				$capability_name = isset($curr_capability_array[0]) ? trim($curr_capability_array[0]) : '';
				$capability_desc = isset($curr_capability_array[1]) ? trim($curr_capability_array[1]) : '';
				
				create_new_capability(array('name'=>$capability_name, 'description'=>$capability_desc));			
			}
			
			$return_data = array('success'=>true, 'message'=>'Capabilities created');
		}
	}
		
	elseif($action == 'update_capability')
	{
		$capability_data     = ItemModel::get_item_data($capability_id);
		$capability_category = isset($capability_data['category']) ? $capability_data['category'] : '';
		$validate     = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Capabilities'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($capability_category),            'error_message'=>'Invalid Capability ID', 'error_type'=>'wrongID'),
			array( 'error_condition'=>$capability_category != 'available-user-capabilities', 'error_message'=>'Invalid Capability ID', 'error_type'=>'falseCategory'),
			array( 'error_condition'=>empty($capability_name), 'error_message'=>'The capability name cannot be empty', 'error_type'=>'emptyCapabilityName'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			ItemModel::update_item($capability_id, array(
				array('data_key'=>'name', 'data_value'=>$capability_name, 'overwrite'=>true),
				array('data_key'=>'description', 'data_value'=>$capability_description, 'overwrite'=>true)
			));
			
			$return_data = array('success'=>true, 'message'=>'Capability updated successfully');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Capabilities'); ?>
<?php if(isset($_GET['capability'])): $capability_data = ItemModel::get_item_data($_GET['capability']);  endif; ?>
<?php $page_title = ( !empty($capability_data['category']) && ($capability_data['category'] == 'available-user-capabilities') ) ? 'Edit '. $capability_data['name']. ' capability' : 'Create new capabilities'; ?>
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
   <?php if( !empty($capability_data['category']) && ($capability_data['category'] == 'available-user-capabilities') ): ?>
   <div class="form-group">
    <label>Capability</label>
    <input id="capability-name" type="text" value="<?php echo $capability_data['name']; ?>" placeholder="Name" class="form-control" />
   </div>
   <div class="form-group">
    <label>Description</label>
    <input id="capability-description" type="text" value="<?php echo (!empty($capability_data['description']) ? $capability_data['description'] : ''); ?>" placeholder="Description" class="form-control" />
   </div>
   <input id="capability-id" type="hidden" value="<?php echo $capability_data['id']; ?>"/>
   <?php else: ?>
    <div class="form-group">
    <label>Capability Names and Descriptions</label>
    <textarea id="capabilities" class="form-control resize-vertical" placeholder="Enter capability names and descriptions in the form: cap1Name:cap1Description, cap2Name:cap2Description"></textarea>
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
		
		<?php if( !empty($capability_data['category']) && ($capability_data['category'] == 'available-user-capabilities') ): ?>
		var params = ''            +
		'action='                  + 'update_capability'                +
		'&capability_id='          + $O('capability-id').value          +
		'&capability_name='        + $O('capability-name').value        +
		'&capability_description=' + $O('capability-description').value
		<?php else: ?>
		var params = ''  +
		'action='        + 'create_capabilities' +
		'&capabilities=' + $O('capabilities').value;
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
					if(response.message.toLowerCase() == 'capabilities created')
					{
						$O('capabilities').value = '';
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