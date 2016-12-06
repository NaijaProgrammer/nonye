/**
* @date June 5, 2013
* @author: Michael Orji
*/
form = {

	/**
	* Example usage: 
	* var opts = {'selectElement':'department', 'valueAtZero':'Department', 'optionsObj': { '1':'Law', '2':'Philosophy', '3':'Psychology' } }
	* form.populateSelectElementWithOptions(opts);
	*/
	populateSelectElementWithOptions : function(configObj)
	{
		configObj         = configObj || {}
		var selectElement = $O(configObj.selectElement);
		var valueAtZero   = configObj.valueAtZero;
		var optionsObj    = configObj.optionsObj; //e.g { {'name':'value'}, {'name2':'value2'}, ... }
 
		/*
		* selectElement.add(new Option(valueAtZero, 0, false, false), true); 
		* wasn't working with mozilla browsers
		*/
 		selectElement.options[0] = new Option(valueAtZero, 0, false, false); 
        	
		var counter = 1;

		for(var i in optionsObj)
		{
         	selectElement.options[counter++] = new Option(optionsObj[i], i, false, false);
		}
	},
	
	getSelectElementSelectedOptionObject : function(selectElem)
	{
		selectElem  = $O(selectElem);          
		var selElem = selectElem['options'];
		var selectedOptionObject = selElem[selElem['selectedIndex']];
		return selectedOptionObject;
	},

	getSelectElementSelectedValue : function(selectElem)
	{
		selectElem        = $O(selectElem);          
		var selElem       = selectElem['options'];
		var selectedValue = selElem[selElem['selectedIndex']].value;
		return selectedValue
	},
	
	getSelectElementSelectedText : function(selectElem)
	{
		selectElem       = $O(selectElem);          
		var selElem      = selectElem['options'];
		var selectedText = selElem[selElem['selectedIndex']].text;
		return selectedText
	},

	setSelectElementSelectedValue : function(selectElem, value)
	{
		selectElem = $O(selectElem);
		if(!isEmpty(value))
		{
 			for(var i=0; i<selectElem['options'].length; i++) 
 			{
  				if ( selectElem['options'][i].text == value )
  				{
    				selectElem['selectedIndex'] = i;
    				break;
  				}
			}
		}
	},
	
	setSelectElementSelectedText : function(selectElem, text)
	{
		selectElem = $O(selectElem);
		var selElem = selectElem['options'];
		selElem[selElem['selectedIndex']].text = text;
	}
}