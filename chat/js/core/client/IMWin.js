//utf-8

/*
* all coding and design by michael orji
*/

function loadIMWin(hideOnInit){setTimeout(function(){createIMWin(hideOnInit)}, 500)}

function createIMWin(hideOnInit){

  conf = {

   'attributes': {
    'className': 'IM',
    'id': 'IMWin',
    'title' : 'IMWin'
   },

   'options': 
      {
       'closable': true,
       'minimizable': true,
       'maximizable': true,
       'detachable': false,
       'resizable': true,
       'draggable' : false,
       'hasTitle': true
      },

   'styleOptions': {
    'visibility': 'hidden', 
    'width': '210px',
    'height': (Browser.Size.height() - 60) + 'px',
    'backgroundColor': bgcolor,
    'backgroundImage': 'url(' + styleImgPath + 'bg.png)',
    'zIndex': '11',
    'position' : IMWinLocation.positioning,
    'right' : IMWinLocation.right,
    'bottom' : IMWinLocation.bottom
   },
   
   'topControlsBoxHeight' : '23px',
   'minZIndex': minimumZIndex,
   'maxZIndex': maximumZIndex,
   'resizeCallbacks': [handleIMWinResize], //defined in chatResizeHandler.js
   'minimizeCallbacks' : [updateChatState],
   'maximizeCallbacks' : [updateChatState],
   'closeCallbacks' : [stopGettingAndresetBuddiesArray, updateChatClose], //defined in closedWindowManager, and chatInitialiser.js respectively
   'focusCallbacks' : [updateChatState],
   'showCallbacks' : [updateChatState]
}

 var IMWin = new FloatingWindow(conf);
 IMWin.setTopControlsContent(topControlsContent('IM'));
 IMWin.setContent(IMWinContent(), false);
 IMWin.addToDOM();
    if(typeof initWindow != 'undefined'){
     initWindow(IMWin, "IM", 0, null, false, hideOnInit);
    }
}

function loadRoom(room, loadedFromServer){
 roomToMoveTo = room;
 (!document.getElementById('chatRoomWin')) ? loadChatRoom(0, room) : joinRoom(room);
   if(!loadedFromServer){ 
      if( (document.getElementById('chatRoomWin')) && (typeof doUpdateChatRoom != 'undefined') ){
       setTimeout(function(){doUpdateChatRoom();}, 500);  //defined in chatRoomWinInitialiser.js
      }
   }
   if(loadedFromServer){
    //change the rooms drop down of the chat room window to the room the person is in ( as returned by the server)
    objectify('roomHolder').innerHTML = room;
    //change the 'select a room' drop down of the IMWindow to the room the person is in
    doCustomSwap('IMRoomHolder', 'roomHolder'); //defined below
       //use this to prevent the 'you are already in this room' message from displaying if we're opening the window based on response from the server
       if(!suppressAlreadyInRoomMsg){
        Browser.setCookie('suppressAlreadyInRoomMsg', 'true'); 
      }
   }
}

/*
* handles the case when we do the swap
* without having loaded the chatRoomWin
* so that the roomHolder swaping doesn't give errors
*/
function doCustomSwap(oldElem, newElem){
   oldElem = detectObjectness(oldElem);
   newElem = detectObjectness(newElem);
   if(oldElem && newElem){
    swapContent(oldElem, newElem);
   }
}

function topControlsContent(winType, responderId){
 
 var mainId = '';
 var topMidId = '';
 var topMidClassExt = '';

   if(winType == 'IM'){
    mainId = 'im_dragger';
    topMidId = "im_top_mid";
   }
   else if(winType == 'chatroom'){
    mainId = "chat_room_dragger";
    topMidId = "chat_room_top_mid";
   }
   else if(winType == 'PM'){
    mainId = "pm_dragger_" + responderId;
    topMidId = "pm_top_mid_" + responderId;
    topMidClassExt = "pm_top_mid";
   }

 var c = '' +
 '<div class="window_dragger" id="' + mainId + '">' +
  '<div class="top_divs top_left"> </div>' +
  '<div class="top_divs top_mid ' + topMidClassExt + '" id="' + topMidId + '"> </div>' +
  '<div class="top_divs top_right"> </div>' +
 '</div>' +
 '';
 return c;

}


function IMWinContent(){

 var uid = getChatUserId();

 var c = ' ' + 

 '<div id="im_container">' +

  '<ul class="chat_rooms" id="im_chat_rooms">' +
   '<li><a href="#" id="IMRoomHolder" class="roomHolder" onclick="toggleElementVisibility(\'IMRoomsList\'); handleEvent(event); return false"> Select a chat room </a>'+
    '<div id="IMRoomsList">' + 
     '<a href="#" id="IM_General" onclick="loadRoom(\'General\'); swapContent(\'IMRoomHolder\', \'IM_General\'); doCustomSwap(\'roomHolder\', \'IMRoomHolder\'); StyleManager.hideElement(\'IMRoomsList\'); return false"> General </a>' +
     '</div>' +
   '</li>' +
  '</ul>' +

  '<ul id="im_active_windows_ul" class="active_windows_ul">' +
   '<li><a href="#" id="im_active_windows_li" class="active_windows_li" onclick="toggleElementVisibility(\'im_active_windows_list\'); handleEvent(event); return false">Open Conversations</a>'+
    '<div id="im_active_windows_list" class="active_windows_list">' + 
     //'<a href="#" id="" class="active_windows" title="IMWin" onclick="new Floatingwindow().focus(\'IMWin\'); StyleManager.hideElement(\'im_active_windows_list\'); handleEvent(event); return false">IMWin</a>' +
     '</div>' +
   '</li>' +
  '</ul>' +

  '<input class="uid" type="hidden" value="' + uid + '" \>' +

  '<div id="buddy_list_holder">' +
   '<ul id="buddylist" class="sortable box">' +
    '<li style="display:none"></li>' + 
    '<li id="buddyList_0"> </li>' + //dummy 'li' used when we call removeBuddy()
   '</ul>' +
  '</div>' +

  //'<div class="clear">&nbsp;</div>' + 

  '<div id="IMWin_creator_info" class="creator_info">Powered by ' + appCreator + '</div>' +

 messengerCMenuContent('IMWin') +  //load the custom context menu
 '</div> <!-- close the im_container -->' +

 attachEventListener(document, "click", function(event){hideCustomContextMenu('IMWin_divContext', event); StyleManager.hideElement('IMRoomsList'); StyleManager.hideElement('im_active_windows_list'); StyleManager.hideElementsOfClass('fontsList'); StyleManager.hideElementsOfClass('fontSizeList'); StyleManager.hideElementsOfClass('fontColourList'); StyleManager.hideElementsOfClass('emoteList');}, false);

 return c;

}