<?php
require_once('config.php');
header('Content-type:text/javascript');
echo 'var siteURL = \''. SITE_URL. '\';';
echo 'var ajaxURL = siteURL + \'/ajax\';';
//echo 'var isDevServer = '. (boolean) is_development_server(). ';';
echo 'var isDevServer = '. (is_development_server() ? '1' : '0').  ';';