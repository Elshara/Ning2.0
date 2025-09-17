<?php
if (!defined('NF_APP_BASE')) {
    define('NF_APP_BASE', dirname(__DIR__, 2));
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/stubs/W_WidgetApp.php';
require_once __DIR__ . '/stubs/W_Widget.php';
require_once __DIR__ . '/stubs/W_Cache.php';
require_once __DIR__ . '/stubs/W_Controller.php';
require_once __DIR__ . '/stubs/XG_App.php';
require_once __DIR__ . '/compat/XN.php';

Ning\SDK\Environment::bootstrap();
