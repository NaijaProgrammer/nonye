//utf-8

/*
* registers or updates the state of a chat window to the server
*/

function registerChatWindowState(chatWin, windowType, receiverId, registerType){
  
  var starterName = '';
  var receiverName = ''; 
  var room = roomToMoveTo;

   if(uid){getUserData(uid);} //see PMWin.js for (preferred) alternate way to achieve this
   if(receiverId){getUserData(receiverId);}   
   setTimeout(
    function(){ 
       if(usersDetails.length > 0){
          for(var x in usersDetails){
             if(usersDetails[x]['userId'] == uid){
              starterData = usersDetails[x];
              starterName = starterData.name;
             }
             else if(usersDetails[x]['userId'] == receiverId){
              receiverData = usersDetails[x];
              receiverName = receiverData.name;  
             }
          }
           
          if(registerType == 'registerNew'){
             IMRegConf = {
              'windowId' : chatWin.getId(), 
              'windowTitle' : chatWin.getTitle(),
              'windowType': windowType,
              'lastMessageId' : lastMessageId,
              'room' : room,
              'starterId': uid,
              'starterName' : starterName,
              'receiverId' : receiverId,
              'receiverName' : receiverName,
              'openTime' : null,
              'isVisible' : chatWin.isVisible,
              'focused' : chatWin.focused,
              'width' : chatWin.getSize().width, 
              'height' : chatWin.getSize().height, 
              'positionLeft': ( (chatWin.left) ? chatWin.left : chatWin.getPosition().left ), //if the window has been moved, then it is now positioned using top and left positioning, see effects.js
              'positionTop': ( (chatWin.top) ? chatWin.top : chatWin.getPosition().top ), //same as above
              'positionRight' : ( (!chatWin.left) ? chatWin.getPosition().right : 'undefined' ), //if top and left are defined, then no need for right and bottom
              'positionBottom' : ( (!chatWin.top) ? chatWin.getPosition().bottom : 'undefined' ),
              'positionStyle' : chatWin.getPosition().positioning 
             } 
            
            registerNewChatWindowState(IMRegConf); //defined in chatWinStateHandler
            
          }
          else if(registerType == 'update'){
             chatWin.isMaximized = ( (chatWin.widthMaximized && chatWin.heightMaximized) ? true : false);
             IMUpdtConfig = {
              'windowId' : chatWin.getId(),
              'windowTitle' : chatWin.getTitle(),
              'lastMessageId' : lastMessageId,
              'room' : room,
              'isVisible' : chatWin.isVisible,
              'focused' : chatWin.focused,
              'closed' : chatWin.closed,
              'closeTime' : '',
              'closedBy' : chatWin.closedBy,
              'minimized' : chatWin.isMinimized,
              'maximized' : chatWin.isMaximized,
              'width' : chatWin.getSize().width,
              'height' : chatWin.getSize().height,
              'positionLeft': ( (chatWin.left) ? chatWin.left : chatWin.getPosition().left ), //if the window has been moved, then it is now positioned using top and left positioning, see effects.js
              'positionTop': ( (chatWin.top) ? chatWin.top : chatWin.getPosition().top ), //same as above
              'positionRight' : ( (!chatWin.left) ? chatWin.getPosition().right : 'undefined' ), //if top and left are defined, then no need for right and bottom
              'positionBottom' : ( (!chatWin.top) ? chatWin.getPosition().bottom : 'undefined' ),
              'positionStyle' : chatWin.getPosition().positioning 
             }
           
           updateChatWindowState(IMUpdtConfig); //defined in chatWinStateHandler
          }
       }
    }, 1500);
}