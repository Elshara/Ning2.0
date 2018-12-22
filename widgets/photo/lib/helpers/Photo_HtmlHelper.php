<?php

/**
 * Contains helper methods for dealing with HTML.
 */
class Photo_HtmlHelper {

    /**
	 *  Returns sub navigation menu.
     *
	 *	@param		$widget	W_Widget				Photo widget
	 *  @param      $add   	photo|album|none		What kind of "add content" link to show.
     *  @return     hash
     */
	public static function subMenu($widget, $add = 'photo') {
		$menu = array(
			'allPhotos'	=> array( 'name' => xg_html('ALL_PHOTOS'), 'url' => $widget->buildUrl('index', 'index') ),
			'allAlbums' => array( 'name' => xg_html('ALL_ALBUMS'), 'url' => $widget->buildUrl('album', 'list') ),
			'myPhotos'	=> array( 'name' => xg_html('MY_PHOTOS'),  'url' => $widget->buildUrl('photo', 'listForContributor', array('screenName' => XN_Profile::current()->screenName)) ),
			'myAlbums'	=> array( 'name' => xg_html('MY_ALBUMS'),  'url' => $widget->buildUrl('album', 'listForOwner', array('screenName' => XN_Profile::current()->screenName)) ),
			'myFavs'	=> array( 'name' => xg_html('MY_FAVORITES'),'url'=> $widget->buildUrl('photo', 'listFavorites', array('screenName' => XN_Profile::current()->screenName)) ),
		);
		switch($add) {
			case 'none': break;
			case 'album':
				$menu['add'] = array( 'name' => xg_html('ADD_AN_ALBUM'), 'url' => $widget->buildUrl('album', 'new'), 'add' => 1 );
				break;
			case 'photo':
			default:
				$menu['add'] = array( 'name' => xg_html('ADD_PHOTOS'), 'url' => $widget->buildUrl('photo', XG_MediaUploaderHelper::action()), 'add' => 1 );
				break;
		}
		return $menu;
	}

    /**
	 *  Returns the slideshow link.
     *
     *  @param      $args	hash	Options
     *  @return     string
     */
    public function slideshowLink($args) {
		if ($args['feed_url']) {
			$url = W_Cache::getWidget('photo')->buildUrl('photo', 'slideshow', array('feed_url' => $args['feed_url']));
		} else {
			list ($url,$feedUrl) = Photo_SlideshowHelper::urls($args);
		}
		return '<a href="' . qh($url) . '" class="desc play">' . xg_html('VIEW_SLIDESHOW') . '</a>';
    }


    public static function excerpt($description, $maxLength, $url = null, &$excerpted = null) {
        // updated to use strip_tags rather than xnhtmlentities
        // updated to use the mb_ functions, and to allow for the omitting of the url
        if (mb_strlen(strip_tags($description)) <= $maxLength) {
            $excerpted = false;
            $result = strip_tags($description);
        } else {
            $excerpted = true;
            $result = strip_tags(mb_substr($description, 0, $maxLength-3));
            if ($url) {
                $result = $result . '<a href="' . xnhtmlentities($url) . '">...</a>';
            } else {
                $result = $result . '...';
            }
        }
        return $result;
    }

    public static function formattedDate($dateString) {
        // Pass it through date to normalize the date (padding with zeros and
        // fixing impossible dates like February 30). [Jon Aquino 2005-09-23]
        return date(xg_text('M_J_Y'), strtotime($dateString));
    }

    public static function prettyDate($date) {
        return xg_elapsed_time($date);
    }

    public static function pagination($total, $pageSize) {
        $currentPage = $_GET['page'] ? $_GET['page'] : 1;
        $vars = $_GET;
        unset($vars['popDownMessage']);
        unset($vars['page']);
        $path = preg_replace('/\?.*/u', '', Photo_HttpHelper::currentUrl());
        $path .= '?' . http_build_query($vars);
        $path = preg_replace('/&$/u', '', $path);
        $pageCount = ceil($total / $pageSize);
        return array('targetUrl' => $path, 'pageParamName' => 'page', 'curPage' => $currentPage, 'numPages' => $pageCount);
    }

    public static function avatar($screenName, $extent) {
        if ($screenName) {
            $src = XG_UserHelper::getThumbnailUrl(Photo_FullNameHelper::profile($screenName), $extent, $extent);
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

    public static function linkedScreenName($screenName, $sayMeForCurrentUser = false) {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $url = XG_HttpHelper::profileUrl($screenName);
        return '<a class="fn url" href="' .  xnhtmlentities($url) . '">' . xnhtmlentities($sayMeForCurrentUser && $screenName == XN_Profile::current()->screenName ? xg_text('ME') : Photo_FullNameHelper::fullName($screenName)) . '</a>';
    }

    public static function userPhotosLink($screenName) {
        $text = $text ? $text : xg_html('VIEW_PHOTOS') . "&nbsp;&#187;";
        return '<a href="' . xnhtmlentities(W_Cache::current('W_Widget')->buildUrl('user', 'show', '?screenName=' . $screenName)) . '">' . $text . '</a>';
    }

    public static function hyperlinkUrls($source) {
        return xg_linkify($source);
    }

    public static function cleanText($source) {
        // PHO-603, VID-478 [Jon Aquino 2007-03-02]
        return xg_scrub(self::hyperlinkUrls($source));
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
     * Determines the url, width and height to use for an IMG tag. Also takes care of rotation.
     *
     * @param $photo  Photo  The photo
     * @param $imgUrl string Will receive the url to use for an IMG tag (without width or height)
     * @param $width    int  Will receive the width of the image
     * @param $height    int Will receive the height of the image
     */
    public static function getImageUrlAndDimensions($photo, &$imgUrl, &$width, &$height) {
        $imgUrl     = $photo->fileUrl('data');
        $dimensions = $photo->imageDimensions('data');

        // For now, we assume that the rotation is in 90 degree steps
        if ($photo->my->rotation && (($photo->my->rotation == 90) || ($photo->my->rotation == 270))) {
            $width  = $dimensions[1];
            $height = $dimensions[0];
        } else {
            $width  = $dimensions[0];
            $height = $dimensions[1];
        }
        if ($imgUrl && $photo->my->rotation && ($photo->my->rotation != 0)) {
            $imgUrl = self::addParamToUrl($imgUrl, 'transform', 'rotate(' . $photo->my->rotation . ')');
        }
    }

    /**
     * Determines the width and height to use for an IMG tag so that the given photo fits into the
     * specified thumb while preserving its aspect ratio.
     *
     * @param $photo  Photo       The photo
     * @param $thumbWidth    int  The width of the thumb
     * @param $thumbHeight    int The height of the thumb
     * @param $imgUrl string      Will receive the url to use for an IMG tag
     * @param $imgWidth    int    Will receive the calculated width of the image
     * @param $imgHeight    int   Will receive the calculated height of the image
     * @param boolean $squareCropIfLarge  Whether to crop the image to a square if it can be done without scaling it up
     */
    public static function fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, &$imgUrl, &$imgWidth, &$imgHeight, $squareCropIfLarge = false) {
        self::getImageUrlAndDimensions($photo, $imgUrl, $width, $height);
        if ($squareCropIfLarge) { self::squareCropIfLarge($imgUrl, $width, $height, $thumbWidth); }
        // If width and height don't get set by getImageUrlAndDimensions(),
        // set them to just bigger than the thumbnail box so that things are
        // scaled properly and width + height parameters are added to the URL
        // PHO-532 [ David Sklar 2006-10-09 ]
        if (! $width) { $width = $thumbWidth + 1; }
        if (! $height) { $height = $thumbHeight + 1; }
        //@TODO use XG_ImageHelper::getDimensionsScaled($width, $height, $thumbWidth, $thumbHeight, false) instead
        $imgWidth   = null;
        $imgHeight  = null;
        if (($width > $thumbWidth) && ($height > $thumbHeight)) {
            $widthFac  = $thumbWidth / $width;
            $heightFac = $thumbHeight / $height;
            if ($widthFac < $heightFac) {
                $imgWidth  = $thumbWidth;
                $imgHeight = (int)($height * $widthFac);
            } else {
                $imgWidth  = (int)($width * $heightFac);
                $imgHeight = $thumbHeight;
            }
        } else if ($width > $thumbWidth) {
            $widthFac  = $thumbWidth / $width;
            $imgWidth  = $thumbWidth;
            $imgHeight = (int)($height * $widthFac);
        } else if ($height > $thumbHeight) {
            $heightFac = $thumbHeight / $height;
            $imgWidth  = (int)($width * $heightFac);
            $imgHeight = $thumbHeight;
        }
        // ImageMagick failure can cause returned dimensions to be (incorrectly) 100x100,
        // which may be less than thumbWidth and thumbHeight. So explicitly add width and height
        // parameters even if the returned dimensions are within thumbWidth and thumbHeight,
        // otherwise the unsized image may be very large (BAZ-372) [Jon Aquino 2006-12-05]
        $imgWidth = $imgWidth ? $imgWidth : $width;
        $imgHeight = $imgHeight ? $imgHeight : $height;
        $imgUrl = self::addParamToUrl($imgUrl, 'width', $imgWidth);
        $imgUrl = self::addParamToUrl($imgUrl, 'height', $imgHeight);
    }

    /**
     * Modifies the image URL, width, and height values to be square-cropped
     * if both the width and height exceed the crop extent (i.e. no scaling up).
     *
     * @param $url  the image URL
     * @param $width  the image width, in pixels
     * @param $height  the image height, in pixels
     * @param $cropExtent  the desired side length of the square crop
     */
    protected function squareCropIfLarge(&$url, &$width, &$height, $cropExtent) {
        if ($width < $cropExtent || $height < $cropExtent) { return; }
        $width = $height = $cropExtent;
        $url = XG_HttpHelper::addParameters($url, array('crop' => '1:1', 'width' => $width, 'height' => $height));
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
        if (XG_App::appIsPrivate()) { return; }
        XG_App::addToSection('<link rel="alternate" type="application/rss+xml" title="'.xnhtmlentities($title . ' - ' . XN_Application::load()->name).'" href="'.xnhtmlentities($url).'" />');
    }

    /**
     * Returns sorting metadata
     *
     * @param $sorts array  an array of arrays, each with name and alias
     * @param $currentSortAlias string  alias for the currently selected sort, or null to select the first sort
     * @param $currentUrl string  the URL of the current page, or null to determine it automatically
     * @return array  an array of arrays, each with displayText, url, and selected
     */
    public static function toSortOptions($sorts, $currentSortAlias, $currentUrl = null) {
        if (! $currentUrl) { $currentUrl = XG_HttpHelper::currentUrl(); }
        if (! $currentSortAlias) {
            $firstSort = reset($sorts);
            $currentSortAlias = $firstSort['alias'];
        }
        $sortOptions = array();
        foreach ($sorts as $metadata) {
            $sortOptions[] = array(
                    'displayText' => $metadata['name'],
                    'url' => XG_HttpHelper::addParameters($currentUrl, array('sort' => $metadata['alias'], 'page' => null)),
                    'selected' => $metadata['alias'] == $currentSortAlias);
        }
        return $sortOptions;
    }

}
