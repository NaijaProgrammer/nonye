//converted to a function Feb 13, 2012
//possible values for theme: 'light', 'dark', 
function setTheme(theme){

   if(theme == 'light')
   {
     theme = 'light';
     themeColor = 'silver';
     bgcolor = 'silver';
     themePath = coreScriptsPath + 'themes/light/';
   }
   else if(theme == 'dark')
   {
     theme = 'dark';
     themeColor = 'black';
     bgcolor = 'black';
     themePath = coreScriptsPath + 'themes/dark/';
   }
   else
   {//default to dark, if no theme or an invalid theme is specified
     theme = 'dark';
     themeColor = 'black';
     bgcolor = 'black';
     themePath = coreScriptsPath + 'themes/dark/';
   }
    
 themeStylePath = themePath + 'style/';
 emoteImgPath = themePath + 'emoticons/';
 statusImgPath = themePath + 'status_images/';
 styleImgPath = themePath + 'style_images/'; 
 textEditImgPath = themePath + 'text_edit_images/';
}


function getTheme(what){
 return ( (what == 'color') ? themeColor : theme );
}