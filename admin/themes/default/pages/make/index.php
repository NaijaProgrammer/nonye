<?php //verify_user_can('Manage Forums'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>''));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading(''); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 
 <div class="inline-block main-content">
  
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>