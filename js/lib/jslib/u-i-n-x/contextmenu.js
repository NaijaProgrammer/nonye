/*
* library for handling custom context Menus
* @author: michael Orji
*
* Dependencies: EventManager, MouseManager, Tooltip
* target is the HTML element to get the contextMenu
* content can either be an object retrieved through getElementById or a javascript string containing valid HTML code
*/

function ContextMenu(target, content)
{
	var libPath = get_path('JSLib.js').substring(0);
	$JS([libPath + 'toolTip']);

	var _this            = this;
	var mouseOverContext = false;
	var contextMenuIsActive = false;
	this.contextMenuDiv  = document.createElement('div');
   
	this.setContent = function()
	{
		if(isObject(content))
		{
			this.contextMenuDiv.appendChild(content);
		}
		else if(typeof content == 'string')
		{
			$Html(this.contextMenuDiv, content);
		}
		
		else
		{
			alert('Error: CustomContextMenu content must be either a string or an HTML element Object');
			return false;
		} 

		return true;
	}

	this.init = function()
	{
		if(!this.setContent()){ return; }

		this.contextMenuDiv.style.display  = 'none';
		this.contextMenuDiv.style.position = 'fixed';
		this.contextMenuDiv.style.zIndex   =  '100000';
		this.contextMenuDiv.onmouseover    = setMouseOverContext; 
		this.contextMenuDiv.onmouseout     = unsetMouseOverContext; 
		this.contextMenuDiv.oncontextmenu  = function (){ return false; }
		this.contextMenuDiv.onclick        = this.MouseIsDown;
		EventManager.attachEventListener(target, 'contextmenu', function(event){_this.show(event); handleEvent(event);}, false);
	}

	this.show = function(event)
	{
		var config = 
		{
			'options' : {'closeTimer':2000000, 'content': this.contextMenuDiv},
			'attributes'   : {},
			'styleOptions' : 
			{
				'visibility'      : 'visible', 
				'display'         : 'block', 
				'border'          : 'none', 
				'borderRadius'    : '5px',
				'width'           : '150px', 
				'height'          : 'auto', 
				'overflow'        : 'auto', 
				'backgroundColor' : '#ff0000', 
				'zIndex'          : '15'
			}
		}
   
		this.contextMenuDiv.style.display = 'block';
		new Tooltip(event, config);
		setActive();
	}

	this.hide = function(event)
	{
		if( (isActive()) && (mouseIsOverContext()) && (!EventManager.targetElementTypeIs('a', event)) )
		{
			return;
		}
	
		this.contextMenuDiv.style.display = 'none';
        unsetMouseOverContext();
		unsetActive();
    }

	this.MouseIsDown = function(event)
	{
		event = EventManager.eventObject(event);
  
		if(isActive() && mouseIsOverContext() )
		{
			return;
		}  
		else if(Mouse.Button(event) == 'left button')
		{
			this.hide(this.contextMenuDiv, event);
			unsetActive();
		}
	} 

	setActive   = function()
	{ 
		contextMenuIsActive = true; 
	}
	
	unsetActive = function()
	{ 
		contextMenuIsActive = false; 
	}
	
	setMouseOverContext = function ()
	{ 
		mouseOverContext = true; 
	}
	
	unsetMouseOverContext = function ()
	{ 
		mouseOverContext = false; 
	}
	
	mouseIsOverContext = function() 
	{ 
		return mouseOverContext; 
	}
	
	isActive = function() 
	{ 
		return contextMenuIsActive; 
	}

	this.init();
}