<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_UserSort.php');

class Profiles_UserSortTest extends UnitTestCase {

    public function testGetId() {
        $this->assertEqual('random', Profiles_UserSort::get('random')->getId());
        $this->assertEqual('alphabetical', Profiles_UserSort::get('alphabetical')->getId());
        $this->assertEqual('mostRecent', Profiles_UserSort::get('mostRecent')->getId());
    }

    public function testGetPageTitle() {
        $this->assertEqual('Random Members', Profiles_UserSort::get('random')->getPageTitle(5));
        $this->assertEqual('Members', Profiles_UserSort::get('alphabetical')->getPageTitle(5));
        $this->assertEqual('Recently Added', Profiles_UserSort::get('mostRecent')->getPageTitle(5));
    }

}

class TestRandomUserSort extends Profiles_RandomUserSort {
    private $randomValues;
    public $randArgs = array();
    public function __construct($randomValues) {
        $this->randomValues = $randomValues;
    }
    public function computeChunkSizeAndStartIndexes($n) {
        return parent::computeChunkSizeAndStartIndexes($n);
    }
    protected function rand($min, $max) {
        $this->randArgs[] = array($min, $max);
        return array_shift($this->randomValues);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



