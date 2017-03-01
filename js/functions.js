function getMaxNumber(numArr)
{
	return Math.max.apply(null, numArr);
}
	
function getMinNumber(numArr)
{
	return Math.min.apply(null, numArr);
}

function setAsProcessing(elemID)
{
	//$('#' + elemID).addClass('bg-right').addClass('bg-no-repeat').addClass('bg-spinner');
	Site.Util.addClassTo(elemID, 'bg-right bg-no-repeat bg-spinner');
}
	
function unsetAsProcessing(elemID)
{
	//$('#' + elemID).removeClass('bg-right').removeClass('bg-no-repeat').removeClass('bg-spinner');
	Site.Util.removeClassFrom(elemID, 'bg-right bg-no-repeat bg-spinner');
}
	
function disable(elemID)
{
	Site.Util.disableElement(elemID);
}

function enable(elemID)
{
	Site.Util.enableElement(elemID);
}

function empty(elemID)
{
	if(typeof $O(elemID).innerHTML != 'undefined')
	{
		$O(elemID).innerHTML = '';
	}
	
	if(typeof $O(elemID).value != 'undefined')
	{
		$O(elemID).value = '';
	}
}

function displayStatusMessage(msgContainer, msg, msgType)
{
	msgContainer = msgContainer || this;
	
	if( $O(msgContainer).className.indexOf('status-message') == -1 )
	{
		Site.Util.addClassTo(msgContainer, 'status-message');
	}
	
	switch(msgType)
	{
		case 'error' : Site.Util.removeClassFrom(msgContainer, 'success');
		               Site.Util.addClassTo(msgContainer, 'error');
					   break;
					   
		default      : Site.Util.removeClassFrom(msgContainer, 'error');
		               Site.Util.addClassTo(msgContainer, 'success');
					   break;
	}
	
	$Html(msgContainer, msg);
	jQuery('#' + msgContainer).fadeOut(7000, function(){ 
		Site.Util.removeClassFrom(msgContainer, 'error');
		Site.Util.removeClassFrom(msgContainer, 'success');
		$Html(msgContainer, '');
		$Style(msgContainer).display = 'inline-block';
	});
}

/*
* data members:
* userUrl, userName, userImage, userLastSeen, userJoinDate, userLocation
*/
function createUserCard(data)
{
	var userUrl       = data.userUrl;
	var userName      = data.userName;
	var userImage     = data.userImage;
	var userLastSeen  = data.userLastSeen;
	var userJoinDate  = data.userJoinDate;
	var userLocation  = data.userLocation;

	return [
	 '<div class="UserCard">', 
	   '<div class="container">',
	    '<div class="UserCard-profile">',
		 '<a href="' + userUrl + '"><div class="UserCard-avatar"><img class="Avatar" src="' + userImage + '"></div></a>',
		 '<ul class="UserCard-info" style="list-style-type:none; padding:0;">',
		  '<li title="Display name"><i class="icon fa fa-fw fa-user"></i><a href="' + userUrl + '">'      + userName     + '</a></li>',
		  '<li title="Location"><i class="icon fa fa-fw fa-globe"></i>'      + userLocation + '</li>',
		  '<li title="Signup date"><i class="icon fa fa-fw fa-history"></i>' + userJoinDate + '</li>',
		  '<li title="Last seen"><i class="icon fa fa-fw fa-clock-o"></i>'   + userLastSeen + '</li>',
		 '</ul>',
	    '</div>',
	   '</div>',
	 '</div>',
	].join('');
}

function shareOnFb(url, successCallback)
{   
    successCallback = ( (typeof successCallback == 'function') ? successCallback : function(){} );
	
	shareOnFacebook(url, {
		'success': function(msg) { 
		    notify(msg); 
			successCallback(); 
		},
		'error'  : function(msg) { 
		    notify(msg);
		}
	});
	
	function shareOnFacebook(url, opts)
	{
		FB.ui({
			method: 'share',
			href: url
		},
		function(response)
		{
			if (response && !response.error_code)
			{
				opts.success('You successfully shared this post on Facebook');
			} 
			else
			{
				opts.error('An error occurred while trying to post to Facebook. Please try again.');
			}
		})
	}
}

function shareOnGPlus(url, successCallback)
{
	successCallback = ( (typeof successCallback == 'function') ? successCallback : function(){} );
	
	Site.Util.popup('https://plus.google.com/share?url=' + url, 600, 300);
}

function shareOnLinkedIn(url, shareTitle, shareText, success)
{  
    successCallback = ( (typeof successCallback == 'function') ? successCallback : function(){} );
	
	Site.Util.popup('https://www.linkedin.com/shareArticle?url=' + url + '&mini=true&title=' + shareTitle + '&summary' + shareText, '520', '570');
}

//credits: http://stackoverflow.com/a/369231
function extractUrlParts(html, filter)
{
    var container = document.createElement("p");
    container.innerHTML = html;
	filter = filter || function(elem, idx, url){ return true; }

    var anchors = container.getElementsByTagName("a");
    var list = [];

    for (var i = 0; i < anchors.length; i++)
	{
		if( filter(anchors[i], i, anchors[i].href) )
		{
			var href = anchors[i].href;
			var text = anchors[i].textContent;

			if (text === undefined) 
			{
				text = anchors[i].innerText;
			}

			list.push({'anchor':'<a href="' + href + '">' + text + '</a>', 'href':href, 'text':text});
		}
    }

    return list;
}

function extractUrlsFromText(text)
{ 
	//pattern credits: http://stackoverflow.com/a/29719443
	var urls = text.match(/(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/gi) || [];
	
	return sanitize(urls);
	
	function sanitize(urls)
	{
		var sanitizedUrls = [];
		for(var i = 0; i < urls.length; i++)
		{
			var url = urls[i];
			
			//since the tinyMCE editor converts valid urls to their http(s) version,
			//get only http(s) prefixed urls, for sending to the server side
			//to generate inline url embedding code
			if( url.indexOf('http') != -1 )
			{
				sanitizedUrls.push( url.replace("</p>", "").replace(/>.+/g, '').replace("&nbsp;", "").replace("\"", "") );
			}
		}
		
		//make the array elements unique and return them
		//return sanitizedUrls.reduce(function(a,b){if(a.indexOf(b)<0)a.push(b);return a;},[]); //credits: http://stackoverflow.com/a/15868720
		
		return sanitizedUrls;
	}
}

function escapeRegex(text)
{
	//credits: http://blog.simonwillison.net/post/57956816139/escape
	if (!arguments.callee.sRE)
	{
		var specials = [ '/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\' ];
		arguments.callee.sRE = new RegExp( '(\\' + specials.join('|\\') + ')', 'g' );
	}
	return text.replace(arguments.callee.sRE, '\\$1');
}