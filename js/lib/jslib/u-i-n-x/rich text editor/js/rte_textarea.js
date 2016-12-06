function storeRangeOfCurrentSelection() {
		
	if (document.all) {

	   var selection = document.selection;
 
	   if (selection != null) {
             rng = selection.createRange();
           }
	} 
        else {	
	  var selection = document.getSelection();
	  rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange(); 
	}
}


function retrieveSelectedRange(){

   if (document.all) {
		
      var selection = document.selection; 

      if (selection != null) {
	var newRng = selection.createRange();
	newRng = rng; 
	newRng.select();
      }
   }

   else{
    var newRng = document.getSelection(); 
    newRng.addRange(rng);    
   }
}


function RTE(opts){

    opts                              = opts || {};
    this.originalEditorWindow         = $O(opts.textField);
    this.initOnLoad                   = opts.initOnLoad || false; 
    this.container                    = '';
    this.editBox                      = null;
    this.controlsContainer            = document.createElement('div');
    this.menuBar                      = document.createElement('div');
    this.toolBar                      = document.createElement('div');
    this.controlsContainer.className  = 'rteControlsContainer';
    this.directory                    = get_path('rich text editor') + 'rich text editor/';

    var libPath = get_path('JSLib.js').substring(0); 
    $JS([libPath + 'toolTip.js', libPath + 'colorPallette.js']);
    $CSS([this.directory + 'styles/styles']);

   this.create = function(){
 
	var _this           = this;
	var textarea        = this.originalEditorWindow;
	var container       = document.createElement('div');
	var iframe          = document.createElement('div');
        
	iframe.className       = 'rte-editbox';
	iframe.contentEditable = true;
	iframe.viewMode        = 1; //TO DO: find out why this function is called twice as evidenced by this alert alert(textarea);
        if(textarea == null){ return; }
	$Html(iframe, $Html(textarea));
        
	this.editBox = iframe;
    
	if(textarea.form){ 
		var hiddenField   = document.createElement("input");
		hiddenField.type  = "text";
		hiddenField.name  = textarea.name;
		hiddenField.value = iframe.innerHTML;
		textarea.form.appendChild(hiddenField);
		this.hiddenField = hiddenField;
		textarea.form.onsubmit = function(){ return _this.validateViewMode(_this.editBox) }
	}

	container.appendChild(this.createControls());
	container.appendChild(iframe);
    
	container.onmouseover = function(){ //only when this is in the body can we use the $O in activateControls and in attachEventHandlers
		if(!_this.initialized){ 
			 _this.attachEventHandlers();
			 _this.initialized = true;
		}  
	}
       
	this.container = container;
	this.replaceTextField(); 
   }

   this.replaceTextField = function(){
   
	var _this     = this;
	var textArea  = this.originalEditorWindow;
	var container = this.container;
	var iframe    = this.editBox;
	var width     = parseInt(getStyleValue(textArea, 'width'))  || size(textArea).width;
	var height    = parseInt(getStyleValue(textArea, 'height')) || size(textArea).height;
	var zIndex    = parseInt(getStyleValue(textArea, 'zIndex')) || 0;

	var viewModeSwitcherBox  = document.createElement('div');
	var viewModeSwitcher     = document.createElement('input');
	var viewModeSwitcherLbl  = document.createElement('label');

	viewModeSwitcher.type       = 'checkbox';
	viewModeSwitcher.checked    = false;
	viewModeSwitcher.onclick    = execute(this.toggleViewMode, iframe);
	viewModeSwitcherLbl.onclick = function(){_this.toggleViewMode(iframe); viewModeSwitcher.checked = !viewModeSwitcher.checked}
	viewModeSwitcherLbl.appendChild(document.createTextNode("Show HTML"));
    
	viewModeSwitcherBox.className = 'rteViewModeSwitcherBox';
	viewModeSwitcherBox.appendChild(viewModeSwitcher);
	viewModeSwitcherBox.appendChild(viewModeSwitcherLbl);

	$Style(container).display  = 'inline-block';
	$Style(container).width    = width    + 'px';
	$Style(container).zIndex   = (zIndex  + 5) + '';
	$Style(container).padding  = '5px';
	$Style(iframe).width       = (width   - 15) + 'px';
	$Style(iframe).height      = (height  - 15) + 'px';
	$Style(this.controlsContainer).width  = $Style(iframe).width;

	container.appendChild(viewModeSwitcherBox);
	if(textArea.parentNode){
        	textArea.parentNode.replaceChild(container, textArea);
	}
   }

   this.createControls = function(){

	var d     = new Date().getSeconds();
	var _this = this;
	var resources = this.rte_controls;
    
	for(var i = 0; i < resources.menus.length; i++){ 

		var menu = document.createElement("select");
       		menu.appendChild(createMenuItem(resources.menus[i].header));
       		var menuOpts = resources.menus[i].values;

         	if (menuOpts.constructor === Array) {
	    		for (var opt = 0; opt < menuOpts.length; opt++){
             			menu.appendChild(createMenuItem(menuOpts[opt]));
            		}
	 	} 
         	else {
	    		for (var opt in menuOpts) { 
             			menu.appendChild(createMenuItem(opt, menuOpts[opt])); 
            		}				
	 	}

       		menu.onchange      = execute(this.runSelectCommand, menu, resources.menus[i].command, this.editBox); 
       		this.menuBar.appendChild(document.createTextNode(" "));
       		this.menuBar.appendChild(menu); 
      	}

      	for(var x in resources.buttons){ 

         	var currObj = resources.buttons[x];
         	var opts    = {'src': this.directory + 'icons/' + currObj.image + '.png', 'id': currObj.id + d, 'title': currObj.title, 'className': 'rteControlBtns', 'width': '16px', 'height': '16px'}
         	var img     = createImage(opts);

         	if(currObj.command == 'forecolor'){ 
            		img.onclick = function(event){_this.showColorPallette(event, 'forecolor')}
         	}

         	else if(currObj.command == 'backcolor'){ 
           		img.onclick = function(event){_this.showColorPallette(event, 'backcolor')}
         	}

         	else{
          		var cmdVal  = currObj.value ? currObj.value : null;
          		img.onclick = execute(this.runCommand, currObj.command, false, cmdVal, this.editBox)   
        	 }

         	this.toolBar.appendChild(img);
     	 } 
     
    	this.controlsContainer.appendChild(this.menuBar);       
    	this.controlsContainer.appendChild(this.toolBar);
    	return this.controlsContainer;
   }

   this.showColorPallette = function(event, cmd){
    	var _this = this;
      	storeRangeOfCurrentSelection();
       	new ColorPallette(event, {'colorSelectCallback': function(color){retrieveSelectedRange(); _this.runCommand(cmd, false, color, _this.editBox);}})
   } 
          

   this.attachEventHandlers = function(){
    	var _this = this;
    	var textarea = this.originalEditorWindow;
    	EventManager.attachEventListener(_this.editBox,           'click', function(event){_this.hiddenField.value = _this.editBox.innerHTML;}, false);
    	EventManager.attachEventListener(_this.editBox,           'focus', function(event){_this.hiddenField.value = _this.editBox.innerHTML;}, false);
    	EventManager.attachEventListener(_this.editBox,           'keyup', function(event){_this.hiddenField.value = _this.editBox.innerHTML;}, false);
    	EventManager.attachEventListener(_this.editBox,           'blur',  function(event){_this.hiddenField.value = _this.editBox.innerHTML;}, false);
    	EventManager.attachEventListener(_this.controlsContainer, 'click', function(event){_this.hiddenField.value = _this.editBox.innerHTML;}, false);
   }

   this.init = function(){
   
	var _this = this;

	new XHR
	(
       
        	{
			'type': 'GET', 'url': _this.directory  + 'js/rte_controls.json', 'requestData': '', 'readyStateCallback' : function(){},
			//'debugCallback': function(reply){alert(reply)},
			'successCallback' : function(reply)
			{
                        
				_this.rte_controls = eval( '(' + reply.parsedValue + ')' );
				_this.create(); //TO DO: find out why this is called twice 
				
			}
		}
    
    
   	)
   }

   if(this.initOnLoad){ this.init();}
}

RTE.prototype = {

	constructor: RTE,

   	validateViewMode : function(theWindow){

      		if(theWindow.viewMode == 2){ //code view
       			alert("Uncheck \u00AB Show HTML \u00BB.");
       			theWindow.focus();
       			return false;
      		}

      		return true;
   	},

   	toggleViewMode : function(theWindow){ 
    
      		if(theWindow.viewMode == 1){
       			var pre     = document.createElement("pre");
       			var content = document.createTextNode(theWindow.innerHTML);
       			theWindow.innerHTML = "";
       			theWindow.contentEditable = false;
       			pre.contentEditable       = true;
       			pre.appendChild(content);
       			theWindow.appendChild(pre);
       			theWindow.viewMode = 2; //code view
      		}
      		else{
       			$Html(theWindow, $textContent(theWindow));
       			theWindow.contentEditable = true;
       			theWindow.viewMode = 1; //wysiwyg view
      		}

      		theWindow.focus();
   	},

   	createLink : function(env){
    		env      = env || 'production';
    		var link = trim(prompt("Enter URL", "http:\/\/"));

      		if( link && (env == 'production') && (link.indexOf("http://") == -1) ){
       			link = 'http:\/\/' + link;
      		}

      		if(link){
       			document.execCommand('createlink', false, link);
      		}
   	},

   	addImage : function(){
    		var imgSrc = prompt("enter image location");

      		if(imgSrc != null){
       			document.execCommand('insertimage', false, imgSrc);
      		}
   	},

   	runCommand : function(cmd, showDefaultUI, value, theWindow){

      		if(!RTE.prototype.validateViewMode(theWindow)){ 
			return; 
		}
      		if(cmd == 'createLink'){
       			RTE.prototype.createLink();
       			return;
      		}
      		if(cmd == 'insertimage'){
       			RTE.prototype.addImage();
       			return;
     		 }
      
    		document.execCommand(cmd, false, value);
    		theWindow.focus();
   	},

   	runSelectCommand : function(menu, cmd, theWindow) {
      		if (menu.selectedIndex < 1) { 
			return; 
		}
      		RTE.prototype.runCommand(cmd, false, menu.options[menu.selectedIndex].value, theWindow);
      		menu.selectedIndex = 0;
   	}
}