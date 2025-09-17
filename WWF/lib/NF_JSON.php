<?php
if (!defined('SERVICES_JSON_LOOSE_TYPE')) {
    define('SERVICES_JSON_LOOSE_TYPE', 16);
}

/**
 * Lightweight adapter mimicking the interface of the historical NF_JSON
 * wrapper that lived in the Ning platform.  Internally we defer to PHP's
 * native JSON functions.
 */
class NF_JSON
{
    /** @var bool */
    private $useLooseType;

    public function __construct($type = null)
    {
        $this->useLooseType = ($type === SERVICES_JSON_LOOSE_TYPE);
    }

    public function encode($value)
    {
        return json_encode($value);
    }

    public function decode($value)
    {
        return json_decode($value, $this->useLooseType);
    }
}
