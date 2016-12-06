onLoadListener = function(fn){
   if (typeof window.addEventListener != 'undefined')
   {
    window.addEventListener('load', fn, false);
   }
   else if (typeof document.addEventListener != 'undefined')
   {
    document.addEventListener('load', fn, false);
   }
   else if (typeof window.attachEvent != 'undefined')
   {
    window.attachEvent('onload', fn);
   }
   else
   {
      if (typeof window.onload != 'function')
     {
       window.onload = fn;
      }
      else
      {
       window.onload = function(){fn();};
      }
   }
}

var scriptPath; //holds path to script files
var libPath;
var coreScriptsPath;
var styleSheetPath; //path to generic style sheet
var appRootPath = webRootPath;

onLoadListener(
function(){
   setTimeout(
      function(){
         function getScriptPath(){return webRootPath + 'js/';}
         function getStyleSheetPath(){return getScriptPath() + 'core/styles/';}
         scriptPath = getScriptPath();
         styleSheetPath = getStyleSheetPath(); 
         libPath = scriptPath + 'libraries/';
         coreScriptsPath = scriptPath + 'core/';
         includeJS([libPath + 'APILibrary', libPath + 'ajaxManager', libPath + 'browserManager', libPath + 'windowObject', libPath + 'DOMManager', libPath + 'json2']);
      }, 500
   );
   setTimeout(function(){includeJS([coreScriptsPath + 'client/chatGlobals', coreScriptsPath + 'client/dependenciesLoader']);}, 700);
   setTimeout(function(){includeJS([coreScriptsPath + 'chatStateManagers/async/chatWinStateHandler', coreScriptsPath + 'async/openIMWinsDetect'])}, 900); //get open chat windows using cookies

   setTimeout(
      function(){
       getOnlineBuddies();
       setTheme(chatTheme); 
       var aText = document.createTextNode('Chat');
       var bolText = document.createTextNode(''); //holds the number of online buddies got by the call to getOnlineBuddies() in buddiesOnline.js
       var bolspan = document.createElement('span');
       bolspan.setAttribute('id', 'bolspan');
       bolspan.appendChild(bolText);
       var chatA = document.createElement('a');
       chatA.setAttribute('href', '#');
       chatA.setAttribute('id', 'chatHolderA');
       chatA.appendChild(aText);
       chatA.appendChild(bolspan);
       chatA.onclick = function(){ 
        if(!document.getElementById('IMWin')){
         var hideOnLoad = false;
         loadIMWin(hideOnLoad);
        }
        else if(document.getElementById('IMWin')){
          WindowObject.getWindowAsObject('IMWin').focus('IMWin');
        }
        
       };
       StyleManager.setStyle(chatA, {'position' : 'fixed', 'right' : '155px', 'bottom': '5px', 'textDecoration' : 'none', 'color' : 'black'});
	   var chatContainer = '';
	   
	   try{
              if(trim(chatContainerId)){
               chatContainer = objectify(chatContainerId);
              }
	      if(!chatContainer){
		chatContainer = document.createElement('div');
                chatContainer.setAttribute('id', 'chatLinkHolder');
                StyleManager.setStyle(chatContainer, {'position' : 'fixed', 'right' : '5px', 'bottom' : '0', 'width' : '210px', 'height' : '30px', 'backgroundColor' : 'silver', 'backgroundImage' : 'url(' + appRootPath + 'resources/images/chatContainer_bg.png)'});
	      }
	   }
	   catch(e){
	   chatContainer = document.createElement('div');
           chatContainer.setAttribute('id', 'chatLinkHolder');
           StyleManager.setStyle(chatContainer, {'position' : 'fixed', 'right' : '5px', 'bottom' : '0', 'width' : '210px', 'height' : '30px', 'backgroundColor' : 'silver', 'backgroundImage' : 'url(' + appRootPath + 'resources/images/chatContainer_bg.png)'});
	   }
       chatContainer.appendChild(chatA);
       document.body.appendChild(chatContainer);
      }, 1500
   );

   /**********comment/uncomment this setTimeout/setInterval out to disable/enable the automatic loading of the main IM window in the background********/
   /*
   setInterval(
     function(){
      var hideOnLoad = true;
      if(!document.getElementById('IMWin')){loadIMWin(hideOnLoad);}
     }, 2000
   );
   */
   /*******end of block to comment out to disable the automatic loading of the main IM window in the background************/

   setInterval(
      function(){
       var chatLinkHolder = '';
         try{
          chatLinkHolder = document.getElementById('chatLinkHolder');
            //if(!document.getElementById('IMWin')){
            if( (document.getElementById('IMWin')) && isVisible('IMWin') ){
              chatLinkHolder.style.display = 'none'; //'block';
            }
            else{
             chatLinkHolder.style.display = 'block'; //'none';
            }
         }
         catch(e){}
      }, 500
   );
}  
);