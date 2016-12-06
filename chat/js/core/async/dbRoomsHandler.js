/***** FUNCTIONS THAT HANDLE GETTING AND POPULATING THE ROOMS ARRAY WITH CHAT ROOMS **********/

/*
* gets list of rooms from your database 
* which we then pass to the populateRoomsArray function below
* to insert into the global rooms array
* to be used when we fetch rooms from the array
* 
* use this to fetch rooms from your database
* or just manually add rooms to the rooms array in the chatConfig.js file
*/
function getRooms(requestType){
 var xhr = createXmlHttpRequestObject();
 var params="request=get_rooms";
 makeXHRRequest(serverURL, xhr, function(){handleRoomsResponse(requestType, xhr)}, "POST", params, true);
}

function handleRoomsResponse(requestType, xhr){

   if ( (xhr.readyState == 4) && (xhr.status == 200) )
   {         
     var reply = xhr.responseText;
     populateRoomsArray(requestType, reply);
   }
}

/*
* adds the rooms returned from server to the global rooms array
* re-implement this to add the rooms based on the reply returned by 
* your server (either in XML or in JSON format)
*/
function populateRoomsArray(requestType, serverReply){

 var response = eval ( "(" + serverReply + ")" );
 
   for(var i in response.rooms){
    roomId = response.rooms[i].room_id;
    roomName = response.rooms[i].room_name; 
       if(!inArray(roomId, roomIds)){
        roomIds.push(roomId);
        rooms.push({'room_id': roomId, 'room_name': roomName}); 
       }     
   }

 addRooms(requestType); //defined in core/async/chatHandler.js
}

/************************** END OF ROOMS ARRAY FUNCTIONS **************/