var MO = MO || {};
MO.namespace = function(name)
{
	var parts   = name.split('.');
	var current = MO;
	for (var i in parts)
	{
		if (!current[parts[i]])
		{
			current[parts[i]] = {};
		}
		current = current[parts[i]];
	}
}

/*
* @credits: Wrox professional javascript for web developers, pg 197
*/
if (typeof Node == "undefined") 
{
	var Node = {
		ELEMENT_NODE                : 1,
		ATTRIBUTE_NODE              : 2,
		TEXT_NODE                   : 3,
		CDATA_SECTION_NODE          : 4,
		ENTITY_REFERENCE_NODE       : 5,
		ENTITY_NODE                 : 6,
		PROCESSING_INSTRUCTION_NODE : 7,
		COMMENT_NODE                : 8,
		DOCUMENT_NODE               : 9,
		DOCUMENT_TYPE_NODE          : 10,
		DOCUMENT_FRAGMENT_NODE      : 11,
		NOTATION_NODE               : 12
	}
}


/*
* @Credits: http://www.javascriptkit.com/javatutors/loadjavascriptcss.shtml
* @date; July 13, 2011; 14:24
* @modified by: Michael Orji
*/
var filesadded="" //list of files already added

/*
* Determines if file already exists in the page
*/
function fileExists(filename, filetype)
{
	if (filesadded.indexOf( "[" + filename + "@" + filetype + "]" ) == -1)
	{
		filesadded += "[" + filename + "@" + filetype + "]," //List of files added in the form "[filename1@js],[filename2@css],etc" I -- (Michael O.) -- added the @ symbol and the comma(,) incase a situation arises where I need to split() the string into an array
		return false;
	}
	return true;
}

/*
* removes a file from the page
* Arguments:
*   String name of file
*   String type of file
* @credits: http://www.javascriptkit.com/javatutors/loadjavascriptcss2.shtml
* @date: Feb 28, 2012
* @modified by: Michael Orji
*/
function removeFile(filename, filetype){
 var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist from
 var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
 var allsuspects=document.getElementsByTagName(targetelement)
   for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
      if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1){
       allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
      }
   }
}

/*
* Dynamically loads a server-side (php) script into the current page, script must return valid javascript 
* Arguments: 
*    Array files to add, 
*    boolean specifying whether to overwrite the file if it already exists in the page
* Not to be called directly, as it is used by the $_JS function internally
* @date: March 13, 2012
*/
function $PHP_JS(JSPHPFilesArray, overWrite){

   var str = ''; 
 
   for(var i in JSPHPFilesArray){ 
      
      if(overWrite){
         if(fileExists(JSPHPFilesArray[i], "php") ){ 
          removeFile(JSPHPFilesArray[i], "php");
         }
         if(JSPHPFilesArray[i].indexOf('.php') == -1){
            JSFilesArray[i] += '.php';
          } 
      }

      if( !fileExists(JSPHPFilesArray[i], "php") ){ //call the fileExists function to include the current 'js' file in the 'filesadded' string
          if(JSPHPFilesArray[i].indexOf('.php') == -1){
            JSPHPFilesArray[i] += '.php';
          } 
      } 

     var JSLink = document.createElement("script");
     JSLink.setAttribute("type", "text/javascript");
     JSLink.setAttribute("src", JSPHPFilesArray[i]);

      if(typeof JSLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(JSLink);
      } 
   }
}

/*
* Dynamically loads a javascript file into the current page,
* Arguments: 
*   array  files to add, 
*   boolean specifying whether to overwrite the file if it already exists in the page, 
*   boolean specifying if the file is a serverside (php) script
* @author: michael orji
* @date: Oct 2, 2010
* @modified: Feb 28, 2012
*/
function $JS(JSFilesArray, overWrite, isPHPFile){

   if(isPHPFile){
    $PHP(JSFilesArray, overWrite);
    return;
   }

 var str = ''; 
 
   for(var i in JSFilesArray){ 
      
      /*
      * if the file already exists (in the 'filesadded' string) and we're overwriting the file in the document tree, 
      * we pull it out from the document tree,  
      * then re-insert it into the tree, this way, we remain consistent in the 'filesadded' string since we don't alter (i.e remove it from)
      * the string when we remove the file from the document tree,
      */
      if(overWrite){
         if(fileExists(JSFilesArray[i], "js") ){ 
          removeFile(JSFilesArray[i], "js");
         }
         if(JSFilesArray[i].indexOf('.js') == -1){
            JSFilesArray[i] += '.js';
          } 
      }

      if( !fileExists(JSFilesArray[i], "js") ){ //call the fileExists function to include the current 'js' file in the 'filesadded' string
          if(JSFilesArray[i].indexOf('.js') == -1){
            JSFilesArray[i] += '.js';
          } 
      } 

     var JSLink = document.createElement("script");
     JSLink.setAttribute("type", "text/javascript");
     JSLink.setAttribute("src", JSFilesArray[i]);

      if(typeof JSLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(JSLink);
      } 
   }
}


/*
* dynamically loads css stylesheets
* @argument - array of css files to load
* @author: michael orji
* @date: sept 29, 2010
* based on ajax_im's ajax_im.js
*/
function $CSS(cssFilesArray){
   
   for(var i in cssFilesArray){
      if( !fileExists(cssFilesArray[i], "css") ){
       var CSSLink = document.createElement("link");
       CSSLink.setAttribute("rel", "stylesheet");
       CSSLink.setAttribute("type", "text/css");

         if(cssFilesArray[i].indexOf('.css') == -1){
          cssFilesArray[i] += '.css';
         }
   
       CSSLink.setAttribute("href", cssFilesArray[i]);
     
         if(typeof CSSLink != "undefined"){
          document.getElementsByTagName("head")[0].appendChild(CSSLink);
         }
      }
   }
}

function setStatus(elem, statusIndicator)
{
	$Html(elem, statusIndicator);
}

function clearStatus(elem)
{
	$Html(elem, '');
}

/*
* @ author: Michael Orji
* @ date: Sept 20, 2012
*/
function setDisplayStringLength(str, maxLength, padXter)
{
	padXter = padXter || '...'
	return ( str.substring(0, maxLength) + (str.length > maxLength ?  padXter : '') );
}

function getLastChild(parent, excludeNodeType)
{
	excludeNodeType = excludeNodeType || 3;
	var lastBorn = parent.lastChild;

	while(lastBorn.nodeType == excludeNodeType)
	{
		lastBorn = lastBorn.previousSibling;
	}
	
	return lastBorn;
}

//Opening Off-site Links in a New Window
/*
USAGE : 
all external links should have the rel="external" attribute
1. SITE: <a href="http://www.google.com/" rel="external">Google (offsite)</a>
2. document.onclick = openExtLinks;
*/
openExtLinks = function(e)
{
	var target = e ? e.target : window.event.srcElement;

	while (target && !/^(a|body)$/i.test(target.nodeName))
	{
		target = target.parentNode;
	}

	if (target && target.getAttribute('rel') && target.rel == 'external')
	{
		var external = window.open(target.href);
		return external.closed;
	}
}

function changeLocation(url)
{
	location.href = url;
}

function removeBgImg(elem)
{
	elem = $O(elem);
	elem.style.backgroundImage = 'none';
}

function addBgImg(elem, imgName)
{
	elem = $O(elem);
	elem.style.backgroundImage = "url(" + imgName + ")";
}

function toggleBgImg(elem, imgName)
{
	elem = $O(elem);

	if(elem.value.length > 0)
	{
		removeBgImg(elem)   
	}

	else
	{
		addBgImg(elem, imgName);
	}
}

/* 
 rename this to changeBgColor ||
 merge it with add/removeBgImg to
 create a general-purpose changeBgElement script
*/ 
changeBg = function(oldElem, newElem)
{
	oldElem = $O(oldElem);
	newElem = $O(newElem);
	oldElem.style.backgroundColor = newElem.style.backgroundColor;
}

function changeBgColor(elem, color)
{
	elem = $O(elem);
	elem.style.backgroundColor = color;
}

function changeImageSrc(oldImg, newImg, newImgPath)
{
	oldImg = $O(oldImg);
	var newImgSrc = newImgPath + newImg;
	oldImg.src = newImgSrc;
}

swapContent = function(oldElem, newElem)
{
	oldElem = $O(oldElem);
	newElem = $O(newElem);
	if(oldElem && newElem)
	{
		oldElem.innerHTML = newElem.innerHTML;
	}
}

function isDisplayed(elem)
{
	elem = $O(elem);
	return (elem.style.display != 'none');
}

function toggleElementVisibility(elem)
{
	elem = $O(elem);
	
	if(!isVisible(elem))
	{
		StyleManager.showElement(elem);
	}
	
	else
	{
		StyleManager.hideElement(elem);
	}
}

function setWindowStatusMsg(msg)
{
	window.status = msg;
	return true;
}


/*
* @credits: Accelerated DOM Scripting with Ajax, API's and Libraries by  Jonathan Snook (pg 72, PDF version)
* @modified: Michael Orji
*/
function changeLinksToNewWindow()
{
	/* 
	* grab the url and match up to the first "/" after the "http://"
	* grab the first (and only) match
	*/
	var currentDomain = window.location.href.match(/^http:\/\/[^\/]+/)[0];
	var elements      = document.getElementsByTagName('a');
	for(var i=0;i<elements.length;i++)
	{
		// if the currentDomain is in the href, it'll return a value of 0 or more.
		if(elements[i].href.lastIndexOf(currentDomain) >= 0)
		{
			addListener(elements[i], 'click', openWin);
		}
	}

	function openWin(evt)
	{
		evt = evt||window.event;
		window.open(this.href);
		if(evt.preventDefault)
		{
			evt.preventDefault();
		}
		else
		{
			evt.returnValue=false;
		}
	}
}