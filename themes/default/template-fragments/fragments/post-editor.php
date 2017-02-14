<style>
/*Post editor */
#post-editor-wrapper
{
	position:fixed; 
	bottom:0; 
	left:0; 
	width:100%; 
	z-index:1000; 
	padding:25px 25px; 
	background-color:#e9e9e9; 
	display: none; /*<?php //echo ( $auto_display ? 'block' : 'none' ); ?>*/
}
#editor-collapser
{
	position:absolute; 
	right:10px;
	display:none;
}
#editor-window-wrapper, #preview-window-wrapper
{
	width:50%; 
	padding-right:2px;
}
#preview-window-wrapper
{
	height:225px;
}
#preview-window
{
	box-sizing:border-box; 
	width:100%; 
	height:100%; 
	min-height:auto; 
	padding:7px; 
	margin:0; 
	background:#fff;
	word-wrap:break-word;
	overflow-y:auto;
}

/* Begin - Style used for when the post editor is displayed on init */
#post-editor-wrapper { position:relative; padding-bottom:35px; }
#post-create-btn { position:relative; bottom:5px; }
/* End - Style used for when the post editor is displayed on init */

@media screen and (max-width: 767px){
	#preview-window-wrapper{ display:none; }
	#editor-window-wrapper { width:100%; padding:0; }
	#editor-collapser { }
	#post-create-btn { position:relative; right:15px; top:5px; }
}
@media screen and (max-width: 500px){
	
}
@media screen and (max-width: 450px){
	
}
</style>
<?php
$value       = isset($value)       ? $value       : '';
$placeholder = isset($placeholder) ? $placeholder : '';
$auto_display = isset($auto_display) ? $auto_display : false;
$post_id      = isset($post_id) ? $post_id : 0;
$parent_post_id = isset($parent_post_id) ? $parent_post_id : 0;
$on_before_submit = isset($on_before_submit) ? $on_before_submit : 'function(){ return true; }';
$on_success       = isset($on_success) ? $on_success : 'undefined';

$show_post_title_field    = get_app_setting('show-post-title-field', true);
$show_post_forum_field    = get_app_setting('show-post-forum-field', true);
$show_post_category_field = get_app_setting('show-post-category-field', true);
$show_post_body_field     = get_app_setting('show-post-body-field', true);
$show_post_tags_field     = get_app_setting('show-post-tags-field', true);

$post = !empty($post_id) ? PostModel::get_post_instance($post_id) : null;

$post_title = '';
$post_status = '';
$post_content  = '';
$post_desc     = '';

$editing_post = ($post != null);

if($post != null) {
	$post_title    = $post->get('title');
    $post_status   = $post->get('status');
	$post_content  = $post->get('content');
	$post_desc     = $post->get('excerpt');
}
?>
<?php if($show_post_forum_field): $forums = ForumModel::get_forums( false, array(), array('name'=>'ASC'), 0 ); endif; ?>
<?php if($show_post_category_field && !$show_post_forum_field): $categories = CategoryModel::get_categories( false, array(), array('name'=>'ASC'), 0 ); endif; ?>

<div id="post-editor-wrapper">
<form id="new-post-form">
 <a id="editor-collapser" class="cursor-pointer"><i class="fa fa-arrow-down"></i></a>
 <div class="clear"></div>
 <div class="topic-elements" style="">

 <?php if($show_post_title_field): ?>
 <div class="col-md-3" style="padding-left:0 !important;">
  <input id="post-title-field" class="form-control" type="text" value="<?php echo sanitize_html_attribute($post_title); ?>" placeholder="title"/>
 </div>
 <?php endif; ?>
 
 <?php if($show_post_forum_field): ?>
 <div class="col-md-3">
  <select id="post-forum-selector" class="form-control">
   <option></option>
   <?php foreach($forums AS $forum): ?><option value="<?php echo $forum['id']; ?>"><?php echo $forum['name']; ?></option><?php endforeach; ?>
  </select>
 </div>
 <?php endif; ?>
 
 <?php if($show_post_category_field): ?>
 <div class="col-md-3">
  <select id="post-category-selector" class="form-control bg-right bg-no-repeat">
   <option value="">Category</option>
   <?php if(!$show_post_forum_field): ?>
    <?php foreach($categories AS $category): ?><option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option><?php endforeach; ?>
   <?php endif; ?>
  </select>
 </div>
 </div>
 <?php endif; ?>
 
 <div class="col-md-3">
  <select id="post-status-selector" class="form-control">
    <option value="">Status</option>
    <option value="published" <?php echo set_as_selected_option('published', $post_status); ?>>Published</option>
	<option value="draft" <?php echo set_as_selected_option('draft', $post_status); ?>>Draft</option>
   </select>
 </div>
 <div class="clear">&nbsp;</div>

 <?php if($show_post_body_field): ?>
 <div id="editor-window-wrapper"  class="float-left"><textarea id="post-editor" tabindex="101"></textarea></div>
 <div id="preview-window-wrapper" class="float-left"><div id="preview-window"></div></div>
 <div class="clearfix">&nbsp;</div>
 <?php endif; ?>
 
 <div class="col-md-6" style="padding-left:0 !important; padding-right:0 !important">
  
  <?php if($show_post_tags_field): ?>
  <div class="col-md-10" style="padding-left:0 !important;">
   <input id="post-tags-field" class="form-control bg-right bg-no-repeat" type="text" placeholder="Tag your post. Separate multiple tags using commas(,)"/>
   <div id="tags-container" style="background:#fff; padding:5px; padding-right:3px; max-height:150px; overflow-y:auto;"></div>
  </div>
  <?php endif; ?>
  
  <input id="parent-post-id" type="hidden" value="0"/>
  <div class="col-md-2" style="padding-right:0 !important;">
   <?php $action_btn_id   = ( ($editing_post) ? 'post-update-btn' : 'post-create-btn' ); ?>
   <?php $action_btn_text = ( ($editing_post) ? 'Update' : 'Create' ); ?>
   <button id="<?php echo sanitize_html_attribute($action_btn_id); ?>" class="btn btn-primary float-right pr25 pl25"><?php echo $action_btn_text; ?></button>
  </div>
 </div>
 
 <div class="col-md-6" style="padding-right:0 !important;"><span id="status-message" class="status-message"></span></div>
</form>
 
 <iframe id="image-uploader-target" name="image-uploader-target" style="display:none"></iframe>
 <form id="image-uploader-form" action="<?php echo SITE_URL; ?>/post-editor-uploader.php?source=iframe" target="image-uploader-target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
  <input name="post-image" type="file" onchange="$('#image-uploader-form').submit();this.value='';">
 </form>
 
</div>

<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/form.js"></script>
<script src="<?php echo $site_url; ?>/js/lib/tinymce/tinymce.min.js"></script>
<link rel="stylesheet" href="<?php echo $site_url; ?>/js/lib/select2/css/select2.css" />
<script src="<?php echo $site_url; ?>/js/lib/select2/js/select2.js"></script>

<script>
//var siteURL = '<?php echo $site_url; ?>';
var ajaxURL = siteURL + '/ajax';
var postTagsField = $('#post-tags-field');
var tagsContainerIsVisible = false;

(function doInitTinyMCE() {
//https://www.tinymce.com/docs/configure/editor-appearance/
//https://www.tinymce.com/docs/configure/file-image-upload/
//https://www.tinymce.com/docs/configure/integration-and-setup/
//https://www.tinymce.com/docs/configure/url-handling/
//http://community.tinymce.com/forum/viewtopic.php?id=24254
//http://stackoverflow.com/questions/4412589/tinymce-paths-how-to-specify-where-to-load-things-like-editor-plugin-js
//read for custom tinymce plugin dev: http://stackoverflow.com/a/5067477/1743192
tinymce.init({
		relative_urls     : false,
		remove_script_host : true,
		document_base_url : siteURL + '/',
		//default_link_target: "_blank",
		link_assume_external_targets: true, //http://stackoverflow.com/a/29596786/1743192
		selector          : 'textarea#post-editor',
		menubar           : false,
		height            : 150,
		theme             : 'modern',
		images_upload_url : 'post-editor-uploader.php',
		images_upload_credentials: true,
		images_upload_base_path: siteURL,
		image_advtab      : true,
		toolbar1          : 'undo redo | styleselect | bold italic underline | bullist numlist outdent indent | forecolor emoticons | image code',
		//toolbar1          : 'undo redo | styleselect | bold italic underline | bullist numlist outdent indent | forecolor emoticons | link image',
		//menubar: "tools",
        code_dialog_width: 800,
		//automatic_uploads : false,
		file_picker_types : 'file image media',
		plugins:[
			'advlist autolink lists link image charmap print preview hr anchor pagebreak',
			'searchreplace wordcount visualblocks visualchars code fullscreen',
			'insertdatetime media nonbreaking save table contextmenu directionality',
			'emojis template paste textcolor colorpicker textpattern imagetools codesample toc'
		],
		templates:[
			{ title: 'Test template 1', content: 'Test 1' },
			{ title: 'Test template 2', content: 'Test 2' }
		],
		content_css:[
			'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
			'//www.tinymce.com/css/codepen.min.css'
		],
		
		setup: function(editor){ 
		
			var urlMaps = [];
			
			//http://stackoverflow.com/a/29526186/1743192
			//also checkout : http://stackoverflow.com/a/17825012/1743192
			//and : http://stackoverflow.com/a/27323689
			editor.on("change", function(){
				updateContent();
			});
			editor.on("keyup", function(){
				updateContent();
			});
			editor.on("paste", function(){
				updateContent();
				console.log('paste');
			});
			
			/* function updateContent()
			{
				var editorContent = tinymce.activeEditor.getContent();
				var buffer        = editorContent;
				var urls          = extractUrlParts(editorContent); 
				
				//var urlParts = extractUrlParts(text, function(elem, index, url){ return (elem.parentNode.className.indexOf( getEmbeddedUrlContainerID(url, index)) ) != -1; });
				//console.log(urls);
				
				for(var i = 0; i < urls.length; i++)
				{
					var url = urls[i].href;
					
					if(isInMap(url))
					{ 
						//console.log('url => ' + url);
						//console.log(urlMaps);
						buffer = buffer.replace( new RegExp( getSpinner(), 'gm' ), '' );
						buffer = buffer.replace( new RegExp( escapeRegex('<a href="' + url + '">' + url + '</a>'), 'gm'), getValue(url) );
					}
					else
					{
						var anchorLink = '<a href="' + url + '">' + url + '</a>';
						buffer = buffer.replace( new RegExp(escapeRegex(anchorLink), 'gm'), getSpinner() +  anchorLink );
					}
				}
				
				$('#preview-window').html(buffer);
				
				inlineEmbedUrls(editorContent);
			}
			function getSpinner()
			{
				return '<span class="bg-no-repeat bg-spinner" style="display:inline-block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			}
			function getValue(url)
			{
				for(var i = 0; i < urlMaps.length; i++)
				{
					var currObj = urlMaps[i];
					if( (typeof currObj == 'object')  && (currObj.key == url) )
					{
						return ( (currObj.value != '') ? currObj.value : '<a href="' + url + '">' + url + '</a>' );
					}
				}

				return '<a href="' + url + '">' + url + '</a>';
			}
			function isInMap(url)
			{
				for(var i = 0; i < urlMaps.length; i++)
				{
					var currObj = urlMaps[i];
					if( (typeof currObj == 'object')  && (currObj.key == url) )
					{
						return true;
					}
				}
				
				return false;
			}
			function inlineEmbedUrls(text)
			{
				var urlParts = extractUrlParts(text);

				for(var i = 0; i < urlParts.length; i++)
				{
					var url = urlParts[i].href;
					
					if(isInMap(url))
					{
						continue;
					}
					else
					{
						embed(url);
					}
				}
				
				function embed(url)
				{
					$.ajax(ajaxURL, {
						method   : 'GET',
						cache    : false,
						data     : { p : 'posts', 'get-embed-code':true, 'url':url },
						error    : function(jqXHR, status, error){
							if(isDevServer)
							{
								console.log( 'Url embed status : ' + status + '\r\nerror : ' + error );
							}
						},
						success  : function(data, status, jqXHR){
							if(isDevServer)
							{
								console.log( 'Url embed status : ' + status + '\r\nsuccess : ' + data );
							}
							
							data     = JSON.parse(data);
							var url  = decodeURIComponent(data.url);
							var html = data.html;
							
							//$('#preview-window').html( tinymce.activeEditor.getContent().replace( new RegExp( '<a href="' + url + '">' + url + '</a>', 'gm'), html ) );
							$('#preview-window').html( $('#preview-window').html().replace( new RegExp( getSpinner(), 'gm' ), '' ) );
							$('#preview-window').html( $('#preview-window').html().replace( new RegExp( escapeRegex('<a href="' + url + '">' + url + '</a>'), 'gm'), html ) );
							
							urlMaps.push({'key':url, 'value':html});
						},
						complete : function(jqXHR, status)
						{
							
						}
					});
				}
			}*/
		},
		init_instance_callback : function(editor){  
			//editor.getBody().style.backgroundColor = "#FFFF66"; 
			<?php if(!empty($value)): ?>
			editor.setContent('<?php echo $value; ?>');
			updateContent();
			<?php endif; ?>
		},
	  
		file_picker_callback: function(callback, value, meta) {
			
			// Provide file and text for the link dialog
			if (meta.filetype == 'file') {
				//callback('mypage.html', {text: 'My text'});
			}

			// Provide image and alt text for the image dialog
			if (meta.filetype == 'image') {
				//callback('myimage.jpg', {alt: 'My alt text'});
				$('#image-uploader-form input').click();
			}

			// Provide alternative source and posted for the media dialog
			if (meta.filetype == 'media') {
				//callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
			}
		},
});

	function updateContent()
	{
				var editorContent = tinymce.activeEditor.getContent();
				var buffer        = editorContent;
				var urls          = extractUrlParts(editorContent); 
				
				//var urlParts = extractUrlParts(text, function(elem, index, url){ return (elem.parentNode.className.indexOf( getEmbeddedUrlContainerID(url, index)) ) != -1; });
				//console.log(urls);
				
				for(var i = 0; i < urls.length; i++)
				{
					var url = urls[i].href;
					
					if(isInMap(url))
					{ 
						//console.log('url => ' + url);
						//console.log(urlMaps);
						buffer = buffer.replace( new RegExp( getSpinner(), 'gm' ), '' );
						buffer = buffer.replace( new RegExp( escapeRegex('<a href="' + url + '">' + url + '</a>'), 'gm'), getValue(url) );
					}
					else
					{
						var anchorLink = '<a href="' + url + '">' + url + '</a>';
						buffer = buffer.replace( new RegExp(escapeRegex(anchorLink), 'gm'), getSpinner() +  anchorLink );
					}
				}
				
				$('#preview-window').html(buffer);
				
				inlineEmbedUrls(editorContent);
	}
	function getSpinner()
	{
		return '<span class="bg-no-repeat bg-spinner" style="display:inline-block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
	}
	function getValue(url)
	{
				for(var i = 0; i < urlMaps.length; i++)
				{
					var currObj = urlMaps[i];
					if( (typeof currObj == 'object')  && (currObj.key == url) )
					{
						return ( (currObj.value != '') ? currObj.value : '<a href="' + url + '">' + url + '</a>' );
					}
				}

				return '<a href="' + url + '">' + url + '</a>';
	}
	function isInMap(url)
	{
		for(var i = 0; i < urlMaps.length; i++){
			var currObj = urlMaps[i];
			if( (typeof currObj == 'object')  && (currObj.key == url) ){
				return true;
			}
		}

		return false;
	}
		function inlineEmbedUrls(text)
	{
				var urlParts = extractUrlParts(text);

				for(var i = 0; i < urlParts.length; i++)
				{
					var url = urlParts[i].href;
					
					if(isInMap(url))
					{
						continue;
					}
					else
					{
						embed(url);
					}
				}
				
				function embed(url)
				{
					$.ajax(ajaxURL, {
						method   : 'GET',
						cache    : false,
						data     : { p : 'posts', 'get-embed-code':true, 'url':url },
						error    : function(jqXHR, status, error){
							if(isDevServer)
							{
								console.log( 'Url embed status : ' + status + '\r\nerror : ' + error );
							}
						},
						success  : function(data, status, jqXHR){
							if(isDevServer)
							{
								console.log( 'Url embed status : ' + status + '\r\nsuccess : ' + data );
							}
							
							data     = JSON.parse(data);
							var url  = decodeURIComponent(data.url);
							var html = data.html;
							
							//$('#preview-window').html( tinymce.activeEditor.getContent().replace( new RegExp( '<a href="' + url + '">' + url + '</a>', 'gm'), html ) );
							$('#preview-window').html( $('#preview-window').html().replace( new RegExp( getSpinner(), 'gm' ), '' ) );
							$('#preview-window').html( $('#preview-window').html().replace( new RegExp( escapeRegex('<a href="' + url + '">' + url + '</a>'), 'gm'), html ) );
							
							urlMaps.push({'key':url, 'value':html});
						},
						complete : function(jqXHR, status)
						{
							
						}
					});
				}
	}	
})();

//because the initial display value of the editor-wrapper is none,
//If these are init-ed on page load, before we set the display to block (using the slide function)
//their lengths shrink.
//To prevent that, wrap their initialization in this function,
//and call the function after sliding up the editor window
//because after sliding up, the editor window will have a display value of 'block',
//and these wouldn't shrink
function initSelect2Fields()
{
	//https://select2.github.io/options.html
	//https://select2.github.io/examples.html
	$('#post-forum-selector').select2({
		tags                    : false,
		placeholder             : 'Forum',
		allowClear              : true,
		minimumResultsForSearch : 20, // at least 20 results must be displayed
	});
	$('#post-category-selector').select2({placeholder:'Category'});<?php //just dummy for consistent look-and-feel ?>
	$('#post-forum-selector').on('select2:select', function (evt){ 
	
		var categoryChooser = $('#post-category-selector');
		categoryChooser.select2("destroy");
		categoryChooser.empty();
		categoryChooser.addClass('disabled')
		categoryChooser.addClass('bg-spinner');
	
		$.ajax(ajaxURL, {
			method   : 'GET',
			//dataType : 'json', //leave it at intelligentGuess
			cache    : false,
			data     : { 
				p    : 'categories',
				forum:$('#post-forum-selector').val() 
			},
			error    : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'status : ' + status + '\r\nsuccess : ' + data );
				}
				categoryChooser.select2({
					placeholder             : 'Category',
					allowClear              : true,
					minimumResultsForSearch : 20,
					//the select2 plugin uses data.text, 
					//if your backend returns result in the format with 'text' as a data member, use the data as returned. 
					//if your backend returns result with no .text member, use the jquery.map function to parse the returned result to 
					// the select2 supported format
					//data                  : JSON.parse(data), //[{ id: 0, text: 'enhancement' }, { id: 1, text: 'bug' }, { id: 2, text: 'duplicate' }],
					data                    : $.map( JSON.parse(data), function (obj) {
												obj.text = obj.text || obj.name;
												return obj;
											})
				})
			},
			complete : function(jqXHR, status)
			{
				categoryChooser.removeClass('disabled');
				categoryChooser.removeClass('bg-spinner');
			}
		})
	});
}

function initPostTags()
{
	postTagsField.tagEditor({placeholder : 'Tag your post. Separate multiple tags using commas(,)'});

	(function bindTagEditorListener(){
		
		$('.tag-editor').on('click', function(e){
			
			//This allows the user type into the tags field
			//Without this check, the function will run again, preventing the user from typing
			if(tagsContainerIsVisible)
			{
				return;
			}
			
			$('.tag-editor').addClass('bg-right').addClass('bg-no-repeat').addClass('bg-spinner');
			$.ajax(ajaxURL, {
				method   : 'GET',
				cache    : true,
				data     : { 
					p           : 'tags',
					'tag-names' : true 
				},
				error    : function(jqXHR, status, error){
					if(isDevServer)
					{
						console.log( 'status : ' + status + '\r\nerror : ' + error );
					}
				},
				success  : function(data, status, jqXHR){
					if(isDevServer)
					{
						console.log( 'status : ' + status + '\r\nsuccess : ' + data );
					}
						
					var availTags = JSON.parse(data);
					postTagsField.tagEditor('destroy');
					postTagsField.tagEditor({
						forceLowercase : false,
						placeholder    : 'Tag your question. Separate multiple tags using commas(,)',
						maxTags        : 5,
						autocomplete   : {
							delay    : 0, // show suggestions immediately
							position : { collision: 'flip' }, // automatic menu position up/down
							source   : availTags
						},
						beforeTagSave : function(field, editor, tags, tag, val)
						{
							
							
							<?php import_admin_functions(); ?>
							<?php if( !user_can('Create Tags') ): ?>
							//prevent user from entering tags that don't exist
							if(!Site.Util.inArray(val, availTags))
							{
								return false;
							}
							<?php endif; ?>

							return val;
						}
					});
						
					showTagsContainer(availTags);
					$('.tag-editor').removeClass('bg-right').removeClass('bg-no-repeat').removeClass('bg-spinner');
					$('.tag-editor').addClass("tag-editor-active");//just to allow css styling, credits: https://github.com/Pixabay/jQuery-tagEditor/issues/39#issuecomment-220808222 
				},
				complete : function(jqXHR, status)
				{
					bindTagEditorListener();
				}
			})
		});
	})()
}

function showTagsContainer(tags)
{
	var arr = [];
	
	for(var i = 0, len = tags.length; i < len; i++)
	{
		var tag = tags[i];
		arr[i] = '<small id="tag-item-' + tag + '" class="selectable-tag cursor-pointer">' + tag + '</small>';
	}

	$('#tags-container').html( arr.join('') );
	
	(function initTagClick(tags){
		for(var i = 0, len = tags.length; i < len; i++)
		{
			currTag = tags[i];
			addClickListener(currTag);
		}
	})(tags);
	
	function addClickListener(tag)
	{
		var tagID = 'tag-item-' + tag;
		$('#' + tagID).on('click', function(e){
			postTagsField.tagEditor('addTag', $Html(tagID), true);
		});
	}
		
	$('#tags-container').slideDown(function(){
		tagsContainerIsVisible = true;
	});
}

function hideTagsContainer()
{
	$('#tags-container').html( '' );
	$('#tags-container').slideUp(function(){
		tagsContainerIsVisible = false;
	});
}

/*
* Remove the styling on the 'post-tags-field' input
* and hide the pull-out tags div,
* only if the click event occurs outside of the boundaries of the post-tags-field and the pull-out tags div
*/
$(document).on('click', function(e){
	if( (e.target.className.indexOf('tag-editor') == -1) && ( e.target.className.indexOf('selectable-tag') == -1 ) && ( e.target.id != 'tags-container') )
	{
		$('.tag-editor').removeClass("tag-editor-active");
		hideTagsContainer();
	}
});

//$('#post-create-btn').on('click', function(e){
$('#new-post-form').on('submit', function(e){
	e.preventDefault();
	$('#status-message').html( '' );
	
	var editingPost = '<?php echo $editing_post; ?>';
	var btnID       = editingPost ? 'post-update-btn' : 'post-create-btn';
	
	setAsProcessing(btnID);
	disable(btnID);
	
	var formContent = ''; //$('#preview-window').html(); //tinymce.activeEditor.getContent();
	<?php if($show_post_body_field): ?>
	formContent = $('#preview-window').html();
	<?php endif; ?>
	
	var data = { p : 'posts', content : formContent, creator_id : '<?php echo UserModel::get_current_user_id(); ?>' }
	var extraData = null;
	var parentPostID = $('#parent-post-id').val();

	if( parentPostID > 0 ) {
		//this is a reply/response
		extraData = { reply : true, parent_id : parentPostID }
	}
	else {
		
		var postTitle    = '';
		var postForum    = '';
		var postCategory = '';
		var postTags     = '[]';
		var postStatus   = form.getSelectElementSelectedValue('post-status-selector');
		
		<?php if($show_post_title_field): ?>
		postTitle = $('#post-title-field').val();
		<?php endif; ?>
		
		if(editingPost) {
			//we are updating a previously created post
			extraData = {
				update : true,
				id     : '<?php echo $post_id; ?>',
				title  : postTitle, 
				status : postStatus,
			}
		}
		
		else {
			//this is a brand-new post
			<?php if($show_post_forum_field): ?>
			postForum = $('#post-forum-selector').val();
			<?php endif; ?>
			
			<?php if($show_post_category_field): ?>
			postCategory = $('#post-category-selector').val();
			<?php endif; ?>
			
			<?php if($show_post_tags_field): ?>
			postTags = JSON.stringify( postTagsField.tagEditor('getTags')[0].tags );
			<?php endif; ?>
			
			extraData = {
				create   : true,
				title    : postTitle, //$('#post-title-field').val(),
				forum    : postForum, //$('#post-forum-selector').val(),
				category : postCategory, //$('#post-category-selector').val(),
				tags     : postTags, //JSON.stringify( postTagsField.tagEditor('getTags')[0].tags )
				status   : postStatus,
			}
		}
	}
	
	var onBeforeSubmit = <?php echo $on_before_submit; ?>;
	var onSuccess = <?php echo $on_success; ?>;
	
	if( typeof onBeforeSubmit === 'function' ) {
		var moreData = onBeforeSubmit(); 
		
		if( moreData === false ) {
			enable(btnID);
			unsetAsProcessing(btnID)
			return;
		}
		else if( (typeof moreData['error'] !== 'undefined') && (moreData['error']) ) {
			displayStatusMessage( moreData['message'], 'error' );
			enable(btnID);
			unsetAsProcessing(btnID);
			return;
		}
		if(typeof moreData === 'object') {
			for(var x in moreData) {
		        extraData[x] = moreData[x];
	        }
		}
	}
		
	for(var x in extraData) {
		data[x] = extraData[x];
	}
	
	function displayStatusMessage(message, msgType) {
		if(msgType == 'error') {
			$('#status-message').removeClass('success');
			$('#status-message').addClass('error');
		}
		else {
			$('#status-message').removeClass('error');
			$('#status-message').addClass('success');
		}
		
		$('#status-message').html( message );
	}
	
	$.ajax(ajaxURL + '/index.php', {
		method   : "POST",
		cache    : false,
		data     : data,
		error    : function(jqXHR, status, error) {
			enable(btnID);
			unsetAsProcessing(btnID);
		},
		success  : function(data, status, jqXHR) {
			
			data = JSON.parse(data);

			if(data.error) {
				displayStatusMessage( data.message, 'error' );
				if(data.errorType == 'unauthenticatedUserError') {
					showLoginForm();
					
					function showLoginForm() {
						//$('#user-authentication-section').modal();
						setTimeout(function redirect(){location.reload()}, 2000);
					}
				}
				else {
					enable('post-create-btn');
					unsetAsProcessing('post-create-btn');
				}
			}
			else {
				
				if( parentPostID > 0 ) {
					$('#new-post-form')[0].reset();
					displayStatusMessage('', 'success');
					return; //since we are now using ajax to auto-get the most recent comments, (in view.php) no need to refresh the page to see the comment
				}
				
				if( typeof onSuccess == 'function') {
					//onSuccess signature(form)
					onSuccess( $('#new-post-form')[0] )
				}
				else {
					$('#new-post-form')[0].reset();
					displayStatusMessage('Post submitted successfully. Redirecting...', 'success');
				    $('#post-editor-wrapper').slideUp('slow');
				    setTimeout(function redirect(){location.reload()}, 1000);
				}
				
				enable(btnID);
				unsetAsProcessing(btnID);
			}
		},
		complete : function(jqXHR, status) {

		}
	})
});
</script>
<script>
//$('.post-editor-opener').on( 'click', function(event){ showPostEditor(event); } )

//function showPostEditor(event)
function showPostEditor(parentPostID)
{
    //event.preventDefault();
	//var target = event.target;
	//var parentPostID = parseInt( target.getAttribute('data-parent-id') );
	
	$('#post-editor-wrapper').slideDown('slow');
	$('#editor-collapser').on('click', function(event){ $('#post-editor-wrapper').slideUp('slow'); });
	
	//this is a response/comment to a post
	if( parentPostID && parentPostID > 0 )
	{
		postTagsField.tagEditor('destroy'); //destroy this if it previously exists, so we can successfully hide the post-tags-field without issues. See comment in 'else' section
		$('#post-create-btn').html('Post reply');
		$('#parent-post-id').val(parentPostID);
		$('#post-title-field').hide();
		$('#post-forum-selector').hide();
		$('#post-category-selector').hide();
		$('#post-tags-field').hide();
		$('#tags-container').hide();
		$('#post-status-selector').hide();
	}
	else
	{
		//put these function calls here -- inside else -- ,
		//otherwise, -- without being inside the 'else' statement -- 
		// the 'hide()' effect will not work on the 'forum', 'categories' and 'tags' fields
		//because what we are hiding are the original form fields,
		//but these (select2 and tagEditor) create their own fields,
		//which are not affected by our hide() call
		initSelect2Fields();
		initPostTags();
	}
}
</script>

<?php if( $auto_display ): ?>
<script>
$(document).ready(function(){
	showPostEditor( <?php echo $parent_post_id; ?> );
	
	$('.post-editor-opener').on('click', function(event){ 
		event.preventDefault();
		$('body,html').animate({scrollTop:$(document).height()},1000);
	});
});
</script>
<?php endif; ?>