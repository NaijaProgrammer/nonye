<div class="inline-block sidebar sidebar-left" style="border:none;">&nbsp;</div>
<div class="inline-block">
	<?php echo do_page_heading('Access Denied'); ?>
	<p>
	<span style="font-weight:400; font-size:150px; color:red;">Sorry!!!</span><br />
	You lack the appropriate permissions to access this resource.<br />
	The minimum required capability level to access this page is : <strong><?php echo $capability; ?></strong> 
	</p>
	<?php //this is usefule for when this is displayed on front-end and user is not admin, since they won't see the navigation bar ?>
	<center><a style="font-size:18px; font-weight:400;" href="<?php echo SITE_URL; ?>">Back to <?php echo SITE_NAME; ?></a></center>
</div>
<div class="inline-block sidebar sidebar-right" style="border:none;">&nbsp;</div>