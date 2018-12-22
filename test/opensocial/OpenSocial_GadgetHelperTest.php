<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');

class OpenSocial_GadgetHelperTest extends UnitTestCase {

    public function testCurrentViewerName() {
        //TODO: Difficult to test anon user.  Possibly break currentViewerName into two so that
        // can be tested?  [Thomas David Baker 2008-08-04]
        $myName = XN_Profile::current()->screenName;
        $this->assertEqual($myName, OpenSocial_GadgetHelper::currentViewerName());
    }
    
    public function testGetInstalledApps() {
        $appUrl = 'http://example.com/testGetInstalledApps';
        $screenName = XN_Profile::current()->screenName;
        $appData = OpenSocialAppData::create($appUrl, $screenName, true /* installed by url */);
        $appData->my->isOnMyPage = false;
        $appData->save();
        
        $apps = OpenSocial_GadgetHelper::getInstalledApps($screenName);
        $this->assertTrue(count($apps) > 0);
        $found = false;
        foreach ($apps as $app) {
            $found = $found || ($app['appData']->my->appUrl == $appUrl);
        }
        $this->assertTrue($found);
    }
    
    public function testNumMembers() {
        $appUrl = 'http://example.com/testNumMembers';
        $numMembers = OpenSocial_GadgetHelper::numMembers($appUrl);
        $x = OpenSocialAppData::create($appUrl, XN_Profile::current()->screenName, false /* installed from application directory */);
        $x->save();
        XG_Query::invalidateCache('opensocial-num-users-' . md5($appUrl));
        $this->assertEqual($numMembers + 1, OpenSocial_GadgetHelper::numMembers($appUrl));
    }
    
    public function testNumFriendsViaNumUsers() {
        $currentUser = XN_Profile::current()->screenName;
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $friendInfo = Profiles_UserHelper::findFriendsOf($currentUser, 0, 100);
        if (! $friendInfo['friends']) {
            echo "<p>Warning: Cannot run testNumFriends because the current user does not have a friend.</p>";
            return;
        }
        $friend = $friendInfo['friends'][0];
        $appUrl = 'http://example.com/testNumFriends';
        $numFriends = OpenSocial_GadgetHelperMock::numUsers($appUrl, $friend->title);
        $x = OpenSocialAppData::create($appUrl, $currentUser, false /* installed from application directory */);
        $x->save();
        $this->assertEqual($numFriends + 1, OpenSocial_GadgetHelperMock::numUsers($appUrl, $friend->title));
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
    
    //TODO Lots of testable stuff missing from here [Thomas David Baker 2008-08-07]
}

class OpenSocial_GadgetHelperMock extends OpenSocial_GadgetHelper {
    // Just to change the visibility for testing.
    public static function numUsers($appUrl, $screenName) {
        return parent::numUsers($appUrl, $screenName);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
