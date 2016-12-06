<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	$validate = Validator::validate(array(
		array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
		array( 'error_condition'=>!user_can('Manage Users'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
		array( 'error_condition'=>empty($user_id), 'error_message'=>'No user specified', 'error_type'=>'noUserSpecified')
	));

	if($validate['error'])
	{
		$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	
	else
	{
		$prev_user_role         = get_user_role($user_id);
		$new_user_role          = !empty($role) ? $role : 'User';
		$prev_user_capabilities = get_user_capabilities($user_id);
		$added_capabilities     = json_decode($added_capabilities, true);
		$removed_capabilities   = json_decode($removed_capabilities, true);

		if($prev_user_role != $new_user_role)
		{
			assign_role_to_user($user_id, $new_user_role);
		}
		
		foreach($added_capabilities AS $capability)
		{
			grant_capability_to_user($user_id, $capability);
		}

		foreach($removed_capabilities AS $capability)
		{
			revoke_user_capability($user_id, $capability);
		}
		
		$role_str      = '';
		$new_user_role = get_user_role($user_id);
		if($prev_user_role != $new_user_role)
		{
			$role_str = '<p>You have been assigned the <strong>'. $new_user_role. '</strong> role</p>';
		}
		
		$capabilities_str = '';
		$new_user_capabilities = get_user_capabilities($user_id);
		if( !empty($new_user_capabilities) )
		{
			$capabilities_str .= '<p>You have been granted the following capabilities:</p>';
			$capabilities_str .= "<ul>";
			
			if(is_string($new_user_capabilities))
			{
				$capability_id   = get_capability_id($new_user_capabilities);
				$capability_desc = ItemModel::get_item_data($capability_id, 'description');
				$capabilities_str .= "<li><strong>$capability : $capability_desc</strong></li>";
			}
			elseif(is_array($new_user_capabilities))
			{
				foreach($new_user_capabilities AS $capability)
				{
					$capability_id   = get_capability_id($capability);
					$capability_desc = ItemModel::get_item_data($capability_id, 'description');
					$capabilities_str .= "<li><strong>$capability : $capability_desc</strong></li>";
				}
			}
			
			$capabilities_str .= "</ul>";
		}
		
		if(is_string($prev_user_capabilities))
		{
			if(in_array($prev_user_capabilities, $removed_capabilities))
			{
				$newly_removed_capabilities = $prev_user_capabilities;
				$capabilities_str .= '<p>The following capabilities have been revoked:</p>';
				$capabilities_str .= "<ul>";
				
				$capability_id   = get_capability_id($newly_removed_capabilities);
				$capability_desc = ItemModel::get_item_data($capability_id, 'description');
				$capabilities_str .= "<li><strong>$capability : $capability_desc</strong></li>";
				
				$capabilities_str .= "</ul>";
			}
		}
		else if(is_array($prev_user_capabilities))
		{
			$newly_removed_capabilities = array_intersect($prev_user_capabilities, $removed_capabilities);
			if( !empty($newly_removed_capabilities) )
			{
				$capabilities_str .= '<p>The following capabilities have been revoked:</p>';
				$capabilities_str .= "<ul>";
				
				foreach($newly_removed_capabilities AS $capability)
				{
					$capability_id   = get_capability_id($capability);
					$capability_desc = ItemModel::get_item_data($capability_id, 'description');
					$capabilities_str .= "<li><strong>$capability : $capability_desc</strong></li>";
				}
				
				$capabilities_str .= "</ul>";
			}
		}
		
		if( $new_user_role != 'User' || in_array('Access Admin', $new_user_capabilities) )
		{
			$login_str = '<p>You may login <a href="'. ADMIN_URL. '">Here</a> to access the administration interface<p>';
		}
		else
		{
			$login_str = '';
		}
		
		$site_name     = get_site_name();
		$user_instance = UserModel::get_user_instance($user_id);
		$mail_subject  = 'Your Account at '. $site_name;
		$message       = get_mail_message('user-privilege-change-mail-message', array('username'=>$username, 'roles'=>$role_str, 'capabilities'=>$capabilities_str, 'login_url'=>$login_str));
		
		/*
		$message = 'Dear '. $user_instance->get('username'). ',<br />'.
				   'Your data has been updated on '. $site_name.
				   $role_str. $capabilities_str. $login_str.
					'	-- The '. $site_name. ' Site Admin';
		*/
				
		send_email(array(
			'to'      => $user_instance->get('email'),
			'from'    => $site_name. ' <'. get_app_setting('user-privilege-change-mail-sender'). '>',
			'subject' => $mail_subject,
			'message' => generate_html_mail( array('title'=>$mail_subject, 'message'=>$message) )
		));
		
		$return_data = array('success'=>true, 'message'=>'User data updated');
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Users'); ?>
<?php if(!isset($_GET['user'])): header("location:". ADMIN_URL. "/?dir=users"); exit; endif; ?>
<?php
$user_instance = UserModel::get_user_instance($_GET['user']);
extract( $user_instance->get() );
$user_role = get_user_role($user_instance->get('id'));
$user_capabilities = get_user_capabilities($user_instance->get('id'));
?>
<?php $page_title = 'Edit '. ( isset($username) ? $username : $email ). '\'s data'; ?>
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
   <div class="form-group"><input type="text"  value="<?php echo isset($lastname)  ? $lastname  : ''; ?>" placeholder="Lastname" class="form-control" readonly="readonly" /></div>
   <div class="form-group"><input type="text"  value="<?php echo isset($firstname) ? $firstname : ''; ?>" placeholder="Firstname" class="form-control" readonly="readonly" /></div>
   <div class="form-group"><input type="email" value="<?php echo $email; ?>" placeholder="Email" class="form-control" readonly="readonly" /></div>
   <div class="form-group">
    <select id="user-role-selector" class="form-control">
     <?php $avail_roles = get_roles(); ?>
     <option value="">-- Select User Role --</option>
	 <?php foreach($avail_roles AS $avail_role): ?>
	 <option value="<?php echo $avail_role['name']; ?>" <?php echo set_as_selected_option($user_role, $avail_role['name']); ?>><?php echo $avail_role['name']; ?></option>
	 <?php endforeach; ?>
    </select>
   </div>
   <div class="form-group" id="user-capabilities-container">
	<label>Specify User Capabilities</label>
	<?php $i = 0; $avail_capabilities = get_capabilities(); ?>	
	<?php foreach($avail_capabilities AS $avail_capability): ?>
	<?php
	 $cap_id   = $avail_capability['id'];
	 $cap_name = $avail_capability['name'];
		
	 $i++;
	 /*
	 * the first check in_array()comes from getting user capabilities from database
	 * the second check isset() takes care of when the form is submitted, provide sticky functionality
	 */
	 //if(in_array($cap_name, $user_capabilities) || isset($user_capabilities[$cap_name]) )
	 if( user_has_capability($cap_name, $user_instance->get('id')) )
	 {
		$checked_value = 'on';
	 }
	 else
	 {
		//unset($checked_value);
		$checked_value = '';
	 }
	 if($i % 2){ echo '<br/>'; }
	?>
	<span style="display:inline-block; width:200px; margin-right:15px;">
	 <input type="checkbox" class="user-capabilities" value="<?php echo $cap_name; ?>" style="vertical-align:top; margin-right:5px;" <?php echo set_as_checked($checked_value); ?> /><?php echo $cap_name; ?>
	</span>
	<?php endforeach; ?>
   </div>
   <input type="hidden" id="user-id" value="<?php echo $user_instance->get('id'); ?>" />
   <div id="status-message" class="text-centered status-message"></div>
   <div><input id="submit-btn" type="submit" value="Save" class="btn btn-primary processing-bg bg-no-repeat bg-right pull-right" /></div>
  </form>
 </div>
 <div class="clear"></div>
</div>
<script>
(function(){
	var userCapabilitiesVisible = true;
	toggleCapabilitiesDisplay();
	Site.Event.attachListener('user-role-selector', 'change', toggleCapabilitiesDisplay);
	function toggleCapabilitiesDisplay()
	{
		if(form.getSelectElementSelectedText('user-role-selector').toLowerCase()=='super admin')
		{
			slideUp('user-capabilities-container');
			userCapabilitiesVisible = false;
		}
		else
		{
			if(!userCapabilitiesVisible)
			{
				slideDown('user-capabilities-container');
				userCapabilitiesVisible = true;
			}
		}
	}
})();
(function(){
	var btnID    = 'submit-btn';
	var msgField = 'status-message';
	Site.Event.attachListener(btnID, 'click', function(e){
		Site.Event.cancelDefaultAction(e);
		disable(btnID);
		showProcessing(btnID);
		
		var userCaps     = document.querySelectorAll('.user-capabilities');
		var capsArray    = [];
		var capsToRemove = [];
		
		for(var i = 0; i < userCaps.length; i++)
		{
			if(userCaps[i].checked)
			{
				capsArray.push(userCaps[i].value);
			}
			else
			{
				capsToRemove.push(userCaps[i].value);
			}
		}
		
		var params = ''          +
		'action='                + 'update_user'                                            +
		'&user_id='              + $O('user-id').value                                      +
		'&role='                 + form.getSelectElementSelectedValue('user-role-selector') +
		'&added_capabilities='   + JSON.stringify(capsArray)                                +
		'&removed_capabilities=' + JSON.stringify(capsToRemove)
		
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
				}
				
				hideProcessing(btnID);
				enable(btnID);
			}
		});
	});
})();
</script>
<?php $page_instance->load_footer('', array()); ?>