//utf-8

/**
 * Will run through passed variable 'text' and fix
 * it's regExpression faults.
 *
 * @author Joshua Gross
 * @return fromatted 'text'
 **/
function regExpEscape(text) {
  if (!arguments.callee.sRE) {
    var specials = [
      '/', '.', '*', '+', '?', '|',
      '(', ')', '[', ']', '{', '}', '\\'
    ];
    arguments.callee.sRE = new RegExp(
      '(\\' + specials.join('|\\') + ')', 'g'
    );
  }
  return text.replace(arguments.callee.sRE, '\\$1');
}

/**
* Replaces emotes with images 
* based on the 'emoteReplace' function from im.basic.js in 'Ajax_im' by Joshua Gross;
* modified by michael orji
*
* @arguments
* str - the message to run replaces on
* itemsList - array of emotes
* emotePath - path to the emote icons
**/
strToEmote = function(str, itemsList, emotePath) 
{
   for(var s in itemsList) {
      if(str.indexOf(s) > -1){
       str = str.replace(new RegExp(regExpEscape(s), 'g'), '<img src="' + emotePath + itemsList[s] + '" alt="' + itemsList[s] + '" title="' + s + '" />');
      }
   } 
  return str;
}


/*
* returns the list of emote codes as an object
* @author: michael orji
* @date: march 15, 2011
*/
emoteList = function(){

 return {
 ":smile:" : "smile.png",
 ":angry:" : "angry.png",
 ":cool:" : "cool.png",
 ":cry:" : "cry.png",
 ":embarassed:" : "embarassed.png",
 ":grin:" : "grin.png",
 ":heart:" : "heart.png",
 ":sad:" : "sad.png",
 ":serious:" : "serious.png",
 ":silly:" : "silly.png",
 ":tongue:" : "tongue.png",
 ":wink:" : "wink.png"  
 }

}