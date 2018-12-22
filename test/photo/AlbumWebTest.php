<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests the album templates.
 */
class AlbumWebTest extends WebTestCase {

    public function testNavigation() {
        $this->get('http://' . $_SERVER['HTTP_HOST'] . '/photo/album/list');
        $this->assertPattern('@<li.*Add an Album@');
    }

    public function testNavigation2() {
        $this->get('http://' . $_SERVER['HTTP_HOST'] . '/photo/photo/list');
        $this->assertNoPattern('@<li.*Create an Album@');
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


