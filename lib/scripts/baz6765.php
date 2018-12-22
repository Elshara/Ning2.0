<?php
// So that Job callback URL construction works properly
define('NF_BASE_URL', '/index.php');

/**
 * Re-runs 3.0 release async jobs and sets User fullname searchability - for BAZ-6765
 */
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

/* Helper to run Jobs properly */
XG_App::includeFileOnce('/lib/XG_JobHelper.php');

/* Don't show boundary comments by default */
NF_Controller::hideBoundaryComments();

/* Load the widgets */
$appClass = W_Cache::getClass('app');
call_user_func(array($appClass,'loadWidgets'));


$userShape = XN_Shape::load('User');
if ($userShape) {
    if ((! $userShape->attributes['my.fullName']) ||
        ($userShape->attributes['my.fullName']->indexing != 'text')) {
        $userShape->setAttribute('my.fullName',
                                 array('type' => XN_Attribute::STRING,
                                       'indexing' => 'text'));
        $userShape->save();
    }
 }

try {
    if (BlogArchive::instance()->my->buildStatus == BlogArchive::NOT_STARTED) {
        XG_JobHelper::start('profiles', 'buildBlogArchive');
    }
} catch (Exception $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
  }

try {
    XG_JobHelper::start('photo', 'addAlbumCovers');
} catch (Exception $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
  }
  
try {
    XG_JobHelper::start('groups', 'initializeActivityAttributes');
} catch (Exception $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
  }

print 'Done.';


