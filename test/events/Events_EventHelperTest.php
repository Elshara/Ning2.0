<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');

class Events_EventHelperTest extends UnitTestCase {

    public function testCreate1() {
        $oldAppPrivacy = W_Cache::getWidget('main')->config['appPrivacy'];
        W_Cache::getWidget('main')->config['appPrivacy'] = 'private';
        $this->assertTrue(Events_EventHelper::create('Event')->isPrivate);
        W_Cache::getWidget('main')->config['appPrivacy'] = $oldAppPrivacy;
    }

    public function testCreate2() {
        $oldAppPrivacy = W_Cache::getWidget('main')->config['appPrivacy'];
        W_Cache::getWidget('main')->config['appPrivacy'] = 'public';
        $this->assertFalse(Events_EventHelper::create('Event')->isPrivate);
        W_Cache::getWidget('main')->config['appPrivacy'] = $oldAppPrivacy;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
