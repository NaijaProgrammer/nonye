//utf-8

/*
* @date: March 13, 2012
*/
function includeJSPHP(JSPHPFilesArray, overWrite){

   var str = ''; 
 
   for(var i in JSPHPFilesArray){ 
      
      if(overWrite){
         if(fileExists(JSPHPFilesArray[i], "php") ){ 
          removeFile(JSPHPFilesArray[i], "php");
         }
         if(JSPHPFilesArray[i].indexOf('.php') == -1){
            JSFilesArray[i] += '.php';
          } 
      }

      if( !fileExists(JSPHPFilesArray[i], "php") ){ //call the fileExists function to include the current 'js' file in the 'filesadded' string
          if(JSPHPFilesArray[i].indexOf('.php') == -1){
            JSPHPFilesArray[i] += '.php';
          } 
      } 

     var JSLink = document.createElement("script");
     JSLink.setAttribute("type", "text/javascript");
     JSLink.setAttribute("src", JSPHPFilesArray[i]);

      if(typeof JSLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(JSLink);
      } 
   }
}

/*
* dynamically loads javascript files
* @argument - array of js files to load
* @author: michael orji
* @date: Oct 2, 2010
* @modified: Feb 28, 2012
*/
function includeJS(JSFilesArray, overWrite, isPHPFile){

   if(isPHPFile){
    includeJSPHP(JSFilesArray, overWrite);
    return;
   }

 var str = ''; 
 
   for(var i in JSFilesArray){ 
      
      /*
      * if the file already exists (in the 'filesadded' string) and we're overwriting the file in the document tree, 
      * we pull it out from the document tree,  
      * then re-insert it into the tree, this way, we remain consistent in the 'filesadded' string since we don't alter (i.e remove it from)
      * the string when we remove the file from the document tree,
      */
      if(overWrite){
         if(fileExists(JSFilesArray[i], "js") ){ 
          removeFile(JSFilesArray[i], "js");
         }
         if(JSFilesArray[i].indexOf('.js') == -1){
            JSFilesArray[i] += '.js';
          } 
      }

      if( !fileExists(JSFilesArray[i], "js") ){ //call the fileExists function to include the current 'js' file in the 'filesadded' string
          if(JSFilesArray[i].indexOf('.js') == -1){
            JSFilesArray[i] += '.js';
          } 
      } 

     var JSLink = document.createElement("script");
     JSLink.setAttribute("type", "text/javascript");
     JSLink.setAttribute("src", JSFilesArray[i]);

      if(typeof JSLink != "undefined"){
       document.getElementsByTagName("head")[0].appendChild(JSLink);
      } 
   }
}


/*
* dynamically loads css stylesheets
* @argument - array of css files to load
* @author: michael orji
* @date: sept 29, 2010
* based on ajax_im's ajax_im.js
*/
function loadCSS(cssFilesArray){
   
   for(var i in cssFilesArray){
      if( !fileExists(cssFilesArray[i], "css") ){
       var CSSLink = document.createElement("link");
       CSSLink.setAttribute("rel", "stylesheet");
       CSSLink.setAttribute("type", "text/css");

         if(cssFilesArray[i].indexOf('.css') == -1){
          cssFilesArray[i] += '.css';
         }
   
       CSSLink.setAttribute("href", cssFilesArray[i]);
     
         if(typeof CSSLink != "undefined"){
          document.getElementsByTagName("head")[0].appendChild(CSSLink);
         }
      }
   }
}


/*
* @Credits: http://www.javascriptkit.com/javatutors/loadjavascriptcss.shtml
* @date; July 13, 2011; 14:24
* @modified by: Michael Orji
*/
var filesadded="" //list of files already added

function fileExists(filename, filetype){
   if (filesadded.indexOf( "[" + filename + "@" + filetype + "]" ) == -1){
    filesadded += "[" + filename + "@" + filetype + "]," //List of files added in the form "[filename1@js],[filename2@css],etc" I -- (Michael O.) -- added the @ symbol and the comma(,) incase a situation arises where I need to split() the string into an array
    return false;
   }
 return true;
}


/*
* @credits: http://www.javascriptkit.com/javatutors/loadjavascriptcss2.shtml
* @date: Feb 28, 2012
* @modified by: Michael Orji
*/
function removeFile(filename, filetype){
 var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist from
 var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
 var allsuspects=document.getElementsByTagName(targetelement)
   for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
      if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1){
       allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
      }
   }
}