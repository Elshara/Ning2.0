<?php

/**
 * Useful functions for working with bulk operations.
 */
class Page_BulkHelper {

    /**
     * Changes the privacy level of a chunk of objects from the content store.
     * To be used as part of a network's switch from public->private or private->public.
     *
     * @param   $limit integer          Maximum number of items to switch privacy level before returning.
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @return  array                   ['changed'] The number of content objects changed,
     *                                  ['remaining'] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function setPrivacy($limit, $toPrivate) {
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        $changed = XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'page');
        return array('changed' => $changed, 'remaining' => ($changed >= $limit ? 1 : 0));
    }

    /**
     * Removes a page and its Comments, and UploadedFiles attached to
     * the page or Comments.
     *
     * @param $page XN_Content|W_Content  The page to delete
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @return array  [0] The number of content objects deleted (approximate), and [1] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function remove($page, $limit) {
        $changed = 0;
        $x = Comment::getCommentsFor($page->id, 0, $limit - $changed);
        foreach($x['comments'] as $comment) {
            Page_FileHelper::deleteAttachments($comment);
            XN_Content::delete(W_Content::unwrap($comment));
        }
        $changed += count($x['comments']);
        if ($changed < $limit) {
            Page_FileHelper::deleteAttachments($page);
            XN_Content::delete(W_Content::unwrap($page));
            $changed++;
        }
        Page_UserHelper::updateActivityCount(User::load(XN_Profile::current()->screenName))->save();
        return array($changed, $changed >= $limit ? 1 : 0);
    }

    /**
     * Removes Page objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @param $testing boolean  Whether this function is currently being tested. Defaults to FALSE.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function removeByUser($limit, $user, $testing = FALSE) {
        $changed = 0;
        // Even if Comments have already been deleted by the top-level bulk-deletion logic,
        // we must still delete the UploadedFiles that were attached to them [Jon Aquino 2007-01-29]
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('my->mozzle', '=', W_Cache::current('W_Widget')->dir);
        $query->filter('contributorName', '=', $user);
        if ($testing) { $query->filter('my->test', '=', 'Y'); }
        $query->begin($begin);
        $query->end($limit - $changed);
        foreach ($query->execute() as $object) {
            XN_Content::delete($object);
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

}
