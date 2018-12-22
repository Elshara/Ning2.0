<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

class XG_HttpHelperTest extends UnitTestCase {
    private $base_urls = array(
        'http://google.com/search',
        'http://example.com/some/path',
        'http://ning.com/',
    );

    private $base_url = '';

    public function setUp() {
        static $host;
        if (! $host) { $host = $_SERVER['HTTP_HOST']; }
        $_SERVER['HTTP_HOST'] = $host;
        shuffle($this->base_urls);
        $this->base_url = reset($this->base_urls);
    }

    public function testHasParameter() {
        $this->assertEqual(true, XG_HttpHelper::hasParameter('http://example.org?x=0', 'x'));
        $this->assertEqual(false, XG_HttpHelper::hasParameter('http://example.org?x=0', 'y'));
        $this->assertEqual(false, XG_HttpHelper::hasParameter('http://example.org?x=0&y=', 'y'));
        $this->assertEqual(true, XG_HttpHelper::hasParameter('http://example.org?x=0&y=%26z=1', 'y'));
        $this->assertEqual(false, XG_HttpHelper::hasParameter('http://example.org?x=0&y=%26z=1', 'z'));
        $this->assertIdentical('0', XG_HttpHelper::getParameter('http://example.org?x=0', 'x'));
        $this->assertIdentical(null, XG_HttpHelper::getParameter('http://example.org?x=0', 'y'));
        $this->assertIdentical('', XG_HttpHelper::getParameter('http://example.org?x=0&y=', 'y'));
        $this->assertIdentical(':', XG_HttpHelper::getParameter('http://example.org?x=0&y=%3A', 'y'));
        $this->assertIdentical('&z=1', XG_HttpHelper::getParameter('http://example.org?x=0&y=%26z=1', 'y'));
    }

    public function testAddParameters() {
        $this->assertEqual('http://google.com/?a=1&b=2', XG_HttpHelper::addParameters('http://google.com', array('a' => '1', 'b' => '2')));
        $this->assertEqual('http://example.org/?a=0&b=0', XG_HttpHelper::addParameters('http://example.org', array('a' => 0, 'b' => '0')));
    }

    public function testAddParameterBasicUsage() {
        $expected = "{$this->base_url}?q=Hello+World";
        $actual = XG_HttpHelper::addParameter($this->base_url, 'q', 'Hello World');
        $this->assertEqual($expected, $actual);
    }

    public function testAddParameterWithQueryString() {
        $version = rand(10, 20);
        $expected = "{$this->base_url}?q=Hello+World&v={$version}";
        $actual = XG_HttpHelper::addParameter($this->base_url . '?q=Hello+World', 'v', $version);
        $this->assertEqual($expected, $actual);
    }

    public function testAddParameterWithZeroParameter() {
        $expected = "{$this->base_url}?v=0";
        $actual = XG_HttpHelper::addParameter($this->base_url, 'v', 0);
        $this->assertEqual($expected, $actual);
    }

    public function testAddParameterWithZeroStringParameter() {
        $expected = "{$this->base_url}?v=0";
        $actual = XG_HttpHelper::addParameter($this->base_url, 'v', '0');
        $this->assertEqual($expected, $actual);
    }

    public function testAddParameterAgainWithZeroParameter() {
        $random_number = rand(10, 20);
        $expected = "{$this->base_url}?v=0";
        $actual = XG_HttpHelper::addParameter("{$this->base_url}?v={$random_number}", 'v', 0);
        $this->assertEqual($expected, $actual);
    }

    public function testRemoveParameters() {
        $this->assertEqual('http://google.com/?a=1&c=3&e=5', XG_HttpHelper::removeParameters('http://google.com?a=1&b=2&c=3&d=4&e=5', array('b', 'd')));
        $this->assertEqual('http://google.com?a=1&b=2&c=3&d=4&e=5', XG_HttpHelper::removeParameters('http://google.com?a=1&b=2&c=3&d=4&e=5', array()));
        $this->assertEqual('http://google.com/?b=2&c=3&d=4&e=5', XG_HttpHelper::removeParameters('http://google.com?a=1&b=2&c=3&d=4&e=5', array('a')));
        $this->assertEqual('http://google.com/', XG_HttpHelper::removeParameters('http://google.com?a=1&b=2&c=3&d=4&e=5', array('a', 'b', 'c', 'd', 'e', 'z', 'z')));
    }

    public function testCurrentUrl() {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/index.php/pizza';
        $this->assertEqual('http://example.com/pizza', XG_HttpHelper::currentUrl());

        $group = Group::create('Food Lovers');
        $group->my->url = 'foodlovers';
        $group->save();

        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/index.php/groups/invitation/new?groupUrl=foodlovers&';
        $this->assertEqual('http://example.com/group/foodlovers/invitation/new', XG_HttpHelper::currentUrl());

        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/index.php/forum/topic/show?groupUrl=foodlovers&id=656995%3ATopic%3A2386';
        $this->assertEqual('http://example.com/group/foodlovers/forum/topic/show?id=656995%3ATopic%3A2386', XG_HttpHelper::currentUrl());
    }

    public function testIsHomepage() {
        $this->assertEqual(false, XG_HttpHelper::isHomepage(null));
        $this->assertEqual(false, XG_HttpHelper::isHomepage(''));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('/profiles'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('/profiles?x=5'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main?x=5'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main/index'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main/index?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('/main/foo'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main/index/index'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('/main/index/index?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('/main/index/foo'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('/main/index/index/index'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST']));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST'] . '/'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST'] . '/?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST'] . '/photo'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST'] . '/photos'));
        $this->assertEqual(true, XG_HttpHelper::isHomepage('http://' . $_SERVER['HTTP_HOST'] . '/main'));
        $this->assertEqual(false, XG_HttpHelper::isHomepage('http://example.org/main'));
    }

    public function testIsMyPage() {
        $this->assertEqual(false, XG_HttpHelper::isMyPage(null));
        $this->assertEqual(false, XG_HttpHelper::isMyPage(''));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('/'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles?x=5'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('/profiles/profile'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/' . XN_Profile::current()->screenName));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/' . XN_Profile::current()->screenName . '?x=5'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/' . User::profileAddress(XN_Profile::current()->screenName)));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/' . User::profileAddress(XN_Profile::current()->screenName) . '?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('/profiles/profile/023740'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?id=' . XN_Profile::current()->screenName));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?id=' . XN_Profile::current()->screenName . '&x=5'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?id=' . User::profileAddress(XN_Profile::current()->screenName)));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?id=' . User::profileAddress(XN_Profile::current()->screenName) . '&x=5'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('/profiles/profile/show?id=023740'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?screenName=' . XN_Profile::current()->screenName));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('/profiles/profile/show?screenName=' . XN_Profile::current()->screenName . '&x=5'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('/profiles/profile/show?screenName=023740'));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('http://' . $_SERVER['HTTP_HOST'] . '/profiles/profile/' . XN_Profile::current()->screenName));
        $this->assertEqual(true, XG_HttpHelper::isMyPage('http://' . $_SERVER['HTTP_HOST'] . '/profiles/profile/' . XN_Profile::current()->screenName . '?x=5'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('http://' . $_SERVER['HTTP_HOST'] . '/profiles/profile/023740'));
        $this->assertEqual(false, XG_HttpHelper::isMyPage('http://example.org/profiles/profile/' . XN_Profile::current()->screenName));
    }

    public function testProfileUrl() {
        $user = XN_Content::create('User');
        $user->title = 'Joe';
        $user->save();
        User::insertIntoUserMap(array($user));
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/u_Joe', XG_HttpHelper::profileUrl($user));
        $user->my->profileAddress = 'MrCool';
        $this->assertEqual('http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/u_Joe', XG_HttpHelper::profileUrl($user));
    }

    public function testJoinParsedUrl() {
        $schemes = array(null, 'http', 'ftp');
        $hosts = array(null, 'server', 'server.domain.com');
        $users = array(null, 'http', 'user');
        $passwords = array(null, 'password', 'http');
        $paths = array('/', '/a', '/a/b/c/d/e', '/averyvery/longlonglonglong/path/');
        $queries = array(null, 'a=', 'a=b', 'a=b&c=', 'a=b&c=d');
        $anchors = array(null, 'anchor');

        # build urls
        $urls = array();
        foreach ($schemes as $scheme) {
            foreach ($users as $user) {
                foreach ($passwords as $password) {
                    foreach ($hosts as $host) {
                        foreach ($paths as $path) {
                            foreach ($queries as $query) {
                                foreach ($anchors as $anchor) {
                                    $url = ! is_null($scheme) && ! is_null($host) ? $scheme . '://' : '';
                                    if (! is_null($scheme) && ! is_null($host) && (! is_null($user) || ! is_null($password))) {
                                        $url .= ! is_null($user) ? $user : '';
                                        $url .= ! is_null($password) ? ':' . $password : '';
                                        if (! is_null($user) || ! is_null($password)) {
                                            $url .= '@';
                                        }
                                    }
                                    $url .= ! is_null($scheme) && ! is_null($host) ? $host : '';
                                    $url .= $path;
                                    $url .= ! is_null($query) ? '?' . $query : '';
                                    $url .= ! is_null($anchor) ? '#' . $anchor : '';
                                    $urls[$url] = 1;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($urls as $url => $v) {
            $this->assertEqual($url, XG_HttpHelper::joinParsedUrl(parse_url($url)));
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
