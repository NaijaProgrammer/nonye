/*
* An object for managing window, browser, keyboard, etc events
* @author: Michael Orji
* @date: 25 Feb, 2012
*/
var EventManager = 
{
   getEventObject : function(event){
    //event = (typeof event !== "undefined") ? event : window.event ; 
    //event = event || window.event;
    return ( (event) ? event : window.event);
   },
  
   eventTarget : function(event){
    event = this.getEventObject(event);
    return ( (event.target) ? event.target : event.srcElement); 
   },
   
   /*
   * returns a boolean value indicating
   * whether or not the passed element (elem)
   * is the target of the event
   * @access: public
   * @params: String indicating the target element to test for
   */
   targetElementTypeIs : function(elem, event){
    var target = this.eventTarget(event);
    return target.tagName.toLowerCase() == elem.toLowerCase();
   },   //e.g Usage EventManager.isTargetElementType('a'); EventManager.isTargetElementType('div');

   targetIsDocument : function(event){
    return this.targetElementTypeIs('document', event) || this.targetElementTypeIs('body', event) || this.targetElementTypeIs('html', event);
   }
}//close the WindowEvents object

var EM = EventManager; //provides a short (concise) way to reference the EventManager object