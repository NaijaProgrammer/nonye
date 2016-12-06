<?php
if( ($_SERVER['REQUEST_METHOD'] == 'POST') )
{  
	echo json_encode( UserAuth::login_user($_POST['data']), true ); 
	exit;
}
?>
<!Doctype HTML>
<html>
 <head>
  <style>
  .outer{ display:table; position:absolute; height:100%; width:100%; }
  .middle{ display:table-cell; vertical-align:middle; }
  .inner{ margin-left:auto; margin-right:auto; width:/*whatever width you want*/; }
  .input-field { width:300px; height:30px; padding:5px; }
  input{ margin-top:10px; }
  </style>
 </head>
 <body>
  <div class="outer">
   <div class="middle">
    <div class="inner">
     <center>
	  <legend style="margin-bottom:10px;">Admin Interface. Sign in to proceed</legend>
	  <div id="status-message-field" style="font-size:20px;"></div>
	  <div><input id="login-field" type="text" placeholder="Email or Username" class="input-field" /></div>
	  <div><input id="password-field" type="password" placeholder="Password" class="input-field" /></div>
	  <div>
	   <input id="remember-user-field" type="checkbox" style="vertical-align:bottom; margin-right:5px;">
	   <span>Keep me signed in</span>
	  </div>
	  <div><input id="login-btn" type="submit" value="Sign in" style=" padding:5px 20px;"/></div>
     </center>	
    </div>
   </div>
  </div>

<script src="<?php echo SITE_URL; ?>/js/lib/jslib/jslib.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/eventmanager.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/xhr.js"></script>
<script src="<?php echo SITE_URL; ?>/js.php"></script>
<script src="<?php echo SITE_URL; ?>/js/site.js"></script>
<script>
(function(){
	var btnID = 'login-btn';
	Site.Event.attachListener(btnID, 'click', function(e){
		
		Site.Event.cancelDefaultAction(e);
		Site.Util.disableElement(btnID);
		showProcessing();
		
		var email        = $O('login-field').value;
		var password     = $O('password-field').value;
		var rememberUser = $O('remember-user-field').checked ? 1 : 0;
			
		Site.UserModel.loginUser
		({
			'requestURL'                :   '',
			'userLogin'                 : 	email,
			'userPassword'              : 	password,
			'rememberUser'              : 	rememberUser, 
			'redirectOnSuccessfulLogin' : 	false, 
			'emptyLoginFieldError'      : 	'Please fill in the email/username field',
			'emptyPasswordFieldError'   : 	'Please fill in the password field',
			'unverifiedAccountError'    : 	'The account details you supplied could not be verified',
			'readyStateCallback'        : 	function(){},
			'debugCallback'             : 	function(reply){ console.log(reply); },
			'errorCallback'				:	function(xhrObject, aborted)
			{ 
				if(aborted)
				{
					displayStatusMessage('This operation took too long and has been aborted', 'error');
				}
				
				hideProcessing(btnID); 
				Site.Util.enableElement(btnID);  
			},
			'successCallback' : function(parsedResponse)
			{
				if(parsedResponse.error)
				{
					displayStatusMessage(parsedResponse.message, 'error');
					hideProcessing(btnID);
					Site.Util.enableElement(btnID);
				}
				
				else
				{
					location.reload();
				}
			}
		});
	});
	
	function showProcessing()
	{
		$Style(btnID).backgroundRepeat = 'no-repeat';
		$Style(btnID).backgroundPosition = 'right';
		$Style(btnID).backgroundImage    = 'url("<?php echo rtrim(SITE_URL, '/'); ?>/resources/images/processing.svg")';
	}
	
	function hideProcessing()
	{
		$Style(btnID).backgroundImage = '';
	}
	
	function displayStatusMessage(msg, msgType)
	{
		var msgField = 'status-message-field';
		
		switch(msgType)
		{
			case 'error' : $Style(msgField).color = '#900'; break;
			default      : $Style(msgField).color = '#090'; break;
		}
		
		$Html(msgField, msg);
	}
})();
</script>
 </body>
</html>