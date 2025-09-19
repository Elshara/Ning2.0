<?php

// TODO: Split up this file, e.g., XG_TagTemplateHelpers, etc.

/**
 * This file includes functions defined in the global namespace which should
 * be of use in generating templates.
 */
function xg_varDump($var, $comment = NULL) {
    echo "<!-- " . (isset($comment) ? $comment . ' ' : '');
    var_dump($var);
    echo " -->\n";
}

function xg_getVarDump($var, $comment = NULL) {
    ob_start();
    xg_varDump($var, $comment);
    $retval = ob_get_contents();
    ob_end_clean();
    return $retval;
}

/**
 * Generates a consistent, well-styled headline in Bazel per:
 * http://deshuy36.xna.ningops.net/_docs/guide-headline.php
 *
 * @param $title        string          The title displayed in the headline
 * @param $options      Array           (optional) Associative array of options, described below:
 *         avatarUser   XN_Profile|     A reference to a user whose avatar should be shown; XN_Profile,
 *                       XN_Content|        User content object, or the user's screenName are acceptable.
 *                       string
 *         avatarSize   integer         Size of the avatar image in pixels (default: 64)
 *         count        integer         A count to be included with the title, such as for search results
 *         countKey     string          A unique key per page that allows the count to be updated
 *                                          dynamically via Javascript.  See xg.shared.CountUpdater for details.
 *         showZero     boolean         If the count is <= 0, show it anyway against our style guide
 *         bylinesHtml  Array(string)   One or two lines of HTML to be used as bylines in the headline
 *         byline1Html  string          Instead of passing by array, you can pass them individually
 *         byline2Html  string          Second byline
 *         rightNavHtml string          Right-side navigation to display in the headline
 *
 * @return              string          Headline HTML
 */
function xg_headline($title, $options = array()) {
    // set default values for some options
    if (! array_key_exists('showZero', $options)) { $options['showZero'] = false; }   // by default do not show <= 0 counts in title
    if (! array_key_exists('avatarSize', $options)) { $options['avatarSize'] = 64; }  // default avatar size is 64x64 pixels^2
    if (! array_key_exists('bylinesHtml', $options)) { $options['bylinesHtml'] = array(); }
    if (is_string($options['bylinesHtml'])) { $options['bylinesHtml'] = array($options['bylinesHtml']); }
    if ($options['byline1Html']) { $options['bylinesHtml'][] = $options['byline1Html']; }
	if ($options['byline2Html']) { $options['bylinesHtml'][] = $options['byline2Html']; }

    // the return value
    $headlineHtml = '';

    // build it
    $numBylines = count($options['bylinesHtml']);
    if ($numBylines > 2) {
        // max 2 bylines allowed
        $options['bylinesHtml'] = array_slice($options['bylinesHtml'], 0, 2);
        $numBylines = 2;
    }
    $headlineClasses = array('xg_headline');
    if ($options['avatarUser']) {
        $headlineClasses[] = 'xg_headline-img';
        $hasAvatar = true;
    } else {
        $hasAvatar = false;
    }
    $hasRightNav = array_key_exists('rightNavHtml', $options);
    if ($numBylines > 0) { $headlineClasses[] = 'xg_headline-' . $numBylines . 'l'; } // ell not the number one

    $headlineHtml .= "<div class=\"" . implode(' ', $headlineClasses) . "\">\n";
    if ($hasAvatar) {
        // why does xg_avatar echo to stdout anyway?
        ob_start();
        xg_avatar($options['avatarUser'], $options['avatarSize']);
        $avatarHtml = ob_get_clean();
        $headlineHtml .= "    <div class=\"ib\">" . $avatarHtml . "</div>\n";
    }
    $headlineHtml .= "    <div class=\"tb\">\n        <h1>";
    if (! array_key_exists('count', $options)) {
        $headlineHtml .= xg_html('HEADLINE_TITLE', qh($title));
    } else {
        $showZero = array_key_exists('showZero', $options) && $options['showZero'];
        $count = max(intval($options['count']), 0);
        if (! array_key_exists('countKey', $options)) {
            // fixed count
            if (($count > 0) || $showZero) {
                $headlineHtml .= xg_html('HEADLINE_TITLE_COUNT', qh($title), xg_number($count), 'class="count"');
            } else {
                $headlineHtml .= xg_html('HEADLINE_TITLE', qh($title));
            }
        } else {
            // updatable count
            $countKey = $options['countKey'];
            if ($showZero) {
                $headlineHtml .= xg_html(
                    'HEADLINE_TITLE_COUNT_UPDATE_SHOWZERO',
                    qh($title),
                    xg_number($count),
                    'class="xj_count_'.$countKey.' xj_count_'.$countKey.'_n"',
                    'class="count"',
                    'class="xj_count"'
                );
            } else {
                $headlineHtml .= xg_html(
                    'HEADLINE_TITLE_COUNT_UPDATE',
                    qh($title),
                    xg_number($count),
                    'class="xj_count_'.$countKey.' xj_count_'.$countKey.'_0"' . ($count > 0 ? ' style="display:none;"' : ''),
                    'class="xj_count_'.$countKey.' xj_count_'.$countKey.'_n"' . ($count > 0 ? '' : ' style="display:none;"'),
                    'class="count"',
                    'class="xj_count"'
                );
            }
        }
    }
    $headlineHtml .= "</h1>\n";
    if (($numBylines > 0) || $hasRightNav) {
        $headlineHtml .= "        <ul class=\"navigation byline\">\n";
        foreach ($options['bylinesHtml'] as $byline) {
            $headlineHtml .= "            <li>" . $byline . "</li>\n";
        }
        if ($hasRightNav) {
            $headlineHtml .= "            <li class=\"right\">" . $options['rightNavHtml'] . "</li>\n";
        }
        $headlineHtml .= "        </ul>\n";
    }
    $headlineHtml .= "    </div>\n</div>\n";

    return $headlineHtml;
}

/**
 *  Inserts the app header.
 *
 *  @param highlight 	Name of tab to be highlighted (or NULL for none)
 *  @param title      Title of the page
 *  @param user       XN_Profile or User object of user owning current page (or NULL)
 *  @param options
 * 		otherMozzles: an array of mozzle names for which to include component CSS
 *     xgDivClass: optional additional CSS class for the xg div
 * 		additional options are implemented by the header action
 */
function xg_header($highlight = NULL, $title = NULL, $user = NULL, $options = NULL) {
    if (XG_GroupHelper::inGroupContext()) { $highlight = 'groups'; }
    $mainWidget = W_Cache::getWidget('main');
    $widget = W_Cache::current('W_Widget');
    if (!$widget) {
        $widget = $mainWidget;
    }
    if (!is_array($options)) {
        $options = array();
    }

    $componentCss = array( $widget->buildResourceUrl('css/component.css') );
    if (isset($options['otherMozzles'])) {
        $names = (is_array($options['otherMozzles']) ? $options['otherMozzles']
                : array($options['otherMozzles']));
        foreach ($names as $name) {
            $otherWidget = W_Cache::getWidget($name);
            if ($otherWidget) {
                $componentCss[] = $otherWidget->buildResourceUrl('css/component.css');
            }
        }
    }

    $mainWidget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');;
    $typographyCssUrl = $mainWidget->buildResourceUrl(Index_AppearanceHelper::getTypographyCssFile($mainWidget->config['typography']));

    //  Add user's css if a user has been specified
    if (isset($user)) {
        if ($user instanceof XN_Profile) {
            $user = User::load($user);
        }
        if ($user) {
            // BAZ-5004: fix bad profile-theme image URLs  [Jon Aquino 2007-10-17]
            $fixedFlag = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'fixedBaz5004');
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            if (Index_AppearanceHelper::getThemeCssUrl($user) && $user->my->$fixedFlag != 'Y' && XG_Cache::lock('fixing-baz-5004-for-' . $user->title)) {
                    Index_AppearanceHelper::setThemeCss($user, str_replace('url(/xn_resources', 'url(http://' . $_SERVER['HTTP_HOST'] . '/xn_resources', Index_AppearanceHelper::getThemeCss($user)));
                    $user->my->$fixedFlag = 'Y';
                    $user->save();
            }
            $options['userObject'] = $user;
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            $profileThemeCssUrl = Index_AppearanceHelper::getThemeCssFilename($user);
            $profileCustomCssUrl = Index_AppearanceHelper::getCustomCssFilename($user);
        }
    }
    XG_App::startSectionMarkerProcessing();
    $mainWidget->dispatch('embed', 'header', array($highlight, $title, $componentCss,
                           $typographyCssUrl, $profileThemeCssUrl, $profileCustomCssUrl, $options));
}

function xg_sidebar($controller, $onlySitewide = true, $isMemberProfilePage = false, $onlyUserBox = false) {
    if (! $controller) { throw new Exception("Controller not properly supplied to xg_sidebar."); }
    W_Cache::getWidget('main')->dispatch('embed', 'sidebar', array($onlySitewide, $isMemberProfilePage, $onlyUserBox));
}

/**
 *  Inserts the app footer.
 */
function xg_footer($extraHtml=null, $options = null) {
    $mainWidget = W_Cache::getWidget('main');
    if (XG_Browser::current() instanceof XG_Browser_Iphone) {
        $mainWidget->dispatch('embed', 'footer_iphone',array($extraHtml, $options));
    } else {
    $mainWidget->dispatch('embed', 'footer',array($extraHtml, $options));
    }
    XG_App::finishSectionMarkerProcessing();
}

/**
 * Returns a localized, HTML-encoded version of a message. The first argument is the message name, e.g., 'ADD_A_PHOTO'.
 * Subsequent arguments are substitution values (if the message contains sprintf format elements).
 * These arguments should be HTML-encoded, e.g., use &amp; instead of &.
 * You can use the xnhtmlentities() function to do the encoding.
 *
 * @param $name string the message name, e.g., 'CANCEL'
 * @param ... optional substitution strings and numbers
 * @return string the localized string, which will be HTML-encoded
 * @see XG_MessageCatalog_en_US.php
 */
function xg_html($name) {
    $args = func_get_args();
    static $search = array(' & ', '< ', ' >'); // Note the spaces [Jon Aquino 2008-02-02]
    static $replace = array(' &amp; ', '&lt; ', ' &gt;');
    return str_replace($search, $replace, XG_LanguageHelper::text($args));
}

/**
 * Returns a localized, plain-text version of a message. The first argument is the message name, e.g., 'ADD_A_PHOTO'.
 * Subsequent arguments are substitution values (if the message contains sprintf format elements).
 *
 * @param $name string the message name, e.g., 'CANCEL'
 * @param ... optional substitution strings and numbers
 * @return string the localized string, which will be plain text (not HTML-encoded)
 * @see XG_MessageCatalog_en_US.php
 */
function xg_text($name) {
    $args = func_get_args();
    return strip_tags(XG_LanguageHelper::text($args));
}

/**
 * Returns a localized version of the number - for example, 25000.00 is 25,000.00 in US and 25 000,00 in france.
 * Subsequent arguments are substitution values (if the message contains sprintf format elements).
 *
 * @param $number number to be
 * @param $decimal bool to indicate a decimal number or not, default is false
 * @param ... optional substitution strings and numbers
 * @return string the localized number
 */
function xg_number($number, $decimal=false) {
    return XG_LanguageHelper::number($number, $decimal);
}

/**
 * Returns a locale-dependent version of a url. The first argument is the url name, e.g., 'FLICKR_SUMMARY_SCREENSHOT'.
 * Subsequent arguments are substitution values (if the url contains sprintf format elements). Please note that these urls
 * are not xhtml encoded, rather it is up to the user to do that. Likewise, any argument needs to be properly
 * url encoded by the user before passing them in.
 *
 * @param $name string the url key, e.g., 'FLICKR_SUMMARY_SCREENSHOT'
 * @param ... optional substitution strings and numbers
 * @return string the url for this catalog
 */
function xg_localized_url($name) {
    $args = func_get_args();
    return XG_LanguageHelper::url($args);
}

/**
 * Return the preferred way of referring to a user (full name, then screen name)
 *
 * @param $p XN_Profile
 * @return string
 */
function xg_username($p) {
    $fullName = XG_UserHelper::getFullName($p);
    return mb_strlen($fullName) ? $fullName : $p->screenName;
}

// See Web Design Group, "HTML 4 Block-Level Elements", http://htmlhelp.com/reference/html40/block.html  [Jon Aquino 2007-03-31]
// Keep this list in sync with xg.shared.util.nl2br [Jon Aquino 2007-04-02]
if (!defined('XG_BLOCK_LEVEL_ELEMENT_PATTERN')) {
    define('XG_BLOCK_LEVEL_ELEMENT_PATTERN', 'XG_BLOCK_LEVEL_ELEMENT_PATTERN');
}
if (!defined(XG_BLOCK_LEVEL_ELEMENT_PATTERN)) {
    define(XG_BLOCK_LEVEL_ELEMENT_PATTERN, '(?:OBJECT|EMBED|PARAM|APPLET|IFRAME|SCRIPT|BR|ADDRESS|BLOCKQUOTE|CENTER|DIR|DIV|DL|FIELDSET|FORM|H1|H2|H3|H4|H5|H6|HR|ISINDEX|MENU|NOFRAMES|NOSCRIPT|OL|P|PRE|TABLE|UL|DD|DT|FRAMESET|LI|TBODY|TD|TFOOT|TH|THEAD|TR)');
}

/**
 * Replaces newlines with <br />s, except on lines with certain HTML elements like <p>
 *
 * @param $s string The original text or HTML
 * @return string  The text with <br />s inserted
 * @see xg.shared.util.nl2br
 */
function xg_nl2br($s) {
    $result = '';
    foreach (explode("\n", $s) as $line) {
        $result .= $line;
        if (! preg_match('/<.?' . XG_BLOCK_LEVEL_ELEMENT_PATTERN . '\b/iu', $line)) {
            $result .= '<br />';
        }
        $result .= "\n";
    }
    return preg_replace('/(<br \/>)+$/u', '', trim($result));
}

/**
 * Replaces <br> elements with newlines.
 *
 * @param $s string The original HTML
 * @return string  The HTML with <br> replaced by \n
 */
function xg_br2nl($s) {
    return preg_replace('@<br */?>@iu', "\n", $s);
}

/**
 * Return the HTML for an avatar image, linked to the person's profile page.
 *
 * @param $p XN_Profile|string  The person's profile object, or a string for the user name
 * @param $size integer  The width and height, in pixels
 * @param $imageClass string  CSS classes for the img tag
 * @param $imageAttributes string  Additional attributes for the img tag
 * @param $imageOnly boolean  True to show avatar with no link; default False
 * @return string  HTML for the person's avatar
 */
function xg_avatar($p, $size, $imageClass = 'photo', $imageAttributes = '', $imageOnly = false) {
    if (!$p || (($p instanceof XN_Profile) && $p->screenName === "")) return '';  // BAZ-10393 - for anonymous users.
    $image = '<img ' . ($imageClass ? 'class="' . $imageClass . '"' : '') . ' ' . $imageAttributes . ' src="' . xnhtmlentities(XG_UserHelper::getThumbnailUrl($p,$size,$size)) . '" height="' . $size . '" width="' . $size . '" alt="' . xnhtmlentities(xg_username($p)) . '" />';
    if (User::isMember($p)) {
        if (is_string($p)) {
            $screenName = $p;
        } elseif ($p instanceof XN_Profile) {
            $screenName = $p->screenName;
        } else {
            $screenName = $p->contributorName;
        }
    ?>
        <span class="xg_avatar"><a class="fn url" href="<%= xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenName)) %>"  title="<%= xnhtmlentities(xg_username($p)) %>"><%= $image %></a></span>
    <?php
    } elseif ($imageOnly) {
        echo $image;
    } else {
        echo '<span class="xg_avatar">'.$image.'</span>';
    }
}

/**
 * Return an appropriately linked user name. This returns HTML, so
 * the interpolated values are escaped inside the function.
 *
 * @param $p XN_Profile
 * @param $attributes string  Additional attributes for the anchor tag
 * @param $skipMemberCheck boolean  Whether to skip the query to check if the person is a member.
 *     You can set this to true for improved performance if you know that the person is a member.
 * @param $url string  Href for the anchor tag, or null to specify the person's profile page.
 */
function xg_userlink($p, $attributes = NULL, $skipMemberCheck = FALSE, $url = NULL) {
  if ($skipMemberCheck || User::isMember($p)) {
    $url = $url ? $url : User::quickProfileUrl($p->screenName);
    return '<a href="' . xnhtmlentities($url) . '"'
            . ($attributes ? " $attributes" : '')
            . '>' . xnhtmlentities(xg_username($p)) . '</a>';
  } else {
    return xnhtmlentities(xg_username($p));
  }
}

/**
 * Return a block of HTML showing the age and location of the specified profile
 *
 * @param $profile XN_Profile  the profile of the user
 * @param $singleLine boolean  whether to display the information on one line or two
 * @param $forceDisplayAge boolean  whether to display the person's age, even if User->my->displayAge is N
 */
function xg_age_and_location($profile, $singleLine = false, $forceDisplayAge = false) {
    if (XG_UserHelper::canDisplayAge($profile) || $forceDisplayAge) { $age = XG_UserHelper::getAge($profile); }
    if (XG_UserHelper::canDisplayGender($profile)) { $gender = (string) XG_UserHelper::getGender($profile); }
    return xg_age_and_location_proper($age, $gender, XG_UserHelper::getLocation($profile), XG_UserHelper::getCountry($profile), $singleLine);
}

/**
 * Return a block of HTML showing the age and location of the specified profile
 *
 * @param $age integer  the age in years, or null
 * @param $gender string  'm', 'f', or null
 * @param $location string  the city name, or null
 * @param $country string  2-letter country code, e.g., AU, or null
 * @param $singleLine boolean  whether to display the information on one line or two
 */
function xg_age_and_location_proper($age, $gender, $location, $country, $singleLine = false) {
    $ageAndLocation = '';
    if ($age) { $ageAndLocation .= $age; }
    if ($gender) {
        if ($age) { $ageAndLocation .= ', '; }
        if ($gender == 'f') {
            $ageAndLocation .= xg_html('FEMALE');
        } else {
            $ageAndLocation .= xg_html('MALE');
        }
    }
    if ($location || $country) {
        if ($age || $gender) {
            $ageAndLocation .= $singleLine ? ', ' : '<br/>';
        }
        if ($location) {
            $ageAndLocation .= xnhtmlentities($location);
            if ($country) {
                $ageAndLocation .= ', ';
            }
        }
        if ($country) {
            $ageAndLocation .= xnhtmlentities(xg_text('COUNTRY_' . $country));
        }
    }
    return $ageAndLocation;
}

/**
 * Return a date adjusted appropriately for the app's timezone setting
 *
 * @param $fmt string Date format string (what PHP's date() function understands)
 * @param $timestamp mixed optional timestamp to format. Can be either seconds
 * since epoch or an ISO8601-formatted time. Defaults to the current time
 * @return string
 */
function xg_date($fmt, $timestamp = null) {
    $widget = W_Cache::getWidget('main');
    if (is_null($timestamp)) {
        $timestamp = time();
    }
    elseif (! ctype_digit((string) $timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    /* Adjust the timestamp based on the app's tzOffset --  subtract since
     * tzOffset is minutes west of GMT */
    $localTimestamp = $timestamp - ($widget->config['tzOffset'] * 60);

    /* If the app observes DST and DST is currently active, adjust further */
    if ($widget->config['tzUseDST'] != 0) {
    /* From http://webexhibits.org/daylightsaving/b.html */
    /* In the EU, summer time begins at 0100 GMT on the last sunday in march
     * In the EU, summer time ends   at 0100 GMT on the last sunday in october
     * In the US (>= 2007) summer time begins at 0200 local on the second sunday in march
     * In the US (>= 2007) summer time ends   at 0200 local on the first sunday in november
     */

     /* If your tzOffset is one that the US uses, use the US dates for DST
         * switch, otherwise use the EU dates. This will need to be augmented
         * when we actually know a location and can use the appropriate switch
         * dates for that location
         * @todo: build some testcases for this DST math
         */
         if (($widget->config['tzOffset'] >= 300) && ($widget->config['tzOffset'] <= 540)) {
             list($localYr, $localMo, $localDy,$localHr) = explode(',',gmdate('Y,m,d,H', $localTimestamp));
             if ($localYr >= 2007) {
                 if (($localMo > 3) && ($localMo < 11)) {
                     /* Always add an hour in April - October */
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp += 3600;
                     } else {
                         $localTimestamp -= 3600;
                     }
                 } elseif ($localMo == 3) {
                     $dayToSwitch = 14 - floor(1 + $localYr * 5 / 4) % 7;
                     if (($localDy > $dayToSwitch) ||
                         (($localDy == $dayToSwitch) && ($localHr >= 2))) {
                             if ($widget->config['tzUseDST'] == 1) {
                                $localTimestamp += 3600;
                             } else {
                                 $localTimestamp -= 3600;
                             }
                     }
                 } elseif ($localMo == 11) {
                     $dayToSwitch = 7 - floor(1 + $localYr * 5 / 4) % 7;
                     if (($localDy < $dayToSwitch) ||
                     (($localDy == $dayToSwitch) && ($localHr < 2))) {
                         if ($widget->config['tzUseDST'] == 1) {
                            $localTimestamp += 3600;
                         } else {
                             $localTimestamp -= 3600;
                         }
                     }
                 }
             }
         } else {
             list($yr,$mo,$dy,$hr) = explode(',',gmdate('Y,m,d,H', $timestamp));
             if (($mo > 3)&&($mo < 10)) {
                 if ($widget->config['tzUseDST'] == 1) {
                    $localTimestamp += 3600;
                 } else {
                     $localTimestamp -= 3600;
                 }
             } elseif ($mo == 3) {
                 $dayToSwitch = (31 - (floor($yr * 5 / 4) + 4) % 7);
                 if (($dy > $dayToSwitch) || (($dy == $dayToSwitch) && ($hr >= 2))) {
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp += 3600;
                     } else {
                         $localTimestamp -= 3600;
                     }
                 }
             } elseif ($mo == 10) {
                 $dayToSwitch = (31 - (floor($yr * 5 / 2) + 1) % 7);
                 if (($dy < $dayToSwitch)||(($dy == $dayToSwitch) && ($hr < 1))) {
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp += 3600;
                     } else {
                         $localTimestamp -= 3600;
                     }
                 }
             }
         }
    }

    return gmdate($fmt, $localTimestamp);
}

/**
 * Returns an array of date options for forms with date inputs
 * @param $short Indicates use of short (3 char) month strings, otherwise use the full month name
 * @return array (year options, month options, day options)
 */
function xg_date_options($short = false) {
        $currentYear = date('Y', time());
        $yearOptions = array(xg_text('YEAR'));
        for ($i = $currentYear; $i >= $currentYear - 100 ; $i--) { $yearOptions[$i] = $i; }
        $suffix = $short ? '_SHORT' : '';
        $monthOptions = array(xg_text('MONTH'), xg_text("JANUARY$suffix"), xg_text("FEBRUARY$suffix"), xg_text("MARCH$suffix"), xg_text("APRIL$suffix"), xg_text("MAY$suffix"), xg_text("JUNE$suffix"), xg_text("JULY$suffix"), xg_text("AUGUST$suffix"), xg_text("SEPTEMBER$suffix"), xg_text("OCTOBER$suffix"), xg_text("NOVEMBER$suffix"), xg_text("DECEMBER$suffix"));
        $dayOptions = array(xg_text('DAY'));
        for ($i = 1; $i <= 31; $i++) { $dayOptions[$i] = $i; }
        return array($yearOptions, $monthOptions, $dayOptions);
}

/**
 * Return a timestamp given time parts in app-local time
 *
 * @param $hr
 * @param $min
 * @param $sec
 * @param $mon
 * @param $day
 * @param $yr
 * @return integer
 */
function xg_mktime($hr, $min, $sec, $mo, $dy, $yr) {

    $widget = W_Cache::getWidget('main');

    /* Adjust the timestamp based on the app's tzOffset -- this is in the opposite
     * direction as xg_date */
    $timestamp = gmmktime($hr, $min, $sec, $mo, $dy, $yr);
    $localTimestamp = $timestamp + ($widget->config['tzOffset'] * 60);

    /* If the app observes DST and DST is currently active, adjust further */
    if ($widget->config['tzUseDST'] != 0) {
    /* From http://webexhibits.org/daylightsaving/b.html */
    /* In the EU, summer time begins at 0100 GMT on the last sunday in march
     * In the EU, summer time ends   at 0100 GMT on the last sunday in october
     * In the US (>= 2007) summer time begins at 0200 local on the second sunday in march
     * In the US (>= 2007) summer time ends   at 0200 local on the first sunday in november
     */

     /* If your tzOffset is one that the US uses, use the US dates for DST
         * switch, otherwise use the EU dates. This will need to be augmented
         * when we actually know a location and can use the appropriate switch
         * dates for that location
         * @todo: build some testcases for this DST math
         * @todo: this is copied from xg_date() (except for the sign change) --
         * we should consolidate
         */
         if (($widget->config['tzOffset'] >= 300) && ($widget->config['tzOffset'] <= 540)) {
             list($localYr, $localMo, $localDy,$localHr) = array($yr, $mo, $dy, $hr);
             if ($localYr >= 2007) {
                 if (($localMo > 3) && ($localMo < 11)) {
                     /* Always subtract an hour in April - October */
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp -= 3600;
                     } else {
                         $localTimestamp += 3600;
                     }
                 } elseif ($localMo == 3) {
                     $dayToSwitch = 14 - floor(1 + $localYr * 5 / 4) % 7;
                     if (($localDy > $dayToSwitch) ||
                         (($localDy == $dayToSwitch) && ($localHr >= 2))) {
                             if ($widget->config['tzUseDST'] == 1) {
                                $localTimestamp -= 3600;
                             } else {
                                 $localTimestamp += 3600;
                             }
                     }
                 } elseif ($localMo == 11) {
                     $dayToSwitch = 7 - floor(1 + $localYr * 5 / 4) % 7;
                     if (($localDy < $dayToSwitch) ||
                     (($localDy == $dayToSwitch) && ($localHr < 2))) {
                         if ($widget->config['tzUseDST'] == 1) {
                            $localTimestamp -= 3600;
                         } else {
                             $localTimestamp += 3600;
                         }
                     }
                 }
             }
         } else {
             list($yr,$mo,$dy,$hr) = explode(',',gmdate('Y,m,d,H', $timestamp));
             if (($mo > 3)&&($mo < 10)) {
                 if ($widget->config['tzUseDST'] == 1) {
                    $localTimestamp -= 3600;
                 } else {
                     $localTimestamp += 3600;
                 }
             } elseif ($mo == 3) {
                 $dayToSwitch = (31 - (floor($yr * 5 / 4) + 4) % 7);
                 if (($dy > $dayToSwitch) || (($dy == $dayToSwitch) && ($hr >= 2))) {
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp -= 3600;
                     } else {
                         $localTimestamp += 3600;
                     }
                 }
             } elseif ($mo == 10) {
                 $dayToSwitch = (31 - (floor($yr * 5 / 2) + 1) % 7);
                 if (($dy < $dayToSwitch)||(($dy == $dayToSwitch) && ($hr < 1))) {
                     if ($widget->config['tzUseDST'] == 1) {
                        $localTimestamp -= 3600;
                     } else {
                         $localTimestamp += 3600;
                     }
                 }
             }
         }
    }

    return $localTimestamp;
}

/**
 * Convert applicable characters to XML entities.
 * In HTML contexts, use xnhtmlentities() instead.
 *
 * @param $s The string to convert.
 * @return The escaped string
 */
function xg_xmlentities($s) {
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

/**
 *  Excerpts the html (if necessary)
 *
 *  @param  $html       string      The HTML
 *  @param  $cutoff     integer     The maximum length of the description before truncation
 *  @param  $excerpted  boolean     Whether the text was truncated
 *  @return             string      The description HTML, possibly excerpted
 *
 * @TODO : Handle displayable text portion of html for length calculations  [2008-09-19 Mohan Gummalam]
 */
function xg_excerpt_html($html, $cutoff, &$excerpted = null) {
    if (mb_strlen($html) <= $cutoff) { return $html; }
    $excerpted = true;
    $html = mb_substr($html, 0, $cutoff);  // doesn't handle <script>, <style> etc tags...
    $lastTagOpen  = mb_strrpos($html, '<');
    $lastTagClose = mb_strrpos($html, '>');
    // If the last < is after the last >, then we've cut off in the middle
    // of a tag, and should backtrack to just before the last <
    if ($lastTagOpen > $lastTagClose) {
        $html = mb_substr($html, 0, $lastTagOpen);
    }
    // Now scrub to close any open tags, etc.
    $html = xg_scrub($html);
    // And add a nice ellipsis on to the end
    //  If the summary ends with a tag, put the ellipsis inside
    if (mb_substr($html, -1) === '>') {
        $lastTagOpen  = mb_strrpos($html, '<');
        $before = mb_substr($html, 0, $lastTagOpen);
        $after = mb_substr($html, $lastTagOpen);
        $html = $before . '&hellip;' . $after;
    } else {
        $html .= '&hellip;';
    }
    return $html;
}


/**
 * Excerpts the given text if necessary. HTML tags will be removed.
 *
 * @param $description string  The text to excerpt.
 * @param $maxLength string  The maximum length of the result, including ellipsis.
 * @param $url string  URL for an anchor tag to put around the ellipsis.
 * @param $excerpted boolean  Whether the text was excerpted.
 * @param $wordTruncation boolean  Whether to truncate words below the $wordTruncationLimit
 * @param $wordTruncationLimit integer  Max length for words, if $wordTruncation is specified
 * @return string  The excerpted text.
 */
function xg_excerpt($description, $maxLength, $url = null, &$excerpted = null, $wordTruncation = false, $wordTruncationLimit = 50) {
    // updated to use strip_tags rather than xnhtmlentities
    // updated to use the mb_ functions, and to allow for the omitting of the url
    // wordTruncation splits up the text and truncates words above the limit; this helps with things like long urls that
    // can otherwise mess up layouts.
    if ($wordTruncation) {
        $wordset = explode(' ', strip_tags($description));
        $updated = false;
        $newDescription = '';
        foreach($wordset as $word) {
            if (mb_strlen($word) > $wordTruncationLimit) {
                $word = mb_substr($word, 0, $wordTruncationLimit);
            }
            $updated = true;
            $newDescription .= $word . " ";
        }
        if ($updated) {
            $description = $newDescription;
        }
    }
    if (mb_strlen(strip_tags($description)) <= $maxLength) {
        $excerpted = false;
        $result = strip_tags($description);
    } else {
        $excerpted = true;
        $result = (mb_substr(strip_tags($description), 0, $maxLength-3));
        if ($url) {
            $result = $result . '<a href="' . xnhtmlentities($url) . '">...</a>';
        } else {
            $result = $result . '...';
        }
    }
    return $result;
}

/**
 * Inserts a feed autodiscovery link into the head of the document.
 *
 * @param $url string  The URL of the feed
 * @param $title string  The title of the feed
 * @param $format string  The feed format: rss or atom
 */
function xg_autodiscovery_link($url, $title, $format = 'rss') {
    if (! ($format == 'rss' || $format == 'atom')) { throw new Exception('Assertion failed (2138198541)'); }
    if (XG_App::appIsPrivate()) { return; }
    XG_App::addToSection('<link rel="alternate" type="application/'.$format.'+xml" title="'.xnhtmlentities($title . ' - ' . XN_Application::load()->name).'" href="'.xnhtmlentities($url).'" />');
}

/**
 * Returns a mailto: URL.
 *
 * @param $to string  email of the recipient, or an empty string
 * @param $subject  string  subject for the email
 * @param $body  string  body for the email
 * @return  the URL
 */
function xg_mailto_url($to, $subject, $body) {
    // Replace … with ..., as Outlook mangles it [Jon Aquino 2007-12-14]
    $subject = preg_replace('@…@u', '...', $subject);
    $body = preg_replace('@…@u', '...', $body);
    return 'mailto:' . $to . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body);
}

/**
 * Turns plain-text URLs into links.
 *
 * @param $s string The text to linkify
 * @return string  The text with plain-text URLs replaced by anchor tags
 */
function xg_linkify($s) {
    $not_anchor = '(?<!\'|"|=|>)';
    $protocol = '(?<!\w)(http|ftp|https):\/\/';
    $domain = '[\w]+(\.[\w]+)';
    $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
    $expr = '/' . $not_anchor . $protocol . $domain . $subdir . '/ui';
    $result = preg_replace( $expr, '<a href="$0">$0</a>', $s);
    $not_http = '(?<!:\/\/)';
    $domain = '(?<!\w)www(\.[\w]+)';
    $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
    $expr = '/' . $not_anchor . $not_http . $domain . $subdir . '/ui';
    $result = preg_replace( $expr, '<a href="http://$0">$0</a>', $result );
    return $result;

}

/**
 * Surrounds the given action-button text with a <span> tag with appropriate classes.
 *
 * @param $text string  HTML to put within the <span> tag
 * @param $additionalAttributes boolean  Additional attributes for the <span> tag, e.g., 'style="display: none"'
 * @param $additionalClasses string  Additional CSS classes for the <span> tag
 */
function xg_action_button_text($text, $additionalAttributes = '', $additionalClasses = NULL) {
    $class .= mb_stripos($text, '<br') !== FALSE ? 'split ' : '';
    $class .= $additionalClasses;
    $class = $class ? ' class="' . trim($class) . '"' : '';
    $additionalAttributes = $additionalAttributes ? ' ' . trim($additionalAttributes) : '';
    return '<span' . $additionalAttributes . $class . '>' . $text . '</span>';
}

/**
 * Scales down the dimensions of <embed> and <object> tags to fit within the given size limit.
 *
 * @param $html string  HTML which may contain <embed> or <object> tags
* @param $maxWidth integer  Maximum allowed width, in pixels. Set to NULL if you specify $columnCount instead.
 * @param $columnCount integer  Whether to use a standard max width for 1, 2, or 3 columns. Set to NULL if you specify $maxWidth instead.
 * @return string  The HTML with resized <embed> and <object> tags
 */
function xg_resize_embeds($html, $maxWidth = NULL, $columnCount = NULL) {
    if (mb_stripos($html, 'embed') === FALSE && mb_stripos($html, 'object') === FALSE) { return $html; }
    global $xg_max_embed_width;
    $xg_max_embed_width = $maxWidth ? $maxWidth : ($columnCount == 1 ? 206 : ($columnCount == 2 ? 438 : 800));
    // max 420, otherwise too big in IE in Video detail description field [Jon Aquino 2007-02-13]
    $html = preg_replace_callback('/(embed[^>]*)(\bwidth\b)([:=" ]*)(\d+)([^>]*)(\bheight\b)([:=" ]*)(\d+)/ui', 'xg_compute_embed_extent', $html);
    $html = preg_replace_callback('/(embed[^>]*)(\bheight\b)([:=" ]*)(\d+)([^>]*)(\bwidth\b)([:=" ]*)(\d+)/ui', 'xg_compute_embed_extent', $html);
    $html = preg_replace_callback('/(object[^>]*)(\bwidth\b)([:=" ]*)(\d+)([^>]*)(\bheight\b)([:=" ]*)(\d+)/ui', 'xg_compute_embed_extent', $html);
    $html = preg_replace_callback('/(object[^>]*)(\bheight\b)([:=" ]*)(\d+)([^>]*)(\bwidth\b)([:=" ]*)(\d+)/ui', 'xg_compute_embed_extent', $html);
    return $html;
}

/**
 * Callback for xg_resize_embeds(). Alters width and height values in the given array of regular-expression matched elements.
 *
 * @param $matches array  Regular-expression matched elements from xg_resize_embeds()
 * @return array  The matches with width and height values replaced
 */
function xg_compute_embed_extent($matches) {
    global $xg_max_embed_width;
    $widthIndex = mb_strtolower($matches[2]) == 'width' ? 4 : 8;
    $heightIndex = mb_strtolower($matches[2]) == 'height' ? 4 : 8;
    $width = $matches[$widthIndex];
    $height = $matches[$heightIndex];
    if ($width >= $xg_max_embed_width) {
        $newWidth = $xg_max_embed_width;
        $newHeight = intval($height * $newWidth / $width);
        $matches[$widthIndex] = $newWidth;
        $matches[$heightIndex] = $newHeight;
    }
    return implode('', array_slice($matches, 1, 8));
}

function xg_shorten_linkText($html) {
    return preg_replace('@>([^<]{67,67})[^<]{4,}</a>@ui', '>\1...</a>', $html);
}

/**
 *  Universal URL tool. Allows to add/remove parameters, make relative/absolute URLs and retreive current URL with and without parameters.
 *
 *  @param      $url			string|bool		Base URL. TRUE - current URL with all parameters. FALSE - current URL w/o parameters
 *  @param		$args			string|hash		Extra url parameters. NULL value means "remove parameter"
 *  @param		$options		string			"abs" - absolute url (default), "rel" - relative.
 *  @return     string
 */
function xg_url($url = true, $args = '', $options = 'abs') {
    if (is_bool($url)) {
        list($base,$defArgs) = explode('?', str_replace('/index.php', '', $_SERVER['REQUEST_URI']), 2);
        if (!$url) {
            $defArgs = '';
        }
        $rel  = $base;
        $abs  = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    } else {
        list($base, $defArgs) = explode('?', $url, 2);
        $i = parse_url($base);
        $abs = ( isset($i['scheme']) ? $i['scheme'] : ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')) . '://';
        if ( isset($i['user']) || isset($i['pass']) ) {
            if (isset($i['user'])) { $abs .= $i['user']; }
            if (isset($i['pass'])) { $abs .= ":$i[pass]"; }
            $abs .= '@';
        }
        $abs .= isset($i['host']) ? $i['host'] : $_SERVER['HTTP_HOST'];
        $rel = isset($i['path']) ? $i['path'] : '/';
    }

    $params = array();
    if (mb_strlen($defArgs)) {
        parse_str($defArgs,$params);
    }
    if (!is_array($args)) {
        parse_str($args,$args);
    }
    foreach($args as $k=>$v) {
        if (NULL === $v) {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    $params = $params ? '?' . http_build_query($params) : '';
    return ('abs' == $options ? $abs : '') . $rel . $params;
}

/**
 * Turns a relative URL into an absolute URL for an HTTP request to the current
 *   server.
 *
 * @param $url string Relative or absolute URL
 * @return string Absolute URL
 */
function xg_absolute_url($url) {
    $urlParts = parse_url($url);
    if (!isset($urlParts['host'])) {
        $url = $_SERVER['SERVER_NAME'] . XG_Browser::current()->rewriteUrl($url, true);
    }
    if (!isset($urlParts['scheme'])) {
        $url = 'http://' . $url;
    }
    return $url;
}

/**
 * Turns an absolute URL into a relative URL for an HTTP request to the current server
 *
 * @param url string  Relative or absolute URL
 *
 * @return string  relative URL
 */
function xg_relative_url($url) {
    $urlParts = parse_url($url);
    $relUrl = '';
    if (isset($urlParts['path'])) {
        $relUrl = $urlParts['path'];
    } else if (isset($urlParts['query'])) {
        $relUrl = '/';
    }
    if (isset($urlParts['query'])) {
        $relUrl .= '?' . $urlParts['query'];
    }
    if (isset($urlParts['fragment'])) {
        $relUrl .= '#' . $urlParts['fragment'];
    }
    return $relUrl;
}

/**
 * Converts the URL to an equivalent URL served by the Ning CDN.
 *
 * @param $url string  the URL to convert
 * @param $addVersionParameter boolean  whether to append the app's current version to the URL
 * @return string   the CDN version of the URL, or the original URL if a CDN equivalent could not be determined.
 */
function xg_cdn($url, $addVersionParameter = true) {
    $cdnUrl = preg_replace('@.*/xn_resources(.*)@u', 'http://' . XN_AtomHelper::HOST_APP('static') . '/' . XN_Application::load()->relativeUrl . '$1', $url);
    if ($addVersionParameter && $url !== $cdnUrl) { $cdnUrl = XG_Version::addVersionParameter($cdnUrl); }
    return $cdnUrl;
}

/**
 * Converts the URL to an equivalent URL served by Akamai.
 *
 * @param $url string  the URL to convert
 * @param $addVersionParameter boolean  whether to append the app's current version to the URL
 * @return string   the Akamai version of the URL, or the original URL if an Akamai equivalent could not be determined.
 * @deprecated 3.3  Use xg_cdn instead
 */
function xg_akamai_url($url, $addVersionParameter = true) {
    return xg_cdn($url, $addVersionParameter);
}

/**
 * Cleans up the given HTML and removes Javascript.
 *
 * @param $html string  The HTML to clean
 * @return string  Valid HTML with scripts removed
 */
function xg_scrub($html) {
    $result = str_replace('[original-br]', '<br/>', str_replace("<br/>", "\n", str_replace("\n", "", HTML_Scrubber::scrub(xg_nl2br(preg_replace('/<br ?\/?>/ui', '[original-br]', $html))))));
    // Hack: The list-style:none elements appear when the scrubber encounters "(newline)" between <li> elements [Jon Aquino 2007-03-02]
    $result = preg_replace('@<li style="list-style: none">\s*</li>@u', '', $result);
    // Change <div /> to <div></div> (VID-478)  [Jon Aquino 2006-09-06]
    $result = preg_replace('@></(br|hr|img)>@u', '/>', preg_replace('@<([a-z]+) ([^>]+)/>@u', '<${1} ${2}></${1}>', $result));
    // See Web Design Group, "HTML 4 Block-Level Elements", http://htmlhelp.com/reference/html40/block.html  [Jon Aquino 2007-03-31]
    $result = trim(preg_replace('@<(/object|/embed(?![^>]*>\s*<(param|/object))|/applet|/iframe|/script|/br|/address|/blockquote|/center|/dir|/div|dl|/dl|/fieldset|/form|/h1|/h2|/h3|/h4|/h5|/h6|/hr|/isindex|/menu|/noframes|/noscript|ol|/ol|/p|/pre|table|/table|ul|/ul|/dd|/dt|/frameset|/li|/tbody|/td|/tfoot|/th|/thead|/tr)\b[^>]*>\s*@u', "$0\n", $result));
    return $result;
}

/**
 * Outputs the given message, with the time since the first call to this function.
 * Used for performance tuning.
 */
function xg_output_time($message) {
    $now = microtime(true);
    static $start;
    static $previous;
    if (! $start) {
        $start = $now;
        $previous = $start;
    }
    if (round($now - $previous, 1)) { echo ' <div style="font-size: 20px; color: black; background: yellow;">'; }
    else { echo ' <div style="font-size: 20px; color: white; background: black;">'; }
    echo ' ***** ' . $message . ' ' . number_format($now - $start, 1);
    if (round($now - $previous, 1)) { echo ' (' . number_format($now - $previous, 1) . ' since previous)'; }
    echo '</div>';
    $previous = $now;
}

/**
 * Converts a date to an expression of elapsed time, e.g., 15 seconds ago.
 *
 * @param $date string  date that strtotime can parse, e.g., Feb 15, 1977.
 * @param $showingMonth boolean  (output) whether the month is shown
 * @param $monthDayFormat string  date format for month and day; or null for the default
 * @param $monthDayFormat string  date format for month, day, and year; or null for the default
 */
function xg_elapsed_time($date, &$showingMonth = false, $monthDayFormat = NULL, $monthDayYearFormat = NULL) {
    if(!$date) return "";
    $stamp = strtotime($date);
    $diff = time() - $stamp;
    if ($diff <= 0) {
        // Workaround for NING-2053 [Jon Aquino 2006-02-22]
        return xg_text('JUST_NOW');
    } elseif ($diff < 60) {
        return xg_text('N_SECONDS_AGO', $diff);
    } elseif ($diff < 3600) {
        $minutes = floor($diff/60);
        return xg_text('N_MINUTES_AGO', $minutes);
    } elseif ($diff < 86400) {
        $hours = floor($diff/60/60);
        $minutes = floor(($diff - 60*60*$hours)/60);
        return xg_text('N_HOURS_AGO', $hours);
    } elseif ($diff < 3600*48) {
        return xg_text('1_DAY_AGO');
    } elseif (date('Y') == date('Y',$stamp)) {
        // Suppress the hour/minute display to sidestep timezone issues [Jon Aquino 2006-02-17]
        $showingMonth = true;
        if (! $monthDayFormat) { $monthDayFormat = xg_text('M_J'); }
        return date($monthDayFormat, $stamp);
    } else {
        $showingMonth = true;
        if (! $monthDayYearFormat) { $monthDayYearFormat = xg_text('M_J_Y'); }
        return date($monthDayYearFormat, $stamp);
    }
}

/**
 * Returns HTML for an anchor tag that opens the Send Message box in the Ningbar.
 *
 * @param $screenName string  Username of the person to send a message to
 * @param $friendStatus string  The relationship (contact, friend, pending, requested, groupie,
 *         blocked, or not-friend), or null if it is not known (or has not been queried, for performance)
 * @param $text string  (optional) Text for the anchor tag
 * @param $cssClass string  (optional) Alternative CSS for the link
 * @param $target string  The URL to go to after the user fills out the Compose page, or null for the current URL
 * @return string  HTML for the anchor tag
 */
function xg_send_message_link($screenName, $friendStatus, $text = null, $cssClass = null, $target = null) {
	if ($screenName === NULL || $screenName === '' || XN_Profile::current()->screenName == $screenName) { return ''; }
    if (! XG_SecurityHelper::currentUserCanSendMessageTo($screenName, $friendStatus)) { return ''; }
    if (! $target) { $target = XG_HttpHelper::currentUrl(); }
    $html = $text ? xnhtmlentities($text) : xg_html('SEND_A_MESSAGE');
    XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
    W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_MessageHelper.php');
    $url = W_Cache::getWidget('profiles')->buildUrl('message', 'newFromProfile', array('screenName' => $screenName, 'target' => $target));
    $href = XN_Profile::current()->isLoggedIn() ? 'href="' . xnhtmlentities($url) . '"' : ('href="' . xnhtmlentities(XG_HttpHelper::signUpUrl($url)) . '"');
    return '<a class="'.($cssClass ? $cssClass : "sendmessage desc").'" ' . $href . '>' . $html . '</a>';
}

/**
 * Returns HTML for an anchor tag that sends a friend request, or a message
 * indicating how the person is related to you ("Is your friend", "Request sent", etc.).
 * If the current user's screenName is given, an empty string is returned.
 *
 * @param $screenName			string  The person's username
 * @param $friendStatus			string  The person's friend status ("friend", "pending", etc.)
 * @param $altClass			string  Alternative CSS class string, or null to use the default
 * @param $altRequestSentClasses	string  Alternative CSS class string, for the request sent case, overrides $altClass for this case only
 * @param $altFriendClasses		string	Alternative CSS class string, for the 'is your friend' case, overrides $altClass for this case only
 * @return				string  HTML for a link or a message
 */
function xg_add_as_friend_link($screenName, $friendStatus, $altClass = null, $altRequestSentClasses = null, $altFriendClasses = null) {
    XG_App::ningLoaderRequire('xg.shared.AddAsFriendLink');
    $requestSentClasses = '';
    if (isset($altRequestSentClasses)) {
	$requestSentClasses = $altRequestSentClasses;
    } else {
	$requestSentClasses = isset($altClass) ? $altClass : 'friend-pending desc';
    }
    $friendClasses = '';
    if (isset($altFriendClasses)) {
	$friendClasses = $altFriendClasses;
    } else {
	$friendClasses = isset($altClass) ? $altClass : '';
    }
    if (! User::isMember(XN_Profile::current())) {
        return '';
    } elseif ($screenName == XN_Profile::current()->screenName) {
        return '';
    } elseif ($friendStatus == 'friend') {
        $class = isset($friendClasses) ? $friendClasses : '';
        return '<span class="' . $class . '">' . xg_html('IS_YOUR_FRIEND') . '</span>';
    } elseif ($friendStatus == 'pending') {
        $class = $requestSentClasses;
        return '<span class="' . $class . '">' . xg_html('REQUEST_SENT') . '</span>';
    } else {  // even if $friendStatus == 'blocked'
        $class = isset($altClass) ? $altClass : 'addfriend desc';
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
        return '<a class="' . $class . '" href="#" dojoType="AddAsFriendLink" _requestSentClasses="' . $requestSentClasses . '" _name="' . xnhtmlentities(xg_username(XG_Cache::profiles($screenName))) . '"  _screenName="' . xnhtmlentities($screenName) . '" _maxMessageLength="' . FriendRequestMessage::MAX_MESSAGE_LENGTH . '" _friendLimitExceededMessage="' . xg_html('REACHED_LIMIT_N_FRIENDS', XG_App::constant('Profiles_FriendHelper::FRIEND_LIMIT')) . '"  _sentFriendRequestLimitExceededMessage="' . xg_html('REACHED_LIMIT_N_FRIEND_REQUESTS', XG_App::constant('Profiles_FriendHelper::SENT_FRIEND_REQUEST_LIMIT')) . '">' . xg_html('ADD_AS_FRIEND') . '</a>';
    }
}

/**
 *  Returns HTML for a pair of paragraph tags containing links for starting or
 *  stopping following of a specified content object.  One paragraph tag will be
 *  visible based on whether or not the current user is already following the
 *  object.
 *
 *  Suitable for use in the module footer on content detail pages.
 */
function xg_follow_unfollow_links($object) {
    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
    $isFollowed = Index_NotificationHelper::userIsFollowing($object);
    $followUrl = W_Cache::getWidget('main')->buildUrl('content', 'startFollowing',
            array('id' => $object->id, 'xn_out' => 'json'));
    $unfollowUrl = W_Cache::getWidget('main')->buildUrl('content', 'stopFollowing',
            array('id' => $object->id, 'xn_out' => 'json'));
    $rand = rand();
    XG_App::ningLoaderRequire('xg.shared.FollowLink');
    switch ($object->type) {
        case 'Group':
            $html = '<li><a href="#" dojoType="FollowLink"';
            $html .= ' _isFollowed="' . ($isFollowed ? '1' : '0') . '"';
            $html .= ' _addUrl="' . xnhtmlentities($followUrl) . '"';
            $html .= ' _removeUrl="' . xnhtmlentities($unfollowUrl) . '"';
            $html .= ' _addDescription=""';
            $html .= ' _removeDescription=""';
            $html .= ' _joinPromptText="' . xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnSave()) . '"';
            $html .= XG_JoinPromptHelper::promptAttributesForPending();
            $html .= ' _signUpUrl="' . XG_AuthorizationHelper::signUpUrl() . '"';
            $html .= '></a></li>';
            return $html;
        default:
            $html = '<p class="right"><a href="#" dojoType="FollowLink"';
            $html .= ' _isFollowed="' . ($isFollowed ? '1' : '0') . '"';
            $html .= ' _addUrl="' . xnhtmlentities($followUrl) . '"';
            $html .= ' _removeUrl="' . xnhtmlentities($unfollowUrl) . '"';
            $html .= ' _addDescription="' . xg_html('EMAIL_ME_WHEN_PEOPLE_REPLY') . '"';
            $html .= ' _removeDescription="' . xg_html('DO_NOT_EMAIL_ME_WHEN_PEOPLE_REPLY') . '"';
            $html .= ' _joinPromptText="' . xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnSave()) . '"';
            $html .= XG_JoinPromptHelper::promptAttributesForPending();
            $html .= ' _signUpUrl="' . XG_AuthorizationHelper::signUpUrl() . '"';
            $html .= '></a></p>';
            return $html;
    }
}

/**
 * Outputs the message then throws it as an exception.
 *
 * @param $message string  the error message
 * @param $displayTrace boolean  whether to echo the stack trace
 */
function xg_echo_and_throw($message, $displayTrace = false) {
    echo $message;
    $e = new Exception($message);
    if ($displayTrace) {
        echo '<pre>' . xnhtmlentities($e->getTraceAsString()) . '</pre>';
        error_log($e->getTraceAsString());
    }
    throw $e;
}

/**
 * Outputs the Send Message, Add Friend, and View All links used on detail pages.
 *
 * @param $contributorName string  screen name of the creator of the object shown on the detail page
 * @param $viewAllLinkUrl string  URL for the View All link that appears at the end
 * @param $viewAllLinkText string  text for the View All link
 */
function xg_message_and_friend_links($contributorName, $viewAllLinkUrl, $viewAllLinkText) {
	if ($contributorName === NULL || $screenName === '') { return ''; }
    XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
    $friendStatus = XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, $contributorName);
    $link = xg_send_message_link($contributorName, $friendStatus, xg_text('SEND_MESSAGE'));
    if ($link) { $links[] = $link; }
    if ($friendStatus != 'friend') { // BAZ-6327 [Jon Aquino 2008-02-28]
        $link = xg_add_as_friend_link($contributorName, $friendStatus);
        if ($link) { $links[] = $link; }
    }
    $links[] = '<a href="' . xnhtmlentities($viewAllLinkUrl) . '">' . xnhtmlentities($viewAllLinkText) . '</a>';
    return implode(' &nbsp ', $links);
}

/**
 * returns a linked list of tags as HTML
 *
 * @param comma delimited string or array of tags $tags
 * @param  $url string  A url that each tag will be linked to; tag = $tag will automatically be appended to the url
 * @param $max integer  The max number of tags to return; optional, defaults to size of tag array
 * @param $showMoreLinkIfNeeded boolean  Whether to show a More link to reveal tags past the $max
 * @return string  The HTML, or an empty string if there are no tags
 */
function xg_tag_links($tags, $url, $max = null, $showMoreLinkIfNeeded = false) {
    if (is_string($tags)) { $tags = XN_Tag::parseTagString(trim($tags)); }
    if (count($tags) == 0) { return ''; }
    $links = array();
    for ($i = 0; $i < count($tags); $i++) {
        $links[] = '<a href="' . xnhtmlentities(XG_HttpHelper::addParameter($url,'tag',$tags[$i])) . '">' . xnhtmlentities($tags[$i]) . '</a>';
    }
    return $showMoreLinkIfNeeded ? xg_links_with_more($links, $max) : implode(', ', array_slice($links, 0, $max));
}

/**
 * Outputs HTML for a link that reveals a small map, which the user clicks to choose a location.
 *
 * @param $locationInputId string  ID of the location input
 * @param $latitude array  name, ID, and value to use for the latitude input
 * @param $longitude array  name, ID, and value to use for the longitude input
 * @param $zoomLevel array  name, ID, and value to use for the zoomLevel input
 * @param $locationType array  name to use for the locationType input
 */
function xg_map_it_link($args) {
    XG_App::ningLoaderRequire('xg.shared.MapItLink'); ?>
    <div class="locationMap" dojoType="MapItLink" _locationInputId="<%= $args['locationInputId'] %>" _open="<%= mb_strlen($args['latitude']['value']) ? 'true' : 'false' %>">
        <a href="#"><%= xg_html('MAP_IT') %></a>
        <div class="mapbox xg_lightborder" style="display:none;">
            <div class="errordesc" style="display:none"></div>
            <div style="width:205px; height:205px;"></div>
            <p>
                <input type="text" class="textfield" />&nbsp;
                <input type="button" class="button" value="<%= xg_html('FIND') %>" />
            </p>
        </div>
        <input type="hidden" name="<%= xnhtmlentities($args['latitude']['name']) %>" id="<%= xnhtmlentities($args['latitude']['id']) %>" value="<%= xnhtmlentities($args['latitude']['value'] ? $args['latitude']['value'] : 25) %>"/>
        <input type="hidden" name="<%= xnhtmlentities($args['longitude']['name']) %>" id="<%= xnhtmlentities($args['longitude']['id']) %>" value="<%= xnhtmlentities($args['longitude']['value'] ? $args['longitude']['value'] : -40) %>"/>
        <input type="hidden" name="<%= xnhtmlentities($args['zoomLevel']['name']) %>" id="<%= xnhtmlentities($args['zoomLevel']['id']) %>" value="<%= xnhtmlentities($args['zoomLevel']['value'] ? $args['zoomLevel']['value'] : 1) %>"/>
        <input type="hidden" name="<%= xnhtmlentities($args['locationType']['name']) %>" value="<%= mb_strlen($args['latitude']['value']) ? 'latlng' : 'skip' %>"/>
    </div>
<?php
}

/**
 * Returns HTML for the location links. If $lat exists, call XG_MapHelper::outputScriptTag()
 * before xg_footer().
 *
 * @param $lat float  the latitude
 * @param $lng float  the longitude
 * @param $zoom float  the zoom level
 * @param $location string  the location string, e.g., Hawaii
 * @param $locationUrl string  URL to go to when the user clicks the location
 * @param $mapDiv string  (output) HTML for the div that will contain the map; this is a <div>, so make sure that it is not placed within a <p>
 * @return string  HTML, or null if the object contains no location information
 */
function xg_location_links($lat, $lng, $zoom, $location, $locationUrl, &$mapDiv) {
    if (mb_strlen($lat)) { XG_App::ningLoaderRequire('xg.shared.PopupMap'); }
    $locationLink = '<a href="' . xnhtmlentities($locationUrl) . '">' . xnhtmlentities($location) . '</a>';
    $mapLinkAttributes = 'href="#" dojoType="PopupMap" _lat="' . xnhtmlentities($lat) . '" _lng="' . xnhtmlentities($lng) . '" _zoom="' . xnhtmlentities($zoom) . '"';
    // div, not span, which causes errors in IE [Jon Aquino 2008-02-08]
    $mapDiv = '<div id="map_container" class="xg_lightborder" style="display:none; width:230px; height:230px;"></div>';
    // Insert the map div into the markup. IE gives us errors if we try to insert it with JavaScript [Jon Aquino 2008-02-08]
    if (mb_strlen($lat) && mb_strlen($location)) {
        return xg_html('LOCATION_LINK_SHOW_MAP', $locationLink, $mapLinkAttributes);
    } elseif (mb_strlen($location)) {
        return $locationLink;
    } elseif (mb_strlen($lat)) {
        return xg_html('SHOW_MAP', $mapLinkAttributes);
    }
    return null;
}

/**
 * Outputs an image showing 0-5 stars.
 *
 * @param $rating float  the rating, from 0 to 5
 * @param string  HTML for the <img>
 */
function xg_rating_image($rating) {
    if (! $rating) { $rating = 0; }
    $rating = round($rating * 2) / 2;
    return '<img class="rating" src="' . xnhtmlentities(xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/rating/rating' . $rating . '.gif'))) . '" alt="' . xg_html('N_OF_5_STARS', $rating) . '" />';
}

/**
 * Outputs a Dojo widget for rating an object.
 *
 * @param   $rating         integer the current rating, from 0 to 5
 * @param   $setRatingUrl   string  URL for the endpoint for changing a rating
 * @param   $resultId       string  Element DOM ID for displaying rating results. Empty means "no element"
 * @param   $setRatingId    string  Element DOM ID for updating with new rating when clicked (for use as part of a form).
 * @return                  void    Called for the side effect of outputting the widget HTML.
 */
function xg_rating_widget($rating, $setRatingUrl, $resultId = '', $setRatingId=null) {
    XG_App::ningLoaderRequire('xg.shared.StarRater');
    if (! $rating) { $rating = 0; } ?>
    <ul class="rating-small easyclear" dojoType="StarRater" _rating="<%= $rating %>" _resultId="<%=xnhtmlentities($resultId)%>" _setRatingUrl="<%= xnhtmlentities($setRatingUrl) %>" _setRatingId="<%= xnhtmlentities($setRatingId) %>" _isPending="<%= User::isPending(XN_Profile::current()) ? 'true' : 'false' %>">
        <li class="current" style="width: <%= $rating * 13 %>px;"><%= xg_html('CURRENTLY_N_STARS', $rating) %></li>
        <li><a class="stars1" title="<%= xg_html('N_STARS_OUT_OF_5', 1) %>" href="#">1</a></li>
        <li><a class="stars2" title="<%= xg_html('N_STARS_OUT_OF_5', 2) %>" href="#">2</a></li>
        <li><a class="stars3" title="<%= xg_html('N_STARS_OUT_OF_5', 3) %>" href="#">3</a></li>
        <li><a class="stars4" title="<%= xg_html('N_STARS_OUT_OF_5', 4) %>" href="#">4</a></li>
        <li><a class="stars5" title="<%= xg_html('N_STARS_OUT_OF_5', 5) %>" href="#">5</a></li>
    </ul>
<?php
}

/**
 * Returns a comma-delimited list of links. Only the first few are shown;
 * the rest are revealed when the user clicks More.
 *
 * @param $links array  the <a> elements
 * @param $visibleLinkCount integer  the maximum number of <a> elements to display initially
 * @return string  the <a> elements separated by commas, possibly followed by a More link
 */
function xg_links_with_more($links, $visibleLinkCount = 5) {
    $visibleLinks = array();
    $hiddenLinks = array();
    if (count($links) <= $visibleLinkCount) {
        foreach ($links as $link) { $visibleLinks[] = $link; }
    } else {
        foreach (array_slice($links, 0, $visibleLinkCount) as $link) { $visibleLinks[] = $link; }
        XG_App::ningLoaderRequire('xg.shared.MoreLink');
        $visibleLinks[] = '<a href="#" dojoType="MoreLink">' . xg_html('MORE_ELLIPSIS') . '</a>';
        foreach (array_slice($links, $visibleLinkCount) as $link) { $hiddenLinks[] = $link; }
    }
    $result = implode(', ', $visibleLinks);
    if ($hiddenLinks) { $result .= '<span style="display:none">' . implode(', ', $hiddenLinks) . '</span>'; }
    return $result;
}

/**
 *  Quotes string to for HTML. The same as xnhtmlentities() but much shorter to write.
 *  qh stands for Quote Html.
 *
 *  @param      $str   string    Source string
 *  @return     string
 */
function qh($str) {
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

/**
 *  Perl/Ruby-like qw operator.
 *  Samples:
 *  	qw('a b c');				// array('a','b','c');
 *  	qw('a>3 b c>2');			// array('a'=>3, 'b', 'c>2');
 *  	qw('a>some text+b','+');	// array('a'=>'some text', 'b');
 *
 *  @param      $str    	string		Source string
 *  @param		$delim		string		Delimiter for the array items. Default is space
 *  @return		array
 */
function qw($str, $delim = ' ') {
    if ($str === '') {
        return array();
    }
    $res = array();
    foreach($delim == ' ' ? preg_split('/\s+/u',trim($str)) : explode($delim,$str) as $value) {
        $v = explode('>',$value,2);
        if(count($v) == 2)
            $res[$v[0]] = $v[1];
        else
            $res[] = $v[0];
    }
    return $res;
}

/**
 *  Writes all arguments to the error.log. Strings are written as is, other data types are var_export'ed.
 *  Can be used as replacement for error_log()
 *
 *  @return     void
 */
function xg_log() {
    $str = '';
    foreach(func_get_args() as $a) {
        $d = is_string($a) ? $a : var_export($a,TRUE);
        // make the output a bit prettier
        $d = preg_replace('/=>\s*/u','=> ',$d);
        $d = preg_replace('/^(\s+)/um','$1$1',$d);
        $str .= "$d;";
    }
    error_log($str);
}

/**
 * Take a windows or unix full pathname and return the basename.
 *
 * @param string $filename the full pathname
 * @return string the basename
 */
function xg_basename($filename) {
    if (preg_match('/^[A-Za-z]:\\\/u', $filename) !== false) {
        // do windows
        $pathParts = explode('\\', $filename);
        $filename = array_pop($pathParts);
    } else {
        $filename = basename($filename);
    }
    return $filename;
}
