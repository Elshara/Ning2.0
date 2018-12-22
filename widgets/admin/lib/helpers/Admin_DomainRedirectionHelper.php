<?php

/**
 * Useful functions for working with domain redirection.
 * @see BAZ-7268
 */
class Admin_DomainRedirectionHelper {

    /** Default contents of the /index.php file */
    const DEFAULT_INDEX_FILE_CONTENTS = "<?php
define('NF_APP_BASE',dirname(__FILE__));
require_once NF_APP_BASE . '/lib/index.php';";

    /**
     * Returns HTTP header strings for redirecting to a mapped domain, or null
     * if no redirection is needed.
     *
     * @param $domainName  e.g., mydomain.com, or null if domain redirection is not configured
     * @param $requestUri  e.g., /forum
     * @param $requestMethod  GET, POST, PUT, or DELETE
     * @return array|null  HTTP headers, or null
     */
    public static function domainRedirectionHeaders($domainName, $requestUri, $requestMethod) {
        if (! $domainName) { return null; }
        if (mb_strtolower($domainName) == mb_strtolower($_SERVER['HTTP_HOST'])) { return null; }
        if ($requestMethod != 'GET') { return null; }
        if (mb_strpos($requestUri, 'editDomainRedirection') !== false) { return null; }
        return array('HTTP/1.1 301 Moved Permanently', 'Location: http://' . $domainName . $requestUri);
    }

    /**
     * Sets the contents of the index.php file. The original file will be backed up to /xn_private/xn_volatile/backups.
     *
     * @param $contents string  the new contents of index.php
     */
    private static function setIndexFileContents($contents) {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        XG_FileHelper::filePutContentsWithBackup($_SERVER['DOCUMENT_ROOT'] . '/index.php', $contents);
    }

    /**
     * Returns the contents of the /index.php file.
     *
     * @return string  index.php's text
     */
    private static function getIndexFileContents() {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/index.php');
    }

    /**
     * Resets the contents of the index.php file. Any user customizations will be removed.
     * The original file will be backed up to /xn_private/xn_volatile/backups.
     * @see BAZ-7268
     */
    public static function resetIndexFile() {
        self::setIndexFileContents(self::DEFAULT_INDEX_FILE_CONTENTS);
    }

    /**
     * Disables redirection code that the user may have added to /index.php
     * according to http://developer.ning.com/wiki/Index_Redirect
     */
    public static function disableRedirectionInIndexFile() {
        $newIndexFileContents = self::disableRedirectionInIndexFileProper($oldIndexFileContents = self::getIndexFileContents());
        if ($oldIndexFileContents != $newIndexFileContents) { self::setIndexFileContents($newIndexFileContents); }
    }

    /**
     * Disables redirection code that the user may have added to /index.php
     * according to http://developer.ning.com/wiki/Index_Redirect
     *
     * @param $indexFileContents  string the contents of the /index.php file
     * @return string  the contents with redirection code disabled
     */
    protected static function disableRedirectionInIndexFileProper($indexFileContents) {
        // The user may have inserted an exit() or return() call after the two header() calls.
        // Thus, rather than commenting out the header() calls, it is safer to alter the "if" statement. [Jon Aquino 2008-04-15]
        return preg_replace('@if\s*\(\s*\$_SERVER\[.SERVER_NAME.\]@u', 'if (FALSE /* Manual domain redirection is deprecated. */ && $_SERVER[\'SERVER_NAME\']', $indexFileContents);
    }

    /**
     * Returns the domain names for this network, including networkname.ning.com
     *
     * @return array  the unmapped and mapped domain names (keys same as values)
     */
    public static function domainNames() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $response = $json->decode(XN_REST::get('http://' . XN_AtomHelper::HOST_APP(XN_Application::load()->relativeUrl) . '/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl)));
        $domainNames = array();
        foreach ($response['application']['domains'] as $domainName) {
            foreach (self::wwwAndNonWwwVariants($domainName) as $variant) {
                $domainNames[$variant] = $variant;
            }
        }
        $unmappedDomainName = XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX;
        $domainNames[$unmappedDomainName] = $unmappedDomainName;
        return $domainNames;
    }

    /**
     * Returns the domain name with and without the www prefix
     *
     * @param $domainName string  domain name with or without the www prefix
     * @return array  domain name without the prefix followed by domain name with the prefix
     */
    protected static function wwwAndNonWwwVariants($domainName) {
        return mb_stripos($domainName, 'www.') === 0
                ? array(mb_substr($domainName, 4), $domainName)
                : array($domainName, 'www.' . $domainName);
    }

    /**
     * Returns the ID of the network at the given domain name.
     *
     * @param $domainName string  the domain, e.g., thisis50.com or thisis50.ning.com
     * @return string  the application ID, e.g., thisis50, or null if it could not be determined.
     */
    public static function applicationId($domainName) {
        $response = @file_get_contents('http://' . $domainName . '/xn/atom/1.0/application');
        if (! $response) { return null; }
        if (mb_strpos($response, '<entry>') === false) { return null; }
        return self::applicationIdProper($response);
    }

    /**
     * Returns the ID of the network at the given domain name.
     *
     * @param $response string  the response to /xn/atom/1.0/application
     * @return string  the application ID, e.g., thisis50, or null if it could not be determined.
     */
    protected static function applicationIdProper($response) {
        try {
            $xml = @new SimpleXMLElement($response);
            $id = (string) $xml->entry[0]->id;
            return $id ? $id : null;
        } catch (Exception $e) {
            return null;
        }
    }

}
