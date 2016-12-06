<?php 
//credits: http://stackoverflow.com/a/5607444/1743192
//cf. also: http://stackoverflow.com/a/1809974/1743192, http://stackoverflow.com/a/1091399/1743192, http://stackoverflow.com/a/19075291/1743192
//cf. also: //http://stackoverflow.com/a/2934271/1743192 on mysql timezones
//include before closing '</body>' tag

$user_timezone_offset = '';

if(isset($_GET['user_timezone_offset']))
{
	$_SESSION['user_timezone_offset'] = $_GET['user_timezone_offset'];
}
if( isset($_SESSION['user_timezone_offset']) )
{
	$user_timezone_offset =  $_SESSION['user_timezone_offset'];
	date_default_timezone_set( convert_offset_to_timezone($user_timezone_offset) );
}
?>
<script>
$(document).ready(function(){
    if("<?php echo $user_timezone_offset; ?>".length==0)
	{
        var visitortime     = new Date();
		var visitortimezone = -visitortime.getTimezoneOffset()/60; //"GMT " + -visitortime.getTimezoneOffset()/60;
        $.ajax({
            type    : "GET",
            url     : "",
            data    : "user_timezone_offset=" + visitortimezone,
            success : function(){ location.reload(); }
        });
    }
});
</script>