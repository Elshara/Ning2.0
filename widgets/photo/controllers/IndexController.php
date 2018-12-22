<?php

class Photo_IndexController extends XG_BrowserAwareController {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        Photo_PrivacyHelper::checkMembership();
    }

    public function action_index() {
         $this->forwardTo('index', 'photo');
    }

    public function action_acceptEmailContent() {
        return $this->_widget->dispatch('photo', 'uploadByMail');
    }

    public function action_blank() {
    }

    public function action_error() {
    }

    public function action_approvalLink() {
        if (! XG_SecurityHelper::userIsAdmin()) { return null; }
        if (! XG_App::contentIsModerated()) { return null; }
        // Caching Approach: approval-link
        $this->setCaching(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
        $photosData = Photo_PhotoHelper::getSortedPhotos($this->_user, array('forApproval' => true), null, 0, 1);
        $this->numPhotos = $photosData['numPhotos'];
    }

    /**
     * The SearchController in the main widget is interested to know what this
     * widget has to say about app-wide search queries. @see BAZ-3821
     *
     * @param $query XN_Query The query object to modify
     */
    public function action_annotateSearchQuery($query) {
        /* Exclude Albums that have my.hidden = Y */
        $query->filter(XN_Filter::any(XN_Filter('type','!like','Album'),
                                      XN_Filter::all(XN_Filter('type','like','Album'),
                                                     XN_Filter('my.hidden','!like','Y'))));
    }

    /**
     * Displays the detail page for the specified content object.
     * External and internal resources may refer to /xn/detail/FOO
     * as a URL for a detail view of the content object with ID FOO.
     *
     * @param $content XN_Content the content object to display
     * @see "Bazel Code Structure: /xn/detail URL Handling"
     */
    public function action_detail($content = null) {
        // If content is supplied because this action is dispatched from the main
        // /xn/detail handler, then use that. Otherwise, redirect to the homepage.
        if (is_null($content)) {
            header("Location: http://{$_SERVER['HTTP_HOST']}/");
            exit();
        }
        switch ($content->type) {
            case 'Photo' :
                $this->redirectTo('show', 'photo', array('id' => $content->id));
                return;
            case 'Album' :
                $this->redirectTo('show', 'album', array('id' => $content->id));
                return;
            case 'Comment' :
                XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
                if ($content->my->attachedToType == 'Album') { return $this->redirectTo(XG_CommentHelper::url($content, 'commentPage')); }
                if ($content->my->attachedToType == 'Photo') { return $this->redirectTo(XG_CommentHelper::url($content)); }
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }

     /**
     * @see Photo_Controller's addContent action
     */
    public function action_addContent() {
        $this->forwardTo('addContent', 'photo');
    }
}
