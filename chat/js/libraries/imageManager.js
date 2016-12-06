/*
* @author: michael orji
* @date: dec 26, 2011
*/
function HTMLImage(obj){

 obj = obj ||{}

 img = obj.imageToShow;
 width = obj.imageWidth;
 height = obj.imageHeight;
 alt = obj.imageAlt;
 title = obj.imageTitle;

 return '<img src=' + img + ' width=' + width + 'height=' + height + ' alt=' + alt + ' title=' + title + '  />';

}