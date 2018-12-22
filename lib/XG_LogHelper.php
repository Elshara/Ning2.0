<?php

/**
 * Useful functions for logging.
 */
class XG_LogHelper {

    /**
     * Logs a message pertaining to sign-in or sign-up.
     *
     * @param $message string  a brief message to log
     * @param $data array  optional key/value pairs to append to the message
     * @see BAZ-8141
     */
    public static function logBasicFlows($message, $data = array()) {
        $message = 'Basic Flows: ' . $message;
        $data['trace'] = XN_REST::$TRACE;
        $data['remote-addr'] = $_SERVER['REMOTE_ADDR'];
        foreach ($data as $k => $v) { $message .= " $k:$v"; }
        self::logCentrallyAndLocally($message);
    }

    /**
     * Logs to a centralized log and the app error log.
     *
     * @param $message string  a brief message to log
     * @param $usePrefix boolean  whether to prefix the message with timestamp and appcore address
     */
    public static function logCentrallyAndLocally($message, $usePrefix = true) {
        error_log($message);
        self::logCentrally($message, $usePrefix);
    }

    /**
     * Logs to a centralized log (not the app error log).
     *
     * @param $message string  a brief message to log
     * @param $usePrefix boolean  whether to prefix the message with timestamp and appcore address
     */
    public static function logCentrally($message, $usePrefix = true) {
        if ($usePrefix) {
            $message = gmdate('[D M d H:i:s Y] ').XN_Application::load()->relativeUrl .
                ' (appcore=' . XN_REST::$LOCAL_API_HOST_PORT. '): ' . $message;
        }
        $fp = fopen('php://stderr','a');
        if ($fp) {
            fputs($fp, trim($message)."\n");
            fclose($fp);
        }
    }
}
