<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_TablayoutHelper.php');

class Index_TablayoutHelperTest extends UnitTestCase {

    public function testCreatePageProper() {
        $helper = new TestTablayoutHelper();
        $page = $helper->createPageProper(str_repeat('a', 300));
        $this->assertEqual(str_repeat('a', 200), $page->title);
        $this->assertEqual('', $page->description);
        $this->assertEqual('page', $page->my->mozzle);
        $this->assertEqual(0, $page->my->viewCount);
        $this->assertEqual(XG_App::appIsPrivate(), $page->isPrivate);
    }

    public function testCreatePage() {
        Mock::generate('stdClass', 'MockXN_Content', array('save'));
        $page = new MockXN_Content();
        $page->expectOnce('save');
        $page->id = '123';
        Mock::generatePartial('TestTablayoutHelper', 'TablayoutHelperPartialMock', array('createPageProper'));
        $helper = new TablayoutHelperPartialMock();
        $helper->expectOnce('createPageProper', array(str_repeat('a', 300)));
        $helper->setReturnValue('createPageProper', $page);
		$this->assertEqual($helper->createPage(str_repeat('a', 300)), '123');
    }

    public function testCreateInternalStyleSheet() {
        $subTabColors = array('textColor' => 'red', 'textColorHover' => 'green', 'backgroundColor' => 'blue', 'backgroundColorHover' => 'yellow');
        $css = Index_TablayoutHelper::createInternalStyleSheet($subTabColors);
        $expectedCss = '
<style type="text/css" media="screen,projection">
#xg_navigation ul div.xg_subtab ul li a {
  color: red;
  background: blue;
}
#xg_navigation ul div.xg_subtab ul li a:hover {
  color: green;
  background: yellow;
}
</style>';
        $this->assertEqual(preg_replace('@\s@', '', $expectedCss), preg_replace('@\s@', '', $css));
    }

}

class TestTablayoutHelper extends Index_TablayoutHelper {
    public function createPageProper($title) {
        return parent::createPageProper($title);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
