<?php
$value       = isset($value)       ? $value       : '';
$placeholder = isset($placeholder) ? $placeholder : '';
?>
<?php
$forums = ForumModel::get_forums( false );
?>
<a id="editor-opener" style="position:absolute; top:100px; right:200px; cursor:pointer; text-decoration:none;">+ Create New Topic</a>

<div id="post-editor-wrapper" style="display:block;">
 <a id="editor-collapser" class="cursor-pointer"><i class="fa fa-arrow-down"></i></a>
 <div class="clear"></div>
 <div class="topic-elements" style="">
 <div class="col-md-4" style="padding-left:0 !important;"><input class="form-control" type="text" placeholder="title"/></div>
 <div class="col-md-2">
  <select id="post-forum-selector" class="form-control">
   <option></option>
   <?php foreach($forums AS $forum): ?><option value="<?php echo $forum['id']; ?>"><?php echo $forum['name']; ?></option><?php endforeach; ?>
  </select>
 </div>
 <div class="col-md-2">
  <select id="post-category-selector" class="form-control">
   <option value="">Category</option>
  </select>
 </div>
 </div>
 <div class="col-md-4" style="padding-left:0 !important;"><input class="form-control" type="text" placeholder="Tags"/></div>
 <div class="clear">&nbsp;</div>

 <div id="editor-window-wrapper"  class="float-left"><textarea id="post-editor" tabindex="101"></textarea></div>
 <div id="preview-window-wrapper" class="float-left"><div id="preview-window"></div></div>
 <div class="clear"></div>
</div>

<script src="<?php echo $site_url; ?>/js/lib/tinymce/tinymce.min.js"></script>
<link rel="stylesheet" href="<?php echo $site_url; ?>/js/lib/select2/css/select2.css" />
<script src="<?php echo $site_url; ?>/js/lib/select2/js/select2.js"></script>
<script>
//https://www.tinymce.com/docs/configure/editor-appearance/
//https://www.tinymce.com/docs/configure/file-image-upload/
//https://www.tinymce.com/docs/configure/integration-and-setup/
tinymce.init({
	selector          : 'textarea#post-editor',
	menubar           : false,
	height            : 150,
	theme             : 'modern',
	images_upload_url : 'postAcceptor.php',
	image_advtab      : true,
	toolbar1          : 'undo redo | styleselect | bold italic underline | bullist numlist outdent indent | forecolor emoticons | link responsivefilemanager',
	automatic_uploads : true,
	//file_picker_types : 'file image media',
	plugins:[
		'advlist autolink lists link charmap print preview hr anchor pagebreak',
		'searchreplace wordcount visualblocks visualchars code fullscreen',
		'insertdatetime media nonbreaking save table contextmenu directionality',
		'emoticons template paste textcolor colorpicker textpattern imagetools'
	],
	templates:[
		{ title: 'Test template 1', content: 'Test 1' },
		{ title: 'Test template 2', content: 'Test 2' }
	],
	content_css:[
		'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
		'//www.tinymce.com/css/codepen.min.css'
	],
	
	//begin responsive-file-manager plugin integration
	external_filemanager_path:"<?php echo SITE_URL; ?>/lib/responsive-filemanager/",
	filemanager_title:"Upload File" ,
	external_plugins: { "responsivefilemanager" : "plugins/responsivefilemanager/plugin.min.js" },
	//end responsive-file-manager plugin integration
	
	setup: function(editor){
		//http://stackoverflow.com/a/29526186/1743192
		editor.on("keyup", function(){
			$('#preview-window').html(tinymce.activeEditor.getContent());
		});
	},
	init_instance_callback : function(editor){  
		//editor.getBody().style.backgroundColor = "#FFFF66"; 
	},
  
	/* file_picker_callback: function(callback, value, meta) {
		// Provide file and text for the link dialog
		if (meta.filetype == 'file') {
		callback('mypage.html', {text: 'My text'});
		}

		// Provide image and alt text for the image dialog
		if (meta.filetype == 'image') {
		callback('myimage.jpg', {alt: 'My alt text'});
		}

		// Provide alternative source and posted for the media dialog
		if (meta.filetype == 'media') {
			callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
		}
	},
	*/
	/* images_upload_handler: function (blobInfo, success, failure) {
		//https://www.tinymce.com/docs/configure/file-image-upload/
		var xhr, formData;

		xhr = new XMLHttpRequest();
		xhr.withCredentials = false;
		xhr.open('POST', 'postAcceptor.php');
		xhr.onload = function(){
			var json;

			if (xhr.status != 200){
				failure('HTTP Error: ' + xhr.status);
				return;
			}

			json = JSON.parse(xhr.responseText);

			if (!json || typeof json.location != 'string') {
				failure('Invalid JSON: ' + xhr.responseText);
				return;
			}

			success(json.location);
		};

		formData = new FormData();
		formData.append('file', blobInfo.blob(), blobInfo.filename());

		xhr.send(formData);
	},
	*/
});


//https://select2.github.io/options.html
$('#post-forum-selector').select2({
	tags                    : false,
	placeholder             : 'Forum',
	allowClear              : true,
	minimumResultsForSearch : 20, // at least 20 results must be displayed
});

$('#post-forum-selector').on('select2:select', function (evt){
	
	$.ajax({
		url      : "<?php echo $site_url; ?>/ajax/categories",
		dataType : 'json'
		$('#post-forum-selector').val()
	})
	
	$('#post-category-selector').select2({
		placeholder             : 'Forum',
		allowClear              : true,
		minimumResultsForSearch : 20,
		ajax : {
			url      : "<?php echo $site_url; ?>/ajax/categories",
			dataType : 'json',
			delay    : 250,
			data : function (params){ console.log(params)
				return {
					forum : params.term, // search term
					page  : params.page
				};
			},
			processResults : function (data, params){
				// parse the results into the format expected by Select2
				// since we are using custom formatting functions we do not need to
				// alter the remote JSON data, except to indicate that infinite
				// scrolling can be used
			  
				/*
				* Returned data format: http://stackoverflow.com/questions/20926707/how-to-use-select2-with-json-via-ajax-request
				[{"itemName":"Test item no. 1","id":5},
				{"itemName":"Test item no. 2","id":6},
				{"itemName":"Test item no. 3","id":7},
				{"itemName":"Test item no. 4","id":8},
				{"itemName":"Test item no. 5","id":9},
				{"itemName":"Test item no. 6","id":10},
				{"itemName":"Test item no. 7","id":11}]
				*/
				params.page = params.page || 1;

				return {
					results : $.map(data, function (item){
						//http://stackoverflow.com/a/21602199/1743192
						//http://stackoverflow.com/a/37082328/1743192
						return {
							text: item.completeName, //item.tag_value
							slug: item.slug,
							id: item.id //item.tag_id
						}
					}),
					pagination : {
						more: (params.page * 30) < data.total_count
					}
				};
			},
			cache: true
		},
	})
})
</script>

<script>
$('#editor-opener').on('click', function(event){ $('#post-editor-wrapper').slideToggle('slow'); });
$('#editor-collapser').on('click', function(event){ $('#post-editor-wrapper').slideUp('slow'); });
</script>