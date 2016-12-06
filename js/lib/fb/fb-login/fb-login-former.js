window.fbAsyncInit = function() {
	FB.init({
		appId      : '1100758689958372',
		cookie     : true,  // enable cookies to allow the server to access the session
		xfbml      : true,  // parse social plugins on this page
		version    : 'v2.2' // use version 2.2
	});
	
	(function(){
		
		Site.Event.attachListener('fb-login-button', 'click', function doFBLogin(){
			processFBAction('login');
		});
		
		Site.Event.attachListener('fb-signup-button', 'click', function doFBSignup(){
			processFBAction('signup');
		});

		function processFBAction(action)
		{
			/*
			* Check if we already have the response object stored in this function object,
			* If so, no need to call FB.getLoginStatus.
			* Rather, just make use of the response object to process user login or signup
			*/
			if(typeof processFBAction.response !== 'undefined')
			{
				if(typeof processFBAction.response.status === 'connected')
				{
					processFBAPPLoginSuccess(action);
				}
				else
				{
					
				}
			}
			
			else
			{
				/*
				* This function gets the state of the person visiting this page and can return one of three states to the callback you provide.  
				* They can be:
				* 1. Logged into your app ('connected')
				* 2. Logged into Facebook, but not your app ('not_authorized')
				* 3. Not logged into Facebook and can't tell if they are logged into your app or not.
				*/
				// The response object is returned with a status field that lets the
				// app know the current login status of the person.
				FB.getLoginStatus(function(response){
					
					// Logged into your app and Facebook.
					if (response.status === 'connected')
					{
						processFBAPPLoginSuccess(action);
					} 
					
					// The person is logged into Facebook, but not your app.
					else if (response.status === 'not_authorized')
					{
						redirectToFBLoginPage();
						
						/*
						FB.login(function(response){
							console.log(response); return;
							//cache the response object
							processFBAction.response = response;
							
							//call this function again,
							//this time it will make use of the cached response object
							processFBAction(action);
						});
						*/
					} 
					
					// The person is not logged into Facebook, 
					// so we're not sure if they are logged into this app or not.
					else 
					{
						redirectToFBLoginPage();
						/*
						FB.login(function(response){
							
							//cache the response object
							processFBAction.response = response;
							
							//call this function again,
							//this time it will make use of the cached response object
							processFBAction(action);
						});
						*/
					}
				});
			}
		}
		
		function redirectToFBLoginPage()
		{
			//credits: http://stackoverflow.com/a/7522667
			//also see: http://stackoverflow.com/a/9950065
			var p = 'https://www.facebook.com/login.php?skip_api_login=1&api_key=1100758689958372&signed_next=1&next=https%3A%2F%2Fwww.facebook.com%2Fv2.5%2Fdialog%2Foauth%3Fredirect_uri%3Dhttp://localhost/sites/naija-so/authenticate/fb.php%253Fversion%253D42%2523cb%253Df34c15153%2526domain%253Dlocalhost%2526origin%253Dhttp%25253A%25252F%25252Flocalhost%25252Ffc2290658%2526relation%253Dopener%2526frame%253Df1a04e4de8%26display%3Dpopup%26response_type%3Dtoken%252Csigned_request%26domain%3Dlocalhost%26origin%3D1%26client_id%3D1100758689958372%26ret%3Dlogin%26sdk%3Djoey&cancel_url=http%3A%2F%2Fstaticxx.facebook.com%2Fconnect%2Fxd_arbiter.php%3Fversion%3D42%23cb%3Df34c15153%26domain%3Dlocalhost%26origin%3Dhttp%253A%252F%252Flocalhost%252Ffc2290658%26relation%3Dopener%26frame%3Df1a04e4de8%26error%3Daccess_denied%26error_code%3D200%26error_description%3DPermissions%2Berror%26error_reason%3Duser_denied%26e2e%3D%257B%257D&display=page';
			top.location = window.location = p;
		}

		function processFBAPPLoginSuccess(action)
		{
			console.log('Welcome!  Fetching your information.... ');
			
			/*
			* Official implementation from: https://developers.facebook.com/docs/facebook-login/web
			* was unable to get me the user's public_profile and email data as promised by the doc.
			* It only got me the user's ID and the user's name (firstname and lastname strung together)
			* So, had to resort to the solution below.
			*/
			//credits: http://stackoverflow.com/a/32606003, referencing: http://stackoverflow.com/a/32585470
			FB.api('/me', 'get', { fields:'id,name,gender,email,first_name,last_name' }, function (response) {
				console.log(response);
				switch(action)
				{
					case 'login'  : //handle login code here
					case 'signup' : //handle signup code here
				}
			});
		}
	})();
};