<?php
/**
 *  Handles all requests to Notes module.
 *
 *  URL generation and parsing is controlled by lib/Notes_UrlHelper.php
 *
 **/
class Notes_IndexController extends W_Controller {
    const   PAGE_SIZE          = 20;
    const   FEATURED_PAGE_SIZE = 5;
    const   EXCERPT_LENGTH     = 200;

    public $note = NULL;

    public function action_overridePrivacy($action) {
        return ! XG_App::appIsPrivate() && $action == 'feed';
    }

    //
    public function _before() { # void
        $this->canCreate = XG_SecurityHelper::userIsAdmin();
        W_Cache::getWidget('notes')->includeFileOnce('/lib/helpers/Notes_TemplateHelper.php');
    }

//** Show actions
    /**
     *  Shows the content of a note
     *
     *  @param      $noteKey	string		Note to display. Empty key means Notes Home.
     *  @param		$create		bool		If true, we came here from "add note" box, so display "do you want to edit note" box
     */
    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        $key = $_GET['noteKey'];
        if ( mb_strlen($key) > Note::MAX_TITLE_LENGTH ) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $isAdmin = XG_SecurityHelper::userIsAdmin();
        // If note already loaded, do not reload it.
        if ($this->note || $this->note = Note::byKey($key)) {

            $this->canFeature = (bool)$isAdmin;
            $this->canEdit = (bool)$isAdmin;
            $this->canDelete = (bool)$isAdmin && !Note::isMain($key);
            $this->canRead = $this->note->my->visibility != 'A' || $isAdmin;
            $this->showEditBox = (bool) @$_GET['create'];
            $this->isMain = Note::isMain($key);

            $this->title = $key ? $this->note->title : xg_text('NOTES');
            $this->pageTitle = $key ? $this->note->title : xg_text('NOTES_HOME');
            $this->noteContent = $this->canRead ? $this->note->description : xg_html('NOTE_ACCESS_DENIED', xnhtmlentities($this->note->title));

            User::loadMultiple(array($this->note->contributorName, $this->note->my->lastUpdatedBy));
        } else {
            $this->title = Note::title($key);
            $this->noteContent = xg_html('NOTE_NOT_EXISTS',xnhtmlentities($this->title));
            $this->noteTitle = $this->title;
        }
    }

    /**
     *  /xn/detail handler
     */
    public function action_detail($object) {
        if ($object->type != 'Note') {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->note = $object;
        $_GET['noteKey'] = $this->note->my->noteKey;
        return $this->forwardTo('show','index');
    }

    /**
     * 	Displays the list of all notes
     *
     *  @param      $sort	string	alpha|updated|created
     */
    public function action_allNotes($show = 'both') {
        $this->sort = $this->_getSort();
        $this->page = $this->_getPage();
        $this->pageTitle = xg_text('ALL_NOTES');
        $this->onlyFeatured = $show === 'onlyFeatured';
        $this->excerptLength = self::EXCERPT_LENGTH;
        $this->title = xg_text('ALL_NOTES');

        if (count($this->allNotes = Note::getAllNotes(self::PAGE_SIZE, $this->sort))) {
            $this->feedUrl = $this->_buildUrl('index','feed', array('type'=>'all', 'sort' => $this->sort, 'xn_auth' => 'no'));
            $this->hideSort = false;
            $this->render('listNotes');
        } else {
            $this->notFoundMsg = xg_html('THERE_ARE_NO_NOTES');
            $this->showSearch = 0;
            $this->render('emptyList');
        }

        if ($this->page == 1 || $this->onlyFeatured) {
            $pageSize = $this->onlyFeatured ? self::PAGE_SIZE : self::FEATURED_PAGE_SIZE;
            $this->featuredNotes = Note::getFeaturedNotes($pageSize, 'promoted');
            $this->feedUrl = $this->_buildUrl('index','feed', array('type'=>'featured', 'sort' => $this->sort, 'xn_auth' => 'no'));
            $this->featuredNotesHeading = xg_text('FEATURED_NOTES');
            if (!$this->onlyFeatured && (count($this->featuredNotes) == $pageSize)) {
                $this->showViewAllFeaturedUrl = true;
                $this->viewAllFeaturedUrl = $this->_buildUrl('index','featuredNotes');
            }
        }

        $this->pageSize = self::PAGE_SIZE;
    }

    /**
     *  Displays the list of featured notes
     *
     *  @param      $sort   string  alpha|updated|created
     */
    public function action_featuredNotes() {
        $this->forwardTo('allNotes','index',array('onlyFeatured'));
    }

    /**
     * 	Displays a feed of various notes
     *
     */
    public function action_feed() {
        $feedSize = 20;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created';
        switch ($_GET['type']) {
            case 'featured':
                $list = Note::getFeaturedNotes($feedSize, $sort);
                $title = xg_text('FEATURED_NOTES');
                break;
            case 'all':
                $list = Note::getAllNotes($feedSize, $sort);
                $title = xg_text('ALL_NOTES');
                break;
            default:
                $list = Note::getAllNotes($feedSize, 'updated');
                $title = xg_text('MOST_RECENTLY_UPDATED');
        }
        header('Content-Type: application/atom+xml');
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        XG_FeedHelper::cacheFeed(array('id' => 'comment-feed-' . md5(XG_HttpHelper::currentUrl())));
        XG_FeedHelper::outputFeed($list, $title);
    }

    /**
     *  @param      $q	string		Search request
     */
    public function action_search() {
        $this->searchQuery = trim($_REQUEST['q']);
		$this->pageTitle = xg_text('NOTES');
		$this->title = xg_text('SEARCH_RESULTS');
        if ($this->searchQuery && count($this->allNotes = Note::searchNotes($this->searchQuery, self::PAGE_SIZE))) {
            $this->hideSort = true;
            $this->render('listNotes');
        } else {
            $this->notFoundMsg = xg_html('NO_NOTES_FOUND');
            $this->showSearch = 1;
            $this->render('emptyList');
        }
    }

//** CRUD actions
	// handler for the "quick post" feature
    public function action_createQuick() { # void
		$_GET['noteKey'] = $_REQUEST['noteKey'];
		$this->action_update();
		unset($this->content, $this->version);
		if ($this->status == 'ok') {
			$this->message = xg_html('YOUR_NOTE_WAS_ADDED');
			$this->viewUrl = Notes_UrlHelper::noteUrl($_GET['noteKey']);
			$this->viewText = xg_html('VIEW_THIS_NOTE');
		} elseif ($this->status == 'updated') {
			// Overwrite this specific message to match the mocks
			$this->message = xg_html('NOTE_EXISTS2', qh($_GET['noteKey']));
		}
    }

    /**
     *  Shows content editor for a note.
     *
     *  @param      $noteKey	string		Note to edit. Empty key means Notes Home.
	 *  @param		$create		bool		If true, we came here from "add note" block
	 *  @param		$fromQuickPost bool		Request is made from the "quick post"
     */
    public function action_edit () {
        XG_SecurityHelper::redirectIfNotAdmin();

        if ($_REQUEST['fromQuickPost']) {
			$this->noteKey = $_REQUEST['noteKey'];
			$this->activeTab = 1; // activate the Source tab
			$defaultContent = "".$_REQUEST['content'];
		} else {
			$this->noteKey = $_GET['noteKey'];
			$this->activeTab = 0;
			$defaultContent = '';

		}
        if ( mb_strlen($this->noteKey) > Note::MAX_TITLE_LENGTH ) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->isMain = Note::isMain($this->noteKey);

		// If we came from the quick post, pretend that note doesn't exist
        if ( !$_REQUEST['fromQuickPost'] && ($this->note = Note::byKey($this->noteKey)) ) {
            if ($_GET['create'] || ! XG_SecurityHelper::userIsAdmin()) {
                return $this->forwardTo('show');
            }
            $this->title = $this->note->title;
            $this->noteContent = "".$this->note->description;
            $this->noteVersion = $this->note->my->version;
            $this->noteVisibility = $this->note->my->visibility;
        } else {
            $this->title = Note::isMain($this->noteKey) ? xg_text('NOTES_HOME') : Note::title($this->noteKey);
			$this->noteContent = $defaultContent;
            $this->noteVersion = 0;
            $this->noteVisibility = 'E';
        }
    }

    /**
     *  Removes note
     *
     *  @param      $noteKey	string 		Note to delete. Empty key is disallowed
     */
    public function action_delete() {
        if ( XG_SecurityHelper::userIsAdmin() && !Note::isMain($key = $_GET['noteKey']) ) {
            Note::delete($key);
        }
        return $this->redirectTo(Notes_UrlHelper::noteUrl(''));
    }

    /**
     * 	Updates note content.
     *
     *  @param      $noteKey	string	Note to update. Empty key means Notes Home.
     *  @param		$version 	int
     *  @param		$content 	string
     *  @param		$title		string	Note title (only for Notes Home)
     *
     *  @return		hash:
     *  	$message			string	Explaining message
     * 		$status				string	Status
     * 			ok						Note saved successfully
     * 				$version	string	Assigned version
     * 			fail					Note cannot be saved (temporary error)
     * 			updated					Note has been already updated
     * 				$version			Current version
     * 				$content			Current content
     * 			deleted
     */
    public function action_update() {
        $this->status = 'fail';
        if (!isset($_GET['noteKey']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->message = xg_html('BAD_REQUEST');
            return;
        }
        if (mb_strlen($_GET['noteKey']) > Note::MAX_TITLE_LENGTH ) {
            $this->message = xg_html('NOTE_TITLE_TOO_LONG');
            return;
        }
        $key = $_GET['noteKey'];
        if ($note = Note::byKey($key)) {
            if (! XG_SecurityHelper::userIsAdmin()) {
                $this->message = xg_html('YOU_MUST_BE_ADMIN');
                return;
            }
        } else if (! XG_SecurityHelper::userIsAdmin()) {
            $this->message = xg_html('YOU_MUST_BE_ADMIN');
            return;
        }
        $title = Note::isMain($key) ? "".$_POST['title'] : NULL;
        W_Cache::getWidget('notes')->includeFileOnce('/lib/helpers/Notes_Scrubber.php');
		list($status, $note) = Note::update($key, $this->_user->screenName, $_POST['version'], Notes_Scrubber::scrub($_POST['content']), 'E', $title, $_POST['featureOnMain']);
        switch($status) {
            case 'ok':
                $this->status = 'ok';
                $this->version = $note->my->version;
                $this->message = xg_html('NOTE_HAS_BEEN_SAVED', xnhtmlentities(xg_date(xg_text('M_J_Y_G_IA'))), xnhtmlentities($this->version));
                break;
            case 'updated':
                $this->status = 'updated';
                $updatedBy = User::load($note->my->lastUpdatedBy);
                $this->content = $note->description;
                $this->version = $note->my->version;
                $this->message = xg_html('NOTE_HAS_BEEN_UPDATED',
                    xnhtmlentities(xg_username($updatedBy)),
                    xnhtmlentities(xg_date(xg_text('M_J_Y_G_IA'),$note->updatedDate)),
                    xnhtmlentities(xg_elapsed_time($note->updatedDate)));
                break;
            case 'deleted':
                $this->status = 'deleted';
                $this->message = xg_html('NOTE_HAS_BEEN_DELETED', xnhtmlentities(Note::title($key)));
                break;
            case 'device_busy':
            default:
                $this->status = 'failed';
                $this->message = xg_html('NOTE_LOCKED');
                break;
        }
    }

    /**
     * 	Sets featured notes
     *
     *  @param      $noteKey	string		Note to update. Empty key means Notes Home.
     *	@param		$featured 	bool		Set/reset fatured flag for the note
     */
    public function action_setFeatured() {
        if (XG_SecurityHelper::userIsAdmin()) {
            Note::setFeatured($_GET['noteKey'], $_REQUEST['featured'] ? 1 : 0);
        }
        $this->redirectTo(Notes_UrlHelper::noteUrl($_GET['noteKey']));
    }
//** Helpers
    protected function _getSort() { # string
		return $_GET['sort'] ? $_GET['sort'] : 'updated';
    }

    protected function _getPage() { # string
        return $_GET['page'] ? $_GET['page'] : 1;
    }
}
?>
