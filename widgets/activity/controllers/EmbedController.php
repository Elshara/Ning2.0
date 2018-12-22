<?php
/**
 * Dispatches requests pertaining to page modules, also known as "embeds".
 * These <div> elements typically have the "xg_embed" CSS class.
 */
class Activity_EmbedController extends W_Controller {

    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
    }

    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }
    private function renderEmbed($embed, $columnCount) {
        if ($embed->getType() == 'homepage') {
            $this->rssTitle        = xg_text('LATEST_ACTIVITY_ON_APPNAME',$appName);
        } else {
            XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
            $this->rssTitle        = xg_text('XS_LATEST_ACTIVITY_ON_APPNAME',XG_FullNameHelper::fullName($embed->getOwnerName()),$appName);
            $embedOwnerUser     = User::load($embed->getOwnerName());
            $this->activity_off_user    = (($embedOwnerUser->my->activityNewContent=='N')&&($embedOwnerUser->my->activityNewComment=='N')&&($embedOwnerUser->my->activityProfileUpdate=='N'));
        }
        $num_options = array(0,4,8,12,16,20);
        $this->num_options = array();
        foreach ($num_options as $opt) {
            $this->num_options[] = array('label' => (string)($opt), 'value' => (string)($opt));
        }
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $defaultItemNum = 8;
        if ($embed->get('activityNum') == null) {
            $embed->set('activityNum', $defaultItemNum);
        }

        $this->embed        = $embed;
        $this->columnCount  = $columnCount;
        ob_start();
        W_Cache::getWidget('activity')->dispatch('embed', 'moduleBodyAndFooterHtml', array('embed' => $embed, 'columnCount' => $columnCount));;
        $moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();
        $this->moduleBodyAndFooterHtml = $moduleBodyAndFooterHtml;
        if (trim($this->moduleBodyAndFooterHtml)) {
            $this->render('embed');
        }
    }

    private function getItems($embed, $begin=0, $end=8, $isOwnedByCurrentUser=false) {
        if ($embed->getType() == 'profiles') {
            $logData        = XG_ActivityHelper::getUserActivityLog($embed->getOwnerName(), $begin, $end, null, null, 'desc', false, $isOwnedByCurrentUser);
            $logItems       = $logData ['items'];
        } else {
            $logData        = XG_ActivityHelper::getUserActivityLog(null, $begin, $end);
            $logItems       = $logData ['items'];
        }
        $contentIds = array();
        $usernames  = array();
        foreach($logItems as $itemkey => $item){
            $itemContents   = explode(',',$item->my->contents);
            //remove empty contents
            foreach($itemContents as $key=>$content){
                if(!$content){ unset($itemContents[$key]); }
            }
            array_splice($itemContents, 4);
            $itemUsernames  = explode(',',$item->my->members);
            //HACK safe measure to not display other users activities on your profile BAZ-4454 while the definitive fix is not in place
                if (($embed->getType() == 'profiles')&&($item->my->category != XG_ActivityHelper::CATEGORY_NETWORK)) {
                    $userIsInvolved = false;
                    foreach($itemUsernames as $itemUsername){
                        if($itemUsername == $embed->getOwnerName()){
                            $userIsInvolved = true;
                            continue;
                        }
                    }
                    if(!$userIsInvolved){
                        unset($logItems[$itemkey]);
                        continue;
                    }
                }
            $contentIds     = array_merge($contentIds, $itemContents);
            $usernames      = array_merge($usernames, $itemUsernames);
        }
        $contentIds = array_unique($contentIds);
        $usernames = array_unique($usernames);
        //load the necessary profiles on cache
               $users = array();
               foreach (array_chunk($usernames, 50) as $chunk) {
                       foreach (XG_Cache::profiles($chunk) as $k=>$v) {
                               $users[$k] = $v;
                       }
               }
        //load the necessary contents on cache
        $contents = XG_Cache::content($contentIds);
        $logItems = XG_ActivityHelper::mergeSimilar($logItems);
        return $logItems;
    }

    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('activityNum', $_POST['activityNum']);
        ob_start();
        W_Cache::getWidget('activity')->dispatch('embed', 'moduleBodyAndFooterHtml', array('embed' => $embed, 'columnCount' => $_GET['columnCount']));
        $moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();
        $this->moduleBodyAndFooterHtml = $moduleBodyAndFooterHtml;

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    public function action_moduleBodyAndFooterHtml($embed, $columnCount) {
        if ($embed->getType() == 'homepage') {
      // $this->setCaching(array($embed->get('activityNum') . '-item-' .$columnCount . '-column-homepage-activity-embed', XG_Cache::key('type', 'ActivityLogItem')));
        } else {
            $embedOwnerUser = User::load($embed->getOwnerName());
            $this->activity_off_user    = (($embedOwnerUser->my->activityNewContent=='N')&&($embedOwnerUser->my->activityNewComment=='N')&&($embedOwnerUser->my->activityProfileUpdate=='N'));
            $this->embedOwnerUser       = $embedOwnerUser;
        }
        $this->activity_off_network = ((!XG_App::logNewContent())&&(!XG_App::logNewComments())&&(!XG_App::logNewMembers())&&(!XG_App::logProfileUpdates()));
        $this->logItems = array();
        if( (!$this->activity_off) && ($embed->get('activityNum')>0) ) {
            $isOwnedByCurrentUser = $embed->isOwnedByCurrentUser();
            $this->logItems = self::getItems($embed, 0, $embed->get('activityNum'),$isOwnedByCurrentUser);
        }
        $activityNum = $embed->get('activityNum');
        $embed->set('activityItemsCount', count($this->logItems));
        $this->embed = $embed;
        // TODO: Rename fragment_moduleBodyAndFooter.php to moduleBodyAndFooterHtml.php
        // then remove this line [Jon Aquino 2007-09-04]
        $this->render('fragment_moduleBodyAndFooter');
    }
}