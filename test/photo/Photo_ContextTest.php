<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_Context.php');
Mock::generate('stdClass', 'MockXN_AttributeContainer', array('raw'));
Mock::generate('XN_Query');

class Photo_ContextTest extends BazelTestCase {

    public function testFilterQueryByAlbumContext1() {
        $photo->id = 3;
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('id', 'in', array(1, 2)));
        Photo_Context::get('album')->filterQueryByContext($query, '>', $photo, 0, 2, array(1, 2, 3, 4, 5));
    }

    public function testFilterQueryByAlbumContext2() {
        $photo->id = 3;
        $query = new MockXN_Query();
        $query->expectOnce('filter', array('id', 'in', array(4, 5)));
        Photo_Context::get('album')->filterQueryByContext($query, '<', $photo, 0, 2, array(1, 2, 3, 4, 5));
    }

    public function testFilterQueryByUserContext() {
        $photo->id = 3;
        $photo->contributorName = 'george';
        $photo->createdDate = '2008-05-11T15:30:00Z';
        $query = new MockXN_Query();
        $query->expectAt(0, 'filter', array('contributorName', 'eic', 'george'));
        $query->expectAt(1, 'filter', array('createdDate', '>', '2008-05-11T15:30:00Z', 'date'));
        $query->expectOnce('order', array('createdDate', 'asc', 'date'));
        $query->expectOnce('begin', array(0));
        $query->expectOnce('end', array(2));
        Photo_Context::get('user')->filterQueryByContext($query, '>', $photo, 0, 2, null);
    }

    public function testFilterQueryByLocationContext() {
        $photo->id = 3;
        $photo->my->location = 'Hawaii';
        $photo->createdDate = '2008-05-11T15:30:00Z';
        $query = new MockXN_Query();
        $query->expectAt(0, 'filter', array('my->location', 'eic', 'Hawaii'));
        $query->expectAt(1, 'filter', array('createdDate', '<', '2008-05-11T15:30:00Z', 'date'));
        $query->expectOnce('order', array('createdDate', 'desc', 'date'));
        $query->expectOnce('begin', array(0));
        $query->expectOnce('end', array(2));
        Photo_Context::get('location')->filterQueryByContext($query, '<', $photo, 0, 2, null);
    }

    public function testFilterQueryByFeaturedContext() {
        $photo->id = 3;
        $photo->my = new MockXN_AttributeContainer();
        $photo->my->setReturnValue('raw', '2008-05-11T15:30:00Z');
        $query = new MockXN_Query();
        $query->expectAt(0, 'filter', array('my->xg_main_promotedOn', '<>', null, 'date'));
        $query->expectAt(1, 'filter', array('my->xg_main_promotedOn', '<', '2008-05-11T15:30:00Z', 'date'));
        $query->expectOnce('order', array('my->xg_main_promotedOn', 'desc', 'date'));
        $query->expectOnce('begin', array(0));
        $query->expectOnce('end', array(2));
        Photo_Context::get('featured')->filterQueryByContext($query, '<', $photo, 0, 2, null);
    }

    public function testGetListPageUrl() {
        $_GET['albumId'] = '123:Album:456';
        $photo->contributorName = 'joe';
        $photo->my->location = 'Hawaii';
        $url = Photo_Context::get('album')->getListPageUrl($photo);
        $this->assertTrue(strpos($url, '/photo/album/show?id=123%3AAlbum%3A456') !== false, $this->escape($url));
        $url = Photo_Context::get('user')->getListPageUrl($photo);
        $this->assertTrue(strpos($url, '/photo/photo/listForContributor?screenName=joe') !== false,$this->escape($url));
        $url = Photo_Context::get('location')->getListPageUrl($photo);
        $this->assertTrue(strpos($url, '/photo/photo/listForLocation?location=Hawaii') !== false, $this->escape($url));
        $url = Photo_Context::get('featured')->getListPageUrl($photo);
        $this->assertTrue(strpos($url, '/photo/photo/listFeatured') !== false, $url);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
