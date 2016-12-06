function authenticateUser(response){
	$.ajax(ajaxURL + '/index.php', {
		method : 'POST',
		cache  : true,
		data   : { 
			'p'                          : 'users', 
			'authorize-with-third-party' : true, 
			'auth-provider'              : 'facebook',
			'email'                      : response.email,
			'firstname'                  : response.first_name,
			'lastname'                   : response.last_name,
			'rememberUser'               : true, 
		},
		error : function(jqXHR, status, error){
			if(isDevServer)
			{
				console.log( 'Third party authorization attempt status : ' + status + '\r\nerror : ' + error );
				//displayStatusMessage(formID + '-status-message', data.message, 'error');
			}
		},
		success  : function(data, status, jqXHR){
			if(isDevServer)
			{
				console.log( 'Third party authorization attempt status : ' + status + '\r\nsuccess : ' + data );
			}
			if(data.error)
			{
				//displayStatusMessage(formID + '-status-message', data.message, 'error');
			}
				
			else if(data.success)
			{
				location.reload();
			}
		},
		complete : function(jqXHR, status)
		{

		}
	})
}

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
		authenticateUser(response);
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
	}
	else
	{
		// The person is not logged into Facebook, so we're not sure if they are logged into this app or not.
		// document.getElementById('status').innerHTML = 'Please log ' + 'into Facebook.';
	}
}

window.fbAsyncInit = function(){
	FB.init({
		appId      : '1100758689958372', //'983608321746284',
		cookie     : true,
		xfbml      : true,
		version    : 'v2.5' //'v2.6'
	});
	
	// Call FB.getLoginStatus().  
	// This function gets the state of the
	// person visiting this page and can return one of three states to
	// the callback you provide.  They can be:
	//
	// 1. Logged into your app ('connected')
	// 2. Logged into Facebook, but not your app ('not_authorized')
	// 3. Not logged into Facebook and can't tell if they are logged into your app or not.
	FB.getLoginStatus(function(response){
		statusChangeCallback(response); 
	});
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));