<style>
.modal-backdrop.in { filter alpha(opacity=20);  opacity:.2;}
.modal {}
@media screen and (min-width: 768px)
{
	.modal-content
	{
	  width:700px;
	  margin:auto;
	  height:auto !important;
	  overflow:visible;
	  border-radius:3px;
	}
	.modal-dialog
	{
	  height: 80% !important;
	  padding-top:0px;
	}
	.modal-body
	{
	  height: 80%;
	  overflow: auto;
	}
}
.modal, .modal-content, .modal-dialog, .modal-header, .modal-body
{
	padding-left:0px !important;
	padding-right:0px !important;
}
</style>
<div id="user-authentication-section" class="modal fade">
 <div class="modal-content">
  <div class="modal-dialog" role="document">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div class="text-center">
     <div class="btn-group">
	  <a href="#return-user" role="tab" data-toggle="tab" class="big btn btn-danger"><span style="color:#fff;"><i class="fa fa-user"></i>&nbsp;Registered User</span></a>
      <a href="#new-user" role="tab" data-toggle="tab" class="big btn btn-default"><i class="fa fa-plus"></i>&nbsp;New User</a>
     </div>
    </div>
	<div class="clear">&nbsp;</div>
   </div>
   <div class="modal-body">
   
    <div class="tab-content">
	 <style scoped>
	    #login-form-submit-button, #signup-form-submit-button, #forgot-password-form-submit-button{float:right;}
		@media screen and (min-width: 768px){ #forgot-password-link { position:relative; bottom:95px; left:485px;} }
		.modal-header { padding:0; border-bottom:none; }
	  </style>
     <div class="tab-pane fade in active" id="return-user">
      <div>
	   <?php $page_instance->add_form('login-form', array() ); ?>
	   <a id="forgot-password-link" href="#fp-user" role="tab" data-toggle="tab" style="text-decoration:none;">&nbsp;Forgot Password</a>
	  </div>
	 </div>
	 <div class="tab-pane fade in" id="new-user">
	  <script>
		function signupSuccess(data){
			$('#login-form-status-message').removeClass('error');
			$('#login-form-status-message').addClass('success');
			$('#login-form-status-message').html('Signup successful. Please login using the form above');
			$('#login-form-user-login-field').val( data.userLogin );
			$('#forgot-password-form-email-field').val(data.userEmail);
			$('a[href="#return-user"]').tab('show');
		}
	  </script>
	  <div><?php $page_instance->add_form('signup-form', array() ); ?></div>
	 </div>
	 <div class="tab-pane fade in" id="fp-user">
	  <div><?php $page_instance->add_form('forgot-password-form', array() ); ?></div>
	 </div>
	</div>
	
	<div class="or-container">
     <hr class="or-hr">
     <div class="or">or</div>
    </div>
	
	<div class="text-center">
	 <button id="fb-login-link" class="social-btn bg-fb-color cursor-pointer text-center" title="Connect with Facebook">
	  <span class="icon-container fb-icon-container icon-font-awesome fb-icon"></span>
	  Connect with Facebook
	 </button>
	 
	 <script>
	 function handleThirdPartyAuth(authObj)
	 {
		authObj = authObj || {};
		//authObj.provider; //google, linkedin
		
		if( (typeof authObj.status === 'string') && (authObj.status == 'success' ) )
		{
			location.reload();
		}
		else
		{
			if(typeof authObj.message === 'string')
			{
				alert(authObj.message);
			}
		}
	 }
	 </script>
	 <button id="google-login-link" class="social-btn bg-google-color-blue cursor-pointer text-center" onclick="Site.Util.popup('<?php echo SITE_URL; ?>/user-auth/google', '520', '570');" title="Connect with Google">
	  <span class="icon-container google-icon-container-blue icon-font-awesome google-icon"></span>
	  Connect with Google
	 </button>
	</div>
	<div class="text-center">
	 <button id="linked-in-login-link" class="social-btn bg-linkedin-color cursor-pointer text-center" onclick="Site.Util.popup('<?php echo SITE_URL; ?>/user-auth/linkedin', '520', '570');" title="Connect with LinkedIn">
	  <span class="icon-container linkedin-icon-container icon-font-awesome linkedin-icon"></span>
	  Connect with LinkedIn
	 </button>
	 
	 <button id="twitter-login-link" class="social-btn bg-twitter-color cursor-pointer text-center" onclick="Site.Util.popup('<?php echo SITE_URL; ?>/user-auth/twitter', '520', '570');" title="Connect with Twitter">
	  <span class="icon-container twitter-icon-container icon-font-awesome twitter-icon"></span>
	  Connect with Twitter
	 </button>
	</div>
	
   </div>
  </div>
 </div>
</div>
<script>
//$('.post-editor-opener').on('click', function(event){
$('.user-auth-btn').on('click', function(event){ 
	$('#user-authentication-section').modal();
	
	/*
	$('#user-authentication-section').on('shown.bs.modal', function(){
		var contentWidth  = 50%;
		var contentHeight = 100%;
		var screenWidth   = window.innerWidth;
		var screenHeight  = window.innerHeight;
		var xpos = (screenWidth - contentWidth) * 0.5;
		var ypos = (screenHeight - contentHeight) * 0.5;
		
		document.getElementById('modal-content').style.left = xpos + 'px';
		document.getElementById('modal-content').style.top  = ypos + 'px';
	});
	*/
});


/* The files needed for this to function are defined in js/lib/fb/sdk-init.js */
/*function displayLoginPrompt()
{
	FB.login(function(response){ statusChangeCallback(response); });
}
document.getElementById('fb-login-link').onclick = function(){ displayLoginPrompt() };
*/
</script>