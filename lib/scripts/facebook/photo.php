<?php
define('NF_APP_BASE', $_SERVER['DOCUMENT_ROOT']);
require_once NF_APP_BASE . '/lib/XG_FacebookApp.php';

XG_FacebookApp::run('photo');
