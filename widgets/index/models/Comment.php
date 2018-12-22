<?php
/** A comment on a content object.
 *
 *	WARNING: All commentFormat restrictions are usually applied to the text processed by HTML_Scrubber!
 *	For the comment text validating "commentFormat" parameter in the "main" widget configuration is used.
 *	Parameter has the following format (spaces and newlines are ignored):
 *		format 	   = type *( ";" type)
 *		type 	   = typeName +( ":" paramName "=" paramValue)
 *		typeName   = "*" / word
 *	    paramName  = "order" / "allow" / "deny"
 *	    paramValue = "*" / nameList
 *	    nameList   = word *( "," word)
 *	    word       = +[a-z]
 *
 *	Where:
 *		typeName	lowercased content type or "*" (used only if tag is not matched by the specific typeName rule)
 *		order		sets the order of appling "allow" and "deny" rules. Can be any combination of
 *					"allow" and "deny". First match stops the tag processing, so
 *					    order = allow, deny : allow = * : deny = img;
 *					has no sense, becase all tags are allowed. Use "order = deny, allow" in this case.
 *					By default tag is allowed.
 *
 *		allow		sets the list of allowed tags or "*" for all tags
 *		deny		sets the list of prohibited tags or "*" for all tags
 *
 *	Empty commentFormat means: "* : order = allow : allow = *"
 *
 *  Example (comments added for readabiblity only):
 *  	# Prohibit <script>, <applet>, <body> and <html> for all comments
 *  	* : order = deny, allow : allow = "*" : deny = script, applet, body, html;
 *
 *  	# Prohibit embeds in the profile comments (<script>, <applet> etc are handled by the "*" rule)
 *  	user : order = deny : deny = embed, object;
 *
 *  	# Allow <script> in video comments (overrides the restriction set in the "*" rule)
 *  	video : order = allow : allow = script
 *
 **/
class Comment extends W_Model {
    /**
     * The comment text.
     *
     * @var XN_Attribute::STRING
     * @rule length 0,40000
     * @feature indexing text
     */
    public $description;
    const MAX_COMMENT_LENGTH = 40000;

    /**
     * System attribute marking whether the comment is available on the pivot and search results.
     *
     * @var XN_Attribute::NUMBER
     */
    public $isPrivate;

    /**
     * The id of the object that this comment is attached to.
     *
     * @var XN_Attribute::STRING
     */
    public $attachedTo;

    /**
     * The type of the object that this comment is attached to. Fixed to 'Photo'.
     *
     * @var XN_Attribute::STRING
     * @rule length 0,100
     */
    public $attachedToType;

    /**
     * The owner of the commented object.
     *
     * @var XN_Attribute::STRING
     * @rule length 0,100
     */
    public $attachedToAuthor;

    /**
     * Is this comment approved?
     * Y == Approved (or moderation is turned off)
     * N == Not yet approved
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     * @feature indexing phrase
     */
    public $approved;
    public $approved_choices = array('Y','N');

    /**
     * The mozzle that created this piece of content
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * Content ID of the Group to which this object belongs
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing phrase
     */
    public $groupId;

    /**
     * "Y" indicates that this group should be excluded from Ningbar and widget
     * searches. This is true of comments in private groups.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here
    static protected $_commentFormat = NULL;

    /**
     * Creates a new comment object attached to a piece of content.
     * Use HTML_Scrubber::scrub($commentText) to clean the comment text
     * before passing it in.
     *
     * @param $content The content object the comment is attached to
     * @param $commentText string the scrubbed text of the comment
     * @param $moderated boolean optional whether this comment is moderated
     * @return The new, unsaved comment object
     */
    public static function createAndAttachTo($content, $commentText, $moderated = false) {
        $comment = W_Content::create('Comment');
        // Comments are scrubbed on the way in
        $comment->description = self::formatCommentText($content->type, $commentText);
        // Make Comments always private (so they don't appear in the pivots or search results).
        $comment->isPrivate = true;
        $widget = W_Cache::current('W_Widget');
        $comment->my->mozzle = $widget->dir;
        $comment->my->approved = $moderated ? 'N' : 'Y';
        $comment->my->attachedTo = $content->id;
        $comment->my->attachedToType = $content->type;
        $comment->my->attachedToAuthor = $content->contributorName;
        self::adjustCounts($content, $comment, 1);
        return $comment;
    }

    /**
     * Loads a comment.
     *
     * @param id The id of the comment
     * @return The comment object if it exists, or null
     * not used [Phil McCluskey 2007-02-06]
     */
    public static function load($id) {
        return self::findById('Comment',$id);
    }

    /**
     * Returns all comments for the indicated content object.
     *
     * @param $id         The id of the object whose comments to return
     * @param $begin     The index of the first comment to return
     * @param $end       The index of comment after the last comment to return
     * @param $approved Y: Only return approved comments, N: Only return unapproved comments, null: return all comments
     * @param $order optional How to order comments, defaults to createdDate
     * @param $dir optional Order direction defaults to asc
     * @param $filters optional array of filters to add to the query
     * @param $ignoreGroupFilter boolean doesn't apply the group filter if true; defaults to false
     * @return An array 'comments' => the comments, 'numComments' => the total number of comments that match the query
     */
    public static function getCommentsFor($id, $begin = 0, $end = 100, $approved = NULL, $order='createdDate', $dir = 'asc', $filters = null, $ignoreGroupFilter = false) {
        $query = XN_Query::create('Content')
                         ->filter('type', '=', 'Comment')
                         ->filter('owner')
                         ->filter('my->attachedTo', '=', (string) $id)
                         ->order($order, $dir)
                         ->begin($begin)
                         ->end($end)
                         ->alwaysReturnTotalCount(true);
        if (! $ignoreGroupFilter) {
            XG_GroupHelper::addGroupFilter($query);
        }
        if ($approved == 'Y') {
            $query->filter('my->approved','=','Y');
        } elseif ($approved == 'N') {
            $query->filter('my->approved','=','N');
        }
        if (is_array($filters)) {
            foreach ($filters as $filterKey => $filterValue) {
                if (! is_array($filterValue)) {
                    $query->filter($filterKey,'=',$filterValue);
                } else {
                    $args = $filterValue;
                    // If each element of $args is itself an array, that means there are multiple
                    // filters to apply to this $filterKey
                    if (is_array($args[0])) {
                        foreach ($args as $subArgs) {
                            array_unshift($subArgs, $filterKey);
                            call_user_func_array(array($query, 'filter'), $subArgs);
                        }
                    } else {
                        array_unshift($args, $filterKey);
                        call_user_func_array(array($query, 'filter'), $args);
                    }
                }
            }
        } else if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Comment'));
        }
        $comments    = $query->execute();
        $numComments = $query->getTotalCount();
        return array('comments' => $comments, 'numComments' => $numComments, 'query' => $query);
    }

    /**
     * Returns all comments on content contributed by a particular user
     *
     * @param $user mixed A screen name, User object, or XN_Profile object
     * @param $begin     The index of the first comment to return
     * @param $end       The index of comment after the last comment to return
     * @param $approved Y: Only return approved comments, N: Only return unapproved comments, null: return all comments
     * @param $order optional How to order comments, defaults to createdDate
     * @param $dir optional Order direction defaults to desc
     * @param $filters optional array of filters to add to the query
     * @return An array 'comments' => the comments, 'numComments' => the total number of comments that match the query
     */
    public static function getCommentsForContentBy($user, $begin = 0, $end = 100, $approved = NULL, $order = 'createdDate', $dir = 'asc', $filters = null) {
        if ($user instanceof XN_Content || $user instanceof W_Content) {
            $screenName = $user->contributorName;
        } elseif ($user instanceof XN_Profile) {
            $screenName = $user->screenName;
        } else {
            $screenName = $user;
        }
        $query = XN_Query::create('Content')
        ->filter('type', '=', 'Comment')
        ->filter('owner')
        ->filter('my->attachedToAuthor', '=', $screenName)
        ->order($order, $dir)
        ->begin($begin)
        ->end($end)
        ->alwaysReturnTotalCount(true);
        if ($approved == 'Y') {
            $query->filter('my->approved','=','Y');
        } elseif ($approved == 'N') {
            $query->filter('my->approved','=','N');
        }
        if (is_array($filters)) {
            foreach ($filters as $filterKey => $filterValue) {
                if (! is_array($filterValue)) {
                    $query->filter($filterKey,'=',$filterValue);
                } else {
                    $args = $filterValue;
                    // If each element of $args is itself an array, that means there are multiple
                    // filters to apply to this $filterKey
                    if (is_array($args[0])) {
                        foreach ($args as $subArgs) {
                            array_unshift($subArgs, $filterKey);
                            call_user_func_array(array($query, 'filter'), $subArgs);
                        }
                    } else {
                        array_unshift($args, $filterKey);
                        call_user_func_array(array($query, 'filter'), $args);
                    }
                }
            }
        } else if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Comment'));
        }
        $comments    = $query->execute();
        $numComments = $query->getTotalCount();
        return array('comments' => $comments, 'numComments' => $numComments);
    }

       /**
     * Returns all comments by a particular user
     *
     * @param $user mixed A screen name, User object, or XN_Profile object
     * @param $begin     The index of the first comment to return
     * @param $end       The index of comment after the last comment to return
     * @param $approved Y: Only return approved comments, N: Only return unapproved comments, null: return all comments
     * @param $order optional How to order comments, defaults to createdDate
     * @param $dir optional Order direction defaults to desc
     * @param $filters optional array of filters to add to the query
     * @return An array 'comments' => the comments, 'numComments' => the total number of comments that match the query
     */
     public static function getCommentsBy($user, $begin = 0, $end = 100, $approved = NULL, $order = 'createdDate', $dir = 'asc', $filters = null) {
        if ($user instanceof XN_Content) {
            $screenName = $user->contributorName;
        } elseif ($user instanceof XN_Profile) {
            $screenName = $user->screenName;
        } else {
            $screenName = $user;
        }
        $query = XN_Query::create('Content')
        ->filter('type', '=', 'Comment')
        ->filter('owner')
        ->filter('contributorName', '=', $screenName)
        ->order($order, $dir)
        ->begin($begin)
        ->end($end)
        ->alwaysReturnTotalCount(true);
        if ($approved == 'Y') {
            $query->filter('my->approved','=','Y');
        } elseif ($approved == 'N') {
            $query->filter('my->approved','=','N');
        }
        if (is_array($filters)) {
            foreach ($filters as $filterKey => $filterValue) {
                if (! is_array($filterValue)) {
                    $query->filter($filterKey,'=',$filterValue);
                } else {
                    $args = $filterValue;
                    // If each element of $args is itself an array, that means there are multiple
                    // filters to apply to this $filterKey
                    if (is_array($args[0])) {
                        foreach ($args as $subArgs) {
                            array_unshift($subArgs, $filterKey);
                            call_user_func_array(array($query, 'filter'), $subArgs);
                        }
                    } else {
                        array_unshift($args, $filterKey);
                        call_user_func_array(array($query, 'filter'), $args);
                    }
                }
            }
        } else if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Comment'));
        }
        $comments    = $query->execute();
        $numComments = $query->getTotalCount();
        return array('comments' => $comments, 'numComments' => $numComments);
    }


    /**
     *  For two users, get comments posted by one user on the other's User object
     *    (chatterwall) and vice versa
     *
     * @param $userOne mixed A screen name, User object, or XN_Profile object
     * @param $userTwo mixed
     * @param $begin     The index of the first comment to return
     * @param $end       The index of comment after the last comment to return
     * @return An array 'comments' => the comments, 'numComments' => the total number of comments that match the query
     *
     */
    public static function getCommentThread($userOne, $userTwo, $begin = 0, $end = 100) {
        //  Get screen name and user object for user one
        if ($userOne instanceof XN_Content) {
            $screenNameOne = $userOne->contributorName;
        } elseif ($userOne instanceof XN_Profile) {
            $screenNameOne = $userOne->screenName;
        } else {
            $screenNameOne = $userOne;
        }
        if (!$userOne instanceof XN_Content) {
            $userOne = User::load($screenNameOne);
        }
        if (!$userOne) { return NULL; }

        //  Get screen name and user object for user two
        if ($userTwo instanceof XN_Content) {
            $screenNameTwo = $userTwo->contributorName;
        } elseif ($userTwo instanceof XN_Profile) {
            $screenNameTwo = $userTwo->screenName;
        } else {
            $screenNameTwo = $userTwo;
        }
        if (!$userTwo instanceof XN_Content) {
            $userTwo = User::load($screenNameTwo);
        }
        if (!$userTwo) { return NULL; }

        $filterOneToTwo = XN_Filter::all(XN_Filter('contributorName', '=', $screenNameOne),
                XN_Filter('my->attachedTo', '=', $userTwo->id));
        $filterTwoToOne = XN_Filter::all(XN_Filter('contributorName', '=', $screenNameTwo),
                XN_Filter('my->attachedTo', '=', $userOne->id));
        $query = XN_Query::create('Content')
            ->filter('type', '=', 'Comment')
            ->filter('owner')
            ->filter(XN_Filter::any($filterOneToTwo, $filterTwoToOne))
            ->filter('my->approved','=','Y')
            ->order('createdDate', 'desc')
            ->begin($begin)
            ->end($end)
            ->alwaysReturnTotalCount(true);

        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->addCaching(XG_Cache::key('type','Comment'));
        }

        $comments    = $query->execute();
        $numComments = $query->getTotalCount();
        return array('comments' => $comments, 'numComments' => $numComments);
    }

    /**
     * Removes a comment
     *
     * @param $idOrComment mixed The ID of the comment or the content object of the comment
     * @param $saveContent boolean Whether to save the content the comment is attached to after
     *   adjusting its counts. Defaults to true, but you can set to false if you're deleting
     *   a bunch of comments and will handle the content saving yourself.
     * @return boolean whether the removal succeeds
     */
    public static function remove($idOrComment, $saveContent = true) {
        try {
            if ($idOrComment instanceof XN_Content) {
                $comment = $idOrComment;
                $idForError = $comment->id;
            } else {
                $comment = Comment::load($idOrComment);
                $idForError = $idOrComment;
            }
            XN_Content::delete($comment);
        } catch (Exception $e) {
            error_log("Couldn't remove comment: $idForError -- {$e->getMessage()}");
            if (W_Cache::getWidget('main')->config['debugLogStackTraceOnCommentRemovalException']) {
                W_Cache::getWidget('main')->config['debugLogStackTraceOnCommentRemovalException'] = 0;
                W_Cache::getWidget('main')->saveConfig();
                error_log($e->getTraceAsString());
            }
            return false;
        }

    // If removing the comment succeeded, adjust the object the comment
    // is attached to. If this fails, don't report an error (this attached-to
    // object may have been deleted already (BAZ-3222)
    try {
            $content = XG_Cache::content($comment->my->attachedTo);
            self::adjustCounts($content, $comment, -1);
            if ($saveContent) {
                $content->save();
            }
        } catch (Exception $e) {
        }

    return true;
    }

    /**
     * Removes multiple Comment objects. Useful for bulk-delete operations.
     *
     * @param $comments array the Comment objects to remove
     * @param $numComments integer the total number of comments that will eventually be removed.
     *         Defaults to the size of $comments.
     * @return array 'changed' => the number of comments removed,
     *         'remaining' => difference between numComments and changed,
     *         'content' => array of content objects that the comments were attached to
     */
    public static function removeComments($comments, $numComments = null) {
        $numComments = $numComments ? $numComments : count($comments);
        $changed = $remaining = 0;
        if (count($comments)) {
            // Load all of the content that the comments are attached to, so they can be saved after their counts are updated
            $unapprovedIds = array();
            $ids = array();
            foreach ($comments as $comment) {
                if (mb_strlen(trim($comment->my->attachedTo))) {
                    $ids[] = $comment->my->attachedTo;
                }
                if (($comment->my->approved == 'N') && in_array($comment->my->attachedToType, array('User','BlogPost'))) {
                    // unapproved chatters and blogpost comments should update my.chattersToApprove and my.commentsToApprove (BAZ-10147) [ywh 2008-09-24]
                    if (! array_key_exists($comment->my->attachedToAuthor, $unapprovedIds)) {
                        $unapprovedIds[$comment->my->attachedToAuthor] = array('User' => 0, 'BlogPost' => 0);
                    }
                    $unapprovedIds[$comment->my->attachedToAuthor][$comment->my->attachedToType]++;
                }
            }
            try {
                $content = XG_Cache::content($ids);
                $unapprovedCommentUsers = array_keys($unapprovedIds);
                $updateUsers = count($unapprovedCommentUsers) > 0 ?
                                    User::loadMultiple($unapprovedCommentUsers) :
                                    array();
            } catch (Exception $e) {
                $content = array();
            }
            // Now that all the content is loaded, we can delete each comment
            foreach ($comments as $comment) {
                Comment::remove($comment, false);
                $changed++;
            }

            // update unapproved chatter/comment counts (BAZ-10147) [ywh 2008-09-24]
            foreach ($updateUsers as $user) {
                $user->my->chattersToApprove -= $unapprovedIds[$user->title]['User'];
                $user->my->commentsToApprove -= $unapprovedIds[$user->title]['BlogPost'];
                $user->save();
            }

            // And now save the content that the comments are attached to, since
            // the comment counts have been updated except user objects we already
            // saved above.
            foreach ($content as $c) {
                if (! in_array($c->title, $unapprovedCommentUsers)) {
                    $c->save();
                }
            }
            $remaining += ($numComments - $changed);
        }
        if (! is_array($content)) { $content = array(); }
        return array('changed' => $changed, 'remaining' => $remaining, 'content' => $content);
    }

    /**
     * Approves a comment
     *
     * @param $idOrComment mixed The ID of the comment or the content object of the comment
     * @param $save bool	Whether to save the object comment is attached to
     * @return boolean whether the removal succeeds
     */
    public static function approve($idOrComment, $save = true) {
        try {
            if ($idOrComment instanceof XN_Content) {
                $comment = $idOrComment;
            } else {
                $comment = Comment::load($idOrComment);
            }
            // Don't do anything if the comment doesn't need to be approved
            if ($comment->my->approved != 'N') {
                return;
            }
            $content = XG_Cache::content($comment->my->attachedTo);
            $comment->my->approved = 'Y';
            self::adjustApprovalCount($content, $comment, -1);
            $comment->save();
            if ($save) {
                $content->save();
            }
        } catch (Exception $e) {
            error_log("Couldn't approve comment: $idOrComment -- {$e->getMessage()}");
            return false;
        }
    }


    /**
     * Adjust the comment counts (regular, to approve) on the corresponding content object
     *
     * @param $content XN_Content The content the comment is attached to
     * @param $comment XN_Content The comment itself
     * @param $increment integer what to adjust the counts by
     */
    protected static function adjustCounts($content, $comment, $increment) {
        $widget = W_Cache::getWidget($comment->my->mozzle);
        $commentCountAttrName = XG_App::widgetAttributeName($widget, 'commentCount');
        $content->my->{$commentCountAttrName} = $content->my->{$commentCountAttrName} + $increment;
        if ($content->my->{$commentCountAttrName} < 0) {
            $content->my->{$commentCountAttrName} = 0;
        }
        if ($comment->my->approved == 'N') {
            self::adjustApprovalCount($content, $comment, $increment);
        }
    }

    /**
     * Adjust the comment approval count on the corresponding content object
     *
     * @param $content XN_Content The content the comment is attached to
     * @param $comment XN_Content The comment itself
     * @param $increment integer what to adjust the counts by
     */
    protected static function adjustApprovalCount($content, $comment, $increment) {
        $widget = W_Cache::getWidget($comment->my->mozzle);
        $toApproveAttributeName = XG_App::widgetAttributeName($widget, 'commentToApproveCount');
        $content->my->{$toApproveAttributeName} = $content->my->{$toApproveAttributeName} + $increment;
        if ($content->my->{$toApproveAttributeName} < 0) {
            $content->my->{$toApproveAttributeName} = 0;
        }
    }

    /**
     * Create new, zeroed-out comment counts on an object
     *
     * @param $content XN_Content The content object
     * @param $widget W_BaseWidget optional widget that the comments are contributed by
     */
     public static function initializeCounts($content, $widget = null) {
         if (is_null($widget)) { $widget = W_Cache::current('W_Widget'); }
         $commentCountAttrName = XG_App::widgetAttributeName($widget, 'commentCount');
         $content->my->{$commentCountAttrName} = 0;
         $toApproveAttributeName = XG_App::widgetAttributeName($widget, 'commentToApproveCount');
         $content->my->{$toApproveAttributeName} = 0;
     }

    /**
     * Get comment counts for a piece of content
     *
     * @param $content XN_Content The content the comments are attached to
     * @return array The total comment count and the to-approve count
     */
     public static function getCounts($content) {
         $counts = array();
         if (!$content->my->mozzle) {
             return $counts;
         }
         $widget = W_Cache::getWidget($content->my->mozzle);
         $commentCountAttrName = XG_App::widgetAttributeName($widget, 'commentCount');
         $counts['commentCount'] = $content->my->{$commentCountAttrName};
         $counts['commentCount'] = $counts['commentCount'] ? $counts['commentCount'] : 0;
         $toApproveAttributeName = XG_App::widgetAttributeName($widget, 'commentToApproveCount');
         $counts['commentToApproveCount'] = $content->my->{$toApproveAttributeName};
         $counts['approvedCommentCount'] = max(0, $counts['commentCount'] - $counts['commentToApproveCount']);
         return $counts;
     }

    /**
     *  Formats the comment according to the "commentFormat" rules. Read the description above.
     *
     *  @param      $attachedTo	string	Content type the comment is attached to
     *  @param		$text		string	The actual comment text
     *  @return     string
     */
    public static function formatCommentText($attachedTo, $text) {
        if (NULL === self::$_commentFormat) { //  No multibyte string functions here.
            self::$_commentFormat = array();
            foreach( array_filter( explode(';', preg_replace('/\s+/u','', mb_strtolower(W_Cache::getWidget('main')->privateConfig['commentFormat'])) ) ) as $type) {
                $type = explode(':', $type);
                if (!preg_match('/^(\*|[a-z]+)$/u',$typeName = array_shift($type))) {
                    throw new Exception("Malformed typeName `$typeName'");
                }
                self::$_commentFormat[$typeName] = array();
                foreach($type as $param) {
                    list($name, $value) = explode('=', $param);
                    if ($name == 'order' || $name == 'allow' || $name == 'deny') {
                        $value = $value == '*' ? $value : explode(',',$value);
                        self::$_commentFormat[$typeName][$name] = $value;
                    } else {
                        throw new Exception("Unknown parameter `$name'");
                    }
                }
            }
        }
        $attachedTo = mb_strtolower($attachedTo);
        if ( !isset(self::$_commentFormat[$attachedTo]) ) {
            if (!isset(self::$_commentFormat['*']) ) {
                // shortcut for the "no commentFormat" case
                return $text;
            }
            $attachedTo = '*';
        }
        return strip_tags($text, self::_getCompiledFormatRule($attachedTo));
    }

    /**
     *  Returns the rule string compiled for using in strip_tags()
     *
     *  @param      $attachedTo   string	Lowercased content type
     *  @return     string
     */
    protected static function _getCompiledFormatRule($attachedTo) { # string
        static $allTags = array('a','abbr','acronym','address','applet','area','b','base','basefont',
            'bdo','big','blockquote','body','br','button','caption','center','cite','code','col',
            'colgroup','dd','del','dfn','dir','div','dl','dt','em','fieldset','font','form','frame',
            'frameset','h1','h2','h3','h4','h5','h6','head','hr','html','i','iframe','img','input',
            'ins','isindex','kbd','label','legend','li','link','map','menu','meta','noframes',
            'noscript','object','ol','optgroup','option','p','param','pre','q','s','samp','script',
            'select','small','span','strike','strong','style','sub','sup','table','tbody','td',
            'textarea','tfoot','th','thead','title','tr','tt','u','ul','var');
        $conf =& self::$_commentFormat[$attachedTo];
        if (isset($conf['tags'])) {
            return $conf['tags'];
        }
        if ($attachedTo == '*' || !isset(self::$_commentFormat['*']) ) {
            $tags = $allTags;
        } else {
            self::_getCompiledFormatRule('*');
            $tags = self::$_commentFormat['*']['tagList'];
        }
        foreach (array_reverse((array)$conf['order']) as $rule) {
            $v = $conf[$rule];
            if ('allow' == $rule) {
                $tags = ($v == '*') ? $allTags : array_merge($tags, $v);
            } else {
                $tags = ($v == '*') ? array() : array_diff($tags, $v);
            }
        }
        if ($attachedTo == '*') {
            // keep the tag list for the future reuse
            $conf['tagList'] = $tags;
        }
        return $conf['tags'] = '<' . join('><',$tags) . '>';
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
