function initFBSDK(doneCallback, params){
	window.fbAsyncInit = function(){
		FB.init({
		  appId      : '1100758689958372', //'983608321746284',
		  cookie     : true,
		  xfbml      : true,
		  version    : 'v2.5' //'v2.6'
		});
		
		if(typeof doneCallback === 'function')
		{
			doneCallback(params);
		}
	};
}

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));