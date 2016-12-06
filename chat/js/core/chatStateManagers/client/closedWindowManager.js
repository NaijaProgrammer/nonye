function stopGettingAndresetBuddiesArray(){
 clearTimeout(buddiesGetter); //variable holding ref to setTimeout in buddiesHandler.js
 buddyIds = [];
 buddies = [];
}

function setClosedWindow(windowId){
 closedWindowId = windowId;
}
function getClosedWindow(){
 return closedWindowId; //global variable in chatGlobals.js
}

function removeWindowFromOpenConversationsStack(){
 
 var windowToRemoveId = getClosedWindow(); //defined above
 DOMManager.removeElementsFromParentNode('active_windows_' + windowToRemoveId);
 removeFromArray(windowToRemoveId, openConversationsIDStack);
   for(var i = 0; i < openConversationsStack.length; i++){
      if(openConversationsStack[i]['windowId'] == windowToRemoveId){
       removeFromArray(openConversationsStack[i], openConversationsStack);
      }
   }

 var windowToFocusId = getRandomArrayElement(openConversationsIDStack, windowToRemoveId) || 'IMWin';

   if(WindowObject.exists(windowToFocusId)){
    WindowObject.setActiveWindow(windowToFocusId);
    setTimeout(function(){new FloatingWindow().focus(windowToFocusId, useStackView, 'IMWin');}, 500)
   }
}

function updateReadMessages(){
   for(var i = 0; i < readMessagesIds.length; i++){
    updateReadStatus(readMessagesIds[i]); //update the message as read on the server, defined in chatHandler.js 
   }
}

function stopRetrievingMsgsOnClose()
{
  var windows = WindowObject.getWindows(true);
  var winsLength = windows.length;
  /*
  * since this function is run before the window is actually closed (see FloatingWindow.close/destroy )
  * the window is still among the windows retrieved by the call to WindowObject.getWindows() above
  * hence, if there are only two windows left, then, it is the current window(which we just closed and which calls this function before closing) and one other.
  * if that one other is the parent IMWin window, then we call the stopRetrievingMsgs function to prevent our script from trying to get chat messages from the server
  * when neither the chat room window or the private message (pm) window is open
  */
   if(winsLength == 2){
      for(var i = 0; i < winsLength; i++){
         if(windows[i].getId() == 'IMWin') {
          stopRetrievingMsgs(); //a closure defined in chatInitialiser.js
         }
      }
   }
}