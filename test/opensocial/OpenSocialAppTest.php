<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocialAppTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::deleteTestObjects();
    }
    
    public function testLoad() {
        $url = 'http://example.com/OpenSocialAppTest/testLoad';
        $app = OpenSocialApp::load($url, TRUE /* create if not found */);
        $app->my->numMembers = $app->my->numMembers + 1;
        $app->my->numReviews = 5;
        $app->my->avgRating = 2.3;
        $app->save();
        $app = OpenSocialApp::load($url, FALSE /* do not create if not found */);
        $this->assertEqual(1, $app->my->numMembers);
        $this->assertEqual(5, $app->my->numReviews);
        $this->assertEqual(2.3, $app->my->avgRating);
    }
    
    public function testLoadMultiple() {
        $urls = array('http://example.com/OpenSocialAppTest/testLoadMultiple1',
            'http://example.com/OpenSocialAppTest/testLoadMultiple10',
            'http://example.com/OpenSocialAppTest/testLoadMultiple100');
        foreach ($urls as $url) {
            $app = OpenSocialApp::load($url, TRUE /* create if not found */);
            $app->my->numMembers = mb_strlen($url);
            $app->save();
        }
        $apps = OpenSocialApp::loadMultiple($urls);
        $this->assertEqual(count($urls), count($apps));
        $this->assertEqual(mb_strlen($url), $app->my->numMembers);
    }
    
    public function testMembers() {
        $app = OpenSocialApp::load('http://example.com/testMembers', TRUE /* create if not found */);
        $this->assertEqual(0, count(OpenSocialApp::getMembers($app)));
        OpenSocialApp::addMember($app, 'Alice');
        $this->assertEqual(1, count(OpenSocialApp::getMembers($app)));
        OpenSocialApp::addMember($app, 'Bob');
        $this->assertEqual(2, count(OpenSocialApp::getMembers($app)));
        OpenSocialApp::removeMember($app, 'Alice');
        $this->assertEqual(1, count(OpenSocialApp::getMembers($app)));
        OpenSocialApp::addMember($app, 'Charlie');
        $this->assertEqual(2, count(OpenSocialApp::getMembers($app)));
    }
    
    public function testFind() {
        $urls = array('http://example.com/testFind1', 'http://example.com/testFind2', 'http://example.com/testFind3');
        $app = OpenSocialApp::load($urls[0], TRUE /* create if not found */);
        $app->save();
        $app = OpenSocialApp::load($urls[1], TRUE /* create if not found */);
        $app->save();
        $app = OpenSocialApp::load($urls[2], TRUE /* create if not found */);
        $app->save();
        $appInfo = OpenSocialApp::find(0, 3, 'latest');
        $apps = $appInfo['apps'];
        $this->assertTrue($appInfo['numApps'] >= 3);
        $this->assertEqual(3, count($apps));
        $this->assertEqual($urls[2], $apps[0]->my->appUrl);
        $this->assertEqual($urls[1], $apps[1]->my->appUrl);
        $this->assertEqual($urls[0], $apps[2]->my->appUrl);        
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
