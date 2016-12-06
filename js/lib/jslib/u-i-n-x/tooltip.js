/*
* @author: Michael Orji
*
* Dependencies: errorManager, eventManager, Mouse, Browser, FloatingWindow, windowObject
*/
function Tooltip(evt, configObj)
{
	configObj.attributes.id        = configObj.attributes.id        || 'dynamic_tooltip_' + new Date().getSeconds();
	configObj.attributes.className = configObj.attributes.className || 'dynamic_tooltip';
	 
	this.init(configObj);
	this.setContent(this.initOptions.content, true);
	this.closeTimer = this.initOptions.closeTimer || 0;
	this.load(evt);
}

Tooltip.prototype = {
	
	constructor: Tooltip,
 
	load : function(evt)
	{
		/*
		var cls = this.initAttributes.className; //getClassName();
		var instances = $Class(cls, strict=true);
     
		if(instances)
		{
			for(var i = 0; i < instances.length; i++)
			{
				if(instances[i].id != this.id)
				{
					instances[i].unload(evt);
				}
			}
		}
		*/
   
		var _this = this;
		this.updatePosition(evt);
		this.setListeners();
		this.addToDOM();  
		this.show();
    
		var triggerTarget = this.triggerTarget = EventManager.eventTarget(evt);
		EventManager.attachEventListener(triggerTarget, "mousemove", function(event){_this.updatePosition(event);}, false);
	},
   
	setListeners : function()
	{
		var _this   = this;
		var id      = this.getId();
		var timer   = this.closeTimer;
		var unload  = this.unload;
  
		EventManager.attachEventListener(document, "mouseover", function(event){unload.call(_this, event, timer)});
		EventManager.attachEventListener(document, "click",     function(event){unload.call(_this, event)});
	},
	
	updatePosition : function(evt)
	{
		var MP    = Mouse.Position(evt); 
		var XPos  = MP.left;
		var YPos  = MP.top;
		this.setPosition({'top': parseInt(YPos)+2, 'left': parseInt(XPos)+2});
	},
	
	unload : function(event, timer)
	{
    
		var _this = this;
		var id    = this.getId();
		timer     = timer  || 0;
		var et    = EventManager.eventTarget(event);
  
		if(Mouse.isOutside(id, event) && (et != _this) && (et != $O(id + '_contentBox')) && (et != _this.triggerTarget))
		{
			setTimeout(function(){ if($O(id)){WindowObject.destroyWindow(id); }}, timer);
		} 
	}
}

extend(Tooltip, FloatingWindow);