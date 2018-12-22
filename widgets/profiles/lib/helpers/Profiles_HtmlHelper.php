<?php

/**
 * Useful functions for HTML output.
 */
class Profiles_HtmlHelper {

    /**
	 *  Returns sub navigation menu.
     *
	 *	@param		$widget	W_Widget				Profiles widget
	 *  @param      $add   	post|none				What kind of "add content" link to show.
     *  @return     hash
     */
	public static function blogSubMenu($widget, $add = 'post') {
		$menu = array(
			'allPosts'	=> array( 'name' => xg_html('ALL_BLOG_POSTS'), 'url' => $widget->buildUrl('blog', 'list') ),
			'myBlog'	=> array( 'name' => xg_html('MY_BLOG'),  'url' => $widget->buildUrl('blog', 'list', array('my' => XN_Profile::current()->isLoggedIn() ? null : 1, 'user' => XN_Profile::current()->screenName)) ),
		);
		switch($add) {
			case 'none': break;
			case 'post':
			default:
				$menu['add'] = array( 'name' => xg_html('ADD_NEW_BLOG_POST'), 'url' => $widget->buildUrl('blog', 'new'), 'add' => 1 );
				break;
		}
		return $menu;
	}

    /**
     * Convert the tag names to a list of anchor tags separated by commas.
     *
     * @param $tags array  The tag names
     * @return string  An HTML string
     */
    public static function tagLinks($tags) {
        $widget = W_Cache::current('W_Widget');
        $links = array();
        foreach ($tags as $tag) {
            $links[] = '<a href="' . xnhtmlentities(XG_GroupHelper::buildUrl('profiles', 'blog', 'list', array('tag' => $tag))) . '">' . xnhtmlentities(xg_excerpt($tag, 30)) . '</a>';
        }
        return implode(', ', $links);
    }

    /**
     * Returns HTML for the tag display for the blog-post detail page.
     *
     * @param $tags array  The blog post's most popular tag names
     * @return The HTML, suitable for use as innerHTML for a <p> element
     */
    public static function tagHtmlForDetailPage($tags) {
        return count($tags) ? xg_html('TAGS_X_NO_STRONG', Profiles_HtmlHelper::tagLinks(array_slice($tags, 0, BlogPost::TOP_TAGS_COUNT))) : '';
    }

    /**
     * Returns HTML for the tabs for the My Friends and Friend Requests pages.
     *
     * @param $selected string  name of the selected tab: friends, received, or sent
	 * @param $numOfFriends int	The total number of friends. We pass it here, to avoid making extra query
     */
    public static function tabsHtml($selected, $numOfFriends) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        $sent = Profiles_FriendHelper::instance()->getSentFriendRequestCount();
        $received = Profiles_FriendHelper::instance()->getReceivedFriendRequestCount();
        if (! $sent && ! $received) { return ''; }
        $tabs = array('friends' => array('label' => xg_html('MY_FRIENDS') . ($numOfFriends ? " ($numOfFriends)" : ""), 'url' => User::quickFriendsUrl(XN_Profile::current()->screenName)));
        if ($received) { $tabs['received'] = array('label' => xg_html('FRIEND_REQUESTS_N', 'class="xj_count"', $received), 'url' => W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listReceived')); }
        if ($sent) { $tabs['sent'] = array('label' => xg_html('REQUESTS_SENT_N', 'class="xj_count"', $sent), 'url' => W_Cache::getWidget('profiles')->buildUrl('friendrequest', 'listSent')); }
        $tabsHtml = '<ul class="page_tabs xg_lightborder">';
        foreach ($tabs as $name => $tab) {
            if ($name == $selected) {
                $tabsHtml .= '<li class="this"><span class="xg_tabs xg_lightborder">';
                if (! in_array($name, array('received','sent'))) {
                    $tabsHtml .= $tab['label'];
                } else {
                    $tabsHtml .= '<span class="xj_count_friendrequests'.$name.' xj_count_friendrequests'.$name.'_n xj_count_friendrequests'.$name.'_0 xj_count_friendrequests'.$name.'_1">' . $tab['label'] . '</span>';
                }
                $tabsHtml .= '</span></li>';
            } else {
                $tabsHtml .= '<li><a href="' . xnhtmlentities($tab['url']) . '">' . $tab['label'] . '</a></li>';
            }
        }
        $tabsHtml .= '</ul>';
        return $tabsHtml;
    }

    /**
     *  Returns the standardized headline for the friend(s) requests pages
     *
     *  @param      $title              string          Page title
     *  @param      $user               XN_Profile      Page owner
     *  @param      $count              interger        Number of friends
     *
     *  @return     string
     */
    public static function friendsHeadline($title, $user, $count) {
		return xg_headline($title, array(
			'avatarUser' => $user,
			'count' => $count,
			'byline1Html' => ($user->screenName == XN_Profile::current()->screenName) && ($count > 0)
				? '<a href="' . W_Cache::getWidget('profiles')->buildUrl('message', 'new', array('allFriends' => 1)) . '" class="sendmessage desc">' . xg_html('SEND_MESSAGE_TO_FRIENDS') . '</a>'
				: NULL,
		));
    }

    /**
	 *  Returns the standartized search for for the friend requests pages
     *
     *  @return     string
     */
    public static function friendsSearchForm() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserSort.php');
        $sortOptions = array();
        $currentUrl = xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('friend', 'list', array('user' => User::profileAddress(XN_Profile::current()->screenName))));
        foreach (array('mostRecent', 'alphabetical', 'random') as $sortName) {
            $sort = Profiles_UserSort::get($sortName);
            $sortOptions[] = array(
                    'displayText' => $sort->getDisplayText(),
                    'url' => XG_HttpHelper::addParameters($currentUrl, array('sort' => $sort->getId(), 'page' => null)),
                    'selected' => false);
        }
		return XG_PageHelper::searchBar(array(
			'url' => User::quickFriendsUrl(XN_Profile::current()->screenName),
			'buttonText' => xg_text('SEARCH_FRIENDS_NO_COLON'),
			'sortOptions' => $sortOptions,
		));
    }

}
