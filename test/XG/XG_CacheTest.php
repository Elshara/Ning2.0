<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');

XN_Debug::allowDebug();

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(true);
    XG_Cache::clearStatistics();
}

class XG_CacheTest extends UnitTestCase {

    public function testMixedCaseProfile() {
        $p1 = XG_Cache::profiles('ningdev');
        $p2 = XG_Cache::profiles('NiNGdEV');
        $this->assertIdentical($p1, $p2);
    }

    public function testProfiles() {
        $this->assertEqual('', self::profilesToString(XG_Cache::profiles()));
        $this->assertEqual('NingDev', self::profilesToString(XG_Cache::profiles('NingDev')));
        $this->assertEqual('NingDev=>NingDev', self::profilesToString(XG_Cache::profiles(array('NingDev'))));
        $this->assertEqual('NULL', self::profilesToString(XG_Cache::profiles('')));
        $this->assertEqual('NULL', self::profilesToString(XG_Cache::profiles(NULL)));
        $this->assertEqual('NULL', self::profilesToString(XG_Cache::profiles('Dummy12345_UIFUHWEun')));
        $this->assertEqual('NingDev=>NingDev', self::profilesToString(XG_Cache::profiles('', NULL, 'NingDev')));
        $this->assertEqual('', self::profilesToString(XG_Cache::profiles('', NULL)));
        $this->assertEqual('NingDev=>NingDev', self::profilesToString(XG_Cache::profiles('', NULL, XN_Profile::load('NingDev'))));
        $this->assertEqual('NingDev=>NingDev', self::profilesToString(XG_Cache::profiles('', NULL, array(XN_Profile::load('NingDev')))));
        $this->assertEqual('NingDev=>NingDev', self::profilesToString(XG_Cache::profiles('', NULL, array(array(XN_Profile::load('NingDev'))))));
        $this->assertEqual('NingDev', self::profilesToString(XG_Cache::profiles('NingDev@users')));
        $this->assertEqual('JonathanAquino', self::profilesToString(XG_Cache::profiles('JonathanAquino@users')));
        $this->assertEqual('NingDev@users=>NingDev', self::profilesToString(XG_Cache::profiles(array('NingDev@users'))));
        $this->assertEqual('JonathanAquino@users=>JonathanAquino', self::profilesToString(XG_Cache::profiles(array('JonathanAquino@users'))));
    }

    private function profilesToString($profiles) {
        if ($profiles instanceof XN_Profile) { return $profiles->screenName; }
        if (is_null($profiles)) { return 'NULL'; }
        $strings = array();
        foreach ($profiles as $screenName => $profile) {
            $strings[] = $screenName . '=>' . $profile->screenName;
        }
        return implode(',', $strings);
    }


    public function testBaseConvert() {
        $this->assertEqual(XG_Cache::baseConvert(255, 10, 16), 'FF');
        $this->assertEqual(XG_Cache::baseConvert('0255', 10, 16), 'FF');

        // Test 0 for BAZ-1565
        for ($i = 0; $i < 126; $i++) {
            $this->assertIdentical((string) $i, XG_Cache::baseConvert($i, 10, 10));
        }
    }

    public function testWidgetKey() {
        $mainWidget = W_Cache::getWidget('main');
        // The first argument can be a widget
        $key1 = XG_Cache::widgetKey($mainWidget, 'apple');
        $this->assertEqual($key1, 'xg:' . XG_Cache::hash('main-apple'));

        // or a string
        $key2 = XG_Cache::widgetKey('main', 'banana');
        $this->assertEqual($key2, 'xg:' . XG_Cache::hash('main-banana'));

        W_Cache::push($mainWidget);
        // Just 1 argument? Use the current widget
        $key3 = XG_Cache::widgetKey('coconut');
        W_Cache::pop($mainWidget);
        $this->assertEqual($key3, 'xg:' . XG_Cache::hash('main-coconut'));
    }

    public function testKey() {
        /* --------------------------------------------------------------------------
   *   XN_Profile object                        xg-user-<screenName>
   *   User Content object                      xg-user-<content contributor screenName>
   *   'user', screen name as string            xg-user-<screenName>
   *   Other content object                     xg-content-<id>
   *   'promotion', content object              xg-promotion-<content object type>
   *   'moderation', XN_Profile or User object  xg-moderation-<screenName>
   *   'moderation', XN_Profile or User object,
   *       content object                       xg-moderation-<screenName>-<content object type>
   *   'type', content object                   xg-type-<content object type>
   *   'type', string                           xg-type-<string>
   *   XG_Embed object                          xg-embed-<embed locator>-<[2]>
   *
   * [2] The second part of the embed cache key is 'o' if the embed object is
   * owned by the current user and 'u' otherwise.
   */
        $profile = XN_Profile::load('david');
        $user = XN_Query::create('Content')->filter('owner')->filter('type','eic','User')->end(1)->uniqueResult();
        $w_user = W_Content::create($user);
        $content = XN_Query::create('Content')->filter('owner')->filter('type','eic','Invitation')->end(1)->uniqueResult();

        if ($content instanceof XN_Content) {
            $contentType = mb_strtolower($content->type);
            $w_content = W_Content::create($content);
        }
        if ($w_content instanceof W_Content) {
            $w_contentType = mb_strtolower($w_content->type);
        }

        if ($profile instanceof XN_Profile) {
            $this->assertEqual(XG_Cache::key($profile), 'xg-' . XG_Cache::hash("user-{$profile->screenName}"));
            $this->assertEqual(XG_Cache::key('moderation', $profile), 'xg-' . XG_Cache::hash("moderation-{$profile->screenName}"));
            if ($content instanceof XN_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $profile, $content), 'xg-' . XG_Cache::hash("moderation-{$profile->screenName}-{$contentType}"));
            }
            if ($w_content instanceof W_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $profile, $w_content), 'xg-' . XG_Cache::hash("moderation-{$profile->screenName}-{$w_contentType}"));
            }
        }
        if ($user instanceof XN_Content) {
            $userContributorName = mb_strtolower($user->contributorName);
            $this->assertEqual(XG_Cache::key($user), 'xg-' . XG_Cache::hash("user-$userContributorName"));
            $this->assertEqual(XG_Cache::key('moderation', $user), 'xg-' . XG_Cache::hash("moderation-$userContributorName"));
            $this->assertEqual(XG_Cache::key('type',$user), 'xg-' . XG_Cache::hash('type-' . mb_strtolower($user->type)));
            if ($content instanceof XN_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $user, $content), 'xg-' . XG_Cache::hash("moderation-$userContributorName-{$contentType}"));
            }
            if ($w_content instanceof W_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $user, $w_content), 'xg-' . XG_Cache::hash("moderation-$userContributorName-{$w_contentType}"));
            }
        }
        if ($w_user instanceof W_Content) {
            $w_userContributorName = mb_strtolower($w_user->contributorName);
            $this->assertEqual(XG_Cache::key($w_user), 'xg-' . XG_Cache::hash("user-$w_userContributorName"));
            $this->assertEqual(XG_Cache::key('moderation', $w_user), 'xg-' . XG_Cache::hash("moderation-$w_userContributorName"));
            $this->assertEqual(XG_Cache::key('type',$w_user), 'xg-' . XG_Cache::hash('type-' . mb_strtolower($w_user->type)));
            if ($content instanceof XN_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $w_user, $content), 'xg-' . XG_Cache::hash("moderation-$w_userContributorName-{$contentType}"));
            }
            if ($w_content instanceof W_Content) {
                $this->assertEqual(XG_Cache::key('moderation', $w_user, $w_content), 'xg-' . XG_Cache::hash("moderation-$w_userContributorName-{$w_contentType}"));
            }
        }
        if ($content instanceof XN_Content) {
            $this->assertEqual(XG_Cache::key($content), 'xg-' . XG_Cache::hash("content-{$content->id}"));
            $this->assertEqual(XG_Cache::key('promotion',$content), 'xg-' . XG_Cache::hash("promotion-{$contentType}"));
            $this->assertEqual(XG_Cache::key('type',$content), 'xg-' . XG_Cache::hash("type-$contentType"));
        }
        if ($w_content instanceof W_Content) {
            $this->assertEqual(XG_Cache::key($w_content), 'xg-' . XG_Cache::hash("content-{$w_content->id}"));
            $this->assertEqual(XG_Cache::key('promotion',$w_content), 'xg-' . XG_Cache::hash("promotion-{$w_contentType}"));
            $this->assertEqual(XG_Cache::key('type',$w_content), 'xg-' . XG_Cache::hash("type-$w_contentType"));
        }
        $this->assertEqual(XG_Cache::key('user','david'), 'xg-' . XG_Cache::hash('user-david'));
        $this->assertEqual(XG_Cache::key('type','Comment'), 'xg-' . XG_Cache::hash('type-comment'));
    }

    public function testCacheSaveAndLoad() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'ethel the aardvark went quantity surveying';
        XG_Cache::save($cacheId, $data);
        $this->assertEqual(XG_Cache::load($cacheId), $data);
        XG_Cache::remove($cacheId);
    }

    public function testCacheSaveAndTest() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'ethel the aardvark went quantity surveying';
        XG_Cache::save($cacheId, $data);
        $this->assertIdentical(XG_Cache::test($cacheId), true);
        XG_Cache::remove($cacheId);
    }

    public function testCacheMiss() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->assertIsA(XG_Cache::load(uniqid()), XG_Cache_Miss);
    }

    public function testCacheTestMiss() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->assertIdentical(XG_Cache::test(uniqid()), false);
    }

    public function testCacheAgeHit() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = "why don't you try WH smith's?";
        $now = time();
        XG_Cache::save($cacheId, $data);
        $this->assertEqual(XG_Cache::load($cacheId, 10 + time() - $now), $data);
        XG_Cache::remove($cacheId);
    }

    public function testCacheAgeMiss() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = "why don't you try WH smith's?";
        XG_Cache::save($cacheId, $data);
        sleep(5);
        $this->assertIsA(XG_Cache::load($cacheId, 2), XG_Cache_Miss);
        XG_Cache::remove($cacheId);
    }

    public function testSaveAndLoadWithKey() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'I did, they sent me here.';
        $key = 'knickerless';
        XG_Cache::save($cacheId, $data, array('keys' => $key));
        $this->assertEqual(XG_Cache::load($cacheId, null, array('keys' => $key)), $data);
        XG_Cache::remove($cacheId);
    }

    public function testSaveAndLoadWithKeys() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'I did, they sent me here.';
        $keys = array('knickerless', 'nickleby');
        XG_Cache::save($cacheId, $data, array('keys' => $keys));
        $this->assertEqual(XG_Cache::load($cacheId, null, array('keys' => $keys)), $data);
        XG_Cache::remove($cacheId);
    }

    public function testSaveAndLoadWithKeySubset() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'I did, they sent me here.';
        $keys = array('knickerless', 'nickleby');
        XG_Cache::save($cacheId, $data, array('keys' => $keys));
        $this->assertEqual(XG_Cache::load($cacheId, null, array('keys' => $keys[0])), $data);
        XG_Cache::remove($cacheId);
    }

    public function testSaveAndLoadWithDifferentKeys() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data_1 = 'I did, they sent me here.';
        $key_1 = 'knickerless';
        $data_2 = 'Certainly not!';
        $key_2 = 'nickelby';
        XG_Cache::save($cacheId, $data_1, array('keys' => $key_1));
        XG_Cache::save($cacheId, $data_2, array('keys' => $key_2));
        $this->assertEqual(XG_Cache::load($cacheId, null, array('keys' => $key_1)), $data_1);
        $this->assertEqual(XG_Cache::load($cacheId, null, array('keys' => $key_2)), $data_2);
        XG_Cache::remove($cacheId);
    }

    public function testCacheSaveAndRemoveAndMiss() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'ethel the aardvark went quantity surveying';
        XG_Cache::save($cacheId, $data);
        $this->assertEqual(XG_Cache::load($cacheId), $data);
        XG_Cache::remove($cacheId);
        $this->assertIsA(XG_Cache::load($cacheId), XG_Cache_Miss);
    }

    public function testCacheSaveAndInvalidateAndMiss() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $data = 'ethel the aardvark went quantity surveying';
        $key = 'biggles';
        XG_Cache::save($cacheId, $data, array('keys' => $key));
        $this->assertEqual(XG_Cache::load($cacheId), $data);
        XG_Cache::invalidate($key);
        $this->assertIsA(XG_Cache::load($cacheId), XG_Cache_Miss);

    }

     public function testCacheSaveAndInvalidateAndMiss2() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $cacheId = uniqid();
        $cacheId_2 = uniqid();
        $data = 'ethel the aardvark went quantity surveying';
        $data_2 = 'rarnaby budge';
        $keys = array('biggles','combs','his','hair');
        XG_Cache::save($cacheId, $data, array('keys' => $keys));
        XG_Cache::save($cacheId_2, $data_2, array('keys' => $keys[0]));
        $this->assertEqual(XG_Cache::load($cacheId), $data);
        XG_Cache::invalidate($key[2]);
        $this->assertIsA(XG_Cache::load($cacheId), XG_Cache_Miss);
        $this->assertEqual(XG_Cache::load($cacheId_2), $data_2);
    }

    public function testCollidingKeys() {
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        // XG_Cache::allowDebug();
        // These two strings both MD5-hash to the same value
        // See http://www.x-ways.net/md5collision.html

        $vec1 = array(0xd1, 0x31, 0xdd, 0x02, 0xc5, 0xe6, 0xee, 0xc4, 0x69, 0x3d, 0x9a, 0x06, 0x98, 0xaf, 0xf9, 0x5c,
        0x2f, 0xca, 0xb5, 0x87, 0x12, 0x46, 0x7e, 0xab, 0x40, 0x04, 0x58, 0x3e, 0xb8, 0xfb, 0x7f, 0x89,
        0x55, 0xad, 0x34, 0x06, 0x09, 0xf4, 0xb3, 0x02, 0x83, 0xe4, 0x88, 0x83, 0x25, 0x71, 0x41, 0x5a,
        0x08, 0x51, 0x25, 0xe8, 0xf7, 0xcd, 0xc9, 0x9f, 0xd9, 0x1d, 0xbd, 0xf2, 0x80, 0x37, 0x3c, 0x5b,
        0xd8, 0x82, 0x3e, 0x31, 0x56, 0x34, 0x8f, 0x5b, 0xae, 0x6d, 0xac, 0xd4, 0x36, 0xc9, 0x19, 0xc6,
        0xdd, 0x53, 0xe2, 0xb4, 0x87, 0xda, 0x03, 0xfd, 0x02, 0x39, 0x63, 0x06, 0xd2, 0x48, 0xcd, 0xa0,
        0xe9, 0x9f, 0x33, 0x42, 0x0f, 0x57, 0x7e, 0xe8, 0xce, 0x54, 0xb6, 0x70, 0x80, 0xa8, 0x0d, 0x1e,
        0xc6, 0x98, 0x21, 0xbc, 0xb6, 0xa8, 0x83, 0x93, 0x96, 0xf9, 0x65, 0x2b, 0x6f, 0xf7, 0x2a, 0x70);

        $vec2 = array(0xd1, 0x31, 0xdd, 0x02, 0xc5, 0xe6, 0xee, 0xc4, 0x69, 0x3d, 0x9a, 0x06, 0x98, 0xaf, 0xf9, 0x5c,
        0x2f, 0xca, 0xb5, 0x07, 0x12, 0x46, 0x7e, 0xab, 0x40, 0x04, 0x58, 0x3e, 0xb8, 0xfb, 0x7f, 0x89,
        0x55, 0xad, 0x34, 0x06, 0x09, 0xf4, 0xb3, 0x02, 0x83, 0xe4, 0x88, 0x83, 0x25, 0xf1, 0x41, 0x5a,
        0x08, 0x51, 0x25, 0xe8, 0xf7, 0xcd, 0xc9, 0x9f, 0xd9, 0x1d, 0xbd, 0x72, 0x80, 0x37, 0x3c, 0x5b,
        0xd8, 0x82, 0x3e, 0x31, 0x56, 0x34, 0x8f, 0x5b, 0xae, 0x6d, 0xac, 0xd4, 0x36, 0xc9, 0x19, 0xc6,
        0xdd, 0x53, 0xe2, 0x34, 0x87, 0xda, 0x03, 0xfd, 0x02, 0x39, 0x63, 0x06, 0xd2, 0x48, 0xcd, 0xa0,
        0xe9, 0x9f, 0x33, 0x42, 0x0f, 0x57, 0x7e, 0xe8, 0xce, 0x54, 0xb6, 0x70, 0x80, 0x28, 0x0d, 0x1e,
        0xc6, 0x98, 0x21, 0xbc, 0xb6, 0xa8, 0x83, 0x93, 0x96, 0xf9, 0x65, 0xab, 0x6f, 0xf7, 0x2a, 0x70);

        $cacheId_1 = implode('', array_map('chr', $vec1));
        $cacheId_2 = implode('', array_map('chr', $vec2));
        $this->assertTrue($cacheId_1 != $cacheId_2, 'ensure these IDs are different');

        $this->assertEqual(XG_Cache::hash($cacheId_1), XG_Cache::hash($cacheId_2));
        $data_1 = 'The Amazing Adventures of Captain Gladys Stoutpamphlet';
        $data_2 = 'and her Intrepid Spaniel Stig Amongst the Giant Pygmies of Beckles';
        XG_Cache::save($cacheId_1, $data_1);
        $this->assertTrue(XG_Cache::load($cacheId_1) == $data_1);
        XG_Cache::save($cacheId_2, $data_2);
        $this->assertTrue(XG_Cache::load($cacheId_1) == $data_1);
        $this->assertTrue(XG_Cache::load($cacheId_2) == $data_2);
    }

    public function testSetGetMaxCacheSize() {
        $start = XG_Cache::getMaxCacheSize();
        $new = $start * 2 + 1;
        XG_Cache::setMaxCacheSize($new);
        $this->assertEqual($new, XG_Cache::getMaxCacheSize());
        XG_Cache::setMaxCacheSize($start);
        $this->assertEqual($start, XG_Cache::getMaxCacheSize());
    }

    public function testSetGetCacheCleanupPercentage() {
        $start = XG_Cache::getCacheCleanupPercentage();
        $new = $start * 2 + 1;
        if ($new > 100) { $new = 98; }
        XG_Cache::setCacheCleanupPercentage($new);
        $this->assertEqual($new, XG_Cache::getCacheCleanupPercentage());
        XG_Cache::setCacheCleanupPercentage($start);
        $this->assertEqual($start, XG_Cache::getCacheCleanupPercentage());
    }

    public function testCacheCleanup() {
        $oldCacheSize = XG_Cache::getMaxCacheSize();
        $oldPercentage = XG_Cache::getCacheCleanupPercentage();
        $newCacheSize = 3;
        $overflow = 2;
        XG_Cache::setMaxCacheSize($newCacheSize);
        XG_Cache::setCacheCleanupPercentage(100);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        // Write $overflow more values than should fit into the cache
        for ($i = 0; $i < ($newCacheSize + $overflow); $i++) {
            $data[$i] = array('id' => uniqid(), 'value' => "$i-" . uniqid());
            XG_Cache::save($data[$i]['id'], $data[$i]['value']);
        }
        foreach ($data as $k => $v) {
            $load = XG_Cache::load($v['id']);
            if ($k < $overflow) {
                $this->assertIsA($load, XG_Cache_Miss);
            } else {
                $this->assertEqual($load, $v['value']);
            }
        }
        // Restore Default Values
        XG_Cache::setMaxCacheSize($oldCacheSize);
        XG_Cache::setCacheCleanupPercentage($oldPercentage);
    }

    public function testGatherStatistics() {
        if (! $_GET['stats']) { return; }
        $stats = XG_Cache::getStatistics();
        print '<table border="1">';
        foreach ($stats as $line) {
            print '<tr><td>' . implode('</td><td>', explode("\t",$line)) . '</td></tr>';
        }
        print '</table>';
        $this->assertNotEqual(XG_Cache::getCacheSize(), 0);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
        $this->assertEqual(XG_Cache::getCacheSize(), 0);
    }

    public function testGetSetCacheTooOldPercentage() {
        $start = XG_Cache::getTooOldCleanupPercentage();
        $new = $start * 2 + 1;
        if ($new > 100) { $new = 98; }
        XG_Cache::setTooOldCleanupPercentage($new);
        $this->assertEqual($new, XG_Cache::getTooOldCleanupPercentage());
        XG_Cache::setTooOldCleanupPercentage($start);
        $this->assertEqual($start, XG_Cache::getTooOldCleanupPercentage());
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
