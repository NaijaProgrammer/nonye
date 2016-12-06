<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Received Messages'));
$page_instance->load_nav();
?>
<?php $messages = ItemModel::get_items( array('category'=>'contact-us-messages') ); ?>
<div class="container">
 <?php echo do_page_heading('Received Messages'); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block float-left main-content">
  <table class="table table-bordered table-hover table-responsive">
   <thead>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Sender</th>
     <th class="text-centered">Email</th>
     <th class="text-centered">Subject</th>
     <th class="text-centered">Message</th>
    </tr>
   </thead>
   <tfoot>
    <tr>
     <th class="text-centered">Serial No.</th>
     <th class="text-centered">Sender</th>
     <th class="text-centered">Email</th>
     <th class="text-centered">Subject</th>
     <th class="text-centered">Message</th>
    </tr>
   </tfoot>
   <tbody>
    <?php $serial_no = 1; ?>
    <?php foreach($messages AS $message_data): ?>
    <?php $row_class = ( ($serial_no % 2) ? 'odd-row' : 'even-row' ); ?>
    <tr class="<?php echo $row_class; ?>">
	 <td class="text-centered"><?php echo $serial_no; ?></td>
     <td class="text-centered"><?php echo $message_data['sender']; ?></td>
     <td class="text-centered"><?php echo $message_data['email']; ?></td>
     <td class="text-centered"><?php echo $message_data['subject']; ?></td>
     <td class="text-centered"><?php echo $message_data['message']; ?></td>
	</tr>
	<?php ++$serial_no; ?>
	<?php endforeach; ?>
   </tbody>
  </table>
 </div>
</div>
<?php $page_instance->load_footer('', array()); ?>

