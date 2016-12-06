/*
* @author: michael orji
*/
function inArray(needle, haystack, strict)
{ 
	var len = haystack.length;
	//for(var i = 0; i < len; i++)
	for(var i in haystack)
	{
   
		if(strict)
		{
			if(haystack[i] === needle)
			{
				return true;
			}
		}
		else
		{
			if(haystack[i] == needle)
			{
				return true;
			}
		}
	}
	
	return false;
}

/*
* @author: michael orji
* @date: 25 oct, 2010 16:41:26
*/
function removeFromArray(idOfArrayElementToRemove, arr)
{
	var arrayLength = arr.length;
	for(var i = 0; i < arrayLength; i++)
	{
		if((arr[i] == idOfArrayElementToRemove) || (arr[i]['id'] == idOfArrayElementToRemove) || (arr[i].id == idOfArrayElementToRemove))
		{
			arr.splice(i,1);
			return;
		}
	}
}


//@date: 29 April, 2012
function getRandomArrayElement(arr, elementToExclude)
{
	var numOfElems = arr.length;
	var randomizer = Math.floor(numOfElems * Math.random());

	if(isEmpty(arr))
	{
		return null;
	}

	if(arr[randomizer] && (arr[randomizer] != elementToExclude) )
	{
		return arr[randomizer];
	} 
	else
	{
		getRandomArrayElement(arr, elementToExclude);
	} 
}

//returns the last element in an array, without removing that element from the array
//@date: 29 Apr, 2012
function getLastElementInArray(arr)
{
    return isEmpty(arr) ? null : arr[arr.length-1];
}