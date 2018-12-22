<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz9491CmdlineTest extends CmdlineTestCase {

    public function testUseFriendHelperSetContactStatus() {
        // Use Profiles_FriendHelper::instance()->setContactStatus instead of XN_Profile::setContactStatus,
        // so that caches are properly invalidated [Jon Aquino 2008-09-01]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (mb_strpos($file, '/test') !== false) { continue; }
            if (mb_strpos($file, '/Profiles_FriendHelper') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (mb_strpos($contents, 'setContactStatus') === FALSE) { continue; }
            $contents = str_replace('Profiles_FriendHelper::instance()->setContactStatus', '', $contents);
            $this->assertTrue(mb_strpos($contents, 'setContactStatus(') === FALSE, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
