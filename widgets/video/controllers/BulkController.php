<?php
/**
 * Approves or deletes large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Video_BulkController extends W_Controller {

    protected function _before(){
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_MessagingHelper.php');
	XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
    }

    /**
     * Sets the privacy level of a chunk of objects created by the Videos module.
     *
     * @param   $limit integer          Maximum number of content objects to change (approximate).
     * @param   $privacyLevel  string   Privacy level to swtich to: 'private' or 'public'.
     * @return  array                   'changed' => the number of content objects deleted,
     *                                  'remaining' => 1 or 0 depending on whether or not there are content objects remaining to set privacy of.
     */
    public function action_setPrivacy($limit = null, $privacyLevel = null) {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        if ($privacyLevel !== 'public' && $privacyLevel !== 'private') { throw new Exception("privacyLevel must be 'public' or 'private'"); }
        $this->_widget->includeFileOnce('/lib/helpers/Video_BulkHelper.php');
        return Video_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Removes Videos and Comments by the specified user.
     *
     * $_GET['onlyVideos'] whether to remove the person's videos only, or also their comments on other videos
     * @param $limit integer maximum number of content objects to remove (approximate). Can also be specified with $_GET['limit'].
     * @param $user string username of the person whose content to remove. Can also be specified with $_GET['user'].
     * @return array 'changed' => the number of content objects deleted,
     *     'remaining' => a positive number if there are content objects that remain to be deleted; otherwise, zero.
     *     The latter is also stored in $this->contentRemaining.
     * @throws Exception if the current user is not the site owner
     */
    public function action_removeByUser($limit = null, $user = null) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        $limit = $limit ? $limit : $_GET['limit'];
        $user = $user ? $user : $_GET['user'];
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) {
            throw new Exception("Permission denied.");
        }
        $changed = 0;
        if ($changed < $limit && ! $_GET['onlyVideos']) {
            $x = Comment::getCommentsBy($user, 0, $limit - $changed, null, 'createdDate','asc', array('my->attachedToType' => 'Video'));
            Comment::removeComments($x['comments']);
            $changed += count($x['comments']);
        }
        if ($changed < $limit) {
            $x = Comment::getCommentsForContentBy($user, 0, $limit - $changed, null, 'createdDate','asc', array('my->attachedToType' => 'Video'));
            Comment::removeComments($x['comments']);
            $changed += count($x['comments']);
        }
        if ($changed < $limit) {
            $x = Video_VideoHelper::getSortedVideos($this->_user, array('contributor' => $user), null, 0, $limit - $changed);
            $changed += Video_VideoHelper::deleteVideos($x['videos'], $limit - $changed);
        }

        // Invalidate the approval-link cache -- the user may have had unapproved videos
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));

        $this->contentRemaining = $changed >= $limit ? 1 : 0;
        return array('changed' => $changed, 'remaining' => $this->contentRemaining);
    }

    /**
     * Removes a Video and its comments
     *
     * $_GET['id'] the id of the video to delete
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the contributor of the video
     */
    public function action_remove() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        $video = XN_Content::load($_GET['id']);
        if (! XG_SecurityHelper::userIsAdminOrContributor($this->_user, $video)) { throw new Exception(); }
        $limit = $_GET['limit'];
        $changed = 0;
        if ($changed < $limit) {
            $x = Comment::getCommentsFor($_GET['id'], 0, $limit - $changed);
            Comment::removeComments($x['comments']);
            $changed += count($x['comments']);
        }
        if ($changed < $limit) {
            Video_VideoHelper::deleteVideos(array($video), $limit - $changed);
            $changed += 1;
        }
        $this->contentRemaining = $changed >= $limit ? 1 : 0;
    }

    /**
     * Removes Videos that have not yet been approved.
     *
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * $_GET['user'] username of the person whose unmoderated videos to remove, or null to remove all unmoderated videos.
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the site owner
     */
    public function action_removeUnapprovedVideos() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        if (! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        $limit = $_GET['limit'];
        $user = $_GET['user'];
        $query = Video_VideoHelper::query($this->_user, null, $limit, null, false);
        $query->filter('my->approved', '=', 'N');
        if ($user) { $query->filter('contributorName', '=', $user); }
        $videos = $query->execute();
        // Notify the user (BAZ-1614)
        foreach ($videos as $video) {
            Video_MessagingHelper::videoRejected($video);
        }
        $changed = Video_VideoHelper::deleteVideos($videos, $limit);
        $this->contentRemaining = $changed >= $limit ? 1 : 0;
    }

    /**
     * Approves Videos by the specified user.
     *
     * @param $limit integer maximum number of content objects to remove (approximate). Can also be specified with $_GET['limit'].
     * @param $user string username of the person whose content to remove. Can also be specified with $_GET['user'].
     * @return array 'changed' => the number of content objects approved,
     *     'remaining' => a positive number if there are content objects that remain to be approved for the user; otherwise, zero.
     *     The latter is also stored in $this->contentRemaining.
     * @throws Exception if the current user is not the site owner
     */
    public function action_approveByUser($limit = null, $user = null) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        if (! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        $limit = $limit ? $limit : $_GET['limit'];
        $user = $user ? $user : $_GET['user'];
        $query = Video_VideoHelper::query($this->_user, null, $limit, null, false);
        $query->filter('my->approved', '=', 'N');
        if ($user) { $query->filter('contributorName', '=', $user); }
        $videos = $query->execute();
        $videoIds = '';
        foreach ($videos as $video) { $videoIds .= $video->id.','; }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        if($user){
            $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_VIDEO, $user, $videos);
        } else {
            foreach ($videos as $video) {
                $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_VIDEO, $video->contributorName, array($video));
            }
        }

        $contributors = array();
        foreach ($videos as $video) {
            Video_VideoHelper::setApproved($video, 'Y');
            $video->my->newContentLogItem = $logItem->id;
            $video->save();
            $contributors[$video->contributorName] = 1;
            // Notify the user (BAZ-1614)
            Video_MessagingHelper::videoApproved($video);
        }
		if ($contributors) {
        	$users = User::loadMultiple($contributors);
        	foreach($contributors as $n) {
        		Video_VideoHelper::updateVideoCount($users[$n], TRUE);
			}
		}
        $this->contentRemaining = count($videos) >= $limit ? 1 : 0;
        return array('changed' => count($videos), 'remaining' => $this->contentRemaining);
    }

    /**
     * Approves all Videos waiting to be moderated.
     *
     * @param $limit integer maximum number of content objects to remove (approximate). Can also be specified with $_GET['limit'].
     * @return array 'changed' => the number of content objects approved,
     *     'remaining' => a positive number if there are content objects that remain to be approved; otherwise, zero.
     *     The latter is also stored in $this->contentRemaining.
     * @throws Exception if the current user is not the site owner
     */
    public function action_approveAll($limit = null) {
        return $this->action_approveByUser($limit, null);
    }

}

