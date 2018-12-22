<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
Mock::generate('stdClass', 'MockW_Widget', array('saveConfig'));

class XG_SecurityHelperTest extends UnitTestCase {

    public function testUserIsAdminOrContributor() {
        $profile = XN_Profile::create('joe', 'password');
        $object = XN_Content::create('food');
        $this->assertFalse(XG_SecurityHelper::userIsAdminOrContributor($profile, $object));
        $this->assertTrue(XG_SecurityHelper::userIsAdminOrContributor(XN_Profile::current(), $object));
    }

    public function testGetAdministrators() {
        $ownerCount = 0;
        foreach (XG_SecurityHelper::getAdministrators() as $user) {
            if ($user->title == XN_Application::load()->ownerName) { $ownerCount++; }
        }
        $this->assertEqual(1, $ownerCount);
    }

    public function testGetAdministratorsBesidesOwner() {
        $ownerCount = 0;
        foreach (XG_SecurityHelper::getAdministratorsBesidesOwner() as $user) {
            if ($user->title == XN_Application::load()->ownerName) { $ownerCount++; }
        }
        $this->assertEqual(0, $ownerCount);
    }

    public function testCheckCsrfTokenProper() {
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('a1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-23T18:15:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertFalse(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-24T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('a1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1', '2008-04-22T18:22:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1', '2008-04-22T18:22:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertTrue(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-23T18:15:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1', '2008-04-22T18:22:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertFalse(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-24T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1', '2008-04-22T18:22:47+00:00' => 'a1'), '2008-04-22T18:20:47+00:00'));
        $this->assertFalse(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), null));
        $this->assertFalse(TestSecurityHelper::checkCsrfTokenProper('b1', strtotime('2008-04-22T18:20:47+00:00'), array('2008-04-22T18:20:47+00:00' => 'a1'), false));
    }

    public function testGetCsrfTokensProper() {
        $this->assertEqual(array(), TestSecurityHelper::getCsrfTokensProper('jane', array()));
        $this->assertEqual(array('3a18aacd193d5be8f39a443d5d0eb220', 'd5a5635c7e2cfc0d40bab54200b11620'), TestSecurityHelper::getCsrfTokensProper('jane', array('1', '2')));
    }

    public function testGetCsrfSaltsProper1() {
        $this->doTestGetCsrfSaltsProper(1, array(), array('2008-04-22T18:22:47+00:00' => 11111));
    }

    public function testGetCsrfSaltsProper2() {
        $this->doTestGetCsrfSaltsProper(0, array('2008-04-20T18:22:47+00:00' => 11111), array('2008-04-20T18:22:47+00:00' => 11111));
    }

    public function testGetCsrfSaltsProper3() {
        $this->doTestGetCsrfSaltsProper(1, array('2008-04-18T18:22:47+00:00' => 22222), array('2008-04-22T18:22:47+00:00' => 11111, '2008-04-18T18:22:47+00:00' => 22222));
    }

    public function testGetCsrfSaltsProper4() {
        $this->doTestGetCsrfSaltsProper(1, array('2008-04-18T18:22:47+00:00' => 22222, '2008-04-10T18:22:47+00:00' => 33333), array('2008-04-22T18:22:47+00:00' => 11111, '2008-04-18T18:22:47+00:00' => 22222));
    }

    private function doTestGetCsrfSaltsProper($saveConfigCount, $oldSalts, $newSalts) {
        $mainWidget = new MockW_Widget();
        $mainWidget->privateConfig = array('csrfSalts' => serialize($oldSalts));
        $mainWidget->expectCallCount('saveConfig', $saveConfigCount);
        TestSecurityHelper::getCsrfSaltsProper($mainWidget, 1208888567, 3600*24*3, 'test-' . mt_rand(), 11111);
        $this->assertEqual(serialize($newSalts), $mainWidget->privateConfig['csrfSalts']);
    }

    public function testSpecifyXgTokenInAsyncJobs() {
        // Apply XG_SecurityHelper::addCsrfToken() to callback URL given to XN_Task::create [Jon Aquino 2008-04-28]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (basename($file) == 'XG_JobHelper.php') { $contents = str_replace('$job->addTask( XN_Task::create($url, array(self::KEY => $asyncKey, \'extraFile\' => $fileToLoad, \'task\'=>$task) ) );', '', $contents); }
            $this->assertNoPattern('@XN_Task::create(?!.*addCsrfToken)@ui', $contents, $file);
        }
    }

    public function testAddCsrfToken() {
        $this->assertEqual('http://example.org/?xg_token=' . XG_SecurityHelper::getCsrfToken(), XG_SecurityHelper::addCsrfToken('http://example.org/'));
    }

}

class TestSecurityHelper extends XG_SecurityHelper {
    public static function getCsrfTokenProper($currentProfile) {
        return parent::getCsrfTokenProper($currentProfile);
    }
    public static function getCsrfTokensProper($screenName, $salts) {
        return parent::getCsrfTokensProper($screenName, $salts);
    }
    public static function getCsrfSaltsProper($mainWidget, $time, $maxAge, $lockId = 'generate-csrf-salts', $newSalt = null) {
        return parent::getCsrfSaltsProper($mainWidget, $time, $maxAge, $lockId, $newSalt);
    }
    public static function checkCsrfTokenProper($token, $time, $validTokens, $gracePeriodStart) {
        return parent::checkCsrfTokenProper($token, $time, $validTokens, $gracePeriodStart);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
