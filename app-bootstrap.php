<?php

/**
 * File name: app-bootstrap.php
 * contains application-specific bootstrap scripts
 * The scripts you add here will be run immediately after the last line in the 'config.php' global, framework-wide configuration file
*/
include INCLUDES_DIR. '/app-functions.php';
require CONTROLLERS_DIR. '/app-controller.class.php';
include SITE_DIR. '/_secret/application-keys.php';

detect_and_redirect_short_url();