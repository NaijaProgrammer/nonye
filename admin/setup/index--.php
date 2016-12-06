<?php
require dirname(__DIR__). '/admin-bootstrap.php';
//if($_SERVER['REQUEST_METHOD'] == 'POST' ): var_dump($_POST); exit; endif; 
if( isset($_POST['action']) && ($_POST['action'] == 'register_user') )
{  
	$array_data = UserAuth::register_user($_POST['data'], $output_response=false);
	
	if( isset($array_data['success']) )
	{
		$user_id = $array_data['userID'];
		assign_role_to_user( $user_id, 'Super Admin' );
	}
	
	create_json_string($array_data, true);
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
	<legend style="margin-bottom:10px;">Welcome to the Admin Account Setup. Please enter your details below</legend>
	<form id="signup-form" class="form-vertical" action="" method="post">
	 <div><input type="email" name="email" id="signup-form-email-field" class="input-field" size="30" placeholder="you@example.org"></div>
	 <div><input type="password" name="password" id="signup-form-password-field" class="input-field" placeholder="********"></div>
	 <input id="signup-form-submit-button" type="button" style="padding:5px 20px;" value="Create Account">
	 <div id="signup-status-message" style="text-align:center; font-size:20px;"></div>
	</form>
   </center>
  </div>
 </div>
</div>
</div>
<script src="<?php echo SITE_URL; ?>/js/lib/jslib/jslib.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/eventmanager.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/jslib/u-i-n-x/xhr.js"></script>
<script src="<?php echo SITE_URL; ?>/js.php"></script>
<script src="<?php echo SITE_URL; ?>/js/site.js"></script>
<script>
Site.Event.attachListener( 'signup-form-submit-button', 'click', function(e){ processSignUpFormSubmission(e); } );
Site.Event.attachListener( 'signup-form', 'submit', function(e){ processSignUpFormSubmission(e); } );
function processSignUpFormSubmission(e)
{
	var btnID = 'signup-form-submit-button';
	
	Site.Event.cancelDefaultAction(e);
	disable(btnID);
	showProcessing();
		
	var email        = $O('signup-form-email-field').value;
	var password     = $O('signup-form-password-field').value;
		
	Site.UserModel.registerUser
	({
		'email'                 : 	email,
		'password'              : 	password,
		'requestURL'            :   '',
		'readyStateCallback'    : 	function(){},
		'debugCallback'         : 	function(reply){ console.log(reply); },
		'errorCallback'         :	function(xhrObject, aborted)
		{ 
			hideProcessing();
			enable(btnID); 
		},
		'successCallback'       : 	function(parsedResponse)
		{
			if(parsedResponse.error)
			{
				//$Html('signup-status-message', parsedResponse.message);
				displayStatusMessage(parsedResponse.message, 'error');
				hideProcessing();
				enable(btnID);
			}
			
			else
			{
				/*
				if(typeof onUserRegistrationSuccess === 'function')
				{
					var userData = {'userID':parsedResponse.userID}
					onUserRegistrationSuccess(userData);
				}
				*/
				location.href = '<?php echo ADMIN_URL; ?>';
				hideProcessing();
				//enable(btnID);
			}
		}
	});
	
	function disable(elem)
	{
		Site.Util.disableElement(elem);
	}
	
	function enable(elem)
	{
		Site.Util.enableElement(elem);
	}
	
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
		var msgField = 'signup-status-message';
		
		switch(msgType)
		{
			case 'error' : $Style(msgField).color = '#900'; break;
			default      : $Style(msgField).color = '#090'; break;
		}
		
		$Html(msgField, msg);
	}
}
</script>
</body>
</html>