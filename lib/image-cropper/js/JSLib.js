var MO = MO || {};
MO.namespace = function(name){
	var parts   = name.split('.');
	var current = MO;
	for (var i in parts) {
		if (!current[parts[i]]) {
			current[parts[i]] = {};
		}
		current = current[parts[i]];
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
function fileExists(filename, filetype){
   if (filesadded.indexOf( "[" + filename + "@" + filetype + "]" ) == -1){
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

/*
* Wrapper function for document.getElementsByTagName
* Argument: HTML element tag name
*/
function $Tag(tagName){
 var that = (this == window) ? document : this;
 return ( (tagName) ? that.getElementsByTagName(tagName) : that.getElementsByTagName('*') );
 }


/*
* Wrapper function for document.getElementById
* Argument: HTML element id or DOM object reference
*/
function $O(elem){
 var obj = null;
   if(isObject(elem)){
    obj = elem;
   }
   else if($ID(elem)){
    obj = $ID(elem);
   }
   else{
    obj =  $ID(String(elem));
   }

   function $ID(elemId){
     return document.getElementById(elemId);
     //return typeof elemId=='string'?document.getElementById(elemId):elemId;
   }
   
   return obj;
}

/*
* returns the style property of supplied element
* Argument: HTML element id or reference
*/
function $Style(elem){return $O(elem).style;}

/* 
* gets or sets the innerHTML value of element
* Argument[s]
*    HTML element id or reference
*    String value : optional 
* if the optional value is supplied, the element's inner HTML value is set to the supplied value and then returned
* otherwise, the current innerHTML value is returned
*/
function $Html(id, value){

 if (typeof value != 'undefined'){
  $O(id).innerHTML = value
 }
 return $O(id).innerHTML
}

function append(id, value){

	if ( typeof value != 'undefined' ){
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
function $textContent(elem, val){ 
 elem = $O(elem);
 var tc = '';

   if(typeof elem.textContent !== 'undefined'){
      if(typeof val === 'string'){
       elem.textContent = val;
      }
    tc = elem.textContent;
   }
   else{
      if(typeof val === 'string'){
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
function $Class(cssClassName, strict){
 var arr = [];
 var that = (this == window) ? document : this;
 var candidates = that.getElementsByTagName('*');
 var len = candidates.length;
   for(var i = 0; i < len; i++){
      if(strict){
         if(candidates[i].className == cssClassName){
          arr.push(candidates[i]);
         }
      }
      else{
         if( (candidates[i].className.indexOf(cssClassName) != -1) || (candidates[i].className == cssClassName) ){
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
*/
function $properties(element){
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
*/
function $Center(elemId){

 var elem = $O(elemId);

 var elemDimensions = size(elem);
 var elemWidth      =  parseInt(elemDimensions.width)  || parseInt(getStyleValue(elem, 'width')); 
 var elemHeight     =  parseInt(elemDimensions.height) || parseInt(getStyleValue(elem, 'height'));
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

/* 
*removes leading and trailing spaces from string 
*/
function trim(s){return ( (typeof s == 'string') ? s.replace(/(^\s+)|(\s+$)/g, "") : s );}

/* 
* remove leading and trailing spaces from string
* @credits: by joshua gross (popup.html of ajax_im )
*/
function trim2(text){return (text != null) ? text.replace(/^[ \t]+|[ \t]+$/g, "") : null;}

/*
* @credits: Stoyan Stefanov "Object Oriented Javascript"
*/
function extendByValue(Child, Parent) {
 var p = Parent.prototype;
 var c = Child.prototype;
   for (var i in p) {
    c[i] = p[i];
   }
 c.uber = p;
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
				customExtend(Parent[i], Child[i]);
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
* @date: Oct. 10, 2012, 20:37
*/
function logToConsole(msg){
   if(typeof console != 'undefined' && typeof console.log == 'function'){
    return console.log(msg);
   }
}

/*
* Splits a string on the first occurence of multiple substrings
* @credits: http://stackoverflow.com/questions/7527374/how-to-split-a-string-on-the-first-occurence-of-multiple-substrings-in-j
* @date: 27 Oct, 2012
* idea is, replace the first occurence with a special (invisible) string, and then split against this string
*/
function splitOnFirstOccurenceOfMultipleSubstrings(strToSplit, substr, invisibleXter){
 invisibleXter = invisibleXter || "\x034";
 return strToSplit.replace(new RegExp(substr), invisibleXter).split(invisibleXter); 
}

function getLastChar(str){
 return trim(str.substring(str.length - 1));
}

/*
* Gets the absolute path to specified file or folder 
* @author: Michael Orji
* @date: Nov. 11, 2012
*/
function getPath(targetName){
 
 var scripts     = $Tag('script');
 var targetPath  = '';
   for(i = 0; i < scripts.length; i++){
    var scriptPath           = scripts[i].src.replace(/%20/g, ' ');  
    var targetPathStartIndex = scriptPath.lastIndexOf('/' + targetName) + 1; //
    var scriptName           = scriptPath.substring(targetPathStartIndex, targetPathStartIndex + targetName.lastIndexOf(getLastChar(targetName)) + 1 );
      if(trim(scriptName) == trim(targetName)){ 
       targetPath = trim( scriptPath.substring(0, targetPathStartIndex) );
       return targetPath + '/';
      }
   }
}

/*
* credit: http://james.padolsey.com/javascript/get-document-height-cross-browser/
*/
function docHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function size(elem){
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

function getDisplayType(element){
 element = $O(element);
 if(!element){ return; }
 return (element.style.visibility) ? 'visibility' : 'display';
}

function isObject(elem, strict){
   if(strict)
    return (typeof elem === 'object');
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
function resolveScope(callback, x, allowDefaultAction, allowEventPropagation){

 var func = callback; 

   return function(event){

       if(typeof x === 'array' || typeof x === 'object'){ 
        arrayWalk(x, func);
       }

       else{
        func(x); 
       }
    
    handleEvent(event, allowDefaultAction, allowEventPropagation);
   }
}

function execute(){

 var callback = arguments[0], args = [];

   for(var i = 1; i < arguments.length; i++){
    args.push(arguments[i]); 
   }
   
   return function(){
    callback.apply(callback, args);  
   }
}

/* @author: michael Orji
*  @date  : 15 sept, 2012
*/
function exec(callback){
 var args = [];
   for(var i = 1; i < arguments.length; i++){
    args.push(arguments[i]);
   }
   return function(){
    return callback(args);
   }
}

function delay(callback, delayTime){
 var t = setTimeout(callback, delayTime);
 return t;
}

function hide(){
   if(arguments.length > 0){
      for(var i = 0; i < arguments.length; i++){
       $Style(arguments[i]).visibility = 'hidden';
      }
   }
}
function show(){
 var len = arguments.length;
   if(arguments.length > 0){
      for(var i = 0; i < arguments.length; i++){
       $Style(arguments[i]).visibility = 'visible';
      }
   }
}

function display(elems, displayVal){
 elems = (typeof elems == 'string') ? elems.split(elems, ',') : elems;
 displayVal = displayVal || 'block';

 if(elems.length > 0){
      for(var i = 0; i < elems.length; i++){
         if($O(trim(elems[i]))){
          $Style(trim(elems[i])).display = displayVal;
         }
      }
   }
}

function undisplay(){
 if(arguments.length > 0){
      for(var i = 0; i < arguments.length; i++){
         if($O(arguments[i])){
          $Style(arguments[i]).display = 'none';
         }
      }
   }
}

function toggleDisplay(elem, currDisplay){
 if( !(elem = $O(elem)) ){ return; }
 currDisplay = currDisplay || $Style(elem).display || 'none';
 $Style(elem).display =  ( ($Style(elem).display  != 'none') ? 'none' : 'block' );
 return elem;
}

function setStatus(elem, statusIndicator){
 $Html(elem, statusIndicator);
}

function clearStatus(elem){
 $Html(elem, '');
}

function resetForm(theform){
 $O(theform).reset();
}

function submitForm(theform){
 theform.submit();
}

function isVisible(elem){
 
 return ( ($Style(elem).visibility != 'hidden') && ($Style(elem).display != 'none') );
}

$Opacity = function(elemId, opacityLevel){
 var elem = $O(elemId);
   if(opacityLevel > .99) opacityLevel = .99;
   if(opacityLevel < 0) opacityLevel = 0;

   if(typeof elem.style.opacity != 'undefined') elem.style.opacity = opacityLevel;
   else if(typeof elem.style.MozOpacity != 'undefined') elem.style.MozOpacity = opacityLevel;
   else if(typeof elem.style.KhtmlOpacity != 'undefined') elem.style.KhtmlOpacity = opacityLevel;
   else elem.style.filter = "alpha(opacity=" + opacityLevel * 100 + ")";
}

fadeIn = function (elemId, maxOpacity, speed){
 Effects.fadeIn(elemId, maxOpacity, speed);
}

fadeOut = function (elemId, minOpacity, speed){
 Effects.fadeOut(elemId, minOpacity, speed)
}

function bindToParentAction(child, parentAction){
   if(child.hasChildNodes){
      for(var i = 0; i < child.childNodes.length; i++){
       bindToParentAction(child.childNodes[i], parentAction);
      }
   }
   if(typeof parentAction === 'object'){
      for(var x in parentAction){
         if(typeof parentAction[x] === 'function'){
          child[x] = parentAction[x];
         }
      }
   }
   else{
    parentAction.call(child);
   }
}

function pageName(){
 var fullPageUrl     = document.location.href;
 var indxOfPath      = fullPageUrl.lastIndexOf('/');
 var loosePageName   = fullPageUrl.substring(indxOfPath + 1, fullPageUrl.length);
 var queryStringIndx = loosePageName.indexOf('?');
 var strictPageName  = loosePageName.substring(loosePageName, queryStringIndx);
 return strictPageName;
}

/*
* @ author: Michael Orji
* @ date: Sept 20, 2012
*/
function setDisplayStringLength(str, maxLength, padXter){
 padXter = padXter || '...'
 return ( str.substring(0, maxLength) + (str.length > maxLength ?  padXter : '') );
}

function getLastChild(parent, excludeNodeType){
 excludeNodeType = excludeNodeType || 3;
 var lastBorn = parent.lastChild;

   while(lastBorn.nodeType == excludeNodeType){
    lastBorn = lastBorn.previousSibling;
   }
 return lastBorn;
}

function changeLocation(url){
 location.href = url;
}

function createMenuItem (value, label) {
 var newOpt       = document.createElement("option");
 newOpt.value     = value;
 newOpt.innerHTML = label || value;
 return newOpt;
}

/*************************************************
* MANAGERS
**************************************************/
var Browser = {

   UA: {

      Name: function(){
       
         if (typeof navigator.vendor != "undefined" && navigator.vendor == "KDE"){
          return 'kde';
         }
         else if (typeof window.opera != "undefined"){
          return 'opera';
         }
         else if(typeof document.all != "undefined"){
          return 'IE';
         }
         else if (typeof document.getElementById != "undefined"){
            if (navigator.vendor.indexOf("Apple Computer, Inc.") != -1){
             return 'safari';
            }
          return 'mozilla';
         }
      },

      Version: function(){
       var agent = this.UA.Name().toLowerCase();
       //var agent = Name().toLowerCase();

         if(agent == 'kde'&& typeof window.sidebar != "undefined"){
          return 'kde 3.2+';
         }
         else if(agent == 'opera'){
          agent = navigator.userAgent.toLowerCase();
          var version = parseFloat(agent.replace(/.*opera[\/ ]([^ $]+).*/, "$1"));
            if(version >= 7){
             return "opera7+";
            }
            else if (version >= 5){
             return "opera5+6";
            }
          return false;
         }
         else if(agent == 'ie'){
          agent = navigator.userAgent.toLowerCase();
            if(typeof document.getElementById != "undefined"){
             var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, "$1").replace(/ /, "");
               if (typeof document.uniqueID != "undefined"){
                  if (browser.indexOf("5.5") != -1){
                   return browser.replace(/(.*5\.5).*/, "$1");
                  }
                  else{
                   return browser.replace(/(.*)\..*/, "$1");
                  }
               }
               else{
                return "ie5mac";
               }
            }
          return false;
         }
         else if(agent == 'safari'){
            if(typeof window.XMLHttpRequest != "undefined"){
             return "safari1.2";
            }
          return "safari1";
         }
         else if(agent == 'mozilla'){
          return navigator.userAgent;
         }
      },

      OS: function(){
       var agent = navigator.userAgent.toLowerCase();
         if(agent.indexOf("win") != -1){
          return "win";
         }
         else if(agent.indexOf("mac") != -1){
          return "mac";
         }
         else{
          return "unix";
         }
      }
   }, //end of Browser.UA

   setCookie: function(name, value, daysTillExpire, path, domain, secure){
    
     var cookieName = trim(name);
     var cookieValue = trim(value);
     var theCookie = cookieName + "=" + cookieValue;
   
      if(daysTillExpire){
       var d = new Date();
       var expires = d.setTime(d.getTime() + (daysTillExpire*24*60*60*1000)).toGMTString();
       theCookie += "; expires=" + expires;
      }  
      if(path){theCookie += "; path=" + path;}
      else{theCookie += "; path=/";}
      if(domain){theCookie +=  "; domain=" + domain;}
      if(secure){theCookie += "; secure";}
     document.cookie = theCookie;
   },

   getCookie: function(searchName){
    var cookies = document.cookie.split(";");
       for (var i = 0; i < cookies.length; i++)
      {
        var cookieCrumbs = cookies[i].split("=");
        var cookieName = cookieCrumbs[0]; 
        var cookieValue = cookieCrumbs[1];
     
          if (trim(cookieName) == trim(searchName))
         { 
           return trim(cookieValue);
         }
      }
   return null;
   },

   setStatusBarMsg: function(msg)
   {
    window.status = msg;
    return true;
   }, //usage e.g: <a href="" onmouseover="func(); return Browser.setStatusBarMsg('hi')"> </a>

   Window : {

      popup : function(winId, url, options){
       var thisSize = this.size();
       var defaultOptions = {'left' : thisSize.width/4, 'top' : thisSize.height/4}
       var windowProps    = '';
       for( var x in options ){ defaultOptions[x] = options[x]; }
       for(var y in defaultOptions){ windowProps += y +'=' + defaultOptions[y] + ','; }
       return window.open(url, winId,  windowProps);
      },

      //needs no modification: created by me
      size: function(){
       var w, h;

         if(typeof window.innerWidth != 'undefined')
         {
          w = window.innerWidth; //other browsers
          h = window.innerHeight;
         } 
         else if(typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) 
         {
          w =  document.documentElement.clientWidth; //IE
          h = document.documentElement.clientHeight;
         }
         else{
          w = document.body.clientWidth; //IE
          h = document.body.clientHeight;
         }
       return {'width':w, 'height': h};
      },//end of Browser.Window.Size

      scrollBarIsDown : function(){
       var visibleContentHeight = this.size().height;
       var maxScrollTop = docHeight() - visibleContentHeight;
       return this.getScrollPosition().top == maxScrollTop;
      }, 

      getScrollPosition: function (){
       var Scroll = {'left':0, 'top':0};
         if(typeof window.pageYOffset != 'undefined'){
          Scroll = {'left':window.pageXOffset, 'top':window.pageYOffset};
         }
         else if(typeof document.documentElement.scrollTop != 'undefined' && 
         (document.documentElement.scrollTop > 0 || document.documentElement.scrollLeft > 0)) {
          Scroll = {'left':document.documentElement.scrollLeft, 'top':document.documentElement.scrollTop};
         }
         else if (typeof document.body.scrollTop != 'undefined'){
          Scroll = {'left':document.body.scrollLeft, 'top':document.body.scrollTop};
         }
       return Scroll;
      } //end Browser.getScrollPosition, this is complete
   }//end Window

} //end of Browser

var DOMManager = {

   getElementsOfClass : function(cssClassName, parentElem, strict){
    return this.getElementsByClassName(cssClassName, parentElem, strict);
   },

   getElementsByClassName : function(cssClassName, parentElem, strict){
    var arr = [];
    var d   = $O(parentElem) || document;
    
    //var allElements = document.getElementsByTagName('*');
    var allElements = d.getElementsByTagName('*');
    var len = allElements.length;
      for(var i = 0; i < len; i++){
         if(strict){
            if(allElements[i].className == cssClassName){
             arr.push(allElements[i]);
            }
         }
         else{
            if( (allElements[i].className.indexOf(cssClassName) != -1) || (allElements[i].className == cssClassName) ){
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
		node = $O(node);
		var a = [];
		var re = new RegExp('(^| )'+classname+'( |$)');
		var els = node.getElementsByTagName("*");
		for(var i=0,j=els.length; i<j; i++)
			if(re.test(els[i].className))a.push(els[i]);
		return a;
   },

   /*
   * @author: michael orji
   * @date: 25 oct, 2010 16:41:26
   */
   removeFromParentNode : function(nodeIDToRemove){
     var nodeToRemove = $O(nodeIDToRemove); 
      if( (typeof nodeToRemove != 'undefined') ){
       var PN = nodeToRemove.parentNode || document.body;
       PN.removeChild(nodeToRemove);
       return true;
      }
   }, 

   /*
   * @author: michael orji
   * @date: 25 April, 2012
   */
   removeElementsFromParentNode : function(classOfElementsToRemove){
     var nodesToRemove = this.getElementsOfClass(classOfElementsToRemove);
     var nodesLen = nodesToRemove.length;
      for(var i = 0; i < nodesLen; i++){
       var nodeToRemove = nodesToRemove[i]; 
         if( (typeof nodeToRemove != 'undefined') ){
          nodeToRemove.parentNode.removeChild(nodeToRemove);
          //this.removeFromParentNode(nodeToRemove);
         }
      }
   }, 

   /*
   * @author: michael orji
   * @date: 25 oct, 2010 16:41:26
   */
   removeFromArrayAndParentNode : function(idOfArrayElementToRemove, nodeIDToRemove, arr){
      if(this.removeFromParentNode(nodeIDToRemove)){
       removeFromArray(idOfArrayElementToRemove, arr);
      }
   }, 


   /*
   * traverses a parent node looking for a child node
   * identified by its css style name and value;
   * returns: the child node if found, else false
   *
   * @date: 05, sept, 2010
   *
   * CAN STILL DO WITH SOME IMPROVEMENT
   */
   findNode : function(parentNode, targetNodeStyleName, targetNodeStyleValue){
    var children = parentNode['childNodes'];
      for(var i in children){  
         if(children[i][targetNodeStyleName] == targetNodeStyleValue){
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
      if(typeof elem.parentNode !== 'undefined'){
       this.removeFromParentNode(elem);
      }
      else{
       StyleManager.hideElement(elem);
       document.body.removeChild(elem);
      }
   }
}//end of DOMManager

/* 
* @author: Michael Orji
* @date: March 25, 2012
*/
var errorManager = {

   logError : function(msg, opts){
      if(typeof console != 'undefined' && typeof console.log == 'function'){
       return console.log(msg);
      }
      else{
       this.displayError(opts);
      }
   },

   displayError : function(opts){

    opts = opts || {};
    var errorObj  = opts.errorObject || new Object();
    var divElem   = $O(opts.errorMessageContainer);
    var overWrite = opts.overWrite || true;
    var er;
      
      if(!divElem){
       divElem = document.createElement('div');
       $Tag('body')[0].appendChild(divElem);
      }
      for(var i in errorObj){
       er = i + ' : ' + errorObj[i];
         if(overWrite){
          divElem.innerHTML = '';
         }
       divElem.innerHTML += er + "\n";
      }
   }
} //end of errorManager

/*
* dynamically creates a new window
*
* @author: michael orji
* @encoding: utf-8
*
* functions are called on /accessed via the FloatingWindow Object;
* properties are accessed via the FloatingWindow Object.element property
* e.g: win = new FloatingWindow(configObj);
* win.setPosition(l, t); win.element.style.visibility = 'hidden'
*
* Dependencies: StyleManager, DOMManager
*/

function FloatingWindow(){this.init.apply(this, arguments);}

FloatingWindow.prototype = { 

constructor: FloatingWindow,

 /*
 * constructor
 *
 * @param: a configuration object specifying 
 * the attributes of the window as well as it's
 * styling -the styling can be specified either as
 * configObj.attributes.style property or encapsulated
 * into a configObj.styleOptions object
 * for usage of these alternatives, see createDynamicElem()
 *
 * See create()
 */
  init : function(configObj){

   configObj             = configObj              || {}; 
   this.initOptions      = configObj.options      || {};
   this.initAttributes   = configObj.attributes   || {};
   this.initStyleOptions = configObj.styleOptions || {};

   this.initAttributes.id        = this.initAttributes.id        || 'dynamic-floating-window_' + new Date().getTime();
   this.initAttributes.className = this.initAttributes.className || 'dynamic-floating-window';  

   this.loadCallbacks     = configObj.loadCallbacks     || [];
   this.resizeCallbacks   = configObj.resizeCallbacks   || [];
   this.minimizeCallbacks = configObj.minimizeCallbacks || [];
   this.maximizeCallbacks = configObj.maximizeCallbacks || [];
   this.showCallbacks     = configObj.showCallbacks     || [];
   this.detachCallbacks   = configObj.detachCallbacks   || [];
   this.focusCallbacks    = configObj.focusCallbacks    || [];
   this.closeCallbacks    = configObj.closeCallbacks    || [];
 
   this.sendMessageBox = configObj.sendMessageBox;
   this.minZIndex      = configObj.minZIndex;
   this.maxZIndex      = configObj.maxZIndex;

   this.detached    = this.initOptions.detached; //TO DO : if this is set, the window should open in a new (pop-up) window
   this.closed      = this.initOptions.closed;
   this.isMinimized = this.initOptions.minimized; //TO DO ; if this is set, the window should open minimized
   this.isMaximized = this.initOptions.maximized; //TO DO ; if this is set, the window should open maximized
   this.isDraggable = this.initOptions.draggable;

   this.currWidth         = this.initStyleOptions.width 
   this.currHeight        = this.initStyleOptions.height; 
   this.currLeft          = this.initStyleOptions.left;
   this.currTop           = this.initStyleOptions.top;
   this.currRight         = this.initStyleOptions.right; 
   this.currBottom        = this.initStyleOptions.bottom;
   this.positioning       = this.initStyleOptions.position;
   this.isRightPositioned = ((this.currRight) ? true : false);
   this.isTopPositioned   = ((this.currTop) ? true : false);
   this.horizPos          =  ( (this.isRightPositioned) ? 'right' : 'left'); 
   this.horizPosValue     = ( (this.isRightPositioned) ? this.currRight : this.currLeft);
   this.vertPos           = ( (this.isTopPositioned) ? 'top' : 'bottom');
   this.vertPosValue      = ( (this.isTopPositioned) ? this.currTop : this.currBottom);

   this.topControlsBoxHeight = configObj.topControlsBoxHeight; //see the use of this in miminize() below

   this.element = this.create();
  
   }, //end init()

   /*
   * same as createDynamicElem(), major differences:
   * this already has it's this.elem's 'tag' property
   * set to a div, so it only creates 'div' elements
   * where as createDynamicElem() can create any HTML element;
   *
   */
   create : function(){

    var floatingDiv = document.createElement('div');
 
       for(var i in this.initAttributes){
        floatingDiv.setAttribute(i, this.initAttributes[i]);
       }

       /*
       * alternatively set styling options using 
       * this.initStyleOptions object here or 
       * the this.initAttributes.style property
       */
       for(var j in this.initStyleOptions){
        floatingDiv.style[j] = this.initStyleOptions[j];
       }

       var className = this.initAttributes.className; 
       var id = this.initAttributes.id;
       var maximizeNRestoreTitle = ( (this.isMaximized) ? 'restore' : 'maximize');           
       currFlWin = this;           
       
       var closer = this.initOptions.closable ? "<div title='close' class='"+ className +"_close' id='"+ id +"_close' onclick='setClosedWindow(\"" + id + "\"); currFlWin.close(\"" + id + "\");'></div>" : "";
       var minimizer = this.initOptions.minimizable ? "<div title='minimize' class='"+ className + "_minimize' id='"+ id +"_minimize' onclick='currFlWin.toggleMinimize(\"" + id + "\")'></div>" : "";
       var maximizer = this.initOptions.maximizable ? "<div title='"+ maximizeNRestoreTitle + "' class='"+ className + "_maximize' id='"+ id +"_maximize' onclick='currFlWin.toggleMaximize(\"" + id + "\")'></div>" : "";
       var detacher = this.initOptions.detachable ? "<div title='detach' class='"+ className + "_detach' id='"+ id +"_detach' onclick='currFlWin.detach(\"" + id + "\")'></div>" : "";
       var sizer = this.initOptions.resizable ? "<div title='resize' class='" + className + "_sizer' id='" + id + "_sizer' onmousedown='Effects.resizeElem(WindowObject.getWindowAsObject(\"" + id + "\"),event);'></div>" : "";     
       var windowTitle = this.initAttributes.title ? this.initAttributes.title : "";
       var titleBar = this.initOptions.hasTitle ? "<div class='"+ className +"_titleBar' id='"+ id +"_titleBar'>" + windowTitle + "</div>" : "";
       this.titleBar = titleBar;

       this.topControlsDivId = (id + '_topControls');
       this.topControls = '<div class="' + className + '_topControls" id="' + this.topControlsDivId + '">';
          if(this.initOptions.hasTitle) this.topControls += titleBar;
          if(this.initOptions.closable) this.topControls += closer;
          if(this.initOptions.minimizable) this.topControls += minimizer;
          if(this.initOptions.maximizable) this.topControls += maximizer;
          if(this.initOptions.detachable)this.topControls += detacher;
       this.topControls += '</div>'; 

       this.bottomControlsDivId = (id + '_bottomControls');
       this.bottomControls = '<div id="' + this.bottomControlsDivId + '">';
          if(this.initOptions.resizable) this.bottomControls += sizer;
       this.bottomControls += '</div>';

       this.contentBox = document.createElement('div'); 
       this.contentBox.setAttribute('id', id + '_contentBox');
       this.contentBox.setAttribute('className', className + '_contentBox');

       this.topControlsBox = document.createElement('div');
       this.bottomControlsBox = document.createElement('div');
       this.topControlsBox.innerHTML = this.topControls;
       this.bottomControlsBox.innerHTML = this.bottomControls;
        
       floatingDiv.innerHTML = '';
       floatingDiv.appendChild(this.topControlsBox); 
       floatingDiv.appendChild(this.contentBox);
       floatingDiv.appendChild(this.bottomControlsBox);

       return floatingDiv;

   }, //end create()

   addToDOM : function(parentElement){ //parentElement is not working yet, TO DO: make it 
     parentElement = parentElement || document.body;
     parentElement.appendChild(this.element);
     WindowObject.register(this.element);
     WindowObject.registerWinObject(this);

      if(this.isDraggable){
        Effects.enableDrag(this.topControlsBox, this.getId());
      }
      this.handleLoad(this);
   }, //end addToDom

   setId : function(newId){
    this.element.id = newId;
   }, // end setId

   getId : function(){
    return this.element.id;
   }, //end getId

   setClassName : function(newClassName){
    this.element.className = newClassName;
   },

   getClassName : function(){
    return this.element.className;
   },

   setTitle : function(title, maxLength){
      if(typeof title == 'string'){
       var displayTitle = ( title.substring(0, maxLength) + (title.length > maxLength ? '...' : '') );
       detectObjectness(this.getId() + '_titleBar').innerHTML = displayTitle;
       this.element.title = title;
      }
   }, //end setTitle

   getTitle : function(){
    return this.element.title;
   }, //end getTitle

   setContent : function(content, overWrite){

      if(overWrite){ 
 
         if(typeof content == 'string'){
             this.contentBox.innerHTML = content;
         }
         else if(typeof content == 'object'){ 
            this.contentBox.innerHTML = '';
            this.contentBox.appendChild(content); 
         }
      }

      else{
         
         if(typeof content == 'string'){
            this.contentBox.innerHTML += content;
           //this.contentBox.innerHTML = (this.getContent() + content);
         }
         else if(typeof content == 'object'){
            this.contentBox.appendChild(content);
         }
      }
      
    //this.element.innerHTML = ''; //causes IE to empty the below appended child elements
    this.element.appendChild(this.topControlsBox);
    this.element.appendChild(this.contentBox);
    this.element.appendChild(this.bottomControlsBox);  
   }, //end setContent

   getContent : function(){
    return this.contentBox.innerHTML;
   }, //end getContent

   setTopControlsContent : function(content){
    this.topControlsBox.innerHTML += content;
   },

   focus : function(winId, hideOtherWindows, exemption){
       
     var win = winId;
     var allWindows = WindowObject.getWindows();
     var winObj = WindowObject.getWindowAsObject(win);

       for(var i = 0; i < allWindows.length; i++){
          if(allWindows[i].id == win){ 
           var winArray = [];
           var minWinArray = []; 
             
             if(WindowObject.exists(win+'_close')) winArray.push(win+'_close');
             if(WindowObject.exists(win+'_minimize')) winArray.push(win+'_minimize');
             if(WindowObject.exists(win+'_titleBar')) winArray.push(win+'_titleBar');
             if(WindowObject.exists(win+'_maximize')){
              winArray.push(win+'_maximize'); 
              minWinArray.push(win+'_maximize');
             }
             if(WindowObject.exists(win+'_detach')){
              winArray.push(win+'_detach'); 
              minWinArray.push(win+'_detach');
             }

             if(winArray.length > 0){
               StyleManager.showElements(winArray);
                //if(winObj.isVisible){StyleManager.showElements(winArray);}
                //else{StyleManager.hideElements(winArray);}
             }
             if(WindowObject.exists(win+'_sizer')){
              var sizer = (win+'_sizer');
                if(winObj.isMinimized || !winObj.isVisible){StyleManager.hideElement(sizer);}
                else{StyleManager.showElement(sizer);}
             }
           
             //handle cases where the window is minimized, while having focus
             if( (winObj.isMinimized) && (minWinArray.length > 0) ){
              StyleManager.hideElements(minWinArray);
             }
             if( (!winObj.isMinimized) && (!isVisible(win + '_contentBox')) ){
              StyleManager.showElement(win + '_contentBox');
             }
           StyleManager.showElement(win);
           winObj.focused = true;
           winObj.handleFocus(winObj);
          }
          else{  
           var hide = hideOtherWindows;      
             if(WindowObject.exists(allWindows[i].id)){           
              var allWins = allWindows[i].id;
              this.unfocus(allWins, hide, exemption); 
             }
          }
       }
   }, //end focus

   unfocus : function(winId, hide, exemption){
      
     var win = winId;
     var winObj = WindowObject.getWindowAsObject(win);
     var winArray = []; 
      
            
      if(WindowObject.exists(win+'_maximize')) winArray.push(win+'_maximize');
      if(WindowObject.exists(win+'_detach')) winArray.push(win+'_detach');
      if(WindowObject.exists(win+'_sizer')) winArray.push(win+'_sizer'); 
      if(!winObj.isMinimized){ //in minimized state, we don't hide the title bar, the minimize and the close buttons
       if(WindowObject.exists(win+'_titleBar')) winArray.push(win+'_titleBar');
       if(WindowObject.exists(win+'_close')) winArray.push(win+'_close');
       if(WindowObject.exists(win+'_minimize')) winArray.push(win+'_minimize'); 
      }
      if(winArray.length > 0){StyleManager.hideElements(winArray);}
      if(hide && (win != exemption)){
       StyleManager.hideElement(win);
         if(isVisible(win + '_contentBox')){ 
          StyleManager.hideElement(win + '_contentBox');
         }
      }
    winObj.focused = false;
    this.handleUnfocus(winObj);
   }, //end unfocus

   minimize : function(winObj)
   { 

       if(winObj){
        var win = winObj.getId(); 
        var winArray = [];

         if(isVisible(win + '_contentBox')){
          var currDimensions = winObj.getSize();
          winObj.currHeight = parseInt(currDimensions.height);
         }            
         if(WindowObject.exists(win + '_sizer')){winArray.push(win + "_sizer");}
         if(WindowObject.exists(win + '_detach')){winArray.push(win + "_detach");}
         if(WindowObject.exists(win + '_maximize')){winArray.push(win + "_maximize");}
         if(winArray.length > 0){StyleManager.hideElements(winArray);}  
       StyleManager.hideElement(win + '_contentBox');
       StyleManager.setStyle(winObj.getId(), {'height' : winObj.topControlsBoxHeight});
       winObj.isMinimized = true;
      }
   }, //end minimize

   toggleMinimize : function(win)
   {
    var winObj = WindowObject.getWindowAsObject(win);
  
      if(isVisible(win + '_contentBox')){
       winObj.minimize(winObj);
         
      }
      else{
       var restoreHeight = winObj.currHeight + 'px';
       StyleManager.setStyle(winObj.getId(), {'height' : restoreHeight});
       StyleManager.showElement(win + '_contentBox');
       winObj.isMinimized = false;
      }
    winObj.handleMinimize(winObj);
   }, //end toggleMinimize

   maximize : function(winObject){
   
      if(!winObject.widthMaximized && !winObject.heightMaximized){      
       var bWidth = parseInt(Browser.Window.size().width);
       var bHeight = parseInt(Browser.Window.size().height); 
       var currDimensions = winObject.getSize(); 
       var currPosition = winObject.getPosition();

       winObject.currWidth = parseInt(currDimensions.width); 
       winObject.currHeight = parseInt(currDimensions.height); 
       winObject.currLeft = parseInt(currPosition.left);
       winObject.currTop = parseInt(currPosition.top);
       winObject.currRight = parseInt(currPosition.right); 
       winObject.currBottom = parseInt(currPosition.bottom);
       winObject.positioning = currPosition.positioning;
       winObject.horizPos =  ( (winObject.isRightPositioned) ? 'right' : 'left'); 
       winObject.horizPosValue = ( (winObject.isRightPositioned) ? winObject.currRight : winObject.currLeft);
       winObject.vertPos = ( (winObject.isTopPositioned) ? 'top' : 'bottom');
       winObject.vertPosValue = ( (winObject.isTopPositioned) ? winObject.currTop : winObject.currBottom);
       var winPositioning = winObject.positioning;
         if( (winObject.horizPos == 'right') && (winObject.vertPos == 'bottom')){winObject.setPosition({'positioning' : winPositioning, 'right' : '0', 'bottom' : '0'});}
         else{winObject.setPosition({'positioning' : winPositioning, 'left' : '0', 'top' : '0'});}
       winObject.setSize(bWidth, bHeight);
       winObject.widthMaximized = true;
       winObject.heightMaximized = true;
       winObject.isMaximized = true; 
       winObject.handleResize(winObject); winObject.handleMaximize(winObject);     
      }// end if(!winObject.widthMaximized && !winObject.heightMaximized)
   }, //end maximize
   
   toggleMaximize : function(win){    
    var winObject = WindowObject.getWindowAsObject(win); 

      if(!winObject.widthMaximized && !winObject.heightMaximized){winObject.maximize(winObject);}
      else{ 
       var winPositioning = winObject.positioning; 
       var winHorizPos = winObject.horizPos;
       var winHorizPosValue = winObject.horizPosValue;
       var winVertPos = winObject.vertPos;
       var winVertPosValue = winObject.vertPosValue; 

         if((winObject.horizPos == 'right') && (winObject.vertPos == 'bottom')){winObject.setPosition({'positioning': winPositioning, 'right' : winHorizPosValue, 'bottom': winVertPosValue}); }  
         else{winObject.setPosition({'positioning': winPositioning, 'left' : winHorizPosValue, 'top': winVertPosValue});}
       winObject.setSize(winObject.currWidth, winObject.currHeight); 
       winObject.widthMaximized = false;
       winObject.heightMaximized = false;
       winObject.isMaximized = false;
       winObject.handleResize(winObject); winObject.handleMaximize(winObject);
      }     
   }, //end toggleMaximize 

   setMaximizeNRestoreTitle : function(winObj)
   {
      if(WindowObject.exists(winObj.getId() +  "_maximize")){
       detectObjectness(winObj.getId() + "_maximize").title = ( (winObj.isMaximized) ? 'restore' : 'maximize');
      }
   }, //end setMaximizeNRestoreTitle

   detach : function(winId){
    var winObj = WindowObject.getWindowAsObject(winId);
    this.setDetached(winObj, true);
    this.handleDetach(winObj);
   }, //end detach

   setDetached : function(winObject, detachedState){
     winObject.detached = detachedState;
     winObject.element.detached = detachedState;
   }, //end setDetached

   isDetached : function(winId){
    winId || WindowObject.getActiveWindow();
    var winObj = WindowObject.getWindowAsObject(winId);
    return winObj.detached || winObj.element.detached;
   }, //end isDetached

   show : function(windowId){
    winId = windowId || this.element;
    winObj = ( (windowId) ? WindowObject.getWindowAsObject(windowId) : this);
    
    StyleManager.showElement(winId);
    winObj.isVisible = true;
       if(!this.showCallbacks){
        this.showCallbacks = [];
       }
       for(var i in this.showCallbacks){
          if(typeof this.showCallbacks[i] == 'function'){
           this.showCallbacks[i]();
          }
       } 
   }, //end show

   hide : function(winId, hide, exemption){
    var winObj = WindowObject.getWindowAsObject(winId);

    this.unfocus(winId, hide, exemption);
    StyleManager.hideElement(winId);
    winObj.isVisible = false;
       if(!winObj.showCallbacks){
        winObj.showCallbacks = [];
       }
       for(var i in winObj.showCallbacks){
          if(typeof winObj.showCallbacks[i] == 'function'){
           winObj.showCallbacks[i]();
          }
       }
   }, //end hide

   close : function(winId){    
    var win = winId;
    var winObj = WindowObject.getWindowAsObject(win);
      
      if(WindowObject.exists(win)){
       this.hide(win, true, 'none');
       winObj.isVisible = false;
       winObj.closed = true;
       winObj.handleClose(winObj);
       setTimeout(function(){winObj.destroy(winId);}, 1000);//allow the handleClose to execute the close callbacks before removing the window from the DOM
      }
   }, //end close

   destroy : function(winId){
    WindowObject.destroyWindow(winId);         
   }, //end destroy

   setSize : function(w, h) {
    this.setWidth(w);
    this.setHeight(h);
   }, //end setSize

   getSize : function() {
    return {
           width: (this.element.width) ? parseInt(this.element.width) : parseInt(this.element.style.width), 
           height: (this.element.height) ? parseInt(this.element.height): parseInt(this.element.style.height)
          };
   }, //end getSize

   setWidth : function(w) {
    w += '';
     if(w.indexOf('px') == -1){w += 'px';}
    this.element.style.width = w; 
   }, //end setWidth

   setHeight : function(h) {
    h += '';
    if(h.indexOf('px') == -1){ h += 'px';}
   this.element.style.height = h; 
   }, //end setHeight

   _setAttribute : function(attributeName, attributeValue){this.element.setAttribute(attributeName, attributeValue);}, //end _setAttribute
   _getAttribute : function(attributeName){
    var att = this.element.getAttribute(attributeName);
    return att;
   }, //end _getAttribute

   _getAttributes : function(){
     var attrs = [];
      for(var i in this.element){
       attrs.push(this.element[i]);
      }
     return attrs;
   }, //end _getAttributes

   _setStyles : function(cssStyleSyntaxDefinition){this.element.setAttribute('style', cssStyleSyntaxDefinition);},  //usage example: currObj._setStyles('font-family:arial;color:#c00;');
   _setStyle : function(styleName, styleValue){this.element.style['styleName'] = styleValue;},  //usage example: currObj._setStyle('zIndex', '100');
   _getStyle : function(styleName){return this.element.style[styleName];}, 

   /*
   * positions the window
   * centers the window if no positions are specified
   */
   setPosition : function (configObj) {

    configObj = configObj || {};
    
    var windowWidth = Browser.Window.size().width; 
    var windowHeight = Browser.Window.size().height;      
    var winHeight = size(this.element).height;  //this.element.size().height;
    var winWidth  = size(this.element).width; //this.element.size().width; 

    var top = (configObj.top) ? configObj.top : ( (windowHeight - winHeight)/2 );  
    var left = (configObj.left) ? configObj.left : ( (windowWidth - winWidth)/2 );
    var bottom = configObj.bottom;
    var right = configObj.right; 
    
    var positioning = ( (configObj.positioning) ? configObj.positioning : 'absolute');

    this.hasFixedPositioning = ( (positioning == 'fixed') ? true : false );
    this.hasRelativePositioning = ( (positioning == 'relative') ? true : false );
    this.hasAbsolutePositioning = ( (positioning == 'absolute') ? true : false ); 

    this.isTopPositioned = (configObj.top) ? true : false;
    this.isLeftPositioned = (configObj.left) ? true : false;
    this.isBottomPositioned = (configObj.bottom) ? true : false;
    this.isRightPositioned = (configObj.right) ? true : false; 
    
    if( (String(top).indexOf('px') == -1) && (parseInt(top) != 0) ){top += 'px';}
    if( (String(left).indexOf('px') == -1) && (parseInt(left) != 0) ){left += 'px';}
    if( (String(bottom).indexOf('px') == -1) && (parseInt(bottom) != 0) ){bottom += 'px';}
    if( (String(right).indexOf('px') == -1) && (parseInt(right) != 0) ){right += 'px';}
    
    this.element.style.position = positioning;
      if(configObj.top) this.element.style.top = top;   
      if(configObj.left)this.element.style.left = left;
      if(configObj.bottom)this.element.style.bottom = bottom;   
      if(configObj.right)this.element.style.right = right;

   }, //end setPosition(), this is complete

   getPosition : function() {
   return {
           left: (this.element.left) ? parseInt(this.element.left) : ( (this.element.style.left) ? parseInt(this.element.style.left) : 'undefined' ), 
           top: (this.element.top)? parseInt(this.element.top) : ( (this.element.style.top)? parseInt(this.element.style.top) : 'undefined'),
           bottom: (this.element.bottom) ? parseInt(this.element.bottom) : ( (this.element.style.bottom) ? parseInt(this.element.style.bottom) : 'undefined' ),
           right: (this.element.right) ? parseInt(this.element.right) : ( (this.element.style.right) ? parseInt(this.element.style.right) : 'undefined' ),
           positioning: (this.element.position) ? this.element.position : ( (this.element.style.position) ? this.element.style.position : 'undefined')
          };
   }, //end getPosition


   /*******************************
   * FloatingWindow Event Handlers
   *******************************/

   handleLoad : function(winObj){

      if(!winObj.loadCallbacks){
        winObj.loadCallbacks = [];
      }
      for(var i in winObj.loadCallbacks){
         if(typeof winObj.loadCallbacks[i] == 'function'){
           winObj.loadCallbacks[i]();
         }
      }

   }, //end handleLoad

   handleFocus : function(winObj){ 
     
      if(!winObj.focusCallbacks){
        winObj.focusCallbacks = [];
      }
      for(var i in winObj.focusCallbacks){
         if(typeof winObj.focusCallbacks[i] == 'function'){
           winObj.focusCallbacks[i]();
         }
      }
   }, //end handleFocus

   handleUnfocus : function(winObj){ 
       if(!winObj.unfocusCallbacks){
        winObj.unfocusCallbacks = [];
      }
      for(var i in winObj.unfocusCallbacks){
         if(typeof winObj.unfocusCallbacks[i] == 'function'){
           winObj.unfocusCallbacks[i]();
         }
      }
   }, //end handleUnfocus

   handleResize : function(winObj){ 
     winObj = winObj || WindowObject.getActiveWindowAsObject();
      if(!winObj.resizeCallbacks){
       winObj.resizeCallbacks = [];
      }
      for(var i in winObj.resizeCallbacks){
         if(typeof winObj.resizeCallbacks[i] == 'function'){
           winObj.resizeCallbacks[i]();
         }
      }
   }, //end handleResize

   handleMinimize : function(winObj){ 
       if(!winObj.minimizeCallbacks){
        winObj.minimizeCallbacks = [];
      }
      for(var i in winObj.minimizeCallbacks){
         if(typeof winObj.minimizeCallbacks[i] == 'function'){
           winObj.minimizeCallbacks[i]();
         }
      }
   }, //end handleMinimize

   handleMaximize : function(winObj){ 
     winObj.setMaximizeNRestoreTitle(winObj);
       if(!winObj.maximizeCallbacks){
        winObj.maximizeCallbacks = [];
      }
      for(var i in winObj.maximizeCallbacks){
         if(typeof winObj.maximizeCallbacks[i] == 'function'){
           winObj.maximizeCallbacks[i]();
         }
      }
   }, //end handleMaximize

   handleDetach : function(winObj){ 
   
       if(!winObj.detachCallbacks){
        winObj.detachCallbacks = [];
      }
      for(var i in winObj.detachCallbacks){
         if(typeof winObj.detachCallbacks[i] == 'function'){
           winObj.detachCallbacks[i]();
         }
      }
   }, //end handleDetach

   handleClose : function(winObj){ 
       if(!winObj.closeCallbacks){
        winObj.closeCallbacks = [];
      }
      for(var i in winObj.closeCallbacks){
         if(typeof winObj.closeCallbacks[i] == 'function'){
           winObj.closeCallbacks[i]();
         }
      }
   } //end handleClose
}//end of FloatingWindow

/*
* An object for managing window, browser, keyboard, etc events
* @author: Michael Orji
* @date: 25 Feb, 2012
*/
var EventManager = {

   /*
   * @credits: The javascript Anthology
   */
   addLoadListener : function(fn){

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
            window.onload = function(){
             oldfn();
             fn();
            };
         }
      }
   },

   attachEventListener : function (target, eventType, functionRef, capture){
    target = $O(target) //line added by Michael orji, Feb 25, 2012
    
      if (typeof target.addEventListener != "undefined"){
	    EventManager.attachEventListener = function(target, eventType, functionRef, capture){
		 target = $O(target);
         target.addEventListener(eventType, functionRef, capture);
		}
      }
      else if (typeof target.attachEvent != "undefined"){
	     EventManager.attachEventListener = function(target, eventType, functionRef, capture){
		  target = $O(target);
          target.attachEvent("on" + eventType, function(){ functionRef.call(target) }); //IE: bind the "this" object to the target object, otherwise "this" points to the global (window) object
		 }
      }
      else{
	    EventManager.attachEventListener = function(target, eventType, functionRef, capture){
		  target = $O(target);
          eventType = "on" + eventType;
          if (typeof target[eventType] == "function"){ //if the target already has a listener attached to it using the target.listener = function(){} approach,
           var oldListener = target[eventType]; //copy the listener function into the oldListener variable,
            target[eventType] = function(){ //then attach a new anonymous listener function to the target, from which you call the oldListener function, and the functionRef
             oldListener();
             return functionRef();
            };
          }
          else{
           target[eventType] = functionRef;
          }
        }
	  }
	  
	  EventManager.attachEventListener(target, eventType, functionRef, capture);
   },

   detachEventListener : function (target, eventType, functionRef, capture)
   {
      target = $O(target);
      if (typeof target.removeEventListener != "undefined"){
	   EventManager.detachEventListener = function (target, eventType, functionRef, capture){
	    target = $O(target);
        target.removeEventListener(eventType, functionRef, capture);
       }		
      }
      else if (typeof target.detachEvent != "undefined"){
	    EventManager.detachEventListener = function (target, eventType, functionRef, capture){
	     target = $O(target);
         target.detachEvent("on" + eventType, functionRef);
		}
      }
      else{
	    EventManager.detachEventListener = function (target, eventType, functionRef, capture){
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
   formatEvent : function (oEvent) {

     var B = Browser.UA.Name().toLowerCase();
     var S = Browser.UA.OS().toLowerCase();

      if ( (B == 'ie') && (S == 'win') ) {

       oEvent.charCode   = (oEvent.type == "keypress") ? oEvent.keyCode : 0;
       oEvent.eventPhase = 2;
       oEvent.isChar     = (oEvent.charCode > 0);
       oEvent.pageX      = oEvent.clientX + document.body.scrollLeft;
       oEvent.pageY      = oEvent.clientY + document.body.scrollTop;
       oEvent.target     = oEvent.srcElement;
       oEvent.time       = (new Date).getTime();

         if (oEvent.type == "mouseout") {
          oEvent.relatedTarget = oEvent.toElement;
         } 
         else if (oEvent.type == "mouseover") {
          oEvent.relatedTarget = oEvent.fromElement;
         }
         oEvent.preventDefault = function () {
          this.returnValue = false;
         }
         oEvent.stopPropagation = function () {
          this.cancelBubble = true;
         }
         
      }

    return oEvent;
   },

   eventObject : function(event){
    //event = (typeof event !== "undefined") ? event : window.event ; 
    //event = event || window.event;
    //event = (!event) ? window.event : event;

      /*if(event){
          return ( (event) ? event : window.event);
      }
      else{ //see eventObject2 below for how this works
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
   eventObject2 : function(){
    return ( (window.event) ? this.formatEvent(window.event) : EventManager.eventObject2.caller.arguments[0] );  
   },
  
   eventTarget : function(event){
    event = this.eventObject(event);
    var targetElement = null;
    targetElement = ( (event.target) ? event.target : event.srcElement); 
      while (targetElement.nodeType == 3 && targetElement.parentNode != null){
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
   */
   targetElementTypeIs : function(elem, event){
    var target = this.eventTarget(event);
    return target.tagName.toLowerCase() == elem.toLowerCase();
   },   //e.g Usage EventManager.isTargetElementType('a'); EventManager.isTargetElementType('div');

   targetIsDocument : function(event){
    return this.targetElementTypeIs('document', event) || this.targetElementTypeIs('body', event) || this.targetElementTypeIs('html', event);
   }, 

   cancelDefaultAction : function(e){
    e = this.eventObject(e);
    (typeof e.preventDefault !== "undefined") ? e.preventDefault() : e.returnValue = false;
   },

   stopEventPropagation : function(e){
    e = this.eventObject(e);
    (typeof e.stopPropagaion !== "undefined") ? e.stopPropagation() : e.cancelBubble = true;
   },

   eventTypeIs : function(eventType, event){
    event = this.eventObject(event);
    return (event.type == eventType);
   }
   
}//end of the EventManager object

/*
* @author: michael orji
* @date: dec 26, 2011
*/
function createImage(obj){

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
* @author: Michael Orji
*/
var Mouse = {

   Button: function(event){
   
    var browserName = Browser.UA.Name().toLowerCase();
    event = EventManager.eventObject(event);

      if(browserName == 'ie'){ 
         switch(event.button){
          case 0: return 'left button'; break;    
          case 1: return 'left button'; break;
          case 2: return 'right button'; break;
          case 4: return 'middle button'; break;
          case 3: return 'left and right button'; break;
          case 5: return 'left and middle button'; break;
          case 6: return 'right and middle button'; break;
         }
      }
      else{
         switch(event.button){ 
          case 0: return 'left button'; break;
          case 1: return 'middle button'; break;
          case 2: return 'right button'; break;
         }
      }
      
   }, //close Button()

   Position: function(event){

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
   

      if(typeof event.pageX !== "undefined" && typeof event.x !== "undefined"){
       cursorLocation.left = event.pageX;
       cursorLocation.top  = event.pageY;
      }
      else{
       cursorLocation.left = event.clientX + scrollPos.left;
       cursorLocation.top  = event.clientY + scrollPos.top;
      }
    return cursorLocation;
   }, //end Position()

   /*
  * @credits: http://javascript.info/tutorial/mouse-events
  * @date: 27 Feb, 2012
  */
   isOutside : function(mainElem, evt) {
    evt      = EventManager.eventObject(evt); //added by Michael Orji
    var parent   = $O(mainElem); //added by Michael Orji
    var elem = evt.relatedTarget || evt.toElement || evt.fromElement;
      while ( elem && elem !== parent) {
       elem = elem.parentNode;
      }
      if ( elem !== parent) {
       return true
      }
   }// end isOutside()
}//close Mouse object

/*
* @author: michael orji
* @date: 28 Feb, 2012
*/
var StyleManager = {   

   setStyle : function(elem, styleOptions){
     elem = $O(elem);
     styleOptions = styleOptions || {}; 
      for(var j in styleOptions){
        elem.style[j] = styleOptions[j];
       }
   }, //end setStyle

   styleElementsOfClass : function(cssClassName, styleObject){
    styleObject = styleObject || {};
    var ccn = DOMManager.getElementsOfClass(cssClassName);
      for(var x in ccn){
       this.setStyle(ccn[x], styleObject);
      }
   }, //end styleElementsOfClass

   hideElement : function(elemId){
    elem = $O(elemId);
    elem.style.visibility = 'hidden';   
   }, //end hideElement

   showElement : function(elem, elemStyle){
    elem = $O(elem);
    elem.style.visibility = 'visible';
   }, //end showElement

   setElementDisplay : function(elemId, displayVal){
    elem = $O(elemId);
    elem.style.display = displayVal;
   }, //end setElementDisplay

   hideElements : function(elemsCollection){
      for(var i in elemsCollection){
       this.hideElement(elemsCollection[i]);
      }  
   }, //end hideElements

   showElements : function(elems){
      for(var i = 0; i < elems.length; i++){
       this.showElement(elems[i]);
      }  
   }, //end showElements

   showElementsOfClass : function(cssClass, displayStyle){
    var ElementsToShow = DOMManager.getElementsOfClass(cssClass);
    var len = ElementsToShow.length;
      for(var i = 0; i < len; i++){
       this.showElement(ElementsToShow[i], displayStyle);
      }
   },//end showElementsOfClass

   hideElementsOfClass : function(htmlTag, cssClass){
    var candidateElements = document.getElementsByTagName(htmlTag);
    var len = candidateElements.length;
      for(var i = 0; i < len; i++){
         if(candidateElements[i].className == cssClass){
          this.hideElement(candidateElements[i]);
         }
      }
   }, //end hideElementsOfClass

   hideElementsOfClass : function(cssClass, useDisplay){
    var ElementsToHide = DOMManager.getElementsOfClass(cssClass);
    var len = ElementsToHide.length;
      for(var i = 0; i < len; i++){
       this.hideElement(ElementsToHide[i], useDisplay);
      }
   }//end hideElementsOfClass
}//end StyleManager

/*
* object for accessing any other dynamically generated window inside the current window
* (also known as the global object)
* this object cannot create any new window, to do that
* use the floatingWindow constructor in floatingWindow.js
*
* @author: michael orji
* @encoding: utf-8
*/
var WindowObject = {   

 /* 
 * keeps track of the number of dynamically generated windows on the page
 * holds the window.element property which is an html element having DOM
 * properties and methods like id, name, etc
 */
 windows: [],

 /* 
 * keeps track of the number of dynamically generated window objects on the page 
 * holds the a reference/pointer to the window object itself which contains its
 * own properties and methods
 */
 winObjects: [],

 /*
 * max z-index of dynamically generated windows
 * we set it to a number higher than the z-index of the highese element on site/page
 */
 maxZIndex: 11, 
   
   /* 
   * checks if window as an object or element with id 'win' exists in the 
   * DOM Node; returns true if exists, false else
   * @author: michael orji
   * @param: either a reference to the window object or a string representing the id of the window object 
   */
   exists: function(win){
      if( (typeof win == 'string' ) && (document.getElementById(win)) ){
       return true;
      }
      else if(typeof win == 'object'){
         for(var i = 0; i < this.winObjects.length; i++){
            if(this.winObjects[i] == win){
             return true;
            }
         }
      }
    return false;
   }, //end exists, complete

   /* registers a window with its element (FloatingWindow.element)  or id as the parameter */
   register: function(win) {
    win = ( (typeof win['id'] == 'undefined') ? win : win['id'])
    
    /*
   * register the window only if it has actually been added to the document
   */
    win = $O(win);
      if(win){
       this.windows.push(win);
     }
    return win;
   },

   /* registers a window as an object */
   registerWinObject: function(winObj)
   {
    this.winObjects.push(winObj);
    return winObj;
   },

   getWindows: function(asObject) {
      if(asObject){return this.winObjects;}
    return this.windows;
   },

   //sets a dynamically generated window to be the active one among all on the current window
   setActiveWindow: function(winId)
   {
    var win = $O(winId);
       if(win){
        win.isActive = true;
          if(typeof this.getWindowAsObject(winId).focus != 'undefined'){
           this.getWindowAsObject(winId).focus();
          }
       }//catch(e){} 
    
      //set other windows to inactive
      for(var i = 0; i < this.windows.length; i++){
         if(this.windows[i] != win){
          this.windows[i].isActive = false;
         }
      }
   },

   /*
   * @param: boolean value indicating whether to return the complete html element(true) 
   * or just its id(false), which can the be converted to a DOM object with document.getElementbyId
   */
   getActiveWindow: function(completeElem)
   {
    completeElem = completeElem || false;
      for(var i = 0; i < this.windows.length; i++){
         if(this.windows[i].isActive){
          /*
         * since windows[] can also contain a window's id, (see register() above), if we're not returning the complete elem, 
         * then we're either returning the element.id, if we're what we have in windows[] is the element's id, else we get the complete html element itself if that's what is registered in windows[]
         */
          return ( (completeElem) ? this.windows[i] : ( (typeof this.windows[i]['id'] != 'undefined') ? this.windows[i]['id'] : this.windows[i] ) ); 
         }
      }
    return -1;
   },

   //gets active window as an independent object, not as an HTML DOM(document.getElementById) object
   getActiveWindowAsObject: function()
   {
      for(var i = 0; i < this.winObjects.length; i++){
         for(var j = 0; j < this.windows.length; j++){
            if(this.windows[j].isActive){
               if(this.windows[j]['id'] == this.winObjects[i]['element']['id']){
                return this.winObjects[i];
               }
            }
         }
      }
    return null;
   },

   //gets a window as an independent object, not as an HTML DOM(document.getElementById) object
   getWindowAsObject: function(winId){ 
      for(var i = 0; i < this.winObjects.length; i++){
         if(winId == this.winObjects[i]['element']['id']){
          return this.winObjects[i];
         }
      }
    return null;
   },

   destroyWindow: function(winId)
   {
      var winObj = this.getWindowAsObject(winId);
       
       if(winObj.element && winObj.element.parentNode){
        DOMManager.removeFromParentNode(winObj.element);
       }
       
       for(var j = 0; j < this.windows.length; j++){
          var wID = ( (this.windows[j]['id'] != 'undefined') ? this.windows[j]['id'] : this.windows[j] );
          if(wID == winObj.getId()){
           this.windows.splice(j, 1);
          }
       }
       for(var i = 0; i < this.winObjects.length; i++){
          if(this.winObjects[i] == winObj){
           this.winObjects.splice(i, 1);
          }
       }
    },

   getWindowPosition: function(win, which)
   {
     win = win || this.getActiveWindow(true);
     which = which || 'current';

     win = $O(win);

     var ws = (win.style) ? win.style : win.element.style;
     var windows = this.windows;

      for(var i = 0; i < windows.length; i++){
         if(windows[i]['id'] == win.id){
            if(which == 'previous'){
             var previousWin = windows[i-1];
             ws = (previousWin.style) ? previousWin.style : previousWin.element.style; 
            }
            else if(which == 'next'){
             var nextWin = windows[i+1];
             ws = (nextWin.style) ? nextWin.style : nextWin.element.style; 
            }
         }
      }
    return {'position': ws.position, 'top': ws.top, 'left': ws.left, 'bottom': ws.bottom, 'right': ws.right};
   },


   getWindowSize: function(win, which)
   {
     win = win || this.getActiveWindow(true);
     which = which || 'current';

     win = $O(win);

     var ws = (win.style) ? win.style : win.element.style;
     var windows = this.windows;

      for(var i = 0; i < windows.length; i++){
         if(windows[i]['id'] == win.id){
            if(which == 'previous'){
             var previousWin = windows[i-1];
             ws = (previousWin.style) ? previousWin.style : previousWin.element.style; 
            }
            else if(which == 'next'){
             var nextWin = windows[i+1];
             ws = (nextWin.style) ? nextWin.style : nextWin.element.style; 
            }
         }
      }
    return {'width': ws.width, 'height': ws.height};
   }
}//end of WindowObject

/*
* Creates Objects
*/
function XHR(configObj){
 
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
      {xmlHttp = new XMLHttpRequest();}
      catch(e)
      {
       //IE6 or older
       var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0","MSXML2.XMLHTTP.5.0","MSXML2.XMLHTTP.4.0","MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"];

         for (var i=0; i<XmlHttpVersions.length && !xmlHttp; i++)
         {
            try{xmlHttp = new ActiveXObject(XmlHttpVersions[i]);}
            catch (e) {xmlHttp = null;}
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
   this.setRequestHeaders = function (){

      if(this.requestType == "POST"){
       this.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      }
      if(!this.cache){
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
   this.encodeFormData = function(dataString) {

    var pairs     = [];
    var regexp    = /%20/g; // A regular expression to match an encoded space
    var dataArray = trim(dataString).split('&');
     
      for(var i in dataArray){

        /*
        * splits it only at the first occurence of the '=' xter
        * this handles situations such as: <a href="www.trend-this.com">trend-this</a>
        * where we have the '=' xter of the link as part of the data, so we avoid splitting it as if it were among the name/value pairs
        * the used function was created on the same date in response to this problem
        */
        eachData = splitOnFirstOccurenceOfMultipleSubstrings(dataArray[i], '=', "\x034"); 
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
    
      if(timeout){
       timer = setTimeout( 
         function (){
            //if(!ready(xhrObject)){
             aborted = true; //@credits: Accelerated Dom Scripting with Ajax, APIs and Libraries, chap. 5, section 4: planning for failure
             xhrOjbect.abort();
            //}
         }, timeout * 1000);
      }

      if(this.requestType == 'GET'){ 
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
    var innerFX = function(val){
         closureObject.closureName = function(){
          var returnVal = val;
          return returnVal;
         }
      }
	
    xhrObject.onreadystatechange = function()

      {
         if(ready(xhrObject)){

            if(timer){ clearTimeout(timer); } //@credits: javascript, the definitive guide, 5th edition, example 20.7
            if( !aborted && (xhrObject.status == 200) )
			{ //@credits: Accelerated Dom Scripting with Ajax, APIs and Libraries, chap. 5, section 4: planning for failure
				successCallback(parseServerReply(xhrObject, debugCallback));
			 
				if(closureObject.closureName && (typeof closureObject.closureName == 'string') ){ //call inner function only if user specifies that a value (a closure) should be returned
					innerFX(xhrObject.responseText);
				}
            }
            else{
             /*
             * the aborted argument lets you determine if the call was aborted as a result of timeout, so you can serve an appropriate response
             * indicating either a timeout of the request or just an error returned from the server
             */
             errorCallback(xhrObject, aborted);
             var messageOnAbort = aborted ? "The request timed out" : ""; 
             logToConsole("Error " + xhrObject.status + " : " + xhrObject.statusText + "\n" + messageOnAbort);    
            } 
         }
         else{
          readyStateCallback(++progressCounter); // see the declaration of the progressCounter variable above for how this can be used within the function definition
          logToConsole('progress counter ' + progressCounter);
         }
      }

    xhrObject.send(sendParam);
   }

   //parses and returns server reply, either as XML, a JSON object or as a string
   this.parseServerReply = function(xhrObject, debugCallback){

      if(debugCallback && typeof debugCallback === 'function'){ //added on Nov. 4, 2012
       debugCallback(xhrObject.responseText);
      }
    
    var responseObject = null;
    var rawValue       = '';
    
      //credits: Javascript the Definitive guide, 5th edition, example 20.6
      //@modified : michael Orji
      switch(xhrObject.getResponseHeader("Content-Type")) {
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

   this.isComplete = function(xhrObject){
      try
      { 
         if( xhrObject.readyState == 4 ){
          return true;
         }
      }
      catch(e)
      {
       logToConsole('Caught Exception: ' + e.description); //TO DO: Pass an error place holder parameter to the XHR constructor and place e.description inside
      }
   }

   this.run = function(closureObject){this.makeRequest(closureObject);}
 this.run(this.closureObject);
}//end of XHRManager Constructor Function

/*
* Object with callable functions, a rewrite of makeXHRRequest function
*/
var XHRManager = {

   createXHRObject : function()
   {
    var xmlHttp = null;
     
      try //all browsers except IE6 and older
      {xmlHttp = new XMLHttpRequest();}
      catch(e)
      {
       //assume IE6 or older
       var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0","MSXML2.XMLHTTP.5.0","MSXML2.XMLHTTP.4.0","MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"];

         for (var i=0; i<XmlHttpVersions.length && !xmlHttp; i++)
         {
            try{xmlHttp = new ActiveXObject(XmlHttpVersions[i]);}
            catch (e) {xmlHttp = null;}
         }
      }
    return xmlHttp;   
   },

   makeXHRRequest : function(configObj, closureObject)
   {
    configObj          = configObj || {};
    var serverURL      = configObj.url;
    var xhrObject      = configObj.xhrObject;
    var callback       = configObj.callback;
    var requestType    = configObj.type || 'POST';
    var params         = configObj.params || null; //params is null for 'GET' requests, replaces if(requestType == 'GET'){params = null}
    var async          = configObj.async || true;
    var callbackParams = configObj.callbackParams || configObj.callback.arguments || '';

    /*
    xhrObject.setRequestHeader("If-Modified-Since", "Wed, 15 Jan 1995 01:00:00 GMT");
    xhrObject.setRequestHeader("Cache-Control","no-cache");
    xhrObject.setRequestHeader("Cache-Control", "must-revalidate");
    xhrObject.setRequestHeader("Cache-Control","no-store");
    xhrObject.setRequestHeader("Pragma","no-cache");
    xhrObject.setRequestHeader("Expires","0");
    */

    xhrObject.open(requestType, serverURL, async);
      if(requestType == "POST"){xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");}

      /*
      * since it's not possible to return a value through the onreadystatechange event handler, coz of the anynchronous nature of Ajax,
      * we create this inner (private) function, 'innerFX' which -- by being called by the onreadystatechange() -- 
      * then returns the server's responseText through a closure, 'closureObject.closureName'
      */
      closureObject = closureObject || {};
      var innerFX = function(val){
         closureObject.closureName = function(){
          var returnVal = val;
          return returnVal;
         }
      }
      xhrObject.onreadystatechange = function(){
       callback(callbackParams);
         if(closureObject.closureName && (typeof closureObject.closureName == 'string') ){ //only call the inner function if the user specifies that a value (a closure) should be returned
            if( (xhrObject.readyState == 4) && (xhrObject.status == 200) ){
             innerFX(xhrObject.responseText);
            }
         }
      }
    xhrObject.send(params);
   },

   parseServerReply : function(xhrObject, getStrValue){
      if(getStrValue){return xhrObject.responseText;}
    return eval( '(' + xhrObject.responseText + ')' );
   },

   isComplete : function(xhrObject){
    return ( (xhrObject.readyState == 4) && (xhrObject.status == 200) );
   }
}//end of AjaxManager object






/*
* Gets the absolute path to specified file or folder
* @author: Michael Orji
* @date: Nov. 11, 2012
*/
function get_path(targetName){
 
 var scripts     = $Tag('script');
 var targetPath  = '';
   for(i = 0; i < scripts.length; i++){
    var scriptPath           = scripts[i].src.replace(/%20/g, ' ');  
    var targetPathStartIndex = scriptPath.lastIndexOf('/' + targetName) + 1; //
    var scriptName           = scriptPath.substring(targetPathStartIndex, targetPathStartIndex + targetName.lastIndexOf(getLastChar(targetName)) + 1 );
      if(trim(scriptName) == trim(targetName)){ 
       targetPath = trim( scriptPath.substring(0, targetPathStartIndex) );
       return targetPath + '/';
      }
   }
}

function getIFrameContent(iframeIdOrObject)
{
	var frame = $O(iframeIdOrObject);
	var frameBody = '';
	if(frame.contentDocument)
	{
		frameBody = frame.contentDocument.getElementsByTagName('body')[0];
	}
	else if(frame.contentWindow)
	{ //IE
		frameBody = frame.contentWindow.document.getElementsByTagName('body')[0];
	}
	return trim(frameBody.innerHTML);
}

/*
* @date: Dec 5, 2013
* @author: Michael Orji
*/
function getPath( opts )
{
	opts = opts || {};
	var targetName = opts.targetName || '';
	var excludeCurrentDirectory = opts.excludeCurrentDirectory || false;
	var currentDirectoryName    = opts.currentDirectoryName || '';
	var targetPath = get_path(targetName).substring(0);
	
	if(excludeCurrentDirectory && !isEmpty(currentDirectoryName))
	{
		targetPath = targetPath.substring(0, targetPath.indexOf(currentDirectoryName)); //remove currentDirectoryName + '/'
	}
	return targetPath;
}

function emptyFieldValue(fieldId, testValue){
 testValue    = testValue.toLowerCase();
 var fieldVal = $O(fieldId).value.toLowerCase();
 
   if(fieldVal == testValue){
    $Style(fieldId).color = '#000000';
    $O(fieldId).value = '';
   }
}
function replaceFieldValue(fieldId, value){
 
   if(isEmpty(trim($O(fieldId).value))){
    $Style(fieldId).color = '#aaaaaa';
    $O(fieldId).value = value;
   }
}

