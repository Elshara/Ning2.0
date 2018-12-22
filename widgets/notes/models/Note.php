<?php
/**
 * A note.
 *
 *  @cache_lock     Note-md5($key):lock          wraps code that modifies a Note object
 */
class Note extends W_Model {
    /**
     * The title of the note.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,255
     * @feature indexing text
     */
    public $title;
    const MAX_TITLE_LENGTH = 255;

    /**
     * The body of the note; HTML (100Kb limit)
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,102400
     * @feature indexing text
     */
    public $description;
    const MAX_LENGTH = 102400; // 100k

    /**
     * Is this note public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * Specially transformed note title. Used for uniquely locating a note.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,255
     */
    public $noteKey;

    /**
     * The number of note chnages. Updated everytime when note changes.
     *
     * @var XN_Attribute::NUMBER
     */
    public $version;

    /**
     * The screen name of person who updated this note last time.
     * @var XN_Attribute::STRING
     */
    public $lastUpdatedBy;

    /**
     * Whether this note is viewable/editable by "administrators" or "everyone". A=administrators, E=everyone, H=author only
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $visibility;
    public $visibility_choices = array('A', 'E', 'H');

    /**
     * "Y" indicates that this note should be excluded from Ningbar and widget
     * searches. This is true of notes in private groups.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
    const MAIN = 'Notes_Home';
    protected static $isInInitProcess = 0;

    /**
     *  Returns the list of all notes
     *
     *  @param      $limit	int		Page size
     *  @param		$sort	string	alpha|updated|created
     *  @param		$noPage	bool	Disable current page autodetection.
     *  @return     XG_PagingList<Note>
     */
    public static function getAllNotes($limit, $sort, $noPage = false) {
        return self::_getList(XN_Query::create('Content'), $limit, $sort, $noPage);
    }

    /**
     *  Returns the list of all featured notes
     *
     *  @param      $limit	int		Page size
     *  @param		$sort	string	alpha|updated|created|promoted
     *  @param		$noPage	bool	Disable current page autodetection.
     *  @return     XG_PagingList<Note>
     */
	public static function getFeaturedNotes($limit, $sort, $noPage = false) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_PagingList.php');
        if (! XG_PromotionHelper::areQueriesEnabled()) { return new XG_PagingList($limit); }
        return self::_getList(XG_PromotionHelper::addPromotedFilterToQuery( XN_Query::create('Content') ), $limit, $sort, $noPage);
    }

    /**
     *  Returns the list of all notes created by the user
     *
     *	@param		$screenName string
     *  @param      $limit	int		Page size
     *  @param		$sort	string	alpha|updated|created
     *  @param		$noPage	bool	Disable current page autodetection.
     *  @return     XG_PagingList<Note>
     *
    public static function getAllUserNotes($screenName, $limit, $sort, $noPage = false) {
        return self::_getList(XN_Query::create('Content')->filter('contributorName', '=', $screenName), $limit, $sort, $noPage);
    }*/

    /**
     *  Search notes
     *
     *  @param      $search     string      Search terms
     *  @param      $limit		int		Page size
     *  @return     XG_PagingList<Note>
     */
    public static function searchNotes($search, $limit) {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_App::includeFileOnce('/lib/XG_PagingList.php');
        $list   = new XG_PagingList($limit);
        $query  = NULL;

        if (XG_QueryHelper::getSearchMethod() == 'search') {
            try {
                $query = $list->processQuery(XN_Query::create('Search')->filter('type', 'like', 'Note'));
                XG_QueryHelper::addSearchFilter($query, $search, true);
                XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
                if (!XG_SecurityHelper::userIsAdmin()) {
                    $query->filter('my->visibility','like','E');
                }
                $list->setResult(XG_QueryHelper::contentFromSearchResults($query->execute(), false),$query->getTotalCount());
            } catch (Exception $e) {
                // do nothing
            }
        }

        if (!$query) {
            $query = $list->processQuery(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Note'));
            XG_QueryHelper::addSearchFilter($query, $search);
            XG_QueryHelper::addExcludeFromPublicSearchFilter($query);
            if (!XG_SecurityHelper::userIsAdmin()) {
                $query->filter('my->visibility','like','E');
            }
            $list->setResult($query->execute(), $query->getTotalCount());
        }

        self::_preloadUsers($list);
        return $list;
    }

    /**
     *  Checks whether key is pointing to "Notes Home"
     *
     *  @param      $key	string
     *  @return     bool
     */
    public static function isMain($key) {
        return self::key($key) == self::MAIN;
    }

    /**
     *  Returns note by its key (specially transformed title)
     *
     *  @param      $key string
     *  @return     W_Content | NULL
     */
    public static function byKey($key) {
        $key = self::key($key);
        $note = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Note')->filter('my->noteKey', 'eic', $key)->uniqueResult();
        if (!$note && $key == self::MAIN && !self::$isInInitProcess) {
            // if something went wrong and Home wasn't created, create it now.
            $note = self::onAfterAdd('notes');
        }
        return $note;
    }

    /**
     *  Returns note key (for passing via URLs) by note title.
     *
     *  @param      $title string
     *  @return     string
     */
    public static function key($title) {
        $key = preg_replace('/\s/u','_',trim($title));
        return $key == '' ? self::MAIN : $key;
    }

    /**
     *  Returns note title by note key
     *
     *  @param      $key string
     *  @return     string
     */
    public static function title($key) {
        return str_replace('_',' ',trim($key));
    }
    /**
     *  Updates/creates note. Returns a list:
     *  	status			ok|updated|deleted|device_busy (cannot lock)
     *  	note			Loaded object or NULL
     *
     *  @param      $key		string			Note key
     *  @param		$screenName	string			Author
     *  @param		$version 	int 			Current version (at the moment when author loaded the note content)
     *  @param		$content 	string			Note content
     *  @param		$visibility	string 			Visibility flag
     *  @param		$title		string|NULL     New note title (NULL - do not update title)
     *  @param		$feature	bool			Feature new note
     *  @return     list<status,note>
     */
    public static function update($key, $screenName, $version, $content, $visibility, $title = NULL, $feature = false) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');

        $key = self::key($key);
        if (mb_strlen($key) > self::MAX_TITLE_LENGTH) {
            throw new Exception("Note title is too long (4435643672)");
        }

        $lockKey = "Note-".md5($key).":lock";
        if (!XG_LockHelper::lock($lockKey)) {
            return array('device_busy',NULL);
        }
        $note = self::byKey($key);
        if ($note) { // note exists
            if ($note->my->version != $version) { // but version musmatch
                XG_LockHelper::unlock($lockKey);
                return array('updated', $note);
            }
            // everything is ok
            $change = 0;
        } else { // note don't exist
            if ($version != 0) { // but version isn't 0 - assume that note was deleted
                XG_LockHelper::unlock($lockKey);
                return array('deleted', NULL);
            }
            // init note
            $note = W_Content::create('Note');
            $note->title = self::title($key);
            $note->isPrivate = XG_App::appIsPrivate();
            $note->my->mozzle = W_Cache::current('W_Widget')->dir;
            $note->my->noteKey = $key;
            $change = 1;
        }
        $content = substr(trim($content),0,self::MAX_LENGTH); // not mb_substr
        if (NULL !== $title && $note->title != $title) { $note->title = $title; $change = 1; }
        if ($note->description != $content) { $note->description = $content; $change = 1; }
        if ($note->my->visibility != $visibility) { $note->my->visibility = $visibility; $change = 1; }
        if ($note->my->lastUpdatedBy != $screenName) {$note->my->lastUpdatedBy = $screenName; $change = 1; }

        if ($feature) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            XG_PromotionHelper::promote($note);
            XG_Query::invalidateCache('has-featured-notes');
            $change = 1;
        }

        if ($change) {
            $note->my->version = $version+1;
            $note->save();
        }
        XG_LockHelper::unlock($lockKey);
        return array('ok',$note);
    }

    /**
     *  Deletes note
     *
     *  @param      $key	string	Note key
     *  @return     void
     */
    public static function delete($key) {
        if ($note = self::byKey($key)) {
            XN_Content::delete($note);
        }
    }

    /**
     *  Updates featured flag of the note (note must exists)
     *
     *  @param      $key		string	Note key
     *  @param		$featured	bool	Set/reset featured flag
     *  @return     bool
     */
    public static function setFeatured($key, $featured) {
        // TODO: Use the standard network featuring mechanism [Jon Aquino 2008-04-28]
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');

        $key = self::key($key);
        $lockKey = "Note-".md5($key).":lock";
        if (!XG_LockHelper::lock($lockKey)) {
            return false;
        }
        if (!$note = self::byKey($key)) {
            XG_LockHelper::unlock($lockKey);
            return false;
        }
        if ($featured) {
            XG_PromotionHelper::promote($note);
            self::_swapVisibilityForActivityLog($note);
            XG_PromotionHelper::addActivityLogItem(XG_ActivityHelper::SUBCATEGORY_NOTES, $note);
            self::_restoreVisibilityAfterActivityLog($note);
        } else {
            XG_PromotionHelper::remove($note);
        }
        $note->save();
        XG_LockHelper::unlock($lockKey);
        XG_Query::invalidateCache('has-featured-notes');
        return true;
    }

    /**
     * @internal these declarations are used in setFeatured() method above until
     *           it is refactored (see @todo from Jon Aquino) - they should not be used further...
     */
    private static $_originalVisibility = array();
    private static function _swapVisibilityForActivityLog($note) {
        self::$_originalVisibility[$note->id] = $note->my->visibility;
        switch($note->my->visibility) {
            case 'A' :
                $note->my->visibility = 'me';
                break;
            case 'E' :
                $note->my->visibility = 'all';
                break;
            case 'H' :
                $note->my->visibility = 'me';
                break;
        }
    }

    private function _restoreVisibilityAfterActivityLog($note) {
        if (!isset(self::$_originalVisibility[$note->id])) {
            return;
        }
        $note->my->visibility = self::$_originalVisibility[$note->id];
    }


    /**
    *  Returns a boolean indicating whether there are any featured notes
    *
    *  @return     bool
    */
    public static function hasFeaturedNotes() {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled()) { return 0; }
        $query = XG_Query::create('Content')
                ->filter('owner')
                ->filter('type','=','Note')
                ->begin(0)
                ->end(1)
                ->setCaching('has-featured-notes');
        $query = XG_PromotionHelper::addPromotedFilterToQuery($query);
        $results = $query->execute();
        return count($results);
    }


    /**
     *  Updates note title (note must exists). Note version is not updated. Returns new note title
     *
     *  @param      $key		string	Note key
     *  @param		$title		string	New title
     *  @return     string|NULL
     */
    public static function setTitle($key, $title) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');

        $key = self::key($key);
        $lockKey = "Note-".md5($key).":lock";
        if (!XG_LockHelper::lock($lockKey)) {
            return NULL;
        }
        if (!$note = self::byKey($key)) {
            XG_LockHelper::unlock($lockKey);
            return NULL;
        }
        $note->title = mb_substr($title, 0, self::MAX_TITLE_LENGTH);
        $note->save();
        XG_LockHelper::unlock($lockKey);
        return $note->title;
    }

//** Event handlers
    /**
     *  Called when Note is added as a network feature
     *
     *  @param      $widgetName	string	Added feature. All except "notes" must be skipped.
     *  @return     Note
     */
    public static function onAfterAdd($widgetName) {
        if ($widgetName != 'notes') {
            return;
        }
        self::$isInInitProcess = 1;
        // it's safe because if page already exists, nothing happens
        list($status,$note) = Note::update('',XN_Application::load()->ownerName, 0, xg_html('NOTES_HOME_DEFAULT', 'href="' . Notes_UrlHelper::url('allNotes') . '"'),'E',xg_text('NOTES_HOME'));
        self::$isInInitProcess = 0;
        return $note;
    }

//** Implementation
    /**
     *  Processes sorting, paging and preload users.
     */
    protected static function _getList ($query, $limit, $sort, $noPage) { # XG_PagingList
        $query->filter('owner')->filter('type', '=', 'Note');
        if (!XG_SecurityHelper::userIsAdmin()) {
            $query->filter('my->visibility','=','E');
        }
        switch ($sort) {
            case 'created':	$query->order('createdDate','desc'); break;
            case 'updated': $query->order('updatedDate','desc'); break;
			case 'promoted': $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE); break;
            case 'alpha':
            default:		$query->order('title','asc'); break;
        }

        XG_App::includeFileOnce('/lib/XG_PagingList.php');
        $list = XG_PagingList::create($limit, $query, $noPage);
        self::_preloadUsers($list);
        return $list;
    }

    /**
     *  Preload users.
     */
    protected static function _preloadUsers($list) { # void
        $users = array();
        foreach ($list as $note) {
            $users[$note->contributorName] = 1;
            $users[$note->my->lastUpdatedBy] = 1;
        }
        User::loadMultiple(array_keys($users));
    }
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
XN_Event::listen('feature/add/after', 'Note::onAfterAdd');
?>
