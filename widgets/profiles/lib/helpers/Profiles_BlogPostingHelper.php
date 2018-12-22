<?php
/*
 *	Tool for publishing future blog posts
 */
class Profiles_BlogPostingHelper {
	static protected $fgCount = 10;		// the number of posts to update in the foreground
	static protected $bgCount = 50;		// the number of posts to update in the background

	// The name of a lock used to block simultaneous publishers
	static protected $publishLock = 'Profiles_BlogPostingHelper::publish-sd874erg42e60ghqw2879345';
	// The name of a lock used to block simultaneous "next post" searchers.
	static protected $searchLock = 'Profiles_BlogPostingHelper::search-g09bm5j3498dgbq2isf04567';

    // connect timeout in seconds
    const PINGOMATIC_HTTP_CONNECTION_TIMEOUT = 45;

    /**
     * Pings 3rd party blog tracking services via pingomatic
     * currently: weblogs.com, blo.gs, Technorati, Feedburner and Yahoo!
     *
     */
    public static function sendPings($blogPost) {
        if (!XG_App::everythingIsVisible() || $blogPost->my->publishStatus != 'publish' || $blogPost->my->visibility != 'all') {
        	return;
		}
		$widget = W_Cache::getWidget('profiles');
        $widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        $feedUrl = Profiles_FeedHelper::feedUrl($widget->buildUrl('blog','feed',array('user' => $blogPost->contributorName, 'xn_auth' => 'no')));
        $blogUrl = User::profileUrl($blogPost->contributorName);
        $url = "http://pingomatic.com/ping/?title=" . urlencode(BlogPost::getTextTitle($blogPost)) . "&blogurl=" . urlencode($blogUrl) . "&rssurl=" . urlencode($feedUrl) . "&chk_weblogscom=on&chk_blogs=on&chk_technorati=on&chk_feedburner=on&chk_myyahoo=on";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, self::PINGOMATIC_HTTP_CONNECTION_TIMEOUT);
        $response = curl_exec ($ch);
        curl_close ($ch);
    }

    /**
     * Adds the blog post to the activity dashboard, if it has public visibility.
     *
     * @param $blogPost XN_Content|W_Content  the BlogPost
     */
    public static function logActivity($blogPost) {
        if ($blogPost->my->visibility !== 'all') { return; }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_BLOG, $blogPost->contributorName, array($blogPost));
    }

    /**
	 *  Runs the check for the presence of posts awaiting the publication.
	 *  If there are such posts, they are posted. If there are TOO MANY such posts, new job is executed to post them
	 *  in the background.
     *
     *  @return     void
     */
    public static function doFuturePostsCheck() {
    	$blogArchive = BlogArchive::instance();
		if ($blogArchive->my->nextFuturePost === NULL) {
			self::_findNextFuturePost();
		}
		$nextFuturePost = strtotime($blogArchive->my->nextFuturePost);
		if ($nextFuturePost && $nextFuturePost < time()) {
			self::task_publishPosts(self::$fgCount);
		}
	}

	public static function syncFuturePostDate($post) { # void
		if ($post->my->publishStatus != 'queued') {
			return;
		}
		$blogArchive = BlogArchive::instance();
		$current = $blogArchive->my->nextFuturePost ? strtotime($blogArchive->my->nextFuturePost) : 0;
		if (!$current || $current > strtotime($post->my->publishTime)) {
			XG_App::includeFileOnce('/lib/XG_LockHelper.php');
			if (!XG_LockHelper::lock(self::$searchLock)) {
				// TODO: After adding versioning, we can avoid locking here and just use "safe" save().
				return;
			}
			$blogArchive->my->nextFuturePost = $post->my->publishTime;
			$blogArchive->save();
			XG_LockHelper::unlock(self::$searchLock);
		}
    }

    /**
	 *  Background handler to publish future posts. Publishes all posts with publish date in the past.
     *
	 *	@param	$limit		int		Number of objects to update
	 *	@param  $noChain	bool	Prevents chaining if the total number of objects is larger than $limit
     *  @return void
     */
	public static function task_publishPosts($limit = 0, $noChain = false) {
		// We use BlogPost::find() here and we want to prevent the double triggering.
		BlogPost::$_futureCheckIsDone = 1;

		if ( !$limit = abs(intval($limit)) ) {
			$limit = self::$bgCount;
		}

		XG_App::includeFileOnce('/lib/XG_LockHelper.php');
		if (!XG_LockHelper::lock(self::$publishLock, 0)) {
			return; // if locked, do nothing and do not wait: another process is resposible for everything.
		}

		W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
        /* Find all posts that are marked as 'queued' and have a publish time in the past,
         * mark them as published, and invalidate appropriate caches */
		$postInfo = BlogPost::find( array(
        	'ignoreVisibility' => true,
			'my->publishStatus' => 'queued',
			'my->publishTime' => array('<=', date('c'), XN_Attribute::DATE),
		), 0, $limit );
        foreach ($postInfo['posts'] as $post) {
            $post->my->publishStatus = 'publish';
            $post->save();
            $user = User::load($post->contributorName);
            Profiles_BlogArchiveHelper::addPostToArchiveIfEligible($user, $post);
            $user->save();
			self::logActivity($post);
			self::sendPings($post);
        }
		XG_LockHelper::unlock(self::$publishLock);

		self::_findNextFuturePost();

		if ( !$noChain && $postInfo['numPosts'] > count($postInfo['posts']) ) {
			XG_App::includeFileOnce('/lib/XG_JobHelper.php');
			$task = array(array(__CLASS__,'task_publishPosts'));
			XG_JobHelper::create(array($task), __FILE__);
		}
    }

    // Finds the next closest future post and updates BlogArchive instance.
    protected static function _findNextFuturePost() { # void
		XG_App::includeFileOnce('/lib/XG_LockHelper.php');
		if (!XG_LockHelper::lock(self::$searchLock, 0)) {
			return; // if locked, do nothing and do not wait: another process is resposible for everything.
		}
		$postInfo = BlogPost::find( array(
        	'ignoreVisibility' => true,
			'my->publishStatus' => 'queued',
			// we're interested in all queued posts
		), 0, 1, 'my->publishTime', 'asc');
		$nextDate = $postInfo['numPosts'] ? $postInfo['posts'][0]->my->publishTime : date('c',0);

		$blogArchive = BlogArchive::instance();
		if ($blogArchive->my->nextFuturePost !== $nextDate) {
			$blogArchive->my->nextFuturePost = $nextDate;
			$blogArchive->save();
		}
		XG_LockHelper::unlock(self::$searchLock);
    }
}
?>
