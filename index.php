<?php

define('NF_APP_BASE', __DIR__);

$configPath = NF_APP_BASE . '/config/app.php';
if (!is_file($configPath)) {
    require NF_APP_BASE . '/setup/index.php';
    exit;
}

$GLOBALS['nf_app_config'] = require $configPath;

require_once NF_APP_BASE . '/bootstrap.php';

if (!defined('XN_INCLUDE_PREFIX')) {
    define('XN_INCLUDE_PREFIX', NF_APP_BASE);
}

require_once NF_APP_BASE . '/lib/index.php';
