<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_TemplateHelpers.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');
XG_App::includeFileOnce('/widgets/page/lib/helpers/Page_HtmlHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_HtmlHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_HtmlHelper.php');

class XG_TemplateHelpersTest extends UnitTestCase {

    public function test_xg_absolute_url() {
        $testUrls = array('http://www.foo.com',
                          'http://www.foo.com/',
                          'http://www.foo.com/?a=b',
                          'http://www.foo.com/?a=b#c',
                          '',
                          '/',
                          '/?a=b',
                          '/?a=b#c');
        foreach ($testUrls as $testUrl) {
            if (preg_match('/^http:\/\//', $testUrl)) {
                $this->assertEqual($testUrl, xg_absolute_url($testUrl));
            } else {
                $this->assertEqual('http://'.$_SERVER['SERVER_NAME'].$testUrl, xg_absolute_url($testUrl));
            }
        }
    }

    public function test_xg_relative_url() {
        $testUrls = array('http://www.foo.com',
                          'http://www.foo.com/',
                          'http://www.foo.com/?a=b',
                          'http://www.foo.com/?a=b#c',
                          '',
                          '/',
                          '/?a=b',
                          '/?a=b#c');
        foreach ($testUrls as $testUrl) {
            if (preg_match('/^http:\/\/(?:[^\/]+)(\/?.*)$/', $testUrl, $matches)) {
                $this->assertEqual($matches[1], xg_relative_url($testUrl));
            } else {
                $this->assertEqual($testUrl, xg_relative_url($testUrl));
            }
        }
    }

    public function test_xg_action_button_text() {
        $this->assertEqual('<span>foo bar</span>', xg_action_button_text('foo bar'));
        $this->assertEqual('<span class="split">foo<br />bar</span>', xg_action_button_text('foo<br />bar'));
        $this->assertEqual('<span style="display: none">foo bar</span>', xg_action_button_text('foo bar', 'style="display: none"'));
        $this->assertEqual('<span style="display: none" class="split">foo<br />bar</span>', xg_action_button_text('foo<br />bar', 'style="display: none"'));
        $this->assertEqual('<span style="display: none" class="added-span">foo bar</span>', xg_action_button_text('foo bar', 'style="display: none"', 'added-span'));
        $this->assertEqual('<span style="display: none" class="split added-span">foo<br />bar</span>', xg_action_button_text('foo<br />bar', 'style="display: none"', 'added-span'));
    }

    public function test_xg_linkify() {
        $this->assertEqual('<a href="http://www.ning.com">http://www.ning.com</a>',xg_linkify('http://www.ning.com'));
        $this->assertEqual('ffhttp://www.ning.com',xg_linkify('ffhttp://www.ning.com'));
        $this->assertEqual('<a href="http://www.ning.com">www.ning.com</a>',xg_linkify('www.ning.com'));
        $this->assertEqual('FFwww.ning.com',xg_linkify('FFwww.ning.com'));
        $this->assertEqual("<img src='http://www.google.com/intl/en_ALL/images/logo.gif' />",xg_linkify("<img src='http://www.google.com/intl/en_ALL/images/logo.gif' />"));
        $this->assertEqual('<img src="http://www.google.com/intl/en_ALL/images/logo.gif" />',xg_linkify('<img src="http://www.google.com/intl/en_ALL/images/logo.gif" />'));
    }

    public function test_xg_resize_embeds() {
        $this->assertEqual('<p>hello world</p>', xg_resize_embeds('<p>hello world</p>', 100));
        $this->assertEqual('<embed style="width:100px;height:10px"></embed><embed style="width:15;height:5"></embed><embed style="width:100;height:20"></embed>', xg_resize_embeds('<embed style="width:200px;height:20px"></embed><embed style="width:15;height:5"></embed><embed style="width:400;height:80"></embed>', 100));
        $this->assertEqual('<embed style="width:206px;height:20px"></embed>', xg_resize_embeds('<embed style="width:3000px;height:300px"></embed>', NULL, 1));
        $this->assertEqual('<embed style="width:438px;height:43px"></embed>', xg_resize_embeds('<embed style="width:3000px;height:300px"></embed>', NULL, 2));
        $this->assertEqual('<embed style="width:800px;height:80px"></embed>', xg_resize_embeds('<embed style="width:3000px;height:300px"></embed>', NULL, 3));
    }

    public function test_xg_shorten_linkText() {
        $this->assertEqual('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_fo...</a>', xg_shorten_linkText('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_for_testing_things</a>'));
        $this->assertEqual('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_fo...</a>', xg_shorten_linkText('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_for_te</a>'));
        $this->assertEqual('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_for_t</a>', xg_shorten_linkText('<a href="#">http://google.com/this_is_just_some_really_really_long_link_text_for_t</a>'));
    }

    public function testScrubbing1() {
        $text = 'Welcome to Ning Network Creators! This is the place to meet other creators of social networks on Ning, ask questions of us ("us" being Ning in this case) and each other.

<strong>Getting Started</strong>

So the first thing that you want to do to become a Network Creator is create your own social network on Ning. You can do so by following the "Create Your Own Social Network" button at the top of this page or over the right hand column in the "About Ning Network Creators."

From there, we provide a helpful guide to the most frequently asked questions on customizing your new social network directly in your network itself. From your new network, click on the "Manage" navigation button or the link on the right hand column of any page that says, "Manage your network." From this Manage page, there\'s a link to "Advanced Customization". The Advanced Customization link will give you the lay of the land.

If you\'ve looked through that and still have questions, by all means post your questions, thoughts, comments, and opinions here. There\'s a good chance other people have similar questions, thoughts, comments, and opinions and we\'ll respond to discussions here usually within a few hours.

<strong>Video Demo from <a href="http://www.podtech.net/scobleshow/technology/1373/build-your-own-social-space-with-ning-version-2">The Scoble Show</a></strong>

<!-- PodTech Media Player v1.1.3 --><embed type="application/x-shockwave-flash" src="http://www.podtech.net/player/podtech-player.swf?bc=3F34K2L1" flashvars="content=http://media1.podtech.net/media/2007/02/PID_010362/Podtech_NING_demo.flv&totalTime=751000&" height="269" width="320" />';
        $expectedScrubbedText = 'Welcome to Ning Network Creators! This is the place to meet other creators of social networks on Ning, ask questions of us ("us" being Ning in this case) and each other.

<strong>Getting Started</strong>

So the first thing that you want to do to become a Network Creator is create your own social network on Ning. You can do so by following the "Create Your Own Social Network" button at the top of this page or over the right hand column in the "About Ning Network Creators."

From there, we provide a helpful guide to the most frequently asked questions on customizing your new social network directly in your network itself. From your new network, click on the "Manage" navigation button or the link on the right hand column of any page that says, "Manage your network." From this Manage page, there\'s a link to "Advanced Customization". The Advanced Customization link will give you the lay of the land.

If you\'ve looked through that and still have questions, by all means post your questions, thoughts, comments, and opinions here. There\'s a good chance other people have similar questions, thoughts, comments, and opinions and we\'ll respond to discussions here usually within a few hours.

<strong>Video Demo from <a href="http://www.podtech.net/scobleshow/technology/1373/build-your-own-social-space-with-ning-version-2">The Scoble Show</a></strong>

<embed type="application/x-shockwave-flash" src="http://www.podtech.net/player/podtech-player.swf?bc=3F34K2L1" flashvars="content=http://media1.podtech.net/media/2007/02/PID_010362/Podtech_NING_demo.flv&amp;totalTime=751000&amp;" height="269" width="320" allowscriptaccess="never"></embed>';
        $this->doScrubTest($text, $expectedScrubbedText);
    }

    public function testScrubbing2() {
        $text = 'Hi Guys!

So I wanted to flag two known issues tonight and let you know what we\'re doing about them:

<ol>
<li><strong>"Protect Your Network" Premium Services Option doesn\'t take the link to Create Your Own Social Network off the right hand column.</strong> <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A488">We know about this</a>, will not charge you for purchasing it, and are going to have it fixed next week.</li>
<li><strong>If you have over 35 friends, your My Page, Friend Requests, and Message panels up top will be intermittently slow.</strong> We should have this addressed tonight, but if people are running into spotty slowness on those pages, it should be fast again tomorrow.</li>
<li><strong>Yahoo blocking their APIs to Networks on Ning.</strong> We\'re following up with Yahoo tomorrow, but if you are seeing any issues with pulling in anything from Yahoo, that\'s probably the culprit. We\'ll keep you posted.
<li><strong>Improvements to the Discussion Forum and Flickr.</strong> We\'ve seen some great comments on this discussion forum about <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A388">Forum Improvements</a> and <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A633"><Flickr Enhancements</a>. It\'s awesome stuff and we really appreciate it. </li>
</ol>

I\'m going to be off the discussion for the next, say, 8 hours (zzzzzzzzz) but then we\'ll be back in action to answer questions, discover and fix issues, and sing your praises for giving us fantastic ideas and suggestions tomorrow.

Good Night!';
        $expectedScrubbedText = 'Hi Guys!

So I wanted to flag two known issues tonight and let you know what we\'re doing about them:

<ol>
<li><strong>"Protect Your Network" Premium Services Option doesn\'t take the link to Create Your Own Social Network off the right hand column.</strong> <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A488">We know about this</a>, will not charge you for purchasing it, and are going to have it fixed next week.</li>
<li><strong>If you have over 35 friends, your My Page, Friend Requests, and Message panels up top will be intermittently slow.</strong> We should have this addressed tonight, but if people are running into spotty slowness on those pages, it should be fast again tomorrow.</li>
<li><strong>Yahoo blocking their APIs to Networks on Ning.</strong> We\'re following up with Yahoo tomorrow, but if you are seeing any issues with pulling in anything from Yahoo, that\'s probably the culprit. We\'ll keep you posted.</li>
<li><strong>Improvements to the Discussion Forum and Flickr.</strong> We\'ve seen some great comments on this discussion forum about <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A388">Forum Improvements</a> and <a href="http://networkcreators.ning.com/forum/topic/show?id=492224%3ATopic%3A633"></a>. It\'s awesome stuff and we really appreciate it.</li>
</ol>

I\'m going to be off the discussion for the next, say, 8 hours (zzzzzzzzz) but then we\'ll be back in action to answer questions, discover and fix issues, and sing your praises for giving us fantastic ideas and suggestions tomorrow.

Good Night!';
        $this->doScrubTest($text, $expectedScrubbedText);
    }

    public function testScrubbing3() {
        $text = '<ol>
<li><strong>test</strong></li>
</ol>';
        $expectedScrubbedText = '<ol>
<li><strong>test</strong></li>
</ol>';
        $this->doScrubTest($text, $expectedScrubbedText);
    }

    public function testScrubbing4() {
        $text = '<ol>

<li><strong>test</strong></li>

</ol>';
        $expectedScrubbedText = '<ol>
<li><strong>test</strong></li>
</ol>';
        $this->doScrubTest($text, $expectedScrubbedText);
    }

    public function testScrubbing5() {
        $text = '<Br><Hr><Br style="clear:left">';
        $expectedScrubbedText = '<br/><hr/><br style="clear:left"/>';
        $this->doScrubTest($text, $expectedScrubbedText);
    }

    private function doScrubTest($text, $expectedScrubbedText) {
        /*
        // For debugging [Jon Aquino 2007-03-02]
        echo '<pre>';
        echo xnhtmlentities(str_replace("\n", "(n)", $expectedScrubbedText));
        echo "\n";
        echo xnhtmlentities(str_replace("\n", "(n)", Photo_HtmlHelper::cleanText($text)));
        echo '</pre>';
        */
        $this->assertEqual($expectedScrubbedText, Forum_HtmlHelper::scrub($text));
        $this->assertEqual($expectedScrubbedText, Photo_HtmlHelper::cleanText($text));
        $this->assertEqual($expectedScrubbedText, Video_HtmlHelper::cleanText($text));
        $this->assertEqual($expectedScrubbedText, xg_linkify(Forum_HtmlHelper::scrub($text)));
        $this->assertEqual($expectedScrubbedText, xg_shorten_linkText(xg_linkify(Forum_HtmlHelper::scrub($text))));
        $this->assertEqual($expectedScrubbedText, xg_resize_embeds(xg_shorten_linkText(xg_linkify(Forum_HtmlHelper::scrub($text)))), 590);
    }

    public function test_xg_nl2br() {
        $this->assertEqual('a<br />
b<br />
<object></object>
<embed></embed>
<param></param>
<li>
<p>c</p>
d', xg_nl2br('a<br />
b
<object></object>
<embed></embed>
<param></param>
<li>
<p>c</p>
d'));
    }

    public function test_xg_scrub() {
        $this->assertEqual('Normal Text
<br/>
<br/>
<br/>
<ol>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ol>
<ul>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ul>
<dl>
<dt>One</dt>
<dd>A Number</dd>
</dl>', xg_scrub('Normal Text
<BR>
<br/>
<br />
<ol>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ol>
<ul>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ul>
<dl>
<dt>One</dt>
<dd>A Number</dd>
</dl>'));
    }

    public function test_xg_scrub2() {
        $lorem = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.';
        $this->assertEqual($lorem, xg_scrub($lorem));
        $this->assertEqual('<img src="http://example.com"/><hr/><div class="foo"></div>', xg_scrub('<img src="http://example.com"><hr><div class="foo">'));
        $this->assertEqual('<p>hello</p>
<p>there</p>
<p>world</p>', xg_scrub('<p>hello</p>
<p>there</p><p>world</p>'));
    }

    public function test_xg_cdn1() {
        $this->assertEqual('http://static' . XN_AtomHelper::$DOMAIN_SUFFIX . '/' . XN_Application::load()->relativeUrl . '/instances/main/embeddable/badge-config.xml', xg_cdn('/xn_resources/instances/main/embeddable/badge-config.xml', FALSE));
        $this->assertEqual('http://static' . XN_AtomHelper::$DOMAIN_SUFFIX . '/' . XN_Application::load()->relativeUrl . '/instances/main/embeddable/badge-config.xml', xg_cdn('http://foo.com/xn_resources/instances/main/embeddable/badge-config.xml', FALSE));
        $this->assertEqual('http://foo.com', xg_cdn('http://foo.com', FALSE));
        $this->assertEqual('http://foo.com/', xg_cdn('http://foo.com/', FALSE));
    }

    public function test_xg_cdn2() {
        $version = urlencode(XG_Version::currentCodeVersion());
        $this->assertEqual('http://static' . XN_AtomHelper::$DOMAIN_SUFFIX . '/' . XN_Application::load()->relativeUrl . '/instances/main/embeddable/badge-config.xml?v=' . $version, xg_cdn('/xn_resources/instances/main/embeddable/badge-config.xml'));
        $this->assertEqual('http://static' . XN_AtomHelper::$DOMAIN_SUFFIX . '/' . XN_Application::load()->relativeUrl . '/instances/main/embeddable/badge-config.xml?v=' . $version, xg_cdn('http://foo.com/xn_resources/instances/main/embeddable/badge-config.xml'));
        $this->assertEqual('http://foo.com', xg_cdn('http://foo.com'));
        $this->assertEqual('http://foo.com/', xg_cdn('http://foo.com/'));
    }

    public function test_xgAgeAndLocation() {
        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        User::loadOrCreate($profile);
        $this->assertEqual('', xg_age_and_location($profile, false, true));
        $profile->birthdate = '1977-02-15';
        $this->assertEqual('31', xg_age_and_location($profile, false, true));
        $profile->country = 'CA';
        $this->assertEqual('31<br/>Canada', xg_age_and_location($profile, false, true));
        $profile->location = 'Victoria';
        $this->assertEqual('31<br/>Victoria, Canada', xg_age_and_location($profile, false, true));
        $profile->gender = 'm';
        $this->assertEqual('31, Male<br/>Victoria, Canada', xg_age_and_location($profile, false, true));
    }

    public function test_xgAgeAndLocationSingleLine() {
        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $this->assertEqual('', xg_age_and_location($profile, true, true));
        $profile->birthdate = '1977-02-15';
        $this->assertEqual('31', xg_age_and_location($profile, true, true));
        $profile->country = 'CA';
        $this->assertEqual('31, Canada', xg_age_and_location($profile, true, true));
        $profile->location = 'Victoria';
        $this->assertEqual('31, Victoria, Canada', xg_age_and_location($profile, true, true));
        $profile->gender = 'm';
        $this->assertEqual('31, Male, Victoria, Canada', xg_age_and_location($profile, true, true));
    }

    public function test_xgBr2Nl() {
        $this->assertEqual("a
b
c
d", xg_br2nl('a<br>b<BR />c<bR/>d'));
    }

    public function testXgRatingImage() {
        $this->assertPattern('@2.5.gif@', xg_rating_image(2.6));
        $this->assertPattern('@3.gif@', xg_rating_image(2.9));
        $this->assertEqual(xg_rating_image(2.9), Video_HtmlHelper::stars(2.9));
        $this->assertEqual(xg_rating_image(2.9), Photo_HtmlHelper::stars(2.9));
    }

    public function testXgExcerptHtml() {
        $this->assertEqual('Hi <a href="1">foo&hellip;</a>', xg_excerpt_html('Hi <a href="1">foo</a> boy!', 22, $excerpted));
        $this->assertEqual('Hi <a href="1">foo&hellip;</a>', xg_excerpt_html('Hi <a href="1">foo</a> boy!', 23, $excerpted));
        $this->assertEqual('Hi <a href="1">foo</a> b&hellip;', xg_excerpt_html('Hi <a href="1">foo</a> boy!', 24, $excerpted));
        $this->assertEqual('Hi&hellip;', xg_excerpt_html('Hi <a href="1">foo</a> boy!', 5, $excerpted));
    }

    public function testXgExcerpt() {
        $this->assertEqual('12...', xg_excerpt('123456789', 5, null, $excerpted, false, null));
        $this->assertEqual('12...', xg_excerpt('123456789', 5, null, $excerpted, false, null, true));
    }

    public function testXgElapsedTime() {
        $this->assertEqual('Mar 20', xg_elapsed_time('March 20, ' . date('Y'), $showingMonth));
        $this->assertEqual('Mar. 20, 1968', xg_elapsed_time('March 20, 1968', $showingMonth));
        $this->assertEqual('March 20', xg_elapsed_time('March 20, ' . date('Y'), $showingMonth, 'F j', 'F j, Y'));
        $this->assertEqual('March 20, 1968', xg_elapsed_time('March 20, 1968', $showingMonth, 'F j', 'F j, Y'));
    }

    public function testXgMailtoUrl() {
        $this->assertEqual('mailto:jon@foo.com?subject=a%20b&body=c%20d', xg_mailto_url('jon@foo.com', 'a b', 'c d'));
        $this->assertEqual('mailto:?subject=a%20b&body=c%20d', xg_mailto_url('', 'a b', 'c d'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
