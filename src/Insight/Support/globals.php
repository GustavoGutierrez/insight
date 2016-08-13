<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__FILE__) . DS);

define('BASE_PLUGIN_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DS);
define('DIRECTORY_APP_NAME', 'app');
define('DIRECTORY_APP', BASE_PLUGIN_PATH . DIRECTORY_APP_NAME . DS);
define('DIRECTORY_APP_PLUGINS', BASE_PLUGIN_PATH . DIRECTORY_APP_NAME . DS . 'Plugins' . DS);
define('DIRECTORY_APP_CONFIG', DIRECTORY_APP . 'Config' . DS);
define('DIRECTORY_APP_VIEWS', DIRECTORY_APP . 'Views' . DS);
define('DIRECTORY_APP_STORAGE', BASE_PLUGIN_PATH . 'storage' . DS);
define('DIRECTORY_APP_CACHE', DIRECTORY_APP_STORAGE . 'cache' . DS);
define('DIRECTORY_APP_CACHE_VIEWS', DIRECTORY_APP_STORAGE . 'views');
