/*
* Object with callable functions, a rewrite of makeXHRRequest function
*/
var XHRManager = {

   createXHRObject : function()
   {
    var xmlHttp = null;
     
      try //all browsers except IE6 and older
      {xmlHttp = new XMLHttpRequest();}
      catch(e)
      {
       //assume IE6 or older
       var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0","MSXML2.XMLHTTP.5.0","MSXML2.XMLHTTP.4.0","MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"];

         for (var i=0; i<XmlHttpVersions.length && !xmlHttp; i++)
         {
            try{xmlHttp = new ActiveXObject(XmlHttpVersions[i]);}
            catch (e) {xmlHttp = null;}
         }
      }
    return xmlHttp;   
   },

   makeXHRRequest : function(configObj, closureObject)
   {
    configObj          = configObj || {};
    var serverURL      = configObj.url;
    var xhrObject      = configObj.xhrObject;
    var callback       = configObj.callback;
    var requestType    = configObj.type || 'POST';
    var params         = configObj.params || null; //params is null for 'GET' requests, replaces if(requestType == 'GET'){params = null}
    var async          = configObj.async || true;
    var callbackParams = configObj.callbackParams || configObj.callback.arguments || '';

    /*
    xhrObject.setRequestHeader("If-Modified-Since", "Wed, 15 Jan 1995 01:00:00 GMT");
    xhrObject.setRequestHeader("Cache-Control","no-cache");
    xhrObject.setRequestHeader("Cache-Control", "must-revalidate");
    xhrObject.setRequestHeader("Cache-Control","no-store");
    xhrObject.setRequestHeader("Pragma","no-cache");
    xhrObject.setRequestHeader("Expires","0");
    */

    xhrObject.open(requestType, serverURL, async);
      if(requestType == "POST"){xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");}

      /*
      * since it's not possible to return a value through the onreadystatechange event handler, coz of the anynchronous nature of Ajax,
      * we create this inner (private) function, 'innerFX' which -- by being called by the onreadystatechange() -- 
      * then returns the server's responseText through a closure, 'closureObject.closureName'
      */
      closureObject = closureObject || {};
      var innerFX = function(val){
         closureObject.closureName = function(){
          var returnVal = val;
          return returnVal;
         }
      }
      xhrObject.onreadystatechange = function(){
       callback(callbackParams);
         if(closureObject.closureName && (typeof closureObject.closureName == 'string') ){ //only call the inner function if the user specifies that a value (a closure) should be returned
            if( (xhrObject.readyState == 4) && (xhrObject.status == 200) ){
             innerFX(xhrObject.responseText);
            }
         }
      }
    xhrObject.send(params);
   },

   parseServerReply : function(xhrObject, getStrValue){
      if(getStrValue){return xhrObject.responseText;}
    return eval( '(' + xhrObject.responseText + ')' );
   },

   isComplete : function(xhrObject){
    return ( (xhrObject.readyState == 4) && (xhrObject.status == 200) );
   }
}//end of AjaxManager object