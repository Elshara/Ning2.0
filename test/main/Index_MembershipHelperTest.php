<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MembershipHelper.php');

class Index_MembershipHelperTest extends UnitTestCase {

    public function testAddMemberStatus() {
        // Create 100 normal users who should not be affected.
        for ($i = 0; $i < 100; $i++) {
            $x = XN_Content::create('User', 'testuser' . $i);
            $x->save();
        }
        // Create fifty-two administrators who should be affected.
        for ($i = 0; $i < 52; $i++) {
            $x = XN_Content::create('User', 'testadmin' . $i);
            $x->my->isAdmin = 'Y';
            $x->save();
        }
        // Apply addMemberStatus and check that two remain to convert
        $this->assertEqual(2, Index_MembershipHelper::addMemberStatus());
        $this->assertEqual(0, Index_MembershipHelper::addMemberStatus());
        //TODO add some Users into the test to check that these filters work as expected:
        /*
        $query->filter('my->xg_index_status', '<>', 'pending');
        $query->filter('my->xg_index_status', '<>', 'blocked');
        $query->filter('my->status', '<>', 'banned');
        */
    }
    
    public function tearDown() {
        // We create 152 test objects above, make sure we delete them all.
        XG_TestHelper::deleteTestObjects();
        XG_TestHelper::deleteTestObjects();
        XG_TestHelper::deleteTestObjects();
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
