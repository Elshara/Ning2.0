<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_SlideshowHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_AlbumHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_ContentHelper.php');

class Photo_SlideshowHelperTest extends UnitTestCase {

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

    public function testFeedUrl() {
        XG_TestHelper::setCurrentWidget('photo');
        $this->assertEqual('/photo/photo/slideshowFeed?', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('all', 'Joe')));
        $this->assertEqual('/photo/photo/slideshowFeed?promoted=true', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('promoted', 'Joe')));
        $this->assertEqual('/photo/photo/slideshowFeedForContributor?screenName=Joe', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('for_contributor', 'Joe')));
        $this->assertEqual('/photo/photo/slideshowFeed?popular=true', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('popular', 'Joe')));
        $this->assertEqual('/photo/photo/slideshowFeed?owner=true', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('owner', 'Joe')));
        $album = XN_Content::create('Album');
        $album->my->photoCount = 5;
        $album->save();
        $photoSet = 'album_' . $album->id;
        $this->assertEqual('/photo/photo/slideshowFeedAlbum?id=' . urlencode($album->id), preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl($photoSet, 'Joe')));
        XN_Content::delete($album);
        TestContentHelper::setFindByIDResults(array());
        $this->assertEqual('/photo/photo/slideshowFeed?promoted=true', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl($photoSet, 'Joe')));
    }

    public function testFeedUrlRandom() {
        XG_TestHelper::setCurrentWidget('photo');
        $this->assertEqual('/photo/photo/slideshowFeed?random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('all', 'Joe', false, true)));
        $this->assertEqual('/photo/photo/slideshowFeed?promoted=true&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('promoted', 'Joe', false, true)));
        $this->assertEqual('/photo/photo/slideshowFeedForContributor?screenName=Joe&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('for_contributor', 'Joe', false, true)));
        $this->assertEqual('/photo/photo/slideshowFeed?popular=true&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('popular', 'Joe', false, true)));
        $this->assertEqual('/photo/photo/slideshowFeed?owner=true&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl('owner', 'Joe', false, true)));
        $album = XN_Content::create('Album');
        $album->my->photoCount = 5;
        $album->save();
        $photoSet = 'album_' . $album->id;
        $this->assertEqual('/photo/photo/slideshowFeedAlbum?id=' . urlencode($album->id) . '&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl($photoSet, 'Joe', false, true)));
        XN_Content::delete($album);
        TestContentHelper::setFindByIDResults(array());
        $this->assertEqual('/photo/photo/slideshowFeed?promoted=true&random=1', preg_replace('@.*(/photo/photo)@ui', '\1', Photo_SlideshowHelper::feedUrl($photoSet, 'Joe', false, true)));
    }

}


class TestContentHelper extends Photo_ContentHelper {
    public static function setFindByIDResults($findByIDResults) {
        parent::$findByIDResults = $findByIDResults;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


