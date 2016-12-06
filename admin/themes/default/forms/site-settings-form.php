<?php echo isset($status_message) ? '<p class="text-centered">'. $status_message. '</p>' : ''; ?>
<form method="post" action="">

 <div class="input-group">
  <span class="input-group-addon">Session Lifetime (in seconds)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <input type="text" name="session_lifetime" value="<?php echo UserModel::get_app_setting('session_lifetime'); ?>" class="form-control">
 </div>

 <div class="input-group">
  <span class="input-group-addon">Newsletter Subscription Provider</span>
  <?php $curr_provider = UserModel::get_app_setting('newsletter_subscription_provider'); ?>
  <select class="form-control" name="newsletter_subscription_provider">
   <option value="mailchimp" <?php echo set_as_selected_option('mailchimp', $curr_provider); ?>>MailChimp</option>
   <option value="mailrelay" <?php echo set_as_selected_option('mailrelay', $curr_provider); ?>>MailRelay</option>
  </select>
 </div>

 <div class="pull-right"><input type="submit" value="Update" /></div>

</form>
