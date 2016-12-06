function pushWindowOnOpenConversationsStackOnLoad(){
 var allWindows = WindowObject.getWindows();
 var winId = '';
 var winTitle = '';
 
   for(var i = 0; i < allWindows.length; i++){
      winId = trim(allWindows[i].id);
      winTitle = trim(allWindows[i].title);
      //only add window to stack of open conversations if window is neither the main IM window nor the floating user profile window
      if( (winId != 'IMWin') && (winId.indexOf('userProfileWin_') == -1) ){
       addWindowToOpenConversationsStack(winId, winTitle);
       reFillOpenConversationsStack();
      }
   }
}

function addWindowToOpenConversationsStack(winId, winTitle){ //counter-part: removeWindowFromOpenConversationsStack is defined in closedWindowManager.js
   if(!inArray(winId, openConversationsIDStack)){
    openConversationsIDStack.push(winId); 
    openConversationsStack.push({'windowId' : winId, 'windowTitle' : winTitle});
   }
}

function reFillOpenConversationsStack(){
 var activeWindows = DOMManager.getElementsOfClass('active_windows_list');
 var listStr = '';
   for(var j = 0; j < openConversationsStack.length; j++){
    var windowId = openConversationsStack[j].windowId;
    var windowTitle = openConversationsStack[j].windowTitle;   
    listStr += '<span class="active_windows_' + windowId + '"><a href="#" id="" class= "active_windows" title="' + windowTitle + '" onclick="detectDetachBeforeFocus(\'' + windowId + '\', useStackView, \'IMWin\'); StyleManager.hideElementsOfClass(\'active_windows_list\'); handleEvent(event); return false">' + windowTitle + '</a></span>';
    //listStr += '<a href="#" id="" class= "active_windows active_windows_' + windowId + '" title="' + windowTitle + '" onclick="detectDetachBeforeFocus(\'' + windowId + '\', useStackView, \'IMWin\'); StyleManager.hideElementsOfClass(\'active_windows_list\'); handleEvent(event); return false">' + windowTitle + '</a>';
   }
   for(var i = 0; i < activeWindows.length; i++){
    activeWindows[i].innerHTML = listStr;
   }
}

function detectDetachBeforeFocus(windowToFocusId, useStackView, exemption){

   if( !(WindowObject.getWindowAsObject(windowToFocusId).isDetached(windowToFocusId)) ){ //only focus this window in the current window if it is not detached
    WindowObject.getWindowAsObject(windowToFocusId).focus(windowToFocusId, useStackView, exemption);
   }
   else{
      for(var i in winToLoad){
         if(winToLoad[i].id == windowToFocusId){
          winToLoad[i].popup.focus(); //focus the popup window using global array variable reference winToLoad[] from detachManager.js
         }
      } 
   }
}