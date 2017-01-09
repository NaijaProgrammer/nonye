<script>
if( typeof handleThirdPartyAuth === 'undefined'){
	function handleThirdPartyAuth(authObj)
	{
		authObj = authObj || {};
		//authObj.provider; //google, linkedin
			
		if( (typeof authObj.status === 'string') && (authObj.status == 'success' ) )
		{
				location.reload();
		}
		else
		{
			if(typeof authObj.message === 'string')
			{
				alert(authObj.message);
			}
		}
	}
}
</script>