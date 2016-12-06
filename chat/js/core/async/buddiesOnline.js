function getOnlineBuddies(){
 var xhr = createXmlHttpRequestObject();
 var params="request=get_buddies" +
 "&user_id=" + encodeURIComponent(uid) +
 "&status=" + encodeURIComponent('online');
   if (xhr.readyState == 4 || xhr.readyState == 0){
    makeXHRRequest(serverURL, xhr, function(){handleGetOnlineBuddies(xhr)}, "POST", params, true);
   }
   else{
    getOnlineBuddies();
   }
}

function handleGetOnlineBuddies(xhr){
 
   if ( (xhr.readyState == 4) && (xhr.status == 200) )
   {      
    var reply = xhr.responseText; 
    processGetOnlineBuddies(reply);
         
   }
}

function processGetOnlineBuddies(serverReply){

 var buddyId;
 var buddyName; 
 var buddyStatus; 
 
 serverReply = "(" + serverReply + ")"; 
 var response = eval(serverReply);
   for(var i in response.friends){
    buddyId = response.friends[i].friend_id;
    buddyName = response.friends[i].friend_name; 
    buddyStatus = response.friends[i].friend_status;
     
      if(trim(buddyStatus) != 'offline'){
         if(!inArray(buddyId, buddyOnlineIds)){
          buddyOnlineIds.push(buddyId);
          buddiesOnline.push({'id': buddyId, 'name': buddyName, 'status': buddyStatus});
         }
      }
      else{
         if(inArray(buddyId, buddyOnlineIds)){
          removeFromArray(buddyId, buddyOnlineIds);
            for(var j = 0; j < buddiesOnline.length; j++){ //global array variable 
               if( (buddiesOnline[j].id == buddyId)){
                removeFromArray(buddiesOnline[j], buddiesOnline);
               } 
            }
         }  
      }
   }
 setTimeout(function(){getOnlineBuddies()}, 1000);
}

setInterval(
   function(){ 
    var bc = detectObjectness('bolspan');
      if(bc)
       bc.innerHTML = ' (' + buddiesOnline.length + ')'; //buddiesOnline;
   }, 500);
