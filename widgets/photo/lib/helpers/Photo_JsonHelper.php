<?php

/**
 * Contains helper methods for dealing with JSON, which are especially useful to AJAX actions.
 */
class Photo_JsonHelper {
    public static function outputAndExit($array) {
        $json = new NF_JSON();
        self::outputAndExitProper($json->encode($array));
    }

    public static function outputAndExitProper($json) {
        // Wrap in parentheses to tell eval that { signifies a hash rather than a block. See Lasse Reichstein Nielsen,
        // "Objects from streams", http://groups.google.com/group/comp.lang.javascript/browse_thread/thread/cb27e65cd1897b2b/0eb38ac5f8e5020e?lnk=st&q=javascript+eval+parentheses&rnum=4&hl=en#0eb38ac5f8e5020e  [Jon Aquino 2006-04-30]
        $output = '(' . $json . ')';
        XG_App::includeFileOnce(XN_INCLUDE_PREFIX .'/XNC/Ajax.php', false);
        XNC_Ajax::startAjaxPage();
        if ($_REQUEST['dojo_transport'] == 'iframe') {
            // Although Dojo recommends putting the response in a textarea in an HTML document, use text/plain instead
            // as it will be faster (HTML responses include the Ningbar javascript).[Jon Aquino 2006-05-06]
            // Using Dojo's IFrame transport, if we return a text/plain response IE prompts the person to download it for some reason.
            // Redirecting to a text file does not have this problem [Jon Aquino 2006-05-06]
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/json-response.txt', $output);
            // Append random number to prevent browser and server caching, just in case [Jon Aquino 2006-05-06]
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/json-response.txt?x=' . mt_rand());
            exit;
        }
        header('Content-Type: text/javascript');
        echo $output;
        exit;
    }

    public static function handleExceptionInAjaxCall($e) {
        self::outputAndExit(array('friendlyErrorMessage' => xg_text('HMM_SOMETHING_WENT_AWRY'),
                                  'errorMessage'         => self::exceptionToString($e)));
    }

    private static function exceptionToString($exception) {
        return $exception->getMessage() . "\n" . $exception->getTraceAsString();
    }
}
