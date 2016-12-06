/*
* keeps track of the number of browser windows open
* @author: Michael Orji
* @date: August 12, 2011 19:00
* @encoding: utf-8
*/

includeJS([webRootPath + 'javascript/libraries/browser']);

var BrowserWindows = {

 //currentCount : 0,

  getOpenWindows : function(){
    return parseInt(Browser.getCookie('browserCount'));
  },

   updateOpenWindows : function(add){        
      if(add == true){
       BrowserWindows.incrementOpenWindows();
      }
      else if(add == false){
       BrowserWindows.decrementOpenWindows();
      }
   },

   incrementOpenWindows : function(){
    BrowserWindows.currentCount = BrowserWindows.getOpenWindows();
      if(!BrowserWindows.currentCount){
       BrowserWindows.currentCount = 0;
      }  
    Browser.setCookie('browserCount', (BrowserWindows.currentCount+1));
   
   },

   decrementOpenWindows : function(){
     BrowserWindows.currentCount = BrowserWindows.getOpenWindows();
       if(BrowserWindows.currentCount > 0){
        Browser.setCookie('browserCount', (BrowserWindows.currentCount-1));
       
      }
   }
}//close the BrowserWindows Ojbect