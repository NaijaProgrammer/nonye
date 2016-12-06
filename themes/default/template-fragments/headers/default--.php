<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <title><?php echo $page_title. ' :: '. get_site_name(); ?></title>
	<meta name="keywords" content="<?php echo $page_keywords; ?>" />
	<meta name="description" content="<?php echo $page_description; ?>" />
	<meta name="robots" content="<?php echo isset($robots_value) ? $robots_value : 'all'; ?>" />
	
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@Michael05907608" />
	<meta name="twitter:domain" content="asqeet.com" />
	<meta property="fb:app_id" content="983608321746284" />
	<meta property="og:url"  content="<?php echo $open_graph_data['url']; ?>" />
	<meta property="og:type" content="<?php echo $open_graph_data['content-type']; ?>" />
	<meta property="og:title" itemprop="title name" name="twitter:title" content="<?php echo $open_graph_data['title']; ?>" />
	<meta property="og:description" itemprop="description" name="twitter:description"  content="<?php echo $open_graph_data['description']; ?>" />
	<meta property="og:image" itemprop="image primaryImageOfPage" name="twitter:image" content="<?php echo $open_graph_data['image-url']; ?>" />
	<meta property="og:image:width" content="150" />
	<meta property="og:image:height" content="85" />
	
	<!--<base href="<?php echo $site_url. '/'; ?>" />-->
	
	<link rel="canonical" href="<?php echo $open_graph_data['url']; ?>" />
	<link rel="Shortcut Icon" type="image/x-icon" href="<?php echo $site_url; ?>/logo.gif" />

	<link rel="stylesheet" href="<?php echo $site_url; ?>/css/util.css">
	<link rel="stylesheet" href="<?php echo $site_url; ?>/css/font-awesome-4.1.0.css">
	<link rel="stylesheet" href="<?php echo $theme_url; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>/css/theme.css">
	
	<script src="<?php echo $site_url; ?>/js/lib/jslib/jslib.js"></script>
	<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/eventmanager.js"></script>
	<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/animator.js"></script>
	<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/slider.js"></script>
	<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/xhr.js"></script>
	<script src="<?php echo $site_url; ?>/js/lib/jslib/u-i-n-x/form.js"></script>
	<script src="<?php echo $site_url; ?>/js.php"></script>
	<script src="<?php echo $site_url; ?>/js/site.js"></script>
	<script src="<?php echo $theme_url; ?>/js/jquery-2.2.1.min.js"></script>
	<script src="<?php echo $site_url; ?>/js/functions.js"></script>
	
	<link rel="stylesheet" href="<?php echo $site_url; ?>/css/prettify.css"/>
	<script src="<?php echo $site_url; ?>/js/lib/prettify/prettify.js?lang=html&amp;skin=sunburst"></script>
	
 </head>
 <body>
 <!-- Begin google analytics <?php include(SITE_DIR. '/includes/google-analytics-tracking-code.php'); ?> End google analytics -->
 <!-- include Linked-in Javascript SDK -->
<script type="text/javascript" src="//platform.linkedin.com/in.js">
    api_key:   774x9dguwvb7wr
    authorize: true
    lang:      en_US
</script>
<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
<script src="<?php echo $site_url; ?>/js/lib/fb/sdk-init.js"></script>
