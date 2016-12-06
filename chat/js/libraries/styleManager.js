
/*
* @author: michael orji
* @date: 28 Feb, 2012
*/
var StyleManager = {   

   setStyle : function(elem, styleOptions){
     elem = detectObjectness(elem);
     styleOptions = styleOptions || {}; 
      for(var j in styleOptions){
        elem.style[j] = styleOptions[j];
       }
   }, //end setStyle

   styleElementsOfClass : function(cssClassName, styleObject){
    styleObject = styleObject || {};
    var ccn = DOMManager.getElementsOfClass(cssClassName);
      for(var x in ccn){
       this.setStyle(ccn[x], styleObject);
      }
   }, //end styleElementsOfClass

   hideElement : function(elemId, useDisplay){
    elem = detectObjectness(elemId);

      if(elem){
         if(useDisplay){
          elem.style.display = 'none';
         }
         else{
          elem.style.visibility = 'hidden';
         }
      }
   }, //end hideElement

   showElement : function(elem, elemStyle){
    
    elem = detectObjectness(elem);
      if(elemStyle == 'display'){
       elem.style.display = 'block';
      }
      else{
       elem.style.visibility = 'visible';
      }
   }, //end showElement


   hideElements : function(elemsCollection, useDisplay){
      for(var i in elemsCollection){
       this.hideElement(elemsCollection[i], useDisplay);
      }  
   }, //end hideElements

   showElements : function(elems, elemStyle){
      for(var i = 0; i < elems.length; i++){
       this.showElement(elems[i], elemStyle);
      }  
   }, //end showElements

   showElementsOfClass : function(cssClass, displayStyle){
    var ElementsToShow = DOMManager.getElementsOfClass(cssClass);
    var len = ElementsToShow.length;
      for(var i = 0; i < len; i++){
       this.showElement(ElementsToShow[i], displayStyle);
      }
   },//end showElementsOfClass

   hideElementsOfClass : function(htmlTag, cssClass){
    var candidateElements = document.getElementsByTagName(htmlTag);
    var len = candidateElements.length;
      for(var i = 0; i < len; i++){
         if(candidateElements[i].className == cssClass){
          this.hideElement(candidateElements[i]);
         }
      }
   }, //end hideElementsOfClass

   hideElementsOfClass : function(cssClass, useDisplay){
    var ElementsToHide = DOMManager.getElementsOfClass(cssClass);
    var len = ElementsToHide.length;
      for(var i = 0; i < len; i++){
       this.hideElement(ElementsToHide[i], useDisplay);
      }
   },//end hideElementsOfClass

   setElementOpacity : function(elemId, opacityLevel)
   {
    var elem = document.getElementById(elemId);
      if(opacityLevel > .99) opacityLevel = .99;
      if(opacityLevel < 0) opacityLevel = 0;

      if(typeof elem.style.opacity != 'undefined') elem.style.opacity = opacityLevel;
      else if(typeof elem.style.MozOpacity != 'undefined') elem.style.MozOpacity = opacityLevel;
      else if(typeof elem.style.KhtmlOpacity != 'undefined') elem.style.KhtmlOpacity = opacityLevel;
      else elem.style.filter = "alpha(opacity=" + opacityLevel * 100 + ")";
   }//end setElementOpacity
}