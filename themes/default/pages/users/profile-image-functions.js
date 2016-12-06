/* These are for the image cropping function (after successful upload)*/
function updateUserImagesUrl(imgUrl)
{
	$O('user-photo').src = imgUrl;
}
function cropProcessingCallback(submitBtnID)
{
	Site.Util.addClassTo(submitBtnID, 'btn btn-primary bg-right bg-no-repeat pr25 pl25');
	disable(submitBtnID);
	setAsProcessing(submitBtnID);
}
function cropSuccessCallback(response, submitBtnID)
{
	response = Site.Util.parseAjaxResponse(response);
	updateUserImagesUrl(response.thumbnailUrl);
	//setMainNavUserPixUrl(response.miniImageUrl);
	$Style('image-crop-preview').display = 'none';
	unsetCropPreviewAreaToRounded();
	unsetAsProcessing('profile-pix-processing');
	unsetAsProcessing(submitBtnID);
	Site.Util.removeClassFrom(submitBtnID, 'btn btn-primary bg-right bg-no-repeat pr25 pl25');
	enable(submitBtnID);
}

//used only for current site/theme, to add the class that makes the profile image rounded
//this has the effect of making the preview area as also rounded
function setCropPreviewAreaToRounded()
{
	$('#image-crop-preview').addClass('user-image');
}
function unsetCropPreviewAreaToRounded()
{
	$('#image-crop-preview').removeClass('user-image');
}

/* ---------------------------These are for the main upload function */

function activateUploaderOn(elem, opts)
{
	/*
	* because the Dropzone constructor function uses document.querySelector internally to get the dropzone element, 
	* you have to pass in div#my-dropzone, rather than my-dropzone
	* otherwise, you will get an "invalid dropzone element" error
	*/
	//var myDropzone = new Dropzone("#profile-pix-changer", {
	var myDropzone = new Dropzone("#" + elem, {
		method                       : "post", //"put" is also allowed
		url                          : ajaxURL + '/index.php',
		paramName                    : "file", // The name that will be used to transfer the file
		params                       : {p:'users', 'update-profile-image':true},
        maxFilesize                  : 1, // MB
		maxFiles                     : null, //if not null defines how many files this Dropzone handles.
		uploadMultiple               : false,
		acceptedFiles                : 'image/*',
		autoProcessQueue             : false,
		dictResponseError            : 'Error from server with status {{statusCode}}',
		dictInvalidFileType          : 'Error invalid file type',
		dictFileTooBig               : 'Error file too big. file size is {{filesize}}, max allowed upload size is {{maxFilesize}}',
		addRemoveLinks               : false,
		dictCancelUpload             : 'Cancel this upload', //If addRemoveLinks is true, the text to be used for the cancel upload link.
		dictCancelUploadConfirmation : 'Are you sure you wana cancel this upload?', //If addRemoveLinks is true, the text to be used for confirmation when cancelling upload.
		dictRemoveFile               : 'Remove this file', //If addRemoveLinks is true, the text to be used to remove a file.
		parallelUploads              : 1, //How many file uploads to process in parallel 
		
		/*
		resize: function(file){ return { srcX : '', srcY : '', srcWidth : '', srcHeight : '' } },
		*/
		
		processing(file)
		{
			setAsProcessing('profile-pix-processing');
		},
		
        accept: function(file, done)
		{ 
			/*
			* discovered - while trying to auto-submit the image/form -
			* that done() must first be called for it (or possibly anything, for that matter) to work.
			*
			* Alternatively, you can make use of the event queue to delay the trigger
			* for the form submission, e.g using addEventListener or setTimeout.
			* That way, done() is run first, before the event is processed.
			* See the Site.Event.attachListener() call below
			* @date May 7, 2016 15:10 hrs
			*/
			done(); 
			
			/*
			if (file.name == "something.jpg"){done("Naha, you don't.");}
			else { done(); }
			*/
			//console.log(file);
			//$Style('upload-image-button').visibility = 'visible';
			//Site.Event.attachListener('upload-image-button', 'click', function(){  myDropzone.processQueue() });
			
			//hide the image the dropzone area that shows the selected image
			//this is just to create an effect/experience
			//see reason for this in the 'onsuccess()' callback
			hideDropZonePreview(); 
			
			//auto-submit the form, see reason for this in the 'onsuccess()' callback
			myDropzone.processQueue();
		},
		
		//credits : http://stackoverflow.com/a/32481251/1743192
		error: function(file, errorMessage)
		{
			myDropzone.errors = true;
			myDropzone.errorMsg = errorMessage;
			
			hideDropZonePreview();
			hideDropZoneErrorAndSuccessMarks();
		},
		
		queuecomplete: function()
		{
			if(myDropzone.errors) 
			{
				if( myDropzone.errorType == 'invalid_upload_directory' )
				{
					displayImageUploadStatusMessage('Unable to create image directory. Please try again later');
				}
				else
				{
					displayImageUploadStatusMessage(myDropzone.errorMsg);
				}
			}
			else
			{
				//displayImageUploadStatusMessage('Image successfully uploaded');
			}
		}
	});
	
	/*
	* success is called on each file processed, 
	* Once the backend server returns a response,
	* it matters not whether the backend server response is positive or negative (i.e, file uploaded or not).
	* It is called to indicate that the file has been handed on to the backend processor
	* (and the processor has returned)
	*/
	myDropzone.on('success', function(file, response){ 
		//onqueuecomplete() doesn't take the file and response parameters
		//so, manually create the flags as part of "this" dropzone object
		if(response.error)
		{
			myDropzone.error = true;
			myDropzone.message = response.message;
			myDropzone.errorType = response.errorType;
		}
		else
		{
			myDropzone.error = false;
			console.log(response);
			//$Style('upload-image-button').visibility = 'hidden';
			
			/*
			* don't set the images here, let them be set on successful cropping of the image
			* so that the user doesn't first see this, and then - when cropping is complete - the cropped version
			* an effect that is not so nice, since user is supposed to see the operation as one (atomic) operation
			* not as a two-step upload first, and crop later, 
			* although - under the hood - that is what takes place.
			*
			* For similar reasons, we auto-submit the form,
			* rather than let the user click on the 'upload' button.
			* See the accept() method for more.
			*/
			//setProfilePixUrl(response.imageUrl);
			//setMainNavUserPixUrl(response.imageUrl);
			
			setCropPreviewAreaToRounded();
			activateImageResizer({
				'imageUrl'                 : response.imageUrl, 
				'uniqueIDPrefix'           : opts.imageCropperIDPrefix,
				'displayCropWindowAsPopup' : true,
				'maxWidth'                 : 160,
				'maxHeight'                : 160,
				'minWidth'                 : 160,
				'minHeight'                : 160,
				'popupWidth'               : 160 + 280,
				'popupHeight'              : 160 + 268,
				'previewContainerID'       : 'image-crop-preview' //'user-photo-container'
			});
		} 
		
		hideDropZonePreview();
	});
	
	function displayImageUploadStatusMessage(msg)
	{
		alert(msg);
	}
	
	function hideDropZonePreview()
	{
		document.querySelectorAll('.dz-preview')[0].style.display = 'none';
	}
	
	function hideDropZoneErrorAndSuccessMarks()
	{
		document.querySelectorAll('.dz-success-mark')[0].style.display = 'none';
		document.querySelectorAll('.dz-error-mark')[0].style.display = 'none';
	}
}