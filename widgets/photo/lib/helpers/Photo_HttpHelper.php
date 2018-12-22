<?php

/**
 * Contains methods for dealing with HTTP headers.
 */
class Photo_HttpHelper {
    public static function currentUrl() {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        return XG_HttpHelper::currentUrl();
    }

    public static function trimGetAndPostValues() {
        // TODO: use a multibyte/utf-8 aware trim instead ?
        foreach ($_GET as $key => $value) {
            $_GET[$key] = trim($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = trim($value);
        }
    }
}
