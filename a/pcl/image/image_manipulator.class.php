<?php

class ImageManipulator
{
	public static function image_data_uri($imgfile, $mimetype)
	{
 		$filecontents = file_get_contents($imgfile);
 		$encoding     = base64_encode($filecontents);
 		return ('data:' . $mimetype . ';base64,'. $encoding);

 		//usage: <img src="image_data_uri('logo.png', 'image/png');" alt="imgAlt" />
	}
	
	public static function show_image($options)
	{
		$params = array('url'=>'', 'is_link'=>false, 'linked_page'=>'', 'alt'=>'', 'title'=>'', 'class'=>'', 'id'=>'', 'caption'=>'', 'width'=>'', 
                                  'height'=>'', 
				);

		ArrayManipulator::copy_array($params, $options);

		$image = $params['image'];
		$is_link = $params['is_link'];
		$linked_page = $params['linked_page'];
		$alt = $params['alt'];
		$title = $params['title'];
		$class = $params['class'];
		$id = $params['id'];
		$caption = $params['caption'];
		$width = $params['width'];
		$height = $params['height'];

		if(true == $is_link)
		{
			$linked_image = " <a href=\"$linked_page\"> ";
			$linked_image.= "<img width=\"$width\" height=\"$height\" src=\"$image\" alt=\"$alt\" title=\"$title\" id=\"$id\" class=\"$class\" \>";
			$linked_image.= "</a>";
		}
		else if(false == $is_link)
		{
			$linked_image = "<img width=\"$width\" height=\"$height\" src=\"$image\" alt=\"$alt\" title=\"$title\" id=\"$id\" class=\"$class\" \>";
		}
		if($caption != "")
		{
			$linked_image.= '<br />'. $caption;
		}
		return $linked_image;
	}
}