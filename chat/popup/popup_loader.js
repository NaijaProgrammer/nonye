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

function getScriptPath(){return webRootPath + 'js/';}
function getStyleSheetPath(){return getScriptPath() + 'core/styles/';}
var scriptPath = getScriptPath();
var styleSheetPath = getStyleSheetPath();
var coreScriptsPath = '';
var libPath = '';

onLoadListener(function(){

setTimeout(function(){   
 var incLink = document.createElement("script");
 incLink.setAttribute("type", "text/javascript");
 incLink.setAttribute("src", scriptPath + "libraries/include.js");
      if(typeof incLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(incLink);
      }
}, 200);
setTimeout(function(){
 libPath = scriptPath + 'libraries/';
 coreScriptsPath = scriptPath + 'core/';
 includeJS([libPath + 'APILibrary', libPath + 'ajaxManager', libPath + 'browserManager', libPath + 'windowObject',  libPath + 'DOMManager', libPath + 'json2']);
}, 300);

setTimeout(function(){includeJS([coreScriptsPath + 'client/chatGlobals', coreScriptsPath + 'client/dependenciesLoader', coreScriptsPath + 'chatStateManagers/async/chatWinStateHandler']);}, 500);
setTimeout(function(){setTheme(chatTheme)}, 850); //ensure scripts are loaded before setting the theme using function defined in themeSetter.js

}
);