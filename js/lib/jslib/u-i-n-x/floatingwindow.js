/*
* dynamically creates a new window
*
* @author: michael orji
* @encoding: utf-8
*
* functions are called on /accessed via the FloatingWindow Object;
* properties are accessed via the FloatingWindow Object.element property
* e.g: win = new FloatingWindow(configObj);
* win.setPosition(l, t); win.element.style.visibility = 'hidden'
*
* Dependencies: StyleManager, DOMManager
*/

function FloatingWindow(){this.init.apply(this, arguments);}

FloatingWindow.prototype = { 

constructor: FloatingWindow,

 /*
 * constructor
 *
 * @param: a configuration object specifying 
 * the attributes of the window as well as it's
 * styling -the styling can be specified either as
 * configObj.attributes.style property or encapsulated
 * into a configObj.styleOptions object
 * for usage of these alternatives, see createDynamicElem()
 *
 * See create()
 */
  init : function(configObj){

   configObj             = configObj              || {}; 
   this.initOptions      = configObj.options      || {};
   this.initAttributes   = configObj.attributes   || {};
   this.initStyleOptions = configObj.styleOptions || {};

   this.initAttributes.id        = this.initAttributes.id        || 'dynamic-floating-window_' + new Date().getTime();
   this.initAttributes.className = this.initAttributes.className || 'dynamic-floating-window';  

   this.loadCallbacks     = configObj.loadCallbacks     || [];
   this.resizeCallbacks   = configObj.resizeCallbacks   || [];
   this.minimizeCallbacks = configObj.minimizeCallbacks || [];
   this.maximizeCallbacks = configObj.maximizeCallbacks || [];
   this.showCallbacks     = configObj.showCallbacks     || [];
   this.detachCallbacks   = configObj.detachCallbacks   || [];
   this.focusCallbacks    = configObj.focusCallbacks    || [];
   this.closeCallbacks    = configObj.closeCallbacks    || [];
 
   this.sendMessageBox = configObj.sendMessageBox;
   this.minZIndex      = configObj.minZIndex;
   this.maxZIndex      = configObj.maxZIndex;

   this.detached    = this.initOptions.detached; //TO DO : if this is set, the window should open in a new (pop-up) window
   this.closed      = this.initOptions.closed;
   this.isMinimized = this.initOptions.minimized; //TO DO ; if this is set, the window should open minimized
   this.isMaximized = this.initOptions.maximized; //TO DO ; if this is set, the window should open maximized
   this.isDraggable = this.initOptions.draggable;

   this.currWidth         = this.initStyleOptions.width 
   this.currHeight        = this.initStyleOptions.height; 
   this.currLeft          = this.initStyleOptions.left;
   this.currTop           = this.initStyleOptions.top;
   this.currRight         = this.initStyleOptions.right; 
   this.currBottom        = this.initStyleOptions.bottom;
   this.positioning       = this.initStyleOptions.position;
   this.isRightPositioned = ((this.currRight) ? true : false);
   this.isTopPositioned   = ((this.currTop) ? true : false);
   this.horizPos          =  ( (this.isRightPositioned) ? 'right' : 'left'); 
   this.horizPosValue     = ( (this.isRightPositioned) ? this.currRight : this.currLeft);
   this.vertPos           = ( (this.isTopPositioned) ? 'top' : 'bottom');
   this.vertPosValue      = ( (this.isTopPositioned) ? this.currTop : this.currBottom);

   this.topControlsBoxHeight = configObj.topControlsBoxHeight; //see the use of this in miminize() below

   this.element = this.create();
  
   }, //end init()

   /*
   * same as createDynamicElem(), major differences:
   * this already has it's this.elem's 'tag' property
   * set to a div, so it only creates 'div' elements
   * where as createDynamicElem() can create any HTML element;
   *
   */
   create : function(){

    var floatingDiv = document.createElement('div');
 
       for(var i in this.initAttributes){
        floatingDiv.setAttribute(i, this.initAttributes[i]);
       }

       /*
       * alternatively set styling options using 
       * this.initStyleOptions object here or 
       * the this.initAttributes.style property
       */
       for(var j in this.initStyleOptions){
        floatingDiv.style[j] = this.initStyleOptions[j];
       }

       var className = this.initAttributes.className; 
       var id = this.initAttributes.id;
       var maximizeNRestoreTitle = ( (this.isMaximized) ? 'restore' : 'maximize');           
       currFlWin = this;           
       
       var closer = this.initOptions.closable ? "<div title='close' class='"+ className +"_close' id='"+ id +"_close' onclick='setClosedWindow(\"" + id + "\"); currFlWin.close(\"" + id + "\");'></div>" : "";
       var minimizer = this.initOptions.minimizable ? "<div title='minimize' class='"+ className + "_minimize' id='"+ id +"_minimize' onclick='currFlWin.toggleMinimize(\"" + id + "\")'></div>" : "";
       var maximizer = this.initOptions.maximizable ? "<div title='"+ maximizeNRestoreTitle + "' class='"+ className + "_maximize' id='"+ id +"_maximize' onclick='currFlWin.toggleMaximize(\"" + id + "\")'></div>" : "";
       var detacher = this.initOptions.detachable ? "<div title='detach' class='"+ className + "_detach' id='"+ id +"_detach' onclick='currFlWin.detach(\"" + id + "\")'></div>" : "";
       var sizer = this.initOptions.resizable ? "<div title='resize' class='" + className + "_sizer' id='" + id + "_sizer' onmousedown='Effects.resizeElem(WindowObject.getWindowAsObject(\"" + id + "\"),event);'></div>" : "";     
       var windowTitle = this.initAttributes.title ? this.initAttributes.title : "";
       var titleBar = this.initOptions.hasTitle ? "<div class='"+ className +"_titleBar' id='"+ id +"_titleBar'>" + windowTitle + "</div>" : "";
       this.titleBar = titleBar;

       this.topControlsDivId = (id + '_topControls');
       this.topControls = '<div class="' + className + '_topControls" id="' + this.topControlsDivId + '">';
          if(this.initOptions.hasTitle) this.topControls += titleBar;
          if(this.initOptions.closable) this.topControls += closer;
          if(this.initOptions.minimizable) this.topControls += minimizer;
          if(this.initOptions.maximizable) this.topControls += maximizer;
          if(this.initOptions.detachable)this.topControls += detacher;
       this.topControls += '</div>'; 

       this.bottomControlsDivId = (id + '_bottomControls');
       this.bottomControls = '<div id="' + this.bottomControlsDivId + '">';
          if(this.initOptions.resizable) this.bottomControls += sizer;
       this.bottomControls += '</div>';

       this.contentBox = document.createElement('div'); 
       this.contentBox.setAttribute('id', id + '_contentBox');
       this.contentBox.setAttribute('className', className + '_contentBox');

       this.topControlsBox = document.createElement('div');
       this.bottomControlsBox = document.createElement('div');
       this.topControlsBox.innerHTML = this.topControls;
       this.bottomControlsBox.innerHTML = this.bottomControls;
        
       floatingDiv.innerHTML = '';
       floatingDiv.appendChild(this.topControlsBox); 
       floatingDiv.appendChild(this.contentBox);
       floatingDiv.appendChild(this.bottomControlsBox);

       return floatingDiv;

   }, //end create()

   addToDOM : function(parentElement){ //parentElement is not working yet, TO DO: make it 
     parentElement = parentElement || document.body;
     parentElement.appendChild(this.element);
     WindowObject.register(this.element);
     WindowObject.registerWinObject(this);

      if(this.isDraggable){
        Effects.enableDrag(this.topControlsBox, this.getId());
      }
      this.handleLoad(this);
   }, //end addToDom

   setId : function(newId){
    this.element.id = newId;
   }, // end setId

   getId : function(){
    return this.element.id;
   }, //end getId

   setClassName : function(newClassName){
    this.element.className = newClassName;
   },

   getClassName : function(){
    return this.element.className;
   },

   setTitle : function(title, maxLength){
      if(typeof title == 'string'){
       var displayTitle = ( title.substring(0, maxLength) + (title.length > maxLength ? '...' : '') );
       detectObjectness(this.getId() + '_titleBar').innerHTML = displayTitle;
       this.element.title = title;
      }
   }, //end setTitle

   getTitle : function(){
    return this.element.title;
   }, //end getTitle

   setContent : function(content, overWrite){

      if(overWrite){ 
 
         if(typeof content == 'string'){
             this.contentBox.innerHTML = content;
         }
         else if(typeof content == 'object'){ 
            this.contentBox.innerHTML = '';
            this.contentBox.appendChild(content); 
         }
      }

      else{
         
         if(typeof content == 'string'){
            this.contentBox.innerHTML += content;
           //this.contentBox.innerHTML = (this.getContent() + content);
         }
         else if(typeof content == 'object'){
            this.contentBox.appendChild(content);
         }
      }
      
    //this.element.innerHTML = ''; //causes IE to empty the below appended child elements
    this.element.appendChild(this.topControlsBox);
    this.element.appendChild(this.contentBox);
    this.element.appendChild(this.bottomControlsBox);  
   }, //end setContent

   getContent : function(){
    return this.contentBox.innerHTML;
   }, //end getContent

   setTopControlsContent : function(content){
    this.topControlsBox.innerHTML += content;
   },

   focus : function(winId, hideOtherWindows, exemption){
       
     var win = winId;
     var allWindows = WindowObject.getWindows();
     var winObj = WindowObject.getWindowAsObject(win);

       for(var i = 0; i < allWindows.length; i++){
          if(allWindows[i].id == win){ 
           var winArray = [];
           var minWinArray = []; 
             
             if(WindowObject.exists(win+'_close')) winArray.push(win+'_close');
             if(WindowObject.exists(win+'_minimize')) winArray.push(win+'_minimize');
             if(WindowObject.exists(win+'_titleBar')) winArray.push(win+'_titleBar');
             if(WindowObject.exists(win+'_maximize')){
              winArray.push(win+'_maximize'); 
              minWinArray.push(win+'_maximize');
             }
             if(WindowObject.exists(win+'_detach')){
              winArray.push(win+'_detach'); 
              minWinArray.push(win+'_detach');
             }

             if(winArray.length > 0){
               StyleManager.showElements(winArray);
                //if(winObj.isVisible){StyleManager.showElements(winArray);}
                //else{StyleManager.hideElements(winArray);}
             }
             if(WindowObject.exists(win+'_sizer')){
              var sizer = (win+'_sizer');
                if(winObj.isMinimized || !winObj.isVisible){StyleManager.hideElement(sizer);}
                else{StyleManager.showElement(sizer);}
             }
           
             //handle cases where the window is minimized, while having focus
             if( (winObj.isMinimized) && (minWinArray.length > 0) ){
              StyleManager.hideElements(minWinArray);
             }
             if( (!winObj.isMinimized) && (!isVisible(win + '_contentBox')) ){
              StyleManager.showElement(win + '_contentBox');
             }
           StyleManager.showElement(win);
           winObj.focused = true;
           winObj.handleFocus(winObj);
          }
          else{  
           var hide = hideOtherWindows;      
             if(WindowObject.exists(allWindows[i].id)){           
              var allWins = allWindows[i].id;
              this.unfocus(allWins, hide, exemption); 
             }
          }
       }
   }, //end focus

   unfocus : function(winId, hide, exemption){
      
     var win = winId;
     var winObj = WindowObject.getWindowAsObject(win);
     var winArray = []; 
      
            
      if(WindowObject.exists(win+'_maximize')) winArray.push(win+'_maximize');
      if(WindowObject.exists(win+'_detach')) winArray.push(win+'_detach');
      if(WindowObject.exists(win+'_sizer')) winArray.push(win+'_sizer'); 
      if(!winObj.isMinimized){ //in minimized state, we don't hide the title bar, the minimize and the close buttons
       if(WindowObject.exists(win+'_titleBar')) winArray.push(win+'_titleBar');
       if(WindowObject.exists(win+'_close')) winArray.push(win+'_close');
       if(WindowObject.exists(win+'_minimize')) winArray.push(win+'_minimize'); 
      }
      if(winArray.length > 0){StyleManager.hideElements(winArray);}
      if(hide && (win != exemption)){
       StyleManager.hideElement(win);
         if(isVisible(win + '_contentBox')){ 
          StyleManager.hideElement(win + '_contentBox');
         }
      }
    winObj.focused = false;
    this.handleUnfocus(winObj);
   }, //end unfocus

   minimize : function(winObj)
   { 

       if(winObj){
        var win = winObj.getId(); 
        var winArray = [];

         if(isVisible(win + '_contentBox')){
          var currDimensions = winObj.getSize();
          winObj.currHeight = parseInt(currDimensions.height);
         }            
         if(WindowObject.exists(win + '_sizer')){winArray.push(win + "_sizer");}
         if(WindowObject.exists(win + '_detach')){winArray.push(win + "_detach");}
         if(WindowObject.exists(win + '_maximize')){winArray.push(win + "_maximize");}
         if(winArray.length > 0){StyleManager.hideElements(winArray);}  
       StyleManager.hideElement(win + '_contentBox');
       StyleManager.setStyle(winObj.getId(), {'height' : winObj.topControlsBoxHeight});
       winObj.isMinimized = true;
      }
   }, //end minimize

   toggleMinimize : function(win)
   {
    var winObj = WindowObject.getWindowAsObject(win);
  
      if(isVisible(win + '_contentBox')){
       winObj.minimize(winObj);
         
      }
      else{
       var restoreHeight = winObj.currHeight + 'px';
       StyleManager.setStyle(winObj.getId(), {'height' : restoreHeight});
       StyleManager.showElement(win + '_contentBox');
       winObj.isMinimized = false;
      }
    winObj.handleMinimize(winObj);
   }, //end toggleMinimize

   maximize : function(winObject){
   
      if(!winObject.widthMaximized && !winObject.heightMaximized){      
       var bWidth = parseInt(Browser.Window.size().width);
       var bHeight = parseInt(Browser.Window.size().height); 
       var currDimensions = winObject.getSize(); 
       var currPosition = winObject.getPosition();

       winObject.currWidth = parseInt(currDimensions.width); 
       winObject.currHeight = parseInt(currDimensions.height); 
       winObject.currLeft = parseInt(currPosition.left);
       winObject.currTop = parseInt(currPosition.top);
       winObject.currRight = parseInt(currPosition.right); 
       winObject.currBottom = parseInt(currPosition.bottom);
       winObject.positioning = currPosition.positioning;
       winObject.horizPos =  ( (winObject.isRightPositioned) ? 'right' : 'left'); 
       winObject.horizPosValue = ( (winObject.isRightPositioned) ? winObject.currRight : winObject.currLeft);
       winObject.vertPos = ( (winObject.isTopPositioned) ? 'top' : 'bottom');
       winObject.vertPosValue = ( (winObject.isTopPositioned) ? winObject.currTop : winObject.currBottom);
       var winPositioning = winObject.positioning;
         if( (winObject.horizPos == 'right') && (winObject.vertPos == 'bottom')){winObject.setPosition({'positioning' : winPositioning, 'right' : '0', 'bottom' : '0'});}
         else{winObject.setPosition({'positioning' : winPositioning, 'left' : '0', 'top' : '0'});}
       winObject.setSize(bWidth, bHeight);
       winObject.widthMaximized = true;
       winObject.heightMaximized = true;
       winObject.isMaximized = true; 
       winObject.handleResize(winObject); winObject.handleMaximize(winObject);     
      }// end if(!winObject.widthMaximized && !winObject.heightMaximized)
   }, //end maximize
   
   toggleMaximize : function(win){    
    var winObject = WindowObject.getWindowAsObject(win); 

      if(!winObject.widthMaximized && !winObject.heightMaximized){winObject.maximize(winObject);}
      else{ 
       var winPositioning = winObject.positioning; 
       var winHorizPos = winObject.horizPos;
       var winHorizPosValue = winObject.horizPosValue;
       var winVertPos = winObject.vertPos;
       var winVertPosValue = winObject.vertPosValue; 

         if((winObject.horizPos == 'right') && (winObject.vertPos == 'bottom')){winObject.setPosition({'positioning': winPositioning, 'right' : winHorizPosValue, 'bottom': winVertPosValue}); }  
         else{winObject.setPosition({'positioning': winPositioning, 'left' : winHorizPosValue, 'top': winVertPosValue});}
       winObject.setSize(winObject.currWidth, winObject.currHeight); 
       winObject.widthMaximized = false;
       winObject.heightMaximized = false;
       winObject.isMaximized = false;
       winObject.handleResize(winObject); winObject.handleMaximize(winObject);
      }     
   }, //end toggleMaximize 

   setMaximizeNRestoreTitle : function(winObj)
   {
      if(WindowObject.exists(winObj.getId() +  "_maximize")){
       detectObjectness(winObj.getId() + "_maximize").title = ( (winObj.isMaximized) ? 'restore' : 'maximize');
      }
   }, //end setMaximizeNRestoreTitle

   detach : function(winId){
    var winObj = WindowObject.getWindowAsObject(winId);
    this.setDetached(winObj, true);
    this.handleDetach(winObj);
   }, //end detach

   setDetached : function(winObject, detachedState){
     winObject.detached = detachedState;
     winObject.element.detached = detachedState;
   }, //end setDetached

   isDetached : function(winId){
    winId || WindowObject.getActiveWindow();
    var winObj = WindowObject.getWindowAsObject(winId);
    return winObj.detached || winObj.element.detached;
   }, //end isDetached

   show : function(windowId){
    winId = windowId || this.element;
    winObj = ( (windowId) ? WindowObject.getWindowAsObject(windowId) : this);
    
    StyleManager.showElement(winId);
    winObj.isVisible = true;
       if(!this.showCallbacks){
        this.showCallbacks = [];
       }
       for(var i in this.showCallbacks){
          if(typeof this.showCallbacks[i] == 'function'){
           this.showCallbacks[i]();
          }
       } 
   }, //end show

   hide : function(winId, hide, exemption){
    var winObj = WindowObject.getWindowAsObject(winId);

    this.unfocus(winId, hide, exemption);
    StyleManager.hideElement(winId);
    winObj.isVisible = false;
       if(!winObj.showCallbacks){
        winObj.showCallbacks = [];
       }
       for(var i in winObj.showCallbacks){
          if(typeof winObj.showCallbacks[i] == 'function'){
           winObj.showCallbacks[i]();
          }
       }
   }, //end hide

   close : function(winId){    
    var win = winId;
    var winObj = WindowObject.getWindowAsObject(win);
      
      if(WindowObject.exists(win)){
       this.hide(win, true, 'none');
       winObj.isVisible = false;
       winObj.closed = true;
       winObj.handleClose(winObj);
       setTimeout(function(){winObj.destroy(winId);}, 1000);//allow the handleClose to execute the close callbacks before removing the window from the DOM
      }
   }, //end close

   destroy : function(winId){
    WindowObject.destroyWindow(winId);         
   }, //end destroy

   setSize : function(w, h) {
    this.setWidth(w);
    this.setHeight(h);
   }, //end setSize

   getSize : function() {
    return {
           width: (this.element.width) ? parseInt(this.element.width) : parseInt(this.element.style.width), 
           height: (this.element.height) ? parseInt(this.element.height): parseInt(this.element.style.height)
          };
   }, //end getSize

   setWidth : function(w) {
    w += '';
     if(w.indexOf('px') == -1){w += 'px';}
    this.element.style.width = w; 
   }, //end setWidth

   setHeight : function(h) {
    h += '';
    if(h.indexOf('px') == -1){ h += 'px';}
   this.element.style.height = h; 
   }, //end setHeight

   _setAttribute : function(attributeName, attributeValue){this.element.setAttribute(attributeName, attributeValue);}, //end _setAttribute
   _getAttribute : function(attributeName){
    var att = this.element.getAttribute(attributeName);
    return att;
   }, //end _getAttribute

   _getAttributes : function(){
     var attrs = [];
      for(var i in this.element){
       attrs.push(this.element[i]);
      }
     return attrs;
   }, //end _getAttributes

   _setStyles : function(cssStyleSyntaxDefinition){this.element.setAttribute('style', cssStyleSyntaxDefinition);},  //usage example: currObj._setStyles('font-family:arial;color:#c00;');
   _setStyle : function(styleName, styleValue){this.element.style['styleName'] = styleValue;},  //usage example: currObj._setStyle('zIndex', '100');
   _getStyle : function(styleName){return this.element.style[styleName];}, 

   /*
   * positions the window
   * centers the window if no positions are specified
   */
   setPosition : function (configObj) {

    configObj = configObj || {};
    
    var windowWidth = Browser.Window.size().width; 
    var windowHeight = Browser.Window.size().height;      
    var winHeight = size(this.element).height;  //this.element.size().height;
    var winWidth  = size(this.element).width; //this.element.size().width; 

    var top = (configObj.top) ? configObj.top : ( (windowHeight - winHeight)/2 );  
    var left = (configObj.left) ? configObj.left : ( (windowWidth - winWidth)/2 );
    var bottom = configObj.bottom;
    var right = configObj.right; 
    
    var positioning = ( (configObj.positioning) ? configObj.positioning : 'absolute');

    this.hasFixedPositioning = ( (positioning == 'fixed') ? true : false );
    this.hasRelativePositioning = ( (positioning == 'relative') ? true : false );
    this.hasAbsolutePositioning = ( (positioning == 'absolute') ? true : false ); 

    this.isTopPositioned = (configObj.top) ? true : false;
    this.isLeftPositioned = (configObj.left) ? true : false;
    this.isBottomPositioned = (configObj.bottom) ? true : false;
    this.isRightPositioned = (configObj.right) ? true : false; 
    
    if( (String(top).indexOf('px') == -1) && (parseInt(top) != 0) ){top += 'px';}
    if( (String(left).indexOf('px') == -1) && (parseInt(left) != 0) ){left += 'px';}
    if( (String(bottom).indexOf('px') == -1) && (parseInt(bottom) != 0) ){bottom += 'px';}
    if( (String(right).indexOf('px') == -1) && (parseInt(right) != 0) ){right += 'px';}
    
    this.element.style.position = positioning;
      if(configObj.top) this.element.style.top = top;   
      if(configObj.left)this.element.style.left = left;
      if(configObj.bottom)this.element.style.bottom = bottom;   
      if(configObj.right)this.element.style.right = right;

   }, //end setPosition(), this is complete

   getPosition : function() {
   return {
           left: (this.element.left) ? parseInt(this.element.left) : ( (this.element.style.left) ? parseInt(this.element.style.left) : 'undefined' ), 
           top: (this.element.top)? parseInt(this.element.top) : ( (this.element.style.top)? parseInt(this.element.style.top) : 'undefined'),
           bottom: (this.element.bottom) ? parseInt(this.element.bottom) : ( (this.element.style.bottom) ? parseInt(this.element.style.bottom) : 'undefined' ),
           right: (this.element.right) ? parseInt(this.element.right) : ( (this.element.style.right) ? parseInt(this.element.style.right) : 'undefined' ),
           positioning: (this.element.position) ? this.element.position : ( (this.element.style.position) ? this.element.style.position : 'undefined')
          };
   }, //end getPosition


   /*******************************
   * FloatingWindow Event Handlers
   *******************************/

   handleLoad : function(winObj){

      if(!winObj.loadCallbacks){
        winObj.loadCallbacks = [];
      }
      for(var i in winObj.loadCallbacks){
         if(typeof winObj.loadCallbacks[i] == 'function'){
           winObj.loadCallbacks[i]();
         }
      }

   }, //end handleLoad

   handleFocus : function(winObj){ 
     
      if(!winObj.focusCallbacks){
        winObj.focusCallbacks = [];
      }
      for(var i in winObj.focusCallbacks){
         if(typeof winObj.focusCallbacks[i] == 'function'){
           winObj.focusCallbacks[i]();
         }
      }
   }, //end handleFocus

   handleUnfocus : function(winObj){ 
       if(!winObj.unfocusCallbacks){
        winObj.unfocusCallbacks = [];
      }
      for(var i in winObj.unfocusCallbacks){
         if(typeof winObj.unfocusCallbacks[i] == 'function'){
           winObj.unfocusCallbacks[i]();
         }
      }
   }, //end handleUnfocus

   handleResize : function(winObj){ 
     winObj = winObj || WindowObject.getActiveWindowAsObject();
      if(!winObj.resizeCallbacks){
       winObj.resizeCallbacks = [];
      }
      for(var i in winObj.resizeCallbacks){
         if(typeof winObj.resizeCallbacks[i] == 'function'){
           winObj.resizeCallbacks[i]();
         }
      }
   }, //end handleResize

   handleMinimize : function(winObj){ 
       if(!winObj.minimizeCallbacks){
        winObj.minimizeCallbacks = [];
      }
      for(var i in winObj.minimizeCallbacks){
         if(typeof winObj.minimizeCallbacks[i] == 'function'){
           winObj.minimizeCallbacks[i]();
         }
      }
   }, //end handleMinimize

   handleMaximize : function(winObj){ 
     winObj.setMaximizeNRestoreTitle(winObj);
       if(!winObj.maximizeCallbacks){
        winObj.maximizeCallbacks = [];
      }
      for(var i in winObj.maximizeCallbacks){
         if(typeof winObj.maximizeCallbacks[i] == 'function'){
           winObj.maximizeCallbacks[i]();
         }
      }
   }, //end handleMaximize

   handleDetach : function(winObj){ 
   
       if(!winObj.detachCallbacks){
        winObj.detachCallbacks = [];
      }
      for(var i in winObj.detachCallbacks){
         if(typeof winObj.detachCallbacks[i] == 'function'){
           winObj.detachCallbacks[i]();
         }
      }
   }, //end handleDetach

   handleClose : function(winObj){ 
       if(!winObj.closeCallbacks){
        winObj.closeCallbacks = [];
      }
      for(var i in winObj.closeCallbacks){
         if(typeof winObj.closeCallbacks[i] == 'function'){
           winObj.closeCallbacks[i]();
         }
      }
   } //end handleClose
}//end of FloatingWindow