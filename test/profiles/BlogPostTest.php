<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Tests JavaScript dependencies
 */
class BlogPostTest extends UnitTestCase {

    public function testTextTitle() {
        $this->assertEqual('A & B Sound', BlogPost::textTitle('A
&amp; B <b>Sound</b>'));
                $this->assertEqual('A & B Sound', BlogPost::textTitle('A & B Sound'));
    }

    public function testUpgradeDescriptionFormat() {
        $this->assertEqual('a b
c d', BlogPost::upgradeDescriptionFormat('a
b<br>c
d', null));
        $this->assertEqual('Three things:
<ol>
<li>red</li>
<li>yellow</li>
<li>blue</li>
</ol>
- Goethe', BlogPost::upgradeDescriptionFormat('Three things:
<ol>
<li>red</li>
<li>yellow</li>
<li>blue</li>
</ol>
- Goethe', null));
        $this->assertEqual('a
b<br>c
d', BlogPost::upgradeDescriptionFormat('a
b<br>c
d', '2.2'));
        $original = <<<EOS
We're working through a major update to our <a href="http://www.ning.com/help">FAQ</a>, Support Pages, and <a href="http://docs.ning.com/">Ning</a> <a href="http://docs.ning.com/">Documentation</a>.<br/><br/>The goal of our support and documentation is <b>instant gratification</b> - getting you the answer you want when you want it, as quickly and easily as possible. While I think we have answers to just about anything people want to know, we don't always make getting to them as quick and easy as possible. That's what we're looking to change.<br/><br/>Our general approach to documentation taking people from simplest to most complex. Here's our current working outline of the most efficient way of getting people familiar with creating and customizing <a href="http://www.ning.com/about/features.html">your own social network</a> on Ning.<br/><br/>Here are the easiest to the most complex things you can do using Ning:<br/><br/><ol>
<li>Create your own social network</li>
<li>Use the "point and click" options you have in the "get your own" process: (1) set up your network, (2) choose your <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-1">features</a>, (3) customize your <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-2">appearance</a>, (4) choose your privacy and member settings, and (5) customize your profile questions.<br/></li>
<li><a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-6">Customize</a> the photo slideshows and video players on your network.<br/></li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-8">Premium Services</a> to your network.</li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-15">external "widgets"</a> or <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-16">RSS feeds</a> to your network.<br/></li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-5">your own HTML and CSS</a> to your network.</li>
</ol>
<br/>You can do all of these things without requesting a copy of the source code running your social network on Ning. That's typically where other social networking services stop. However, <a href="http://blog.ning.com/2007/02/what_is_a_platform.html">Ning is a platform</a>, which means that you can actually change your network by going directly into the source code running it and, well, change how it works. It's like online DIY. Things you can do on your own social network, if you know PHP:<br/><br/><ol>
<li>Put your social network in <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A6">another language</a>.<br/></li>
<li><a href="http://docs.ning.com/page/page/show?id=492524:Page:22">Customize any feature</a> on your social network.<br/></li>
<li>Add a new feature to your social network. (Note: we're in the process of making this ALOT easier for developers to do in the coming weeks and months.)<br/></li>
</ol>
<br/>Your social network on Ning is only one web application running on the Ning Platform. You can in fact create your own web application on the Ning Platform. <a href="http://cchits.ning.com/">CC Hits</a> is a great example of a web application built from scratch on the Ning Platform. Things you can do on the Ning Platform:<br/><br/><ol>
<li><a href="http://documentation.ning.com/group.php?FAQGroup:title=General+Questions+about+Development">Use the Ning Platform for development.</a><br/></li>
<li><a href="http://www.ning.com/?view=apps&amp;op=add">Create a simple web application</a>.</li>
<li><a href="http://documentation.ning.com/">Create a complex web application.</a></li>
<li><a href="http://documentation.ning.com/post.php?Post:slug=Querying-Content-Store">Understand and query</a> the Ning Content Store.<br/></li>
<li>Add <a href="http://documentation.ning.com/">platform - or system - services</a> to your web application.<br/></li>
<li>Take advantage of the <a href="http://documentation.ning.com/">API suite</a> when creating your web application.</li>
</ol>
So, this is our current working draft. Any feedback for us in terms of what you'd like to see? Anything that's confusing that you'd like us to address?<br/><br/>Any and all feedback is welcome!<br/><br/><br/><br/>
EOS;
        $expected = <<<EOS
We're working through a major update to our <a href="http://www.ning.com/help">FAQ</a>, Support Pages, and <a href="http://docs.ning.com/">Ning</a> <a href="http://docs.ning.com/">Documentation</a>.

The goal of our support and documentation is <b>instant gratification</b> - getting you the answer you want when you want it, as quickly and easily as possible. While I think we have answers to just about anything people want to know, we don't always make getting to them as quick and easy as possible. That's what we're looking to change.

Our general approach to documentation taking people from simplest to most complex. Here's our current working outline of the most efficient way of getting people familiar with creating and customizing <a href="http://www.ning.com/about/features.html">your own social network</a> on Ning.

Here are the easiest to the most complex things you can do using Ning:

<ol>
<li>Create your own social network</li>
<li>Use the "point and click" options you have in the "get your own" process: (1) set up your network, (2) choose your <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-1">features</a>, (3) customize your <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-2">appearance</a>, (4) choose your privacy and member settings, and (5) customize your profile questions.
</li>
<li><a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-6">Customize</a> the photo slideshows and video players on your network.
</li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-8">Premium Services</a> to your network.</li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-15">external "widgets"</a> or <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-16">RSS feeds</a> to your network.
</li>
<li>Add <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A270#a1-5">your own HTML and CSS</a> to your network.</li>
</ol>

You can do all of these things without requesting a copy of the source code running your social network on Ning. That's typically where other social networking services stop. However, <a href="http://blog.ning.com/2007/02/what_is_a_platform.html">Ning is a platform</a>, which means that you can actually change your network by going directly into the source code running it and, well, change how it works. It's like online DIY. Things you can do on your own social network, if you know PHP:

<ol>
<li>Put your social network in <a href="http://docs.ning.com/page/page/show?id=492524%3APage%3A6">another language</a>.
</li>
<li><a href="http://docs.ning.com/page/page/show?id=492524:Page:22">Customize any feature</a> on your social network.
</li>
<li>Add a new feature to your social network. (Note: we're in the process of making this ALOT easier for developers to do in the coming weeks and months.)
</li>
</ol>

Your social network on Ning is only one web application running on the Ning Platform. You can in fact create your own web application on the Ning Platform. <a href="http://cchits.ning.com/">CC Hits</a> is a great example of a web application built from scratch on the Ning Platform. Things you can do on the Ning Platform:

<ol>
<li><a href="http://documentation.ning.com/group.php?FAQGroup:title=General+Questions+about+Development">Use the Ning Platform for development.</a>
</li>
<li><a href="http://www.ning.com/?view=apps&amp;op=add">Create a simple web application</a>.</li>
<li><a href="http://documentation.ning.com/">Create a complex web application.</a></li>
<li><a href="http://documentation.ning.com/post.php?Post:slug=Querying-Content-Store">Understand and query</a> the Ning Content Store.
</li>
<li>Add <a href="http://documentation.ning.com/">platform - or system - services</a> to your web application.
</li>
<li>Take advantage of the <a href="http://documentation.ning.com/">API suite</a> when creating your web application.</li>
</ol>
So, this is our current working draft. Any feedback for us in terms of what you'd like to see? Anything that's confusing that you'd like us to address?

Any and all feedback is welcome!
EOS;
        $this->assertEqual($expected, BlogPost::upgradeDescriptionFormat($original, null));
    }

    public function testExcerpt1() {
        $this->doTestExcerpt('hell&hellip;', 'hello', 4);
    }

    public function testExcerpt2() {
        $this->doTestExcerpt('hello', 'hello', 5);
    }

    public function testExcerpt3() {
        $this->doTestExcerpt('hello <b><span>world</span></b>', 'hello <b><span>world</span></b>', 31);
    }

    public function testExcerpt4() {
        $this->doTestExcerpt('hello <b><span>world</span>&hellip;</b>', 'hello <b><span>world</span></b>', 30);
    }

    public function testExcerpt5() {
        $this->doTestExcerpt('hello&hellip;', 'hello <b><span>world</span></b>', 12);
    }

    public function testExcerpt6() {
        $this->doTestExcerpt('hello <b><span>world</span>&hellip;</b>', 'hello <b><span>world</span></b>', 24);
    }

    public function testExcerpt7() {
        $this->doTestExcerpt('a&hellip;', 'a < b >', 5);
    }

    public function testExcerpt8() {
        $this->doTestExcerpt('hello&hellip;', 'hello world', 6);
    }

    private function doTestExcerpt($expected, $description, $maxLength) {
        $blogPost = XN_Content::create('Note');
        $blogPost->description = $description;
        $this->assertEqual($expected, BlogPost::summarize($blogPost, $maxLength));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
