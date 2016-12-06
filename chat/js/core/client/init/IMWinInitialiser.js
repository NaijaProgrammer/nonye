//utf-8
function initialiseIMWin(IMWin, hideOnInit){
 objectify("IMWin_sizer").style.top = (IMWin.getSize().height - 15) + 'px';
 objectify("im_top_mid").style.width = (IMWin.getSize().width - 18) + 'px'; 
 StyleManager.setStyle(objectify("IMWin_creator_info"), {'top' : (IMWin.getSize().height - 25) + 'px'});
 
 setTimeout(
    function(){  
     setElemSize('buddy_list_holder', (IMWin.getSize().width - 12), (IMWin.getSize().height - 95));
     IMWin.setTitle(IMWinTitle, 18); //global variable
     if(typeof getRooms != 'undefined'){ //defined chatHandler.js
      getRooms('forIM');
     }
     else{
      addRooms('forIM');
     }
     getBuddies('all');
     initContextMenu('IMWin_divContext');
     attachEventListener(objectify(IMWin.getId()), "click", function(event){WindowObject.setActiveWindow(IMWin.getId()); IMWin.focus('IMWin'); }, false);
     attachEventListener(window, "resize", 
        function(event){
         IMWin.setSize(IMWin.getSize().width, Browser.Size.height() - 60);
         IMWin.setPosition(IMWinLocation);
         setElemSize('buddy_list_holder', (IMWin.getSize().width - 12), (IMWin.getSize().height - 95));
         handleEvent(event);
        }, false);
     }, 500
 );

/*
* we use a 600+ here to enable every initialization and positioning
* to be set by the previous setTimeout using 500 b4 displaying the window
* this prevents a situation where the window's first displayed in
* its initial position b4 being displayed in the 
* dynamically set position from being visible to the user
*/
 setTimeout(function(){WindowObject.setActiveWindow(IMWin.getId());}, 601); 
 if(!hideOnInit){
  setTimeout(function(){IMWin.show('IMWin'); IMWin.focus('IMWin');}, 605);
 }
 setInterval(
   function(){
    if(IMWin.element.moved){ 
      var IMWinPos = getElemPos(IMWin.element); 
       IMWin.left = IMWinPos.left;
       IMWin.top = IMWinPos.top;
       registerChatWindowState(IMWin, "IM", "", 'update'); //used also in custom/chatStateUpdateHandlers
        IMWin.element.moved = false;
    }
    if(Effects.resized){
     IMWin.handleResize();
       if(parseInt(IMWin.getSize().width) < 210){IMWin.setWidth(210);}
       if(parseInt(IMWin.getSize().height) < 150){IMWin.setHeight(150);}
    } 
   }, 
  00);   

}