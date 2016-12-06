/*
* @author: Michael Orji
* @date: Nov. 14, 2012
* 
* Dependencies: toolTip, EventManager
*/

function ColorPallette(event, opts)
{ 
	opts                    = opts || {};
	var emptyFunction       = function(){};
	var colorSelectCallback = opts.colorSelectCallback || emptyFunction;
   
	var table          = document.createElement('table');
	table.style.width  = '150px';
	table.style.height = '100px';
	table.border       = '1';
	table.cellspacing  = '1';
	table.cellpadding  = '0';
	table.align        = 'center'; 
   
	var colors = 
	[
      "#FFFFFF", "#FFCCCC", "#FFCC99", "#FFFF99", "#FFFFCC", "#99FF99", "#99FFFF", "#CCFFFF", "#CCCCFF", "#FFCCFF", 
      "#CCCCCC", "#FF6666", "#FF9966", "#FFFF66", "#FFFF33", "#66FF99", "#33FFFF", "#66FFFF", "#9999FF", "#FF99FF", 
      "#C0C0C0", "#FF0000", "#FF9900", "#FFCC66", "#FFFF00", "#33FF33", "#66CCCC", "#33CCFF", "#6666CC", "#CC66CC", 
      "#999999", "#CC0000", "#FF6600", "#FFCC33", "#FFCC00", "#33CC00", "#00CCCC", "#3366FF", "#6633FF", "#CC33CC",
      "#666666", "#990000", "#CC6600", "#CC9933", "#999900", "#009900", "#339999", "#3333FF", "#6600CC", "#993399", 
      "#333333", "#660000", "#993300", "#996633", "#666600", "#006600", "#336666", "#000099", "#333399", "#663366", 
      "#000000", "#330000", "#663300", "#663333", "#333300", "#003300", "#003333", "#000066", "#330099", "#330033"
	]

	var counter = 0;
	var cols = 10;
	var rows = colors.length / cols;
   
	for(var i = 1; i <= rows; i++)
	{
		var tr = document.createElement('tr');

		for(var j = 1; j <= cols; j++)
		{
			var td        = document.createElement('td');
			var img       = document.createElement('img');
			var currIndex = counter;
    
			img.width     = '1';
			img.height    = '1';
       
			td.id                    = colors[currIndex];
			td.style.width           = '10px';
			td.style.height          = '10px';
			td.style.padding         = '2px';
			td.style.backgroundColor = colors[currIndex];

			td.appendChild(img);
			tr.appendChild(td);
        
			counter++;
		}
 
		table.appendChild(tr);
	}
 
	var config = {
        'options' 	   : {'closeTimer':2000000, 'content': table},
        'attributes'   : {},
        'styleOptions' : {
			'visibility'      : 'hidden', 
			'border'          : '1px solid #ffffff', 
			'borderRadius'    : '5px',
			'width'           : '150px', 
			'height'          : 'auto', 
			'overflow'        : 'auto', 
			'backgroundColor' : '#ffffff', 
			'zIndex'          : '15'
		}
    }
   
	new Tooltip(event, config);
  
	for(var i = 0; i < colors.length; i++)
	{
		EventManager.attachEventListener( $O(colors[i]), 'mouseover', execute(mouseIsOver, colors[i]),         false);
		EventManager.attachEventListener( $O(colors[i]), 'mouseout',  execute(mouseIsOut,  colors[i]),         false);
		EventManager.attachEventListener( $O(colors[i]), 'click',     execute(colorSelectCallback, colors[i]), false);
	}

	function mouseIsOver(x)
	{
		$Style(x).border = '1px solid white';
		$Style(x).cursor = 'pointer';
		$Style(x).cursor = 'hand';
	}
	
	function mouseIsOut(x)
	{
		$Style(x).border = '2px';
	}
}