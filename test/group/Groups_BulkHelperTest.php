<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_BulkHelper.php');

class Groups_BulkHelperTest extends BulkHelperTestCase {

    public function testSetPrivacy() {
        $pizzaLoversGroup = Group::create('Pizza Lovers');
        $pizzaLoversGroup->isPrivate = true;
        $pizzaLoversGroup->save();
        $pizzaLoversId = $pizzaLoversGroup->id;
        $saladLoversGroup = Group::create('Salad Lovers');
        $saladLoversGroup->isPrivate = false;
        $saladLoversGroup->save();
        $saladLoversId = $saladLoversGroup->id;
        Groups_BulkHelper::setPrivacy(30, true);
        $pizzaLoversGroup = XN_Content::load($pizzaLoversId);
        $saladLoversGroup = XN_Content::load($saladLoversId);
        $this->assertEqual($pizzaLoversGroup->isPrivate, true);
        $this->assertEqual($saladLoversGroup->isPrivate, false);
        Groups_BulkHelper::setPrivacy(30, false);
        $pizzaLoversGroup = XN_Content::load($pizzaLoversId);
        $saladLoversGroup = XN_Content::load($saladLoversId);
        $this->assertEqual($pizzaLoversGroup->isPrivate, true);
        $this->assertEqual($saladLoversGroup->isPrivate, false);
    }

    public function testRemoveByUser() {
        $pizzaLoversGroup = Group::create('Pizza Lovers');
        $pizzaLoversGroup->save();
        $saladLoversGroup = Group::create('Salad Lovers');
        $saladLoversGroup->save();
        Group::setStatus($pizzaLoversGroup, 'JonathanAquino', 'member');
        sleep(1);
        Group::setStatus($saladLoversGroup, 'JonathanAquino', 'member');
        Group::setStatus($pizzaLoversGroup, 'david', 'member');
        Group::setStatus($saladLoversGroup, 'david', 'member');
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'david'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'david'));
        $this->assertEqual('1,1', implode(',', Groups_BulkHelper::removeGroupMemberships(1, 'JonathanAquino')));
        TestGroupMembership::clearCache();
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'JonathanAquino'));
        $this->assertEqual('nonmember', Group::status($saladLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'david'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'david'));
    }

    public function testRemoveByUser2() {
        $pizzaLoversGroup = Group::create('Pizza Lovers');
        $pizzaLoversGroup->save();
        $saladLoversGroup = Group::create('Salad Lovers');
        $saladLoversGroup->save();
        Group::setStatus($pizzaLoversGroup, 'JonathanAquino', 'member');
        sleep(1);
        Group::setStatus($saladLoversGroup, 'JonathanAquino', 'member');
        Group::setStatus($pizzaLoversGroup, 'david', 'member');
        Group::setStatus($saladLoversGroup, 'david', 'member');
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'david'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'david'));
        $this->assertEqual('2,0', implode(',', Groups_BulkHelper::removeGroupMemberships(100, 'JonathanAquino')));
        TestGroupMembership::clearCache();
        $this->assertEqual('nonmember', Group::status($pizzaLoversGroup, 'JonathanAquino'));
        $this->assertEqual('nonmember', Group::status($saladLoversGroup, 'JonathanAquino'));
        $this->assertEqual('member', Group::status($pizzaLoversGroup, 'david'));
        $this->assertEqual('member', Group::status($saladLoversGroup, 'david'));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

class TestGroupMembership extends GroupMembership {
    public static function clearCache() { parent::clearCache(); }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
