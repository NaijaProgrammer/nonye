keyCodeIs = function(codeNum, e)
{
	e = e || window.event;

	if( isNaN(parseInt(codeNum)) )
	{
		return false;
	}

	//var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
	var code = (e.keyCode) ? e.keyCode : ((e.which) ? e.which : ((e.charCode) ? e.charCode : 0));

	return (code == codeNum);
}

handleKeyAction = function( event, keyState, codeNum, callback)
{
	keyAction = (keyState.indexOf("key") == -1) ? ("key" + keyState) : keyState;
   
	if(EventManager.eventTypeIs(keyAction, event))
	{
		if(keyCodeIs(codeNum, event))
		{
			callback();
		}
	}
}