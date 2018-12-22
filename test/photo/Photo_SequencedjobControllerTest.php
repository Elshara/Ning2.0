<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/photo/controllers/SequencedjobController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/photo/lib/helpers/Photo_ContentHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/widgets/photo/lib/helpers/Photo_PhotoHelper.php';
Mock::generate('XN_Query');
Mock::generate('Photo_ContentHelper');
Mock::generate('Photo_PhotoHelper');
Mock::generate('stdClass', 'MockXN_Content', array('save'));

class Photo_SequencedjobControllerTest extends UnitTestCase {

    private $controller, $contentHelper, $photoHelper;

    public function setUp() {
        $this->controller = new TestSequencedjobController(W_Cache::getWidget('main'));
        $this->contentHelper = new MockPhoto_ContentHelper();
        $this->photoHelper = new MockPhoto_PhotoHelper();
    }

    private function createQuery($begin, $end, $returnValue) {
        $query = new MockXN_Query();
        $query->expectAt(0, 'filter', array('owner'));
        $query->expectAt(1, 'filter', array('type', '=', 'Album'));
        $query->expectAt(2, 'filter', array('my.coverPhotoId', '=', null));
        $query->expectOnce('begin', array($begin));
        $query->expectOnce('end', array($end));
        $query->expectOnce('execute', array());
        $query->setReturnValue('execute', $returnValue);
        return $query;
    }

    public function testAddAlbumCovers1() {
        $query = $this->createQuery(0, 20, array());
        $this->controller->addAlbumCovers($query, $this->contentHelper, $this->photoHelper);
        $this->assertIdentical(false, $this->controller->getContinueJob());
        $this->assertEqual(20, $this->controller->start);
    }

    public function testAddAlbumCovers2() {
        $this->controller->start = 20;
        $query = $this->createQuery(20, 40, array());
        $this->controller->addAlbumCovers($query, $this->contentHelper, $this->photoHelper);
        $this->assertIdentical(false, $this->controller->getContinueJob());
        $this->assertEqual(40, $this->controller->start);
    }

    public function testAddAlbumCovers3() {
        $photo = new MockXN_Content();
        $photo->id = '1:Photo:2';
        $this->photoHelper->expectOnce('getSpecificPhotosProper');
        $this->photoHelper->setReturnValue('getSpecificPhotosProper', array('photos' => array($photo)));
        $album = new MockXN_Content();
        $album->expectOnce('save');
        $query = $this->createQuery(0, 20, array($album));
        $this->controller->addAlbumCovers($query, $this->contentHelper, $this->photoHelper);
        $this->assertIdentical(true, $this->controller->getContinueJob());
        $this->assertEqual('1:Photo:2', $album->my->coverPhotoId);
        $this->assertEqual(20, $this->controller->start);
    }

    public function testAddAlbumCovers4() {
        $this->photoHelper->expectOnce('getSpecificPhotosProper');
        $this->photoHelper->setReturnValue('getSpecificPhotosProper', array('photos' => array()));
        $album = new MockXN_Content();
        $album->expectNever('save');
        $this->controller->start = 20;
        $query = $this->createQuery(20, 40, array($album));
        $this->controller->addAlbumCovers($query, $this->contentHelper, $this->photoHelper);
        $this->assertIdentical(true, $this->controller->getContinueJob());
        $this->assertNull($album->my->coverPhotoId);
        $this->assertEqual(40, $this->controller->start);
    }

}

class TestSequencedjobController extends Photo_SequencedjobController {
    public function getContinueJob() { return $this->continueJob; }
    public function addAlbumCovers($query, $contentHelper, $photoHelper) {
        parent::addAlbumCovers($query, $contentHelper, $photoHelper);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
