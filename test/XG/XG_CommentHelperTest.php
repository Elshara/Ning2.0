<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
Mock::generate('stdClass', 'MockXG_CommentHelper', array('createQuery'));
Mock::generate('XN_Query');

class XG_CommentHelperTest extends BazelTestCase {

    public function testcurrentUserCanSeeAddCommentSectionProper() {
        $album->contributorName = 'Jonah';
        $this->assertTrue(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'me',
                'currentUserScreenName' => 'Jonah',
                'currentUserIsFriend' => false)));
        $this->assertFalse(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'me',
                'currentUserScreenName' => 'Samuel',
                'currentUserIsFriend' => true)));
        $this->assertTrue(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'friends',
                'currentUserScreenName' => 'Samuel',
                'currentUserIsFriend' => true)));
        $this->assertFalse(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'friends',
                'currentUserScreenName' => 'Samuel',
                'currentUserIsFriend' => false)));
        $this->assertTrue(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'all',
                'currentUserScreenName' => 'Samuel',
                'currentUserIsFriend' => false)));
        $this->assertTrue(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'all',
                'currentUserScreenName' => '',
                'currentUserIsFriend' => false)));
        $this->assertFalse(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'friends',
                'currentUserScreenName' => '',
                'currentUserIsFriend' => false)));
        $this->assertFalse(TestCommentHelper::currentUserCanSeeAddCommentSectionProper(array(
                'attachedTo' => $album,
                'addCommentPermission' => 'me',
                'currentUserScreenName' => '',
                'currentUserIsFriend' => false)));
    }

    public function testFeedAvailable() {
        $album->my->visibility = null;
        $this->assertFalse(TestCommentHelper::feedAvailableProper($album, true, true));
        $this->assertFalse(TestCommentHelper::feedAvailableProper($album, true, false));
        $this->assertFalse(TestCommentHelper::feedAvailableProper($album, false, true));
        $this->assertTrue(TestCommentHelper::feedAvailableProper($album, false, false));
        $album->my->visibility = 'all';
        $this->assertTrue(TestCommentHelper::feedAvailableProper($album, false, false));
        $album->my->visibility = 'friends';
        $this->assertFalse(TestCommentHelper::feedAvailableProper($album, false, false));
        $album->my->visibility = 'me';
        $this->assertFalse(TestCommentHelper::feedAvailableProper($album, false, false));
    }

    public function testUrl() {
        $comment->my->mozzle = 'photo';
        $comment->my->attachedTo = '123:Photo:456';
        $comment->my->attachedToType = 'Photo';
        $this->assertTrue(strpos(XG_CommentHelper::url($comment), '/photo/photo/show?id=123%3APhoto%3A456') !== false, $this->escape(XG_CommentHelper::url($comment)));
    }

    public function testCreateQuery() {
        $query = TestCommentHelper::createQuery('1:BlogPost:2', 23, 88);
        $this->assertEqual("XN_Query:
  subject [Content]
  returnIds []
  begin [23]
  end [88]
  alwaysReturnTotalCount [1]
  orders [
    published : desc : date
  ]
  filters [
  type : = : 'Comment' : string
  AND
  owner.relativeUrl : = : '" . XN_Application::load()->relativeUrl . "' : string
  AND
  my.attachedTo : = : '1:BlogPost:2' : string
  ]
XG_Query:
  maxAge []
  keys [xg-2jZzyNu]
  cached? [not executed]", $query->debugString());
    }

    public function testPage() {
        $this->doTestPage(1, 0, array());
        $this->doTestPage(1, 19, array());
        $this->doTestPage(2, 20, array());
        $result->id = '5:Comment:6';
        $this->doTestPage(2, 20, array($result));
        $result->id = '3:Comment:4';
        $this->doTestPage(1, 20, array($result));
    }

    private function doTestPage($expectedPage, $totalCount, $queryResults) {
        $query = new MockXN_Query();
        $comment->id = '3:Comment:4';
        $comment->my->attachedTo = '1:BlogPost:2';
        $comment->createdDate = '2008-05-11T15:30:00Z';
        $commentHelper = new MockXG_CommentHelper();
        $commentHelper->expectOnce('createQuery', array('1:BlogPost:2', 0, 1));
        $commentHelper->setReturnValue('createQuery', $query);
        $query->expectOnce('filter', array('createdDate', '<=', '2008-05-11T15:30:00Z', 'date'));
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', $queryResults);
        $query->setReturnValue('getTotalCount', $totalCount);
        $this->assertEqual($expectedPage, TestCommentHelper::page($comment, 20, $commentHelper));
    }

}

class TestCommentHelper extends XG_CommentHelper {
    public static function currentUserCanSeeAddCommentSectionProper($args) {
        return parent::currentUserCanSeeAddCommentSectionProper($args);
    }
    public static function feedAvailableProper($attachedTo, $appIsPrivate, $groupIsPrivate) {
        return parent::feedAvailableProper($attachedTo, $appIsPrivate, $groupIsPrivate);
    }
    public static function createQuery($blogPostId, $begin, $end) {
        return parent::createQuery($blogPostId, $begin, $end);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


