/*
* @author: Michael Orji
*
*Dependencies: Animator
*/
function slideUp(element, opts){

 element   = elementOptions(element); //retrieve the 'displayType', 'visibilityValue' and 'invisibilityValue' of the element
 opts      = opts || {};
 var start = element.constantHeight || size(element).height;
 var stop  = 0; 
 element.sliding = true;
 
 var options = {
  'element'  : element,
  'from'     : start,
  'to'       : stop,
  'duration' : opts.duration || 500, 
  'onStart'  : function(){ 
		 if(typeof opts.onSlideUpStart == 'function'){ opts.onSlideUpStart(); }
	       }, 
  'onStep'   : function(){ 
		 if(typeof opts.onSlideUpStep == 'function'){ opts.onSlideUpStep(); }
	       }, 
  'onEnd'    : function(){ 
                 $Style(element)[element.displayType] = element.invisibilityValue ;
                 $Style(element).height   = start + 'px'; //since we have hidden the element above, re-setting its height to its original state is ok, as it wont show up
                 element.consistentHeight = start; //without this, on every slide up / slide down, 12px is added to the element height (reasons unknown)
                 element.nextFunction = slideDown; //use this to set the element.nextCall property
                 element.sliding    = false;
                 if(typeof opts.onSlideUpEnd == 'function'){ opts.onSlideUpEnd(); }
               }
 };

  var slider = new DimensionAnimator(options);

   if(slider.error){
     logToConsole(slider.error.name + ' : ' + slider.error.message);
     return;
   }

 slider.start();

}

function slideDown(element, opts){

 element   = elementOptions(element);
 opts      = opts || {};
 $Style(element)[element.displayType] = element.visibilityValue; 
 var start = 0;
 var stop  = element.constantHeight || size(element).height; 
 $Style(element).height = '0'; //set the height to 0 (zero) to prevent the flicker caused by the element showing up, then sliding down
 element.sliding = true;
 
 var options = {
  'element'  : element,
  'from'     : start,
  'to'       : stop,
  'duration' : opts.duration || 500,
  'onStart'  : function(){ 
		 if(typeof opts.onSlideDownStart == 'function'){ opts.onSlideDownStart(); }
	       }, 
  'onStep'   : function(){ 
		 if(typeof opts.onSlideDownStep == 'function'){ opts.onSlideDownStep(); }
	       },
  'onEnd'    : function(){ 
                element.nextFunction     = slideUp;
                element.consistentHeight = stop;
                element.sliding    = false;
                $Style(element).overflow = 'visible';
                if(typeof opts.onSlideDownEnd == 'function'){ opts.onSlideDownEnd();}
              }
 };

 var slider = new DimensionAnimator(options);

   if(slider.error){
     logToConsole(slider.error.name + ' : ' + slider.error.message);
     return;
    }
 slider.start();
}

function slideToggle(element, options){

   element = elementOptions(element); //retrieve the 'nextCall' and 'isHidden' properties of the element

   if(element.isSliding){
    return;
   }

   if(element.nextCall){ 
    element.nextCall(element, options);
   }
   else{
      if(element.isHidden){
       slideDown(element, options);
      }
      else{
       slideUp(element, options);
      } 
   } 
}

function elementOptions(element){
 element = $properties(element);
 element.nextCall = element.nextFunction;
 element.constantHeight = element.consistentHeight;
 element.isSliding      = element.sliding;
 $Style(element).overflow = 'hidden';
 return element;
}