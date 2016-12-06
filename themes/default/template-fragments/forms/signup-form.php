<form id="signup-form" class="form-vertical" action="" method="post">
 <div class="form-group">
  <label for="name">Full Name (optional)</label><br>
  <input type="text" id="signup-form-name-field" class="form-control" placeholder="Firstname Lastname">
 </div>
 <div class="form-group">
  <label for="name">Username (optional)</label><br>
  <input type="text" id="signup-form-username-field" class="form-control" placeholder="Username (no spaces)">
 </div>
 <div class="form-group">
  <label for="email">Email</label><br>
  <input type="email" id="signup-form-email-field" class="form-control" placeholder="you@example.org">
 </div>
 <div class="form-group">
  <label for="password">Password</label><br/>
  <input type="password" id="signup-form-password-field" class="form-control" placeholder="********">
 </div>
 <div class="form-group">
  <label for="password verify">Re-enter Password</label><br/>
  <input type="password" id="signup-form-password-verify-field" class="form-control" placeholder="********">
 </div>
 <div class="form-group use-terms-agreement">By registering, you agree to the <a href='/legal/terms-of-service' target='_blank'>terms of use</a>.</div>
 <input id="signup-form-submit-button" class="btn btn-primary pr25 pl25" type="button" value="Sign up">
 <div class="clear"></div>
 <div id="signup-status-message" class="text-centered">&nbsp;</div>
</form>
<script>
$('#signup-form').on('submit', function(e){ processUserSignup(e); e.preventDefault(); });
$('#signup-form-submit-button').on('click', function(e){ processUserSignup(e); e.preventDefault(); });
function processUserSignup(e)
{
	var formID = 'signup-form';
	var btnID  = formID + '-submit-button';
	
	setAsProcessing(btnID);
	disable(btnID);
	
	var name      = $('#signup-form-name-field').val();
	var username  = $('#signup-form-username-field').val();
	var email     = $('#signup-form-email-field').val();
	var password  = $('#signup-form-password-field').val();
	var password2 = $('#signup-form-password-verify-field').val();
	
	$.ajax(ajaxURL + '/index.php', {
		method : 'POST',
		cache  : false,
		data   : { 
			'p'           : 'users', 
			'user-signup' : true, 
			'name'        : name,
			'username'    : username,
			'email'       : email,
			'password'    : password,
			'password2'   : password2
		},
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Signup attempt status : ' + status + '\r\nerror : ' + error );
				displayStatusMessage(formID + 'signup-status-message', 'An unknown error occurred. Please try again.', 'error');
				unsetAsProcessing(btnID);
				enable(btnID);
			}
		},
		success  : function(data, status, jqXHR){
			
			if(isDevServer)
			{
				console.log( 'Signup attempt status : ' + status + '\r\nsuccess : ' + data );
			}
			if(data.error)
			{
				displayStatusMessage('signup-status-message', data.message, 'error');
			}
			
			else if(data.success)
			{
				$('#signup-form')[0].reset();
				$('#signup-status-message').removeClass('error');
				
				if(typeof signupSuccess === 'function')
				{
					signupSuccess(data);
				}
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