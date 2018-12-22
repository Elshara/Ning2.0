<?php
/**
 * A blog post for the profiles mozzle
 */
class BlogPost extends W_Model {
    /**
     * The title of the blog post; scrubbed HTML
     *
     * @var XN_Attribute::STRING optional
     * @rule length 1,200
     */
    public $title;

    /**
     * The body of the blog post; scrubbed HTML
     *
     * @var XN_Attribute::STRING
     * @rule length 1,100000
     */
    public $description;

    /**
     * Is this post public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Title + description with HTML stripped
     *
     * @var XN_Attribute::STRING
     * @feature indexing text
     *
     */
    public $searchText;

    /**
     * one of a selection of "mood" strings (happy, sleepy, dopey, etc.).
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1
     */
    public $mood;
    public $mood_choices = array('Happy','Sad','Cool','Evil','Mad','Envious');
    // Keep mood_choices in sync with moodName below [Jon Aquino 2007-01-02]

    /**
     * draft: saved as draft
     * queued: saved to publish, but not published yet
     * publish: saved to publish now, or previously saved as queued but published
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     * @feature indexing phrase
     */
    public $publishStatus;
    public $publishStatus_choices = array('draft','queued','publish');

    /**
     * If/when this post is published, should it be published
     * immediately or at a scheduled time?
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     */
    public $publishWhen;
    public $publishWhen_choices = array('now','later');

    /**
     * Time before which the post won't appear – only posts with publishStatus = 'publish' displayed
     *
     * @var XN_Attribute::DATE optional
     */
    public $publishTime;

    /**
     * Who is allowed to comment on this blog post?
     * Use getAddCommentPermission() instead of accessing this attribute directly.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @since 2.3
     */
    public $addCommentPermission;
    public $addCommentPermission_choices = array('all', 'friends', 'me');

    /**
     * Are comments allowed on this post?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1
     * @deprecated 2.3  Use getAddCommentPermission() instead
     */
    public $allowComments;
    public $allowComments_choices = array('Y','N');

    /**
     * Who can see this blog post?
     *
     * @var XN_Attribute::STRING
     * @rule choice 1
     * @feature indexing phrase
     */
    public $visibility;
    public $visibility_choices = array('all','friends','me');

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * Bazel version number of the format of the blog post.
     * null indicates pre-2.3 (created using the Dojo Rich Text Editor).
     * "2.3" indicates 2.3 or later (created using the SimpleToolbar).
     *
     * @var XN_Attribute::STRING optional
     */
    public $format;

    /**
     * The post's most popular 10 tags
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $topTags;
    const TOP_TAGS_COUNT = 10;

    /**
     * GMT dates and view counts. Example: 11FEB1977 5, 12FEB1977 10, 07MAR1977 15.
     *
     * @var XN_Attribute::STRING optional
     */
    public $dailyViewCountsForLastMonth;

    /**
     * The popularity value for the photo.
     *
     * @var XN_Attribute::NUMBER optional
     * @rule range 0,*
     */
    public $popularityCount;

    /**
     * The number of detail views of the photo.
     *
     * @var XN_Attribute::NUMBER optional
     * @rule range 0,*
     */
    public $viewCount;

    /**
     * When was the photo viewed (on the detail page) the last time.
     *
     * @var XN_Attribute::DATE optional
     */
    public $lastViewedOn;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here
    /**
     * 	The check for the future posts is already done.
     *  @var bool
     *  @access friend
     */
    public static $_futureCheckIsDone = 0;

    /**
     * Create a new BlogPost object having the provided attribute values
     *
     * @param $data array Values to populate the BlogPost object with
     * @return BlogPost
     */
    public static function createWith($data) {
        $post = W_Content::create('BlogPost');
        Comment::initializeCounts($post);
        $post->my->mozzle = W_Cache::current('W_Widget')->dir;
        /* Set defaults which may be overridden in update */
        $post->my->publishStatus = 'publish';
        $post->my->publishTime = gmdate('Y-m-d\TH:i:s\Z');
        $post->my->publishWhen = 'now';
        $post->my->visibility = 'all';
        return self::update($post, $data);
    }

    /**
     * Update a BlogPost object with the provided attribute values
     *
     * @param $data array values to populate the BlogPost object with
     * @return BlogPost
     */
    public static function update($post, $data) {
        $post->my->format = '2.2';
        $post->isPrivate = XG_App::appIsPrivate();
        if (isset($data['visibility']) && ($data['visibility'] != 'all')) {
            $post->isPrivate = true;
        }
        foreach (array('title','description') as $attr) {
            if (isset($data[$attr])) {
                // BAZ-216: &nbsp; is causing problems. This may be a bug in Tidy
                // or in PHP's tidy interface
                $tmp = str_replace('&nbsp;', ' ', $data[$attr]);
                // Remove any embed placeholders that may have been inserted (BAZ-656)
                $tmp = preg_replace('@<img src="[^"]+" class="xg_embed_marker"[^>]*?>@u','', $tmp);
                $tmp = xg_scrub($tmp);
                $tmp = str_replace(chr(194).chr(160),' ',$tmp);
                $post->{$attr} = $tmp;
            }
        }
        $post->my->searchText = strip_tags($data['title']) . ' ' . strip_tags($data['description']);
        foreach (array('mood' => XN_Attribute::STRING, 'publishStatus' => XN_Attribute::STRING, 'visibility' => XN_Attribute::STRING, 'publishTime' => XN_Attribute::DATE, 'publishWhen' => XN_Attribute::STRING, 'addCommentPermission' => XN_Attribute::STRING) as $attr => $type) {
            if (isset($data[$attr])) {
                $post->my->set($attr, $data[$attr], $type);
            }
        }
        return $post;
    }

    /**
     * Return a localized version of the given mood.
     *
     * @param $moodValue string A valid value for the mood attribute
     * @return string A localized version of the mood
     */
    public static function moodName($moodValue) {
        // Keep list in sync with mood_choices above [Jon Aquino 2007-01-02]
        switch ($moodValue) {
            case 'Happy': return xg_text('HAPPY');
            case 'Sad': return xg_text('SAD');
            case 'Cool': return xg_text('COOL');
            case 'Evil': return xg_text('EVIL');
            case 'Mad': return xg_text('MAD');
            case 'Envious': return xg_text('ENVIOUS');
            default: throw new Exception('Invalid mood: ' . $moodValue);
        }
    }

    /**
     * Query for blog posts, incorporating the right visibility filters based
     * on the currently logged in user
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * You can also specify the special filter 'ignoreVisibility => true' to get a query
     * that ignores visibility settings.
     * @param $begin integer optional result set start. Defaults to 0
     * @param $end integer   optional result set end.   Defaults to 10
     * @param $order string  optional field to order on. Defaults to my->publishTime
     * @param $dir string    optional ordering direction: asc or desc.
     * @return array A two element array: 'posts' => the requested posts
     *                                    'numPosts' => the total number of posts that match
     */
    public static function find($filters, $begin = 0, $end = 10, $order = null, $dir = null) {
        if (!self::$_futureCheckIsDone) {
            self::$_futureCheckIsDone = 1;
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogPostingHelper.php');
            Profiles_BlogPostingHelper::doFuturePostsCheck();
        }
        $query = XN_Query::create('Content')
                   ->filter('owner')
                   ->filter('type','eic','BlogPost');

        $currentUser = XN_Profile::current();
        if (isset($filters['ignoreVisibility']) && ($filters['ignoreVisibility'] == true)) {
            unset($filters['ignoreVisibility']);
        }
        else {
            if ($currentUser->isLoggedIn()) {
               $query->filter(XN_Filter::any(
                       XN_Filter('my.visibility','=','all'),
                       XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                                      XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                       XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                                      XN_Filter('contributorName', '=', $currentUser->screenName))));
               $friendsFilterAdded = TRUE;
           } else {
               $query->filter('my.visibility', '=', 'all');
           }
        }
        if (isset($filters['promoted'])) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            if (! XG_PromotionHelper::areQueriesEnabled()) { return array('posts' => array(), 'numPosts' => 0); }
            XG_PromotionHelper::addPromotedFilterToQuery($query);
            unset($filters['promoted']);
            /* If no order is otherwise specified sort by promotion time, newest first */
            if (is_null($order)) {
                $order = 'my->' . XG_PromotionHelper::attributeName();
            }
            if (is_null($dir)) {
                $dir = 'desc';
            }
        }
        // TODO: Replace this block of code with XG_QueryHelper::applyFilters() [Jon Aquino 2008-08-07]
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
        $query->begin($begin);
        $query->end($end);
        if (is_null($order) && is_null($dir)) {
            $order = 'my->publishTime';
            $dir = 'desc';
        }
        if (! is_null($order)) {
            $dir = is_null($dir) ? 'asc' : $dir;
            $query->order($order, $dir);
        }
        $query->alwaysReturnTotalCount(true);

        /* Only cache the query if:
         * - the FRIENDS() filter is not being used
         * AND
         * - there are no $filters OR it's OK to cache order N
         *   queries (@see BAZ-2969)
         */
        // TODO: Allow caching for the friends filter, but use XG_QueryHelper::setMaxAgeForFriendsQuery [Jon Aquino 2008-09-17]
        if (!$friendsFilterAdded && (count($filters) == 0 || XG_Cache::cacheOrderN())) {
            // Add type-based caching to query
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','BlogPost'));
        }

        try {
            $posts = $query->execute();
        } catch (Exception $e) {
            error_log('BAZ-6796 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER']);
            throw $e;
        }
        $numPosts = $query->getTotalCount();
        return array('posts' => $posts, 'numPosts' => $numPosts);
    }

    /**
     * Remove a blog post and its associated comments
     *
     * @param $post BlogPost
     * @param $removeComments boolean Whether to remove comments on the post.
     *    Defaults to true, but set to false if you're removing the comments
     *    yourself as part of bulk operations.
     */
    public static function remove($post, $removeComments = true) {
        if ($removeComments) {
            // @todo: handle a large comment count ( > 100) differently?
            $commentInfo = Comment::getCommentsFor($post->id);
            foreach ($commentInfo['comments'] as $comment) {
                XN_Content::delete($comment->id);
            }
        }
        XN_Content::delete($post);
    }

    /**
     * Produce a truncated version of the blog post, with HTML
     * structure roughly preserved.
     *
     * @param $post BlogPost Post to summarize
     * @param $cutoff optional character position to start truncation.
     * @return string
     */
    public static function summarize($post, $cutoff = 500) {
        return xg_excerpt_html($post->description, $cutoff);
    }

    /**
     * Return either the title or, if there's no title, a string calculated
     * from the description that can be used where the title would be.
     * (see BAZ-945).
     *
     * @param $post BlogPost
     * @return string  HTML for the title
     */
    public static function getHtmlTitle($post, $length = 100) {
        // If there's a title, use that.
        if (mb_strlen($post->title)) {
            return $post->title;
        }
        // TODO: Use xg_excerpt? [Jon Aquino 2007-09-06]
        $desc = html_entity_decode(strip_tags($post->description), ENT_QUOTES, 'UTF-8');
        if (mb_strlen($desc) <= $length) {
            return $desc;
        } else {
            return mb_substr($desc, 0, $length) . '…';
        }
    }

    /** @deprecated 3.0  Use getHtmlTitle instead */
    public static function getTitle($post, $length = 100) {
        return self::getHtmlTitle($post, $length);
    }

    /**
     * Returns a plain-text version of the blog post's title (blog-post titles are scrubbed HTML).
     * Newlines and tags are removed; HTML entities are decoded.
     *
     * @param $post XN_Content  The BlogPost
     * @return  The plain-text title of the blog post
     */
    public static function getTextTitle($post) {
        return self::textTitle(self::getHtmlTitle($post));
    }

    /**
     * Returns a plain-text version of the blog post's title (blog-post titles are scrubbed HTML).
     * Newlines and tags are removed; HTML entities are decoded.
     *
     * @param $title string  The HTML title of the BlogPost
     */
    public static function textTitle($title) {
        return preg_replace('@\s+@u', ' ', strip_tags(html_entity_decode($title, ENT_QUOTES, 'UTF-8')));
    }

    /**
     * Converts a blog post body from pre-2.3 format (Dojo Rich Text Editor)
     * to 2.3 format (SimpleToolbar).
     *
     * @param $description string  a pre-2.3 post body
     * @param $format string  Bazel version number of the format of the blog post
     * @return string  the post body in 2.3 format
     * @see BlogPostTest#testUpgradeDescriptionFormat
     */
    public static function upgradeDescriptionFormat($description, $format) {
        if (! is_null($format)) { return $description; }
        // Before 2.3, newlines were ignored; <br/>s were inserted explicitly.
        // In 2.3 and later, newlines are no longer ignored.  [Jon Aquino 2007-11-28]
        $result = '';
        $currentLine = '';
        foreach (explode("\n", $description) as $nextLine) {
            $result .= $currentLine;
            // For readability, preserve newlines where ignored by xg_nl2br() [Jon Aquino 2007-11-28]
            if (preg_match('/^<.?' . XG_BLOCK_LEVEL_ELEMENT_PATTERN . '\b/iu', $currentLine) || preg_match('/^<.?' . XG_BLOCK_LEVEL_ELEMENT_PATTERN . '\b/iu', $nextLine)) {
                $result .= "\n";
            } else {
                $result .= ' ';
            }
            $currentLine = $nextLine;
        }
        $result .= $currentLine;
        return trim(xg_br2nl($result));
    }

    /**
     * Returns the next or previous published BlogPost by the same user,
     * visible to the current user.
     *
     * @param $comparison string  < or >, for previous or next
     * @param $post XN_Content|W_Content  the current BlogPost
     * @return XN_Content  the next (or previous) BlogPost
     */
    public static function adjacentPost($comparison, $post) {
        $filters = array(
                'my->publishTime' => array($comparison, $post->my->publishTime, XN_Attribute::DATE),
                'contributorName' => $post->contributorName,
                'my->publishStatus' => 'publish');
        $postInfo = BlogPost::find($filters, 0, 1, 'my->publishTime', $comparison == '>' ? 'asc' : 'desc');
        return $postInfo['posts'][0];
    }

    /**
     * Identifies the set of users who are allowed to see the Add A Comment form.
     *
     * @param $post XN_Content|W_Content  The BlogPost
     * @param $blogPostOwner XN_Content|W_Content  The User object for author of the BlogPost
     * @return string  "all", "friends", or "me"
     */
    public static function getAddCommentPermission($blogPost, $blogPostOwner) {
        if ($blogPost->my->addCommentPermission) { return $blogPost->my->addCommentPermission; }
        if ($blogPost->my->allowComments == 'N') { return 'me'; }
        return $blogPostOwner->my->addCommentPermission;
    }

    //
    public static function syncFuturePostDate ($objects) { # void
        foreach (is_array($objects) ? $objects : array($objects) as $object) {
            if ($object->type == 'BlogPost' && $object->my->publishStatus == 'queued') {
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogPostingHelper.php');
                Profiles_BlogPostingHelper::syncFuturePostDate($object);
            }
        }
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('xn/content/save/after', array('BlogPost', 'syncFuturePostDate'));

/*
    These attributes will be useful in the future:

    my.recentCommenters 	string 	10 alternating usernames and timestamps
    my.favoritedCount 	number
    my.viewCount 	number 	*
    my.lastViewedOn 	date
    my.viewCountForLastDay 	number 	If prior lastViewedOn matches today, this is incremented; if not, this is reset.*
    my.viewCountsForLastMonth 	number 	*
    my.totalViewCountForLastMonth 	number 	*
    my.viewCountsForLastWeek 	number 	*
    my.totalViewCountForLastWeek 	number 	*
    my.popularityCount 	number 	*
    my.popularityDate 	date 	*
    my.recentFavoriters 	string 	10 alternating usernames and timestamps
*/
