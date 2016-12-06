//FROM THE JAVASCRIPT ANTHOLOGY

addLoadListener = function(fn){

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
   //var oldfn = window.onload;
      if (typeof window.onload != 'function')
      {
      window.onload = fn;
      }

      else
      {
      window.onload = function(){
      //oldfn();
      fn();
      };
      }
   }
}



attachEventListener = function (target, eventType, functionRef, capture)
{
   target = detectObjectness(target) //line added by Michael orji, Feb 25, 2012
   if (typeof target.addEventListener != "undefined"){
    target.addEventListener(eventType, functionRef, capture);
   }

   else if (typeof target.attachEvent != "undefined"){
    target.attachEvent("on" + eventType, functionRef);
   }

   else{
    eventType = "on" + eventType;

      if (typeof target[eventType] == "function"){
       var oldListener = target[eventType];

         target[eventType] = function(){
         oldListener();
         return functionRef();
         };
      }

      else{
       target[eventType] = functionRef;
      }
   }
}



detachEventListener = function (target, eventType, functionRef, capture)
{

   if (typeof target.removeEventListener != "undefined"){
    target.removeEventListener(eventType, functionRef, capture);
   }

   else if (typeof target.detachEvent != "undefined"){
    target.detachEvent("on" + eventType, functionRef);
   }

   else{
    target["on" + eventType] = null;
   }
}


function getElemPosition(theElement)
{
 var positionLeft = 0;
 var positionTop = 0;
   while (theElement != null){
    positionLeft += theElement.offsetLeft;
    positionTop += theElement.offsetTop;
    theElement = theElement.offsetParent;
   }
 return {'left': positionLeft, 'top': positionTop};
}

/*
* original code source: http://www.quirksmode.org/js/findpos.html 
* access date: July 9, 2011, 10:42 am;
*/
function getElemPos(obj)
{
 var curleft = curtop = 0;
   if (obj.offsetParent) {
      do {
        curleft += obj.offsetLeft;
        curtop += obj.offsetTop;
      } while (obj = obj.offsetParent);
    return {'left': curleft, 'top': curtop};
   }
}

function getEventTarget(event){

var targetElement = null;

   if (typeof event.target != "undefined"){
    targetElement = event.target;
   }

   else{
    targetElement = event.srcElement;
   }

   while (targetElement.nodeType == 3 && targetElement.parentNode != null){
    targetElement = targetElement.parentNode;
   }

return targetElement;

}



//Opening Off-site Links in a New Window
openExtLinks = function(e)
{
var target = e ? e.target : window.event.srcElement;

   while (target && !/^(a|body)$/i.test(target.nodeName)){
    target = target.parentNode;
   }

   if (target && target.getAttribute('rel') && target.rel == 'external'){
    var external = window.open(target.href);
    return external.closed;
   }

}

/*
USAGE : 
all external links should have the rel="external" attribute
1. SITE: <a href="http://www.google.com/" rel="external">Google (offsite)</a>
2. document.onclick = openExtLinks;
*/

//FROM OTHER SOURCES
/* removes leading and trailing spaces from strings */
function trim(s)
{
   if(typeof s == 'string'){
     return s.replace(/(^\s+)|(\s+$)/g, "")
   }
 return s;
}

/* credits: popup.html of ajax_im by joshua gross */
function trim2(text) {
   if(text == null) return null;
   return text.replace(/^[ \t]+|[ \t]+$/g, "");
}

/*
* credit: http://james.padolsey.com/javascript/get-document-height-cross-browser/
*/
function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

/*
* from facebook, based on prototype.js
*/
$=function(a){return typeof a=='string'?document.getElementById(a):a;}


// THE FOLLOWING ARE BY MICHAEL ORJI

function objectify(elemId){
 return document.getElementById(elemId);
 //return typeof elemId=='string'?document.getElementById(elemId):elemId;
}

function detectObjectness(elem){
 return ( isObject(elem) ? elem : objectify(elem) );
}

function isObject(elem, strict){
   if(strict)
    return (typeof elem === 'object');
 return (typeof elem == 'object' || typeof elem == 'array' || typeof elem == 'function');
}

function cancelDefaultAction(e){
 (typeof e.preventDefault !== "undefined") ? e.preventDefault() : e.returnValue = false;
}

function stopEventPropagation(e){
 (typeof e.stopPropagaion !== "undefined") ? e.stopPropagation() : e.cancelBubble = true;
}

handleEvent = function(e, ada, aep){
 
//ada = allow default action
//aep = allow event propagation

   if(typeof ada === 'undefined'){
    cancelDefaultAction(e);
   }

   if(typeof aep === 'undefined'){
    stopEventPropagation(e);
   }
}

//determines if 'element' is the target of event 'e'
isEventTarget = function(element, e){

event = e || window.event;
var target = (typeof event.target !== 'undefined') ? event.target : event.srcElement;

   if(target === element){
   return true;
   }

return false;

}


function removeBgImg(elem){
 elem = detectObjectness(elem);
 elem.style.backgroundImage = 'none';
}


function addBgImg(elem, imgName){
 elem = detectObjectness(elem);
 elem.style.backgroundImage = "url(" + imgName + ")";
}

function toggleBgImg(elem, imgName){

 elem = detectObjectness(elem);

   if(elem.value.length > 0){
    removeBgImg(elem)   
   }

   else{
   addBgImg(elem, imgName);
   }
}

/* 
 rename this to changeBgColor ||
 merge it with add/removeBgImg to
 create a general-purpose changeBgElement script
*/ 
changeBg = function(oldElem, newElem){
 oldElem = detectObjectness(oldElem);
 newElem = detectObjectness(newElem);
 oldElem.style.backgroundColor = newElem.style.backgroundColor;
}


function changeLocation(url){
 location.href = url;
}

isEventType = function(eventType, event){
 event = (!event) ? window.event : event;
 return (event.type == eventType);
}


isKeyCode = function(codeNum, e){

e = e || window.event;

   if( isNaN(parseInt(codeNum)) ){
    return false;
   }

//var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
//var code = (e.keyCode) ? e.keyCode : ((e.charCode) ? e.charCode : ((e.which) ? e.which : 0));
var code = (e.keyCode) ? e.keyCode : ((e.which) ? e.which : ((e.charCode) ? e.charCode : 0));

return (code == codeNum);

}


handleKeyAction = function(keyState, codeNum, callback, event){

keyAction = (keyState.indexOf("key") == -1) ? ("key" + keyState) : keyState;
   
   if(isEventType(keyAction, event)){
   
      if(isKeyCode(codeNum, event)){
       callback();
      }
   }
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
*
* using resolveScope however, we could write the above as follows:
* for(var k = 0; k < thumbs.length; k++){
*  attachEventListener(thumbs[k], "click", resolveScope("selectImg", mediaIds[k]), false);
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



/*
* @create any HTML element/tag
* @author: michael orji
* @date: 28 sept, 2010
*
* @param: a configuration object specifying:
* the HTML element to be created: configObj.element property
* its attributes: configObj.attributes object, as well as 
* it's styling: the styling can be specified either as
* configObj.attributes.style property or encapsulated
* into a configObj.styleOptions object
*/
function createDynamicElem(configObj){

 var el = document.createElement(configObj.element);
 
   for(var i in configObj.attributes){
    el.setAttribute(i, configObj.attributes[i]);
   }

   /*
   * alternatively set styling options using 
   * configObj.styleOptions object or 
   * use the configObj.elem object above
   */
   for(var j in configObj.styleOptions){
    el.style[j] = configObj.styleOptions[j];
   }
 
 return el;

/*************
* USAGE EXAMPLE:
conf = {

  'element': 'input', //div, span, or any HTML tag

  'attributes': {
    'type': 'text',
    'id': 'value',
    'name': 'value', 
    'className': 'value',
    'style': 'display:none;backgroundColor:blue; ...'
   },


  
  //alternate styling object
  'styleOptions': {
   'display': 'none',
   'backgroundColor': 'blue',
   'position': 'absolute',
   'top': '2px',
   'left': '2px'
  }

 }

 var prevWin = createDynamicElem(conf);
 prevWin.innerHTML = 'this is not bad';
 document.body.appendChild(prevWin);
 prevWin.style.display = "block";
 prevWin.onmouseout = function(){hideElem(prevWin)};

********************************/

} //close the createDynamicElem function


/*
* gets the css style value of an element
*/
getStyleValue =  function (element, CSSProperty)
{
 var styleValue = (typeof element.currentStyle != "undefined") ? element.currentStyle : document.defaultView.getComputedStyle(element, null);   
 return styleValue[CSSProperty];
}



/* 
* keeps a draggable element from being dragged outside of
* the parent's boundary size or browser window boundary
* @author: Michael Orji
* @date: 2nd october, 2010
*/
function maintainBoundary(elem, parentElem){

   if(parentElem == null || parentElem == ''){
    parentElem = Browser;
    parentWidth = Browser.Size.width();
    parentHeight = getDocHeight(); //Browser.Size.height();
    
   }

   else{
    parentWidth = parseInt(getStyleValue(parentElem, 'width'));
    parentHeight = parseInt(getStyleValue(parentElem, 'height'));
   }

  var elemWidth = parseInt(getStyleValue(elem, 'width'));
  var elemHeight = parseInt(getStyleValue(elem, 'height'));
  var elemLeft = parseInt(elem.style.left);
  var elemRight = parentWidth - (elemLeft + elemWidth);
  var elemTop = parseInt(elem.style.top);
  var elemBottom = parentHeight - (elemTop + elemHeight);

   if((elemLeft + elemWidth) >= parentWidth){
    elem.style.left = parentWidth - elemWidth + 'px';
   }
   if((elemRight + elemWidth) >= parentWidth){
    elem.style.left = '0px';
   }
   if( (elemTop + elemHeight) >= parentHeight){
    elem.style.top = parentHeight - elemHeight + 'px';
   }
   if( (elemBottom + elemHeight) >= parentHeight){
    elem.style.top = '0px';
   }
}



function getElemSize(elem){
 return {width: elem.offsetWidth, height: elem.offsetHeight}
}

/*
* returns the scrollTop property of a document as rendered
* by any particular browser
*/
function scrollTop(){
 var scrollTop = (document.body.scrollTop) ? document.body.scrollTop : document.documentElement.scrollTop;
 return scrollTop;
}

/*
* returns the scrollLeft property of a document as rendered
* by any particular browser
*/
function scrollLeft(){
 var scrollLeft = (document.body.scrollLeft) ? document.body.scrollLeft : document.documentElement.scrollLeft;
 return scrollLeft;
}   

/*
* checks if the scrollbar of a scrollable element is down
* returns true if the scrollbar is down, false otherwise
* @author: michael orji
* @date oct 5, 2010, 03:16
*/
function isScrollDown(elem){
 elem = detectObjectness(elem);
   if(elem){
    return (elem.scrollHeight - elem.scrollTop <= elem.offsetHeight);
   }
}


function scrollToBottom(elem){
 elem = detectObjectness(elem);
 elem.scrollTop = elem.scrollHeight - elem.offsetHeight;
}


function changeBgColor(elem, color){
 elem = detectObjectness(elem);
 elem.style.backgroundColor = color;
}

function changeImageSrc(oldImg, newImg, newImgPath){
 oldImg = detectObjectness(oldImg);
 var newImgSrc = newImgPath + newImg;
 oldImg.src = newImgSrc;
}

swapContent = function(oldElem, newElem){
 oldElem = detectObjectness(oldElem);
 newElem = detectObjectness(newElem);
   if(oldElem && newElem){
    oldElem.innerHTML = newElem.innerHTML;
   }
}

function isVisible(elem){
 elem = detectObjectness(elem);
 
 return ( (elem.style.visibility != 'hidden') && (elem.style.display != 'none') );
 /*
   if(!elem.style.visibility && !elem.style.display){
    return true;
   }
   if( (elem.style.visibility) && (elem.style.visibility != 'hidden') ){
    return true;
   }
   if( (elem.style.display) && (elem.style.display != 'none') ){
    return true;
   }
 
  return false;
 */
 
}


function isDisplayed(elem){
 elem = detectObjectness(elem);
 return (elem.style.display != 'none');
}

function toggleElementVisibility(elem){
 elem = detectObjectness(elem);
   if(!isVisible(elem)){
    StyleManager.showElement(elem);
   }
   else{
    StyleManager.hideElement(elem);
   }
}



function setElemSize(elem, w, h) {
 elem = detectObjectness(elem);
 elem.style.width = w + 'px';
 elem.style.height = h + 'px';
}

function setWindowStatusMsg(msg){
 window.status = msg;
 return true;
}


/*
* @author: michael orji
* @date: 10 Nov, 2010 
* needs to be worked on since it's
* being used by the floatingWindow's create()
*/

function toggleMinimize(elem)
{
 elem = detectObjectness(elem);

   if(typeof elem.minimize !== 'undefined'){
    elem.minimize();
   }
   else{
    toggleElementVisibility(elem);
   }
}


/*
* @author: michael orji
*/
function inArray(needle, haystack, strict){ 
 var len = haystack.length;
   for(var i = 0; i < len; i++){
   
      if(strict){
         if(haystack[i] === needle){
          return true;
         }
      }
      else{
         if(haystack[i] == needle){
          return true;
         }
      }
   }
 return false;
}

/*
* @author: michael orji
* @date: 25 oct, 2010 16:41:26
*/
function removeFromArray(idOfArrayElementToRemove, arr){
  var arrayLength = arr.length;
   for(var i = 0; i < arrayLength; i++){
      if((arr[i] == idOfArrayElementToRemove) || (arr[i]['id'] == idOfArrayElementToRemove) || (arr[i].id == idOfArrayElementToRemove)){
       arr.splice(i,1);
       return;
      }
   }
}


//@date: 29 April, 2012
function getRandomArrayElement(arr, elementToExclude){

 var numOfElems = arr.length;
 var randomizer = Math.floor(numOfElems * Math.random());

   if(isEmpty(arr)){
    return null;
   }

   if(arr[randomizer] && (arr[randomizer] != elementToExclude) ){
    return arr[randomizer];
   } 
   else{
    getRandomArrayElement(arr, elementToExclude);
   } 
}

//returns the last element in an array, without removing that element from the array
//@date: 29 Apr, 2012
function getLastElementInArray(arr){
   if(isEmpty(arr)){
    return null;
   }
 return arr[arr.length-1];
}

//useful for strings and arrays at the moment
//TO DO: extend to apply to every possible object and data-type (except boolean)
//@date: 29 Apr, 2012
function isEmpty(obj){
 return obj.length == 0;
}

/*
   * javascript equivalent of the php array_walk funcion
   * works for both arrays and objects, 
   * @return type void;
   * @date: 04, sept, 2010;
   */
   function arrayWalk(arr, func){

      for(var x in arr){
       func(arr[x]);
      }
   }