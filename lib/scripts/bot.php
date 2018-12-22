<?php
/* The bot, for now, is just the profile widget's publish-future-posts bot */
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/profiles/lib/bots/Profiles_PublishInFutureBot.php';


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

// see if the app has a domain-mapped url
$applicationInfo = XG_App::getDirectoryProfile();
$preferredDomain = XN_Application::load()->relativeUrl . '.ning.com';
if (is_array($applicationInfo['domains']) && count($applicationInfo['domains']) > 0) {
  $preferredDomain = $applicationInfo['domains'][0];
}

$_SERVER['HTTP_HOST'] = $preferredDomain;


XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
XG_EmbeddableHelper::generateResourcesPeriodically();

