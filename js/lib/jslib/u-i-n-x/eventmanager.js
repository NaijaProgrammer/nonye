/*
* An object for managing window, browser, keyboard, etc events
* @author: Michael Orji
* @date: 25 Feb, 2012
*
* Dependencies: Browser(for formatEvent), 
*/
var EventManager = {

	/*
	* @credits: The javascript Anthology
	*/
	addLoadListener : function(fn)
	{
		if (typeof window.addEventListener != 'undefined')
		{
			window.addEventListener('load', fn, false);
		}
		else if (typeof document.addEventListener != 'undefined')
		{
			document.addEventListener('load', fn, false);
		}
		else if (typeof window.attachEvent != 'undefined')
		{
			window.attachEvent('onload', fn);
		}
		else
		{
			if (typeof window.onload != 'function')
			{
				window.onload = fn;
			}
			else
			{
				var oldfn = window.onload;
				window.onload = function()
				{
					oldfn();
					fn();
				};
			}
		}
	},

	attachEventListener : function (target, eventType, functionRef, capture)
	{
		target = $O(target) //line added by Michael orji, Feb 25, 2012
    
		if (typeof target.addEventListener != "undefined")
		{
			EventManager.attachEventListener = function(target, eventType, functionRef, capture)
			{
				target = $O(target);
				target.addEventListener(eventType, functionRef, capture);
			}
		}
		
		else if (typeof target.attachEvent != "undefined")
		{
			EventManager.attachEventListener = function(target, eventType, functionRef, capture)
			{
				target = $O(target);
				target.attachEvent("on" + eventType, function(){ functionRef.call(target) }); //IE: bind the "this" object to the target object, otherwise "this" points to the global (window) object
			}
		}
		
		else
		{
			EventManager.attachEventListener = function(target, eventType, functionRef, capture)
			{
				target    = $O(target);
				eventType = "on" + eventType;
				
				if (typeof target[eventType] == "function")
				{ //if the target already has a listener attached to it using the target.listener = function(){} approach,
					var oldListener = target[eventType]; //copy the listener function into the oldListener variable,
					target[eventType] = function()
					{ //then attach a new anonymous listener function to the target, from which you call the oldListener function, and the functionRef
						oldListener();
						return functionRef();
					};
				}
				
				else
				{
					target[eventType] = functionRef;
				}
			}
		}
	  
		EventManager.attachEventListener(target, eventType, functionRef, capture);
	},

	detachEventListener : function (target, eventType, functionRef, capture)
	{
		target = $O(target);
		
		if (typeof target.removeEventListener != "undefined")
		{
			EventManager.detachEventListener = function (target, eventType, functionRef, capture)
			{
				target = $O(target);
				target.removeEventListener(eventType, functionRef, capture);
			}		
		}
		
		else if (typeof target.detachEvent != "undefined")
		{
			EventManager.detachEventListener = function (target, eventType, functionRef, capture)
			{
				target = $O(target);
				target.detachEvent("on" + eventType, functionRef);
			}
		}
		
		else
		{
			EventManager.detachEventListener = function (target, eventType, functionRef, capture)
			{
				target = $O(target);
				target["on" + eventType] = null;
			}
		}
	  
		EventManager.detachEventListener (target, eventType, functionRef, capture);
	},

	/*
	* @credits: Wrox Professional Javascript for web Developers, p. 326
	* @original function name: EventUtil.formatEvent
	* @modified by: michael Orji
	* defines event properties for ie, so that ie is compatible with mozilla and w3c
	*/
	formatEvent : function (oEvent)
	{
		var B = Browser.UA.Name().toLowerCase();
		var S = Browser.UA.OS().toLowerCase();

		if ( (B == 'ie') && (S == 'win') ) 
		{
			oEvent.charCode   = (oEvent.type == "keypress") ? oEvent.keyCode : 0;
			oEvent.eventPhase = 2;
			oEvent.isChar     = (oEvent.charCode > 0);
			oEvent.pageX      = oEvent.clientX + document.body.scrollLeft;
			oEvent.pageY      = oEvent.clientY + document.body.scrollTop;
			oEvent.target     = oEvent.srcElement;
			oEvent.time       = (new Date).getTime();

			if (oEvent.type == "mouseout")
			{
				oEvent.relatedTarget = oEvent.toElement;
			}
			
			else if (oEvent.type == "mouseover")
			{
				oEvent.relatedTarget = oEvent.fromElement;
			}
			
			oEvent.preventDefault = function ()
			{
				this.returnValue = false;
			}
			
			oEvent.stopPropagation = function ()
			{
				this.cancelBubble = true;
			}
		}

		return oEvent;
	},
	
	getEventObject : function(event)
	{
		return this.eventObject(event);
	},

	eventObject : function(event)
	{
		//event = (typeof event !== "undefined") ? event : window.event ; 
		//event = event || window.event;
		//event = (!event) ? window.event : event;

		/*if(event)
		{
			return ( (event) ? event : window.event);
		}
		else
		{ //see eventObject2 below for how this works
			return this.eventObject2(); //( (window.event) ? this.formatEvent(window.event) : EventManager.eventObject.caller.arguments[0] );
		}*/
		
		return ( (event) ? event : this.eventObject2() );
	},

	/*
	* @credits: Wrox Professional Javascript for web Developers, p. 328
	* @original function name: EventUtil.getEvent
	* @modified by: michael Orji
	*
	* Functionally equivalent to the EventManager.eventObject function above.
	* Because the caller property is a pointer to a function, you can access the arguments property
	* of the event handler. The event object is always the first argument in an event handler, which means
	* you can access arguments[0] in the event handler to get the event object:
	*/
	eventObject2 : function()
	{
		return ( (window.event) ? this.formatEvent(window.event) : EventManager.eventObject2.caller.arguments[0] );  
	},
  
	eventTarget : function(event)
	{
		event = this.eventObject(event);
		var targetElement = null;
		targetElement = ( (event.target) ? event.target : event.srcElement);
		
		while (targetElement.nodeType == 3 && targetElement.parentNode != null)
		{
			targetElement = targetElement.parentNode;
		}
		
		return targetElement;
	},
   
	/*
	* returns a boolean value indicating
	* whether or not the passed element (elem)
	* is the target of the event
	* @access: public
	* @params: String indicating the target element to test for
	* 
	* e.g Usage :
	* EventManager.targetElementTypeIs('a'); 
	* EventManager.targetElementTypeIs('div');
	*/
	targetElementTypeIs : function(elem, event)
	{
		var target = this.eventTarget(event);
		return target.tagName.toLowerCase() == elem.toLowerCase();
	},

	targetIsDocument : function(event)
	{
		return this.targetElementTypeIs('document', event) || this.targetElementTypeIs('body', event) || this.targetElementTypeIs('html', event);
	}, 

	cancelDefaultAction : function(e)
	{
		e = this.eventObject(e);
		(typeof e.preventDefault !== "undefined") ? e.preventDefault() : (e.returnValue = false);
	},

	stopEventPropagation : function(e)
	{
		e = this.eventObject(e);
		(typeof e.stopPropagaion !== "undefined") ? e.stopPropagation() : (e.cancelBubble = true);
	},

	eventTypeIs : function(eventType, event)
	{
		event = this.eventObject(event);
		return (event.type == eventType);
	}  
}//end of the EventManager object