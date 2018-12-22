<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_NotificationHelper.php');

class Index_NotificationHelperTest extends UnitTestCase {

    public function testGroupLabel() {
        $this->assertEqual('xg_group_12345_Group_6789', Index_NotificationHelper::groupLabel('12345_Group_6789'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
