<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/page/lib/helpers/Page_HtmlHelper.php');

class Page_HtmlHelperTest extends UnitTestCase {

    public function testPrettyDate() {
        $this->assertEqual('just now', Page_HtmlHelper::prettyDate(date('c', time() + 5)));
        $this->assertEqual('1 second ago', Page_HtmlHelper::prettyDate(date('c', time() - 1)));
        $this->assertEqual('55 seconds ago', Page_HtmlHelper::prettyDate(date('c', time() - 55)));
        $this->assertEqual('1 minute ago', Page_HtmlHelper::prettyDate('1 minute ago'));
        $this->assertEqual('2 minutes ago', Page_HtmlHelper::prettyDate('2 minutes ago'));
        $this->assertEqual('1 hour ago', Page_HtmlHelper::prettyDate('1 hour ago'));
        $this->assertEqual('2 hours ago', Page_HtmlHelper::prettyDate('2 hours ago'));
        $this->assertEqual('1 day ago', Page_HtmlHelper::prettyDate('1 day ago'));
        $this->assertEqual('Feb. 15, 1977', Page_HtmlHelper::prettyDate('February 15, 1977'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


