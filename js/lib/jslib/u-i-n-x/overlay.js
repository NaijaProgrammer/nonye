function initContentOverlayDiv()
{
	$Style('content_overlay').position        = 'absolute';
	$Style('content_overlay').left            = '255px';
	$Style('content_overlay').width           = '75%'; //'100%';
	$Style('content_overlay').backgroundColor = 'white';
	/*$Style('content_overlay').backgroundImage = "url('" + webRootPath + "resources/images/bgs/graybg.png')";*/
	$Opacity('content_overlay', .50);
	autoAdjustOverlayHeight();
	display(['content_overlay'], 'block');
}

function autoAdjustOverlayHeight()
{
	var centerHeight   = parseInt(size( $O('center_section') ).height);
	var rightHeight    = parseInt(size( $O('right_section')  ).height);
	var tallestSection = Math.max(centerHeight, rightHeight);
 
	$Style('content_overlay').height = (tallestSection + 25 ) + 'px';
	setTimeout(arguments.callee, 50);
}