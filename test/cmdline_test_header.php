<?php
//require_once W_INCLUDE_PREFIX . '/WWF/lib/global.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/NF.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/NF_Controller.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/NF_JSON.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/W_BaseWidget.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/W_Cache.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/W_Controller.php';
require_once W_INCLUDE_PREFIX . '/WWF/lib/W_WidgetApp.php';
//require_once W_INCLUDE_PREFIX . '/XN/AtomHelper.php';
//require_once W_INCLUDE_PREFIX . '/XN/Cache.php';
//require_once W_INCLUDE_PREFIX . '/XN/Event.php';
//require_once W_INCLUDE_PREFIX . '/XN/Profile.php';
$xndir = W_INCLUDE_PREFIX . '/XN';
if (is_dir($xndir)) {
	if ($dh = opendir($xndir)) {
		// iterate over file list
		while (($filename = readdir($dh)) !== false) {
			if (!preg_match("/^.*\.php$/",$filename)) continue;
			require_once $xndir."/".$filename;
		}
	}
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_ConfigCachingApp.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_LanguageHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_App.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_AbstractMessageCatalog.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_MessageCatalog_en_US.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/XG_TestHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/ExceptionMockDecorator.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/CmdlineTestCase.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/index/controllers/SearchController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/index/lib/helpers/Index_MessageCatalogReader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/index/lib/helpers/Index_MessageCatalogWriter.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/profiles/controllers/BulkController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/profiles/controllers/BulkController.php';
//call_user_func(array(W_Cache::getClass('app'), 'loadWidgets'));

?>