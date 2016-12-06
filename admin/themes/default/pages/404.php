<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Page not found'));
$page_instance->load_nav();
?>
<div class="container" style="padding-bottom:30px;">
 <div class="row">
  <div class="col-md-12">
  <center>
   <p>
	<span style="font-weight:400; font-size:150px; color:#900;">Oops!!!</span><br />
	<span>The page you are looking for was not found</span>
   </p>
   <p><a href="<?php echo SITE_URL; ?>">Return to Home page</a></p>
   <p><a href="<?php echo ADMIN_URL; ?>">Return to Admin Dashboard</a></p>
   </center>
  </div>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>