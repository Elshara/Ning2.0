<?php
/**
 * A discussion page.
 */
class Page extends W_Model {

    /**
     * The title of the page. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,200
     */
    public $title;
    const MAX_TITLE_LENGTH = 200;

    /**
     * The body of the page; raw HTML. Optional if created via the Tab Manager.
     *
     * @var XN_Attribute::STRING optional
     */
    public $description;
    const MAX_DESCRIPTION_LENGTH = 1000000;


    /**
     * searchable text; stripped description + title
     *
     * @var XN_Attribute::STRING
     * @feature indexing text
     */
    public $searchable;

    /**
     * Is this page public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * The number of detail views of the page.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $viewCount;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * The page's most popular 10 tags
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $topTags;
    const TOP_TAGS_COUNT = 10;
    
    /**
     * Whether or not the page allows comments
     * 'Y' allows comments; null or 'N' disallows
     *
     * @var XN_Attribute::STRING optional
     */
    public $allowComments;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Construct a new Page.
     *
     * @param $title string  The title of the page
     * @param $description string  The body of the page (HTML scrubbed of scripts and invalid tags)
     * @return W_Content  An unsaved Page
     * @see Page.php for the max title and description lengths
     * @see HTML_Scrubber::scrub()
     */
    public function create($title, $description) {
        $page = W_Content::create('Page');
        $page->title = $title;
        $page->description = $description;
        $page->my->mozzle = W_Cache::current('W_Widget')->dir;
        $page->my->viewCount = 0;
        $page->isPrivate = XG_App::appIsPrivate();
        $page->my->searchable = strip_tags($description) . ' ' . $title;
        return $page;
    }

    /**
     * Increments the view count of this photo.
     */
    public function incrementViewCount() {
        $this->viewCount    = $this->viewCount + 1;
    }

    /**
     * Scrubs, linkifies, and truncates the given description.
     *
     * @param $description string  The Page description
     * @return string  The cleaned up Page description
     */
    public static function cleanDescription($description) {
        return mb_substr(xg_linkify(Page_HtmlHelper::scrub($description ? $description : xg_text('NO_DESCRIPTION'))), 0, self::MAX_DESCRIPTION_LENGTH);
    }

    /**
     * Truncates the given title
     *
     * @param $title string  The Page title
     * @return string  The cleaned up Page title
     */
    public static function cleanTitle($title) {
        return mb_substr($title ? $title : xg_text('UNTITLED'), 0, Page::MAX_TITLE_LENGTH);
    }

    /**
     * For the given array of Page and Comment objects, returns a mapping of page-ID => Page.
     *
     * @param $pagesAndComments array  A mix of Pages and Comments
     * @return array  A mapping of page IDs to Page objects
     */
    public static function pages($pagesAndComments) {
        $pages = array();
        $pageIdsToQuery = array();
        foreach ($pagesAndComments as $pageOrComment) {
            if ($pageOrComment->type == 'Page') {
                $pages[$pageOrComment->id] = $pageOrComment;
            } else {
                $pageIdsToQuery[] = $pageOrComment->my->attachedTo;
            }
        }
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type', 'Page'));
        }
        $query->filter('owner');
        $query->filter('type', '=', 'Page');
        $query->filter('id', 'in', $pageIdsToQuery);
        foreach ($query->execute() as $page) {
            $pages[$page->id] = $page;
        }
        return $pages;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

