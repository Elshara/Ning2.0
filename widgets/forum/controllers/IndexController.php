<?php
/**
* Primary dispatcher for the Forum widget.
*/
class Forum_IndexController extends XG_GroupEnabledController {

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        return ! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate() && $_GET['feed'] == 'yes' && $action == 'index';
    }

    /**
     * Displays the main forum page; either a category listing, discussion listing, or discussions by category.
     *
     * Expected GET variables:
     *     feed - "yes" to output a feed (optional)
     */
    public function action_index() {
        if ($_GET['feed']) {
            $this->forwardTo('list', 'topic');
        }
        if (!XG_GroupHelper::inGroupContext() && (W_Cache::getWidget('forum')->config['forumMainStyle'] == 'categories' ||
                is_null(W_Cache::getWidget('forum')->config['forumMainStyle']) && count(Category::titlesAndIds()) > 1)) {
            $this->forwardTo('listByTitle', 'category');
        } elseif (!XG_GroupHelper::inGroupContext() && W_Cache::getWidget('forum')->config['forumMainStyle'] == 'latestByCategory') {
            $this->forwardTo('list', 'category');
        } else {
            $this->forwardTo('list', 'topic');
        }
    }
	public function action_index_iphone() {
		$this->action_index();
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
        $_GET['groupId'] = $content->my->groupId;  // Used by XG_GroupEnabledController::redirectTo [Jon Aquino 2007-04-16]
        switch ($content->type) {
            case 'Topic' :
                $this->redirectTo('show', 'topic', array('id' => $content->id));
                break;
            case 'TopicCommenterLink' :
                $this->redirectTo('show', 'topic', array('id' => $content->my->topicId));
                break;
            case 'Comment' :
                $this->_widget->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
                header('Location: ' . Forum_CommentHelper::urlProper($content));
                break;
            case 'Category' :
                $this->redirectTo('listForCategory', 'topic', array('categoryId' => $content->id));
                break;
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }

}