<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Admin Dashboard'));
$page_instance->load_nav();
?>
<div class="container">
 Welcome to the admin home page
</div>
<?php $page_instance->load_footer('', array()); ?>