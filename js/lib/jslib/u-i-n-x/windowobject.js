/*
* object for accessing any other dynamically generated window inside the current window
* (also known as the global object)
* this object cannot create any new window, to do that
* use the floatingWindow constructor in floatingWindow.js
*
* @author: michael orji
* @encoding: utf-8
*/
var WindowObject = {   

 /* 
 * keeps track of the number of dynamically generated windows on the page
 * holds the window.element property which is an html element having DOM
 * properties and methods like id, name, etc
 */
 windows: [],

 /* 
 * keeps track of the number of dynamically generated window objects on the page 
 * holds the a reference/pointer to the window object itself which contains its
 * own properties and methods
 */
 winObjects: [],

 /*
 * max z-index of dynamically generated windows
 * we set it to a number higher than the z-index of the highese element on site/page
 */
 maxZIndex: 11, 
   
   /* 
   * checks if window as an object or element with id 'win' exists in the 
   * DOM Node; returns true if exists, false else
   * @author: michael orji
   * @param: either a reference to the window object or a string representing the id of the window object 
   */
   exists: function(win){
      if( (typeof win == 'string' ) && (document.getElementById(win)) ){
       return true;
      }
      else if(typeof win == 'object'){
         for(var i = 0; i < this.winObjects.length; i++){
            if(this.winObjects[i] == win){
             return true;
            }
         }
      }
    return false;
   }, //end exists, complete

   /* registers a window with its element (FloatingWindow.element)  or id as the parameter */
   register: function(win) {
    win = ( (typeof win['id'] == 'undefined') ? win : win['id'])
    
    /*
   * register the window only if it has actually been added to the document
   */
    win = $O(win);
      if(win){
       this.windows.push(win);
     }
    return win;
   },

   /* registers a window as an object */
   registerWinObject: function(winObj)
   {
    this.winObjects.push(winObj);
    return winObj;
   },

   getWindows: function(asObject) {
      if(asObject){return this.winObjects;}
    return this.windows;
   },

   //sets a dynamically generated window to be the active one among all on the current window
   setActiveWindow: function(winId)
   {
    var win = $O(winId);
       if(win){
        win.isActive = true;
          if(typeof this.getWindowAsObject(winId).focus != 'undefined'){
           this.getWindowAsObject(winId).focus();
          }
       }//catch(e){} 
    
      //set other windows to inactive
      for(var i = 0; i < this.windows.length; i++){
         if(this.windows[i] != win){
          this.windows[i].isActive = false;
         }
      }
   },

   /*
   * @param: boolean value indicating whether to return the complete html element(true) 
   * or just its id(false), which can the be converted to a DOM object with document.getElementbyId
   */
   getActiveWindow: function(completeElem)
   {
    completeElem = completeElem || false;
      for(var i = 0; i < this.windows.length; i++){
         if(this.windows[i].isActive){
          /*
         * since windows[] can also contain a window's id, (see register() above), if we're not returning the complete elem, 
         * then we're either returning the element.id, if we're what we have in windows[] is the element's id, else we get the complete html element itself if that's what is registered in windows[]
         */
          return ( (completeElem) ? this.windows[i] : ( (typeof this.windows[i]['id'] != 'undefined') ? this.windows[i]['id'] : this.windows[i] ) ); 
         }
      }
    return -1;
   },

   //gets active window as an independent object, not as an HTML DOM(document.getElementById) object
   getActiveWindowAsObject: function()
   {
      for(var i = 0; i < this.winObjects.length; i++){
         for(var j = 0; j < this.windows.length; j++){
            if(this.windows[j].isActive){
               if(this.windows[j]['id'] == this.winObjects[i]['element']['id']){
                return this.winObjects[i];
               }
            }
         }
      }
    return null;
   },

   //gets a window as an independent object, not as an HTML DOM(document.getElementById) object
   getWindowAsObject: function(winId){ 
      for(var i = 0; i < this.winObjects.length; i++){
         if(winId == this.winObjects[i]['element']['id']){
          return this.winObjects[i];
         }
      }
    return null;
   },

   destroyWindow: function(winId)
   {
      var winObj = this.getWindowAsObject(winId);
       
       if(winObj.element && winObj.element.parentNode){
        DOMManager.removeFromParentNode(winObj.element);
       }
       
       for(var j = 0; j < this.windows.length; j++){
          var wID = ( (this.windows[j]['id'] != 'undefined') ? this.windows[j]['id'] : this.windows[j] );
          if(wID == winObj.getId()){
           this.windows.splice(j, 1);
          }
       }
       for(var i = 0; i < this.winObjects.length; i++){
          if(this.winObjects[i] == winObj){
           this.winObjects.splice(i, 1);
          }
       }
    },

   getWindowPosition: function(win, which)
   {
     win = win || this.getActiveWindow(true);
     which = which || 'current';

     win = $O(win);

     var ws = (win.style) ? win.style : win.element.style;
     var windows = this.windows;

      for(var i = 0; i < windows.length; i++){
         if(windows[i]['id'] == win.id){
            if(which == 'previous'){
             var previousWin = windows[i-1];
             ws = (previousWin.style) ? previousWin.style : previousWin.element.style; 
            }
            else if(which == 'next'){
             var nextWin = windows[i+1];
             ws = (nextWin.style) ? nextWin.style : nextWin.element.style; 
            }
         }
      }
    return {'position': ws.position, 'top': ws.top, 'left': ws.left, 'bottom': ws.bottom, 'right': ws.right};
   },


   getWindowSize: function(win, which)
   {
     win = win || this.getActiveWindow(true);
     which = which || 'current';

     win = $O(win);

     var ws = (win.style) ? win.style : win.element.style;
     var windows = this.windows;

      for(var i = 0; i < windows.length; i++){
         if(windows[i]['id'] == win.id){
            if(which == 'previous'){
             var previousWin = windows[i-1];
             ws = (previousWin.style) ? previousWin.style : previousWin.element.style; 
            }
            else if(which == 'next'){
             var nextWin = windows[i+1];
             ws = (nextWin.style) ? nextWin.style : nextWin.element.style; 
            }
         }
      }
    return {'width': ws.width, 'height': ws.height};
   }
}//end of WindowObject