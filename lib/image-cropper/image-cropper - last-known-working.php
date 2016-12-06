<?php
require dirname(__FILE__). '/lib/wideimage/WideImage.php';
class ImageCropper
{
	/* 
	* $opts data members:
	* plugin_url string url path to the image cropper plugin
	* unique_id_prefix string
	* action_page string
	* include_thumb_scale_details boolean
	* crop_button_value string
	* crop_processing_callback string name of js function to call when the 'submit button' is clicked and the cropping processing starts. 
	* The Function is auto-passed the ID of the submit button.
	* crop_success_callback string name of js function to call when the crop processing returns successfully.
	* The function is auto-receives the server response in JSON format and the ID of the submit button
	*/
	public static function get_image_cropper($opts = array())
	{
		extract($opts);
		defined("NL") or define("NL", "\r\n");
		
		$rel_url = $plugin_url;
		$url_contains_query_string = parse_url($action_page, PHP_URL_QUERY);
		$qs_prefix   = $url_contains_query_string ? '&' : '?';
		
		$html = ''.
		'<link rel="stylesheet" href="'. $rel_url. '/css/imgareaselect-default.css" />'. NL.
		'<link rel="stylesheet" href="'. $rel_url. '/css/custom-styles.css" />'. NL.
		
		'<script type="text/javascript" src="'. $rel_url. '/js/JSLib.js"></script>'.  NL.		
		'<script src="'. $rel_url. '/js/jquery.min.js"></script>'.  NL.
		'<script src="'. $rel_url. '/js/jquery.imgareaselect.min.js"></script>'.  NL;
				
		$html .= '<!-- required if you want to display the crop window in a jquery-ui popup dialog -->'.  NL.
		'<link rel="stylesheet" href="'. $rel_url. '/js/jquery-ui/jquery-ui-1.11.2.css" />'.  NL.
		'<script src="'. $rel_url. '/js/jquery-ui/jquery-ui-1.11.2.js"></script>'.  NL;
		
		$html .= '<!-- required if you want to display the crop window in a magnific popup dialog -->'.  NL.
		'<link rel="stylesheet" href="'. $rel_url. '/js/magnific-popup/magnific.css" />'.  NL.
		'<script src="'. $rel_url. '/js/magnific-popup/magnific.js"></script>'. NL;
		
			
		$html .= '<script src="'. $rel_url. '/js/custom-cropper.js"></script>'.  NL;
		
		$html .= ''.
		'<div id="'. $unique_id_prefix. 'uploaded">'.
		 '<span id="'. $unique_id_prefix. 'popup-closer" style="display:none; float:right; cursor:pointer; border:1px solid #eee; padding:3px; margin-bottom:5px;" title="close">x</span>'.
		 '<form style="display:none;" name="upload_thumb" id="'. $unique_id_prefix. 'upload_thumb" method="post" action="'. $action_page. $qs_prefix. 'act=create-thumb">'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'img_src" name="img_src" class="img_src" />'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'height"  name="height" class="height" value="0" />'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'width" name="width" class="width" value="0" />'.  
		  '<input type="hidden" id="'. $unique_id_prefix. 'y1" class="y1" name="y" />'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'x1" class="x1" name="x" />'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'y2" class="y2" name="y1" />'.
		  '<input type="hidden" id="'. $unique_id_prefix. 'x2" class="x2" name="x1" />'.                        
		  '<input type="submit" id="'. $unique_id_prefix. 'submit-button" class="cropped-image-save-button" value="'. $crop_button_value. '" />'.
		 '</form>'. 
		 '<!-- this button just put here to trigger magnific popup -->'.
		 '<button id="'. $unique_id_prefix. 'popup-trigger" style="display:none;">triggerPopup</button>'.
		'</div>';
			 
		if($include_thumb_scale_details)
		{
			$html .= ''.
			'<div id="'. $unique_id_prefix. 'thumbnail">'.
			 '<div id="'. $unique_id_prefix. 'details">'.
			  '<table width="200">'.
			   '<tr>'.
			    '<td colspan="2">Image Source<br />'.
				 '<input type="text" name="img_src" class="img_src" size="35" />'.
				'</td>'.
			   '</tr>'.
			   '<tr>'.
				'<td>Height<br /><input type="text" name="height" class="height" size="5" /></td>'.
				'<td>Width<br /><input type="text" name="width" class="width" size="5"/></td>'.
			   '</tr>'.
			   '<tr>'.
				'<td>Y1<br /><input type="text" class="y1"  size="5"/></td>'.
				'<td>X1<br /><input type="text" class="x1" size="5" /></td>'.
			   '</tr>'.
			   '<tr>'.
				'<td>Y2<br /><input type="text" class="y2" size="5" /></td>'.
				'<td>X2<br /><input type="text" class="x2" size="5" /></td>'.
			   '</tr>'.
			  '</table>'.
			 '</div>'.
			'</div>';
		}
		
		$html .= ''.
		'<script>'.
		/* 'EventManager.attachEventListener("'. $unique_id_prefix. 'popup-closer", "click", function(){'. NL.
		   'hideImageCropperPopupDialog();'. NL.
		   'hideImageAreaSelect();'. NL.
		 '})'. NL.*/
		 
		 'EventManager.attachEventListener("'. $unique_id_prefix. 'submit-button", "click", function(e){'.
		  'EventManager.cancelDefaultAction(e);'.  NL.
		  $crop_processing_callback. '("'. $unique_id_prefix. 'submit-button");'. NL.
		  'var requestData = "operation=create_image_thumbnail"     +'.
		  '"&img_src=" + $O("'. $unique_id_prefix. 'img_src").value +'.
		  '"&width="   + $O("'. $unique_id_prefix. 'width").value   +'.
		  '"&height="  + $O("'. $unique_id_prefix. 'height").value  +'.
		  '"&x="       + $O("'. $unique_id_prefix. 'x1").value      +'.
		  '"&y="       + $O("'. $unique_id_prefix. 'y1").value'.  NL.
		  
		  'new XHR({'.
			'type               : "POST",'.
			'url                : "'. $action_page. '",'.
			'async              : true,'.
			'requestData        : requestData,'.
			'debugCallback      : function(reply){console.log(reply);},'.
			'readyStateCallback : function(){},'.
			'errorCallback      : function(){},'.
			'successCallback    : function(reply)'.NL.
			'{'. NL.
			  'var response = reply.parsedValue;'. NL.
			  'if(typeof '. $crop_success_callback. ' == "function"){'. NL.
			    $crop_success_callback. '(response, "'. $unique_id_prefix. 'submit-button");'. NL.
				'hideImageCropperPopupDialog();'. NL.
				'hideImageAreaSelect();'. NL.
				'jQuery("#'. $unique_id_prefix. 'uploaded").hide()'. NL.
			  '}'. NL.
			'}'. NL.
		  '});'.  NL.
		'});'. 
		
		'function hideImageCropperPopupDialog()'. NL.
		'{'. NL.
		    'if(typeof jQuery( "#'.  $unique_id_prefix. 'uploaded" ).dialog( "instance" ) === "object")'. NL.
			    '{'. NL.
				    'jQuery( "#'. $unique_id_prefix. 'uploaded" ).dialog( "instance" ).close();'. NL.
			'}'. NL.
		'}'.
		'</script>';
		
		return $html;
	}

	/*
	* $opts data members:
	* thumbnail_directory, 
	* thumbnail_name
	* save_original_image boolean
	*/
	public static function create_image_thumbnail($opts)
	{
		extract($opts);
		
		$op_data = (array(
			'source_image'           => $_POST['img_src'],
			'destination_directory'  => $thumbnail_directory,
			'destination_image_name' => $thumbnail_name,
			'width'                  => $_POST['width'],
			'height'                 => $_POST['height'],
			'x'                      => $_POST['x'], 
			'y'                      => $_POST['y'],
			'save_source_image'      => $save_original_image
		));
		
		self::crop_image($op_data);
	}
	
	/*
	* $arr data members:
	* source_image [could be File name, url, HTML file input field name, binary string, or a GD image resource]
	* destination_image_name
	* destination_directory string
	* width int
	* height int
	* save_source_image boolean
	*/
	public static function resize_image($arr)
	{
		extract($arr);
		
		//use short-circuiting to check if directory exists, and possibly make a new directory if it doesnt
		is_dir($destination_directory) || mkdir($destination_directory, 0777, $recursive = true);
		
		$destination_image = rtrim($destination_directory, '/'). '/'. $destination_image_name;
			
		WideImage::load($source_image)->resize($width, $height, $fit = 'inside', $scale = 'any')->saveToFile($destination_image);
		
		if( !$arr['save_source_image'] )
		{
			if($source_image != $destination_image)
			{
				unlink($source_image); 
			}
		}
	}
	
	/*
	* $opts data members:
	* source_image [could be File name, url, HTML file input field name, binary string, or a GD image resource]
	* destination_directory
	* destination_image_name
	* width int
	* height int
	* x
	* y
	* save_source_image boolean
	*
	*/
	public static function crop_image($opts=array())
	{
		extract($opts);
		
		//use short-circuiting to check if directory exists, and possibly make a new directory if it doesnt
		is_dir($destination_directory) || mkdir($destination_directory, 0777, $recursive = true);
		
		$destination_image = rtrim($destination_directory, '/'). '/'. $destination_image_name;
	
		WideImage::load($source_image)->crop($x, $y, $width, $height)->saveToFile($destination_image);
		
		if( !$save_source_image )
		{
			if($source_image != $destination_image)
			{
				unlink($source_image); 
			}
		}
	}

	private static function create_json_string($data=array(), $output=false)
	{
		$str = '{';
			
		foreach($data AS $key => $value)
		{
				$value = ( is_string($value) ? ('"'. $value. '"') : $value );
				$str .= '"'. $key. '":'. $value. ', ';
		}
		$str  = substr($str, 0, -2); //remove trailing space and comma (, )
		$str .= '}';
			
		if($output)
		{
			header("Content-Type: application/json");
			echo $str;
		}
			
		return $str;
	}
}	