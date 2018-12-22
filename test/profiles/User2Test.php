<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_PrivacyHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PrivacyHelper.php');

class User2Test extends UnitTestCase {

        public function testLoadMultiple() {
        TestUser::setScreenNameToUserMap(array());
        TestUser::setScreenNamesWithoutUserObjects(array());
        list($result, $queried) = $this->doTestLoadMultiple(null);
        $this->assertEqual(false, is_array($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(false, $result instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple('JKHKSDFJSKDF');
        $this->assertEqual(false, is_array($result));
       $this->assertEqual(true, $queried);
        $this->assertEqual(false, $result instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(XN_Profile::current()->screenName);
        $this->assertEqual(false, is_array($result));
       $this->assertEqual(true, $queried);
        $this->assertEqual(true, $result instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(XN_Profile::current()->screenName);
       $this->assertEqual(false, is_array($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(true, $result instanceof XN_Content);

        TestUser::setScreenNameToUserMap(array());
        TestUser::setScreenNamesWithoutUserObjects(array());
        list($result, $queried) = $this->doTestLoadMultiple(array(null));
        $this->assertEqual(0, count($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(false, reset($result) instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(array('JKHKSDFJSKDF'));
       $this->assertEqual(0, count($result));
        $this->assertEqual(true, $queried);
        $this->assertEqual(false, reset($result) instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(array(XN_Profile::current()->screenName));
       $this->assertEqual(1, count($result));
        $this->assertEqual(true, $queried);
        $this->assertEqual(true, reset($result) instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(array(XN_Profile::current()->screenName));
       $this->assertEqual(1, count($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(true, reset($result) instanceof XN_Content);

        TestUser::setScreenNameToUserMap(array(mb_strtolower(XN_Profile::current()->screenName) => User::load(XN_Profile::current()->screenName)));
        TestUser::setScreenNamesWithoutUserObjects(array());
        list($result, $queried) = $this->doTestLoadMultiple(XN_Profile::current()->screenName);
        $this->assertEqual(false, is_array($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(true, $result instanceof XN_Content);

        list($result, $queried) = $this->doTestLoadMultiple(array(XN_Profile::current()->screenName));
        $this->assertEqual(1, count($result));
        $this->assertEqual(false, $queried);
        $this->assertEqual(true, reset($result) instanceof XN_Content);
    }

    public function doTestLoadMultiple($args) {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm-stack';
        ob_start();
        $result = User::loadMultiple($args);
        $output = trim(ob_get_contents());
         ob_end_clean();
        $_GET['xn_debug'] = null;
        return array($result, $output);
    }

    public function testLoad() {
        $GLOBALS['UNIT_TEST_SKIP_PROFILE_CHECK_IN_USER_LOAD'] = true;
        TestUser::setScreenNameToUserMap(array());
        $this->assertEqual('query=Y, user=Y', $this->doTestLoad('User::load(XN_Profile::current()->screenName)'));
        $this->assertEqual('query=N, user=Y', $this->doTestLoad('User::retrieveIfLoaded(XN_Profile::current()->screenName)'));
        $this->assertEqual('query=N, user=Y', $this->doTestLoad('User::loadOrRetrieveIfLoaded(XN_Profile::current()->screenName)'));
        $this->assertEqual('query=Y, user=N', $this->doTestLoad('User::load(\'blahblahfoo\')'));
        $this->assertEqual('query=N, user=N', $this->doTestLoad('User::retrieveIfLoaded(\'blahblahfoo\')'));
        $this->assertEqual('query=N, user=N', $this->doTestLoad('User::loadOrRetrieveIfLoaded(\'blahblahfoo\')'));
    }

    private function doTestLoad($code) {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm-stack';
        ob_start();
        $result = eval('return ' . $code . ';');
        $output = trim(ob_get_contents());
        ob_end_clean();
        $_GET['xn_debug'] = null;
        return 'query=' . (strpos($output, '/xn/atom/1.0/content') !== false ? 'Y' : 'N') . ', user=' . ($result ? 'Y' : 'N');
    }

    public function testEmailIsRegistered() {
        $this->assertTrue(User::emailIsRegistered(XN_Profile::current()->email));
        $this->assertFalse(User::emailIsRegistered('blahblahblahfoo@example.org'));
        $this->assertFalse(User::emailIsRegistered('blahblahblahfoo@'));
    }

    public function tearDown() {
        $GLOBALS['UNIT_TEST_SKIP_PROFILE_CHECK_IN_USER_LOAD'] = false;
    }

}

class TestUser extends User {

    public static function setScreenNameToUserMap($screenNameToUserMap) {
        parent::$screenNameToUserMap = $screenNameToUserMap;
    }

    public static function setScreenNamesWithoutUserObjects($screenNamesWithoutUserObjects) {
        parent::$screenNamesWithoutUserObjects = $screenNamesWithoutUserObjects;
    }

    public static function getScreenNameToUserMap() {
        return parent::$screenNameToUserMap;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



