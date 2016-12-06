var justLoaded = true; //was the current page just loaded
setInterval(
  function(){
    if(justLoaded){
       if(typeof getChatWindows != 'undefined'){
        getChatWindows(); //in chatWinStateHandler.js
        justLoaded = false;
       }
    }  
    /*
    * we do this and the newUpdate here to make sure we always retrieve the current state of the chat windows  
    * as just inserted or updated, 
    * the values are (re-)set by the handleRegisterChatWindowState and handleUpdateChatWindowState.js
    * see these functions' comments for more details
    */ 
    if(Browser.getCookie('newInsert') == 'true'){ 
     getChatWindows(); 
     Browser.setCookie('newInsert', 'false'); 
    }
    if(Browser.getCookie('newUpdate') == 'true'){ 
     getChatWindows(); 
     Browser.setCookie('newUpdate', 'false'); 
    }
    if(Browser.getCookie('newRoomUpdate') == 'true'){
     getChatWindows();
     Browser.setCookie('newRoomUpdate', 'false');
    }
}, 1000 
)