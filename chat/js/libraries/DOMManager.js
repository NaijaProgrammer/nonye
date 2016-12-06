var DOMManager = {

   getElementsByClassName : function(cssClassName, strict){
    var arr = [];
    var allElements = document.getElementsByTagName('*');
    var len = allElements.length;
      for(var i = 0; i < len; i++){
         if(strict){
            if(allElements[i].className == cssClassName){
             arr.push(allElements[i]);
            }
         }
         else{
            if( (allElements[i].className.indexOf(cssClassName) != -1) || (allElements[i].className == cssClassName) ){
             arr.push(allElements[i]);
            }
         }
      }
    return arr;
   }, //end getElementsByClassName

   //getElementsOfClass : this.getElementsByClassName, 
   getElementsOfClass : function(cssClassName, strict){
  
    var arr = [];
    var allElements = document.getElementsByTagName('*');
    var len = allElements.length;
      for(var i = 0; i < len; i++){
         if(strict){
            if(allElements[i].className == cssClassName){
             arr.push(allElements[i]);
            }
         }
         else{
            if( (allElements[i].className.indexOf(cssClassName) != -1) ){
             arr.push(allElements[i]);
            }
         }
      }
    return arr;
   }, //end getElementsOfClass

   /*
   * @author: michael orji
   * @date: 25 oct, 2010 16:41:26
   */
   removeFromParentNode : function(nodeIDToRemove){
     var nodeToRemove = detectObjectness(nodeIDToRemove); 
      if( (typeof nodeToRemove != 'undefined') ){
       var PN = nodeToRemove.parentNode || document.body;
       PN.removeChild(nodeToRemove);
       return true;
      }
   }, 

   /*
   * @author: michael orji
   * @date: 25 April, 2012
   */
   removeElementsFromParentNode : function(classOfElementsToRemove){
     var nodesToRemove = this.getElementsOfClass(classOfElementsToRemove);
     var nodesLen = nodesToRemove.length;
      for(var i = 0; i < nodesLen; i++){
       var nodeToRemove = nodesToRemove[i]; 
         if( (typeof nodeToRemove != 'undefined') ){
          nodeToRemove.parentNode.removeChild(nodeToRemove);
          //this.removeFromParentNode(nodeToRemove);
         }
      }
   }, 

   /*
   * @author: michael orji
   * @date: 25 oct, 2010 16:41:26
   */
   removeFromArrayAndParentNode : function(idOfArrayElementToRemove, nodeIDToRemove, arr){
      if(this.removeFromParentNode(nodeIDToRemove)){
       removeFromArray(idOfArrayElementToRemove, arr);
      }
   }, 


   /*
   * traverses a parent node looking for a child node
   * identified by its css style name and value;
   * returns: true if the child node is found, else false
   *
   * @date: 05, sept, 2010
   *
   * CAN STILL DO WITH SOME IMPROVEMENT
   */
   findNode : function(parentNode, targetNodeStyleName, targetNodeStyleValue){
    var children = parentNode['childNodes'];
      for(var i in children){
         if(children[i][targetNodeStyleName] == targetNodeStyleValue){
          return children[i]; 
         }
      }
    return false;
   },

   /*
   * @author: michael orji
   * @date: 8 Nov, 2010 18:45
   */

   destroyElement : function(elem)
   {
    var elem = detectObjectness(elem);
      if(typeof elem.parentNode !== 'undefined'){
       this.removeFromParentNode(elem);
      }
      else{
       StyleManager.hideElement(elem);
       document.body.removeChild(elem);
      }
   }
}