<?php
require_once dirname(__DIR__) . '/../bootstrap.php';

define('NF_BASE_URL', '/index.php');
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);
//define('W_INCLUDE_PREFIX', dirname(__FILE__));
require XN_INCLUDE_PREFIX . '/WWF/bot.php';
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_Query.php');
XG_App::includeFileOnce('/lib/XG_LangHelper.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_PagingList.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
call_user_func(array(W_Cache::getClass('app'), 'loadWidgets'));
EventWidget::init();
$PAGE_SIZE = 100;

$i = $page = 0;
do {
	$list = XN_Query::create('Content')
		->filter('owner')
		->filter('type', '=', 'Event')
		->order('id')
		->begin($page*$PAGE_SIZE)
		->end(($page+1)*$PAGE_SIZE)->execute();
	foreach ($list as $o) {
		if ($o->my->eventType[0] == '|') {
			$types = array_map('urldecode',explode('|',trim($o->my->eventType,'|')));
			$o->my->eventType = Events_EventHelper::listToType($types);
			$o->save();
			$i++;
		}
	}
	$page++;
} while($list);
echo "Processed $i event(s).\n";

$i = $page = 0;
do {
	$list = XN_Query::create('Content')
		->filter('owner')
		->filter('type', '=', 'EventAttendee')
		->order('id')
		->begin($page*$PAGE_SIZE)
		->end(($page+1)*$PAGE_SIZE)->execute();
	foreach ($list as $o) {
		if ($o->my->eventType[0] == '|') {
			$types = array_map('urldecode',explode('|',trim($o->my->eventType,'|')));
			$o->my->eventType = Events_EventHelper::listToType($types);
			$o->save();
			$i++;
		}
	}
	$page++;
} while($list);
echo "Processed $i event attendees(s).\n";
echo "Done.";
?>
