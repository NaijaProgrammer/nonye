//utf-8
/*
* the fromServer flag is used to determine if we are re-opening an existing window
* in a new window or when the browser is refreshed. If we are re-opening the window,
* then it means the window has already been registered in the database, (and that's the reason
* we are able to retrieve it and re-open it ) so we don't need to 
* re-register it.
* @date parameter added: 16 July, 2011 10:27 am
*/
function initWindow(windowObject, windowType, responderId, roomName, fromServer, hideOnInit){

 loadCSS([styleSheetPath + 'app_window_style', styleSheetPath + 'imwin_style', styleSheetPath + 'chatrooms_style', themeStylePath + 'chat_body', themeStylePath + 'window_controls', styleSheetPath + 'dropdowns_style']);
 includeJS([styleSheetPath + 'AppWinThemeStyler.css'], true);//overwrite, else, the style will not be applied to elements loaded after the jss generated style (sheet)
 
   if(windowObject.getId() == 'IMWin'){
    initialiseIMWin(windowObject, hideOnInit);  //defined in IMWinInitialiser.js
   }
   if(windowObject.getId() == 'chatRoomWin'){
     initialiseChatRoomWin(windowObject, responderId, roomName, fromServer);
   }
   if(windowObject.getId() == 'PMWin_' + responderId){
    initialisePMWin(windowObject, responderId);
   }

   //send the window's current state (properties) to the server
   if(!fromServer){
    setTimeout(function(){registerChatWindowState(windowObject, windowType, responderId, 'registerNew') ;}, 650);
   }

}

function retrieveMsgs(requestType){
  var getMsgs = setTimeout(function(){makeRequest(requestType);}, 1000);
   stopRetrievingMsgs = function()
   { 
    clearTimeout(getMsgs);
   }
}