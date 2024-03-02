<?php
$url = explode('/', dirname(dirname(__DIR__)));
$url = end($url);
define('DS',DIRECTORY_SEPARATOR);
define('BASE', DS . $url . DS);

// errors disabled
error_reporting(0);

// define('BASE', '/product-manager-php/');

define('UNSET_URI_COUNT', 1);
define('DEBUG_URI', false);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'tt333');
define('DB_NAME', 'produtos');