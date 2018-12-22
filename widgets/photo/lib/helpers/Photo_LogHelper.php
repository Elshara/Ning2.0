<?php

/**
 * Contains utility functions for logging purposes.
 */
class Photo_LogHelper {
    public static function log($message, $dumpRequest = false) {
        if ($dumpRequest) {
            $json    = new NF_JSON();
            $message = $message . ' ' . $json->encode($_REQUEST);
        }
        error_log($message);
    }
}
