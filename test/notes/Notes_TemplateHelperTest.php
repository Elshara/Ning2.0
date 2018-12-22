<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Notes_TemplateHelperTest extends UnitTestCase {

    public function setUp() {
        $this->continueLink = '<p><a href="http://' . $_SERVER['HTTP_HOST'] . '/notes/Notes_Home">Continue</a></p>';
    }

    public function testExcerpt1() {
        $this->doTestExcerpt('hell&hellip; ' . $this->continueLink, 'hello', 4);
    }

    public function testExcerpt2() {
        $this->doTestExcerpt('hello', 'hello', 5);
    }

    public function testExcerpt3() {
        $this->doTestExcerpt('hello <b><span>world</span></b>', 'hello <b><span>world</span></b>', 31);
    }

    public function testExcerpt4() {
        $this->doTestExcerpt('hello <b><span>world</span>&hellip;</b> ' . $this->continueLink, 'hello <b><span>world</span></b>', 30);
    }

    public function testExcerpt5() {
        $this->doTestExcerpt('hello&hellip; ' . $this->continueLink, 'hello <b><span>world</span></b>', 12);
    }

    public function testExcerpt6() {
        $this->doTestExcerpt('hello <b><span>world</span>&hellip;</b> ' . $this->continueLink, 'hello <b><span>world</span></b>', 24);
    }

    public function testExcerpt7() {
        $this->doTestExcerpt('a&hellip; ' . $this->continueLink, 'a < b >', 5);
    }

    public function testExcerpt8() {
        $this->doTestExcerpt('hello&hellip; ' . $this->continueLink, 'hello world', 6);
    }

    private function doTestExcerpt($expected, $description, $maxLength) {
        W_Cache::getWidget('notes')->includeFileOnce('/lib/helpers/Notes_TemplateHelper.php');
        $note = XN_Content::create('Note');
        $note->description = $description;
        $this->assertEqual($expected, Notes_TemplateHelper::excerpt($note, $maxLength));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
