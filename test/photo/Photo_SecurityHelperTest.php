<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_SecurityHelper.php');
Mock::generate('XN_Query');
Mock::generate('stdClass', 'MockXN_Profile', array('isLoggedIn'));
Mock::generate('Photo_SecurityHelper');

class Photo_SecurityHelperTest extends UnitTestCase {

    public function testAddVisibilityFilter1() {
        $query = new MockXN_Query();
        $query->expectNever('filter');
        Photo_SecurityHelper::addVisibilityFilter(XN_Profile::current(), $query);
    }

    public function testAddVisibilityFilter2() {
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('my.visibility', '=', 'all'));
        Photo_SecurityHelper::addVisibilityFilter(XN_Profile::create('jane@foo.com', 'foo'), $query);
    }

    public function testAddVisibilityFilter3() {
        $profile = new MockXN_Profile();
        $profile->screenName = 'jane';
        $profile->setReturnValue('isLoggedIn', true);
        $helper = new MockPhoto_SecurityHelper();
        $helper->expectOnce('assertIsXnProfile', array($profile));
        $helper->expectOnce('checkCurrentUserIsAdmin', array($profile));
        $helper->setReturnValue('checkCurrentUserIsAdmin', 'x');
        TestSecurityHelper::setInstance($helper);
        $oldFriendsQueriesEnabled = W_Cache::getWidget('photo')->config['friendsQueriesEnabled'];
        W_Cache::getWidget('photo')->config['friendsQueriesEnabled'] = null;
        $query = new MockXN_Query();
        $query->expectOnce('filter', array(new EqualExpectation(XN_Filter::any(
                XN_Filter('my.visibility','=','all'),
                XN_Filter::all(XN_Filter('my.visibility','=','friends'),
                               XN_Filter('contributor', 'in', XN_Query::FRIENDS())),
                XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                               XN_Filter('contributorName', '=', 'jane'))))));
        Photo_SecurityHelper::addVisibilityFilter($profile, $query);
        W_Cache::getWidget('photo')->config['friendsQueriesEnabled'] = $oldFriendsQueriesEnabled;
    }

    public function testAddVisibilityFilter4() {
        $profile = new MockXN_Profile();
        $profile->screenName = 'jane';
        $profile->setReturnValue('isLoggedIn', true);
        $helper = new MockPhoto_SecurityHelper();
        $helper->expectOnce('assertIsXnProfile', array($profile));
        $helper->expectOnce('checkCurrentUserIsAdmin', array($profile));
        $helper->setReturnValue('checkCurrentUserIsAdmin', 'x');
        TestSecurityHelper::setInstance($helper);
        $oldFriendsQueriesEnabled = W_Cache::getWidget('photo')->config['friendsQueriesEnabled'];
        W_Cache::getWidget('photo')->config['friendsQueriesEnabled'] = 'N';
        $query = new MockXN_Query();
        $query->expectOnce('filter', array(new EqualExpectation(XN_Filter::any(
                XN_Filter('my.visibility','=','all'),
                XN_Filter::all(XN_Filter('my.visibility','in',array('me', 'friends')),
                               XN_Filter('contributorName', '=', 'jane'))))));
        Photo_SecurityHelper::addVisibilityFilter($profile, $query);
        W_Cache::getWidget('photo')->config['friendsQueriesEnabled'] = $oldFriendsQueriesEnabled;
    }

    private function photoWithVisibilityFactory() {
        $photo = new StdClass();
        $photo->my = new StdClass();
        $photo->my->visibility = 'all';
        return $photo;
    }

    /**
     * The follow tests can not run because of the static logic involved
     * in this method as part of its initial implementation
     *
     * @todo make these three tests run
     */
    public function _testIsViewableOnLatestActivityReturnsTrueWhenPhotoVisibilityAllAndNotOnProfile() {
        $photo = $this->photoWithVisibilityFactory();
        $result = Photo_SecurityHelper::isViewableOnLatestActivity($photo, null, false);
        $this->assertTrue($result);
    }

    public function _testIsViewableOnLatestActivityReturnsFalseWhenPhotoVisibilityIsNotAllAndNotOnProfile() {
        $photo = $this->photoWithVisibilityFactory();
        $photo->my->visibility = rand(100, 200);
        $result = Photo_SecurityHelper::isViewableOnLatestActivity($photo, null, false);
        $this->assertFalse($result);
    }

    public function _testIsViewableOnLatestActivityReturnsFalseWhenPhotoVisibilityIsAllAndOnProfile() {
        $photo = $this->photoWithVisibilityFactory();
        $result = Photo_SecurityHelper::isViewableOnLatestActivity($photo, null, true);
        $this->assertFalse($result);
    }

}

class TestSecurityHelper extends Photo_SecurityHelper {

    public static function setInstance($instance) {
        Photo_SecurityHelper::$instance = $instance;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


