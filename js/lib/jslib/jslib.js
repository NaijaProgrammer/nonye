/*
* Wrapper function for document.getElementById
* Argument: HTML element id or DOM object reference
*/
function $O(elem)
{
	var obj = null;
	
	if(isObject(elem))
	{
		obj = elem;
	}
	
	else if($ID(elem))
	{
		obj = $ID(elem);
	}
	
	else
	{
		obj =  $ID(String(elem));
	}

	function $ID(elemId)
	{
		return document.getElementById(elemId);
		//return typeof elemId=='string'?document.getElementById(elemId):elemId;
	}
   
	return obj;
}

/*
* Wrapper function for document.getElementsByTagName
* Argument: HTML element tag name
*/
function $Tag(tagName)
{
	var that = (this == window) ? document : this;
	return ( (tagName) ? that.getElementsByTagName(tagName) : that.getElementsByTagName('*') );
}

/*
* returns the style property of supplied element
* Argument: HTML element id or reference
*
* @added style opts on Dec. 13, 2015 @9:56pm
*/
function $Style(elem, styleOpts)
{
	elem = $O(elem);
	if(!elem)
	{
		return null;
	}
	if(typeof styleOpts === 'string')
	{
		optsArr = styleOpts.split(',');
		optsArr.forEach(function(member){
			var memberArr  = member.split('=');
			var styleName  = memberArr[0];
			var styleValue = memberArr[1];
			elem.style['styleName'] = String(styleValue);
		});
	}
	else if(typeof styleOpts === 'object')
	{
		for(var x in styleOpts)
		{
			elem.style[x] = String(styleOpts[x]);
		}
	}
	
	return elem.style;
}

/* 
* gets (and optionally sets) the innerHTML value of element
* Argument[s]
*    HTML element id or reference
*    String value : optional 
* if the optional value is supplied, the element's inner HTML value is set to the supplied value and then returned
* otherwise, the current innerHTML value is returned
*/
function $Html(id, value)
{
	if (typeof value != 'undefined')
	{
		$O(id).innerHTML = value
	}
	
	return $O(id).innerHTML
}

function append(id, value)
{
	if ( typeof value != 'undefined' )
	{
  		$O(id).innerHTML += value
	}
}

/* gets or sets the innerText / textContent value of element
* Argument[s]
*    HTML element id or reference
*    String value : optional 
* if the optional value is supplied, the element's innerText value is set to the supplied value and then returned
* otherwise, the current innerText value is returned
*/
function $Text(elem, val)
{ 
	elem = $O(elem);
	var tc = '';

	if(typeof elem.textContent !== 'undefined')
	{
		if(typeof val === 'string')
		{
			elem.textContent = val;
		}
		tc = elem.textContent;
	}
	
	else
	{
		if(typeof val === 'string')
		{
			elem.innerText = val;
		}
		tc = elem.innerText;
	} 
	return tc;
}

/*
* returns a collection of every element with the supplied css class name
* Arguments:
*    String css class name
*    boolean 
*/ 
function $Class(cssClassName, strict)
{
	var arr = [];
	var that = (this == window) ? document : this;
	var candidates = that.getElementsByTagName('*');
	var len = candidates.length;
	
	for(var i = 0; i < len; i++)
	{
		if(strict)
		{
			if(candidates[i].className == cssClassName)
			{
				arr.push(candidates[i]);
			}
		}
		
		else
		{
			if( (candidates[i].className.indexOf(cssClassName) != -1) || (candidates[i].className == cssClassName) )
			{
				arr.push(candidates[i]);
			}
		}
	}
	
	return arr;
}

/*
* returns style properties of an element
* cf: slider.js for example usage
* @date: October 7, 2012
*
* Nov. 21, 2015: TO DO: augment to return more than just style properties
*/
function $properties(element)
{
	element                   = $O(element);
	element.displayType       = getDisplayType(element); 
	element.displayValue      = getStyleValue(element, element.displayType);
	element.visibilityValue   = (element.displayType == 'visibility') ? 'visible' : 'block';
	element.invisibilityValue = (element.displayType == 'visibility') ? 'hidden'  : 'none';
	element.isHidden          = (element.displayValue == element.invisibilityValue)  ? true : false;
  
	return element;
}

/*
* centers an element on the page
* @author: Michael Orji
* @date: August 25, 2012
* @dependency : Browser
*/
function $Center(elem)
{
	elem = $O(elem);

	var elemDimensions = size(elem);
	var elemWidth      = parseInt(elemDimensions.width)  || parseInt(getStyleValue(elem, 'width')); 
	var elemHeight     = parseInt(elemDimensions.height) || parseInt(getStyleValue(elem, 'height'));
	var bDimensions    = Browser.Window.size();
	var bWidth         = parseInt(bDimensions.width);
	var bHeight        = parseInt(bDimensions.height);
	var xPos           = (bWidth - elemWidth)   * 0.5;
	var yPos           = (bHeight - elemHeight) * 0.5;

	var parent = elem.parentNode ? elem.parentNode : document.body;
	var removed = parent.removeChild(elem); //remove the child, so that when we later position it absolutely, it won't be in reference to the parent element which may have a relative positioning
 
	$Style(removed).position = 'fixed';
	$Style(removed).left     = xPos + 'px';
	$Style(removed).top      = yPos + 'px';

	document.body.appendChild(removed);
}

$Opacity = function(elemId, opacityLevel)
{   
	var elem = $O(elemId);
	
	if(opacityLevel > .99)
	{
		opacityLevel = .99;
	}
	
	if(opacityLevel < 0)
	{
		opacityLevel = 0;
	}

	if(typeof elem.style.opacity != 'undefined')
	{
		elem.style.opacity = opacityLevel;
	}
	
	else if(typeof elem.style.MozOpacity != 'undefined') 
	{
	   elem.style.MozOpacity = opacityLevel;
	}
	
	else if(typeof elem.style.KhtmlOpacity != 'undefined')
	{
		elem.style.KhtmlOpacity = opacityLevel;
	}
	
	else
	{
		elem.style.filter = "alpha(opacity=" + opacityLevel * 100 + ")";
	}
}

/* 
*removes leading and trailing spaces from string 
*/
function trim(s)
{
	return ( (typeof s == 'string') ? s.replace(/(^\s+)|(\s+$)/g, "") : s );
}

/*
* @credits: Stoyan Stefanov "Object Oriented Javascript"
*/
function extendByValue(Child, Parent)
{
	var p = Parent.prototype;
	var c = Child.prototype;
	
	for (var i in p)
	{
		c[i] = p[i];
	}
	c.uber = p;
}

function extend(Child, Parent)
{
	extendByValue(Child, Parent);
}

/*
* @author: Michael Orji
* @date: 21, August, 2013
* Creates a new child object from parent 
* or 
* Copies only reference members (objects, functions, arrays) by reference from parent into child
* it has the advantage that the child does not inherit the parent's non-reference properties, and hence cannot modify them
* thereby inheriting only reference properties which are meant to be re-used
*
* Based on the customExtend function by Stoyan Stefanov in Object Oriented Javascript
*
* USE EXAMPLES:
* 1. var childObject = referenceExtend(parentObject); //create a brand new Child Object with no properties except parent's reference members
* 2. var childObject = { 'prop':'value', 'method':methodDefinition }
*    referenceExtend(parentObject, childObject); //augment already existing childObject with parent's reference members
*
*/
function referenceExtend(Parent, Child) 
{
	Child = Child || {};

   	for(var i in Parent)
   	{
		if ( (typeof Parent[i] === 'object') || (typeof Parent[i] === 'function') || (typeof Parent[i] === 'array') ) 
		{
			if ( (typeof Parent[i] === 'object') || (typeof Parent[i] === 'array') )
			{
				Child[i] = (Parent[i].constructor === Array) ? [] : {};
				referenceExtend(Parent[i], Child[i]);
			}
			else 
			{
				Child[i] = Parent[i];
			}
		} 
	}
	
	return Child;
}

/*
* Splits a string on the first occurence of multiple substrings
* @credits: http://stackoverflow.com/questions/7527374/how-to-split-a-string-on-the-first-occurence-of-multiple-substrings-in-j
* @date: 27 Oct, 2012
* idea is, replace the first occurence with a special (invisible) string, and then split against this string
*/
function splitOnFirstOccurenceOfMultipleSubstrings(strToSplit, substr, invisibleXter)
{
	invisibleXter = invisibleXter || "\x034";
	return strToSplit.replace(new RegExp(substr), invisibleXter).split(invisibleXter); 
}

function getLastChar(str)
{
	return trim(str.substring(str.length - 1));
}

/*
* Gets the absolute path to specified file or folder 
* @author: Michael Orji
* @date: Nov. 11, 2012
*/
function getPath(targetName)
{
	var scripts     = $Tag('script');
	var targetPath  = '';
	
	for(i = 0; i < scripts.length; i++)
	{
		var scriptPath           = scripts[i].src.replace(/%20/g, ' ');  
		var targetPathStartIndex = scriptPath.lastIndexOf('/' + targetName) + 1; //
		var scriptName           = scriptPath.substring(targetPathStartIndex, targetPathStartIndex + targetName.lastIndexOf(getLastChar(targetName)) + 1 );
		if(trim(scriptName) == trim(targetName))
		{ 
			targetPath = trim( scriptPath.substring(0, targetPathStartIndex) ); 
			return targetPath; //return targetPath + '/';
		}
	}
	
	return targetPath;
}

/*
* get the document height
* credit: http://james.padolsey.com/javascript/get-document-height-cross-browser/
*/
function docHeight()
{
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function size(elem)
{
	elem = $O(elem);
	if(!elem) return;
	
	var w = typeof elem.style != 'undefined' ? elem.style.width  : elem.offsetWidth;
	var h = typeof elem.style != 'undefined' ? elem.style.height : elem.offsetHeight;
	return {width: parseInt(elem.offsetWidth), height: parseInt(elem.offsetHeight)}
}

/*
* gets the css style value of an element
*/
getStyleValue =  function (element, CSSProperty)
{
	element = $O(element);
	if(!element) return;
	var styleValue = (typeof element.currentStyle != "undefined") ? element.currentStyle : document.defaultView.getComputedStyle(element, null);   
	return styleValue[CSSProperty];
}

function getDisplayType(element)
{
	element = $O(element);
	if(!element){ return; }
	return (element.style.visibility) ? 'visibility' : 'display';
}

function isObject(elem, strict)
{
	if(strict)
	{
		return (typeof elem === 'object');
	}
	return (typeof elem == 'object' || typeof elem == 'array' || typeof elem == 'function');
}

/*
* resolves the lexical scope of calling functions
* on a loop
*
* e.g
* for(var k = 0; k < thumbs.length; k++){
*  attachEventListener(thumbs[k], "click", function(event){selectImg(mediaIds[k]); handleEvent(event);}, false);
* }:
* would return only the last media id, the last loop value
* of K due to the lexical scoping of functions in javascript
* since the anonymous function only gets the final value of 'k'
*
* using resolveScope however, we could write the above as follows:
* for(var k = 0; k < thumbs.length; k++){
*  attachEventListener(thumbs[k], "click", resolveScope(selectImg, mediaIds[k]), false);
* }
* and get the desired result
*/
//function resolveScope(callback, x, allowDefaultAction, allowEventPropagation)
function resolveScope(callback, x)
{
	var func = callback; 

	return function(event)
	{
		if(typeof x === 'array' || typeof x === 'object')
		{ 
			arrayWalk(x, func);
		}

		else
		{
			func(x); 
		}
    
		//handleEvent(event, allowDefaultAction, allowEventPropagation);
	}
}

function execute()
{
	var callback = arguments[0], 
	    args     = [];

	for(var i = 1; i < arguments.length; i++)
	{
		args.push(arguments[i]); 
	}
   
	return function()
	{
		callback.apply(callback, args);  
	}
}

/* @author: michael Orji
*  @date  : 15 sept, 2012
*/
function exec(callback)
{
	var args = [];
	for(var i = 1; i < arguments.length; i++)
	{
		args.push(arguments[i]);
	}
	
	return function()
	{
		return callback(args);
	}
}

function delay(callback, delayTime)
{
	var t = setTimeout(callback, delayTime);
	return t;
}

function hide()
{
	if(arguments.length > 0)
	{
		for(var i = 0; i < arguments.length; i++)
		{
			$Style(arguments[i]).visibility = 'hidden';
		}
	}
}

function show()
{
	var len = arguments.length;
	
	if(len > 0)
	{
		for(var i = 0; i < len; i++)
		{
			$Style(arguments[i]).visibility = 'visible';
		}
	}
}

function display(elems, displayVal)
{
	elems = (typeof elems == 'string') ? elems.split(elems, ',') : elems;
	displayVal = displayVal || 'block';

	if(elems.length > 0)
	{
		for(var i = 0; i < elems.length; i++)
		{
			if($O(trim(elems[i])))
			{
				$Style(trim(elems[i])).display = displayVal;
			}
		}
	}
}

function undisplay()
{
	if(arguments.length > 0)
	{
		for(var i = 0; i < arguments.length; i++)
		{
			if($O(arguments[i]))
			{
				$Style(arguments[i]).display = 'none';
			}
		}
	}
}

function toggleDisplay(elem, currDisplay)
{
	if( !(elem = $O(elem)) )
	{ 
		return; 
	}
	
	currDisplay = currDisplay || $Style(elem).display || 'none';
	$Style(elem).display =  ( ($Style(elem).display  != 'none') ? 'none' : 'block' );
	return elem;
}

function resetForm(theform)
{
	$O(theform).reset();
}

function submitForm(theform)
{
	theform.submit();
}

function isVisible(elem)
{
	return ( ($Style(elem).visibility != 'hidden') && ($Style(elem).display != 'none') );
}

fadeIn = function (elemId, maxOpacity, speed)
{
	Effects.fadeIn(elemId, maxOpacity, speed);
}

fadeOut = function (elemId, minOpacity, speed)
{
	Effects.fadeOut(elemId, minOpacity, speed)
}

/*
* useful for strings and arrays at the moment
* TO DO: extend to apply to every possible object and data-type (except boolean)
* @date: 29 Apr, 2012
*/
function isEmpty(obj)
{
	return obj.length == 0;
}

/*
* javascript equivalent of the php array_walk funcion
* works for both arrays and objects, 
* @return type void;
* @date: 04, sept, 2010;
*/
function arrayWalk(arr, func)
{
    for(var x in arr)
	{
		func(arr[x]);
    }
}

/* 
* keeps a draggable element from being dragged outside of
* the parent's boundary size or browser window boundary
* @author: Michael Orji
* @date: 2nd october, 2010
*/
function maintainBoundary(elem, parentElem)
{
	if(parentElem == null || parentElem == '')
	{
		parentElem   = Browser;
		parentWidth  = Browser.Size.width();
		parentHeight = getDocHeight(); //Browser.Size.height();
	}

	else
	{
		parentWidth = parseInt(getStyleValue(parentElem, 'width'));
		parentHeight = parseInt(getStyleValue(parentElem, 'height'));
	}

	var elemWidth  = parseInt(getStyleValue(elem, 'width'));
	var elemHeight = parseInt(getStyleValue(elem, 'height'));
	var elemLeft   = parseInt(elem.style.left);
	var elemRight  = parentWidth - (elemLeft + elemWidth);
	var elemTop    = parseInt(elem.style.top);
	var elemBottom = parentHeight - (elemTop + elemHeight);

	if((elemLeft + elemWidth) >= parentWidth)
	{
		elem.style.left = parentWidth - elemWidth + 'px';
	}
	if((elemRight + elemWidth) >= parentWidth)
	{
		elem.style.left = '0px';
	}
	if( (elemTop + elemHeight) >= parentHeight)
	{
		elem.style.top = parentHeight - elemHeight + 'px';
	}
	if( (elemBottom + elemHeight) >= parentHeight)
	{
		elem.style.top = '0px';
	}
}

function bindToParentAction(child, parentAction)
{
	if(child.hasChildNodes)
	{
		for(var i = 0; i < child.childNodes.length; i++)
		{
			bindToParentAction(child.childNodes[i], parentAction);
		}
	}
	
	if(typeof parentAction === 'object')
	{
		for(var x in parentAction)
		{
			if(typeof parentAction[x] === 'function')
			{
				child[x] = parentAction[x];
			}
		}
	}
	
	else
	{
		parentAction.call(child);
	}
}

function pageName()
{
	var fullPageUrl     = document.location.href;
	var indxOfPath      = fullPageUrl.lastIndexOf('/');
	var loosePageName   = fullPageUrl.substring(indxOfPath + 1, fullPageUrl.length);
	var queryStringIndx = loosePageName.indexOf('?');
	var strictPageName  = loosePageName.substring(loosePageName, queryStringIndx);
	return strictPageName;
}

/*
* original code source: http://www.quirksmode.org/js/findpos.html 
* access date: July 9, 2011, 10:42 am;
*/
function getElemPos(obj)
{
	var curleft = curtop = 0;
	
	if (obj.offsetParent)
	{
		do 
		{
			curleft += obj.offsetLeft;
			curtop  += obj.offsetTop;
		} while (obj = obj.offsetParent);
		
		return {'left': curleft, 'top': curtop};
	}
}

//FROM THE JAVASCRIPT ANTHOLOGY
function getElemPosition(theElement)
{
	var positionLeft = 0;
	var positionTop = 0;
	
	while (theElement != null)
	{
		positionLeft += theElement.offsetLeft;
		positionTop  += theElement.offsetTop;
		theElement    = theElement.offsetParent;
	}
	
	return {'left': positionLeft, 'top': positionTop};
}

function setElemSize(elem, w, h)
{
	elem = $O(elem);
	$Style(elem).width  = (String(w).replace('px', '')) + 'px';
	$Style(elem).height = (String(h).replace('px', '')) + 'px';
}

/*
* Create a new <option> element for a <select> element
*/
function createMenuItem (value, label)
{
	var newOpt       = document.createElement("option");
	newOpt.value     = value;
	newOpt.innerHTML = label || value;
	return newOpt;
}

/*
* @author: michael orji
* @date: dec 26, 2011
*/
function createImage(obj)
{
	obj           = obj || {}

	var img       = document.createElement('img');
	img.src       = obj.src;
	img.id        = obj.id          || new Date().getSeconds()
	img.className = obj.className   || '';
	img.alt       = obj.alt         || '';
	img.title     = obj.title       || '';
 
	return img;
}

/*
* returns the scrollTop property of a document as rendered
* by any particular browser
*/
function scrollTop()
{
	var scrollTop = (document.body.scrollTop) ? document.body.scrollTop : document.documentElement.scrollTop;
	return scrollTop;
}

/*
* returns the scrollLeft property of a document as rendered
* by any particular browser
*/
function scrollLeft()
{
	var scrollLeft = (document.body.scrollLeft) ? document.body.scrollLeft : document.documentElement.scrollLeft;
	return scrollLeft;
}   

/*
* checks if the scrollbar of a scrollable element is down
* returns true if the scrollbar is down, false otherwise
* @author: michael orji
* @date oct 5, 2010, 03:16
*/
function scrollBarIsDown(elem)
{
	elem = $O(elem);
	
	if(elem)
	{
		return (elem.scrollHeight - elem.scrollTop <= elem.offsetHeight);
	}
}

function scrollToBottom(elem)
{
	elem = $O(elem);
	elem.scrollTop = elem.scrollHeight - elem.offsetHeight;
}