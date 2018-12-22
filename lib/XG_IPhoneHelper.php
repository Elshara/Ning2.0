<?php
/** $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  Contains miscellaneous iphone helper functions (basically related to HTML rendering)
 *
 **/

class XG_IPhoneHelper {
    public static function header($highlight = NULL, $title = NULL, $user = NULL, $options = NULL) {
        $mainWidget = W_Cache::getWidget('main');
        $widget = W_Cache::current('W_Widget');
        if (!$widget) {
            $widget = $mainWidget;
        }
        if (!is_array($options)) {
            $options = array();
        }
        $componentCss = array( $widget->buildResourceUrl('css/component_iphone.css') );
        XG_App::startSectionMarkerProcessing();
        $mainWidget->dispatch('embed', 'header_iphone', array($highlight, $title, $componentCss, null, null, null, $options));
    }

    /**
     *   Returns an array describing the iPhone-specific navigation links to be displayed
     *     based on the logged in user and the current set of enabled mozzles
     *
     *	@param string user Screen name of the user profile in context - navigation entries depend on the user
     *  @return array of arrays of (display name, link, key (for highlight), subnavigation arrays)
     */
    public static function getNavEntries($user = null) {
    	XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $enabledModules = XG_ModuleHelper::getEnabledModules();
        $mainWidget = W_Cache::getWidget('main');
        $profilesWidget = W_Cache::getWidget('profiles');
        $navEntries = array();

        // Main/Activity tab
	    if ($enabledModules['activity']) {
	    	$navEntries[] = array(xg_text('ACTIVITY'), W_Cache::getWidget('activity')->buildUrl('log', 'list'), 'activity', array());
	    }

	    // My Page tab
        $navEntries[] = array(xg_text('MY_PAGE_TAB_TEXT'), $profilesWidget->buildUrl('index', 'index'), 'page',
        	array(
        		array(xg_text('MY_PROFILE'), $profilesWidget->buildUrl('index', 'index'), 'profile'),
        		array(xg_text('MY_FRIENDS'), $profilesWidget->buildUrl('friend', 'list'), 'friends')
        	));

        // Members tab
        if ($user) {
        	$mainWidget->includeFileOnce('/models/User.php');
	        $navEntries[] = array(xg_text('MEMBERS_TAB_TEXT'), $profilesWidget->buildUrl('members', ''), 'users',
	        	array(
	        		array(xg_text('PROFILE'), xnhtmlentities(User::quickProfileUrl($user)), 'other_profile'),
	        		array(xg_text('FRIENDS'), xnhtmlentities(User::quickFriendsUrl($user)), 'other_friends')
	            ));
        } else {
	        $navEntries[] = array(xg_text('MEMBERS_TAB_TEXT'), $profilesWidget->buildUrl('members', ''), 'members',
	        	array(
	        		array(xg_text('BROWSE_ALL'), $profilesWidget->buildUrl('members', ''), 'all_members'),
	        		array(xg_text('FEATURED'), $profilesWidget->buildUrl('friend', 'listFeatured'), 'featured')
	            ));
        }

        // Forum tab
	    if ($enabledModules['forum']) {
	    	$navEntries[] = array(xg_text('FORUM_TAB_TEXT'), W_Cache::getWidget('forum')->buildUrl('index', 'index'), 'forum', array());
	    }

	    return $navEntries;
    }

    /**
     * Renders a previous page link depending on the current page number
     *
     * @param int $pageSize the number of items shown per page
     * @param string $linkText optional text to show in the links
     */
    public static function previousPage($pageSize, $linkText = null){
        $page = self::getPageNumber();
        if ($page > 1) {
        	$url = xg_url(XG_HttpHelper::currentUrl(), array('page' => $page-1));
        	$linkText = $linkText ? $linkText : xg_html('PREVIOUS_X_ITEMS', $pageSize);	?>
            <li class="previous" onclick="return window.location = '<%= $url %>';"><%= $linkText %></li>
        <?php
        }
    }

    /**
     * Renders a next page link depending on the showNextLink parameter
     *
     * @param boolean $showNextLink show the next link or not
     * @param int $pageSize the number of items shown per page
     * @param string $linkText optional text to show in the links
     */
    public static function nextPage($showNextLink, $pageSize, $linkText = null){
        $page = self::getPageNumber();
        if ($showNextLink) {
        	$url = xg_url(XG_HttpHelper::currentUrl(), array('page' => $page+1));
        	$linkText = $linkText ? $linkText : xg_html('NEXT_X_ITEMS', $pageSize); ?>
        	<li class="more" onclick="return window.location = '<%= $url %>';"><%= $linkText %></li>
        <?php
        }
    }

    /**
     * Returns the current page number, or 1 by default
     * Expects GET parameter 'page'
     *
     * @return int page number
     */
    public static function getPageNumber() {
    	return ($_GET['page']) ? $_GET['page'] : 1;
    }

    /**
     * Returns the url to a 120x120px image of the network logo
     *
     * @return string url of the image
     */
    public static function largeIconUrl() {
    	return XN_Application::load()->iconUrl(48, 48);
    }

    /**
     * Returns the url to a 24x24px image of the network logo
     *
     * @return string url of the image
     */
    public static function smallIconUrl() {
    	return XN_Application::load()->iconUrl(24, 24);
    }

    /**
     * Creates a 57x57 png file at the root of the network for bookmarking the network
     * on an iPhone.  If the bookmark icon already exists, just return the url.
     *
     * @return string url of the bookmark icon
     */
    public static function bookmarkIconUrl() {
    	$bookmarkIconFilename = NF_APP_BASE . '/apple-touch-icon.png';
    	if (!file_exists($bookmarkIconFilename)) {
    		W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_AppearanceHelper.php');
    		$iconUrl = XN_Application::load()->iconUrl(57, 57);
    		$image = Video_AppearanceHelper::createImage($iconUrl, $iconUrl);
    		if (!imagepng($image, $bookmarkIconFilename)) {
    			return false;
    		}
    	}
    	return '/apple-touch-icon.png';
    }

    /**
	 *  Updates iphone bookmark icon (must be called when the app icon is changed)
     *
     *  @return     void
     */
    public static function updateBookmarkIcon() {
    	$bookmarkIconFilename = NF_APP_BASE . '/apple-touch-icon.png';
    	if (file_exists($bookmarkIconFilename)) {
    		unlink($bookmarkIconFilename);
		}
		self::bookmarkIconUrl();
    }


    /**
     * Displays the iphone version of the list page.
     */
    public static function outputListPage($args) {
        foreach ($args as $key => $value) { ${$key} = $value; }
        if ($_GET['test_empty']) { $objects = $featuredObjects = array(); $numObjects = 0; }
        if (self::getPageNumber() > 1) { ?>
        	<ul class="list <%= $cssInfix %>">
        	<?php XG_IPhoneHelper::previousPage($pageSize); ?>
        	</ul>
        <?php
        }
        if (count($featuredObjects) && ! mb_strlen($_GET['q'])) { ?>
            <ul class="list <%= $cssInfix %>">
              <li class="title"><%= xnhtmlentities($featuredTitleText) %></li>
            <?php
            foreach($featuredObjects as $object) {
                W_Content::create($object)->render('featuredListItem_iphone', array(XG_LangHelper::lcfirst($object->type) => $object));
            }
            ?></ul><?php
        }

        if ($titleHtml) {
            if ($numObjects > 0 && mb_strlen($_GET['q'])) { $titleHtml = xg_html('SEARCH_RESULTS_N', $numObjects); }
            if ($numObjects == 0 && mb_strlen($_GET['q'])) { $titleHtml = xg_html('NO_RESULTS_FOR_SEARCH_TERM', xnhtmlentities($_GET['q'])); } ?>
            <ul>
              <li class="title"><%= $titleHtml %></li>
            </ul>
        <?php
        }
        ?>

        <ul class="list <%= $cssInfix %>">
            <?php
            foreach ($objects as $object) {
                W_Content::create($object)->render('listItem_iphone', array(XG_LangHelper::lcfirst($object->type) => $object));
            }
            XG_IPhoneHelper::nextPage($showNextLink, $pageSize); ?>
        </ul>
        <?php
        if (XG_App::appIsPrivate() || XG_GroupHelper::groupIsPrivate()) { $feedUrl = null; }
        if ($feedUrl) {
            xg_autodiscovery_link($feedUrl, $feedTitle, $feedFormat); ?>
        <?php
        }
    }

    /**
     * Shows a div block containing iphone styled error messages
     *
     * @param $errors 		array		list of error messages returned from the server
	 * @param $showAlways   bool		Show error dom nodes even if there are no errors.
     */
	public static function outputErrors($errors, $showAlways = false) {
		if (!$errors && !$showAlways) {
			return;
		}
?>
		<div id="compose_error" class="msg error"<%=$errors ? '' : ' style="display:none"'%>>
			<h2><%= xg_html('OOPS') %></h2>
			<?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
		</div>
<?php
    }
}
?>
