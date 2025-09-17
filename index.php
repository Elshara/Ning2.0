<?php

define('NF_APP_BASE', __DIR__);

$autoloadPath = NF_APP_BASE . '/vendor/autoload.php';
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

require_once NF_APP_BASE . '/lib/index.php';
