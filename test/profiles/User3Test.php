<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_UserHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_PrivacyHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_UserHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PrivacyHelper.php');

class User3Test extends UnitTestCase {

    public function testSearchText() {
        $user = W_Content::create('User');
        $user->setFullName('Jon');
        $this->assertEqual('Jon', $user->my->fullName);
        $this->assertEqual('', $user->my->profileAddress);
        $this->assertEqual(' J Jo Jon', $user->my->searchText);
        User::setProfileAddress($user, 'Foo');
        $this->assertEqual('Jon', $user->my->fullName);
        $this->assertEqual('Foo', $user->my->profileAddress);
        $this->assertEqual(' J Jo Jon F Fo Foo', $user->my->searchText);
        $user->setFullName('Dave');
        $this->assertEqual('Dave', $user->my->fullName);
        $this->assertEqual('Foo', $user->my->profileAddress);
        $this->assertEqual(' D Da Dav Dave F Fo Foo', $user->my->searchText);
        User::setProfileAddress($user, 'Bar');
        $this->assertEqual('Dave', $user->my->fullName);
        $this->assertEqual('Bar', $user->my->profileAddress);
        $this->assertEqual(' D Da Dav Dave B Ba Bar', $user->my->searchText);
    }

    public function testUseSetProfileAddress() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = file_get_contents($file);
            if (strpos($contents, 'my->profileAddress') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                if (strpos($line, '$user->my->profileAddress = $profileAddress;') !== false) { continue; }
                $this->assertTrue(! preg_match('@my->profileAddress += +@', $line), $line . ' - ' . $file . ' line ' . $i);
                $previousLine = $line;
            }
        }
    }

    public function testProfileAddress() {
        $this->assertEqual('zzyyxx55', User::profileAddress('zzyyxx55'));
    }

    /* @see BAZ-4563 */
    public function testLoadByObject() {
        $content = XN_Query::create('Content')->filter('owner')->filter('type','eic','User')->end(2)->execute();
        if (count($content) != 2) {
            return; // Need 2 user objects for testLoadByObject
        }
        $user1 = User::load($content[0]);
        $user2 = User::load(W_Content::create($content[1]));
        $this->assertTrue($user1 instanceof W_Content && ($user1->id == $content[0]->id));
        $this->assertTrue($user2 instanceof W_Content && ($user2->id == $content[1]->id));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



