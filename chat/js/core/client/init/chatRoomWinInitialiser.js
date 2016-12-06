//utf-8
function initialiseChatRoomWin(chatRoomWin, responderId, roomName, fromServer){

 includeJS([styleSheetPath + 'msgWinsThemeStyler.css'], true);

 objectify("chatRoomMessageWindow").style.width = (chatRoomWin.getSize().width - 175) + 'px';
 objectify("chatRoomMessageWindow").style.height = (chatRoomWin.getSize().height - 140) + 'px';
 objectify("textEditDiv").style.width = (chatRoomWin.getSize().width - 15) + 'px';
 objectify("textEditDiv").style.top = (chatRoomWin.getSize().height - 108) + 'px';
 objectify("textForm").style.top = (chatRoomWin.getSize().height - 80) + 'px';
 objectify(responderId).style.width = (chatRoomWin.getSize().width - 175) + 'px';
 objectify("roomUsersListPane").style.height = (chatRoomWin.getSize().height - 50) + 'px';
 objectify("send_button").style.left = (chatRoomWin.getSize().width - 218) + 'px';
 objectify("reset_button").style.left = '1px';
 objectify("chat_room_top_mid").style.width = (chatRoomWin.getSize().width - 18) + 'px';  
 if(objectify("chatRoomWin_sizer")){
  objectify("chatRoomWin_sizer").style.top = (chatRoomWin.getSize().height - 15) + 'px';
 }
 StyleManager.setStyle(objectify("chatRoomWin_creator_info"), {'top' : (chatRoomWin.getSize().height - 20) + 'px'});
 StyleManager.setStyle(objectify("chatRoomWin_creator_info"), {'left' : '80px'});

 initContextMenu('chatRoomWin_divContext');
 chatRoomWin.setTitle(chatRoomWin.getTitle(), 15); //global variable
 attachEventListener(objectify('chatRoomWin'), "mouseover", function(){initTextEditObjects(responderId); initImages(responderId);}, false); 
 attachEventListener(objectify('chatRoomWin'), "click", function(event){WindowObject.setActiveWindow('chatRoomWin'); chatRoomWin.focus('chatRoomWin', useStackView, 'IMWin');}, false);
 WindowObject.setActiveWindow(chatRoomWin.getId()); chatRoomWin.focus('chatRoomWin', useStackView, 'IMWin');
 initChatRoom(responderId, roomName, fromServer);

 (typeof getRooms != 'undefined') ? getRooms('toChatRoom') : addRooms('toChatRoom');
  
 setInterval(
  function(){
   if(chatRoomWin.element.moved){ 
      var chatRoomWinPos = getElemPos(chatRoomWin.element); 
       chatRoomWin.left = chatRoomWinPos.left;
       chatRoomWin.top = chatRoomWinPos.top;
       registerChatWindowState(chatRoomWin, "chatRoom", responderId, 'update'); //used also in custom/chatStateUpdateHandlers
        chatRoomWin.element.moved = false;
    }
   if(Effects.resized){
    chatRoomWin.handleResize(chatRoomWin.getId());
      if(parseInt(chatRoomWin.getSize().width) < 510){chatRoomWin.setWidth(510);}
      if(parseInt(chatRoomWin.getSize().height) < 150){chatRoomWin.setHeight(150);}
   }
   
  }, 
 00);

}


function initChatRoom(responderId, roomName, fromServer){
 crTextBox = objectify(responderId);
 initTextEditObjects(crTextBox); 
 crTextBox.focus();
 initImages(responderId);
   if(fromServer){loadRoom(roomName, true);}//defined in IMWin.js
   else{loadRoom(roomName);}
 retrieveMsgs('chatRoom'); //defined in chatInitialiser.js
}


function doUpdateChatRoom(){ //called inside IMWin.js and chatWinStateHandler.js
 if(typeof updateCurrentRoom != 'undefined'){updateCurrentRoom();} //defined in chatWinStateHandler.js
}


function leaveRoomOnClose(){
 var IMRoomHolder = objectify('IMRoomHolder');
 joinRoom('none');
 IMRoomHolder.innerHTML = "Select a chat room";

 //reset some global variables
 firstLoad = true;
 currRoom = 'none';
 currDisplayedUsers = [];
 rePopulateList = true;
 justJoinedRoom = true;
 
}


/*
function leaveRoom(roomToLeave)
{
  roomToLeave = roomToLeave || currRoom;

  var xhr = createXmlHttpRequestObject();
  var params = "request=leave_room" +
 "&chat_id=" + encodeURIComponent(lastMessageId) +
 "&room_leaver_id=" + encodeURIComponent(uid) +
 "&room_to_leave=" + encodeURIComponent(roomToLeave);
 
   if (xhr.readyState == 4 || xhr.readyState == 0){
    makeXHRRequest(serverURL, xhr, function(){handleleaveRoom(xhr)}, "POST", params, true);
   }
}

function handleleaveRoom(xhr){if ( (xhr.readyState == 4) && (xhr.status == 200) ){var reply = xhr.responseText;}}
*/