<?php
/**
 * Approves or deletes large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Music_BulkController extends W_Controller {

    protected function _before(){
        $this->_widget->includeFileOnce('/lib/helpers/Music_TrackHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Music_PlaylistHelper.php');
    }

    /**
     * Sets the privacy level of a chunk of objects created by the Music module.
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
        $this->_widget->includeFileOnce('/lib/helpers/Music_BulkHelper.php');
        return Music_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Removes a Track and its comments
     *
     * $_GET['id'] the id of the track to delete
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the contributor of the track
     */
    public function action_remove() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        $track = XN_Content::load($_GET['id']);
        if ((! XG_SecurityHelper::userIsAdmin())&&($this->_user->screenName != $track->contributorName)) { throw new Exception(); }
        $limit = $_GET['limit'];
        $changed = 0;
        //@TODO tracks dont have comments for now
        // if ($changed < $limit) {
        //     $x = Comment::getCommentsFor($_GET['id'], 0, $limit - $changed);
        //     Comment::removeComments($x['comments']);
        //     $changed += count($x['comments']);
        // }
        if ($changed < $limit) {
            Music_TrackHelper::deleteTracks(array($track), $limit - $changed);

            if($_REQUEST['playlistId']) {
                $playlist = Playlist::load($_REQUEST['playlistId']);
                if (XG_SecurityHelper::passed(XG_SecurityHelper::checkCurrentUserContributedOrIsAdmin($this->_user, $playlist))) {
                    Music_PlaylistHelper::updateTrackCount($this->_user, $playlist);
                }
            }
            $changed += 1;
        }
        $this->contentRemaining = $changed >= $limit ? 1 : 0;
    }

    /**
     * Removes Music data created by the specified user. Does not delete the content objects created by the user;
     * assumes that Profiles_BulkController::action_removeByUser will delete those.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function action_removeByUser($limit = null, $user = null) {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (53031671)'); }
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) { xg_echo_and_throw('Not allowed (187808677)'); }
        foreach (User::load($user)->my->attribute(null, true) as $name => $attributeArray) {
            if (mb_strpos($name, 'xg_music_') !== false) { User::load($user)->my->$name = null; }
        }
        User::load($user)->save();
        return array('changed' => 0, 'remaining' => 0);
    }
}