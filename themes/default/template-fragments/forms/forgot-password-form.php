<?php $form_id = isset($id) ? $id : 'forgot-password-form'; ?>
<p>Please enter your email in the field below and hit the submit button</p>
<form id="<?php echo $form_id; ?>" class="form-vertical">
  <div class="form-group">
   <label for="email">Email</label><br>
   <input type="text" id="<?php echo $form_id; ?>-email-field" class="form-control" placeholder="your username or email">
  </div>
  <button id="<?php echo $form_id; ?>-submit-button" class="btn btn-primary pr25 pl25">Submit Request</button>
  <div class="clear"></div>
  <div id="<?php echo $form_id; ?>-status-message" class="text-centered">&nbsp;</div>
</form>
<script>
$('#<?php echo $form_id; ?>').on('submit', function(e){ processPasswordRecovery(e); e.preventDefault(); });
$('#<?php echo $form_id; ?>-submit-button').on('click', function(e){ processPasswordRecovery(e); e.preventDefault(); });
function processPasswordRecovery(e)
{
	var formID = '<?php echo $form_id; ?>';
	var btnID  = formID + '-submit-button';
	var msgField = formID + '-status-message';
	
	setAsProcessing(btnID);
	disable(btnID);
	
	$.ajax(ajaxURL + '/index.php', {
		method : 'POST',
		cache  : true,
		data   : { 'p' : 'users', 'password-recovery': true, 'email' : $('#' + formID + '-email-field').val() },
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Password recovery attempt status : ' + status + '\r\nerror : ' + error );
				displayStatusMessage(msgField, data.message, 'error');
			}
		},
		success  : function(data, status, jqXHR){
			
			if(isDevServer)
			{
				console.log( 'Password recovery attempt status : ' + status + '\r\nsuccess : ' + data );
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