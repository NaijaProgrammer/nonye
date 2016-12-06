//@encoding: utf-8
/*
* Dependencies: EventManager, WindowObject,
*/
var Effects = {}

Effects.dragElem = function (elemToDrag, event)
{
	if(typeof event == "undefined")
	{
		event = window.event;
	} 
   
	if(typeof event.pageX == "undefined")
	{
		var scrollPos = WindowObject.Position.getScrollPosition();
		event.pageX = event.clientX + scrollPos.left;
		event.pageY = event.clientY + scrollPos.top;
	}

	var target = detectObjectness(elemToDrag); 

	var targetPos = getElemPos(target);
	var currentLeft = parseInt(targetPos.left);
	var currentTop = parseInt(targetPos.top);

	if(isNaN(currentLeft))
	{
		currentLeft = "0";
	}
  
	if(isNaN(currentTop) )
	{
		currentTop = "0";
	}
  
	target.differenceX = currentLeft - event.pageX;
	target.differenceY = currentTop - event.pageY;

	document.currentTarget = target;
	document.actionType = 'move';

	attachEventListener(document, "mousemove", Effects.mousemoveDrag, false);
	attachEventListener(document, "mouseup", Effects.stopDragElem, false);

	return false;
}

Effects.resizeElem = function (elemToResize, event)
{
	if(typeof event == "undefined")
	{
		event = window.event;
	} 
	if(typeof event.pageX == "undefined")
	{
		var scrollPos = WindowObject.Position.getScrollPosition();
		event.pageX = event.clientX + scrollPos.left;
		event.pageY = event.clientY + scrollPos.top;
	}
   
	var target = detectObjectness(elemToResize); 
 
	document.currentTarget = target;
	document.actionType = 'resize';
	document.currMouseX = event.pageX;
	document.currMouseY = event.pageY;

	attachEventListener(document, "mousemove", Effects.mousemoveDrag, false);
	attachEventListener(document, "mouseup", Effects.stopResizeElem, false);

	return false;
}

Effects.mousemoveDrag = function (event)
{
	if(typeof event == "undefined")
	{
		event = window.event;
	}
	if(typeof event.pageX == "undefined")
	{
		var scrollPos = WindowObject.Position.getScrollPosition();
		event.pageX = event.clientX + scrollPos.left;
		event.pageY = event.clientY + scrollPos.top;
	}

	var target = document.currentTarget;

	var currMouseX = parseInt(document.currMouseX); 
	var currMouseY = parseInt(document.currMouseY); 
	var prevMouseX;
	var prevMouseY;

	if(document.actionType == 'move')
	{
		target.style.left = event.pageX + target.differenceX + "px";
		target.style.top = event.pageY + target.differenceY + "px";
		maintainBoundary(target);
		
		if(typeof target.element != 'undefined')
		{ //this is because of dynamic created using FloatingWindow constructor
			target.element.moved = true; 
		}
		else
		{ 
			target.moved = true;
		}
		Effects.resized = false;
	}

	else if(document.actionType == 'resize')
	{
		target.style = ((target.style) ? target.style : target.element.style);

		prevMouseX = parseInt(currMouseX); 
		prevMouseY = parseInt(currMouseY);
		currMouseX = parseInt(event.pageX);
		currMouseY = parseInt(event.pageY); 
    
		if(prevMouseX < currMouseX)
		{ 
			target.style.width =  parseInt(target.style.width) + 1 + "px";
			this.isMaximizing = true;
			this.isMinimizing = false;
		}
		else if(prevMouseX > currMouseX)
		{
			target.style.width =  parseInt(target.style.width) - 1 + "px";
			this.isMinimizing = true;
			this.isMaximizing = false;
		}
		if(prevMouseY < currMouseY)
		{ 
			target.style.height = parseInt(target.style.height) + 1 + "px"; 
			this.isMaximizing = true;
			this.isMinimizing = false;
		}
		else if(prevMouseY > currMouseY)
		{
			target.style.height = parseInt(target.style.height) - 1 + "px"; 
			this.isMinimizing = true;
			this.isMaximizing = false;
		} 
		Effects.resized = true; 
	}  
	return true;
}

Effects.stopDragElem = function (event)
{
	detachEventListener(document, "mousemove", Effects.mousemoveDrag,false);
	detachEventListener(document, "mouseup", Effects.stopDragElem,false);
	return true;
}

Effects.stopResizeElem = function (event)
{
	detachEventListener(document, "mousemove", Effects.mousemoveDrag,false);
	detachEventListener(document, "mouseup", Effects.stopResizeElem,false);
	Effects.resized = false; //added may 2, 2012, incase I start experiencing problems with resize
	return true;
}

Effects.enableDrag = function (dragHandle, elemToDrag)
{
	var mh = detectObjectness(dragHandle); 
	var crc = detectObjectness(elemToDrag);
	attachEventListener(mh, "mousedown", function(event){Effects.dragElem(crc, event);}, false);
}

Effects.fadeIn = function (elemId, maxOpacity, speed) 
{
	var opacityLevel = 0;
	maxOpacity = maxOpacity || 1;
	speed = speed || 10000;
	while(opacityLevel <= maxOpacity)
	{
		setTimeout( "$Opacity('" + elemId + "'," + opacityLevel + ")",  (opacityLevel * speed));
		opacityLevel += 0.01;
	}
}

Effects.fadeOut = function (elemId, minOpacity, speed)
{
	var opacityLevel = 1;
	var timer = 0;
	speed = speed || 10000;
	
	while(opacityLevel > minOpacity)
	{    
		setTimeout( "$Opacity('" + elemId + "'," + opacityLevel + ")", (timer * speed));
		timer += 0.01;
		opacityLevel -= 0.01;
	}
}