<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_ApplicationDirectoryHelper.php');

class OpenSocial_ApplicationDirectoryHelperTest extends UnitTestCase {

    public function testGetApplicationDirectoryInfo() {
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getApplicationDirectoryInfo(null, null, 0, 1);
        $this->assertEqual('ok', $appInfo['status']);
        $this->assertEqual(1, count($appInfo['apps']));
        foreach ($appInfo['apps'] as $app) {
            $this->assertTrue($app['prefs']['title']);
        }
        //TODO: Lots more we can test here [Thomas David Baker 2008-08-20]
    }
    
    public function testGetAppUrls() {
        $this->assertEqual(array('http://example.com/example.xml', 'http://example.org/2.xml'), 
            OpenSocial_ApplicationDirectoryHelper::getAppUrls(array(array('appUrl' => 'http://example.com/example.xml'), array('appUrl' => 'http://example.org/2.xml'))));
        $obj = new StdClass();
        $obj->my = new StdClass();
        $obj->my->appUrl = 'http://example.com/example.xml';
        $this->assertEqual(array('http://example.com/example.xml'), OpenSocial_ApplicationDirectoryHelper::getAppUrls(array($obj)));
    }
    
    public function testGetRemoteApplicationDirectoryInfo() {
        // We can't make too many assumptions about what is in the directory but let's at least exercise the code.
        $apps = OpenSocial_ApplicationDirectoryHelper::getRemoteApplicationDirectoryInfo();
        $this->assertTrue(count($apps) > 0);
    }
    
    public function testGetRemoteApplicationDirectoryInfoFromUrls() {
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getRemoteApplicationDirectoryInfo();
        $this->assertEqual('ok', $appInfo['status']);
        $app = $appInfo['apps'][0];
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getRemoteApplicationDirectoryInfoFromUrls(array($app['appUrl']));
        $this->assertEqual('ok', $appInfo['status']);
        $this->assertEqual(1, count($appInfo['apps']));
        $this->assertEqual($app['appUrl'], $appInfo['apps'][0]['appUrl']);
    }
    
    public function testGetLocalApplicationDirectoryInfo() {
        $app = OpenSocialApp::load('http://example.com/testGetLocalApplicationDirectoryInfo', TRUE /* create */);
        $app->save();
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getLocalApplicationDirectoryInfo();
        $this->assertEqual('ok', $appInfo['status']);
        $this->assertTrue(count($appInfo['apps']) > 0);
    }
    
    public function testGetLocalApplicationDirectoryInfoFromUrls() {
        $app = OpenSocialApp::load('http://example.com/testGetLocalApplicationDirectoryInfoFromUrls', TRUE /* create */);
        $app->save();
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getLocalApplicationDirectoryInfoFromUrls(array($app->my->appUrl));
        $this->assertEqual('ok', $appInfo['status']);
        $this->assertEqual(1, count($appInfo['apps']));
        $this->assertEqual($app->my->appUrl, $appInfo['apps'][0]['appUrl']);
    }
    
    public function testGetCategeories() {
        $categories = OpenSocial_ApplicationDirectoryHelper::getCategories();
        $this->assertTrue(count($categories) > 10);
    }
    
    public function testGetAppDetails() {
        $localUrl = 'http://example.com/testGetAppDetails';
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getRemoteApplicationDirectoryInfo();
        $remoteApp = $appInfo['apps'][0];
        $localApp = OpenSocialApp::load($localUrl, TRUE /* create */);
        $localApp->save();
        $apps = OpenSocial_ApplicationDirectoryHelper::getAppDetails(array($localUrl, $remoteApp['appUrl']));
        $this->assertEqual(2, count($apps));
    }
    
    public function testCombineAppInfo() {
        $info1 = array('total' => 2, 
            'apps' => array(array('appUrl' => 'http://example.com/testCombineAppInfo1'), array('appUrl' => 'http://example.com/testCombineAppInfo2')));
        $info2 = array('total' => 100,
            'apps' => array(array('appUrl' => 'http://example.com/testCombineAppInfo1', 'prefs' => array('title' => 'Example')), 
                            array('appUrl' => 'http://example.com/testCombineAppInfo3')));
        $combined = OpenSocial_ApplicationDirectoryHelper::combineAppInfo($info1, $info2);
        $this->assertEqual(100, $combined['total']);
        $this->assertEqual(3, count($combined['apps']));
    }
    
    public function testIsAppApproved() {
        $appInfo = OpenSocial_ApplicationDirectoryHelper::getRemoteApplicationDirectoryInfo(null, null, 0, 1);
        $approvedApp = $appInfo['apps'][0];
        $approvedApp['approved'] = true;
        foreach (array('approved' => $approvedApp, 'unapproved' => array('appUrl' => 'http://example.com/testIsAppApproved')) as $type => $app) {
            if ($app) {
                $this->assertTrue(! ($app['approved'] XOR OpenSocial_ApplicationDirectoryHelper::isAppApproved($app['appUrl'])));
            } else {
                echo "Unable to test an $type app";
            }
        }
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
