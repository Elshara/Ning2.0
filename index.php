<?php

define('NF_APP_BASE', __DIR__);

$autoloadPath = NF_APP_BASE . '/vendor/autoload.php';
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

$configPath = NF_APP_BASE . '/config/app.php';
if (!is_file($configPath)) {
    require NF_APP_BASE . '/setup/index.php';
    exit;
}

$GLOBALS['nf_app_config'] = require $configPath;

require_once NF_APP_BASE . '/lib/index.php';
