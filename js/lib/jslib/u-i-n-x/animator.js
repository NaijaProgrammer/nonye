/*
* @credits: Accelerated DOM Scripting with Ajax, APIs and Libraries
* @modified: Michael Orji
* @date: Oct. 6, 2012
* @time: 23:47
*/
function Animator(options){
 
 var el = $O(options.element);
 if(!el) { return false; }

 var framerate     = 25;//frames per second
 var currentStep   = 0; 
 var totalNumSteps = options.duration / 1000 * framerate; // determines the total number of steps
 var interval      = (options.from - options.to) / totalNumSteps; // determines the interval between each step
 var intervalId;

   function animate(){

    var newval = options.from - (currentStep * interval); // what the new position will be

      // check if the property exists and if the currentStep is 0 (the first step)
      if(options.onStart && currentStep == 0){
       options.onStart();
      }
      if(options.onStep){
       options.onStep();
      }
      if(currentStep++ < totalNumSteps) {
       el.style[options.property] = options.animateStep(newval); //defined in sub Classes below 
      }
      else{
       el.style[options.property] = options.to + options.stepUnit; //declared in sub classes 
       publicMethods.stop();
         if(options.onEnd){
          options.onEnd();
         }
      }
   }
 
   var publicMethods = {
      start:function(){
       intervalId = setInterval(animate, 1000 / framerate);
      },
      stop:function(){
       clearInterval(intervalId);
      },
      gotoStart:function(){
       currentStep = 0;
       el.style[options.property] = options.from + options.stepUnit;
      },
      gotoEnd:function(){
       currentStep = totalNumSteps;
       el.style[options.property] = options.to + options.stepUnit;
      }
   }
 return publicMethods
}


/*
* Animates Dimensions / size: width, height
* @author : Michael orji
*/
function DimensionAnimator(options){

 var opts = { 'property'    : 'height' }

 for(var i in options){ opts[i] = options[i]; }

 opts.stepUnit    = 'px';
 opts.animateStep = function(stepValue){ return Math.ceil(stepValue) + 'px'}
 
   if(opts.property.toLowerCase() != 'width' && opts.property.toLowerCase() != 'height'){
    return {'error' : {"name" : "DimensionAnimationError", "message": "Invalid property supplied \nproperty must be either \"width\" or \"height\" " } };
   }
 return new Animator(opts);
}


/*
* Animates Positions: left, top
* @author : Michael orji
*/
function PositionAnimator(options){

 var opts = { 'property'    : 'left' }

 for(var i in options){ opts[i] = options[i]; }

 opts.stepUnit    = 'px';
 opts.animateStep = function(stepValue){ return Math.ceil(stepValue) + 'px'}
 
   if(opts.property.toLowerCase() != 'left' && opts.property.toLowerCase() != 'top'){
    return {'error' : {"name" : "DimensionAnimationError", "message": "Invalid property supplied \nproperty must be either \"left\" or \"top\" " } };
   }
 return new Animator(opts);

}

/*
* Animates Appearance: Base class of OpacityAnimator and ColourAnimator
* @author : Michael orji
*/
function AppearanceAnimator(options){
 var opts = {}
 for(var i in options){ opts[i] = options[i]; }
 return new Animator(opts);
}

/*
* Animates opacity
* @author : Michael orji
*/
function OpacityAnimator(options){

 var opts = {}
 for(var i in options){ opts[i] = options[i]; }

 opts.stepUnit = '';
 opts.animateStep = function(stepValue){};

 return new Animator(opts);

}

/*
* Animates colour
* @author : Michael orji
*/
function ColourAnimator(options){

 var opts = {}
 for(var i in options){ opts[i] = options[i]; }

 opts.stepUnit = '';
 opts.animateStep = function(stepValue){};

 return new Animator(opts);

}