<?php
// only run this script once per processs
if (!defined('BAZEL_UNIT_TESTS_INITIALIZED')) {
function currentUrl() {
    return str_replace('/index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

if (defined('CMDLINE_TESTING')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/test/cmdline_test_header.php';
    return;
}

if (! XN_Profile::current()->isOwner()) {
    // Some tests assume that you are the app owner [Jon Aquino 2007-09-21]
    XN_Profile::signOut();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/main/authorization/signIn?target=' . xnhtmlentities(urlencode(currentUrl())));
    exit;
}

ini_set('memory_limit', '128M');
// Turn off HTTP_X_XN_TRACE; otherwise, get "Cannot modify header information" warnings in
// several tests beginning in Core 6.11.9. [Jon Aquino 2008-07-03]
$_SERVER['HTTP_X_XN_TRACE'] = 'false';
// Define the base directory in this app

if (!defined('NF_APP_BASE'))          define('NF_APP_BASE', $_SERVER['DOCUMENT_ROOT']);
if (!defined('W_INCLUDE_PREFIX'))     define('W_INCLUDE_PREFIX', $_SERVER['DOCUMENT_ROOT']);
if (!defined('DEVTESTENGINE_PREFIX')) define('DEVTESTENGINE_PREFIX', 'xn-app://DevTestEngine/');
if (!defined('SIMPLETEST_PATH'))      define('SIMPLETEST_PATH', DEVTESTENGINE_PREFIX . 'simpletest_1.0.1alpha3/');
define('UNIT_TESTING', true);

/* Load the base WWF code */
require XN_INCLUDE_PREFIX .'/WWF/bot.php';
/* Load our custom App class */
W_WidgetApp::includeFileOnce('/lib/XG_App.php');

XG_App::includeFileOnce(SIMPLETEST_PATH . 'unit_tester.php', false);
XG_App::includeFileOnce(SIMPLETEST_PATH . 'web_tester.php', false);
XG_App::includeFileOnce(SIMPLETEST_PATH . 'mock_objects.php', false);

// @todo document this
// @todo move this into its own file
// @todo test its utility methods
abstract class BazelTestCase extends UnitTestCase
{
    protected function escape($string) {
        return str_replace('%', '%%', $string);
    }

    protected function prepareLineFileForMsg($line, $file) {
        return str_replace('%', '%%', $line) . ' ' . $file;
    }


    /**
     * Determine if the provided line is a comment
     *
     * This is lazy - i.e., it only matches if the beginning line appears to be
     * a comment.  Much better, long-term solution would be to instantiate a
     * LineByLineIterator that would automatically skip comments based on blocks
     * of code.
     *
     * This only supposed lines that begin with "*" currently, based on the assumption
     * that "* foobar" is invalid PHP.
     *
     * @todo refactor into a custom expectation
     * @todo test logic
     * @todo move to BazelTestCase
     */
    public function isLineComment($line) {
        return preg_match('/^\*.*/', trim($line));
    }

    /**
     * Returns true if the line ends in "/** @allowed * /" - with actual closing
     *
     * @return bool
     * @todo add caching to this so the same lines don't keep getting compared
     */
    protected function isAllowedLine($line) {
        return preg_match('/\/\*\* @allowed \*\//', $line);
    }
}

XG_App::includeFileOnce('/test/XG_TestHelper.php');
XG_App::includeFileOnce('/test/CmdlineTestCase.php');
XG_App::includeFileOnce('/test/ExceptionMockDecorator.php');
XG_App::includeFileOnce('/test/TestRest.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_Query.php');
XG_App::includeFileOnce('/lib/XG_LangHelper.php');
call_user_func(array(W_Cache::getClass('app'), 'loadWidgets'));
XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
define('XG_LOCALE', 'en_US');
W_Cache::getWidget('main')->config['localeHasCustomCatalog'] = 0;
XG_Browser::initFromRequest();
XN_Debug::allowDebug(false);

define('BAZEL_UNIT_TESTS_INITIALIZED', true);
}
