<?php
/**
* Primary dispatcher for the Page widget.
*/
class Page_IndexController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
    }

    /**
    * Displays the homepage.
    */
    public function action_index() {
        $this->forwardTo('list', 'page');
    }

    /**
     * Displays the detail page for the specified content object.
     * Used in search results, and other pages that refer to detail pages
     * using /xn/detail/12345 (where 12345 is the content-object ID).
     *
     * @param $content XN_Content  The content object to display
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
            case 'Page' :
                $this->redirectTo('show', 'page', array('id' => $content->id));
                break;
            case 'PageCommenterLink' :
                $this->redirectTo('show', 'page', array('id' => $content->my->pageId));
                break;
            case 'Comment' :
                $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
                header('Location: ' . Page_CommentHelper::url($content));
                break;
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }


}
