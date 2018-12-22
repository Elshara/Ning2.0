<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_HtmlHelper.php');

class Forum_HtmlHelperTest extends UnitTestCase {

    public function testTagLinks() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->assertEqual('<a href="/forum/topic/listForTag?tag=pizza">pizza</a>, <a href="/forum/topic/listForTag?tag=pop">pop</a>', preg_replace('@http://.*?php@', '', Forum_HtmlHelper::tagLinks(array('pizza', 'pop'))));
    }

    public function testScrub() {
        $this->assertEqual('<div id="foo"></div>', Forum_HtmlHelper::scrub('<div id="foo"></div>'));
        $this->assertEqual('A
B', Forum_HtmlHelper::scrub('A
B'));
        $x = '<object height="345" width="420"><param name="movie" value="http://www.youtube.com/v/g6XLAX0Sovk"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed><param name="allowscriptaccess" value="never"></param></object>';
        $this->assertEqual($x, Forum_HtmlHelper::scrub($x));
        $this->assertEqual('<embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed>
Hello', Forum_HtmlHelper::scrub('<embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed>Hello'));
        $this->assertEqual('<embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed>
<i>foo</i>', Forum_HtmlHelper::scrub('<embed src="http://www.youtube.com/v/g6XLAX0Sovk" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="never" height="345" width="420"></embed><i>foo</i>'));
    }

    public function testFileSizeDisplayText() {
        $this->assertEqual('1 KB', Forum_HtmlHelper::fileSizeDisplayText(55));
        $this->assertEqual('130 KB', Forum_HtmlHelper::fileSizeDisplayText(133456));
        $this->assertEqual('1.4 MB', Forum_HtmlHelper::fileSizeDisplayText(1468123));
    }

    public function testAttachmentIconUrl() {
        XG_TestHelper::setCurrentWidget('forum');
        $this->assertEqual(xg_cdn('/xn_resources/widgets/forum/gfx/fileicons/pdf.gif'), preg_replace('@http://.*?php@', '', Forum_HtmlHelper::attachmentIconUrl('foo.pdf')));
        $this->assertEqual(xg_cdn('/xn_resources/widgets/forum/gfx/fileicons/pdf.gif'), preg_replace('@http://.*?php@', '', Forum_HtmlHelper::attachmentIconUrl('FOO.PDF')));
        $this->assertEqual(xg_cdn('/xn_resources/widgets/forum/gfx/fileicons/file.gif'), preg_replace('@http://.*?php@', '', Forum_HtmlHelper::attachmentIconUrl('foo')));
    }

}


require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';