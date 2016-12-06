//utf-8

function updateChatState(){
   var winId = WindowObject.getActiveWindow();
   var winObj = WindowObject.getWindowAsObject(winId);
   var responderId = '';
   winId += ''; //so that we can call the indexOf function on it, incase it returns a number -- (-1) when the window is not found
   
   if(winId.indexOf("chatRoomWin") != -1){
    var chatRoomWin = winObj; 
    windowType = "chatRoom";
    responderId = winObj.sendMessageBox;
   }
   else if(winId.indexOf("PMWin_") != -1){
    var PMWin = winObj;
    windowType = "PM";
    responderId = winObj.sendMessageBox;
   }
   else if(winId.indexOf("IMWin") != -1){
    var IMWin = winObj;  
    windowType = "IM";
   }
  registerChatWindowState(winObj, windowType, responderId, 'update');
}

function updateChatClose(){
   var winId = WindowObject.getActiveWindow();
   var winObj = WindowObject.getActiveWindowAsObject();
   var responderId = '';

   if(winId.indexOf("chatRoomWin") != -1){
    var chatRoomWin = winObj; 
    windowType = "chatRoom";
    responderId = winObj.sendMessageBox;
   }
   else if(winId.indexOf("PMWin_") != -1){
    var PMWin = winObj;
    windowType = "PM";
    responderId = winObj.sendMessageBox;
    PMWin.closedBy = uid;
   }
   else if(winId.indexOf("IMWin") != -1){
    var IMWin = winObj;  
    windowType = "IM";
   }
  registerChatWindowState(winObj, windowType, responderId, 'update');
 //deleteClosedChatWindow(winId); //defined in chatWinStateHandler.js
}