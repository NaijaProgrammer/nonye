//utf-8
/*
* all coding and design by michael orji
* these are core functions that control the functionality of  the ajax im application
*/

//gets the current user id
if(!uid){ //only re-define the uid if it's not already got
 (function(){ 
 var inputs = document.getElementsByTagName('input');
   for(var i in inputs){
      if(inputs[i].className == 'uid'){ //found in core/client/IMWin.js
       uid = inputs[i].value;
       return;
      }
   }
 })();
}

//the functions defined in core/async/dbRoomsHandler.js call this
function addRooms(requestType){
   for(var i in rooms){
    var roomId = rooms[i].room_id;
    var roomName = rooms[i].room_name; 
    addRoom(requestType, roomId, roomName);      
   }
}

function addRoom(requestType, roomId, roomName){

   if(requestType == 'forIM'){
    objectify('IMRoomsList').innerHTML += '' + 
   '<a href="#" id="IM_' +  roomName + '" onclick="loadRoom(\'' + roomName + '\'); swapContent(\'IMRoomHolder\', \'IM_' + roomName + '\'); swapContent(\'roomHolder\', \'IMRoomHolder\'); StyleManager.hideElement(\'IMRoomsList\'); return false">' + roomName + '</a>';
   }
   else if(requestType == 'toChatRoom'){
    objectify('roomsList').innerHTML += '' + 
   '<a href="#" id="' +  roomName + '" onclick="loadRoom(\'' + roomName + '\'); swapContent(\'roomHolder\', \'' + roomName + '\'); swapContent(\'IMRoomHolder\', \'roomHolder\'); StyleManager.hideElement(\'roomsList\'); return false">' + roomName + '</a>';
   }
}


function joinRoom(roomToJoin)
{
 var roomHolder = objectify('roomHolder');
 currRoom = roomHolder.innerHTML;
 
 var params = "request=join" +
 "&chat_id=" + encodeURIComponent(lastMessageId) +
 "&joiner_id=" + encodeURIComponent(uid) +
 "&curr_room=" + encodeURIComponent(roomHolder.innerHTML) +
 "&room_to_join=" + encodeURIComponent(roomToJoin);

  messageCache.push(params);
} 

/*
* we add 1 to the lastMessageId here
* because we call this function whenever
* a user joins or leaves a room.
* Now, there's no makeRequest() call
* b/w the joining of the room and the call
* to getRoomMembers, so if we don't add 1 
* to the lastMessageId, it will send the same
* lastMessageId sent when the person joined the room
* so, the server will return the same message twice
*/
function getRoomMembers(room){
 var params= "request=get_room_members" +
 "&chat_id=" + encodeURIComponent(lastMessageId+1) +
 "&room=" + encodeURIComponent(room);
 messageCache.push(params);
}


function sendMessage(receiverId, requestType)
{
 errorDiv = objectify('errorDiv_' + receiverId);
 var textBox = objectify(receiverId);
 var roomTo = 'none';

   if(requestType == 'chatRoom'){
     roomTo = objectify('roomHolder').innerHTML;
     errorDiv = objectify('errorDiv');
   }
   // don't send void messages
   if( trim(textBox.value) != "" )
   {
     var params = "request=send" +
     "&chat_id=" + encodeURIComponent(lastMessageId) +
     "&user_id=" + encodeURIComponent(uid) +
     "&receiver_id=" + encodeURIComponent(receiverId) +
     "&room=" + encodeURIComponent(roomTo) +
     "&message=" + encodeURIComponent(textBox.value) +
     "&font=" + encodeURIComponent(textBox.style.fontFamily) +
     "&fontsize=" + encodeURIComponent(textBox.style.fontSize) +
     "&fontcolour=" + encodeURIComponent(textBox.style.color)+
     "&fontweight=" + encodeURIComponent(textBox.style.fontWeight) +
     "&fontstyle=" + encodeURIComponent(textBox.style.fontStyle) +
     "&textdecoration=" + encodeURIComponent(textBox.style.textDecoration);
     
     //add the message to the queue
     messageCache.push(params);
     textBox.value = "";  
     errorDiv.innerHTML = ""; 
     StyleManager.hideElement(errorDiv, true);
     textBox.focus();
   }
   else{
    errorManager.displayError({'Error' : 'you cannot send an empty message'}, errorDiv, true);
    StyleManager.showElement(errorDiv, 'display');
    setTimeout(function(){errorDiv.innerHTML = ''; StyleManager.hideElement(errorDiv, true);}, 5000);
   }
}


function resetChat(receiverId, requestType){
 var textBox = objectify(receiverId);
 messageWindow = detectObjectness(messageWindow); 
 messageWindow.innerHTML = "";
 textBox.value = "";
 textBox.focus();
 retrieveMsgs(requestType); //defined in init/chatInitialiser.js							
}


/* makes asynchronous request to retrieve new messages, post new messages, delete messages */
function makeRequest(requestType)
{
 var xhr = createXmlHttpRequestObject();
 var currentRoom = 'none';
 
   if( (requestType == 'chatRoom') && (WindowObject.exists(WindowObject.getWindowAsObject('chatRoomWin') ) ) ){ 
     currentRoom = objectify('roomHolder').innerHTML;
   }
   if(xhr)
   {
         // don't start another server operation unless the previous one is complete or none is in progress
         if (xhr.readyState == 4 || xhr.readyState == 0)
         {
           var params = "";
            // if there are requests stored in queue, take the oldest one
            if (messageCache.length > 0){
             params = messageCache.shift();
            }
            // if the messages cache is empty, just retrieve new messages
            else{
             params = "request=retrieve" + 
             "&user_id=" + encodeURIComponent(uid) +
             "&room=" + encodeURIComponent(currentRoom) +
             "&chat_id=" + encodeURIComponent(lastMessageId);
            }

          makeXHRRequest(serverURL, xhr, function(){handleServerResponse(requestType,xhr)}, "POST", params, true);
         }
         else
         {
         // check again for new messages
         retrieveMsgs(requestType); //defined in int/chatInitialiser.js
         }
   }
}


/* function that handles the http response when updating messages */
function handleServerResponse(requestType,xhr)
{
   if ( (xhr.readyState == 4) && (xhr.status == 200) )
   {
    readServerResponse(requestType,xhr); 
   }
}

/* 
* function that processes the server's response when updating messages 
* in the message window of the chat application
*/
function readServerResponse(requestType,xhr)
{
 var reply = xhr.responseText;
 var listPane = objectify('roomUsersListPane');
 var response = '';

  if(reply.indexOf('users') != -1){
   response = eval( '(' + processUsers(reply) + ')' );
  } 
      
   //if the response consists of only messages
   else{ 
    reply = "(" + reply + ")";
    response = eval(reply);
   }

  for(var j in response.messages){

    var sender_id = response.messages[j].user_id;
    var sender_name = response.messages[j].user_name;
    var receiver_id = response.messages[j].receiver_id;
    var receiver_name = response.messages[j].receiver_name;
    var room = response.messages[j].room;
    var room_leaver_id = response.messages[j].room_leaver_id;
    var room_leaver_name = response.messages[j].room_leaver_name;
    var message = response.messages[j].message;
    var font = response.messages[j].font;
    var fontsize = response.messages[j].fontsize;
    var fontcolour = response.messages[j].fontcolour;
    var fontweight = response.messages[j].fontweight;
    var fontstyle = response.messages[j].fontstyle;
    var textdecoration = response.messages[j].textdecoration;
    var time = response.messages[j].posted_on;
    var message_id = response.messages[j].chat_id;
    var read_status = response.messages[j].read_status;
    var recipient_was_online = response.messages[j].recipient_online;
    lastMessageId = message_id; 

    /*
    * process joining and leaving rooms
    */ 
    if(((message.indexOf("joined the " + room + " room") != -1) && (sender_id > 0))||((message.indexOf("you are already in this room") != -1) && (sender_id <= 0))){
     message = onRoomJoinCall(message, sender_id, sender_name, room);
    }
    if( (message.indexOf("left the " + room + " room") != -1)  && (sender_id <= 0) ){
     message = onRoomLeaveCall(message, room_leaver_id, room_leaver_name);
    }

    message = strToEmote(message, emoteList(), emoteImgPath);

      //get offline messages
      if( (receiver_id == uid) && (message != 'you are already in this room') && (recipient_was_online == 'false') ){
       verifyAndLoadPMWin(sender_id);
       messageWindow = 'pm_messageWindow_' + sender_id; 
       firstTimeLoad = false;
      }
      
      /*
      * open a new window if someone has PM'd current user
      * but not if the message is from the system
      * that the user is already in the room cos all PM users
      * are in the room 'none';
      * set the window in which PM messages will appear;
      */
      if( (receiver_id == uid) && (message != 'you are already in this room') ){
       verifyAndLoadPMWin(sender_id);
       messageWindow = 'pm_messageWindow_' + sender_id; 
      }
      /*
      * prevent the first PM you send from displaying in the 
      * chat rooms window
      */
      else if( (receiver_id != uid) && (receiver_id != 0) ){
       messageWindow = 'pm_messageWindow_' + receiver_id;
      }
      else{ 
         /* 
         * handle cases when we load PM before chat room window
         * since in these cases, there'd be no 'chatRoomMessageWindow'
         * in the DOM, the receiver_id check prevents a chat room message
         * from appearing in the PM window (when both are loaded) and ensures
         * also that the user sees the 'you are already in this room' message 
         */
         if( (requestType == 'chatRoom') && ( (receiver_id <= 0) || (receiver_id == uid) ) ){
          messageWindow = 'chatRoomMessageWindow';
         }
      }   
  
    font = checkMultiWordFont(font); 
      if(message != ''){
       formatMessages(message_id, sender_id, sender_name, receiver_id, receiver_name, room, message, font, fontsize, fontcolour, fontweight, fontstyle, textdecoration, time, lastMessageId, messageWindow);         
      }
   }     

  // restart sequence
 setTimeout(function(){makeRequest(requestType)}, interval); 
   
}


function processUsers(serverReply){

 var users = serverReply.indexOf('users');
   
   //if part of the response is users' names (this happens when a user joins a room)
   if(users != -1){
     
    //extract the json text that refers to names of users
    var user = users - 2;
    var uReply = serverReply.substring(user, serverReply.length);

    //convert the json response into a javascript accessible object
    var uResponse = eval( '(' + uReply + ')' );

      for(var i in uResponse.users){
       userId = uResponse.users[i].user_id;
       userName = uResponse.users[i].user_name;
       formatUserDisplay(userName, userId); 
       //addRoomUser(userId, userName);     //alternate way to add a new room user to the room users' list
      }

    //extract the json text that refers to messages
    var msgs = serverReply.indexOf('messages');
    var msg = msgs - 2; 
    var mReply = serverReply.substring(msg, user);

    return mReply; 
   }
}


function formatAlreadyInRoomMsg(message, receiver_name, room){

      if(suppressAlreadyInRoomMsg){ //global variable in chatGlobals.js
       message = '';
      }
      else if(!suppressAlreadyInRoomMsg){
         if(Browser.getCookie('suppressAlreadyInRoomMsg') == 'true'){ //created in IMWin.js' loadRoom() function, see that script for how this works
          message = '';
          Browser.setCookie('suppressAlreadyInRoomMsg', 'false');
         }
      }
      /* 
      * when we update the chat room window based on the response from the server,
      * the 'joined the room' message of the current user is not displayed because the user is seen 
      * as being already in the room, rather the 'already in the room' message is supposed to be displayed
      * but that may have been suppressed, either by the global suppressAlreadyInRoomMsg or the  formatAlreadyInRoomMsg() function 
      * we use this conditional statement to ensure the 'joined the room' message is displayed both in the browser from which the current user joined
      * and in the window that is dynamically updated based on the ajax server response
      * @date added: July 16, 2011: 18:50
      * @date updated: July 22, 2011
      */ 
      if(Browser.getCookie('roomLoadedFromServer') == 'true'){
       message = receiver_name + ' has joined the ' + room + ' room';
       Browser.setCookie('roomLoadedFromServer', 'false');
      }
  return message;
}

/* function that formats and appends new messages to the chat list*/
function formatMessages(message_id, sender_id, sender_name, receiver_id, receiver_name, room, message, font, fontsize, fontcolour, fontweight, fontstyle, textdecoration, time, lastMessageId, messageWindow)  
{
   var htmlMessage = "";

   // ensure we don't display the same message twice 
   if(!inArray(message_id, currDisplayedMessages)){

      // compose the HTML code that displays the message
      if( (message.indexOf("joined the " + room + " room") != -1) || 
          ( (message.indexOf("you are already in this room") != -1) && (sender_id <= 0) ) ||
          ( (message.indexOf("left the " + room + " room") != -1)) && (sender_id <= 0) ){
        sender_name = '';
        fontstyle = 'italic';
        fontsize = '11px';
        font = 'verdana';
      }

      /* 
      * when we update the already existing chat room window based on the response from the server,
      * the 'joined the room' message of the current user is not displayed because the user is seen 
      * as being already in the room, rather the 'already in the room' message is supposed to be displayed
      * but that may have been suppressed, either by the global suppressAlreadyInRoomMsg or the  formatAlreadyInRoomMsg() function 
      * we use this conditional statement to ensure the 'joined the room' message is displayed both in the browser from which the current user joined
      * and in the window that is dynamically updated based on the ajax server response
      * @date added: July 16, 2011: 18:50
      */ 
       if( (message.indexOf('you are already in this room') != -1) && (sender_id <= 0) ){
        //message =  sender_name + ' has joined the ' + room + ' room';
        message = formatAlreadyInRoomMsg(message, receiver_name, room);
       }
       //only display the message if it is not empty
       if(message){
        htmlMessage += "<span style='color:blue; font-family:verdana, sans-serif; font-weight:bold; font-size:11px;'>" +
                     "[" + time + "] " + sender_name + " : " +
                     "</span>";
         htmlMessage += "<span style='" +
                     "font-family:" + font + ",sans-serif;" + 
                     "font-size:" + fontsize + ";" +
                     "color:" + fontcolour + ";" +
                     "font-weight:" + fontweight + ";" +
                     "font-style:" + fontstyle + ";" +
                     "text-decoration:" + textdecoration + ";" +               
                     "'>"
         htmlMessage += message;
         htmlMessage += "<br \>";
         htmlMessage += "</span>"; 

         currDisplayedMessages.push(message_id);
         /*
         * handle Firefox's delay in loading
         * the 'verifyAndLoadPMWin(sender_id)' 
         * (called in 'readServerResponse' above) 
         * into the document Node
         */
         setTimeout(function(){displayMessage(htmlMessage, messageWindow, message_id, receiver_id)}, interval);

      } //close the 'if(message)'
   } 
}

function displayMessage(message, messageWindow, messageId, receiverId)
{ 
   /*
   * prevent the 'you are already in this room' message
   * from appearing in a PM window
   * this is cos all PM messages has the room 'none'
   * as the room to which their message goes
   * so it sees them as being in that room
   */   
    if( (messageWindow.indexOf('pm_messageWindow_') != -1) && (message.indexOf('you are already in this room') != -1 ) ){
     message = '';
    }
  
   if(messageWindow){
    var mwString = messageWindow;
    messageWindow = detectObjectness(messageWindow); 

    // check if the scroll bar is down
    var scrollDown = isScrollDown(messageWindow); 
      if(messageWindow){
       messageWindow.innerHTML += message; 
         if( (mwString.indexOf('pm_messageWindow_') != -1) && (receiverId == uid) ){ 
          readMessagesIds.push(messageId); // add the message id to the read messages ids array, to be updated by the updateChatClose function
         } 

       // scroll down the scrollbar or maintain it in the position the user has scrolled to
       messageWindow.scrollTop = scrollDown ? messageWindow.scrollHeight : messageWindow.scrollTop;
      }
   }
}

/*
* update the message in the server as read, 
* so that the next time we retrieve messages, we don't retrieve the message
* @used only for PMs, not for chatRooms
* @date: March 28, 2012
*/
function updateReadStatus(messageId){
 var xhr = createXmlHttpRequestObject();
 var params = "request=update_read_status" +  "&chat_id=" + encodeURIComponent(messageId);
 makeXHRRequest(serverURL, xhr, function(){handleUpdateReadStatus(xhr, messageId)}, "POST", params, true);
}

function handleUpdateReadStatus(xhr, messageId){

   /*
   * For efficiency, remove the message from the array of read messages, to reduce the loop-time of the 
   * 'for loop' in 'updateReadMessages()',  defined in closedWindowManager.js 
   * However, only remove the message from the array if its read status has been successfully updated on the server
   */
   if( (xhr.readyState == 4) && (xhr.status == 200) ){
     removeFromArray(messageId, readMessagesIds); 
   }
}

/* function that formats and appends the user's name to the users' list*/
function formatUserDisplay(userName, userId)  
{
  var responderId = userId;
   
   if(rePopulateList && justJoinedRoom && (userId == uid)){
    currDisplayedUsers = [];
   }

   /*
   * don't display anyone twice
   * in the same room and don't
   * also leave gaps bw names as 
   * a result of the system sending
   * messages like '... left/joined room'
   * with an id of 0(zero);
   */
   if(!inArray(userId, currDisplayedUsers) && (userId > 0) ){

    // compose the HTML code that displays the room users list
    var userFormat = "";
    var spanId = userName + "_" + userId;
    userFormat += "<div id='" + spanId + "' " +
                  "style='" +
                  "display: block;" +
                  "font-family: arial,sans-serif;" + 
                  "font-size: 12px;" +
                  "color: red;" +
                  "font-weight: bold;" +
                  "font-style: normal;" +
                  "text-decoration: none; '" +           
                  ">";
     userFormat += "<span " +
                  //firefox doesn't support this so, we use the parentNode " +
                  //onmouseover='changeBgColor(" + spanId + ", \"gray\");'   " + 
                  "onmouseover='initBuddyCMenuLinks(" + responderId + ", \"forChatRoom\"); hideCustomContextMenu(\"chatRoomWin_divContext\", event); changeBgColor(this.parentNode, \"gray\"); loadUserProfile(" + responderId + ", event);' " + 
                  "onmouseout='changeBgColor(this.parentNode, \"\");' " + 
                  "onclick='contextMenuMouseDown(\"divContext\", event); return false'" +
                  "oncontextmenu='showCustomContextMenu(\"chatRoomWin_divContext\", event); unloadUserProfile(" + responderId + "); handleEvent(event);'" +
                  "ondblclick='verifyAndLoadPMWin(" + responderId + ");' " +
                  ">";
     userFormat += userName;
     userFormat += "</span>";
     userFormat += "</div>";

     currDisplayedUsers.push(responderId);
     displayUsers(userFormat);
   } 
}

function displayUsers(usersList)
{
 var listPane = objectify('roomUsersListPane');
 var scrollisDown = isScrollDown(listPane);
   if(listPane){
    listPane.innerHTML += usersList; 
    listPane.scrollTop = scrollisDown ? listPane.scrollHeight : listPane.scrollTop;
   }
}

/*********************
* Auxiliary functions
**********************/

/*
* loads a new PM with a user if 
* there's no one already open with that user
*/
function verifyAndLoadPMWin(responderId){
   if(!WindowObject.exists('PMWin_' + responderId)){
      if(responderId != uid){
       loadPMWin(responderId); //defined in PMWin.js
      }
      else{
       errorDiv = detectObjectness('errorDiv');
       errorManager.displayError({'Error' : 'you cannot send a message to yourself'}, errorDiv, true);
       StyleManager.showElement(errorDiv, 'display');
       setTimeout(function(){errorDiv.innerHTML = ''; StyleManager.hideElement(errorDiv, true);}, 5000);
      }
   }
   else{
    //alert('you already opened a conversation window with this user');
    //rather than this alert, make the window receive focus as below:
    WindowObject.setActiveWindow('PMWin_' + responderId);
    WindowObject.getActiveWindowAsObject().focus();
   }
}

/***
* ran every time a new user joins the room:
* for those who've been in the room, gets only the newly joined person, 
* sets the 'joined the room' message(s) (if any) of all those who were in the room
* before the current user joins to empty so they don't display 
* in the messages window of the current user, and updates justJoined
* (which was set to true when the user joined in the joinRoom function)
* so that the user is no longer seen as just entering the room, and then
* any new 'joined the room' messages will be seen by him; 
* for the person who just joined, we also update justJoinedRoom to false for same
* reasons as above, then we clear the user's list window here
* and call the getRoomMembers function 
***/
onRoomJoinCall = function(message, joiner_id, joiner_name, roomToJoin){ 
 
   joiner_id = trim(joiner_id);
   uid = trim(uid);
   roomToJoin = trim(roomToJoin);
   currRoom = trim(currRoom);
   
   if(joiner_id != uid ){
      if(justJoinedRoom){ //and if the current user is just entering the room, then no need to display the 'joined the room' messages of those who were there b4 the current user joined
       message = '';
       justJoinedRoom = false;
      }
    addNewRoomMember(joiner_id, joiner_name);
   } 
   else if(joiner_id == uid){ 
      if(roomToJoin == currRoom){

         /*
         * we test for this global variable here because the test "if(roomToJoin != currRoom)" 
         * later below fails the first time the room is loaded
         * because the "currRoom" and the "roomToJoin" variables are the same.
         * After this initial call to getRoomMembers(), all further calls to it are handled by the 
         * call below in the else part of if(roomToJoin == currRoom)
         * @ date updated : July 30, 2011 -- 15:23 
         * @date moved from joinRoom() : April 17, 2012 -- 10:11
         */
         if(firstLoad){
          setTimeout(function(){getRoomMembers(roomToJoin)}, interval);
          firstLoad = false;
         }
       justJoinedRoom = false;
       repopulateList = false;
      }
      else if(roomToJoin != currRoom){
       justJoinedRoom = true;
       repopulateList = true;
       objectify('roomUsersListPane').innerHTML = ''; //used when we use the format user display function
      
        /*
         * get the room members only if the server message has returned that the user has "joined the room"
         * this is necessary and put here rather than in the joinRoom() function where it previously was
         * because if the server response has not come, -- due to too many requests pending -- 
         * we could be getting the room members for the room
         * the user is going to while the user has not left the current room
         * @date moved here: July 30, 2011-- 15:02
         */       
          setTimeout(function(){getRoomMembers(roomToJoin)}, 500);
      }
   } 
 return message;  
}

/*
* this can also be implemented like the
* removeRoomUser function, but is smaller this way
*/
addNewRoomMember = function(joiner_id, joiner_name){
 formatUserDisplay(joiner_name, joiner_id); //defined above
 //alternatively we could use the addRoomUser() function defined below
 //addRoomUser(joiner_id, joiner_name);
}

/***
* ran every time someone leaves the room:
* prevents the 'left the room' messages of those 
* who left b4 the current user came in from 
* showing up in his chat room messages window;
* removes the person who left from the 
* list of currently displayed users
***/
onRoomLeaveCall = function(message, room_leaver_id, room_leaver_name){
   if(justJoinedRoom){
    message = '';
    justJoinedRoom = false;
   }

   /* 
   * when we update the chat room window based on the response from the server,
  * the justJoinedRoom variable is false, so the 'left the room' message is displayed even though the room leaver is the current user
  * we use this conditional statement to prevent that
  * @date added: July 16, 2011: 18:40
  */
   if(trim(room_leaver_id) == trim(uid)){
     message = '';
   }
 removeRoomUser(room_leaver_id, room_leaver_name);
 return message;
}

function removeRoomUser(userId, userName){
 var user = detectObjectness(userName + "_" + userId);
   
   if(user && user.parentNode){
    user.parentNode.removeChild(user);
    removeFromArray(userId, currDisplayedUsers);
   }
}

checkMultiWordFont = function(font){
   if(font == "Times New Roman" || font == "Courier New" || font == "Comic Sans MS"){
    font = '"' + font + '"';
   }
 return font;
}

/**********************
alternatively called from processUsers in customChatHandler.js in place of formatUserDisplay
use any you like, but only one of them can be used, both (this func and formatUserDisplay)
 cannot be used simultaneously
************************/
function addRoomUser(userId, userName){
   if(rePopulateList){
    currDisplayedUsers = [];
   }

   /*
   * don't display anyone twice
   * in the same room and don't
   * also leave gaps bw names as 
   * a result of the system sending
   * messages like '... left/joined room'
   * with an id of 0(zero);
   */
   if( !inArray(userId, currDisplayedUsers) && (userId > 0) ){
    objectify('roomUsersList').innerHTML+= '' + 
    '<li id="roomUser_' +  userId + '" style="padding-top:2px">' + 
     '<span title="' + userName + '" ' +
      'onmouseover="hideCustomContextMenu(\'chatRoomWin_divContext\'); changeBgColor(this.parentNode, \'gray\'); loadUserProfile(' + userId + ', event); initBuddyCMenuLinks(' + userId + ', \'forChatRoom\');"' +
      'onmouseout="changeBgColor(this.parentNode, \'\');"' +
      'onclick="contextMenuMouseDown(\'chatRoomWin_divContext\', event); return false"' +
      'oncontextmenu="showCustomContextMenu(\'chatRoomWin_divContext\', event); unloadUserProfile(' + userId + '); handleEvent(event);"' +
      'ondblclick="verifyAndLoadPMWin(' + userId + ');"' +
     '>' + 
     userName //+ 
     '</span>' +
    '</li>';

    currDisplayedUsers.push(responderId);
   }
}