<?php
class Activity_LogController extends XG_BrowserAwareController {

    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
    }

    public function action_overridePrivacy($action) {
        return ($action == 'list' && $_GET['fmt'] == 'rss');
    }

    public function action_remove() {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');

        if($_REQUEST['cancelUrl'])  $this->cancelUrl    = urldecode($_REQUEST['cancelUrl']);
        if($_REQUEST['isProfile'])  $this->isProfile    = urldecode($_REQUEST['isProfile']);
        $contentIds = explode(',',$_REQUEST['idList']);
        $logData    = XG_ActivityHelper::getUserActivityLog(null, 0, 100, $contentIds);
        if (count($logData ['items']) <= 0) {  throw new Exception('User '.$this->_user->screenName.' tried to access delete page for contents: '.$_REQUEST['idList'].' (9990909840486344)'); }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->cancelUrl) {
                 $success       = false;
                 foreach($logData ['items'] as $content) {
                     $members = explode(',', $content->my->members);
                     if (in_array($this->_user->screenName, $members)||XG_SecurityHelper::userIsAdmin() ){
                         XN_Content::delete($content);
                         $success = true;
                     } else {
                         error_log('User '.$this->_user->screenName.' tried to delete content: '.$content->id.' (15409628259620867)');
                     }
                 }
                 if ($success) {
                 	XG_ActivityHelper::invalidateCache();
				 }
                 if (isset($_REQUEST['xn_out']) && (in_array($_REQUEST['xn_out'], array('json', 'htmljson')))) {
                      echo $success;
                      exit();
                 } else {
                     header("Location: ".$this->cancelUrl);
                     exit();
                }
            }
        } else {
            $this->logItems = XG_ActivityHelper::mergeSimilar($logData ['items']);
        }
    }

    public function action_list() {
        if ($_GET['fmt'] == 'rss') {
            header('Content-Type: application/rss+xml');
            $this->setCaching(array('activity-log-list-' . md5(XG_HttpHelper::currentUrl())), 1800);
            if ($_GET['test_caching']) { var_dump('Not cached'); }
        }
        if ($_GET['fmt'] == 'json') {
            header('Content-Type: text/json');
        }
        $logItems       = $logData ['items'];
        $begin          = (!$_GET['begin']) ? 0 : $_GET['begin'];
        $end            = (!$_GET['end'])   ? 20: $_GET['end'];
        $app            = XN_Application::load();
        $appName        = $app->name;
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');

        if (mb_strlen($_GET['screenName']) > 0 ) {
            $logData            = XG_ActivityHelper::getUserActivityLog($_GET['screenName'], $begin, $end);
            $logItems           = $logData ['items'];
            $this->rssTitle        = xg_text('XS_LATEST_ACTIVITY_ON_APPNAME',XG_FullNameHelper::fullName($_GET['screenName']),$appName);
            $this->description  = '';
            $this->link         = xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . '/profile/'. User::profileAddress($_GET['screenName']));
            $this->feedImageUrl = XG_UserHelper::getThumbnailUrl(XG_Cache::profiles($_GET['screenName']),50, 50);
            $this->feedImageHeight = 50;
        } else {
            $logData            = XG_ActivityHelper::getUserActivityLog(null, $begin, $end);
            $logItems           = $logData ['items'];
            $this->rssTitle        = xg_text('LATEST_ACTIVITY_ON_APPNAME',$appName);
            $this->description  = '';
            $this->link         = xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] );
            if (preg_match('/custom_image/u', $this->_widget->config['headerLayout'])) {
                $headerImageUrl = $this->_widget->config['headerImageUrl'];
                $headerImageHeight = $this->_widget->config['headerImageHeight'];
                if ($this->_widget->config['scaleHeaderImageIfNecessary'] == 'Y' && $headerImageHeight > $this->_widget->config['scaledHeaderImageHeight']) {
                    $headerImageUrl = '/images/theme/custom-scaled-header-image-' . $this->_widget->config['updatedOn'] . '.png';
                    $headerImageHeight = $this->_widget->config['scaledHeaderImageHeight'];
                }
                $this->feedImageUrl = 'http://' . $_SERVER['HTTP_HOST'].$headerImageUrl;
                $this->feedImageHeight = $headerImageHeight;
            } else {
                $this->feedImageUrl = XN_Application::load()->iconUrl(50, 50);
                $this->feedImageHeight = 50;
            }

        }
        $this->logItems = $logItems;
        if ($_GET['fmt'] == 'rss') { return $this->render('rss'); }
        else if ($_GET['fmt'] == 'json') { return $this->render('json'); }
    }

	/*
	 * Displays a list of recent activity feed items (paginated, iPhone-specific)
	 *
     * Expected GET variables:
     *     page - page number (optional)
	 */
	public function action_list_iphone() {
		XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
		$this->pageSize = 20;
		$pageNum = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
		$offset = ($pageNum - 1) * $this->pageSize;
		// We have some activity log item types that we don't support for iphone, so we just skip them and fetch more items to
		// increase our chances to show 20 items. [Andrey 2008-09-12]
		$logData = XG_ActivityHelper::getUserActivityLog(null, $offset, intval($offset + $this->pageSize * 1.5));
        $this->logItems = $logData['items'];
        $this->showNextLink = (count($this->logItems) > $this->pageSize);
        array_pop($this->logItems);
	}
}
?>