/*
* Dependencies: StyleManager (for destroyElement)
*/
var dom = DOMManager;

var DOMManager = {

	getElementsOfClass : function(cssClassName, strict, parentElem)
	{
		return this.getElementsByClassName(cssClassName, parentElem, strict);
	},

	getElementsByClassName : function(cssClassName, parentElem, strict)
	{
		var arr = [];
		var d   = $O(parentElem) || document;
    
		var allElements = d.getElementsByTagName('*');
		var len = allElements.length;
		
		for(var i = 0; i < len; i++)
		{
			if(strict)
			{
				if(allElements[i].className == cssClassName)
				{
					arr.push(allElements[i]);
				}
			}
			else
			{
				if( (allElements[i].className.indexOf(cssClassName) != -1) || (allElements[i].className == cssClassName) )
				{
					arr.push(allElements[i]);
				}
			}
		}
		
		return arr;
	}, //end getElementsByClassName
   
	/*
	* @credits: Accelerated DOM Scripting with Ajax, APIs and Libraries, chap 2: getElementsByClassName function
	* gets elements of a particular class in a given node, 
	* useful when you wish to get elements of a given class within just a particular node rather than within the entire document
	*/
	getElementsByClassNameInNode : function(node, classname)
	{
		var a   = [];
		var re  = new RegExp('(^| )'+classname+'( |$)');
		var els = node.getElementsByTagName("*");
		
		for( var i = 0, j= els.length; i < j; i++ )
		{
			if(re.test(els[i].className))
			{
				a.push(els[i]);
			}
		}
		
		return a;
   },

	/*
	* @author: michael orji
	* @date: 25 oct, 2010 16:41:26
	*/
	removeFromParentNode : function(nodeIDToRemove)
	{
		var nodeToRemove = $O(nodeIDToRemove); 
		if( (typeof nodeToRemove != 'undefined') )
		{
			var PN = nodeToRemove.parentNode || document.body;
			PN.removeChild(nodeToRemove);
			return true;
		}
	}, 

	/*
	* @author: michael orji
	* @date: 25 April, 2012
	*/
	removeElementsFromParentNode : function(classOfElementsToRemove)
	{
		var nodesToRemove = this.getElementsOfClass(classOfElementsToRemove);
		var nodesLen = nodesToRemove.length;
		
		for(var i = 0; i < nodesLen; i++)
		{
			var nodeToRemove = nodesToRemove[i]; 
			
			if( (typeof nodeToRemove != 'undefined') )
			{
				nodeToRemove.parentNode.removeChild(nodeToRemove); //this.removeFromParentNode(nodeToRemove);
			}
		}
	}, 

	/*
	* @author: michael orji
	* @date: 25 oct, 2010 16:41:26
	*/
	removeFromArrayAndParentNode : function(idOfArrayElementToRemove, nodeIDToRemove, arr)
	{
		if(this.removeFromParentNode(nodeIDToRemove))
		{
			removeFromArray(idOfArrayElementToRemove, arr);
		}
	}, 

	/*
	* traverses a parent node looking for a child node
	* identified by its css style name and value
	* especially for a node without an ID or a class //added this line to the documentation on Nov. 21, 2015
	* returns: the child node if found, else false
	*
	* @date: 05, sept, 2010
	*
	* CAN STILL DO WITH SOME IMPROVEMENT
	*/
	findNode : function(parentNode, targetNodeStyleName, targetNodeStyleValue)
	{
		var children = parentNode['childNodes'];
		
		for(var i in children)
		{  
			if(children[i][targetNodeStyleName] == targetNodeStyleValue)
			{
				return children[i]; 
			}
		}
		
		return false;
	},

	/*
	* @author: michael orji
	* @date: 8 Nov, 2010 18:45
	*/
	destroyElement : function(elem)
	{
    
		var elem = $O(elem);
		
		if(typeof elem.parentNode !== 'undefined')
		{
			this.removeFromParentNode(elem);
		}
		
		else
		{
			StyleManager.hideElement(elem);
			document.body.removeChild(elem);
		}
	}
}//end of DOMManager