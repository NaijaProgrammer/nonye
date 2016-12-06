function showLightBox(LBbgId, LBelemId, maxOpacity, fadin, fadeInSpeed)
{
	fadeInSpeed = fadeInSpeed || 5000;
	document.getElementById(LBbgId).style.display = 'block';
	document.getElementById(LBelemId).style.display='block';

	if(fadin)
	{
		Effects.fadeIn(LBbgId, maxOpacity, fadeInSpeed);
		Effects.fadeIn(LBelemId, 1, fadeInSpeed);
	}  	
}

function hideLightBox(LBbgId, LBelemId)
{
	document.getElementById(LBbgId).style.display='none';
	document.getElementById(LBelemId).style.display='none';
}