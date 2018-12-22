<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_HtmlHelper.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_FullNameHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_HttpHelper.php');

class Video_HtmlHelperTest extends UnitTestCase {

    public function testAlternativeText() {
        $this->assertEqual('The Elements of Style', Video_HtmlHelper::alternativeText(XN_Content::create('TestType', 'The Elements of Style', 'Lorem <b>ipsum</b> dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.')));
        $this->assertEqual('The Elements of Style', Video_HtmlHelper::alternativeText(XN_Content::create('TestType', 'The Elements of Style', NULL)));
        $this->assertEqual('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas...', Video_HtmlHelper::alternativeText(XN_Content::create('TestType', NULL, 'Lorem <b>ipsum</b> dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.')));
        $this->assertEqual('', Video_HtmlHelper::alternativeText(XN_Content::create('TestType', NULL, NULL)));
    }

    public function testExcerpt() {
        $this->assertEqual('The quick', Video_HtmlHelper::excerpt('<b>The</b> quick', 10, NULL, $excerpted));
        $this->assertFalse($excerpted);
        $this->assertEqual('The qui...', Video_HtmlHelper::excerpt('<b>The</b> quick brown', 10, NULL, $excerpted));
        $this->assertTrue($excerpted);
        $this->assertEqual('The quick', Video_HtmlHelper::excerpt('<b>The</b> quick', 10, 'http://google.com', $excerpted));
        $this->assertFalse($excerpted);
        $this->assertEqual('The qui<a href="http://google.com">...</a>', Video_HtmlHelper::excerpt('<b>The</b> quick brown', 10, 'http://google.com', $excerpted));
        $this->assertTrue($excerpted);
    }

    public function testPagination() {
        $old_get = $_GET;
        $_GET = array();
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/pizza.php';
        $_GET['page'] = 2;
        $_GET['topping'] = 'pepperoni';
        $result = Video_HtmlHelper::pagination(25, 10);
        // TODO remove preg_replace once verified - $_GET = array() above should remove this
        $this->assertEqual('http://example.com/pizza.php?topping=pepperoni', preg_replace('/json=yes&dojo.preventCache=\d+&/u', '', $result['targetUrl']));
        $this->assertEqual('page', $result['pageParamName']);
        $this->assertEqual(2, $result['curPage']);
        $this->assertEqual(3, $result['numPages']);

        // cleanup
        $_GET = $old_get;
    }

    public function testHyperlinkUrls() {
        $this->assertEqual('I use Google', Video_HtmlHelper::hyperlinkUrls('I use Google'));
        $this->assertEqual('I use <a href="http://google.com">http://google.com</a>', Video_HtmlHelper::hyperlinkUrls('I use http://google.com'));
        $this->assertEqual('I use <a href="http://www.google.com">www.google.com</a>', Video_HtmlHelper::hyperlinkUrls('I use www.google.com'));
        $this->assertEqual('I use <a href="http://google.com">http://google.com</a>', Video_HtmlHelper::hyperlinkUrls('I use <a href="http://google.com">http://google.com</a>'));
        $this->assertEqual('I use <a href="http://www.google.com">www.google.com</a>', Video_HtmlHelper::hyperlinkUrls('I use <a href="http://www.google.com">www.google.com</a>'));
        $this->assertEqual('David Harris announcement is here: <a href="http://www.pmail.com/helpus.htm">http://www.pmail.com/helpus.htm</a>', Video_HtmlHelper::hyperlinkUrls('David Harris announcement is here: http://www.pmail.com/helpus.htm'));
    }

    public function testScrub() {
        $this->assertEqual('<div id="foo"></div>', Video_HtmlHelper::scrub('<div id="foo"></div>'));
        $this->assertEqual('A
B', Video_HtmlHelper::scrub('A
B'));
    }

    public function testBaz4056() {
        $expected = '<object type="application/x-shockwave-flash" data="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&amp;enablejs=true&amp;feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&amp;file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&amp;showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" width="680" height="412" id="showplayer"><param name="movie" value="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&amp;enablejs=true&amp;feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&amp;file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&amp;showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf"></param><param name="quality" value="best"></param><param name="allowscriptaccess" value="never"></param></object>';
        $actual = Video_HtmlHelper::scrub('<object type="application/x-shockwave-flash" data="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&enablejs=true&feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" width="680" height="412" allowfullscreen="true" id="showplayer"><param name="movie" value="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&enablejs=true&feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" /><param name="quality" value="best" /></object>');
        $this->assertEqual($expected, $actual);
    }

    public function testCleanText() {
        $this->assertEqual('A
B <a href="http://foo.com">http://foo.com</a>', Video_HtmlHelper::cleanText('A
B http://foo.com'));
        $x = '<object height="345" width="420"><param name="movie" value="http://www.youtube.com/v/g6XLAX0Sovk"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed><param name="allowscriptaccess" value="never"></param></object>';
        $this->assertEqual($x, Video_HtmlHelper::cleanText($x));
    }

    public function testPrettyDate() {
        $this->assertEqual('just now', Video_HtmlHelper::prettyDate(date('c', time() + 5)));
        $this->assertEqual('1 second ago', Video_HtmlHelper::prettyDate(date('c', time() - 1)));
        $this->assertEqual('55 seconds ago', Video_HtmlHelper::prettyDate(date('c', time() - 55)));
        $this->assertEqual('1 minute ago', Video_HtmlHelper::prettyDate('1 minute ago'));
        $this->assertEqual('2 minutes ago', Video_HtmlHelper::prettyDate('2 minutes ago'));
        $this->assertEqual('1 hour ago', Video_HtmlHelper::prettyDate('1 hour ago'));
        $this->assertEqual('2 hours ago', Video_HtmlHelper::prettyDate('2 hours ago'));
        $this->assertEqual('1 day ago', Video_HtmlHelper::prettyDate('1 day ago'));
        $this->assertEqual('Feb. 15, 1977', Video_HtmlHelper::prettyDate('February 15, 1977'));
    }

    public function testAverageRating() {
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Video_HtmlHelper::averageRating(3, 2, true));
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Video_HtmlHelper::averageRating(3, 1, true));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Video_HtmlHelper::averageRating(3, 2, false));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Video_HtmlHelper::averageRating(3, 1, false));
    }

    public function testAverageRatingWithUserSummary() {
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Video_HtmlHelper::averageRatingWithUserSummary(3, 2, 0, true));
        $this->assertEqual('<strong>Rating:</strong> <img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Video_HtmlHelper::averageRatingWithUserSummary(3, 1, 0, true));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 2 votes', Video_HtmlHelper::averageRatingWithUserSummary(3, 2, 0, false));
        $this->assertEqual('<img class="rating" src="' . xg_cdn('/xn_resources/widgets/index/gfx/rating/rating3.gif') . '" alt="3/5 stars" /> after 1 vote', Video_HtmlHelper::averageRatingWithUserSummary(3, 1, 0, false));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
