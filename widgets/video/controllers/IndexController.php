<?php

class Video_IndexController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Video_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_ContentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_TrackingHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Video_HtmlHelper.php');
        Video_PrivacyHelper::checkMembership();
        Video_TrackingHelper::insertHeader();
    }

    public function action_index() {
         $this->forwardTo('index', 'video');
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
        $query = Video_VideoHelper::query($this->_user, 1, 1, null, false);
        $query->filter('my->approved', '=', 'N');
        $query->execute();
        $this->numVideos = $query->getTotalCount();
    }

    /**
     * The SearchController in the main widget is interested to know what this 
     * widget has to say about app-wide search queries. @see BAZ-3821
     * 
     * @param $query XN_Query The query object to modify
     */
    public function action_annotateSearchQuery($query) {
        /* Exclude Video with my.conversionStatus == 'in progress' or 'failed' */
        $query->filter(XN_Filter::any(XN_Filter('type','!like','Video'),
                                      XN_Filter::all(XN_Filter('type','like','Video'),
                                                     XN_Filter('my.conversionStatus','!like','in progress'),
                                                     XN_Filter('my.conversionStatus','!like','failed'))));
    }
    
    public function action_acceptEmailContent() {
        return $this->_widget->dispatch('video', 'uploadByMail');
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
            case 'Video' :
                $this->redirectTo('show', 'video', array('id' => $content->id));
                break;
            case 'VideoAttachment' :
            case 'VideoPreviewFrame' :
                $this->redirectTo('show', 'video', array('id' => $content->my->video));
                break;
            case 'Comment' :
                $this->redirectTo('show', 'video', array('id' => $content->my->attachedTo));
                break;
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }

    /**
     * @see Video_Controller's addContent action
     */
    public function action_addContent() {
        $this->forwardTo('addContent', 'video');
    }

}