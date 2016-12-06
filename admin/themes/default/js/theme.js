function showProcessing(elem)
{
	$Style(elem).backgroundImage = 'url("' + siteURL + '/resources/images/processing.svg")';
}
	
function hideProcessing(elem)
{
	$Style(elem).backgroundImage = '';
}

function disable(elem)
{
	Site.Util.disableElement(elem);
}

function enable(elem)
{
	Site.Util.enableElement(elem);
}

function displayStatusMessage(msgField, msg, msgType)
{
	switch(msgType)
	{
		case 'error' : $Style(msgField).color = '#900'; break;
		default      : $Style(msgField).color = '#090'; break;
	}
		
	$Html(msgField, msg);
}