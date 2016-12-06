<?php
$id_postfix  = isset($id_postfix)  ? $id_postfix  : '';
$value       = isset($value)       ? $value       : '';
$placeholder = isset($placeholder) ? $placeholder : '';
$cols        = isset($cols)        ? $cols        : '92';
$rows        = isset($rows)        ? $rows        : '15';
$style       = isset($style)       ? $style       : '';
$button_bar_style = isset($button_bar_style) ? $button_bar_style : ''; 
?>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/rte-editor.css" />
<script src="<?php echo SITE_URL; ?>/js/lib/markdown/Markdown.Converter.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/markdown/Markdown.Sanitizer.js"></script>
<script src="<?php echo SITE_URL; ?>/js/lib/markdown/Markdown.Editor.js"></script>
<div class="wmd-container">
 <div id="wmd-button-bar" class="wmd-button-bar" style="<?php echo $button_bar_style; ?>"></div>
 <textarea 
  id="wmd-input<?php echo $id_postfix; ?>" 
  class="wmd-input processed" 
  name="post-text" 
  cols="<?php echo $cols; ?>" 
  rows="<?php echo $rows; ?>" 
  tabindex="101" 
  placeholder="<?php echo $placeholder; ?>"
  style="<?php echo $style; ?>"
  ><?php echo $value; ?></textarea>
</div>
<script>
<?php //we put it inside addLoadListener, so that where the preview field comes after the initialisation script, the preview will still work ?>
Site.Event.addLoadListener(function(){
var converter = Markdown.getSanitizingConverter(),
//var converter = new Markdown.Converter(),
	    help      = function () { alert("Do you need help?"); },
        options   = {
        helpButton : { handler: help },
        strings    : { quoteexample: "whatever you're quoting, put it right here" }
    };
                
    converter.hooks.chain("preBlockGamut", function (text, rbg) {
        return text.replace(/^ {0,3}""" *\n((?:.*?\n)+?) {0,3}""" *$/gm, function (whole, inner) {
            return "<blockquote>" + rbg(inner) + "</blockquote>\n";
        });
    });
	
	converter.hooks.chain("preConversion", function (text) {
        return text.replace(/\b(a\w*)/gi, "*$1*");
    });

    converter.hooks.chain("plainLinkText", function (url) {
        return "This is a link to " + url.replace(/^https?:\/\//, "");
    });
                
	new Markdown.Editor(converter, "<?php echo $id_postfix; ?>", options).run();
	
	//new Markdown.Editor(converter, "-second", options);
});
</script>