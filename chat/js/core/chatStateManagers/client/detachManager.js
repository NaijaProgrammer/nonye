var winToLoad = [];
var pwc = 0; //popup win counter
var detachedWindowsIds = [];

function detachIMWindow(){
 
 var winObject = WindowObject.getActiveWindowAsObject(); 
 var winId = winObject.getId(); //WindowObject.getActiveWindow();
 var winTitle = winObject.getTitle();
 var currDimensions = winObject.getSize();
 var currWidth = currDimensions.width;
 var currHeight = currDimensions.height;
 var xPos = (parseInt(Browser.Size.width()) - parseInt(currWidth))  * 0.5;
 var yPos = (parseInt(Browser.Size.height()) - parseInt(currHeight)) * 0.5;

 //var URL = './popup/popup.php?windowId=' + encodeURIComponent(winId) + '&title=' + encodeURIComponent(winTitle) + '&window=' + encodeURIComponent(pwc);
 var URL = appRootPath + 'popup/popup.php?windowId=' + encodeURIComponent(winId) + '&title=' + encodeURIComponent(winTitle) + '&window=' + encodeURIComponent(pwc);
 winToLoad[pwc] = winObject;
 winToLoad[pwc].id = winId;
 winToLoad[pwc].popup = window.open(URL, winId,  'left=' + xPos +',top=' + yPos + ',width=' +currWidth + ',height=' + currHeight + ',toolbar=0,location=0,status=0,menubar=0,resizable=0,scrollbars=0');
 pwc++;
}

function focusOtherWindowOnDetach(currentWindowId){

 var windowToFocusId = getLastElementInArray(openConversationsIDStack);

   /*
   * every window is detached, none to focus, 
   * also prevents infinite recursion in the call to focusNextWindowOnDetach() below
   */
   if(detachedWindowsIds.length == openConversationsIDStack.length){
    return;
   }

   if(windowToFocusId == null){// the array is empty
    return;
   }

   if( (!windowToFocusId) || (windowToFocusId == currentWindowId) ){
    windowToFocusId = getRandomArrayElement(openConversationsIDStack, currentWindowId);
   }

   if( (windowToFocusId) && !inArray(windowToFocusId, detachedWindowsIds)){ //only focus the window if it's not currently detached into its own window
    var winObject = WindowObject.getWindowAsObject(windowToFocusId); 
    WindowObject.setActiveWindow(windowToFocusId);
    winObject.focus(windowToFocusId, useStackView, 'IMWin');
   }
   else{
    focusOtherWindowOnDetach(currentWindowId);//call the function again until we get window to focus or until we are sure the array is empty
   }
}