<?php

/**
 * Small snippets of HTML. Use partials ("fragments") instead for larger HTML chunks/modules.
 *
 * @see test/HtmlHelperTest.php
 */
class Video_HtmlHelper {

    /**
	 *  Returns sub navigation menu.
     *
	 *	@param		$widget	W_Widget				Video widget
	 *  @param      $add   	video|none				What kind of "add content" link to show.
     *  @return     hash
     */
	public static function subMenu($widget, $add = 'video') {
		$menu = array(
			'allVideos'	=> array( 'name' => xg_html('ALL_VIDEOS'), 'url' => $widget->buildUrl('video', 'index') ),
			'myVideos'	=> array( 'name' => xg_html('MY_VIDEOS'),  'url' => $widget->buildUrl('video', 'listForContributor', array('screenName' => XN_Profile::current()->screenName) ) ),
			'myFavs'	=> array( 'name' => xg_html('MY_FAVORITES'),'url'=> $widget->buildUrl('video', 'listFavorites', array('screenName' => XN_Profile::current()->screenName)) ),
		);
		switch($add) {
			case 'none': break;
			case 'video':
			default:
				$menu['add'] = array( 'name' => xg_html('ADD_A_VIDEO'), 'url' => $widget->buildUrl('video', XG_MediaUploaderHelper::action()), 'add' => 1 );
				break;
		}
		return $menu;
	}

    /** @see xg_excerpt */
    public static function excerpt($description, $maxLength, $url = null, &$excerpted = null) {
        return xg_excerpt($description, $maxLength, $url, $excerpted);
    }

    /**
     * Return a snippet of text suitable for an image's alt text, based on the object's title and description.
     *
     * @param $object string The content object on whose attributes the alt text will be based
     * @return string The alt text, or an empty string if alt text could not be generated
     */
    public static function alternativeText($object) {
        return $object->title ? self::excerpt($object->title, 100) : ($object->description ? self::excerpt($object->description, 100) : '');
    }

    public static function formattedDate($dateString) {
        // Pass it through date to normalize the date (padding with zeros and
        // fixing impossible dates like February 30). [Jon Aquino 2005-09-23]
        return date(xg_text('M_J_Y'), strtotime($dateString));
    }



    public static function prettyDate($date) {
        return xg_elapsed_time($date);
    }



    // From ningbar/controllers/SearchControllerClass.php  [Jon Aquino 2006-07-03]
    public static function pages($total, $currentPage, $pageCount) {
        // The pagination links display the first 3 pages, then links to the
        // 7 around the current page , then links to the last 3
        // In practice this means:
        // If there are <= 13 pages, links to all pages are shown.
        // For > 13 pages, link to first 3,
        //   (current/2 - 3) -> (current/2 + 3), last 3

        // No pagination info for just 1 page
        if ($pageCount < 2) { return array(1); }

        // Display all pages if there are not more than 13 pages
        if ($pageCount <= 13) {
            $pages = range(1,$pageCount);
        }
        // Figure out where to insert the breaks
        else {
            // First three
            foreach (array(1,2,3) as $i) { $pageNumbers[$i] = true; }

            if ($currentPage < 4) {
                $pivot = 4;
            } else if ($currentPage >= ($pageCount - 3)) {
                $pivot = $pageCount - 4;
            } else {
                $pivot = $currentPage;
            }

            // Middle
            for ($i = $pivot - 3; $i <= $pivot + 3; $i++) {
                $pageNumbers[$i] = true;
            }

            // Last three
            foreach (range($pageCount-2,$pageCount) as $i) {
                $pageNumbers[$i] = true;
            }
            $pages = array_keys($pageNumbers);
        }
        return $pages;
    }

    public static function pagination($total, $pageSize, $url = null) {
        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        return XG_PaginationHelper::computePagination($total, $pageSize, $url);
    }

    public static function avatar($screenName, $extent) {
        if ($screenName) {
            $src = XG_UserHelper::getThumbnailUrl(Video_FullNameHelper::profile($screenName), $extent, $extent);
        } else {
            $src = '/images/icons/default-avatar-' . $extent . 'x' . $extent . '.png';
        }
        $html = '<img class="photo" src="' . xnhtmlentities($src) . '" alt="" width="' . $extent . '" height="' . $extent . '" />';
        if ($screenName) {
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $url = XG_HttpHelper::profileUrl($screenName);
            $html = '<a class="fn url" href="' . xnhtmlentities($url) . '">' . $html . '</a>';
        }
        return $html;
    }

    /**
     * Return the HTML for the person's full name, linked to their profile page or videos page.
     *
     * @param $screenName string  The person's username.
     * @param $sayMeForCurrentUser boolean  Whether to output "Me" instead of the person's full name. Defaults to FALSE.
     * @param $linkToProfilePage boolean  Whether to link to the person's profile page or their videos page. Defaults to TRUE (profile page).
     * @param string $class Allows an overriding class to be set for the anchor tag.
     */
    public static function linkedScreenName($screenName, $sayMeForCurrentUser = FALSE, $linkToProfilePage = TRUE, $class = null) {
        if ($linkToProfilePage) {
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $url = XG_HttpHelper::profileUrl($screenName);
        } else {
            $url = W_Cache::current('W_Widget')->buildUrl('video', 'listForContributor', array('screenName' => $screenName));
        }
        $class = is_null($class) ? 'fn url' : $class;
        return '<a class="'. $class .'" href="' . xnhtmlentities($url) . '">' . xnhtmlentities($sayMeForCurrentUser && $screenName == XN_Profile::current()->screenName ? xg_text('ME') : Video_FullNameHelper::fullName($screenName)) . '</a>';
    }

    public static function hyperlinkUrls($source) {
        return xg_linkify($source);
    }

    public static function cleanText($source) {
        return self::scrub(self::hyperlinkUrls($source));
    }

    public static function scrub($html) {
        // Preserve newlines (VID-258)  [Jon Aquino 2006-09-05]
        return xg_scrub($html);
    }

    public static function averageRatingWithUserSummary($averageRating, $ratingCount, $userRating, $showRatingLabel = true) {
        if ($ratingCount == 0) {
            $ratingString = $showRatingLabel ? xg_html('RATING_NOT_RATED_YET') : xg_html('NOT_RATED_YET');
        } else {
            $ratingString = $showRatingLabel ? xg_html('RATING_Y_AFTER_X_VOTES', $ratingCount, self::stars($averageRating)) : xg_html('Y_AFTER_X_VOTES', $ratingCount, self::stars($averageRating));
        }
        if ($userRating > 0) {
            $ratingString .= "<em>" . xg_text('YOUR_RATING_X_STARS', $userRating) . "</em>";
        }
        return $ratingString;
    }

    public static function averageRating($averageRating, $ratingCount, $showRatingLabel = true) {
        if ($ratingCount == 0) {
            return $showRatingLabel ? xg_html('RATING_NOT_RATED_YET') : xg_html('NOT_RATED_YET');
        } else {
            return $showRatingLabel ? xg_html('RATING_Y_AFTER_X_VOTES', $ratingCount, self::stars($averageRating)) : xg_html('Y_AFTER_X_VOTES', $ratingCount, self::stars($averageRating));
        }
    }

    public static function stars($averageRating) {
        return xg_rating_image($averageRating);
    }

    /**
     * Adds a parameter to an url and returns the new url.
     *
     * @param url         The original url
     * @param paramName   The name of the parameter
     * @param paramValue  The value of the parameter
     * @param escapeParam Whether the parameter name and value shall be escaped via xnhtmlentities
     * @return The new url
     */
    public static function addParamToUrl($url, $paramName, $paramValue, $escapeParam = true) {
        $hasSlash   = ($lastSlashPos = @mb_strrpos($url, '/')) != false;
        $hasParams  = ($lastParamPos = @mb_strrpos($url, '?')) != false;
        $concatChar = '?';
        if ($hasParams && (!$hasSlash || ($lastParamPos > $lastSlashPos))) {
            $concatChar = '&';
        }
        return $url . $concatChar .
               ($escapeParam ? xnhtmlentities($paramName) : $paramName) . '=' .
               ($escapeParam ? xnhtmlentities($paramValue) : $paramValue);
    }

    /**
     * Used for urls in CSS, which are surrounded by parentheses.
     * Can't put quotes around the URL, otherwise older versions of Safari will not show the background image.
     * @param $x e.g. http:// ... ?rotate(270)
     */
    public static function urlencodeParentheses($x) {
        return str_replace('(', '%28', (str_replace(')', '%29', $x)));
    }

    public static function outputFeedAutoDiscoveryLink($url, $title) {
        xg_autodiscovery_link($url, $title);
    }
}
