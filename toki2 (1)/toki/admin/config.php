<?php
// HTTP
define('HTTP_SERVER', 'http://localhost:8088/admin/');
define('HTTP_CATALOG', 'http://localhost:8088/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost:8088/admin/');
define('HTTPS_CATALOG', 'http://localhost:8088/');

// DIR
define('DIR_APPLICATION', __DIR__ . '/');
define('DIR_SYSTEM', dirname(__DIR__) . '/system/');
define('DIR_IMAGE', dirname(__DIR__) . '/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_CATALOG', dirname(__DIR__) . '/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'sdsworks_u1');
define('DB_PASSWORD', '!@#kianfebj2020!@');
define('DB_DATABASE', 'sdsworks_toki');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
