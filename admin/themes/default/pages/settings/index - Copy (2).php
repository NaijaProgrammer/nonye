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
	
	if($action == 'update_auto_mailer')
	{
		switch(strtolower($update_type))
		{
			case 'registration-mail' : 
				update_app_settings(array('registration-success-mail-sender'=>$mail_sender, 'registration-success-message'=>htmlspecialchars($mail_message))); 
				break;
				
			case 'password-recovery-mail' : 
				update_app_settings(array('password-recovery-mail-sender'=>$mail_sender, 'password-recovery-mail'=>htmlspecialchars($mail_message)));
				break;
				
			case 'user-privilege-change-mail' : 
				update_app_settings(array('user-privilege-change-mail-sender'=>$mail_sender, 'user-privilege-change-message'=>htmlspecialchars($mail_message)));
				break;
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
   
   <fieldset>
    <legend>Themes</legend>
    <div class="form-group">
	 <?php $theme_name = 'default'; ?>
     <img src="<?php echo get_theme_url('default'); ?>/screenshot.png" width="250" height="250" style="padding:5px; border:1px solid #aaa; border-radius:3px;"/><br/>
	 <div><?php echo $theme_name; ?> (<?php echo (get_current_theme() == $theme_name ? 'active' : '' ); ?>)</div>
    </div>
   </fieldset>
   <div style="height:25px;"></div>
   
   <fieldset>
    <legend>Message settings</legend>
    <style scoped>
    .mail-message { min-height:150px; resize:vertical; }
    </style>
    <div class="half-width float-left" style="margin-right:5px;">
     <div class="form-group">
	  <label>Registration Mail Sender</label>
	  <input id="registration-mail-sender-field" class="form-control" type="text" value="<?php echo sanitize_html_attribute(get_app_setting('registration-success-mail-sender')); ?>"/>
     </div>
     <div class="form-group">
      <label>Registration Message</label>
	  <textarea id="registration-mail-message-field" class="form-control mail-message"><?php echo get_app_setting('registration-success-message'); ?></textarea>
     </div>
	 <button data-update="registration-mail" class="btn btn-primary pull-right mail-message-update-btn">Save</button>
    </div>
   
    <div class="half-width float-left">
     <div class="form-group">
	  <label>Password Recovery Mail Sender</label>
	  <input id="password-recovery-mail-sender-field" class="form-control" type="text" value="<?php echo sanitize_html_attribute(get_app_setting('password-recovery-mail-sender')); ?>"/>
     </div>
     <div class="form-group">
      <label>Password Recovery Message</label>
	  <textarea id="password-recovery-mail-message-field" class="form-control mail-message"><?php echo get_app_setting('password-recovery-mail'); ?></textarea>
     </div>
	 <button data-update="password-recovery-mail" class="btn btn-primary pull-right mail-message-update-btn">Save</button>
    </div>
   </fieldset>
   
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
<script>
var msgUpdateBtns = document.querySelectorAll('.mail-message-update-btn');

for(var i = 0, len = msgUpdateBtns.length; i < len; i++)
{
	attachMailMessageUpdateListener(msgUpdateBtns[i]);
}

function attachMailMessageUpdateListener(updateBtn)
{
	Site.Event.attachListener(updateBtn, 'click', function(evt){
		
		Site.Event.cancelDefaultAction(evt);
		disable(updateBtn);
		showProcessing(updateBtn);
		
		var btnData     = updateBtn.getAttribute('data-update'); //possible values: 'registration-mail', 'password-recovery-mail', etc
		var action      = 'update_auto_mailer'; //('update-' + btnData).replace('-', '_');
		var updateType  = ('update-' + btnData);
		var mailSender  = $O(btnData + '-sender-field').value;
		var mailMessage = $Html(btnData + '-message-field');
		
		var postData = '' + 
		'action='         + action      +
		'&update_type='   + updateType  +
		'&mail_sender='   + mailSender  +
		'&mail_message='  + mailMessage;
		
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