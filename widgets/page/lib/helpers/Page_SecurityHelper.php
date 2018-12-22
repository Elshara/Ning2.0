<?php

/**
 * Useful functions for authorizing access to pages and other resources.
 */
class Page_SecurityHelper {

    /**
     * Returns whether the current user is allowed to delete the attachment.
     *
     * @param $attachedTo  XN_Content|W_Content  The object to which the attachment is attached
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteAttachments($attachedTo) {
        if (XG_SecurityHelper::userIsAdmin()) { return TRUE; }
        if (XN_Profile::current()->screenName == $attachedTo->contributorName) { return TRUE; }
        if ($attachedTo->type == 'Comment' && XN_Profile::current()->screenName == $attachedTo->my->attachedToAuthor) { return TRUE; }
        return FALSE;
    }

    /**
     * Returns whether the current user is allowed to delete the comment.
     *
     * @param $Comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteComment($comment) {
        if (XG_SecurityHelper::userIsAdmin()) { return TRUE; }
        if (XN_Profile::current()->screenName == $comment->contributorName) { return TRUE; }
        if (XN_Profile::current()->screenName == $comment->my->attachedToAuthor) { return TRUE; }
        return FALSE;
    }

    /**
     * Returns whether the current user is allowed to edit the comment.
     *
     * @param $Comment  XN_Content|W_Content  The Comment object
     * @return boolean  Whether permission is granted
     * added time based element [Phil McCluskey 2007-02-17]
     */
    public static function currentUserCanEditComment($comment) {
        if ((XN_Profile::current()->screenName == $comment->contributorName) && (time() - strtotime($comment->createdDate) < 901)) { return TRUE; }
        return FALSE;
    }

    /**
     * Returns whether the current user is allowed to delete the page.
     *
     * @param $page  XN_Content|W_Content  The page object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeletePage($page) {
        return XG_SecurityHelper::userIsAdminOrContributor(XN_Profile::current(), $page);
    }

    /**
     * Returns whether the current user is allowed to edit the page.
     *
     * @param $page  XN_Content|W_Content  The page object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditPage($page) {
        if (XN_Profile::current()->screenName == $page->contributorName) { return TRUE; }
        return FALSE;
    }
    
    private static function assertIsXnProfile($curUser) {
        if (! ($curUser instanceof XN_Profile)) { throw new Exception('$curUser must be an XN_Profile'); }
    }
    
    
    public static function failed($failureMessage) {
        return $failureMessage != null;
    }
    
    
    public static function checkCurrentUserIsAppOwner($curUser) {
        self::assertIsXnProfile($curUser);
        return self::checkCurrentUserIs($curUser, XN_Application::load()->ownerName);
    }
    
    private static function checkCurrentUserIs($curUser, $screenName) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        }
        if ($curUser->screenName != $screenName) {
            return array('title'       => xg_text('SLOW_DOWN_THERE_CHIEF'),
                         'subtitle'    => '',
                         'description' => xg_text('YOU_NEED_TO_BE_X', Page_FullNameHelper::fullName($screenName)));
        }
        return null;
    }
    
    public static function checkCurrentUserIsSignedIn($curUser) {
        self::assertIsXnProfile($curUser);
        if (!$curUser->isLoggedIn()) {
            return array('title'       => xg_text('HOWDY_STRANGER'),
                         'subtitle'    => xg_text('YOU_NEED_TO_BE_SIGNED_IN'),
                         'description' => xg_text('JUST_CLICK_ON_SIGN_IN'));
        } else {
            return null;
        }
    }
    
    public static function checkCurrentUserContributed($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsSignedIn($curUser))) {
            return self::checkCurrentUserIsSignedIn($curUser);
        } else {
            return self::checkCurrentUserIs($curUser, $content->contributorName);
        }
    }    
    
    /**
     * Returns whether the current user is allowed to create a page.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanCreatePage() {
        if (XG_SecurityHelper::userIsAdmin() || W_Cache::current('W_Widget')->config['anyUserCanCreatePage'] == 1) { return TRUE; }
        return FALSE;
    }
    
    /**
     * Returns whether the current user is allowed to create a comment.
     * with 3.6 individual pages have the option of having comments or not, and this overrides the instance config variable
     *
     * @pararm $page W_Content | XN_Content the page object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanCreateComment($page) {
        if (!XN_Profile::current()->isLoggedIn()) { return FALSE; }
        if ($page->my->allowComments == 'Y') {return TRUE; }
        return FALSE;
    }
    
    /**
     * Returns whether the page instance allows comments.
     *
     * @return boolean  Whether comments can be seen
     */
    public static function usersCanComment() {
        if (W_Cache::current('W_Widget')->config['usersCanComment'] == 1) { return TRUE; }
        return FALSE;
    }
    
    
    public static function checkCurrentUserContributedOrIsAppOwner($curUser, $content) {
        self::assertIsXnProfile($curUser);
        if (self::failed(self::checkCurrentUserIsAppOwner($curUser)) &&
            self::failed(self::checkCurrentUserContributed($curUser, $content))) {
            return self::checkCurrentUserContributed($curUser, $content);
        } else {
            return null;
        }
    }    
    
    
    public static function passed($failureMessage) {
        return ! self::failed($failureMessage);
    }
    
}

