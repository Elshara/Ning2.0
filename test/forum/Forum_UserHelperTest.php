<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_UserHelper.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');

class Forum_UserHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testUpdateActivityCount() {
        $user = XN_Content::create('TestUser', XN_Profile::current()->screenName);
        $this->assertNull(User::getWidgetAttribute($user, 'activityCount'));
        $this->assertNull(Forum_UserHelper::updateActivityCount($user, TRUE));
        $this->assertIdentical(0, User::getWidgetAttribute($user, 'activityCount'));
        $topic = Topic::create('test', 'test');
        $topic->save();
        Forum_CommentHelper::createComment($topic, 'test', NULL)->save();
        Forum_CommentHelper::createComment($topic, 'test', NULL)->save();
        Forum_UserHelper::updateActivityCount($user, TRUE);
        $this->assertIdentical(3, User::getWidgetAttribute($user, 'activityCount'));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
