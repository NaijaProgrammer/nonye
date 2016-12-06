function activateImageResizer(opts)
{
	if(typeof opts == 'undefined' || typeof opts.imageUrl == 'undefined')
	{
		return false;
	}
	
	var img                      = opts.imageUrl;
	var uniqueIDPrefix           = opts.uniqueIDPrefix;
	var displayCropWindowAsPopup = opts.displayCropWindowAsPopup || false;
	var popupWidth               = opts.popupWidth || 500;
	var popupHeight              = opts.popupHeight || 500;
	var popupType                = 'jquery-ui';
	//var inputContainerId         = opts.inputContainerID || ''; //where to display the image that will have the resize handles for image selection
	var previewContainerId       = opts.previewContainerID || ''; //where to display the image selection preview
	var maxHeight = opts.maxHeight || 150,
		maxWidth  = opts.maxWidth  || 150,			
		minHeight = opts.minHeight || 100,
		minWidth  = opts.minWidth  || 50;

	/*
	* set the src attribute of our dynamic form hidden input field called img_src to the image loaded from our Iframe.
	* This gets passed to the server-side processing script when our dynamic form is submitted
	*/
	jQuery('.img_src').attr('value', img); /////// get image source , this will be passed into PHP
	
	/*
	if(inputContainerId)
	{
		var imageToResizeContainer = $O(inputContainerId);
		imageToResizeContainer.id  = inputContainerId;
		var img_id = imageToResizeContainer.id + '-big';
	}
	
	else
	{/*
		/*
		* dynamically create a large image view, and insert it before our dynamic form
		*/
		var imageToResizeContainer = document.createElement("div");
		imageToResizeContainer.id = "div_upload_big";
		var img_id = imageToResizeContainer.id + '-big';
		$O(uniqueIDPrefix + 'upload_thumb').parentNode.insertBefore( imageToResizeContainer, $O(uniqueIDPrefix + 'upload_thumb') );
	//}
	
	$Html(imageToResizeContainer, '<img id="' + img_id + '" src="' + img + '" />'); //display the image to resize
	jQuery("#" + uniqueIDPrefix + "upload_thumb").show(); //display the dynamic form that gets the values of the resize operation
	//jQuery('.width, .height, .x1, .y1, .x2, .y2').val(''); // we have to remove the values

	if(previewContainerId)
	{
		//display the image in the preview container, and initially hide it, to be displayed when user selects and moves selection around
		$('#' + previewContainerId).hide();
		$Html(previewContainerId, '<img src="' + img + '" />');
	}
	
	if( displayCropWindowAsPopup )
	{  
		//hideImageAreaSelect();
		
		$Style(uniqueIDPrefix + 'popup-closer').display = 'inline';
		EventManager.attachEventListener(uniqueIDPrefix + 'popup-closer', 'click', function(){
			
			if(typeof $( "#" + uniqueIDPrefix + "uploaded" ).dialog( "instance" ) === 'object')
			{
				$( "#" + uniqueIDPrefix + "uploaded" ).dialog( "instance" ).close();
			}
			if( previewContainerId )
			{
				$Style(previewContainerId).display = 'none';
			}
			
			hideImageAreaSelect();
		});
				
		if(popupType == 'jquery-ui')
		{
			if(typeof $( "#" + uniqueIDPrefix + "uploaded" ).dialog( "instance" ) === 'object')
			{
				$( "#" + uniqueIDPrefix + "uploaded" ).dialog( "instance" ).close();
			}
			
			$( "#" + uniqueIDPrefix + "uploaded" ).dialog({ //'uploaded' is the id of the div holding the form with id: 'upload_thumb'
				modal: true,
				maxWidth: popupWidth,
				maxHeight: popupHeight,
				minWidth: 440,
				minHeight: 440,
				show:
				{
					effect: "blind",
					duration: 1000
				},
				hide:
				{
					effect: "explode",
					duration: 1000
				}
			});
		}
		
		else if(popupType == 'magnific')
		{
			$Center(uniqueIDPrefix + "uploaded");
			
			$("#" + uniqueIDPrefix + "popup-trigger").magnificPopup({
				items : {
					src : "#" + uniqueIDPrefix + "uploaded",
					type : 'inline'
				},
				mainClass: 'mfp-fade',
				fixedContentPos : true
				/*
				type: 'image', //'iframe', 'inline', ajax
				removalDelay: 500,
				closeBtnInside: true,
				dispableOn: 700,
				mainClass: 'mfp-fade',
				preloader: false
				callbacks: {
					beforeOpen: function() {
						this.st.mainClass = this.st.el.attr('data-effect');
					}
				},
				midClick: true
				*/
			});
			
			triggerEvent(uniqueIDPrefix + "popup-trigger", 'click');
			
			function triggerEvent(element, eventType)
			{
				element = $O(element);
				
				var event; // The custom event that will be created

				if (document.createEvent)
				{
					event = document.createEvent("HTMLEvents");
					event.initEvent(eventType, true, true);
				} 
				else
				{
					event = document.createEventObject();
					event.eventType = eventType;
				}

				event.eventName = eventType;

				if (document.createEvent)
				{
					element.dispatchEvent(event);
				} 
				else
				{
					element.fireEvent("on" + event.eventType, event);
				}
			}
		}
	}
	
	//area select plugin http://odyniec.net/projects/imgareaselect/examples.html 
	$('#' + img_id).imgAreaSelect({
		//aspectRatio: '1:1', 
		handles        : true,
		fadeSpeed      : 200,
		resizeable     : false,
		maxHeight      : maxHeight,
		maxWidth       : maxWidth,			
		minHeight      : minHeight, 
		minWidth       : minWidth,	
		x1             : 15,
		y1             : 15,
		x2             : maxWidth + 15,
		y2             : maxHeight + 15,
		//show           : true,
		
		//this works in conjunction with the x1,y1 and x2, y2 values which by specifying, 
		// we auto-select some area of the resizing subject the first time it appears.
		// Without explicitly specifying these values, the user has to drag over the image before the resize selection area and handles appear.
		// By also setting onInit to the preview callback, we ensure that the initial selection area [x1, y1, x2, y2] are captured
		// and ready to be submitted, even if the user never does any dragging [onSelectChange] over the image themselves.
		onInit  : preview, 
		
		//called only when user manually changes the selection area themselves
		onSelectChange : preview
	});
			
	function preview(img, selection) 
	{
		if (!selection.width || !selection.height)
		{
			return;
		}
					
		if( previewContainerId )
		{
			$('#' + previewContainerId).show();
			
			//200 is the #preview dimension, change this to your liking
			//var scaleX = 200 / selection.width; 
			//var scaleY = 200 / selection.height;
			
			var scaleX = maxWidth / selection.width; 
			var scaleY = maxHeight / selection.height;
			
			jQuery('#' + previewContainerId).css({
				width: maxWidth + 'px',
				height: maxHeight + 'px',
				overflow: 'hidden'
			});
					
			jQuery('#' + previewContainerId + ' img').css({
				width: Math.round(scaleX * jQuery('#' + img_id).attr('width')),
				height: Math.round(scaleY * jQuery('#' + img_id).attr('height')),
				marginLeft: -Math.round(scaleX * selection.x1),
				marginTop: -Math.round(scaleY * selection.y1)
			});
		}
				
		jQuery('.x1').val(selection.x1);
		jQuery('.y1').val(selection.y1);
		jQuery('.x2').val(selection.x2);
		jQuery('.y2').val(selection.y2);
		jQuery('.width').val(selection.width);
		jQuery('.height').val(selection.height); 
	}
}

function hideImageAreaSelect()
		{   
			//credits: http://stackoverflow.com/questions/3709633/jquery-imgareaselect-hide-show
			$('div.imgareaselect-selection').parent().hide();
			$('div.imgareaselect-selection').hide();
			$('div.imgareaselect-border1').hide();
			$('div.imgareaselect-border2').hide();
			$('div.imgareaselect-border3').hide();
			$('div.imgareaselect-border4').hide();
			$('div.imgareaselect-handle').hide();
			$('div.imgareaselect-outer').hide();
									
			/* This is currently having issues, but it is preferable
			//credits: http://stackoverflow.com/a/3709701/1743192
			var ias = $('#' + img_id).imgAreaSelect({ instance: true });
			ias.setOptions({ hide: true });
			ias.update();
			*/
		}