/*
* @author: Michael Orji
* Dependencies : Browser, EventManager,
*/
var Mouse = {

	Button: function(event)
	{
		var browserName = Browser.UA.Name().toLowerCase();
		event = EventManager.eventObject(event);

		if(browserName == 'ie')
		{ 
			switch(event.button)
			{
				case 0: return 'left button'; break;    
				case 1: return 'left button'; break;
				case 2: return 'right button'; break;
				case 4: return 'middle button'; break;
				case 3: return 'left and right button'; break;
				case 5: return 'left and middle button'; break;
				case 6: return 'right and middle button'; break;
			}
		}
		else
		{
			switch(event.button)
			{ 
				case 0: return 'left button'; break;
				case 1: return 'middle button'; break;
				case 2: return 'right button'; break;
			}
		}
      
	}, //close Button()

	Position: function(event)
	{
		event              = EventManager.eventObject(event); 
		var cursorLocation = {'left': 0, 'top': 0};
		var scrollPos      = Browser.Window.getScrollPosition();

		/* 
		IE for Mac has pageX (but it's an incorrect value) but it doesn't have x
		so testing for x elmininates IE for Mac and testing for pageX eliminates other
		IE versions that don't support this propertty, thereby leaving all IE browsers to use
		the clientX property. x is actually a nonstandard property, but most browsers support
		it (the exceptions being Opera 8+ and Internet Explorer)
	   */
		if(typeof event.pageX !== "undefined" && typeof event.x !== "undefined")
		{
		   cursorLocation.left = event.pageX;
		   cursorLocation.top  = event.pageY;
		}
		else
		{
		   cursorLocation.left = event.clientX + scrollPos.left;
		   cursorLocation.top  = event.clientY + scrollPos.top;
		}
		
		return cursorLocation;
	}, //end Position()

	/*
	* @credits: http://javascript.info/tutorial/mouse-events
	* @date: 27 Feb, 2012
	*/
	isOutside : function(mainElem, evt)
	{
		evt        = EventManager.eventObject(evt); //added by Michael Orji
		var parent = $O(mainElem); //added by Michael Orji
		var elem   = evt.relatedTarget || evt.toElement || evt.fromElement;
		
		while ( elem && elem !== parent)
		{
		   elem = elem.parentNode;
		}
		
		if ( elem !== parent)
		{
		   return true
		}
	}// end isOutside()
}