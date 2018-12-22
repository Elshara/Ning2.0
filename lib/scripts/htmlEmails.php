<?php
if (! XN_Profile::current()->isOwner()) { throw new Exception('Not allowed'); }

define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);
define('NF_BASE_URL', '');
require_once XN_INCLUDE_PREFIX .'/WWF/bot.php';
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_Query.php');
XG_App::loadWidgets();

$widget = W_Cache::getWidget('main');
if (isset($_REQUEST['update'])) {
	$widget->privateConfig['ignoreForceHtml'] = $_REQUEST['update'] ? '1' : '';
	$widget->saveConfig();
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}
$v = "".$widget->privateConfig['ignoreForceHtml'];
?>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
	<title>HTML email configuration</title>
</head>
<body>
	HTML Emails now are <b><%=$v ? 'DISABLED' : 'ENABLED'%></b>
	<form method="post" action="">
		<input type="hidden" name="update" value="<%=$v ? 0 : 1%>">
		<input class="submit" type="submit" name="submit" value="<%=$v?'Enable':'Disable'%>">
	</form>
</body>
</html>
