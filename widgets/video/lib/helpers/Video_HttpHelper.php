<?php
class Video_HttpHelper {

    public static function currentUrl() {
        return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function trimGetAndPostValues() {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Adds or replaces the given parameter.
     * @param $parameter foo=bar, or foo= to remove a parameter
     */
    public static function addParameter($url, $parameter) {
        // From SnazzySharer.php  [Jon Aquino 2006-10-21]
        $urlParts = parse_url($url);
        $queryStringParameters = array();
        if (isset($urlParts['query'])) { parse_str($urlParts['query'], $queryStringParameters); }
        $parameterParts = explode('=', $parameter, 2);
        $queryStringParameters[$parameterParts[0]] = $parameterParts[1];
        if ($parameterParts[1] === '') { unset($queryStringParameters[$parameterParts[0]]); }
        $newUrl = '';
        if (isset($urlParts['scheme'])) { $newUrl .= $urlParts['scheme'] . '://'; }
        if (isset($urlParts['user'])) { $newUrl .= $urlParts['user']; }
        if (isset($urlParts['pass'])) { $newUrl .= ':' . $urlParts['pass']; }
        if (isset($urlParts['user']) || isset($urlParts['pass'])) { $newUrl .= '@'; }
        if (isset($urlParts['host'])) { $newUrl .= $urlParts['host']; }
        if (isset($urlParts['port'])) { $newUrl .= ':' . $urlParts['port']; }
        if (isset($urlParts['path'])) { $newUrl .= $urlParts['path']; }
        $newUrl .= '?' . http_build_query($queryStringParameters);
        if (isset($urlParts['fragment'])) { $newUrl .= '#' . $urlParts['fragment']; }
        return $newUrl;
    }
    public static function removeParameter($url, $parameterName) {
        return self::addParameter($url, $parameterName . '=');
    }

}
