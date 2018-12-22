<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocial_ActivityControllerTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('opensocial');
        W_Cache::current('W_Widget')->includeFileOnce('/controllers/ActivityController.php');
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php');
    }

    public function testGetActivities() {
        $url = "http://example.com/9999999992/";
        $currentUser = XN_Profile::current()->screenName;
        $this->createActivity($url, $currentUser, "Get Title", "Get Description");
        $this->createActivity($url, $currentUser, "Get Title 2", "Get Description 2");
        $this->createActivity($url, $currentUser, "Get Title 3", "Get Description 3");
        $activities = OpenSocial_ActivityController::getActivities($url, array($currentUser), null);
        list($title1, $title2, $title3, $desc1, $desc2, $desc3) = array(false, false, false, false, false, false);
        $this->assertTrue(count($activities) >= 3);
        $myAppActivities = array();
        foreach ($activities as $activity) {
            if ($activity->my->link == $url) {
                $myAppActivities[] = $activity;
            }
        }
        $this->assertEqual(3, count($myAppActivities));
        foreach ($myAppActivities as $activity) {
            $this->assertEqual($currentUser, $activity->my->members);
            if ($activity->title == 'Get Title') {
                $title1 = true;
            } else if ($activity->title == 'Get Title 2') {
                $title2 = true;
            } else if ($activity->title == 'Get Title 3') {
                $title3 = true;
            }
            if ($activity->description == 'Get Description') {
                $desc1 = true;
            } else if ($activity->description == 'Get Description 2') {
                $desc2 = true;
            } else if ($activity->description == 'Get Description 3') {
                $desc3 = true;
            }
        }
        $this->assertTrue($title1);
        $this->assertTrue($title2);
        $this->assertTrue($title3);
        $this->assertTrue($desc1);
        $this->assertTrue($desc2);
        $this->assertTrue($desc3);
        $activities2 = OpenSocial_ActivityController::getActivities($url, array($currentUser), $activities[2]->id);
        $this->assertEqual(1, count($activities2));
    }
    
    public function testPostActivity() {
        $url = "http://example.com/9999999992/";
        $currentUser = XN_Profile::current()->screenName;
        $this->createActivity($url, $currentUser, "Post Title", "Post Description");
        $query = XN_Query::create('Content')->filter('owner')->begin(0)->end(1)
            ->order('createdDate', 'desc', XN_Attribute::DATE);
        $results = $query->execute();
        $this->assertEqual(1, count($results));
        foreach ($results as $result) {
            $this->assertEqual("Post Title", $result->title);
            $this->assertEqual("Post Description", $result->description);
            $this->assertEqual($currentUser, $result->my->members);
        }
    }
    
    public function testIsRateLimited() {
        $url = 'http://example.com/testIsRateLimited.xml';
        $currentUser = XN_Profile::current()->screenName;
        $appData = new stdClass();
        $appData->my->appUrl = $url;
        for ($i = 0; $i < OpenSocial_ActivityController::ACTIVITY_LOG_ITEMS_ALLOWED_PER_APP_PER_USER_PER_DAY; $i++) {
            $this->assertFalse(OpenSocial_ActivityController::isRateLimited($appData->my->appUrl, $currentUser));
            $this->createActivity($url, $currentUser, "Post Title", "Post Description");
        }
        $this->assertTrue(OpenSocial_ActivityController::isRateLimited($appData->my->appUrl, $currentUser));
        $this->createActivity($url, $currentUser, "Post Title", "Post Description");
        $this->assertTrue(OpenSocial_ActivityController::isRateLimited($appData->my->appUrl, $currentUser));
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
    
    // TODO: When we have the option in Privacy and Feature Control to turn off activity stream writing
    // globally then this will fail on networks with that option disabled.  That will cause the tests
    // that depend on it to fail.  [Thomas David Baker 2008-07-29]
    private function createActivity($appUrl, $user, $title, $text) {
        $viewerId = $user;
        $activity = new stdClass();
        $activity->title = $title;
        $activity->body = $text;
        $appData = new stdClass();
        $appData->my->appUrl = $appUrl;
        OpenSocial_ActivityController::postActivity($appData, $viewerId, $activity);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
