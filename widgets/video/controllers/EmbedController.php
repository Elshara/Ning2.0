<?php

class Video_EmbedController extends W_Controller {
    const VIDEO_COUNT = 3;
    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        Video_HttpHelper::trimGetAndPostValues();
    }
    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }
    public function action_embed3($args) { $this->renderEmbed($args['embed'], 3); }
    private function renderEmbed($embed, $columnCount) {
        $this->columnCount = $columnCount;
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        if ($embed->getType() == 'profiles') {
            $this->videoSetOptions = array(array('label' => xg_text('I_HAVE_RECENTLY_ADDED'), 'value' => 'for_contributor'), array('label' => xg_text('FROM_THE_BAZEL'), 'value' => 'all'));
            $defaultVideoSet = 'for_contributor';
        } else {
            $this->videoSetOptions = array(array('label' => xg_text('FEATURED'), 'value' => 'promoted'), array('label' => xg_text('RECENTLY_ADDED'), 'value' => 'all'), array('label' => xg_text('HIGHEST_RATED'), 'value' => 'rated'));
            $defaultVideoSet = 'all';
        }
        if (is_null($embed->get('displayType'))) { $embed->set('displayType', 'detail'); }
        if (is_null($embed->get('videoSet'))) { $embed->set('videoSet', $defaultVideoSet); }
        if (is_null($embed->get('videoNum'))) { $embed->set('videoNum', self::VIDEO_COUNT); }
        // default in 2.3 was 2, so we bump it to 3 here to avoid having it set to 0 BAZ-6429
        if (in_array($embed->get('videoNum'),array(1,2)) && $this->columnCount == 2) {
            $embed->set('videoNum', self::VIDEO_COUNT);
        }
        if ($this->columnCount == 2) {
            $videoNums = array(12,9,6,3,0);
            $num_options = array(0,3,6,9,12);
            if (!in_array($embed->get('videoNum'),$videoNums)) {
                foreach ($videoNums as $num) {
                    if ($embed->get('videoNum') > $num) {
                        $embed->set('videoNum',$num);
                        break;
                    }
                }
            }
        } else {
            $num_options = array(0,1,2,3,4,5,6,7,8,9,10,11,12);
        }
        $this->num_options = array();
        foreach ($num_options as $opt) {
            $this->num_options[] = array('label' => (string)($opt), 'value' => (string)($opt));
        }
        $this->embed = $embed;
        $videoData = $this->videos($embed);
        $this->videos = $videoData['videos'];
        $this->numVideos = $videoData['numVideos'];
        // BAZ-4889: preload user info
        XG_Cache::profiles($this->videos);
        $previewFrameIds = array();
        foreach ($this->videos as $video) {
            if (mb_strlen($video->my->previewFrame)) {
            $previewFrameIds[] = $video->my->previewFrame;
            }
        }
        if (count($previewFrameIds)) {
            XG_Cache::content($previewFrameIds);
        }
        Video_FullNameHelper::initialize($this->videos);
        if ((! $this->numVideos && $this->embed->getType() == 'profiles') || (! $this->numVideos && ! $this->embed->isOwnedByCurrentUser()) || (!$this->embed->isOwnedByCurrentUser() && $this->embed->get('videoNum') == 0)) { return $this->renderNothing(); }        
        
        $this->title = xg_text('VIDEOS');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        $this->addVideoUrl = (XG_SecurityHelper::currentUserCanSeeAddContentLink($embed, $this->numVideos)
                        ? $this->_buildUrl('video', XG_MediaUploaderHelper::action()) : null);
        //TODO: Wrap this decision in a function in XG_SecurityHelper something like currentUserCanSeeEmbed, and elsewhere, too
        // [Thomas David Baker 2008-03-28]
        if ($embed->getType() == 'profiles') { $this->title = $this->embed->isOwnedByCurrentUser() ? xg_text('MY_VIDEOS') : xg_text('XS_VIDEOS', xg_username(XG_Cache::profiles($this->embed->getOwnerName()))); }
        if ($this->numVideos || $embed->isOwnedByCurrentUser()) {
            $this->render('embed');
        }
    }
    private function videos($embed) {
        $n = $embed->get('videoNum') == 0 && $embed->isOwnedByCurrentUser() ? 1 : $embed->get('videoNum');
        if ($n != 0) {
            if ($embed->get('videoSet') == 'for_contributor') {
                $videoData = Video_VideoHelper::getSortedVideos($this->_user, array('contributor' => $embed->getOwnerName()), Video_VideoHelper::getMostRecentSortingOrder(), 0, $n);
            }
            if ($embed->get('videoSet') == 'all') {
                $videoData = Video_VideoHelper::getSortedVideos($this->_user, null, Video_VideoHelper::getMostRecentSortingOrder(), 0, $n);
            }
            if ($embed->get('videoSet') == 'promoted') {
                $videoData = Video_VideoHelper::getPromotedVideos($n,true);
            }
            if ($embed->get('videoSet') == 'rated') {
                $videoData = Video_VideoHelper::getSortedVideos($this->_user, null, Video_VideoHelper::getHighestRatedSortingOrder(), 0, $n);
            }
            if ($embed->get('videoSet') == 'owner') {
                $filter = array('contributor'=>XN_Application::load()->ownerName);
                $videoData = Video_VideoHelper::getSortedVideos($this->_user, $filter, Video_VideoHelper::getMostRecentSortingOrder(), 0, $n);
            }
        } else {
            $videoData = array('videos'=>array(),'numVideos'=>0);
        }
        return $videoData;
    }
    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('displayType', $_POST['displayType']);
        $embed->set('videoSet', $_POST['videoSet']);
        $embed->set('videoNum', $_POST['videoNum']);
        $videos = $this->videos($embed);
        Video_FullNameHelper::initialize($videos);
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        $addVideoUrl = (XG_SecurityHelper::currentUserCanSeeAddContentLink($embed, $videos['numVideos'])
                        ? $this->_buildUrl('video', XG_MediaUploaderHelper::action()) : null);
        ob_start();
        $this->renderPartial('fragment_moduleBodyAndFooter', array('videos' => $videos['videos'],
                    'columnCount' => $_GET['columnCount'], 'embed' => $embed, 'numVideos' => $videos['numVideos'],
                    'addVideoUrl' => $addVideoUrl));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    public function action_error() {
        $this->render('blank');
    }
}
