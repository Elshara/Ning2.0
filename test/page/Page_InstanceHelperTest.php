<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/page/lib/helpers/Page_InstanceHelper.php');

class Page_InstanceHelperTest extends UnitTestCase {

    public function testValidate1() {
        $this->doTestValidate(array(), array(
            array('title' => 'Pages', 'directory' => 'page', 'displayTab' => false),
            array('title' => 'Pages', 'directory' => 'page2', 'displayTab' => false)
        ));
    }

    public function testValidate2() {
        $this->doTestValidate(array('1-directory', '2-directory'), array(
            array('title' => 'Pages', 'directory' => 'page', 'displayTab' => false),
            array('title' => 'Pages', 'directory' => 'PAGE', 'displayTab' => false),
            array('title' => 'Pages', 'directory' => 'page', 'displayTab' => false),
        ));
    }

    public function testValidate3() {
        $this->doTestValidate(array('0-directory', '1-title'), array(
            array('title' => 'Foo', 'directory' => '', 'displayTab' => false),
            array('title' => '', 'directory' => 'page', 'displayTab' => false),
        ));
    }

    public function testValidate4() {
        $this->doTestValidate(array('2-directory', '3-directory', '4-directory', '5-directory', '6-directory'), array(
            array('title' => 'Foo', 'directory' => 'Foo', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo_o', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo o', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo-o', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo+o', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo/o', 'displayTab' => false),
            array('title' => 'Foo', 'directory' => 'Fo.o', 'displayTab' => false),
            array('title' => 'Page', 'directory' => 'page', 'displayTab' => false),
        ));
    }

    public function testValidate5() {
        $this->doTestValidate(array('0-directory'), array(
        ));
    }

    public function testValidate6() {
        $this->doTestValidate(array('0-directory'), array(
            array('title' => 'Page', 'directory' => 'foo', 'displayTab' => false),
        ));
    }

    public function testValidate7() {
        $this->doTestValidate(array('1-directory'), array(
            array('title' => 'Pages', 'directory' => 'page', 'displayTab' => false),
            array('title' => 'Pages', 'directory' => 'page2', 'displayTab' => false)
        ), array('page2' => $this->createWidget('forum', 'PAGE2')));
    }

    public function testValidate8() {
        $this->doTestValidate(array(), array(
            array('title' => 'Pages', 'directory' => 'page', 'displayTab' => false),
            array('title' => 'Pages', 'directory' => 'page2', 'displayTab' => false)
        ), array('page3' => $this->createWidget('forum', 'PAGE3')));
    }

    private function doTestValidate($expectedErrorKeys, $data, $nonPageWidgets = array()) {
        $errorKeys = array();
        foreach (TestInstanceHelper::validateProper($data, $nonPageWidgets) as $i => $errors) {
            foreach (array_keys($errors) as $fieldName) {
                $errorKeys[] = $i . '-' . $fieldName;
            }
        }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode($expectedErrorKeys), $json->encode($errorKeys));
    }

    public function testPageWidgets1() {
        $this->assertEqual(array('page1', 'page2'), array_keys(TestInstanceHelper::pageWidgets(array($this->createWidget('page', 'page1'), $this->createWidget('page', 'page2'), $this->createWidget('forum', 'forum1'), $this->createWidget('forum', 'forum2')))));
    }

    public function testPageWidgets2() {
        $this->assertEqual(array('forum1', 'forum2'), array_keys(TestInstanceHelper::nonPageWidgets(array($this->createWidget('page', 'page1'), $this->createWidget('page', 'page2'), $this->createWidget('forum', 'forum1'), $this->createWidget('forum', 'forum2')))));
    }

    private function createWidget($root, $dir) {
        $widget = new stdClass;
        $widget->root = $root;
        $widget->dir = $dir;
        return $widget;
    }

    public function testDirectoriesToDelete() {
        $this->assertEqual(array('b'), TestInstanceHelper::directoriesToDelete(array(array('directory' => 'a'), array('directory' => 'c')), array('a', 'b')));
        $this->assertEqual(array('a', 'b'), TestInstanceHelper::directoriesToDelete(array(), array('a', 'b')));
        $this->assertEqual(array(), TestInstanceHelper::directoriesToDelete(array(array('directory' => 'a'), array('directory' => 'b')), array()));
    }

}

class TestInstanceHelper extends Page_InstanceHelper {
    public static function validateProper($data, $nonPageWidgets) {
        return parent::validateProper($data, $nonPageWidgets);
    }
    public static function pageWidgets($widgets) {
        return parent::pageWidgets($widgets);
    }
    public static function nonPageWidgets($widgets) {
        return parent::nonPageWidgets($widgets);
    }
    public static function directoriesToDelete($data, $existingPageDirectories) {
        return parent::directoriesToDelete($data, $existingPageDirectories);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';


