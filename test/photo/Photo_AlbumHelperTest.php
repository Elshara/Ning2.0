<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_AlbumHelper.php');
Mock::generate('TestRest');

class Photo_AlbumHelperTest extends UnitTestCase {

    private $mockRest;

    public function setUp() {
        $this->mockRest = new ExceptionMockDecorator(new MockTestRest());
        TestRest::setInstance($this->mockRest);
    }

    public function tearDown() {
        TestRest::setInstance(null);
    }

    public function testGetSortedAlbums() {
        $this->mockRest->expectOnce('doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/atom/1.0/content(type%20%3D%20%27Album%27%26my.hidden%20%21%3D%20%27Y%27)?from=0&to=4&order=published@D', null, null, null));
        $this->mockRest->setReturnValue('doRequest',
'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Content feed for devbazjon14b</title>
    <id>http%3A%2F%2Fdevbazjon14b.xna.ningops.net%2Fxn%2Fatom%2F1.0%2Fcontent%28id%3D668013%3APhoto%3A13825%29</id>
    <xn:size>0</xn:size>
    <updated>2007-11-07T20:19:51.229Z</updated>
</feed>');
        $result = Photo_AlbumHelper::getSortedAlbums(array(), null, 0, 4);
        $this->assertEqual(array(), $result['albums']);
        $this->assertEqual(0, $result['numAlbums']);
    }

    public function testGetSortedAlbums3() {
        $this->mockRest->expectOnce('doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/atom/1.0/content(type%20%3D%20%27Album%27)?from=0&to=4&order=published@D', null, null, null));
        $this->mockRest->setReturnValue('doRequest',
'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Content feed for devbazjon14b</title>
    <id>http%3A%2F%2Fdevbazjon14b.xna.ningops.net%2Fxn%2Fatom%2F1.0%2Fcontent%28id%3D668013%3APhoto%3A13825%29</id>
    <xn:size>0</xn:size>
    <updated>2007-11-07T20:19:51.229Z</updated>
</feed>');
        $result = Photo_AlbumHelper::getSortedAlbums(array('includeHidden' => 'true'), null, 0, 4);
        $this->assertEqual(array(), $result['albums']);
        $this->assertEqual(0, $result['numAlbums']);
    }

    public function testGetSortedAlbums4() {
        $this->mockRest->expectOnce('doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/atom/1.0/content(type%20%3D%20%27Album%27%26my.hidden%20%21%3D%20%27Y%27%26fulltext%20likeic%20%27hello%27)?from=0&to=4&order=published@D', null, null, null));
        $this->mockRest->setReturnValue('doRequest',
'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Content feed for devbazjon14b</title>
    <id>http%3A%2F%2Fdevbazjon14b.xna.ningops.net%2Fxn%2Fatom%2F1.0%2Fcontent%28id%3D668013%3APhoto%3A13825%29</id>
    <xn:size>0</xn:size>
    <updated>2007-11-07T20:19:51.229Z</updated>
</feed>');
        $result = Photo_AlbumHelper::getSortedAlbums(array('searchTerms' => 'hello'), null, 0, 4);
        $this->assertEqual(array(), $result['albums']);
        $this->assertEqual(0, $result['numAlbums']);
    }

    public function testGetSortedAlbums5() {
        $this->mockRest->expect('doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/atom/1.0/content(type%20%3D%20%27Album%27%26my.hidden%20%21%3D%20%27Y%27)?from=0&to=5&order=random()', null, null, null));
        $this->mockRest->setReturnValue('doRequest',
'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Content feed for devbazjon14b</title>
    <id>http%3A%2F%2Fdevbazjon14b.xna.ningops.net%2Fxn%2Fatom%2F1.0%2Fcontent%28id%3D668013%3APhoto%3A13825%29</id>
    <xn:size>5</xn:size>
    <updated>2007-11-07T20:19:51.229Z</updated>
    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0" xmlns:my="http://devbazjon17.xna.ningops.net/xn/atom/1.0">
      <id>http://devbazjon17.xna.ningops.net/670396:Album:12925</id>
      <xn:type>Album</xn:type>
      <xn:id>670396:Album:12925</xn:id>
      <title type="text">acorn</title>
      <published>2008-02-19T20:41:38.535Z</published>
      <updated>2008-02-19T20:41:47.360Z</updated>
      <author>
        <name>NingDev</name>
      </author>
      <xn:private>false</xn:private>
      <xn:application>devbazjon17</xn:application>
      <link href="http://devbazjon17.xna.ningops.net/xn/detail/670396:Album:12925" rel="alternate" />
      <my:photoCount xn:type="number">1</my:photoCount>
      <my:mozzle xn:type="string">photo</my:mozzle>
      <my:excludeFromPublicSearch xn:type="string">N</my:excludeFromPublicSearch>
      <my:viewCount xn:type="number">1</my:viewCount>
      <my:endDate xn:type="date">2008-02-19T00:18:35.836Z</my:endDate>
      <my:photos xn:type="string">670396:Photo:12908 t1203453698</my:photos>
      <my:coverPhotoId xn:type="string">670396:Photo:12908</my:coverPhotoId>
    </entry>

    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0" xmlns:my="http://devbazjon17.xna.ningops.net/xn/atom/1.0">
      <id>http://devbazjon17.xna.ningops.net/670396:Album:12890</id>
      <xn:type>Album</xn:type>
      <xn:id>670396:Album:12890</xn:id>
      <title type="text">foo</title>
      <published>2008-02-16T22:34:00.473Z</published>
      <updated>2008-02-19T20:55:27.751Z</updated>
      <author>
        <name>NingDev</name>
      </author>
      <xn:private>false</xn:private>
      <xn:application>devbazjon17</xn:application>
      <link href="http://devbazjon17.xna.ningops.net/xn/detail/670396:Album:12890" rel="alternate" />
      <my:photoCount xn:type="number">1</my:photoCount>
      <my:mozzle xn:type="string">photo</my:mozzle>
      <my:excludeFromPublicSearch xn:type="string">N</my:excludeFromPublicSearch>
      <my:viewCount xn:type="number">3</my:viewCount>
      <my:endDate xn:type="date">2008-02-14T23:53:06.118Z</my:endDate>
      <my:photos xn:type="string">670396:Photo:12005 t1203201239</my:photos>
      <my:coverPhotoId xn:type="string">670396:Photo:12005</my:coverPhotoId>
    </entry>

    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0" xmlns:my="http://devbazjon17.xna.ningops.net/xn/atom/1.0">
      <id>http://devbazjon17.xna.ningops.net/670396:Album:11324</id>
      <xn:type>Album</xn:type>
      <xn:id>670396:Album:11324</xn:id>
      <title type="text">Untitled</title>
      <published>2008-02-14T01:54:26.264Z</published>
      <updated>2008-02-16T03:41:29.693Z</updated>
      <author>
        <name>NingDev</name>
      </author>
      <xn:private>false</xn:private>
      <xn:application>devbazjon17</xn:application>
      <link href="http://devbazjon17.xna.ningops.net/xn/detail/670396:Album:11324" rel="alternate" />
      <my:photoCount xn:type="number">1</my:photoCount>
      <my:mozzle xn:type="string">photo</my:mozzle>
      <my:excludeFromPublicSearch xn:type="string">N</my:excludeFromPublicSearch>
      <my:viewCount xn:type="number">1</my:viewCount>
      <my:endDate xn:type="date">2008-02-06T20:22:14.152Z</my:endDate>
      <my:photos xn:type="string">670396:Photo:6917 t1202954066</my:photos>
    </entry>

    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0" xmlns:my="http://devbazjon17.xna.ningops.net/xn/atom/1.0">
      <id>http://devbazjon17.xna.ningops.net/670396:Album:11323</id>
      <xn:type>Album</xn:type>
      <xn:id>670396:Album:11323</xn:id>
      <title type="text">album3</title>
      <published>2008-02-14T01:29:32.117Z</published>
      <updated>2008-02-16T03:41:29.723Z</updated>
      <author>
        <name>NingDev</name>
      </author>
      <xn:private>false</xn:private>
      <xn:application>devbazjon17</xn:application>
      <link href="http://devbazjon17.xna.ningops.net/xn/detail/670396:Album:11323" rel="alternate" />
      <my:photoCount xn:type="number">1</my:photoCount>
      <my:mozzle xn:type="string">photo</my:mozzle>
      <my:excludeFromPublicSearch xn:type="string">N</my:excludeFromPublicSearch>
      <my:viewCount xn:type="number">3</my:viewCount>
      <my:endDate xn:type="date">2008-02-06T20:22:14.152Z</my:endDate>
      <my:photos xn:type="string">670396:Photo:6917 t1202952572</my:photos>
    </entry>

    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0" xmlns:my="http://devbazjon17.xna.ningops.net/xn/atom/1.0">
      <id>http://devbazjon17.xna.ningops.net/670396:Album:11322</id>
      <xn:type>Album</xn:type>
      <xn:id>670396:Album:11322</xn:id>
      <title type="text">album2</title>
      <published>2008-02-14T01:28:36.145Z</published>
      <updated>2008-02-16T03:41:29.753Z</updated>
      <author>
        <name>NingDev</name>
      </author>
      <xn:private>false</xn:private>
      <xn:application>devbazjon17</xn:application>
      <link href="http://devbazjon17.xna.ningops.net/xn/detail/670396:Album:11322" rel="alternate" />
      <my:photoCount xn:type="number">2</my:photoCount>
      <my:mozzle xn:type="string">photo</my:mozzle>
      <my:excludeFromPublicSearch xn:type="string">N</my:excludeFromPublicSearch>
      <my:viewCount xn:type="number">2</my:viewCount>
      <my:endDate xn:type="date">2008-02-06T20:22:14.152Z</my:endDate>
      <my:photos xn:type="string">670396:Photo:6915 t1202952544, 670396:Photo:6917 t1202952544</my:photos>
    </entry>

</feed>');
        $result = Photo_AlbumHelper::getSortedAlbums(array(), Photo_AlbumHelper::getRandomSortingOrder(), 0, 5);
        $this->assertEqual(5, $result['numAlbums']);
        $ids = XG_TestHelper::ids($result['albums']);
        $result = Photo_AlbumHelper::getSortedAlbums(array(), Photo_AlbumHelper::getRandomSortingOrder(), 0, 5);
        $this->assertEqual(5, $result['numAlbums']);
        $this->assertEqual($ids, XG_TestHelper::ids($result['albums']));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


