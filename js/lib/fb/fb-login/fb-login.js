function doFBLogin(params)
{
	//checkLoginState();
	
	// Fetch user data using the Graph API after login is successful. 
	// See statusChangeCallback() for when this call is made.
	function handleSuccessfulLoginAndAppAuthorisation()
	{
		/*console.log('Welcome!  Fetching your information.... ');
		FB.api('/me', function(response){
			console.log(response);
			console.log('Successful login for: ' + response.name);
		});*/
		
		/*
		* Official implementation from: https://developers.facebook.com/docs/facebook-login/web
		* only returns the 'name' and 'id' of the user.
		* It was unable to get me the user's public_profile and email data as promised by the doc.
		* It only got me the user's ID and the user's name (firstname and lastname strung together)
		* So, had to resort to the solution below.
		*/
		//credits: http://stackoverflow.com/a/32606003, referencing: http://stackoverflow.com/a/32585470
		FB.api('/me', 'get', { fields:'id,name,gender,email,first_name,last_name' }, function (response){
			//console.log(response);
			
			if(typeof params.success === 'function')
			{
				params.success(response);
			}
		});
	}
	
	// This is called with the results from from FB.getLoginStatus().
	function statusChangeCallback(response)
	{
		//console.log('statusChangeCallback');
		//console.log(response);
		// The response object is returned with a status field that lets the
		// app know the current login status of the person.
		// Full docs on the response object can be found in the documentation
		// for FB.getLoginStatus().
		if (response.status === 'connected')
		{
			// Logged into Facebook and (gave) your app (authorisation)
			handleSuccessfulLoginAndAppAuthorisation();
		} 
		else if (response.status === 'not_authorized')
		{
			//The person is logged into Facebook, but not your app.
			//my deduction: likely because they didn't authorise your app (to access their data)
			//document.getElementById('status').innerHTML = 'Please log ' + 'into this app.';
			if(typeof params.notAuthorized === 'function')
			{
				params.notAuthorized(response);
			}
		}
		else
		{
			// The person is not logged into Facebook, so we're not sure if they are logged into this app or not.
			// document.getElementById('status').innerHTML = 'Please log ' + 'into Facebook.';
			if(typeof params.notLoggedIn === 'function')
			{
				params.notLoggedIn(response);
			}
			
			//this creates a recursive, infinite loop, because the displayLoginPrompt always calls this function.
			//So if user cancels or closes the dialog, we get a not logged in status, which re-displays the login prompt again, continuously, unless the user chooses to log in
			//I therefore moved this call into the checkLoginState() function 
			//displayLoginPrompt(); 
		}
	}

	// This function is called when someone finishes with the Login Button.  
	// See the onlogin handler attached to it in the sample code below.
	function checkLoginState()
	{
		// Call FB.getLoginStatus().  
		// This function gets the state of the
		// person visiting this page and can return one of three states to
		// the callback you provide.  They can be:
		//
		// 1. Logged into your app ('connected')
		// 2. Logged into Facebook, but not your app ('not_authorized')
		// 3. Not logged into Facebook and can't tell if they are logged into your app or not.
		FB.getLoginStatus(function(response){ 
		
			if( (response.status === 'connected')  || (response.status === 'not_authorized') )
			{
				//user is logged in to facebook
				statusChangeCallback(response); 
			}
			else
			{
				//user isn't logged in to facebook
				//displayLoginPrompt();
			}
		});
	}
	
	function displayLoginPrompt()
	{
		FB.login(function(response){ statusChangeCallback(response); });
	}
	
	document.getElementById(params.loginTrigger).onclick = function(){ displayLoginPrompt(); /*checkLoginState();*/ };
}