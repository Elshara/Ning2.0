<?php
XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class Index_SearchController extends W_Controller {

    /** Content types to exclude from search results. See BAZ-492. */
    protected static $typesToExclude = array('Job', 'BlogArchive','Invitation','UploadedFile','TopicCommenterLink','ProfileCustomizationImage', 'ActivityLogItem',
            'PageLayout', 'VideoPreviewFrame','VideoAttachment','VideoPlayerImage', 'WatermarkImage', 'SlideshowPlayerImage', 'ContactList',
            'GroupInvitation', 'GroupMembership', 'GroupIcon', 'GroupInvitationRequest', 'InvitationRequest', 'PlayerCustomizationImage', 'OpenSocialAppData', 'OpenSocialApp', 'FriendRequestMessage');

    /**
     * This is a very basic in-app search that does content search similar to
     * how the ningbar-app search works. It will be revised in the future.
     * (See BAZ-541, BAZ-489)
     */
    public function action_search() {
        // How to refer to content types in plain English for our initial
        // search page.
        $this->contentTypeNameMap = array('BlogPost' => xg_text('BLOG_POST'));
        $this->pageSize = 10;
        $this->term = self::getTerm();
        $this->page = max(0, isset($_GET['page']) ? ((integer) $_GET['page']) : 1);
        $this->content = array();
        if (mb_strlen($this->term)) {
            if (XG_QueryHelper::getSearchMethod() == 'content') {
                list($this->content, $this->contentCount) = $this->searchWithContentQuery();
            } else {
                list($this->content, $this->contentCount) = $this->searchWithSearchQuery();
            }

            $this->groups = Group::groupsForObjects($this->content);
            $this->numPages = ceil($this->contentCount / $this->pageSize);

            // Load the profiles for all the content creators represented in $this->content
            $this->profiles = array();
            foreach ($this->content as $content) {
                $this->profiles[$content->contributorName] = $content->contributorName;
            }
            $this->profiles = XG_Cache::profiles($this->profiles);
            XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
            $this->friendStatuses = $this->_user->isLoggedIn() ? XG_ContactHelper::getFriendStatusFor($this->_user->screenName, $this->profiles) : array();

            // Get commented on objects' titles (BAZ-2150)
            $commentsByType = array();
            foreach ($this->content as $content) {
                if ($content->type == 'Comment' && $content->my->attachedToType) {
                    $type = $content->my->attachedToType;
                    if ($type == 'User') {
                        //  Chatter - handled differently later
                        continue;
                    }
                    if (is_array($commentsByType[$type])) {
                        $commentsByType[$type][] = $content;
                    }
                    else {
                        $commentsByType[$type] = array($content);
                    }
                }
            }

            //   To avoid monster queries we need to limit the number of types
            //   for which we retrieve objects by ID in one query
            $commentsByType = array_values($commentsByType);
            $commentedOn = array();
            $numTypes = count($commentsByType);
            $numTypesQueried = 0;
            $typeQueryLimit = 5;
            while ($numTypesQueried < $numTypes) {
                $ids = array();
                $i = 0;
                while ($i++ < $typeQueryLimit && $numTypesQueried < $numTypes) {
                    foreach ($commentsByType[$numTypesQueried] as $comment) {
                        $ids[] = $comment->my->attachedTo;
                    }
                    $numTypesQueried++;
                }
                foreach (XG_Cache::content($ids) as $content) {
                    $commentedOn[$content->id] = $content;
                }
            }

            //  Now, for each comment, add a title if available
            foreach ($this->content as $idx => $content) {
                if ($content->type == 'Comment'
                        && isset($commentedOn[$content->my->attachedTo])) {
                    $parent = $commentedOn[$content->my->attachedTo];
                    if (mb_strlen($parent->title) > 0) {
                        $this->content[$idx]->my->attachedToTitle = $parent->title;
                    }
                }
            }

            $this->pageTitle = xg_text('SEARCH_RESULTS') . ' - ' . $this->term;
        } else {
            $this->pageTitle = xg_text('SEARCH_RESULTS');
        }
    }

    protected static function getTerm() {
        if (isset($_GET['q']) && mb_strlen($q = trim($_GET['q']))) {
            return $q;
        }
        if (isset($_POST['q']) && mb_strlen($q = trim($_POST['q']))) {
            return $q;
        }
        return '';
    }

    /**
     * Search for matching content objects using the standard content
     * search. This is the "old" way of doing it (@see BAZ-1697)
     *
     * @return array An array of XN_Content objects that match the search
     *  requirements
     */
    protected function searchWithContentQuery() {
        $from = max(0, ($this->page - 1) * $this->pageSize);
        $to   = $from + $this->pageSize;
        $query = XN_Query::create('Content')->filter('owner');
        $query->filter('my.approved', '<>', 'N');
        XG_SecurityHelper::addVisibilityFilter($this->_user, $query, false);
        XG_QueryHelper::addSearchFilter($query, $this->term);
        XG_QueryHelper::addExcludeFromPublicSearchFilter($query);
        User::addBlockedFilter($query, false);
        User::addPendingFilter($query, false);
        User::addUnfinishedFilter($query, false);

        foreach(self::$typesToExclude as $typeToExclude) {
            $query->filter('type','neic',$typeToExclude);
        }
        // match term against type?

        $query->begin($from);
        $query->end($to);
        $query->alwaysReturnTotalCount(true);
        $content = $query->execute();
        $contentCount = $query->getTotalCount();
        return array($content, $contentCount);
    }


    /**
     * Search for matching content objects using the Search query type.
     * This is the "new" way of doing it (@see BAZ-1697)
     *
     * @return array An array of XN_Content objects that match the search
     *  requirements
     */
    protected function searchWithSearchQuery() {
        $from = max(0, ($this->page - 1) * $this->pageSize);
        $to   = $from + $this->pageSize;
        $query = XN_Query::create('Search')
            ->begin($from)->end($to)
            ->filter('fulltext','like',$this->term)
            ->filter('my.approved', '!like', 'N');
        XG_SecurityHelper::addVisibilityFilter($this->_user, $query, true);
        XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
        User::addBlockedFilter($query, true);
        User::addPendingFilter($query, true);
        User::addUnfinishedFilter($query, true);
        $query->alwaysReturnTotalCount(true);

        /* Give each widget a chance to alter the query */
        XG_ModuleHelper::dispatchToEnabledModules('index','annotateSearchQuery',array($query));

        try {
            $searchResults = $query->execute();
        } catch (Exception $e) {
            /* If something went wrong with the search query, log that, and
             * pretend that there were no results */
             error_log("App-wide search query ({$this->term}) failed with " . $e->getCode());
             return array(array(), 0);
        }
        $searchResultsCount = $query->getTotalCount();
        $content = XG_QueryHelper::contentFromSearchResults($searchResults);
        $content = self::prioritizeUserObjects($content);
        return array($content, $searchResultsCount);
    }

    /**
     * This floats any user objects in the results to the top of the results set
     *
     * @param $content array An array of XN_Content objects
     * @return array An array of XN_Content objects from the search prioritized for user objects
     */
     private function prioritizeUserObjects($content) {
         $sortedContent = array();
         if (count($content)) {
             $userObjects = array();
             $otherObjects = array();
             foreach ($content as $object) {
                 $object->type == 'User' ? $userObjects[] = $object : $otherObjects[] = $object;
             }
             if (count($userObjects)) {
                 // reorder the users based on most recent activity
                 if (count($userObjects) > 1) {
                     usort($userObjects, array("Index_SearchController", "sortUserObjects"));
                 }
                 $sortedContent = array_merge($userObjects, $otherObjects);
             } else {
                 $sortedContent = $content;
             }
         }
         return $sortedContent;
     }


     /**
      * This allows 2 user objects to be compared for purposes of sorting the user block in the search results
      * currently user objects are compared according to their last activity date
      *
      * @param $userA XN_Content an XN_Content object of type User
      * @param $userB XN_Content an XN_Content object of type User
      * @return integer nominating the correct ordering for the two submitted objects
      */
     private function sortUserObjects($userA, $userB) {
         return (strtotime($userA->updatedDate) > strtotime($userB->updatedDate)) ? -1 : 1;
     }


}
