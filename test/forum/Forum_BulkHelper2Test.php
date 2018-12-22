<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_UserHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_BulkHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_BulkHelper2Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testRemove() {
        list($pizzaTopicId, $pizzaTopic) = XG_TestHelper::createTopic();
        list($pizzaAttachment1Id, $pizzaAttachment1) = XG_TestHelper::createAttachment($pizzaTopic);
        list($pizzaAttachment2Id, $pizzaAttachment2) = XG_TestHelper::createAttachment($pizzaTopic);
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        list($pizzaAttachment3Id, $pizzaAttachment3) = XG_TestHelper::createAttachment($pizzaComment1);
        list($pizzaAttachment4Id, $pizzaAttachment4) = XG_TestHelper::createAttachment($pizzaComment1);
        list($pizzaComment2Id, $pizzaComment2) = XG_TestHelper::createComment($pizzaTopic, $pizzaComment1);
        list($pizzaAttachment5Id, $pizzaAttachment5) = XG_TestHelper::createAttachment($pizzaComment2);
        list($pizzaAttachment6Id, $pizzaAttachment6) = XG_TestHelper::createAttachment($pizzaComment2);
        list($pizzaComment3Id, $pizzaComment3) = XG_TestHelper::createComment($pizzaTopic);
        list($pizzaAttachment7Id, $pizzaAttachment7) = XG_TestHelper::createAttachment($pizzaComment3);
        list($pizzaAttachment8Id, $pizzaAttachment8) = XG_TestHelper::createAttachment($pizzaComment3);
        $pizzaIds = array($pizzaTopicId, $pizzaAttachment1Id, $pizzaAttachment2Id, $pizzaComment1Id, $pizzaAttachment3Id, $pizzaAttachment4Id, $pizzaComment2Id, $pizzaAttachment5Id, $pizzaAttachment6Id, $pizzaComment3Id, $pizzaAttachment7Id, $pizzaAttachment8Id);
        $this->assertEqual(implode(',', $pizzaIds), implode(',', XG_TestHelper::existingIds($pizzaIds)));
        $this->assertEqual('2,1', implode(',', Forum_BulkHelper::remove($pizzaTopic, 2)));
        $this->assertEqual(count($pizzaIds) - 6, count(XG_TestHelper::existingIds($pizzaIds)));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
