//utf-8
/*
*@ copyright: michael orji
*/

function getPopUpChatWindow(windowId)
{
 var serverURL = webRootPath + "ajaxphp/ajax_chat_window_state_handler.php";
 var xhr = createXmlHttpRequestObject(); 
 var params="request=get_chat_window_state" +
 "&window_id=" + encodeURIComponent(windowId) +
 "&curr_userid=" + encodeURIComponent(uid); 
 makeXHRRequest(serverURL, xhr, function(){handleGetPopUpChatWindow(xhr)}, "POST", params, true);
}

function handleGetPopUpChatWindow(xhr)
{
   if(xhr.readyState == 4 && xhr.status == 200){
    var reply = xhr.responseText; 
    var replyObj = eval( '(' + reply + ')' );
   
   var winConf = {
                 'attributes': {
                  'className': 'IM',
                  'id': replyObj.windowId,
                  'title' : replyObj.windowTitle
	},
                 'options': {
                   'closable': false,
                   'minimizable': false,
                   'maximizable': false,
                   'detachable': false,
                   'resizable': false,	
                   'hasTitle': true
                  },               
                  'minZIndex': '11',
                  'maxZIndex': '15',
                  'styleOptions': {
                   'visibility': 'hidden',
                   'width': replyObj.width + ( (replyObj.width.indexOf('px') == -1) ? 'px' : '' ),
                   'height': replyObj.height + ( (replyObj.height.indexOf('px') == -1) ? 'px' : '' ),
                   'backgroundColor': bgcolor,
                   'backgroundImage': 'url(' + styleImgPath + 'bg.png)' //'url(' + themePath + 'bg.png)'
                  }
                }

       if(replyObj.windowId != 'IMWin'){winConf.sendMessageBox = replyObj.receiverId;}

       if( replyObj.closed == "T"){ 
          if( (WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )&& (WindowObject.getWindowAsObject(replyObj.windowId) != null) ){
           WindowObject.getWindowAsObject(replyObj.windowId).close();
          }
       }
       
       else if(replyObj.closed == "F") {
       
          if(WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )  {
             WindowObject.getWindowAsObject(replyObj.windowId).setPosition();
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
    
           if(replyObj.windowId == 'chatRoomWin'){
             var chatRoomWin = new FloatingWindow(winConf);
             chatRoomWin.setTopControlsContent(topControlsContent('chatroom'));
             chatRoomWin.setContent(chatRoomWinContent(replyObj.receiverId, replyObj.room, replyObj.windowId), false);
             chatRoomWin.setPosition({'positioning' : 'absolute', 'left' : '0', 'top' : '0'});
             chatRoomWin.addToDOM();
               if(typeof initWindow != 'undefined'){
                initWindow(chatRoomWin, "chatRoom", replyObj.receiverId, replyObj.room, true);
               }
               setTimeout(function(){placePopUpRepWin(replyObj, replyObj.windowId)}, 500);
            } //end ChatRoom
            else if( replyObj.windowId == 'PMWin_' + replyObj.receiverId){
               if(replyObj.receiverId > 0){
                var PMWin = new FloatingWindow(winConf);
                PMWin.setTopControlsContent(topControlsContent('PM', replyObj.receiverId));
                PMWin.setContent(PMWinContent(replyObj.receiverId), false);
                PMWin.setPosition({'positioning' : 'absolute', 'left' : '0', 'top' : '0'});
                PMWin.addToDOM();
                  if(typeof setPMWinTitle != 'undefined'){
                   PMWin.setTitle(setPMWinTitle(PMWin, replyObj.receiverId));
                  }
                  if(typeof initWindow != 'undefined'){
                   initWindow(PMWin, "PM", replyObj.receiverId, null, true);
                  }
                setTimeout(function(){placePopUpRepWin(replyObj, replyObj.windowId)}, 500);
              }
           } //end PM    
           } // end else if(!WindowObject.exists(WindowObject.getWindowAsObject(replyObj.windowId) ) )  ) 
       }  //end else if(replyObj.closed == "F")
   } //end if(xhr.readyState == 4...)
}

function placePopUpRepWin(xhrReplyObj, winId){
 var currWindow = WindowObject.getWindowAsObject(winId); 
   if(xhrReplyObj.minimized == 'T'){currWindow.minimize();}
   if(xhrReplyObj.maximized == 'T'){currWindow.maximize();}
}