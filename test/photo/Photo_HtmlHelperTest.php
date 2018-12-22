<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_HtmlHelper.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_FullNameHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_HttpHelper.php');

class Photo_HtmlHelperTest extends UnitTestCase {

    public function testHyperlinkUrls() {
        $this->assertEqual('I use Google', Photo_HtmlHelper::hyperlinkUrls('I use Google'));
        $this->assertEqual('I use <a href="http://google.com">http://google.com</a>', Photo_HtmlHelper::hyperlinkUrls('I use http://google.com'));
        $this->assertEqual('I use <a href="http://www.google.com">www.google.com</a>', Photo_HtmlHelper::hyperlinkUrls('I use www.google.com'));
        $this->assertEqual('<EMBED src=http://www.youtube.com/v/g6XLAX0Sovk width=205 height=168 type=application/x-shockwave-flash allowscriptaccess="never" wmode="transparent"></EMBED>foo', Photo_HtmlHelper::hyperlinkUrls('<EMBED src=http://www.youtube.com/v/g6XLAX0Sovk width=205 height=168 type=application/x-shockwave-flash allowscriptaccess="never" wmode="transparent"></EMBED>foo'));
    }

    public function testCleanText() {
        $x = '<object height="345" width="420"><param name="movie" value="http://www.youtube.com/v/g6XLAX0Sovk"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed><param name="allowscriptaccess" value="never"></param></object>';
        $this->assertEqual($x, Photo_HtmlHelper::cleanText($x));
    }

    public function testPrettyDate() {
        $this->assertEqual('just now', Photo_HtmlHelper::prettyDate(date('c', time() + 5)));
        $this->assertEqual('1 second ago', Photo_HtmlHelper::prettyDate(date('c', time() - 1)));
        $this->assertEqual('55 seconds ago', Photo_HtmlHelper::prettyDate(date('c', time() - 55)));
        $this->assertEqual('1 minute ago', Photo_HtmlHelper::prettyDate('1 minute ago'));
        $this->assertEqual('2 minutes ago', Photo_HtmlHelper::prettyDate('2 minutes ago'));
        $this->assertEqual('1 hour ago', Photo_HtmlHelper::prettyDate('1 hour ago'));
        $this->assertEqual('2 hours ago', Photo_HtmlHelper::prettyDate('2 hours ago'));
        $this->assertEqual('1 day ago', Photo_HtmlHelper::prettyDate('1 day ago'));
        $this->assertEqual('Feb. 15, 1977', Photo_HtmlHelper::prettyDate('February 15, 1977'));
    }

    public function testAverageRating() {
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Photo_HtmlHelper::averageRating(3, 2, true));
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Photo_HtmlHelper::averageRating(3, 1, true));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Photo_HtmlHelper::averageRating(3, 2, false));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Photo_HtmlHelper::averageRating(3, 1, false));
    }

    public function testAverageRatingWithUserSummary() {
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Photo_HtmlHelper::averageRatingWithUserSummary(3, 2, 0, true));
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Photo_HtmlHelper::averageRatingWithUserSummary(3, 1, 0, true));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Photo_HtmlHelper::averageRatingWithUserSummary(3, 2, 0, false));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Photo_HtmlHelper::averageRatingWithUserSummary(3, 1, 0, false));
    }

    public function testToSortOptions() {
        $sorts = array (
            'mostRecent' => array (
                'name' => 'Latest',
                'attribute' => 'createdDate',
                'alias' => 'mostRecent',
                'direction' => 'desc',
                'type' => 'date',
                'mainPageTitleKey' => 'LATEST_PHOTOS_N'),
            'highestRated' => array (
                'name' => 'Top Rated',
                'attribute' => 'my->ratingAverage',
                'alias' => 'highestRated',
                'direction' => 'desc',
                'type' => 'number',
                'mainPageTitleKey' => 'TOP_RATED_PHOTOS_N'),
            'mostPopular' => array (
                'name' => 'Most Viewed',
                'attribute' => 'my->viewCount',
                'alias' => 'mostPopular',
                'direction' => 'desc',
                'type' => 'number',
                'mainPageTitleKey' => 'MOST_VIEWED_PHOTOS_N'),
            'random' => array (
                'name' => 'Random',
                'attribute' => 'createdDate',
                'alias' => 'random',
                'direction' => 'desc',
                'type' => 'date',
                'mainPageTitleKey' => 'RANDOM_PHOTOS'),
        );
        $expectedSortOptions = array(
            array(
                'displayText' => 'Latest',
                'url' => 'http://example.org/?sort=mostRecent',
                'selected' => true),
            array(
                'displayText' => 'Top Rated',
                'url' => 'http://example.org/?sort=highestRated',
                'selected' => false),
            array(
                'displayText' => 'Most Viewed',
                'url' => 'http://example.org/?sort=mostPopular',
                'selected' => false),
            array(
                'displayText' => 'Random',
                'url' => 'http://example.org/?sort=random',
                'selected' => false));
        $this->assertEqual($expectedSortOptions, Photo_HtmlHelper::toSortOptions($sorts, null, 'http://example.org'));
        $expectedSortOptions = array(
            array(
                'displayText' => 'Latest',
                'url' => 'http://example.org/?sort=mostRecent',
                'selected' => false),
            array(
                'displayText' => 'Top Rated',
                'url' => 'http://example.org/?sort=highestRated',
                'selected' => false),
            array(
                'displayText' => 'Most Viewed',
                'url' => 'http://example.org/?sort=mostPopular',
                'selected' => true),
            array(
                'displayText' => 'Random',
                'url' => 'http://example.org/?sort=random',
                'selected' => false));
        $this->assertEqual($expectedSortOptions, Photo_HtmlHelper::toSortOptions($sorts, 'mostPopular', 'http://example.org'));
    }

    public function testSquareCropIfLarge() {
        $url = 'http://foo.com';
        $width = 10;
        $height = 20;
        TestHtmlHelper::squareCropIfLarge($url, $width, $height, 100);
        $this->assertEqual('http://foo.com, 10, 20', $url . ', ' . $width . ', ' . $height);

        $url = 'http://foo.com';
        $width = 10;
        $height = 999;
        TestHtmlHelper::squareCropIfLarge($url, $width, $height, 100);
        $this->assertEqual('http://foo.com, 10, 999', $url . ', ' . $width . ', ' . $height);

        $url = 'http://foo.com';
        $width = 999;
        $height = 20;
        TestHtmlHelper::squareCropIfLarge($url, $width, $height, 100);
        $this->assertEqual('http://foo.com, 999, 20', $url . ', ' . $width . ', ' . $height);

        $url = 'http://foo.com';
        $width = 10;
        $height = 20;
        TestHtmlHelper::squareCropIfLarge($url, $width, $height, 5);
        $this->assertEqual('http://foo.com/?crop=1%3A1&width=5&height=5, 5, 5', $url . ', ' . $width . ', ' . $height);

        $url = 'http://foo.com';
        $width = 20;
        $height = 10;
        TestHtmlHelper::squareCropIfLarge($url, $width, $height, 5);
        $this->assertEqual('http://foo.com/?crop=1%3A1&width=5&height=5, 5, 5', $url . ', ' . $width . ', ' . $height);
    }

}

class TestHtmlHelper extends Photo_HtmlHelper {
    public function squareCropIfLarge(&$url, &$width, &$height, $cropExtent) {
        parent::squareCropIfLarge($url, $width, $height, $cropExtent);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


