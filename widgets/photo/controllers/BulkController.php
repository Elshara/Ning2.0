<?php

class Photo_BulkController extends W_Controller {

    protected function _before(){
        $this->_widget->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_LogHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_CacheHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_MessagingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
    }

    /**
     * Sets the privacy level of a chunk of objects created by the Photo module.
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
        $this->_widget->includeFileOnce('/lib/helpers/Photo_BulkHelper.php');
        return Photo_BulkHelper::setPrivacy($limit, ($privacyLevel === 'private'));
    }

    /**
     * Remove all content by the specified user. Arguments start as null
     * so we can make sure this action is only called by dispatch from
     * another action.
     *
     * @param $limit integer maximum number of content objects to remove
     * @param $user string user whose content to remove
     * @return array Information about content deleted and content remaining
     */
    public function action_removeByUser($limit = null, $user = null) {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            header("HTTP/1.0 403 Forbidden");
            die();
        }
        if (is_null($limit) && isset($_GET['limit'])) { $limit = $_GET['limit']; }
        if (is_null($user)  && isset($_GET['user' ])) { $user  = $_GET['user' ]; }
        if (is_null($limit) || is_null($user)) {
            throw new Exception("This action cannot be called directly.");
        }
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (! XG_SecurityHelper::currentUserCanDeleteUser($user)) {
            throw new Exception("Permission denied.");
        }
        $changed = 0;
        $remaining = 0;

        if(! isset($_GET['onlyphotos'])){
            // Next, remove comments of this user on other people's photos
            if ($changed < $limit) {
                $commentsData = Comment::getCommentsBy($user, 0, $limit - $changed, null, 'createdDate','asc',array('my->attachedToType' => 'Photo'));
                $commentsRemoved = 0;
                foreach ($commentsData['comments'] as $comment) {
                    Comment::remove($comment, true);
                    $changed++;
                    $commentsRemoved++;
                }
                $remaining += ($commentsData['numComments'] - $commentsRemoved);
            }

            //@TODO remove/uncompute ratings of the user on other people's photos

            // Next, this user's albums
            if ($changed < $limit) {
                $filters = array('owner' => $user, 'includeHidden' => true);
                $albumData = Photo_AlbumHelper::getAlbums($filters, 0,  $limit - $changed);
                $albumsRemoved = 0;
                foreach ($albumData['albums'] as $album) {
                    Photo_AlbumHelper::delete($album);
                    $albumsRemoved++;
                }
                $changed += $albumsRemoved;
                $remaining += ($albumData['numAlbums'] - $albumsRemoved);
            }

        }

        // Next, comments on this user's photos
        if ($changed < $limit) {
            $commentsOnPhotos = Comment::getCommentsForContentBy($user, 0, $limit - $changed, null, 'createdDate','asc',array('my->attachedToType' => 'Photo'));
            $commentsRemoved = Comment::removeComments($commentsOnPhotos['comments']);
            $changed += $commentsRemoved['changed'];
            $remaining += $commentsRemoved['remaining'];
            // No need for cache invalidation here, since we're going to remove these
            // posts in a jiffy
        }

        // Next, this user's photos
        if ($changed < $limit) {
            $photoData = Photo_PhotoHelper::getSortedPhotos($this->_user, array('contributor' => $user), null, 0, $limit - $changed, false);
            $photosRemoved = 0;
            foreach ($photoData['photos'] as $photo) {
                Photo_PhotoHelper::delete($photo);
                $photosRemoved++;
            }
            $changed += $photosRemoved;
            $remaining += ($photoData['numPhotos'] - $photosRemoved);
        } else {
            //assure that it will pass throug all checks
            $remaining++;
        }

        // The user may have had unapproved photos, so clear the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        $this->contentChanged = $changed;
        $this->contentRemaining = $remaining;
        return array('changed' => $changed, 'remaining' => $remaining);
    }

    /**
     * Removes Photos that have not yet been approved.
     *
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * $_GET['user'] username of the person whose unmoderated photos to remove, or null to remove all unmoderated photos.
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the site owner
     */
    public function action_removeUnapprovedPhotos() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        if (! XG_SecurityHelper::userIsAdmin()) { throw new Exception(); }
        $limit = $_GET['limit'];
        $user = $_GET['user'];
        $filters = array('forApproval' => true);
        if ($user) { $filters['contributor'] = $user; }
        $photoData = Photo_PhotoHelper::getSortedPhotos($this->_user, $filters, null, 0, $limit);
        $photos = $photoData['photos'];
        $invalidationkeys = array();
        // Notify the user (BAZ-1614)
        foreach ($photos as $photo) {
            Photo_MessagingHelper::photoRejected($photo);
        }
        Photo_PhotoHelper::deletePhotos($photos,$limit);
        $this->contentRemaining = count($photos) >= $limit ? 1 : 0;
        // Invalidate the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
    }


    /**
     * Removes a Photo and its comments
     *
     * $_GET['id'] the id of the photo to delete
     * $_GET['limit'] maximum number of content objects to remove (approximate).
     * @return null. $this->contentRemaining will be set to a positive number
     *         if there are content objects that remain to be deleted; otherwise, zero.
     * @throws Exception if the current user is not the contributor of the photo
     */
    public function action_remove() {
        XG_SecurityHelper::redirectIfNotMember();
        XG_JoinPromptHelper::joinGroupOnDelete();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception(); }
        $photo = XN_Content::load($_GET['id']);
        if (! XG_SecurityHelper::userIsAdminOrContributor(XN_Profile::load(), $photo)) { throw new Exception(); }
        $limit = $_GET['limit'];
        $changed = 0;
        if ($changed < $limit) {
            $x = Comment::getCommentsFor($_GET['id'], 0, $limit - $changed);
            Comment::removeComments($x['comments']);
            $changed += count($x['comments']);
        }
        if ($changed < $limit) {
            Photo_PhotoHelper::deletePhotos(array($photo), $limit - $changed);
            $changed += 1;
        }
        $this->contentRemaining = $changed >= $limit ? 1 : 0;
    }



    /**
     * Approves Photos by the specified user.  If no user is specified, all pending
     * Photos will be approved.
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
        $filters = array('forApproval' => true);
        if ($user) { $filters['contributor'] = $user; }
        $photoData = Photo_PhotoHelper::getSortedPhotos($this->_user, $filters, null, 0, $limit);
        $photos = $photoData['photos'];
        $invalidationkeys = array();
        foreach ($photos as $photo) {
            W_Content::load($photo)->setApproved('Y');
            $photo->save();
            // Notify the user (BAZ-1614)
            Photo_MessagingHelper::photoApproved($photo);
        }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        if($user){
            Photo_UserHelper::addPhotos(User::load($user))->save();
            $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, $user, $photos);
        } else {
            $photosByContributor = Photo_PhotoHelper::groupPhotosByContributor($photos);
            // do this in batches by user instead of for each photo
            // also do Photo_UserHelper::addPhotos here
            foreach ($photosByContributor as $contributorName => $contributorPhotos) {
                Photo_UserHelper::addPhotos(User::load($contributorName))->save();
                $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_PHOTO, $contributorName, $contributorPhotos);
            }
        }
        $this->contentRemaining = count($photos) >= $limit ? 1 : 0;
        return array('changed' => count($photos), 'remaining' => $this->contentRemaining);
        // Invalidate the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
    }

    /**
     * Approves all Photos waiting to be moderated.
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

