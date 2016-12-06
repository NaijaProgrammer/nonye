//utf-8
var Browser = {

   UA: {

      Name: function(){
       
         if (typeof navigator.vendor != "undefined" && navigator.vendor == "KDE"){
          return 'kde';
         }
         else if (typeof window.opera != "undefined"){
          return 'opera';
         }
         else if(typeof document.all != "undefined"){
          return 'IE';
         }
         else if (typeof document.getElementById != "undefined"){
            if (navigator.vendor.indexOf("Apple Computer, Inc.") != -1){
             return 'safari';
            }
          return 'mozilla';
         }
      },

      Version: function(){
       var agent = this.UA.Name().toLowerCase();
       //var agent = Name().toLowerCase();

         if(agent == 'kde'&& typeof window.sidebar != "undefined"){
          return 'kde 3.2+';
         }
         else if(agent == 'opera'){
          agent = navigator.userAgent.toLowerCase();
          var version = parseFloat(agent.replace(/.*opera[\/ ]([^ $]+).*/, "$1"));
            if(version >= 7){
             return "opera7+";
            }
            else if (version >= 5){
             return "opera5+6";
            }
          return false;
         }
         else if(agent == 'ie'){
          agent = navigator.userAgent.toLowerCase();
            if(typeof document.getElementById != "undefined"){
             var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, "$1").replace(/ /, "");
               if (typeof document.uniqueID != "undefined"){
                  if (browser.indexOf("5.5") != -1){
                   return browser.replace(/(.*5\.5).*/, "$1");
                  }
                  else{
                   return browser.replace(/(.*)\..*/, "$1");
                  }
               }
               else{
                return "ie5mac";
               }
            }
          return false;
         }
         else if(agent == 'safari'){
            if(typeof window.XMLHttpRequest != "undefined"){
             return "safari1.2";
            }
          return "safari1";
         }
         else if(agent == 'mozilla'){
          return navigator.userAgent;
         }
      },

      OS: function(){
       var agent = navigator.userAgent.toLowerCase();
         if(agent.indexOf("win") != -1){
          return "win";
         }
         else if(agent.indexOf("mac") != -1){
          return "mac";
         }
         else{
          return "unix";
         }
      }
   }, //end of UA


   //needs no modification: created by me
   Size: {

      width: function(){
         if(typeof window.innerWidth != 'undefined'){
          return window.innerWidth; //other browsers
         } 
         else if(typeof document.documentElement != 'undefined' && 
          typeof document.documentElement.clientWidth != 'undefined' && 
          document.documentElement.clientWidth != 0) {
           return document.documentElement.clientWidth; //IE
         }
         else{
          return document.body.clientWidth; //IE
         }
      },

      height: function(){
         if(typeof window.innerWidth != 'undefined'){
          return window.innerHeight;
         } 
         else if(typeof document.documentElement != 'undefined' && 
          typeof document.documentElement.clientWidth != 'undefined' && 
          document.documentElement.clientWidth != 0) {
           return document.documentElement.clientHeight;
         }
         else{
          return document.body.clientHeight;
         }
      }
   },//end of Size


   setCookie: function(name, value, daysTillExpire, path, domain, secure){
    
     var cookieName = trim(name);
     var cookieValue = trim(value);
     var theCookie = cookieName + "=" + cookieValue;
   
      if(daysTillExpire){
       var d = new Date();
       var expires = d.setTime(d.getTime() + (daysTillExpire*24*60*60*1000)).toGMTString();
       theCookie += "; expires=" + expires;
      }  
      if(path){theCookie += "; path=" + path;}
      else{theCookie += "; path=/";}
      if(domain){theCookie +=  "; domain=" + domain;}
      if(secure){theCookie += "; secure";}
     document.cookie = theCookie;
   },

   getCookie: function(searchName){
    var cookies = document.cookie.split(";");
       for (var i = 0; i < cookies.length; i++)
      {
        var cookieCrumbs = cookies[i].split("=");
        var cookieName = cookieCrumbs[0]; 
        var cookieValue = cookieCrumbs[1];
     
          if (trim(cookieName) == trim(searchName))
         { 
           return trim(cookieValue);
         }
      }
   return null;
   },

   setStatusBarMsg: function(msg)
   {
    window.status = msg;
    return true;
   } //usage e.g: <a href="" onmouseover="func(); return WindowObject.setStatusBarMsg('hi')"> </a>

} //end of Browser
