/* 
* @author: Michael Orji
* @date: March 25, 2012
*/
var errorManager = {

   displayError : function(errObj, divElem, overWrite){
    divElem = detectObjectness(divElem);
     var er;
      for(var i in errObj){
       er = i + ' : ' + errObj[i];
       if(overWrite){
        divElem.innerHTML = '';
       }
       divElem.innerHTML += er + "\n";
      }
   }
}