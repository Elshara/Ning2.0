<?php
if (! XN_Profile::current()->isOwner()) { throw new Exception('Not allowed'); }
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);
require_once XN_INCLUDE_PREFIX .'/WWF/bot.php';
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_Query.php');
NF_Controller::invalidateCache(NF::INVALIDATE_ALL);
XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
XN_Cache::remove(XN_Cache::ALL);
if ($_GET['target']) {
    header('Location: ' . $_GET['target']);
    exit;
} ?>
<html>
<head><title>Invalidate Cache</title></head>
<body>
    <span style="color:green">&#10004;</span> W cache invalidated<br />
    <span style="color:green">&#10004;</span> XG cache invalidated<br />
    <span style="color:green">&#10004;</span> XN cache invalidated<br />
</body>
</html>


