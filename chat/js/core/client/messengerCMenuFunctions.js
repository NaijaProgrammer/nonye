//@author: michael orji

/*
* initialises the links contained in the buddyCMenuContent() 
* so that we'll be able to pass the buddy id and other parameters
* to the functions attached to their event listeners 
*/ 
function initBuddyCMenuLinks(buddyId, type){

 var winType = ((type == 'forChatRoom') ? 'chatRoomWin' : 'IMWin')
 var contextMenuDiv = winType + '_divContext'; 

/*
* those in your im buddy list window are already your friends
* only some in chat room windows are to be added as friends
*/
var addFriendLink = (winType == 'chatRoomWin') ? '<hr \>' + '<li><a id="cmenu_add_friend_' + buddyId + '" href="#" onclick="hideCustomContextMenu(\'' + contextMenuDiv + '\', event); handleEvent(event);">Add as friend</a></li>' : '';

 detectObjectness(winType + '_cmenu' ).innerHTML = '' + 
 '<li><a id="cmenu_send_pm_' + buddyId + '" href="#" onclick="verifyAndLoadPMWin(' + buddyId + '); hideCustomContextMenu(\'' + contextMenuDiv + '\', event); handleEvent(event);">Ping</a></li>' +
 '<li class="topSep"></li>' +
 '<li><a id="cmenu_view_profile_' + buddyId + '" href="#" onclick="hideCustomContextMenu(\'' + contextMenuDiv + '\', event); handleEvent(event);">View profile</a></li>' +
  
 addFriendLink;

}

/*
* contents of the custom context menu loaded on right-click
* of a buddy in the IM buddy list window or in a chat room
* users-list window
*/ 
function messengerCMenuContent(winType){

loadCSS([styleSheetPath + 'cmenu']);

var c = '' + 
'<div id="' + winType + '_divContext" style="border:1px solid gray; display:none;">' +
 '<ul id="' + winType + '_cmenu" class="cmenu"></ul>' +
'</div>' +
'';
 return c;
}