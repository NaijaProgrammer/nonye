//utf-8

/*
* all coding and design by michael orji
*/

function loadChatRoom(responderId, roomName) {
 setTimeout(function(){createChatRoomWin(responderId, roomName)}, 500);
}

function createChatRoomWin(responderId, roomName){

   conf = 
   {
      'attributes': 
      {
       'className': 'IM',
       'id': 'chatRoomWin',
       'title' : IMWinTitle + ' chatroom'
      },

      'options': 
      {
       'closable': true,
       'minimizable': true,
       'maximizable': true,
       'detachable': true,
       'draggable' : false,
       'resizable': true,
       'hasTitle' : true
      },

      'sendMessageBox': responderId,
      'topControlsBoxHeight' : '23px',
      'minZIndex': minimumZIndex,
      'maxZIndex': maximumZIndex,
      'loadCallbacks' : [pushWindowOnOpenConversationsStackOnLoad],
      'resizeCallbacks': [handleIMWinResize],
      'minimizeCallbacks' : [updateChatState],
      'maximizeCallbacks' : [updateChatState],
      'detachCallbacks' : [detachIMWindow],
      'focusCallbacks' : [updateChatState],
      'closeCallbacks' : [stopRetrievingMsgsOnClose, leaveRoomOnClose, updateChatClose, removeWindowFromOpenConversationsStack], //defined in chatInitialiser, chatRoomWinInitialiser, chatStateUpdateHandlers and closedWindowManager.js respectively

      'styleOptions': 
      {
       'width': '510px', 
       'height': '400px',
       'backgroundColor': bgcolor,
       'backgroundImage': 'url(' + styleImgPath + 'bg.png)',
       'backgroundRepeat': 'repeat',
       'border': '1px solid #42464b',
       'borderTop': 'none',
       'visibility': 'hidden',
       'zIndex': '11',
       'position' : chatRoomWinLocation.positioning,
       'right' : chatRoomWinLocation.right,
       'bottom' : chatRoomWinLocation.bottom
      }
   }

 var chatRoomWin = new FloatingWindow(conf);
 chatRoomWin.setTopControlsContent(topControlsContent('chatroom'));//defined in IMWin.js
 chatRoomWin.setContent(chatRoomWinContent(responderId, roomName, chatRoomWin.getId()), false);
 chatRoomWin.addToDOM();
    if(typeof initWindow != 'undefined'){
     initWindow(chatRoomWin, "chatRoom", responderId, roomName);
    }
}

function chatRoomWinContent(responderId, roomName, minimizeDivId){

 var uid = getChatUserId();

 var c = ' ' + 

 '<div id="chat_room_container">' +
  
   '<ul class="chat_rooms">' +
    '<li> <a href="#" id="roomHolder" class="roomHolder" onclick="toggleElementVisibility(\'roomsList\'); StyleManager.hideElement(\'fontList\'); StyleManager.hideElement(\'fontSizeList\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event); return false">' + roomName + '</a>'+
     '<div id="roomsList" class="roomsList">' + 
      '<a href="#" id="general" onclick="loadRoom(\'General\'); swapContent(\'roomHolder\', \'general\'); swapContent(\'IMRoomHolder\', \'roomHolder\'); StyleManager.hideElement(\'roomsList\'); crTextBox.focus(); handleEvent(event); return false"> General </a>' +
     '</div>' +
    '</li>' +
   '</ul>' +

   '<ul id="chatroom_active_windows_ul" class="active_windows_ul">' +
   '<li><a href="#" id="chatroom_active_windows_li" class="active_windows_li" onclick="toggleElementVisibility(\'chatroom_active_windows_list\'); handleEvent(event); return false">Open Conversations</a>'+
    '<div id="chatroom_active_windows_list" class="active_windows_list">' + 
     //'<a href="#" id="" class="active_windows" onclick="new FloatingWindow().focus(\'IMWin\'); StyleManager.hideElement(\'chatroom_active_windows_list\'); handleEvent(event); return false">IMWin</a>' +
     '</div>' +
   '</li>' +
  '</ul>' +

  '<div id="chatRoomMessageWindow">&nbsp;</div>' +

  '<form class="textForm" id="textForm">' +
   '<textarea wrap="soft" class="pm_text_box" id="' + responderId + '" autocomplete="off" style="color:#000000; font-family:Tahoma; font-size:12px; font-weight:normal; font-style:normal; text-decoration:none;" onkeydown="handleKeyAction(\'down\', 13, function(){sendMessage(' + responderId + ', \'chatRoom\')}, event)"></textarea> ' +
   '<!-- textare wrap="soft/hard"(INTERNET EXPLORER), wrap="virtual/physical"(NETSCAPE NAVIGATOR) -->' +
   '<input class="uid" type="hidden" value="' + uid + '" \><br />' +
   '<input type="button" id="send_button" class="send_button" value="send" onClick="sendMessage(' + responderId + ', \'chatRoom\'); return false" />' +
   '<input type="button" id="reset_button" class="reset_button" value="reset" onClick="resetChat(' + responderId + ', \'chatRoom\'); return false" />' +
  '</form>' +

  

  '<div id="textEditDiv" class="textEditDiv">' +
   
   '<div class="biu_imgs" id="biu_imgs">' +
    '<img name="b_' + responderId + '" id="b_' + responderId + '" src="' + textEditImgPath + 'bold_off.png" onmouseover="imgMouseOver(\'b_' + responderId + '\')" onmouseout="imgMouseOut(\'b_' + responderId + '\')" onclick="imgClick(\'b_' + responderId + '\'); setFontWeight(\'b_' + responderId + '\'); crTextBox.focus();" />' +
    '<img name="i_' + responderId + '" id="i_' + responderId + '" src="' +  textEditImgPath + 'italic_off.png"  onmouseover="imgMouseOver(\'i_' + responderId + '\')" onmouseout="imgMouseOut(\'i_' + responderId + '\')" onclick="imgClick(\'i_' + responderId + '\'); setFontStyle(\'i_' + responderId + '\'); crTextBox.focus();" />' +
    '<img name="u_' + responderId + '" id="u_' + responderId + '" src="' +  textEditImgPath + 'underline_off.png"  onmouseover="imgMouseOver(\'u_' + responderId + '\')" onmouseout="imgMouseOut(\'u_' + responderId + '\')" onclick="imgClick(\'u_' + responderId + '\'); setTextDecoration(\'u_' + responderId + '\'); crTextBox.focus();" />' +
   '</div>' +

   '<ul class="text_editor">' +

    '<li> <a href="#" class="fontHolder" id="fontHolder" onclick="toggleElementVisibility(\'fontList\'); StyleManager.hideElement(\'roomsList\'); StyleManager.hideElement(\'fontColourList\'); StyleManager.hideElement(\'fontSizeList\'); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false" > Tahoma </a>' +
     '<div class="fontsList" id="fontList">' +
      '<a href="#" id="arial" onclick="setFont(\'Arial\'); swapContent(\'fontHolder\', \'arial\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Arial </a>' +
      '<a href="#" id="CSMS" onclick="setFont(\'Comic Sans MS\'); swapContent(\'fontHolder\', \'CSMS\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Comic Sans MS </a>' +
      '<a href="#" id="CNew" onclick="setFont(\'Courier New\'); swapContent(\'fontHolder\', \'CNew\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Courier New </a>' +
      '<a href="#" id="garamond" onclick="setFont(\'Garamond\'); swapContent(\'fontHolder\', \'garamond\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Garamond </a>' +
      '<a href="#" id="georgia" onclick="setFont(\'Georgia\'); swapContent(\'fontHolder\', \'georgia\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Georgia </a>' +
      '<a href="#" id="impact" onclick="setFont(\'Impact\'); swapContent(\'fontHolder\', \'impact\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Impact </a>' +
      '<a href="#" id="tahoma" onclick="setFont(\'Tahoma\'); swapContent(\'fontHolder\', \'tahoma\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Tahoma </a>' +
      '<a href="#" id="TNR" onclick="setFont(\'Times New Roman\'); swapContent(\'fontHolder\', \'TNR\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Times New Roman </a>' +
      '<a href="#" id="verdana" onclick="setFont(\'Verdana\'); swapContent(\'fontHolder\', \'verdana\'); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"> Verdana </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="fontSizeHolder" id="fontSizeHolder" onclick="toggleElementVisibility(\'fontSizeList\'); StyleManager.hideElement(\'roomsList\'); StyleManager.hideElement(\'fontColourList\'); StyleManager.hideElement(\'fontList\'); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"> 12 </a>' +
     '<div class="fontSizeList" id="fontSizeList"> ' +
      '<a href="#" id="s8" onclick="setFontSize(8); swapContent(\'fontSizeHolder\', \'s8\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 8 </a>' +
      '<a href="#" id="s10" onclick="setFontSize(10); swapContent(\'fontSizeHolder\', \'s10\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 10 </a>' +
      '<a href="#" id="s12" onclick="setFontSize(12); swapContent(\'fontSizeHolder\', \'s12\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 12 </a>' +
      '<a href="#" id="s14" onclick="setFontSize(14); swapContent(\'fontSizeHolder\', \'s14\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 14 </a>' +
      '<a href="#" id="s16" onclick="setFontSize(16); swapContent(\'fontSizeHolder\', \'s16\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 16 </a>' +
      '<a href="#" id="s18" onclick="setFontSize(18); swapContent(\'fontSizeHolder\', \'s18\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 18 </a>' +
      '<a href="#" id="s20" onclick="setFontSize(20); swapContent(\'fontSizeHolder\', \'s20\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 20 </a>' +
      '<a href="#" id="s22" onclick="setFontSize(22); swapContent(\'fontSizeHolder\', \'s22\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 22 </a>' +
      '<a href="#" id="s24" onclick="setFontSize(24); swapContent(\'fontSizeHolder\', \'s24\'); StyleManager.hideElement(\'fontSizeList\'); crTextBox.focus(); handleEvent(event); return false"> 24 </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="fontColourHolder" id="fontColourHolder" style="background-color:#000000;" onclick="toggleElementVisibility(\'fontColourList\'); StyleManager.hideElement(\'roomsList\'); StyleManager.hideElement(\'fontList\'); StyleManager.hideElement(\'fontSizeList\'); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"> </a>' +
     '<div class="fontColourList" id="fontColourList">' +
      '<a href="#" id="c1" style="background-color:#b8b8b8;" onclick="setFontColour(\'c1\'); changeBg(\'fontColourHolder\', \'c1\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c2" style="background-color:#b4ad3b;" onclick="setFontColour(\'c2\'); changeBg(\'fontColourHolder\', \'c2\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c3" style="background-color:#bb5c54;" onclick="setFontColour(\'c3\'); changeBg(\'fontColourHolder\', \'c3\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c4" style="background-color:#755a5c;" onclick="setFontColour(\'c4\'); changeBg(\'fontColourHolder\', \'c4\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c5" style="background-color:#a95bef;" onclick="setFontColour(\'c5\'); changeBg(\'fontColourHolder\', \'c5\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c6" style="background-color:#d65a20;" onclick="setFontColour(\'c6\'); changeBg(\'fontColourHolder\', \'c6\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c7" style="background-color:#e39230;" onclick="setFontColour(\'c7\'); changeBg(\'fontColourHolder\', \'c7\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c8" style="background-color:#a71334;" onclick="setFontColour(\'c8\'); changeBg(\'fontColourHolder\', \'c8\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c9" style="background-color:#590099;" onclick="setFontColour(\'c9\'); changeBg(\'fontColourHolder\', \'c9\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c10" style="background-color:#d40088;" onclick="setFontColour(\'c10\'); changeBg(\'fontColourHolder\', \'c10\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c11" style="background-color:#0030ac;" onclick="setFontColour(\'c11\'); changeBg(\'fontColourHolder\', \'c11\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c12" style="background-color:#676f11;" onclick="setFontColour(\'c12\'); changeBg(\'fontColourHolder\', \'c12\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c13" style="background-color:#769321;" onclick="setFontColour(\'c13\'); changeBg(\'fontColourHolder\', \'c13\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c14" style="background-color:#3966fe;" onclick="setFontColour(\'c14\'); changeBg(\'fontColourHolder\', \'c14\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c15" style="background-color:#000000;" onclick="setFontColour(\'c15\'); changeBg(\'fontColourHolder\', \'c15\'); StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event);"> </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="emoteHolder" id="emoteHolder" onclick="toggleElementVisibility(\'emoteList\'); StyleManager.hideElement(\'roomsList\'); StyleManager.hideElement(\'fontList\'); StyleManager.hideElement(\'fontSizeList\');StyleManager.hideElement(\'fontColourList\'); crTextBox.focus(); handleEvent(event); return false"> <img id="CRWinEmote" src="' + emoteImgPath + 'mini_smile.gif" width="14" height="14" style="border:0;" /> </a>' +
     '<div class="emoteList" id="emoteList">' +
      '<a href="#" id=":smile:" onclick="setEmoticon(\':smile:\'); changeImageSrc(\'CRWinEmote\', \'smile.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'smile.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":angry:" onclick="setEmoticon(\':angry:\'); changeImageSrc(\'CRWinEmote\', \'angry.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'angry.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":cool:" onclick="setEmoticon(\':cool:\'); changeImageSrc(\'CRWinEmote\', \'cool.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'cool.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":cry:" onclick="setEmoticon(\':cry:\'); changeImageSrc(\'CRWinEmote\', \'cry.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'cry.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":embarassed:"  onclick="setEmoticon(\':embarassed:\'); changeImageSrc(\'CRWinEmote\', \'embarassed.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'embarassed.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":grin:" onclick="setEmoticon(\':grin:\'); changeImageSrc(\'CRWinEmote\', \'grin.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'grin.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":heart:" onclick="setEmoticon(\':heart:\'); changeImageSrc(\'CRWinEmote\', \'heart.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'heart.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":sad:" onclick="setEmoticon(\':sad:\'); changeImageSrc(\'CRWinEmote\', \'sad.png\', emoteImgPath); StyleManager.hideElement(\'fontList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'sad.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":serious:" onclick="setEmoticon(\':serious:\'); changeImageSrc(\'CRWinEmote\', \'serious.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'serious.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":silly:" onclick="setEmoticon(\':silly:\'); changeImageSrc(\'CRWinEmote\', \'silly.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'silly.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":tongue:" onclick="setEmoticon(\':tongue:\'); changeImageSrc(\'CRWinEmote\', \'tongue.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'tongue.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":wink:" onclick="setEmoticon(\':wink:\'); changeImageSrc(\'CRWinEmote\', \'wink.png\', emoteImgPath); StyleManager.hideElement(\'emoteList\'); crTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'wink.png" width="19" height="19" style="border:0;" /></a>' +
     '</div>' +
    '</li>' +

  '</ul>' +

  '</div> <!-- close the text_editor -->' +

   '<div class="errorDiv" id="errorDiv"> </div>' +

  '<div id="roomUsersListPane">' +
   '<ul id="roomUsersList" class="sortable box">' +
    '<li style="display:none"></li>' + 
    '<li id="roomUser_0" style="display:none"> </li>' + //dummy 'li' 
   '</ul>' +
  '</div>' +

  //'<div class="clear">&nbsp;</div>' + 

  '<div id="chatRoomWin_creator_info" class="creator_info msg_wins_creator_info">Powered by ' + appCreator + '</div>' +

  messengerCMenuContent('chatRoomWin') +  //load the custom context menu
  '</div> <!-- close the chat_room_container -->';

 attachEventListener(document, "click", function(event){hideCustomContextMenu('chatRoomWin_divContext', event); StyleManager.hideElement('roomsList'); StyleManager.hideElement('chatroom_active_windows_list');StyleManager.hideElementsOfClass('fontsList'); StyleManager.hideElementsOfClass('fontSizeList'); StyleManager.hideElementsOfClass('fontColourList'); StyleManager.hideElementsOfClass('emoteList');}, false);
 
 return c;

}