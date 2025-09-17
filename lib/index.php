<?php
/* NF_APP_BASE should have been defined by the time this page is included.
 * This page just does everything else to initialize the app
 */
define('NF_BASE_URL', '');

/* Bootstrap the lightweight Ning SDK compatibility layer if the legacy SDK is unavailable. */
if (!defined('XN_INCLUDE_PREFIX') || !file_exists(XN_INCLUDE_PREFIX . '/WWF/bot.php')) {
    require_once __DIR__ . '/ning/bootstrap.php';
}

/* Load the base WWF code when present. */
if (defined('XN_INCLUDE_PREFIX') && file_exists(XN_INCLUDE_PREFIX . '/WWF/bot.php')) {
    require XN_INCLUDE_PREFIX . '/WWF/bot.php';
}

/* Define the WWF include prefix appropriately: the parent directory
 * of wherever this file lives. If this file is shared, then it'll
 * be the shared dir (BAZ-2551) */
if (!defined('W_INCLUDE_PREFIX')) {
    define('W_INCLUDE_PREFIX', realpath(dirname(__FILE__) . '/..'));
}

/* Load our custom App class */
W_WidgetApp::includeFileOnce('/lib/XG_App.php');

/* Content and profile caching */
XG_App::includeFileOnce('/lib/XG_Cache.php');

/* Query result caching */
XG_App::includeFileOnce('/lib/XG_Query.php');

/* Don't show boundary comments by default */
NF_Controller::hideBoundaryComments();

/* Dispatch the request */
XG_App::go();
