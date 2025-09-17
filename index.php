<?php

define('NF_APP_BASE', __DIR__);

$configPath = NF_APP_BASE . '/config/app.php';
if (!is_file($configPath)) {
    require NF_APP_BASE . '/setup/index.php';
    exit;
}

$config = require $configPath;

if (!is_array($config)) {
    $message = 'The configuration file at "' . $configPath . '" must return an array. '
        . 'Re-run the setup wizard to regenerate a valid configuration.';

    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $message . PHP_EOL);
    } else {
        if (!headers_sent()) {
            header('Content-Type: text/plain; charset=utf-8', true, 500);
        }
        echo $message;
    }

    exit(1);
}

$GLOBALS['nf_app_config'] = $config;

require_once NF_APP_BASE . '/bootstrap.php';

if (!defined('XN_INCLUDE_PREFIX')) {
    define('XN_INCLUDE_PREFIX', NF_APP_BASE);
}

require_once NF_APP_BASE . '/lib/index.php';
