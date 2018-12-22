<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocialAppDataTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::deleteTestObjects();
    }

    public function testCreate() {
        $url = 'http://example.com/9999999998';
        $appData = OpenSocialAppData::create($url, 'a', false /* installed from application directory */);
        $appData->save();
        $query = XN_Query::create('Content')->filter('owner')
            ->filter('type', '=', 'OpenSocialAppData')->filter('appUrl', '=', $url)
            ->filter('user', '=', 'a')->alwaysReturnTotalCount(true);
        $query->execute();
        $this->assertTrue(1, $query->getTotalCount());
    }
    
    public function testLoadMultiple() {
        $appData = OpenSocialAppData::create("http://url.com", 'c', false /* installed from application directory */);
        $appData->save();
        $x = OpenSocialAppData::loadMultiple("http://url.com", 'c');
        $this->assertNotNull($x);
        $this->assertTrue(count($x['apps']) > 0);
        $this->assertTrue($x['total'] > 0);
        $this->assertEqual("http://url.com", $x['apps'][0]->my->appUrl);
        $this->assertEqual('c', $x['apps'][0]->my->user);
    }
    
    public function testLoadMultipleUser() {
        $appData = OpenSocialAppData::create('http://example.com/testLoadMultipleUser1', 'Ziggy', false /* installed from application directory */);
        $appData->save();
        $appData = OpenSocialAppData::create('http://example.com/testLoadMultipleUser2', 'Ziggy', false /* installed from application directory */);
        $appData->save();
        $apps = OpenSocialAppData::loadMultiple(null, 'Ziggy');
        $this->assertEqual(2, count($apps));
    }
    
    public function testLoad() {
        $appData = OpenSocialAppData::create("http://url.com", 'b', false /* installed from application directory */);
        $appData->save();
        $x = OpenSocialAppData::load("http://url.com", 'b');
        $this->assertNotNull($x);
        $this->assertEqual("http://url.com", $x->my->appUrl);
        $this->assertEqual('b', $x->my->user);
    }
    
    public function testGetAndSet() {
        $url = 'http://example.com/9999999993';
        $x = OpenSocialAppData::create($url, XN_Profile::current()->screenName, true /* installed by url */);
        $this->assertNull($x->get('a'));
        $x->set('a', 1);
        $this->assertEqual(1, $x->get('a'));
        $this->assertTrue($x->save());
        $x->save();
        $x = OpenSocialAppData::load($url, XN_Profile::current()->screenName);
        $this->assertEqual(1, $x->get('a'));
    }
    
    public function testGetData() {
        $x = OpenSocialAppData::create('http://example.com/999999995', XN_Profile::current()->screenName, false /* installed from application directory */);
        $x->set('a', 1);
        $x->set('b', 1);
        $this->assertEqual(array('a' => 1, 'b' => 1), $x->getData());
    }
    
    public function testInvalidKeys() {
        list($url, $user) = array('http://example.com/9999999979', XN_Profile::current()->screenName);
        $x = OpenSocialAppData::create($url, $user, true /* installed by url */);
        $caught = false;
        try {
            $x->set('!', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            $x->set('looksalright!butisnt', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            $x->set("O'Leary", 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            $x->set('spaces are not ok', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            $x->set('sneakyontheend^', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            // This is the key used in Google's 0.7 compliance tests.
            $x->set('badKey::!!', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $caught = false;
        try {
            $x->set('thisoneisok', 1);
        } catch (OpenSocial_InvalidKeyException $e) {
            $caught = true;
        }
        $this->assertFalse($caught);
    }
    
    public function testDeleteKey() {
        $x = OpenSocialAppData::create('http://example.com/999999994', XN_Profile::current()->screenName, false /* installed from application directory */);
        $x->set('a', 1);
        $x->set('b', 2);
        $x->set('c', 3);
        $x->deleteKey('a');
        $x->save();
        $x->deleteKey('c');
        $this->assertEqual(array('b' => 2), $x->getData());
    }
    
    public function testUpdateSettings() {
        list($url, $user) = array('http://example.com/999999988', XN_Profile::current()->screenName);
        $x = OpenSocialAppData::create($url, $user, true /* installed by url */);
        $x->my->isOnMyPage = false;
        $x->my->canAddActivities = false;
        $x->my->canSendMessages = true;
        $x->my->promptBeforeSending = false;
        $x->save();
        OpenSocialAppData::updateSettings($url, $user, 
            array('isOnMyPage' => true, 'canAddActivities' => true, 'canSendMessages' => false));
        $x = OpenSocialAppData::load($url, $user);
        $this->assertTrue($x->my->isOnMyPage);
        $this->assertTrue($x->my->canAddActivities);
        $this->assertFalse($x->my->canSendMessages);
        $this->assertTrue($x->my->promptBeforeSending);  // if canSendMessages is false, we automatically set promptBeforeSending to true.
    }

    public function testUpdateSetting() {
        list($url, $user) = array('http://example.com/999999987', XN_Profile::current()->screenName);
        $x = OpenSocialAppData::create($url, $user, true /* installed by url */);
        $x->my->promptBeforeSending = false;
        $x->save();
        OpenSocialAppData::updateSetting($url, $user, 'promptBeforeSending', true);
        $x = OpenSocialAppData::load($url, $user);
        $this->assertTrue($x->my->promptBeforeSending);
    }
    
    public function testRateLimitCheckAndUpdate() {
        list($url, $user) = array('http://example.com/999999876', XN_Profile::current()->screenName);
        $appData = OpenSocialAppData::create($url, $user, false /* installed from application directory */);
        $appData->save();
        $this->assertNotNull($appData->my->rateLimitBlob, "rateLimitBlob is NULL");
        $rateLimitInfo = unserialize($appData->my->rateLimitBlob);
        $this->assertEqual(count($rateLimitInfo), 0, "rateLimitBlob has non-0 entries");
        $rateLimitEntry = $rateLimitInfo[OpenSocialAppData::OPENSOC_RATELIMIT_SENDMESSAGE];
        $this->assertNull($rateLimitEntry, 'rateLimitEntry for '.OpenSocialAppData::OPENSOC_RATELIMIT_SENDMESSAGE.' is already set!');

        $beforeTimestamp = time();
        // run the rateLimitCheckAndUpdate function
        $exceeded = OpenSocialAppData::rateLimitCheckAndUpdate($url, $user, OpenSocialAppData::OPENSOC_RATELIMIT_SENDMESSAGE); // use the default msgType
        
        // check for the updated value
        $appData = OpenSocialAppData::load($url, $user);
        $this->assertNotNull($appData->my->rateLimitBlob, "rateLimitBlob is NULL");
        $rateLimitInfo = unserialize($appData->my->rateLimitBlob);
        $this->assertEqual(count($rateLimitInfo), 1, "rateLimitBlob should have 1 entry");
        $rateLimitEntry = $rateLimitInfo[OpenSocialAppData::OPENSOC_RATELIMIT_SENDMESSAGE];
        $this->assertTrue($rateLimitEntry['timestamp'] >= $beforeTimestamp, 'rateLimitEntry should be set after '.$beforeTimestamp.'!');
        $this->assertEqual($rateLimitEntry['count'], 1, 'rateLimitEntry value is not 1!');
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
