/*
* library for handling custom context Menus
* @Author: michael Orji
* @encoding: utf-8
*
* TO DO : change to a 'CustomContextMenu' object [creator function]
*/

var mouseOverContext = false; //is the mouse over the context menu?
var customContextIsActive = false; //is the custom context menu currently displaying?

function initContextMenu(contextMenu){
 var contextMenuDiv = detectObjectness(contextMenu);
 contextMenuDiv.onmouseover=setMouseOverContext; 
 contextMenuDiv.onmouseout=unsetMouseOverContext; 
 contextMenuDiv.oncontextmenu=function(){return false;}
}

function setCustomContextActive(){
 customContextIsActive = true;
}

function unsetCustomContextActive(){
 customContextIsActive = false;
}

function setMouseOverContext(){
 mouseOverContext = true
} 

function unsetMouseOverContext(){
 mouseOverContext = false
} 

function contextMenuMouseDown(contextMenu, event){
 var contextMenuDiv = detectObjectness(contextMenu);
 event = EventManager.getEventObject(event);
  
   if(customContextIsActive && mouseOverContext){
    return;
   }  
   else if(Mouse.Button(event) == 'left button'){
    hideCustomContextMenu(contextMenuDiv, event);
   }
}

function hideCustomContextMenu(contextMenu, event){
 if( (customContextIsActive) && (mouseOverContext) && (!EventManager.targetElementTypeIs('a', event)) ){
  return;
 } 
 var contextMenuDiv = detectObjectness(contextMenu);
 if(contextMenuDiv){contextMenuDiv.style.display = 'none';}
 unsetMouseOverContext();
 unsetCustomContextActive();
}

function showCustomContextMenu(contextMenu, event)
{
   if(customContextIsActive || mouseOverContext){return;}
 event = EventManager.getEventObject(event);
 var contextMenuDiv = detectObjectness(contextMenu);
 var MP = Mouse.Position(event); 
 contextMenuDiv.style.display = 'none';
 contextMenuDiv.style.position = 'fixed';
 contextMenuDiv.style.zIndex =  '100000' 
 contextMenuDiv.style.left = ( parseInt(MP.left) + 7) + 'px';
 contextMenuDiv.style.top = ( parseInt(MP.top) + 7) + 'px'; 
 contextMenuDiv.style.display = 'block';
 setCustomContextActive();
 return false;
}