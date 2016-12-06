//utf-8

/*
* all coding and design by michael orji
*/

function loadPMWin(responderId) {

 var closureObject = {'closureName' : 'retrieveUserData'};
 getUserData(responderId, closureObject); //defined in buddiesHandler.js; see 'chatStateRegistrars.js' for (less preferred) alternate way of calling this function
 var userDetails = '';
 setTimeout(function(){userDetails = closureObject.closureName();}, 300);
  
 setTimeout(function(){
    userDetails = eval( '(' + userDetails + ')' );
    var userName = userDetails.name;

      conf = {
 
         'attributes': 
         {
          'className': 'IM', //'pm',
          'id': 'PMWin_' + responderId,
          'title': userName
         },

         'options': 
         {
          'closable': true,
          'minimizable': true,
          'maximizable': true,
          'detachable': true,
          'resizable': true,
          'draggable' : false,
          'hasTitle': true
         },

         'styleOptions': 
         {
          'width': '350px',
          'height': '400px',
          'backgroundColor': bgcolor,
          'backgroundImage': 'url(' + styleImgPath + 'bg.png)',
          'backgroundRepeat': 'repeat',
          'border': '1px solid #42464b',
          'borderTop': 'none',
          'visibility': 'hidden',
          'zIndex': '11',
          'position' : PMWinLocation.positioning,
          'right' : PMWinLocation.right,
          'bottom' : PMWinLocation.bottom
         },

         'sendMessageBox': responderId,
         'topControlsBoxHeight' : '23px',
         'minZIndex': minimumZIndex,
         'maxZIndex': maximumZIndex,
         'loadCallbacks' : [pushWindowOnOpenConversationsStackOnLoad],
         'resizeCallbacks': [handleIMWinResize],
         'minimizeCallbacks' : [updateChatState],
         'maximizeCallbacks' : [updateChatState],
         'detachCallbacks' : [detachIMWindow],
         'focusCallbacks' : [updateChatState],
         'closeCallbacks' : [stopRetrievingMsgsOnClose, updateReadMessages, removeWindowFromOpenConversationsStack, updateChatClose] //defined in chatInitialiser.js, closedWindowManager.js and chatStateUpdateHandlers.jsrespectively
      }
      if(responderId > 0){
       var PMWin = new FloatingWindow(conf);
       PMWin.setTopControlsContent(topControlsContent('PM', responderId)); //defined in IMWin.js
       PMWin.setContent(PMWinContent(responderId), false);
       PMWin.addToDOM();
          if(typeof initWindow != 'undefined'){
           initWindow(PMWin, "PM", responderId, null);
          }
      }
   }, 500);
}

/*
* handles the case when we load the PMWin
* without having loaded the chatRoomWin
* so that the roomsList hiding doesn't give errors
*/
function doCustomHide(elem){
 elem = detectObjectness(elem);
   if(elem){
    StyleManager.hideElement(elem);
   }
}

function PMWinContent(responderId){

 var uid = getChatUserId();
 var PMWinEmote = 'PMWinEmote_' + responderId; 

 var c = ' ' + 

 '<div class="pm_container" id="pm_container_' + responderId + '">' +

  '<ul id="pm_active_windows_ul_' + responderId + '" class="active_windows_ul pm_active_windows_ul">' +
   '<li><a href="#" id="pm_active_windows_li_' + responderId + '" class="active_windows_li pm_active_windows_li" onclick="toggleElementVisibility(\'pm_active_windows_list_' + responderId + '\'); handleEvent(event); return false">Open Conversations</a>'+
    '<div id="pm_active_windows_list_' + responderId + '" class="active_windows_list pm_active_windows_list">' + 
     //'<a href="#" id="" class="active_windows pm_active_windows" onclick="new FloatingWindow().focus(\'IMWin\'); StyleManager.hideElement(\'pm_active_windows_list_' + responderId + '\'); handleEvent(event); return false">IMWin</a>' +
     '</div>' +
   '</li>' +
  '</ul>' +

  '<div class="pm_messageWindow" id="pm_messageWindow_' + responderId + '"> </div>' +

  '<form class="textForm" id="textForm_' + responderId + '">' +
   '<textarea wrap="soft" id="' + responderId + '" class="pm_text_box" autocomplete="off" style="color:#000000; font-family:Tahoma; font-size:12px; font-weight:normal; font-style:normal; text-decoration:none;" onkeydown="handleKeyAction(\'down\', 13, function(){sendMessage(' + responderId + ', \'pm\')}, event)"></textarea>' +
   '<input class="uid" type="hidden" value="' + uid + '" \><br />' +
   '<input type="button" id="send_button_' + responderId + '" class="send_button" value="send" onClick="sendMessage(' + responderId + ', \'pm\'); return false" />' +
   '<input type="button" id="reset_button_' + responderId + '" class="reset_button" value="reset" onClick="resetChat(' + responderId + ', \'pm\'); return false" />' +
  '</form>' +

  '<div id="textEditDiv_' + responderId + '" class="textEditDiv">' +
   
   '<div class="biu_imgs" id="biu_imgs_' + responderId + '">' +
    '<img name="b_' + responderId + '" id="b_' + responderId + '" src="' +  textEditImgPath + 'bold_off.png" onmouseover="imgMouseOver(\'b_' + responderId + '\')" onmouseout="imgMouseOut(\'b_' + responderId + '\')"  onclick="imgClick(\'b_' + responderId + '\'); setFontWeight(\'b_' + responderId + '\'); pmTextBox.focus();" />' +
    '<img name="i_' + responderId + '" id="i_' + responderId + '" src="' +  textEditImgPath + 'italic_off.png" onmouseover="imgMouseOver(\'i_' + responderId + '\')" onmouseout="imgMouseOut(\'i_' + responderId + '\')"  onclick="imgClick(\'i_' + responderId + '\'); setFontStyle(\'i_' + responderId + '\'); pmTextBox.focus();" />' +
    '<img name="u_' + responderId + '" id="u_' + responderId + '" src="' +  textEditImgPath + 'underline_off.png" onmouseover="imgMouseOver(\'u_' + responderId + '\')" onmouseout="imgMouseOut(\'u_' + responderId + '\')"  onclick="imgClick(\'u_' + responderId + '\'); setTextDecoration(\'u_' + responderId + '\'); pmTextBox.focus();" />' +
   '</div>' +

   '<ul class="text_editor">' +

    '<li> <a href="#" class="fontHolder" id="fontHolder_' + responderId + '" onclick="toggleElementVisibility(\'fontList_' + responderId + '\'); doCustomHide(\'roomsList\'); StyleManager.hideElementsOfClass(\'fontColourList\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"> Tahoma </a>' +
     '<div class="fontsList" id="fontList_' + responderId + '">' +
      '<a href="#" id="arial_' + responderId + '" onclick="setFont(\'Arial\'); swapContent(\'fontHolder_' + responderId + '\', \'arial_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Arial </a>' +
      '<a href="#" id="CSMS_' + responderId + '" onclick="setFont(\'Comic Sans MS\'); swapContent(\'fontHolder_' + responderId + '\', \'CSMS_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Comic Sans MS </a>' +
      '<a href="#" id="CNew_' + responderId + '" onclick="setFont(\'Courier New\'); swapContent(\'fontHolder_' + responderId + '\', \'CNew_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Courier New </a>' +
      '<a href="#" id="garamond_' + responderId + '" onclick="setFont(\'Garamond\'); swapContent(\'fontHolder_' + responderId + '\', \'garamond_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Garamond </a>' +
      '<a href="#" id="georgia_' + responderId + '" onclick="setFont(\'Georgia\'); swapContent(\'fontHolder_' + responderId + '\', \'georgia_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Georgia </a>' +
      '<a href="#" id="impact_' + responderId + '" onclick="setFont(\'Impact\'); swapContent(\'fontHolder_' + responderId + '\', \'impact_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Impact </a>' +
      '<a href="#" id="tahoma_' + responderId + '" onclick="setFont(\'Tahoma\'); swapContent(\'fontHolder_' + responderId + '\', \'tahoma_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Tahoma </a>' +
      '<a href="#" id="TNR_' + responderId + '" onclick="setFont(\'Times New Roman\'); swapContent(\'fontHolder_' + responderId + '\', \'TNR_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Times New Roman </a>' +
      '<a href="#" id="verdana_' + responderId + '" onclick="setFont(\'Verdana\'); swapContent(\'fontHolder_' + responderId + '\', \'verdana_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontsList\'); pmTextBox.focus(); handleEvent(event); return false"> Verdana </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="fontSizeHolder" id="fontSizeHolder_' + responderId + '" onclick="toggleElementVisibility(\'fontSizeList_' + responderId + '\'); doCustomHide(\'roomsList\'); StyleManager.hideElementsOfClass(\'fontColourList\'); StyleManager.hideElementsOfClass(\'fontsList\'); StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"> 12 </a>' +
     '<div class="fontSizeList" id="fontSizeList_' + responderId + '"> ' +
      '<a href="#" id="s8_' + responderId + '" onclick="setFontSize(8); swapContent(\'fontSizeHolder_' + responderId + '\', \'s8_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 8 </a>' +
      '<a href="#" id="s10_' + responderId + '" onclick="setFontSize(10); swapContent(\'fontSizeHolder_' + responderId + '\', \'s10_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 10 </a>' +
      '<a href="#" id="s12_' + responderId + '" onclick="setFontSize(12); swapContent(\'fontSizeHolder_' + responderId + '\', \'s12_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 12 </a>' +
      '<a href="#" id="s14_' + responderId + '" onclick="setFontSize(14); swapContent(\'fontSizeHolder_' + responderId + '\', \'s14_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 14 </a>' +
      '<a href="#" id="s16_' + responderId + '" onclick="setFontSize(16); swapContent(\'fontSizeHolder_' + responderId + '\', \'s16_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 16 </a>' +
      '<a href="#" id="s18_' + responderId + '" onclick="setFontSize(18); swapContent(\'fontSizeHolder_' + responderId + '\', \'s18_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 18 </a>' +
      '<a href="#" id="s20_' + responderId + '" onclick="setFontSize(20); swapContent(\'fontSizeHolder_' + responderId + '\', \'s20_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 20 </a>' +
      '<a href="#" id="s22_' + responderId + '" onclick="setFontSize(22); swapContent(\'fontSizeHolder_' + responderId + '\', \'s22_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 22 </a>' +
      '<a href="#" id="s24_' + responderId + '" onclick="setFontSize(24); swapContent(\'fontSizeHolder_' + responderId + '\', \'s24_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); pmTextBox.focus(); handleEvent(event); return false"> 24 </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="fontColourHolder" id="fontColourHolder_' + responderId + '" style="background-color:#000000;" onclick="toggleElementVisibility(\'fontColourList_' + responderId + '\'); doCustomHide(\'roomsList\'); StyleManager.hideElementsOfClass(\'fontsList\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"> </a>' +
     '<div class="fontColourList" id="fontColourList_' + responderId + '">' +
      '<a href="#" id="c1_' + responderId + '" style="background-color:#b8b8b8;" onclick="setFontColour(\'c1_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c1_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c2_' + responderId + '" style="background-color:#b4ad3b;" onclick="setFontColour(\'c2_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c2_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c3_' + responderId + '" style="background-color:#bb5c54;" onclick="setFontColour(\'c3_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c3_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c4_' + responderId + '" style="background-color:#755a5c;" onclick="setFontColour(\'c4_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c4_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c5_' + responderId + '" style="background-color:#a95bef;" onclick="setFontColour(\'c5_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c5_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c6_' + responderId + '" style="background-color:#d65a20;" onclick="setFontColour(\'c6_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c6_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c7_' + responderId + '" style="background-color:#e39230;" onclick="setFontColour(\'c7_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c7_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c8_' + responderId + '" style="background-color:#a71334;" onclick="setFontColour(\'c8_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c8_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c9_' + responderId + '" style="background-color:#590099;" onclick="setFontColour(\'c9_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c9_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c10_' + responderId + '" style="background-color:#d40088;" onclick="setFontColour(\'c10_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c10_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c11_' + responderId + '" style="background-color:#0030ac;" onclick="setFontColour(\'c11_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c11_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c12_' + responderId + '" style="background-color:#676f11;" onclick="setFontColour(\'c12_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c12_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c13_' + responderId + '" style="background-color:#769321;" onclick="setFontColour(\'c13_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c13_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c14_' + responderId + '" style="background-color:#3966fe;" onclick="setFontColour(\'c14_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c14_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
      '<a href="#" id="c15_' + responderId + '" style="background-color:#000000;" onclick="setFontColour(\'c15_' + responderId + '\'); changeBg(\'fontColourHolder_' + responderId + '\', \'c15_' + responderId + '\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event);"> </a>' +
     '</div>' +
    '</li>' +

    '<li> <a href="#" class="emoteHolder" id="emoteHolder_' + responderId + '" onclick="toggleElementVisibility(\'emoteList_' + responderId + '\'); doCustomHide(\'roomsList\'); StyleManager.hideElementsOfClass(\'fontsList\'); StyleManager.hideElementsOfClass(\'fontSizeList\'); StyleManager.hideElementsOfClass(\'fontColourList\'); pmTextBox.focus(); handleEvent(event); return false"> <img id="PMWinEmote_' + responderId + '" src="' + emoteImgPath + 'mini_smile.gif" width="14" height="14" style="border:0;" /> </a>' +
     '<div class="emoteList" id="emoteList_' + responderId + '">' +
      '<a href="#" id=":smile_' + responderId + ':" onclick="setEmoticon(\':smile:\'); /*changeImageSrc(' + PMWinEmote + ', \'smile.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'smile.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":angry_' + responderId + ':" onclick="setEmoticon(\':angry:\'); /*changeImageSrc(' + PMWinEmote + ', \'angry.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'angry.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":cool_' + responderId + ':" onclick="setEmoticon(\':cool:\'); /*changeImageSrc(' + PMWinEmote + ', \'cool.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'cool.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":cry_' + responderId + ':" onclick="setEmoticon(\':cry:\'); /*changeImageSrc(' + PMWinEmote + ', \'cry.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'cry.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":embarassed_' + responderId + ':" onclick="setEmoticon(\':embarassed:\'); /*changeImageSrc(' + PMWinEmote + ', \'embarassed.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'embarassed.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":grin_' + responderId + ':" onclick="setEmoticon(\':grin:\'); /*changeImageSrc(' + PMWinEmote + ', \'grin.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'grin.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":heart_' + responderId + ':" onclick="setEmoticon(\':heart:\'); /*changeImageSrc(' + PMWinEmote + ', \'heart.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'heart.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":sad_' + responderId + ':" onclick="setEmoticon(\':sad:\'); /*changeImageSrc(' + PMWinEmote + ', \'sad.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'sad.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":serious_' + responderId + ':" onclick="setEmoticon(\':serious:\'); /*changeImageSrc(' + PMWinEmote + ', \'serious.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'serious.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":silly_' + responderId + ':" onclick="setEmoticon(\':silly:\'); /*changeImageSrc(' + PMWinEmote + ', \'silly.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'silly.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":tongue_' + responderId + ':" onclick="setEmoticon(\':tongue:\'); /*changeImageSrc(' + PMWinEmote + ', \'tongue.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'tongue.png" width="19" height="19" style="border:0;" /></a>' +
      '<a href="#" id=":wink_' + responderId + ':" onclick="setEmoticon(\':wink:\'); /*changeImageSrc(' + PMWinEmote + ', \'smile.png\', emoteImgPath);*/ StyleManager.hideElementsOfClass(\'emoteList\'); pmTextBox.focus(); handleEvent(event); return false"><img src="' + emoteImgPath + 'wink.png" width="19" height="19" style="border:0;" /></a>' +
     '</div>' +
    '</li>' +

  '</ul>' +

  '</div> <!-- close the text_editor -->' +
 
 '<div class="errorDiv" id="errorDiv_' + responderId + '"> </div>' +

 //'<div class="clear">&nbsp;</div>' + 

  '<div id="PMWin_' + responderId + '_creator_info" class="creator_info msg_wins_creator_info">Powered by ' + appCreator + '</div>' +

  '</div> <!-- close the container -->';

 attachEventListener(document, "click", function(event){StyleManager.hideElement('pm_active_windows_list_' + responderId);}, false);

 return c;

}