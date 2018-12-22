<?php

/**
 * Useful functions for working with the Media Uploader.
 */
class XG_MediaUploaderHelper {

    /**
     * Returns the name of the action to use for new photos, videos, and music.
     *
     * @return string  new, newWithUploader, or chooseUploader
     */
    public static function action() {
        if ($_COOKIE[self::getCookieName()] == 'Y') { return 'newWithUploader'; }
        if ($_COOKIE[self::getCookieName()] == 'N') { return 'new'; }
        return 'chooseUploader';
    }

    /**
     * Returns the name of the cookie that stores whether the current user last used
     * the simple uploader or the media uploader [Jon Aquino 2008-01-08]
     */
    private static function getCookieName() {
        return 'using_media_uploader_' . XN_Profile::current()->screenName;
    }

    /**
     * Stores whether the user is using the Media Uploader or the simple uploader.
     *
     * @param $usingMediaUploader boolean  true, if using the Media Uploader; false, the simple uploader
     */
    public static function setUsingMediaUploader($usingMediaUploader) {
        // 2147483647 is largest epoch timestamp that fits in a 32-bit integer.
        // See "PHP Cookbook", Setting Cookies, p. 230 (2nd ed.) [Jon Aquino 2007-12-18]
        setcookie(self::getCookieName(), $usingMediaUploader ? 'Y' : 'N', 2147483647, '/');
    }

    /**
     * Sets the HTTP status code and terminates the output.
     *
     * @param $approvalPending boolean  whether the uploaded content must be approved by an administrator
     */
    public static function exitWithSuccess($approvalPending) {
        header($approvalPending ? 'HTTP/1.1 202 Accepted' : 'HTTP/1.1 201 Created');
        exit;
    }

    /**
     * Sets the HTTP status code, outputs error XML, and terminates the script.
     *
     * @param $errorCode string  error code, e.g., 'media-uploader:2'
     * @param $details string  additional information about the problem
     */
    public static function exitWithError($errorCode, $details = '') {
        switch ($errorCode) {
            case 'media-uploader:1':
                header('HTTP/1.1 400 Bad Request');
                $description = 'An upload error occurred.';
                break;
            case 'media-uploader:2':
                header('HTTP/1.1 403 Forbidden');
                $description = 'User is not allowed to add content of this type.';
                break;
            case 'media-uploader:3':
                header('HTTP/1.1 403 Forbidden');
                $description = 'User is not a member of the network.';
                break;
            case 'media-uploader:4':
                header('HTTP/1.1 405 Method Not Allowed');
                header("Allow: POST");
                $description = 'Not a POST.';
                break;
            case 'media-uploader:5':
                header('HTTP/1.1 415 Unsupported Media Type');
                $description = 'Unsupported format.';
                break;
            case 'media-uploader:6':
                header('HTTP/1.1 500 Internal Server Error');
                $description = 'An exception occurred.';
                break;
            default:
                header('HTTP/1.1 500 Internal Server Error');
                $description = 'Unknown error code.';
                break;
        }
        header('Content-Type: text/xml');
        echo '<errors><error code="' . xg_xmlentities($errorCode) . '">' . xg_xmlentities(trim($description . ' ' . $details)) . '</error></errors>';
        exit;
    }

}
