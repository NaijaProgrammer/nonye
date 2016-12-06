/*
* @author: michael orji
* @date: 28 Feb, 2012
*
* Dependencies : dom,
*/
var css = StyleManager;

var StyleManager = 
{   
	setStyle : function(elem, styleOptions)
	{
		elem = $O(elem);
		styleOptions = styleOptions || {}; 
		
		for(var j in styleOptions)
		{
			elem.style[j] = styleOptions[j];
		}
	}, //end setStyle

	styleElementsOfClass : function(cssClassName, styleObject)
	{
		styleObject = styleObject || {};
		
		var ccn = DOMManager.getElementsOfClass(cssClassName);
		
		for(var x in ccn)
		{
			this.setStyle(ccn[x], styleObject);
		}
	}, //end styleElementsOfClass

	hideElement : function(elemId)
	{
		elem = $O(elemId);
		elem.style.visibility = 'hidden';   
	}, //end hideElement

	showElement : function(elem, elemStyle)
	{
		elem = $O(elem);
		elem.style.visibility = 'visible';
	}, //end showElement

	setElementDisplay : function(elemId, displayVal)
	{
		elem = $O(elemId);
		elem.style.display = displayVal;
	}, //end setElementDisplay

	hideElements : function(elemsCollection)
	{
		for(var i in elemsCollection)
		{
			this.hideElement(elemsCollection[i]);
		}  
	}, //end hideElements

	showElements : function(elems)
	{
		for(var i = 0; i < elems.length; i++)
		{
			this.showElement(elems[i]);
		}  
	}, //end showElements

	showElementsOfClass : function(cssClass, displayStyle)
	{
		var ElementsToShow = DOMManager.getElementsOfClass(cssClass);
		var len = ElementsToShow.length;
		
		for(var i = 0; i < len; i++)
		{
			this.showElement(ElementsToShow[i], displayStyle);
		}
	},//end showElementsOfClass

	hideElementsOfClass : function(htmlTag, cssClass)
	{
		var candidateElements = document.getElementsByTagName(htmlTag);
		var len = candidateElements.length;
		
		for(var i = 0; i < len; i++)
		{
			if(candidateElements[i].className == cssClass)
			{
				this.hideElement(candidateElements[i]);
			}
		}
	}, //end hideElementsOfClass

	hideElementsOfClass : function(cssClass, useDisplay)
	{
		var ElementsToHide = DOMManager.getElementsOfClass(cssClass);
		var len = ElementsToHide.length;
		
		for(var i = 0; i < len; i++)
		{
			this.hideElement(ElementsToHide[i], useDisplay);
		}
	}//end hideElementsOfClass
}//end StyleManager