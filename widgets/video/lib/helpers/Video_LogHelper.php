<?php

class Video_LogHelper {



    public static function log($message, $includePostVariables = FALSE) {
        if ($includePostVariables) {
            $json = new NF_JSON();
            $message = $message . ' ' . $json->encode($_REQUEST);
        }
        error_log($message);
    }



}
