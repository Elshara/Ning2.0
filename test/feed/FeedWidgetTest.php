<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/feed/lib/SimplePie/simplepie.inc');

class FeedWidgetTest extends UnitTestCase {

    public function testSimplePieFix() {
        // BAZ-2226 [Jon Aquino 2007-03-31]
        $this->assertTrue(strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/feed/lib/SimplePie/simplepie.inc'), '@file_get_contents') !== false);
        // BAZ-2467 [Jon Aquino 2007-04-10]
        $this->assertTrue(strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/feed/lib/SimplePie/simplepie.inc'), 'CURLOPT_CONNECTTIMEOUT') !== false);
        // BAZ-2494 [Phil McCluskey 2007-04-19]
        $this->assertTrue(strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/feed/lib/SimplePie/simplepie.inc'), 'if(mb_strlen($data) > 280000) {') !== false);
        // BAZ-5213 [Phil McCluskey 2008-01-03]
        $this->assertTrue(strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/feed/lib/SimplePie/simplepie.inc'), 'var $strip_htmltags = array(\'base\', \'blink\', \'body\', \'doctype\', \'font\', \'form\'') !== false);
    }

    public function testTestSimplePieFix() {
        // Check that testSimplePieFix() above is up to date [Jon Aquino 2008-01-16]
        $currentSimplePieVersion = 'Razzleberry';
        $simplePieVersionChanged = strpos(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/widgets/feed/lib/SimplePie/simplepie.inc'), '@version "' . $currentSimplePieVersion . '"') === false;
        $this->assertFalse($simplePieVersionChanged, 'Reminder: Before upgrading SimplePie, do a diff to check that all modifications are captured in testSimplePieFix()');
    }

    public function testFeeds() {
        $this->doTestFeed('http://jonaquino.blogspot.com');
        $this->doTestFeed('http://feeds.feedburner.com/LondonbikerscomLatestNews');
        $this->doTestFeed('http://newsrss.bbc.co.uk/rss/sportonline_uk_edition/football/rss.xml');
        $this->doTestFeed('http://www.guardian.co.uk/rssfeed/0,,5,00.xml');
        $this->doTestFeed('http://newsrss.bbc.co.uk/rss/sportonline_uk_edition/football/teams/a/arsenal/rss.xml');
        $this->doTestFeed('http://rss.news.yahoo.com/rss/mostviewed');
    }

    private function doTestFeed($feed_url) {
        $feed = new SimplePie();
        $feed->enable_cache(false);
        $feed->set_feed_url($feed_url);
        $feed->init();
        $this->assertNotNull($feed->data);
        $n = $feed->get_item_quantity();
        $this->assertTrue($n > 0, $feed_url . ' ' . $n . ' ' . $feed->error);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';