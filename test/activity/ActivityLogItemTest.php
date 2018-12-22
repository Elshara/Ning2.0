<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class ActivityLogItemTest extends UnitTestCase {

    public function testPseudoDeleted() {
        XG_TestHelper::setCurrentWidget('photo');
        $x = XN_Content::create('Foo');
        $this->assertFalse(TestActivityLogItem::pseudoDeleted($x));
        $x = XN_Content::create('Foo');
        $x->my->deleted = 'Y';
        $this->assertTrue(TestActivityLogItem::pseudoDeleted($x));
        $x = XN_Content::create('Foo');
        $x->my->deleted = 'N';
        $this->assertFalse(TestActivityLogItem::pseudoDeleted($x));
        $x = XN_Content::create('Foo');
        $x->my->xg_forum_deleted = 'Y';
        $this->assertFalse(TestActivityLogItem::pseudoDeleted($x));
        XG_TestHelper::setCurrentWidget('forum');
        $this->assertTrue(TestActivityLogItem::pseudoDeleted($x));
        $x = XN_Content::create('Foo');
        $x->my->xg_hello_deleted = 'N';
        $this->assertFalse(TestActivityLogItem::pseudoDeleted($x));
    }

}

class TestActivityLogItem extends ActivityLogItem {
    public static function pseudoDeleted($content) {
        return parent::pseudoDeleted($content);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
