<button id="google-login-link" class="social-btn bg-google-color-blue cursor-pointer text-center" onclick="Site.Util.popup('<?php echo SITE_URL; ?>/user-auth/google', '520', '570');" title="Connect with Google">
 <span class="icon-container google-icon-container-blue icon-font-awesome google-icon"></span>
 Connect with Google
</button>
<?php include __DIR__. '/third-party-auth-handler-js.php'; ?>