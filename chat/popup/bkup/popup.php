<html>
<head>
<title><?php echo($_GET['title']);?></title>
<!--<script type="text/javascript" src="../chat_loader.php"></script>-->
<script type="text/javascript" src="popup_chat_loader.php"></script>
<script type="text/javascript" src="popup_loader.js"></script>
<script type="text/javascript" src="popupwingetter.js"></script>
<script type="text/javascript">
 
 //window.onload = function(){document.body.innerHTML = (self.opener.winToLoad.getContent());}
</script>
<?php
 $windowId = $_GET['windowId'];
 $currWindow = $_GET['window'];
 
 $fnx = "setTimeout(function(){
               getPopUpChatWindow('". $windowId. "');
               self.opener.detachedWindowsIds.push('". $windowId. "');
               self.opener.winToLoad[". $currWindow. "].hide('" . $windowId. "', useStackView, 'IMWin'); //hide the window in the parent browser
               self.opener.focusNextWindowOnDetach('". $windowId. "');
              }, 1000)";
 /*
 $fnx = "setTimeout(function(){
               getPopUpChatWindow('". $windowId. "');
               self.opener.detachedWindowsIds.push('". $windowId. "');
               self.opener.winToLoad[". $currWindow. "].hide('" . $windowId. "', useStackView, 'IMWin'); //hide the window in the parent browser
               self.opener.focusNextWindowOnDetach('". $windowId. "');
              }, 1000)";
*/

 $before_unload_fnx = "window.onbeforeunload = function() {
                         self.opener.winToLoad[". $currWindow. "].setDetached(self.opener.winToLoad[". $currWindow. "], false);
                         self.opener.winToLoad[". $currWindow. "].focus('" . $windowId. "', useStackView, 'IMWin');
                         removeFromArray('". $windowId. "', self.opener.detachedWindowsIds); //remove the window from the array of detached windows in the parent browser
                        }";

 echo("<script type='text/javascript'>". $fnx. "\n" . $before_unload_fnx. "</script>");
?>
</head>
<body>
</body>
</html>