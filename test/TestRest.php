<?php

/**
 * Exposes protected REST objects for unit tests and mock objects.
 */
class TestRest extends XN_REST {
    public static function setInstance($instance) { parent::setInstance($instance); }
    public static function getInstance($instance) { return parent::getInstance(); }
    public function doRequest($method, $url, &$body = null,$contentType=null, $additionalHeaders = null) {
        return parent::doRequest($method, $url, $body,$contentType, $additionalHeaders);
    }
}
