//utf-8
/*
*@ copyright: michael orji
*/
function registerNewChatWindowState(obj)
{ 
 obj = obj || {};
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject();  
 var winToRegister = ( (obj.windowId == 'chatRoomWin') ? 'chatRoomWin' : '')

 var params="request=register_new_chat_window_state" +
 "&window_id=" + encodeURIComponent(obj.windowId) +
 "&window_title=" + encodeURIComponent(obj.windowTitle) + 
 "&window_type=" + encodeURIComponent(obj.windowType) +
 "&last_message_id=" + encodeURIComponent(obj.lastMessageId) +
 "&starter_id=" + encodeURIComponent(obj.starterId) +
 "&starter_name=" + encodeURIComponent(obj.starterName) +
 "&receiver_id=" + encodeURIComponent(obj.receiverId) +
 "&receiver_name=" + encodeURIComponent(obj.receiverName) +
 "&open_time=" + encodeURIComponent(obj.openTime) +
 "&is_visible=" + encodeURIComponent(obj.isVisible) +
 "&focused=" + encodeURIComponent(obj.focused) +
 "&width=" + encodeURIComponent(obj.width) +
 "&height=" + encodeURIComponent(obj.height) +
 "&position_left=" + encodeURIComponent(obj.positionLeft) +
 "&position_top=" + encodeURIComponent(obj.positionTop) +
 "&position_right=" + encodeURIComponent(obj.positionRight) +
 "&position_bottom=" + encodeURIComponent(obj.positionBottom) +
 "&position_style=" + encodeURIComponent(obj.positionStyle);
  
  makeXHRRequest(serverURL, xhr, function(){handleRegisterChatWindowState(xhr, winToRegister)}, "POST", params, true); 
}

function handleRegisterChatWindowState(xhr, winToRegister)
{ 
   /*
  * we make sure an insert was successful b4 updating the current room (if the window just registered is a chat room window) 
  * subsequent updating of the current room is done by the code inside IMWin.js' loadRoom()
  * and also re-setting the newInsert cookie to true
  */
   if(xhr.readyState == 4 && xhr.status == 200){
      /*
      * only update the current room field in the database if the window just registered is a chat room window, 
      * this prevents updating the window with a value of 'undefined' consequently auto-taking us into an 'undefined' room
      * when the window is auto-loaded
      */
      if(winToRegister == 'chatRoomWin'){ 
       updateCurrentRoom(); //defined below and also called inside IMWin.js
      }
    Browser.setCookie('newInsert', 'true'); 
   }
}

function updateCurrentRoom()
{
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject();  

 var params="request=update_current_chat_room" +
 "&window_id=" + encodeURIComponent('chatRoomWin') +
 "&curr_userid=" + encodeURIComponent(uid) +
 "&last_message_id=" + encodeURIComponent(lastMessageId) +
 "&room=" + encodeURIComponent(roomToMoveTo);
  
  makeXHRRequest(serverURL, xhr, function(){handleupdateCurrentRoom(xhr)}, "POST", params, true);
  
}

function handleupdateCurrentRoom(xhr)
{
   if(xhr.readyState == 4 && xhr.status == 200) {
    Browser.setCookie('newRoomUpdate', 'true'); 
   }
}

function updateChatWindowState(obj)
{
 obj = obj || {};
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject();  

 var params="request=update_chat_window_state" +
 "&window_id=" + encodeURIComponent(obj.windowId) +
 "&curr_userid=" + encodeURIComponent(uid) +
 "&last_message_id=" + encodeURIComponent(obj.lastMessageId) +
 "&is_visible=" + encodeURIComponent(obj.isVisible) +
 "&focused=" + encodeURIComponent(obj.focused) +
 "&closed=" + encodeURIComponent(obj.closed) +
 "&close_time=" + encodeURIComponent(obj.closeTime) +
 "&closed_by=" + encodeURIComponent(obj.closedBy) +
 "&minimized=" + encodeURIComponent(obj.minimized) +
 "&maximized=" + encodeURIComponent(obj.maximized) +
 "&width=" + encodeURIComponent(obj.width) +
 "&height=" + encodeURIComponent(obj.height) +
 "&position_left=" + encodeURIComponent(obj.positionLeft) +
 "&position_top=" + encodeURIComponent(obj.positionTop) +
 "&position_right=" + encodeURIComponent(obj.positionRight) +
 "&position_bottom=" + encodeURIComponent(obj.positionBottom) +
 "&position_style=" + encodeURIComponent(obj.positionStyle);
  
  makeXHRRequest(serverURL, xhr, function(){handleUpdateChatWindowState(xhr)}, "POST", params, true);
  
}

function handleUpdateChatWindowState(xhr)
{
   //we make sure an update was successful b4 re-setting the newUpdate cookie to true
   if(xhr.readyState == 4 && xhr.status == 200) {
    Browser.setCookie('newUpdate', 'true'); 
   }
}


function getChatWindows()
{
 
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject(); 
 var params="request=get_chat_windows" +
 "&curr_userid=" + encodeURIComponent(uid);
   
  makeXHRRequest(serverURL, xhr, function(){handleGetChatWindows(xhr)}, "POST", params, true);
   
}

function handleGetChatWindows(xhr)
{
   if(xhr.readyState == 4 && xhr.status == 200){ 
    var reply = xhr.responseText;
    var replyObj = eval( '(' + reply + ')' ); 
      //for(var i = 0; i < replyObj.length; i++){    
      for(var i in replyObj){
       var winId = replyObj[i]; 
       if(typeof winId != 'undefined'){getChatWindowState(winId);}
      }
   }
}

function getChatWindowState(windowId)
{
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject(); 
 var params="request=get_chat_window_state" +
 "&window_id=" + encodeURIComponent(windowId) +
 "&curr_userid=" + encodeURIComponent(uid); 
 makeXHRRequest(serverURL, xhr, function(){handleGetChatWindowState(xhr)}, "POST", params, true);
}

function handleGetChatWindowState(xhr)
{
   if(xhr.readyState == 4 && xhr.status == 200){
    var reply = xhr.responseText; 
    var replyObj = eval( '(' + reply + ')' );

   var horizPosStyle = ((replyObj.positionLeft != 'undefined') ? 'left' : 'right');
   var horizPos = ((replyObj.positionLeft != 'undefined') ? replyObj.positionLeft : replyObj.positionRight);
   var vertPosStyle = ((replyObj.positionTop != 'undefined') ? 'top' : 'bottom');
   var vertPos = ((replyObj.positionTop != 'undefined') ? replyObj.positionTop : replyObj.positionBottom); 
   var winConf = {
                 'attributes': {
                  'className': 'IM',
                  'id': replyObj.windowId,
                  'title': replyObj.windowTitle
	},
                 'options': {
                   'closable': true,
                   'minimizable': true,
                   'maximizable': true,
                   'resizable': true,	
                   'hasTitle': true
                  },               
                  
                  'styleOptions': {
                   'visibility': 'hidden',
                   'width': replyObj.width + ( (replyObj.width.indexOf('px') == -1) ? 'px' : '' ),
                   'height': replyObj.height + ( (replyObj.height.indexOf('px') == -1) ? 'px' : '' ),
                   'backgroundColor': bgcolor,
                   'position' : replyObj.positionStyle,
                   horizPosStyle : horizPos + ( (horizPos.indexOf('px') == -1) ? 'px' : '' ),
                   vertPosStyle : vertPos + ( (vertPos.indexOf('px') == -1) ? 'px' : ''),
                   'backgroundImage': 'url(' + styleImgPath + 'bg.png)',
                   'zIndex': '11'
                  }, 
     
                  'topControlsBoxHeight' : '23px',
                  'minZIndex': minimumZIndex,
                  'maxZIndex': maximumZIndex, 
                  'resizeCallbacks': [handleIMWinResize],
                  'minimizeCallbacks' : [updateChatState],
                  'maximizeCallbacks' : [updateChatState],
                  'focusCallbacks' : [updateChatState],
                  'closeCallbacks' : [updateChatClose]
                }

       if( (replyObj.windowId == 'chatRoomWin') || ( replyObj.windowId == 'PMWin_' + replyObj.receiverId) ){
        winConf.loadCallbacks = [pushWindowOnOpenConversationsStackOnLoad],
        winConf.closeCallbacks.push(stopRetrievingMsgsOnClose);
        winConf.closeCallbacks.push(removeWindowFromOpenConversationsStack);
       }

       var newWinPosition  = null;
       if(replyObj.positionLeft != 'undefined'){
        newWinPosition  = {'positioning': replyObj.positionStyle, 'top': replyObj.positionTop, 'left': replyObj.positionLeft}
       }
       else if(replyObj.positionRight != 'undefined'){
        newWinPosition  = {'positioning': replyObj.positionStyle, 'bottom': replyObj.positionBottom, 'right': replyObj.positionRight}
       }
       if(replyObj.windowId != 'IMWin'){winConf.sendMessageBox = replyObj.receiverId;}

       if( replyObj.closed == "T"){ 
          if( (WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )&& (WindowObject.getWindowAsObject(replyObj.windowId) != null) ){
           setClosedWindow(replyObj.windowId);
           WindowObject.getWindowAsObject(replyObj.windowId).close(replyObj.windowId);
          }
        setTimeout(function(){deleteClosedChatWindow(replyObj.windowId);}, 1000); //defined below
       }
       
       else if(replyObj.closed == "F") {
       
          if(WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )  {
             WindowObject.getWindowAsObject(replyObj.windowId).setPosition(newWinPosition);
             if(replyObj.minimized == 'T'){
              WindowObject.getWindowAsObject(replyObj.windowId).minimize();
             }
             if(replyObj.maximized == 'T'){
              WindowObject.getWindowAsObject(replyObj.windowId).maximize();
             }
             /*
             * if we move to another chat room in the current browser window, 
             * we use this to update the same move in other browser windows that are open
             */
             if( (replyObj.windowId == 'chatRoomWin') && (replyObj.room != 'undefined') && (trim(replyObj.room) != trim(roomToMoveTo)) ){
              Browser.setCookie('roomLoadedFromServer', 'true'); //used in chatHandler.js' formatAlreadyInRoomMsg()
              loadRoom(replyObj.room, true); 
              setTimeout(function(){getRoomMembers(replyObj.room)}, 1000);
             }
          }
          else if(!WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) ) {

          if(replyObj.windowId == 'IMWin'){ 
             if(newWinPosition  != null){IMWinLocation = newWinPosition;}
             //winConf.attributes.title =  IMWinTitle; //redundant since the value is in chatGlobals.js and doesn't change
             winConf.closeCallbacks.push(stopGettingAndresetBuddiesArray);
             var IMWin = new FloatingWindow(winConf);
             IMWin.setTopControlsContent(topControlsContent('IM'));
             IMWin.setContent(IMWinContent(), false);
             IMWin.addToDOM();             
               if(typeof initWindow != 'undefined'){
                initWindow(IMWin, "IM", 0, null, true);
               }
              setTimeout(function(){placeRepWin(replyObj, replyObj.windowId)}, 1500);     
            } //end parent IM
            else if(replyObj.windowId == 'chatRoomWin'){
               if(newWinPosition != null){chatRoomWinLocation = newWinPosition;}
             winConf.options.detachable = true;
             winConf.detachCallbacks = [detachIMWindow];
             winConf.closeCallbacks.push(leaveRoomOnClose);
             var chatRoomWin = new FloatingWindow(winConf);
             chatRoomWin.setTopControlsContent(topControlsContent('chatroom'));
             chatRoomWin.setContent(chatRoomWinContent(replyObj.receiverId, replyObj.room, replyObj.windowId), false);
             chatRoomWin.addToDOM();
               if(typeof initWindow != 'undefined'){
                initWindow(chatRoomWin, "chatRoom", replyObj.receiverId, replyObj.room, true);
               }
               setTimeout(function(){placeRepWin(replyObj, replyObj.windowId)}, 1500);
            } //end ChatRoom
            else if( replyObj.windowId == 'PMWin_' + replyObj.receiverId){
               if(newWinPosition != null){PMWinLocation = newWinPosition;}
             winConf.options.detachable = true;
             winConf.detachCallbacks = [detachIMWindow];
               if(replyObj.receiverId > 0){
                var PMWin = new FloatingWindow(winConf);
                PMWin.setTopControlsContent(topControlsContent('PM', replyObj.receiverId));
                PMWin.setContent(PMWinContent(replyObj.receiverId), false);
                PMWin.addToDOM();
                  if(typeof initWindow != 'undefined'){
                   initWindow(PMWin, "PM", replyObj.receiverId, null, true);
                  }
                setTimeout(function(){placeRepWin(replyObj, replyObj.windowId)}, 2000);
              }
           } //end PM    
           } // end else if(!WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )  ) 
       }  //end else if(replyObj.closed == "F")
   } //end if(xhr.readyState == 4...)
}

function placeRepWin(xhrReplyObj, winId){
 var currWindow = WindowObject.getWindowAsObject(winId); 
   if(currWindow){
      if(xhrReplyObj.focused == 'T'){currWindow.focus(winId, useStackView, 'IMWin');}
      if(xhrReplyObj.minimized == 'T'){currWindow.minimize();}
      if(xhrReplyObj.maximized == 'T'){currWindow.maximize();}
   }
}

function deleteClosedChatWindow(windowId)
{
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject(); 
 var params="request=delete_closed_chat_window" +
 "&window_id=" + encodeURIComponent(windowId) + 
 "&curr_userid=" + encodeURIComponent(uid);
 makeXHRRequest(serverURL, xhr, function(){handleDeleteClosedChatWindow(xhr)}, "POST", params, true);
}

function handleDeleteClosedChatWindow(xhr){}