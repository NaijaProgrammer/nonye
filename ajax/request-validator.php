<?php
require_once( dirname(__DIR__). '/config.php' );
defined('VALID_AJAX_REQUEST') or header("Location:". SITE_URL);