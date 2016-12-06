/*
* Constructor function
* configObj members:
* type string 'GET', 'POST ' (in upper or lowercase or mixed)
* url string
* async boolean
* requestData string
* cache boolean
* closureObject object {'closureName' : customNameString}
* debugCallback function passed the reply
* readyStateCallback   function passed the progress counter
* successCallback function passed the reply
* errorCallback function(xhrObject, aborted) handles both the timeout scenario as well as any error code and/or status, e.g 404
* timeout integer seconds until timeout
*
* Dependencies: 
*/
function XHR(configObj)
{
	this.configObj            = configObj || {}; 
	this.requestType          = this.configObj.type || 'POST';
	this.requestType          = this.requestType.toUpperCase();
	this.serverURL            = this.configObj.url;
	this.async                = this.configObj.async || true; //boolean
	this.requestData          = this.configObj.requestData;
	this.cache                = this.configObj.cache || false; //boolean
	this.closureObject        = this.configObj.closureObject || {};

	this.createXHRObject = function()
	{
		var xmlHttp = null;
     
		try //all browsers except IE6 and older
		{
			xmlHttp = new XMLHttpRequest();
		}
		catch(e)
		{
			//IE6 or older
			var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0","MSXML2.XMLHTTP.5.0","MSXML2.XMLHTTP.4.0","MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"];

			for (var i=0; i<XmlHttpVersions.length && !xmlHttp; i++)
			{
				try
				{
					xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
				}
				catch (e) 
				{
					xmlHttp = null;
				}
			}
		}

		/*
		* Augment the xmlHttp Object with configuration values
		* This enables us to call our custom setRequestHeaders() method on the object
		*/
		xmlHttp.cache             = this.cache;
		xmlHttp.requestType       = this.requestType;
		xmlHttp.setRequestHeaders = this.setRequestHeaders;

		return xmlHttp;   
	}

	/*
	* the 'this' object works correctly because when we call createXHRObject() above,
	* we copy the three configuration properties (two attributes and one method) needed here into the object it returns
	* and the 'this' refers to that object, which now possesses all these three properties, in addition to its other
	* native methods, including the 'setRequestHeader()' method which we call here
	*/
	this.setRequestHeaders = function ()
	{
		if(this.requestType == "POST")
		{
			this.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		}
		
		if(!this.cache)
		{
			this.setRequestHeader("X-Requested-With", "xmlhttprequest");
			this.setRequestHeader("If-Modified-Since", "Wed, 15 Jan 1995 01:00:00 GMT");
			this.setRequestHeader("Cache-Control","no-cache");
			this.setRequestHeader("Cache-Control", "must-revalidate");
			this.setRequestHeader("Cache-Control","no-store");
			this.setRequestHeader("Pragma","no-cache");
			this.setRequestHeader("Expires","0");
		}

		//this.setRequestHeaders.call(xhrObject);
	}

	/**
	* @credits: Javascript: the Definitive Guide, 5th edition, example 20.5
	*
	* Encode the property name/value pairs of an object as if they were from
	* an HTML form, using application/x-www-form-urlencoded format
	* 
	* @modified: Michael Orji; Oct 4, 2012 : 23:09
	*/
	this.encodeFormData = function(dataString)
	{
		var pairs     = [];
		var regexp    = /%20/g; // A regular expression to match an encoded space
		var dataArray = trim(dataString).split('&');
     
		for(var i in dataArray)
		{
			/*
			* splits it only at the first occurence of the '=' xter
			* this handles situations such as: <a href="www.trend-this.com">trend-this</a>
			* where we have the '=' xter of the link as part of the data, so we avoid splitting it as if it were among the name/value pairs
			* the used function was created on the same date in response to this problem
			*/
			if(typeof dataArray[i] == 'string')
			{
				eachData = splitOnFirstOccurenceOfMultipleSubstrings(dataArray[i], '=', "\x034"); 
			}
			//eachData  = dataArray[i].split('=');
       
			// The global function encodeURIComponent does almost what we want,
			// but it encodes spaces as %20 instead of as "+". We have to fix that with String.replace()
			var dataName  = encodeURIComponent(eachData[0]).replace(regexp,"+");
			var dataValue = encodeURIComponent(eachData[1]).replace(regexp,"+");

			pairs.push(dataName + '=' + dataValue);
		}

		// Concatenate all the name/value pairs, separating them with &
		return pairs.join('&');
	}
   
	this.makeRequest = function(closureObject)
	{
		var xhrObject            = this.createXHRObject(); 
		var emptyFunction        = function(){};
		var debugCallback        = this.configObj.debugCallback      || emptyFunction;
		var readyStateCallback   = this.configObj.readyStateCallback || emptyFunction;
		var successCallback      = this.configObj.successCallback    || emptyFunction;
		var errorCallback        = this.configObj.errorCallback      || emptyFunction; //handles both the timeout scenario as well as any error code and/or status, e.g 404
		var timeout              = this.configObj.timeout            || 0;
		var ready                = this.isComplete; //function reference, defined below
		var parseServerReply     = this.parseServerReply; //function reference, defined below
		var aborted              = false;
		var timer                = 0;

		/* 
		* In browsers such as Firefox that invoke (the onreadystatechange) handler multiple times in state 3, 
		* a progress callback allows a script to display download feedback to the user.
		* The progressCounter variable can be used to create such a (visual) progress indicator based on its current value
		* It is Incremented each time the readyState property is set to some value less than 4. 
		* Each call to the readyStateCallback function is passed an
		* integer value that specifies how many times it has been called.
		* Cf. Javascript: the definitive guide, 5th ed. example 20.7
		*/
		var progressCounter = 0; 
    
		if(timeout)
		{
			timer = setTimeout( function () {
					
				/*if(!ready(xhrObject))
				{
					aborted = true; //@credits: Accelerated Dom Scripting with Ajax, APIs and Libraries, chap. 5, section 4: planning for failure
					xhrOjbect.abort();
				}*/
				
				aborted = true; //@credits: Accelerated Dom Scripting with Ajax, APIs and Libraries, chap. 5, section 4: planning for failure
				xhrObject.abort();
					
			}, timeout * 1000);
		}

		if(this.requestType == 'GET')
		{ 
			this.serverURL += '?' + encodeURIComponent(this.requestData).replace(/%3D/g, '=').replace(/%26/g, '&');
		}
      
		var sendParam = (this.requestType == 'POST') ? this.encodeFormData(this.requestData) : null;
    
		xhrObject.open(this.requestType, this.serverURL, this.async);
		xhrObject.setRequestHeaders(); //custom function defined above, not to be confused with the native setRequestHeader method of the xhrObject
	
		/*
		* since it's not possible to return a value through the onreadystatechange event handler, coz of the anynchronous nature of Ajax,
		* we create this inner (private) function, 'innerFX' which -- by being called by the onreadystatechange() -- 
		* then returns the server's responseText through a closure, 'closureObject.closureName'
		*/
		closureObject = closureObject || {};
		var innerFX   = function(val)
		{
			closureObject.closureName = function()
			{
				var returnVal = val;
				return returnVal;
			}
		}
	
		xhrObject.onreadystatechange = function()
		{
			if(ready(xhrObject))
			{
				if(timer)
				{ 
					clearTimeout(timer); //@credits: javascript, the definitive guide, 5th edition, example 20.7
				} 
				
				if( !aborted && (xhrObject.status == 200) )
				{ 
					//@credits: Accelerated Dom Scripting with Ajax, APIs and Libraries, chap. 5, section 4: planning for failure
					successCallback(parseServerReply(xhrObject, debugCallback));
				 
					if(closureObject.closureName && (typeof closureObject.closureName == 'string') )
					{ //call inner function only if user specifies that a value (a closure) should be returned
						innerFX(xhrObject.responseText);
					}
				}
				else
				{
					/*
					* the aborted argument lets you determine if the call was aborted as a result of timeout, so you can serve an appropriate response
					* indicating either a timeout of the request or just an error returned from the server
					*/
					errorCallback(xhrObject, aborted);
					var messageOnAbort = aborted ? "The request timed out" : ""; 
					//("Error " + xhrObject.status + " : " + xhrObject.statusText + "\n" + messageOnAbort);    
				} 
			}
			else
			{
				readyStateCallback(++progressCounter); // see the declaration of the progressCounter variable above for how this can be used within the function definition
				//logToConsole('progress counter ' + progressCounter);
			}
		}

		xhrObject.send(sendParam);
	}

	//parses and returns server reply, either as XML, a JSON object or as a string
	this.parseServerReply = function(xhrObject, debugCallback)
	{
		if(debugCallback && typeof debugCallback === 'function')
		{ //added on Nov. 4, 2012
			debugCallback(xhrObject.responseText);
		}
    
		var responseObject = null;
		var rawValue       = '';
    
		//credits: Javascript the Definitive guide, 5th edition, example 20.6
		//@modified : michael Orji
		switch(xhrObject.getResponseHeader("Content-Type"))
		{
			case "text/xml"                 : rawValue = responseObject = xhrObject.responseXML; break;
			case "text/json"                :
			case "text/javascript"          :
			case "application/javascript"   :
			case "application/x-javascript" : responseObject = eval( '(' + xhrObject.responseText + ')' ); rawValue = xhrObject.responseText; break;
			case "text/html"                : 
			case "text/plain"               : 
			default                         : responseObject = rawValue = xhrObject.responseText; break;
		}

		return {'parsedValue':responseObject, 'rawValue':rawValue, 'toString': function (){return rawValue} };
	}

	this.isComplete = function(xhrObject)
	{
		try
		{ 
			if( xhrObject.readyState == 4 )
			{
				return true;
			}
		}
		catch(e)
		{
			logToConsole('Caught Exception: ' + e.description); //TO DO: Pass an error place holder parameter to the XHR constructor and place e.description inside
		}
	}

	this.run = function(closureObject)
	{
		this.makeRequest(closureObject);
	}
	
	this.run(this.closureObject);
}//end of XHRManager Constructor Function