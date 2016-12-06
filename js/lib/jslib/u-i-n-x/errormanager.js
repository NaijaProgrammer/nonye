/* 
* @author: Michael Orji
* @date: March 25, 2012
*/
var errorManager = {

	logError : function(msg, opts)
	{
		if(typeof console != 'undefined' && typeof console.log == 'function')
		{
			return console.log('JSLib custom errorManager >> ' + msg);
		}
		
		else
		{
			opts = opts || {};
			opts.message = msg;
			this.displayError(opts);
		}
	},

	displayError : function(opts)
	{
		opts = opts || {};
		var errorObj  = opts.errorObject || new Object();
		var divElem   = $O(opts.errorMessageContainer);
		var overWrite = opts.overWrite || true;
		var customMsg = opts.message;
		var er = 'JSLib custom errorManager >> ' + customMsg + "\n<br/>";
      
		if(!divElem)
		{
			divElem = document.createElement('div');
			$Tag('body')[0].appendChild(divElem);
		}
		
		for(var i in errorObj)
		{
			er += i + ' : ' + errorObj[i];
			if(overWrite)
			{
				divElem.innerHTML = '';
			}
			
			divElem.innerHTML += er + "\n<br/>";
		}
	}
} //end of errorManager