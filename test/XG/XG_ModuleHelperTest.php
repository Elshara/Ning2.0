<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class XG_ModuleHelperTest extends UnitTestCase {

    public function testModuleSortWrapper() {
        // rules:
        // no weight > any weight
        // ties resolved by key name
        $ordering = array('a' => 100, 'b' => 50, 'c' => 50);
        $array = array_flip(array('a','b','c','daab','daaa'));
        uksort($array, array(new XG_ModuleSortWrapper($ordering), 'moduleSortCmp'));
        $this->assertEqual(json_encode($array), '{"b":1,"c":2,"a":0,"daaa":4,"daab":3}');
    }

}

XG_App::includeFileOnce('/test/test_footer.php');
