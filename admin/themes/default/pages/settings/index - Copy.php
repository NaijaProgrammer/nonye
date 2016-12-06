<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'update_site_name')
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Edit Site Settings'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			update_app_settings( array('site-name'=>$site_name) );
			$return_data = array('success'=>true, 'message'=>'Settings updated successfully');
		}
	}
	
	if($action == 'update_session_lifetime')
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Edit Site Settings'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			update_app_settings( array('session-lifetime'=>$session_lifetime) );
			$return_data = array('success'=>true, 'message'=>'Settings have been updated successfully');
		}
	}
	
	/* If we are doing a bulk update
	else
	{
		$update_arr = array();
			
		foreach($_POST AS $key => $value)
		{
			$value = is_string($value) ? trim($value) : $value;
				
			if($value != '')
			{
				$update_arr[$key] = $value;
			}
		}
			
		UserModel::update_app_settings($update_arr);
	}
	*/
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Site Settings'); ?>

<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Site Settings'));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading('Site Settings'); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 
 <div class="inline-block main-content">
  <?php echo isset($status_message) ? '<p class="text-centered">'. $status_message. '</p>' : ''; ?>
   <div class="input-group">
    <span class="input-group-addon">Site Name</span>
    <input type="text" id="site-name" data-key="site_name" value="<?php echo get_app_setting('site-name'); ?>" class="form-control">
	<span data-update-key="site-name" data-action="update_site_name" class="input-group-addon btn btn-primary bg-no-repeat bg-right bg-processing update-btn">Update</span>
   </div>
   <div class="input-group">
    <span class="input-group-addon">Session Lifetime (in seconds)</span>
    <input type="text" id="session-lifetime" data-key="session_lifetime" value="<?php echo get_app_setting('session-lifetime'); ?>" class="form-control">
	<span data-update-key="session-lifetime" data-action="update_session_lifetime" class="input-group-addon btn btn-primary bg-no-repeat bg-right bg-processing update-btn">Update</span>
   </div>
   
   <div class="input-group">
    <h4>Available themes</h4>
   </div>
 </div>
</div>
<script>
var updateBtns = document.querySelectorAll('.update-btn');

for(var i = 0, len = updateBtns.length; i < len; i++)
{
	attachUpdateListener(updateBtns[i]);
}

function attachUpdateListener(updateBtn)
{
	Site.Event.attachListener(updateBtn, 'click', function(evt){
		
		Site.Event.cancelDefaultAction(evt);
		disable(updateBtn);
		showProcessing(updateBtn);
		
		var action = updateBtn.getAttribute('data-action');
		var elemID = updateBtn.getAttribute('data-update-key');
		var key    = $O(elemID).getAttribute('data-key');
		var value  = $O(elemID).value;
		
		var postData = 'action=' + action +
		'&' + key + '=' + value;
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : postData,
			'debugCallback'      : function(reply){ console.log(reply); },
			'readyStateCallback' : function(){},
			'errorCallback'      : function(xhrObject, aborted)
			{
				if(aborted)
				{
					alert('Sorry. This request timed out. Please try again');
					hideProcessing(updateBtn);
					enable(updateBtn);
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
				}
					
				hideProcessing(updateBtn);
				enable(updateBtn);
			}
		});
	});
}
</script>
<?php $page_instance->load_footer('', array()); ?>