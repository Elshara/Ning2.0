<?php
// Define the base directory in this app
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);

/* Load the base WWF code */
require_once XN_INCLUDE_PREFIX . '/WWF/bot.php';

/* Load our custom App class */
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
/* Content and profile caching */
XG_App::includeFileOnce('/lib/XG_Cache.php');
/* Query result caching */
XG_App::includeFileOnce('/lib/XG_Query.php');

/* Don't show boundary comments by default */
NF_Controller::hideBoundaryComments();

/* Load the widgets */
$appClass = W_Cache::getClass('app');
call_user_func(array($appClass,'loadWidgets'));

/* Dispatch the request */
/* @todo: It would be nice if the mozzle name wasn't hardcoded here */
$widget = W_Cache::getWidget('profiles');
/* Publishing 25 posts max */
$widget->dispatch('blog','publishInFuture', array(25, true));
