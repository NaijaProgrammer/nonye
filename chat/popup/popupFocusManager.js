function stackOpenParentWindowsOnFocus(){
 var allWindows = self.opener.WindowObject.getWindows();
 var winId = '';
 var winTitle = '';
 
   for(var i = 0; i < allWindows.length; i++){
      winId = trim(allWindows[i].id + '');
      winTitle = trim(allWindows[i].title + '');
      //only add window to stack of open conversations if window is neither the main IM window nor the floating user profile window
      if( (winId != 'IMWin') && (winId.indexOf('userProfileWin_') == -1) ){
         if(!inArray(winId, openConversationsIDStack)){
          addWindowToOpenConversationsStack(winId, winTitle);
         }
      }
   }
}

function addWindowToOpenConversationsStack(winId, winTitle){
 openConversationsStack.push({'windowId' : winId, 'windowTitle' : winTitle});
 openConversationsIDStack.push(winId); 
 reFillOpenConversationsStack();
}

function reFillOpenConversationsStack(){
 var activeWindows = DOMManager.getElementsOfClass('active_windows_list');
 var listStr = '';
   for(var j = 0; j < self.opener.openConversationsStack.length; j++){
    var windowId = self.opener.openConversationsStack[j].windowId;
    var windowTitle = self.opener.openConversationsStack[j].windowTitle;   
    listStr += '<a href="#" id="" class= "active_windows active_windows_' + windowId + '" title="' + windowTitle + '" onclick="self.opener.detectDetachBeforeFocus(\'' + windowId + '\', useStackView, \'IMWin\'); StyleManager.hideElementsOfClass(\'active_windows_list\'); handleEvent(event); return false">' + windowTitle + '</a>';
   }
   for(var i = 0; i < activeWindows.length; i++){
    activeWindows[i].innerHTML = listStr;
   }
}