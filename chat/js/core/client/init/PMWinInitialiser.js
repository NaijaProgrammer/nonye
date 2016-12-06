//utf-8

function initialisePMWin(PMWin, responderId){

 loadCSS([styleSheetPath + 'pmwins_style']);
 includeJS([styleSheetPath + 'msgWinsThemeStyler.css'], true);

 objectify("pm_messageWindow_" + responderId).style.width = (PMWin.getSize().width - 15) + 'px';
 objectify("pm_messageWindow_" + responderId).style.height = (PMWin.getSize().height - 140) + 'px';
 objectify("textEditDiv_" + responderId).style.width = (PMWin.getSize().width - 15) + 'px';
 objectify("textEditDiv_" + responderId).style.top = (PMWin.getSize().height - 108) + 'px';
 objectify("textForm_" + responderId).style.top = (PMWin.getSize().height - 80) + 'px';
 objectify(responderId).style.width = (PMWin.getSize().width - 15) + 'px';
 objectify("send_button_" + responderId).style.left = (PMWin.getSize().width - 75) + 'px';
 objectify("reset_button_" + responderId).style.left = '1px';
 objectify("pm_top_mid_" + responderId).style.width = (PMWin.getSize().width - 18) + 'px';  
 if(objectify("PMWin_" + responderId + "_sizer")){
  objectify("PMWin_" + responderId + "_sizer").style.top = (PMWin.getSize().height - 15) + 'px';
 }
 StyleManager.setStyle(objectify("PMWin_" + responderId + "_creator_info"), {'top' : (PMWin.getSize().height - 20) + 'px'});
 StyleManager.setStyle(objectify("PMWin_" + responderId + "_creator_info"), {'left' : '80px'}); 

 PMWin.setTitle(PMWin.getTitle(), 13);
 attachEventListener(objectify('PMWin_' + responderId), "mouseover", function(){initImages(responderId); initTextEditObjects(responderId);}, false); 
 attachEventListener(objectify('PMWin_' + responderId), "click", function(event){WindowObject.setActiveWindow('PMWin_' + responderId); PMWin.focus('PMWin_' + responderId, useStackView, 'IMWin');}, false);
 WindowObject.setActiveWindow('PMWin_' + responderId);  
 PMWin.focus('PMWin_' + responderId, useStackView, 'IMWin');
 initPM(responderId);

   setInterval(
   function(){
    if(PMWin.element.moved){ 
      var PMWinPos = getElemPos(PMWin.element); 
       PMWin.left = PMWinPos.left;
       PMWin.top = PMWinPos.top;
       registerChatWindowState(PMWin, "PM", responderId, 'update'); //used also in custom/chatStateUpdateHandlers
       PMWin.element.moved = false;
    }
    if(Effects.resized){
     PMWin.handleResize(PMWin.getId());
       if(parseInt(PMWin.getSize().width) < 350){PMWin.setWidth(350);}
       if(parseInt(PMWin.getSize().height) < 150){PMWin.setHeight(150);}
    }
    
   }, 
  00);
}

function initPM(responderId){
 pmTextBox = objectify(responderId); 
 initTextEditObjects(pmTextBox); 
 pmTextBox.focus();
 initImages(responderId);
 retrieveMsgs('pm'); //defined in chatInitialiser
}