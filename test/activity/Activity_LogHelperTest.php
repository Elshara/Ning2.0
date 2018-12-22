<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/activity/lib/helpers/Activity_LogHelper.php');

Mock::generatePartial('Activity_LogHelper', 'LogHelperPartialMock', array('profileLink'));

class Activity_LogHelperTest extends UnitTestCase {

    public function doTestFriendshipMessageHtml($expectedText, $screenNames, $onMyProfilePage, $total) {
        $logHelper = new LogHelperPartialMock();
        $logHelper->setReturnValue('profileLink', 'A', array('a'));
        $logHelper->setReturnValue('profileLink', 'B', array('b'));
        $logHelper->setReturnValue('profileLink', 'C', array('c'));
        $logHelper->setReturnValue('profileLink', 'D', array('d'));
        $logHelper->setReturnValue('profileLink', 'E', array('e'));
        $logHelper->setReturnValue('profileLink', 'F', array('f'));
        $logHelper->setReturnValue('profileLink', 'G');
        $this->assertEqual($expectedText, $logHelper->friendshipMessageHtml($screenNames, $total, $onMyProfilePage, 'a'));
    }

    public function testFriendshipMessageHtml1() {
        $this->doTestFriendshipMessageHtml('You and B are now friends', array('a', 'b'), TRUE, NULL);
    }

    public function testFriendshipMessageHtml2() {
        $this->doTestFriendshipMessageHtml('You and B are now friends', array('b', 'a'), TRUE, 1);
    }

    public function testFriendshipMessageHtml3() {
        $this->doTestFriendshipMessageHtml('A and B are now friends', array('a', 'b'), FALSE, 1);
    }

    public function testFriendshipMessageHtml4() {
        $this->doTestFriendshipMessageHtml('B and A are now friends', array('b', 'a'), FALSE, 1);
    }

    public function testFriendshipMessageHtml5() {
        $this->doTestFriendshipMessageHtml('A is now friends with B and C', array('a', 'b', 'c'), TRUE, 2);
    }

    public function testFriendshipMessageHtml6() {
        $this->doTestFriendshipMessageHtml('You and B are now friends', array('b', 'a', 'c'), TRUE, 2);
    }

    public function testFriendshipMessageHtml7() {
        $this->doTestFriendshipMessageHtml('A is now friends with B and C', array('a', 'b', 'c'), FALSE, 2);
    }

    public function testFriendshipMessageHtml8() {
        $this->doTestFriendshipMessageHtml('A is now friends with B, C, and D', array('a', 'b', 'c', 'd'), FALSE, 3);
    }

    public function testFriendshipMessageHtml9() {
        $this->doTestFriendshipMessageHtml('A is now friends with B, C, D, and E', array('a', 'b', 'c', 'd', 'e'), FALSE, 4);
    }

    public function testFriendshipMessageHtml10() {
        $this->doTestFriendshipMessageHtml('A is now friends with B, C, D, E, and F', array('a', 'b', 'c', 'd', 'e', 'f'), FALSE, 5);
    }

    public function testFriendshipMessageHtml11() {
        $this->doTestFriendshipMessageHtml('A is now friends with B, C, D, E, F, and 1 other', array('a', 'b', 'c', 'd', 'e', 'f'), FALSE, 6);
    }

    public function testFriendshipMessageHtml12() {
        $this->doTestFriendshipMessageHtml('A is now friends with B, C, D, E, F, and 15 others', array('a', 'b', 'c', 'd', 'e', 'f'), FALSE, 20);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

