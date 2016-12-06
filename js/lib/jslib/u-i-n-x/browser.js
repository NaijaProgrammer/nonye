var Browser = {

	UA: {

		Name: function()
		{
			if (typeof navigator.vendor != "undefined" && navigator.vendor == "KDE")
			{
				return 'kde';
			}
			
			else if (typeof window.opera != "undefined")
			{
				return 'opera';
			}
			
			else if(typeof document.all != "undefined")
			{
				return 'IE';
			}
			
			else if (typeof document.getElementById != "undefined")
			{
				if (navigator.vendor.indexOf("Apple Computer, Inc.") != -1)
				{
					return 'safari';
				}
				
				return 'mozilla';
			}
		},

		Version: function()
		{
			var agent = this.UA.Name().toLowerCase();

			if(agent == 'kde'&& typeof window.sidebar != "undefined")
			{
				return 'kde 3.2+';
			}
			
			else if(agent == 'opera')
			{
				agent = navigator.userAgent.toLowerCase();
				var version = parseFloat(agent.replace(/.*opera[\/ ]([^ $]+).*/, "$1"));
				
				if(version >= 7)
				{
					return "opera7+";
				}
				
				else if (version >= 5)
				{
					return "opera5+6";
				}
				
				return false;
			}
         
			else if(agent == 'ie')
			{
				agent = navigator.userAgent.toLowerCase();
				
				if(typeof document.getElementById != "undefined")
				{
					var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, "$1").replace(/ /, "");
					
					if (typeof document.uniqueID != "undefined")
					{
						if (browser.indexOf("5.5") != -1)
						{
							return browser.replace(/(.*5\.5).*/, "$1");
						}
						
						else
						{
							return browser.replace(/(.*)\..*/, "$1");
						}
					}
					
					else
					{
						return "ie5mac";
					}
				}
				
				return false;
			}
         
			else if(agent == 'safari')
			{
				if(typeof window.XMLHttpRequest != "undefined")
				{
					return "safari1.2";
				}
				
				return "safari1";
			}
			
			else if(agent == 'mozilla')
			{
				return navigator.userAgent;
			}
		},

		OS: function()
		{
			var agent = navigator.userAgent.toLowerCase();
			
			if(agent.indexOf("win") != -1)
			{
				return "win";
			}
			
			else if(agent.indexOf("mac") != -1)
			{
				return "mac";
			}
			
			else
			{
				return "unix";
			}
		}
	}, //end of Browser.UA

	setCookie: function(name, value, daysTillExpire, path, domain, secure)
	{
		var cookieName  = trim(name);
		var cookieValue = trim(value);
		var theCookie   = cookieName + "=" + cookieValue;
   
		if(daysTillExpire)
		{
			var d       = new Date();
			var expires = d.setTime(d.getTime() + (daysTillExpire*24*60*60*1000)).toGMTString();
			theCookie  += "; expires=" + expires;
		}  
		
		if(path)
		{
			theCookie += "; path=" + path;
		}
		
		else
		{
			theCookie += "; path=/";
		}
		
		if(domain)
		{
			theCookie +=  "; domain=" + domain;
		}
		
		if(secure)
		{
			theCookie += "; secure";
		}
		
		document.cookie = theCookie;
	},

	getCookie: function(searchName)
	{
		var cookies = document.cookie.split(";");
		
		for (var i = 0; i < cookies.length; i++)
		{
			var cookieCrumbs = cookies[i].split("=");
			var cookieName   = cookieCrumbs[0]; 
			var cookieValue  = cookieCrumbs[1];
     
			if (trim(cookieName) == trim(searchName))
			{ 
				return trim(cookieValue);
			}
		}
		
		return null;
	},

	//usage e.g: <a href="" onmouseover="func(); return Browser.setStatusBarMsg('hi')"> </a>
	setStatusBarMsg: function(msg)
	{
		window.status = msg;
		return true;
	},

	Window : {

		popup : function(winId, url, options)
		{
			var thisSize       = this.size();
			var defaultOptions = {'left' : thisSize.width/4, 'top' : thisSize.height/4}	
			var windowProps    = '';
			
			for( var x in options )
			{ 
				defaultOptions[x] = options[x]; 
			}
			
			for(var y in defaultOptions)
			{ 
				windowProps += y +'=' + defaultOptions[y] + ','; 
			}
			
			return window.open(url, winId,  windowProps);
		},

		//needs no modification: created by me
		size: function()
		{
			var w, h;

			//other browsers
			if(typeof window.innerWidth != 'undefined')
			{
				w = window.innerWidth; 
				h = window.innerHeight;
			}
			
			//IE
			else if(typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) 
			{
				w =  document.documentElement.clientWidth; 
				h = document.documentElement.clientHeight;
			}
			
			//IE
			else
			{
				w = document.body.clientWidth; 
				h = document.body.clientHeight;
			}
			
			return {'width':w, 'height': h};
		},//end of Browser.Window.Size

		scrollBarIsDown : function()
		{
			var visibleContentHeight = this.size().height;
			var maxScrollTop = docHeight() - visibleContentHeight;
			return this.getScrollPosition().top == maxScrollTop;
		}, 

		getScrollPosition: function ()
		{
			var Scroll = {'left':0, 'top':0};
			
			if(typeof window.pageYOffset != 'undefined')
			{
				Scroll = {'left':window.pageXOffset, 'top':window.pageYOffset};
			}
			
			else if(typeof document.documentElement.scrollTop != 'undefined' && (document.documentElement.scrollTop > 0 || document.documentElement.scrollLeft > 0))
			{
				Scroll = {'left':document.documentElement.scrollLeft, 'top':document.documentElement.scrollTop};
			}
			
			else if (typeof document.body.scrollTop != 'undefined')
			{
				Scroll = {'left':document.body.scrollLeft, 'top':document.body.scrollTop};
			}
			
			return Scroll;
		} //end Browser.getScrollPosition, this is complete
	}//end Window
} //end of Browser