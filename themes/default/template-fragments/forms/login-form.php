<?php 
$form_id = isset($id) ? $id : 'login-form';
$expired_session_user_login = isset($expired_session_user_login) ? $expired_session_user_login : '';
?>
<form id="<?php echo $form_id; ?>" class="form-vertical" action="" method="post">
  <div class="form-group">
   <label for="email">Username or Email</label><br>
   <input type="text" id="<?php echo $form_id; ?>-user-login-field" class="form-control" value="<?php echo $expired_session_user_login; ?>" size="30" maxlength="100" placeholder="your username or email">
  </div>
  <div class="form-group">
   <label for="password">Password</label><br/>
   <input type="password" id="<?php echo $form_id; ?>-user-password-field" class="form-control" placeholder="********">
  </div>
  
  <div class="form-group remember-me">
   <input type="checkbox" id="<?php echo $form_id; ?>-remember-user-field" style="vertical-align:top; margin-right:5px;">
   <span>Keep me signed in</span>
  </div>
  <div class="<?php echo $form_id; ?>-submit-button-container">
   <input type="button" id="<?php echo $form_id; ?>-submit-button" class="btn btn-primary pr25 pl25" value="Login">
  </div>
  <div class="clear"></div>
 <div id="<?php echo $form_id; ?>-status-message" class="text-centered">&nbsp;</div>
</form>
<script>
$('#<?php echo $form_id; ?>').on('submit', function(e){ processLogin(e); e.preventDefault(); });
$('#<?php echo $form_id; ?>-submit-button').on('click', function(e){ processLogin(e); e.preventDefault(); });
function processLogin(e)
{
	var formID = '<?php echo $form_id; ?>';
	var btnID  = formID + '-submit-button';
	
	setAsProcessing(btnID);
	disable(btnID);
	
	var email        = $('#' + formID + '-user-login-field').val();
	var password     = $('#' + formID + '-user-password-field').val();
	var rememberUser = $('#' + formID + '-remember-user-field').is(':checked') ? 1 : 0; //$('#checkbox').is(':checked') 
	
	$.ajax(ajaxURL + '/index.php', {
		method : 'POST',
		cache  : true,
		data   : { 
			'p'                         : 'users', 
			'login-user'                : true, 
			'userLogin'                 : email,
			'userPassword'              : password,
			'rememberUser'              : rememberUser, 
			'redirectOnSuccessfulLogin' : false, 
			'emptyLoginFieldError'      : 'Please fill in the email field',
			'emptyPasswordFieldError'   : 'Please fill in the password field',
			'unverifiedAccountError'    : 'The account details you supplied could not be verified',
		},
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Login attempt status : ' + status + '\r\nerror : ' + error );
			}
			
			displayStatusMessage(formID + '-status-message', 'An unknown error occurred. Please try again.', 'error');
			unsetAsProcessing(btnID);
			enable(btnID);
		},
		success  : function(data, status, jqXHR){
			
			if(isDevServer)
			{
				console.log( 'Login attempt status : ' + status + '\r\nsuccess : ' + data );
			}
			if(data.error)
			{
				displayStatusMessage(formID + '-status-message', data.message, 'error');
				unsetAsProcessing(btnID);
				enable(btnID);
			}
			
			else if(data.success)
			{
				if(typeof loginSuccess === 'function')
				{
					loginSuccess(data);
				}
				else
				{
					location.reload();
				}
			}
		},
		complete : function(jqXHR, status)
		{
			
		}
	})
}
</script>