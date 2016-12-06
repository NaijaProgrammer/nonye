/* creates an XMLHttpRequest instance */
function createXmlHttpRequestObject()
{
// will store the reference to the XMLHttpRequest object
var xmlHttp;

   // this should work for all browsers except IE6 and older
   try
   {
   // try to create XMLHttpRequest object
   xmlHttp = new XMLHttpRequest();
   }

   catch(e)
   {
   // assume IE6 or older
   var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
   "MSXML2.XMLHTTP.5.0",
   "MSXML2.XMLHTTP.4.0",
   "MSXML2.XMLHTTP.3.0",
   "MSXML2.XMLHTTP",
   "Microsoft.XMLHTTP");

      // try every prog id until one works
      for (var i=0; i<XmlHttpVersions.length && !xmlHttp; i++)
      {
         try
         {
         // try to create XMLHttpRequest object
         xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
         }
         catch (e) {}
      }
   }

   // return the created object or display an error message
   if (!xmlHttp){
   //alert("Error creating the XMLHttpRequest object.");
    return null;
   }
 
   else{
    return xmlHttp;
   }
}

//function makeXHRRequest(serverURL, xhrObject, callback, requestType, params, async, callbackParams, closureName)
function makeXHRRequest(serverURL, xhrObject, callback, requestType, params, async, callbackParams, closureObject)
{

 /*
 xhrObject.setRequestHeader("If-Modified-Since", "Wed, 15 Jan 1995 01:00:00 GMT");
 xhrObject.setRequestHeader("Cache-Control","no-cache");
 xhrObject.setRequestHeader("Cache-Control", "must-revalidate");
 xhrObject.setRequestHeader("Cache-Control","no-store");
 xhrObject.setRequestHeader("Pragma","no-cache");
 xhrObject.setRequestHeader("Expires","0");
 */

 callbackParams = callbackParams || callback.arguments || '';
 xhrObject.open(requestType, serverURL, async);
   if(requestType == "POST"){xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");}
   else if(requestType == "GET"){params = null;}

   /*
   * since it's not possible to return a value through the onreadystatechange event handler, coz of the anynchronous nature of Ajax,
   * we create this inner (private) function, 'innerFX' which -- by being called by the onreadystatechange() -- 
   * then returns the server's responseText through a closure, 'closureName'
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
}