<select name="{name}" id="{id}" class="{class}" title="{title}" style="{style_rules}">{options}</select>
<script type="text/javascript">
(function()
{ 
	if($O('{id}'))
	{	
		EventManager.attachEventListener($O('{id}'), 'change', handleOnChange, false);
		
		function handleOnChange()
		{
			if(typeof {onchange_handler} == 'function')
			{
				{onchange_handler}();
			}	
		}
	}
	
	if($O('{parent_id}'))
	{
 		EventManager.attachEventListener($O('{parent_id}'), 'change', populateChildFieldOptions, false);
	}
	
	//handleOnChange used to be here
	
 	function populateChildFieldOptions()
 	{
		var parentId     = form.getSelectElementSelectedValue('{parent_id}');
        var defaultOption = '<option value="{default_value}">{default_text}</option>';
		
		if(parseInt(parentId) <= 0)
		{
			$Html('{id}', defaultOption);
			return;
		}
		
		getChildren(parentId, function(returnedOptions){
			$Html('{id}', defaultOption + returnedOptions);
			
			if(typeof {success_callback} == 'function')
			{
				{success_callback}(returnedOptions)
			}
		});
 	}
	
 	function getChildren (parentId, successCallback)
 	{		
 		var params = "get_children" + "&parent_id=" + parentId + "";
 		new XHR
 		({
			'url':'{app_http_path}/dropdown-fetcher.php', 'type':'GET', 'requestData':params, 'debugCallback':function(reply){},
			'readyStateCallback':function()
			{
				if(typeof {ready_state_callback} == 'function')
				{
					{ready_state_callback}()
				}
			}, 
			
   			'successCallback':function(reply)
     		{
				if(reply.error)
				{ 
					return;
				}
                                
				successCallback(reply.rawValue);
     		}
  	 	});
	}
})();
</script>