function initTextEditObjects(textBox){
 
 textBox = detectObjectness(textBox); 

   setFont = function(fontName){
    textBox.style.fontFamily = fontName;
   }
   setFontSize = function(size){
    textBox.style.fontSize = size + "px";
   }
   setFontColour = function(colour){
    colour = detectObjectness(colour);
    textBox.style.color = colour.style.backgroundColor;
   }
   setEmoticon = function(emoteString){
    textBox.value += emoteString;
   }
   setFontWeight = function(b){

   b = detectObjectness(b);
   var bold = b.getAttribute('src');
   var boldIndex = bold.indexOf("bold");

   bold = bold.substring(boldIndex, bold.length);

      if(bold == "bold_off_hover.png" || bold == "bold_off.png"){
       textBox.style.fontWeight = "bold";
      }
      else if(bold == "bold_on_hover.png" || bold == "bold_on.png"){  
       textBox.style.fontWeight = "normal";
      }
   }
   setFontStyle = function(i){

    i = detectObjectness(i);
    var italic = i.getAttribute('src');
    var italicIndex = italic.indexOf("italic");

    italic = italic.substring(italicIndex, italic.length);

      if(italic == "italic_off_hover.png" || italic == "italic_off.png"){
       textBox.style.fontStyle = "italic";
      }
      else if(italic == "italic_on_hover.png" || italic == "italic_on.png"){  
       textBox.style.fontStyle = "normal";
      }
   }
   setTextDecoration = function(u){

    u = detectObjectness(u);
    var underline = u.getAttribute('src');
    var underlineIndex = underline.indexOf("underline");

    underline = underline.substring(underlineIndex, underline.length);

      if(underline == "underline_off_hover.png" || underline == "underline_off.png"){
       textBox.style.textDecoration = "underline";
      }
      else if(underline == "underline_on_hover.png" || underline == "underline_on.png"){  
       textBox.style.textDecoration = "none";
      }
   }
}