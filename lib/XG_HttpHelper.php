<?php

class XG_HttpHelper {
    public static function currentUrl() {
        if (preg_match('@/index.php/([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)\?groupUrl=([a-zA-Z0-9_]+)&(.*)@u', $_SERVER['REQUEST_URI'], $matches)) {
            // When you click the link in a group invitation email and sign up, you land on the group page
            // but the URL is not the pretty one. Ensure that we land on the pretty URL. [Jon Aquino 2007-05-02]
            return XG_GroupHelper::buildUrl($matches[1], $matches[2], $matches[3], $matches[5], $matches[4]);
        }
        return str_replace('/index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * Returns the URL for the sign-in page.
     *
     * @param $target string  the URL to go to after sign-in, or null for the current page
     * @param $groupToJoin string|XN_Content|W_Content - the Group object (or its URL) to make the user a member of
     * @return string  the URL
     * @deprecated Use XG_AuthorizationHelper::signInUrl instead
     */
    public static function signInUrl($target = null, $groupToJoin = null) {
        return XG_AuthorizationHelper::signInUrl($target, $groupToJoin);
    }

    /**
     * Returns the URL for the join action
     *
     * IMPORTANT:  This does NOT return the URL of the sign up page!
     *   The join action is not accessible to non-members on private apps!
     *
     * This needs DESPERATELY to be redesigned!
     *
     * @param $target string  URL to land on after sign-up, or null for the current page
     * @param $groupToJoin XN_Content|W_Content  group to join as part of sign-up (optional)
     * @return the sign-up URL
     * @deprecated 2.0  Use XG_AuthorizationHelper::signUpUrl instead
     */
    public static function signUpUrl($target = null, $groupToJoin = null) {
        return XG_AuthorizationHelper::signUpUrl($target, $groupToJoin);
    }

    /**
     * Returns the URL to use for the current user to join the network.
     * Unlike signUpUrl(), this URL lands you on your profile page.
     *
     * @return string  the URL
     *
     * @deprecated 2.0  This behavior is deprecated
     */
    public static function joinThenGoToProfileUrl() {
        return XG_AuthorizationHelper::signUpUrl(xg_absolute_url('/profiles'));
    }

    /**
     * For private apps, sometimes we have to send you to sign up explicitly, so that you
     * can see the page before you're a member
     *
     * @deprecated 2.0  Use XG_AuthorizationHelper::signUpUrl instead
     */
    public static function trueSignUpUrl($target = null) {
        return XG_AuthorizationHelper::signUpUrl($target);
    }

    /** @deprecated 2.0  Use XG_AuthorizationHelper::signOutUrl instead */
    public static function signOutUrl() {
        return XG_AuthorizationHelper::signOutUrl();
    }

    /**
     * Return the profile URL for the provided user. Older (pre-2.0) code may
     * pass in a screen name, but more up to date code should use a User object
     * so that the proper URL can be computed
     *
     * @param $screenNameOrUser string|User
     */
    public static function profileUrl($screenNameOrUser) {
        if (is_string($screenNameOrUser)) {
            return 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNameOrUser);
        } else {
            return 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNameOrUser->title);
        }
    }

    /**
     * Adds the given parameters to the URL.
     *
     * @param $url string  The URL
     * @param $parameters array  name => value
     * @return string  The URL with the parameters added
     */
    public static function addParameters($url, $parameters) {
        foreach ($parameters as $name => $value) {
            $url = self::addParameter($url, $name, $value);
        }
        return $url;
    }

    /**
     * Removes the given parameters from the URL.
     *
     * @param $url string  The URL
     * @param $parameterNames array  Names of the parameters to remove
     * @return string  The URL with the parameters removed
     */
    public static function removeParameters($url, $parameterNames) {
        foreach ($parameterNames as $name) {
            $url = self::removeParameter($url, $name);
        }
        return $url;
    }

    /**
     * Regenerates a parsed (via parse_url) URL string
     *
     * @param urlParts array  associative array returned by parse_url
     *
     * @return string  joined URL string
     */
    public static function joinParsedUrl($urlParts) {
        // http_build_url currently does not support building relative urls and substitutes values from the current URI from env vars [ywh 2008-07-03]
        $newUrl = '';
        if (isset($urlParts['scheme'])) { $newUrl .= $urlParts['scheme'] . '://'; }
        if (isset($urlParts['user'])) { $newUrl .= $urlParts['user']; }
        if (isset($urlParts['pass'])) { $newUrl .= ':' . $urlParts['pass']; }
        if (isset($urlParts['user']) || isset($urlParts['pass'])) { $newUrl .= '@'; }
        if (isset($urlParts['host'])) { $newUrl .= $urlParts['host']; }
        if (isset($urlParts['port'])) { $newUrl .= ':' . $urlParts['port']; }
        // if no path but we have query params, force path to be '/'
        if (mb_strlen($urlParts['query']) && ! mb_strlen($urlParts['path'])) { $urlParts['path'] = '/'; }
        if (isset($urlParts['path'])) { $newUrl .= $urlParts['path']; }
        if (mb_strlen($urlParts['query'])) { $newUrl .= '?' . $urlParts['query']; }
        if (isset($urlParts['fragment'])) { $newUrl .= '#' . $urlParts['fragment']; }
        return $newUrl;
    }

    /**
     * Rewrites an absolute URL and replaces the hostname to the specified host
     *
     * @param url string  URL on which to perform the replacement
     * @param host string  Replacement hostname; use $_SERVER['SERVER_NAME'] if not specified
     *
     * @return string  URL with the hostname replaced with the specified hostname
     */
    public static function replaceUrlHost($url, $host = null) {
        // short-circuit if param isn't an absolute url
        if (! preg_match('/^https?:\/\//iu', $url)) { return $url; }

        if (is_null($host)) {
            if (array_key_exists('SERVER_NAME', $_SERVER)) {
                $host = $_SERVER['SERVER_NAME'];
            } else {
                // no specified host and no env variable to use as default
                return $url;
            }
        }

        $urlParts = parse_url($url);
        if (! is_null($urlParts['host']) && mb_strlen($urlParts['host'])) {
            $urlParts['host'] = $host;
        }
        return self::joinParsedUrl($urlParts);
    }

    /**
     * Adds or replaces the given parameter.
     *
     * @param $url string  the URL
     * @param $name string  the name of the parameter
     * @param $value null or empty string to remove the parameter
     * @return string  the updated URL
     */
    public static function addParameter($url, $name, $value) {
        // @todo: Refactor this into a helper for integers
        if (is_numeric($value) && $value == (int)$value) {
            $value = (int)$value;
        }
        // From SnazzySharer.php  [Jon Aquino 2006-10-21]
        $urlParts = parse_url($url);
        $queryStringParameters = array();
        if (isset($urlParts['query'])) { parse_str($urlParts['query'], $queryStringParameters); }
        $queryStringParameters[$name] = $value;
        if (! $value && $value !== 0) { unset($queryStringParameters[$name]); }
        $urlParts['query'] = http_build_query($queryStringParameters);
        return self::joinParsedUrl($urlParts);
    }

    public static function removeParameter($url, $parameterName) {
        return self::addParameter($url, $parameterName, null);
    }

    /**
     * Returns whether the URL contains the given parameter.
     *
     * @param $url string  the URL to examine
     * @return string  the name of the parameter to look for
     */
    public static function hasParameter($url, $name) {
        $value = self::getParameter($url, $name);
        return $value !== null && $value !== '';
    }

    /**
     * Returns the value of the given parameter in the URL
     *
     * @param $url string  the URL to examine
     * @return string  the value, or null if it doesn't exist
     */
    public static function getParameter($url, $name) {
        $urlParts = parse_url($url);
        $parameters = array();
        if (isset($urlParts['query'])) { parse_str($urlParts['query'], $parameters); }
        return $parameters[$name];
    }

    /**
     * Trims all string values in the $_GET and $_POST arrays.
     */
    public static function trimGetAndPostValues() {
        foreach ($_GET as $key => $value) {
            if (is_string($_GET[$key])) { $_GET[$key] = trim($value); }
        }
        foreach ($_POST as $key => $value) {
            // $_POST[$key] may be an array [Jon Aquino 2006-12-20]
            if (is_string($_POST[$key])) { $_POST[$key] = trim($value); }
        }
    }

    /**
     * Returns whether the URL is that of the homepage
     *
     * @param $url string  the URL to test
     * @return boolean  whether it is the URL of the main page
     *
     * @see XG_App::homepageIsVisible
     */
    public static function isHomepage($url) {
        if (! $url) { return false; }
        $urlParts = parse_url(xg_absolute_url($url));
        if ($urlParts['host'] != $_SERVER['HTTP_HOST']) { return false; }
        return in_array($urlParts['path'], array('', '/', '/main', '/main/index', '/main/index/index'));
    }

    /**
     * Returns whether the URL is that of the current user's page
     *
     * @param $url string  the URL to test
     * @return boolean  whether it is the URL of the current user's profile page
     */
    public static function isMyPage($url) {
        if (! $url) { return false; }
        $urlParts = parse_url(xg_absolute_url($url));
        if ($urlParts['host'] != $_SERVER['HTTP_HOST']) { return false; }
        $path = $urlParts['path'];
        if ($path == '/profiles') { return true; }
        if ($path == '/profiles/') { return true; }
        $profile = XN_Profile::current();
        if (! $profile->isLoggedIn()) { return false; }
        if ($path == '/profiles/profile/' . $profile->screenName) { return true; }
        if ($path == '/profiles/profile/show' && self::getParameter($url, 'id') == $profile->screenName) { return true; }
        if ($path == '/profiles/profile/show' && self::getParameter($url, 'screenName') == $profile->screenName) { return true; }
        $profileAddress = User::profileAddress($profile->screenName);
        if (! $profileAddress) { return false; }
        if ($path == '/profiles/profile/' . $profileAddress) { return true; }
        if ($path == '/profiles/profile/show' && self::getParameter($url, 'id') == $profileAddress) { return true; }
        return false;
    }
}
