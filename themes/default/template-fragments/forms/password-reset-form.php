<?php
$form_id     = !empty($form_id)    ? $form_id     : 'password-reset-form';
$user_pnonce = isset($user_pnonce) ? $user_pnonce : ''; 
?>
<form id="<?php echo $form_id; ?>">
 <div>
  <input id="<?php echo $form_id; ?>-new-password-field" class="form-control" type="password" placeholder="Enter your new password" />
  <input id="<?php echo $form_id; ?>-user-nonce-field" type="hidden" value="<?php echo $user_pnonce; ?>" />
 </div>
 <button id="<?php echo $form_id; ?>-submit-button" class="btn btn-primary pr25 pl25">Reset Password</button>
 <div id="<?php echo $form_id; ?>-status-message" class="clear text-centered"></div>
</form>
<script>
$('#<?php echo $form_id; ?>').on('submit', function(e){ processPasswordReset(e); e.preventDefault(); });
$('#<?php echo $form_id; ?>-submit-button').on('click', function(e){ processPasswordReset(e); e.preventDefault(); });
function processPasswordReset(e)
{
	var formID = '<?php echo $form_id; ?>';
	var btnID  = formID + '-submit-button';
	var msgField = formID + '-status-message';
	var nonce    = $('#' + formID + '-user-nonce-field').val();
	var password = $('#' + formID + '-new-password-field').val();
	
	setAsProcessing(btnID);
	disable(btnID);
	
	$.ajax(ajaxURL + '/index.php', {
		method : 'POST',
		cache  : true,
		data   : { 'p':'users', 'reset-password':true, 'nonce':nonce, 'password':password },
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Password reset attempt status : ' + status + '\r\nerror : ' + error );
				displayStatusMessage(msgField, data.message, 'error');
			}
		},
		success : function(data, status, jqXHR){
			
			if(isDevServer)
			{
				console.log( 'Password reset attempt status : ' + status + '\r\nsuccess : ' + data );
				console.log(data.message);
			}
			if(data.error)
			{
				displayStatusMessage(msgField, data.message, 'error');
			}
			
			else if(data.success)
			{
				
			}
		},
		complete : function(jqXHR, status)
		{
			unsetAsProcessing(btnID);
			enable(btnID);
		}
	})
}
</script>