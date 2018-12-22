<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_FileHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_BulkHelper.php');

class Forum_BulkHelper9Test extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testBAZ3239() {
        list($pizzaTopicId, $pizzaTopic) = XG_TestHelper::createTopic();
        list($pizzaComment1Id, $pizzaComment1) = XG_TestHelper::createComment($pizzaTopic);
        // Simulate BAZ-3239 [Jon Aquino 2007-06-08]
        XN_Content::delete($pizzaTopic->id);
        $pizzaComment1->my->attachedToAuthor = 'x';
        $pizzaComment1->save();
        $this->assertEqual('2,0', implode(',', Forum_BulkHelper::removeByUser(20, XN_Profile::current()->screenName)));
        $this->assertTrue(true, 'Shouldn\'t get exception');
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
