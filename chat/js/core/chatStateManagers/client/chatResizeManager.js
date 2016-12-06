 //utf-8

function handleIMWinResize(){

     var winId = WindowObject.getActiveWindow();
     var winObj = WindowObject.getActiveWindowAsObject();     
     var responderId = winObj.sendMessageBox;

       if(winId.indexOf("chatRoomWin") != -1){
        var chatRoomWin = winObj;       
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
        objectify("chatRoomWin_sizer").style.top = (chatRoomWin.getSize().height - 15) + 'px';
        StyleManager.setStyle(objectify("chatRoomWin_creator_info"), {'top' : (chatRoomWin.getSize().height - 20) + 'px'});
        StyleManager.setStyle(objectify("chatRoomWin_creator_info"), {'left' : '80px'});  
       }

       else if(winId.indexOf("PMWin_") != -1){
        var PMWin = winObj;
        objectify("pm_messageWindow_" + responderId).style.width = (PMWin.getSize().width - 15) + 'px';
        objectify("pm_messageWindow_" + responderId).style.height = (PMWin.getSize().height - 140) + 'px';
        objectify("textEditDiv_" + responderId).style.width = (PMWin.getSize().width - 15) + 'px';
        objectify("textEditDiv_" + responderId).style.top = (PMWin.getSize().height - 108) + 'px';
        objectify("textForm_" + responderId).style.top = (PMWin.getSize().height - 80) + 'px';
        objectify(responderId).style.width = (PMWin.getSize().width - 15) + 'px';
        objectify("send_button_" + responderId).style.left = (PMWin.getSize().width - 75) + 'px';
        objectify("reset_button_" + responderId).style.left = '1px';
        objectify("pm_top_mid_" + responderId).style.width = (PMWin.getSize().width - 18) + 'px';  
        objectify("PMWin_" + responderId + "_sizer").style.top = (PMWin.getSize().height - 15) + 'px';
        StyleManager.setStyle(objectify("PMWin_" + responderId + "_creator_info"), {'top' : (PMWin.getSize().height - 20) + 'px'});
        StyleManager.setStyle(objectify("PMWin_" + responderId + "_creator_info"), {'left' : '80px'}); 
      }

      else if(winId.indexOf("IMWin") != -1){
        var IMWin = winObj;
        setElemSize('buddy_list_holder', (IMWin.getSize().width - 12), (IMWin.getSize().height - 95));
        objectify("IMWin_sizer").style.top = (IMWin.getSize().height - 15) + 'px';  
        objectify("im_top_mid").style.width = (IMWin.getSize().width - 18) + 'px';
        StyleManager.setStyle(objectify("IMWin_creator_info"), {'top' : (IMWin.getSize().height - 25) + 'px'});
      }
}