<button id="twitter-login-link" class="social-btn bg-twitter-color cursor-pointer text-center" onclick="Site.Util.popup('<?php echo SITE_URL; ?>/user-auth/twitter', '520', '570');" title="Connect with Twitter">
 <span class="icon-container twitter-icon-container icon-font-awesome twitter-icon"></span>
 Connect with Twitter
</button>
<?php include __DIR__. '/third-party-auth-handler-js.php'; ?>