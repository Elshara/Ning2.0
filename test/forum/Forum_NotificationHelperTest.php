<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_NotificationHelper.php');

class Forum_NotificationHelperTest extends UnitTestCase {

    public function testNewTopicProfileSetId() {
        $this->assertEqual('xg_new_topic_follow', Forum_NotificationHelper::newTopicProfileSetId());
        $_GET['groupId'] = '12345:Group:6789';
        $this->assertEqual('xg_new_topic_follow_12345_Group_6789', Forum_NotificationHelper::newTopicProfileSetId());
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
