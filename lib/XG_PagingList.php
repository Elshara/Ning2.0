<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  SYNOPSIS:
 *
 * 		action:
 * 			$list = XG_PagingList::create($pageSize, $query);
 *
 * 		template:
 * 			foreach ($list as $item) {			# Iterator magic
 *				 ...
 *			 }
 *
 *			 # or, less efficient
 *			 for ($i = 0; $i<count($list);$i++) {  # ArrayAccess magic
 *				 echo $list[$i];
 *			 }
 *
 *	   pagination:
 *			 echo "Total is ",$list->totalCount, " displayed from ",$list->begin, " to ",$list->end;
 *			 for($i = 0;$i<cou$list->pageCount;$i++)
 *				echo '<a href="'.$list->pageUrl($i).'">',$i,'</a>';
 *
 *			 # or
 *			 if (!$list->isFirstPage())
 *				echo '<a href="'.$list->prevPageUrl().'"></a>';
 *			 if (!$list->isLastPage())
 *				echo '<a href="'.$list->nextPageUrl().'"></a>';
 *
 *  DESCRIPTION:
 *
 *	  List with the pagination support.
 *	  WARNING: All protected properties (except starting from "_") are available outside the class as read-only props.
 *
 *	  You can also create empty XG_PagingList if you omit all params to c-tor. It can be useful if you want to make your code cleaner.
 *
 **/

class XG_PagingList implements IteratorAggregate, Countable, ArrayAccess {
    /**
     * 	Total number of items in the list (w/o pagination). For the number of items in the list use count($this).
     *  @var int
     */
    protected $totalCount;

    /**
     *	Index of first object in the page
     *  @var int
     */
    protected $begin;

    /**
     *	Index of first object in the page
     *  @var int
     */
    protected $end;

    /**
     * 	The current page (0-based)
     *  @var int
     */
    protected $page;

    /**
     * 	The Number of pages
     *  @var int
     */
    protected $pageCount;

    /**
     *  @var int
     */
    protected $pageSize;

    /**
     * 	Name of the GET param for passing the current page.
     *  @var string
     */
    protected $pageParam;

    protected $_url, $_extra, $_list = array();

    /**
     *  Creates and initializes XG_PagingList. It's the preferred way to create simple XG_PagingLists
     *
     *  @param      $pageSize   int
     *  @param		$query		XN_Query|XG_Query		Query
     *  @return     XG_PagingList
     */
    public static function create($pageSize, $query, $noPage = false) {
        $list = new XG_PagingList($pageSize, $noPage ? '' : NULL);
        $list->processQuery($query);
        $list->setResult($query->execute(), $query->getTotalCount());
        return $list;
    }

    /**
     *  Constructor.
     *
     *  @param	$pageSize	int						Pass 0 to disable paging (all objects are fetched)
     *  @param	$pageParam	string					Name of the GET param (default is 'page').
     *  											Pass empty string (not NULL or false!) to disable the page autodetection.
     *
     *  											Page can be a number (positive or negative) or a string.
     *  											Formats other than positive numbers must be supported via app-specific logic:
     *  												$list = new XG_PagingList($pageSize,$param);
     *  												if ($list->page < 0) { ... }
     *  												if (preg_match('/some-regex/u',$list->page) { ... }
     *  											In this case you can use XG_PagingList as a simple container.
     */
    public function  __construct($pageSize = 0, $pageParam = NULL) {
        $this->pageSize 	= max(0, intval($pageSize));
        $this->pageParam	= $pageParam;
        if ($this->pageParam === '') {
            $this->page		= 0;
        } else {
            $this->pageParam= $this->pageParam === NULL ? 'page' : $this->pageParam;
            $this->page		= "".$_REQUEST[$this->pageParam];
            // TODO: Set the _url's page parameter using XG_HttpHelper::addParameter [Jon Aquino 2008-04-08]
            $this->_url		= preg_replace("/([?&])$this->pageParam=[^&]*(&|$)/u",'$1',XG_HttpHelper::currentUrl());
            $this->_url		= preg_replace('/[?&]$/u','',$this->_url);
            $this->_url		.= (false === mb_strpos($this->_url,'?')) ? '?' : '&';
        }
        if (is_numeric($this->page) && $this->page > 0) {
            $this->page--;
        }
        $this->begin 		= 0;
        $this->end 			= 0;
        $this->totalCount	= 0;
        $this->pageCount	= 0;

    }

    /**
     *  Processes XN_Query. Adds begin/end/totalCount conditions.
     *
     *  @param      $query   Query		XN_Query|XG_Query
     *  @return     qeury
     */
    public function processQuery($query) { # query
        // TO DO optimize by passing total count via _GET (if page!=0).
        if ($this->pageSize && $this->page >= 0) {
            $begin = $this->pageSize*intval($this->page);
            $query->alwaysReturnTotalCount(true);
            $query->begin($begin)->end($begin+$this->pageSize);
        }
        return $query;
    }

    /**
     *  Sets XG_PagingList content. Extra options are:
     *  	prevPage	scalar		Previous page (can be non-numeric). If not specified, usual numeric logic is used.
     *  							Pass empty value (but not NULL) if you don't want prev page.
     *  	nextPage	scalar      Next page (can be non-numeric). If not specified, usual numeric logic is used
     *  							Pass empty value (but not NULL) if you don't want next page.
     *
     *  @param      $list   array		List of items
     *  @param		$total	int			Total number of items in the list
     *  @param		$extra	hash		Extra options (for custom pagination).
     *  @return     void
     */
    public function setResult(array $list, $total, array $extra = array()) {
        $this->_list		= $list;
        $this->totalCount	= $total;
        $this->begin		= $this->pageSize * intval($this->page);
        $this->end			= $this->begin + count($this->_list) - 1;
        $this->pageCount	= $this->pageSize ? ceil($this->totalCount/$this->pageSize) : 1;
        $this->_extra		= $extra;
    }

    /**
     *  Returns the extra options, for custom pagination.
     *
     *  @return hash    extra options, e.g., prevPage and nextPage
     */
    public function getExtraOptions() {
        return $this->_extra;
    }

//** Accessors
    /**
     *  Checkes whether it's the first page
     *
     *  @return     bool
     */
    public function isFirstPage() { # bool
        return  isset($this->_extra['prevPage']) ? !(bool)$this->_extra['prevPage'] : (!is_numeric($this->page) || $this->page <= 0);
    }

    /**
     *  Checkes whether it's the last page
     *
     *  @return     bool
     */
    public function isLastPage() { # bool
        return  isset($this->_extra['nextPage']) ? !(bool)$this->_extra['nextPage'] : ($this->page >= $this->pageCount-1);
    }

    /**
     *  Returns URL for the previous page
     *
     *  @return     bool
     */
    public function prevPageUrl() { # string
        return $this->pageUrl(isset($this->_extra['prevPage']) ? $this->_extra['prevPage'] : $this->page, true);
    }

    /**
     *  Returns URL for the next page
     *
     *  @return     bool
     */
    public function nextPageUrl() { # string
        return $this->pageUrl(isset($this->_extra['nextPage']) ? $this->_extra['nextPage'] : $this->page+2, true);
    }

    /**
     *  Returns URL for a specific page
     *
     *  @return     bool
     */
    public function pageUrl($page, $asIs = false) { # string
        if (!$asIs && is_numeric($page) && $page >= 0) {
            $page++;
        }
        return $this->_url . $this->pageParam . '=' . urlencode($page);
    }
    /**
     *  Returns the underlying array.
     *  The preferred way is to use just foreach() around the XG_PagingList instance:
     *  	foreach($pagingList as $item) { ... }
     *  but this method can be used where array is required and iterator doesn't fit.
     *
     *  @return     array
     */
    public function getList() {
        return $this->_list;
    }

//** Implementation
    public function __get($name) { # scalar
        if ($name[0] == '_'  || !isset($this->$name)) {
            throw new Exception("Reading of undefined property `$name'");
        }
        return $this->$name; // give read-only access to protected props
    }

//** Interfaces
    public function count() { # int
        return count($this->_list);
    }
    public function getIterator() { # Iterator
        return new ArrayIterator($this->_list);
    }
    public function offsetExists($offset) { # bool
        return array_key_exists($offset, $this->_list);
    }
    public function offsetGet($offset) { # void
        return $this->_list[$offset];
    }
    public function offsetSet($offset, $value) { # void
        throw new Exception("not implemented");
    }
    public function offsetUnset($offset) { # void
        throw new Exception("not implemented");
    }
}
?>
