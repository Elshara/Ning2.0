<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/page/lib/helpers/Page_BulkHelper.php');

class Page_BulkHelperTest extends BulkHelperTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('page');
    }

    public function testSetPrivacy() {
        $ids = array();
        $pizzaPage = Page::create('Pepperoni Pizza', 'Delicious pizza.');
        $pizzaPage->save();
        $ids[] = $pizzaPage->id;
        $saladPage = Page::create('Tuna Nicoise', 'Lip smacking salad.');
        $saladPage->save();
        $ids[] = $saladPage->id;
        Page_BulkHelper::setPrivacy(30, true);
        $this->checkPrivacy(true, $ids);
        Page_BulkHelper::setPrivacy(30, false);
        $this->checkPrivacy(false, $ids);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
