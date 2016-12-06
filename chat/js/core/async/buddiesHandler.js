//utf-8

var hoverTarget = '';
var buddiesGetter = '';

function getBuddies(status){
 
 var xhr = createXmlHttpRequestObject();
 var params="request=get_buddies" +
 "&user_id=" + encodeURIComponent(uid) +
 "&status=" + encodeURIComponent(status);
   if (xhr.readyState == 4 || xhr.readyState == 0){
    makeXHRRequest(serverURL, xhr, function(){handleBuddiesResponse(status, xhr)}, "POST", params, true);
   }
   else{
    buddiesGetter = setTimeout(function(){getBuddies(status)}, interval);
   }
}

function handleBuddiesResponse(status, xhr){
 
   if ( (xhr.readyState == 4) && (xhr.status == 200) )
   {
         //try
         //{
         var reply = xhr.responseText;
         populateBuddiesArray(reply, status); 
         //}
         //catch(e){}
   }
}

function populateBuddiesArray(serverReply, status){

 var buddyId;
 var buddyName; 
 var buddyStatus; 
 
 serverReply = "(" + serverReply + ")"; 
 var response = eval(serverReply);
   for(var i in response.friends){
    buddyId = response.friends[i].friend_id;
    buddyName = response.friends[i].friend_name; 
    buddyStatus = response.friends[i].friend_status;

      if(!inArray(buddyId, buddyIds)){
          addBuddy(buddyId, buddyName, buddyStatus);
          buddyIds.push(buddyId);
          buddies.push({'id': buddyId, 'name': buddyName, 'status': buddyStatus});
      }
      else{   
         for(var j = 0; j < buddies.length; j++){ //global array variable
            //if there's a change in the user's login or IM window loading status 
            if( (buddies[j].id == buddyId) && (buddies[j].status != buddyStatus) ){
             DOMManager.removeFromArrayAndParentNode(buddies[j].id, "buddyList_" + buddies[j].id, buddies);
             addBuddy(buddyId, buddyName, buddyStatus);
             buddyIds.push(buddyId);
             buddies.push({'id': buddyId, 'name': buddyName, 'status': buddyStatus});
            } 
         }
      }
   }
 buddiesGetter = setTimeout(function(){getBuddies(status)}, 1000);
}


function getUserData(userId, closureObject)
{
 
 xhr = createXmlHttpRequestObject();
 var params="request=get_user_data" +
 "&get_full_data=true" + 
 "&user_id=" + encodeURIComponent(userId);

  makeXHRRequest(serverURL, xhr,  function(){handleGetUserData(userId,xhr)}, "POST", params, true, '', closureObject);

}

function handleGetUserData(userId)
{
  var userDetails = null;
    if(xhr.readyState == 4 && xhr.status == 200){
     var reply = xhr.responseText; 
     reply = "(" + reply + ")";
     userDetails = eval(reply);
     usersDetails.push(userDetails);
    }
}

function addBuddy(buddyId, buddyName, buddyStatus){

 var statusImg = statusImgPath + buddyStatus + '.gif';

 objectify('buddylist').innerHTML += '' + 
 '<li id="buddyList_' +  buddyId + '" >' + 
  '<span title="' + buddyName + '" ' +
    'onmouseover="initBuddyCMenuLinks(' + buddyId + ', \'forIM\'); hideCustomContextMenu(\'IMWin_divContext\', event); changeBgColor(this.parentNode, \'gray\'); loadUserProfile(' + buddyId + ', event);"' +
    'onmouseout="changeBgColor(this.parentNode, \'\');"' +
    'onclick="contextMenuMouseDown(\'IMWin_divContext\', event); return false"' +
    'oncontextmenu="showCustomContextMenu(\'IMWin_divContext\', event); unloadUserProfile(' + buddyId + ', event); handleEvent(event);"' +
    'ondblclick="verifyAndLoadPMWin(' + buddyId + ');"' +
   '>' +
    buddyName + ' ' + 
   '</span>' +  HTMLImage({'imageToShow': statusImg, imageAlt: buddyStatus + '.gif', imageTitle: buddyStatus}) +
  '</li>';
}

function loadUserProfile(userId, evt){
   if(!WindowObject.exists('userProfileWin_' + userId)){
    getUserProfile(userId, evt);
   }
}

function getUserProfile(userId, evt){
 evt =  Mouse.Position(evt); 
 prevXPos = evt.left;  
 prevYPos = evt.top;

 var xhr = createXmlHttpRequestObject();
 var params="request=get_user_data" +
 "&get_full_data=false" + 
 "&user_id=" + encodeURIComponent(userId);
 makeXHRRequest(serverURL, xhr, function(){showProfileContents(userId, xhr)}, "POST", params, true);
}

function showProfileContents(userId, xhr){

 var prevConf = {

   'attributes': {
    'className': 'dynamic_user_profile_div',
    'id': 'userProfileWin_' + userId
   },

   'styleOptions': {
    'visibility': 'hidden',
    'borderWidth': '1px',
    'borderStyle' : 'solid',
    'borderColor' : '#BFBCB8 #E0DCD8 #E0DCD8 #BFBCB8',
    'width': '166px', //'auto',
    'height': 'auto', //'50px',
    'overflow' : 'auto',
    'backgroundColor': '#efefef',
    'zIndex': '15'
   }
 }

   if ( (xhr.readyState == 4) && (xhr.status == 200) ) {
      /* I did this coz server returns an empty string (for some unknown reason) 
      * before returning the code in server page, try to fix it later
      */
      if( (xhr.responseText != '') && (xhr.responseText != ' ') ){ 
       var reply = xhr.responseText;
      }
      if(DOMManager.getElementsByClassName('dynamic_user_profile_div').length > 0){ //unload the previous user profile floating div
       unloadAllUserProfileDiv(); 
      }
     
      //if(DOMManager.getElementsByClassName('dynamic_user_profile_div').length == 0){ //don't load more than one user profile at once
       var prevWin = new FloatingWindow(prevConf);
       prevWin.setContent(reply , true); 
       prevWin.setPosition({'top': parseInt(prevYPos)+2, 'left': parseInt(prevXPos)+2});
       prevWin.addToDOM();  //TO DO: add to parent element 'IMWin' by passing 'IMWin' to addToDOM(), so that when we close IMWin, it automatically closes
       prevWin.show();
       prevWin.element.onmouseout = function(event){unloadUserProfile(userId, event)};
       attachEventListener(document, "mouseover", function(event){if( EventManager.targetIsDocument(event) ){unloadUserProfile(userId, event)}}, false);
       attachEventListener(document, "click", function(event){if(EventManager.eventTarget(event).className != "dynamic_user_profile_div"){unloadUserProfile(userId, event)}}, false);
      //}
   }
}

function unloadUserProfile(userId, event) {
  
  var prevWin = WindowObject.getWindowAsObject('userProfileWin_' + userId);
   if( prevWin && WindowObject.exists(prevWin.getId()) ){
      if(Mouse.isOutside(prevWin.getId(), event)){
       document.body.removeChild(objectify('userProfileWin_' + userId));
       //document.body.removeChild(objectify(prevWin.getId()));
      }
   }
}

function unloadAllUserProfileDiv(){
 var targets = DOMManager.getElementsByClassName('dynamic_user_profile_div');
 var targetsLen = targets.length;
   for(var i = 0; i < targetsLen; i++){
      if(targets[i].className == 'dynamic_user_profile_div'){
       document.body.removeChild(objectify(targets[i]['id']));
      }
   }
}