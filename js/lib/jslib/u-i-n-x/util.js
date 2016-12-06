function emptyFieldValue(fieldId, testValue)
{
	testValue    = testValue.toLowerCase();
	var fieldVal = $O(fieldId).value.toLowerCase();
 
	if(fieldVal == testValue){
		$Style(fieldId).color = '#000000';
		$O(fieldId).value = '';
	}
}
function replaceFieldValue(fieldId, value)
{
   if(isEmpty(trim($O(fieldId).value)))
   {
		$Style(fieldId).color = '#aaaaaa';
		$O(fieldId).value = value;
   }
}