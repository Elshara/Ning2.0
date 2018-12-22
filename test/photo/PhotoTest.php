<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/photo/models/Photo.php');

class PhotoTest extends UnitTestCase {

    public function testDateToString() {
        $this->assertEqual('05MAR1977', TestPhoto::dateToString(strtotime('1977-03-05T01:00Z')));
        $this->assertEqual('05MAR1977', TestPhoto::dateToString(strtotime('1977-03-05T23:00Z')));
    }

    public function testSetDailyViewCountsForLastMonth() {
        $photo = W_Content::create('Photo');
        $photo->setDailyViewCountsForLastMonth(array());
        $this->assertEqual('', $photo->my->dailyViewCountsForLastMonth);
        $photo->setDailyViewCountsForLastMonth(array('05MAR1977' => 5, '10APR1977' => 10));
        $this->assertEqual('05MAR1977 5, 10APR1977 10', $photo->my->dailyViewCountsForLastMonth);
         $photo->my->dailyViewCountsForLastMonth = '05MAR1977 15, 10APR1977 20';
         $dailyViewCountsForLastMonth = $photo->getDailyViewCountsForLastMonth();
         $this->assertEqual(2, count($dailyViewCountsForLastMonth));
         $this->assertEqual(15, $dailyViewCountsForLastMonth['05MAR1977']);
         $this->assertEqual(20, $dailyViewCountsForLastMonth['10APR1977']);
    }

    public function testIncrementViewCount() {
        $photo = W_Content::create('Photo');
        $photo->my->viewCount = 0;
        $photo->incrementViewCount();
        $this->assertEqual(1, $photo->my->viewCount);
        $this->assertTrue(2 > time() - strtotime($photo->my->lastViewedOn));
    }

    public function testIncrementViewCount2() {
        $photo = W_Content::create('Photo');
        $photo->my->viewCount = 0;
        $photo->incrementViewCount(strtotime('1977-03-15T00:00Z'));
        $this->assertEqual('15MAR1977 1', $photo->my->dailyViewCountsForLastMonth);
        $this->assertEqual(100*1 + 10*1 + 1, $photo->my->popularityCount);
        $photo->my->dailyViewCountsForLastMonth = '11FEB1977 5, 12FEB1977 10, 07MAR1977 15, 08MAR1977 20, 14MAR1977 25, 15MAR1977 30';
        $photo->incrementViewCount(strtotime('1977-03-15T18:00Z'));
        $this->assertEqual('12FEB1977 10, 07MAR1977 15, 08MAR1977 20, 14MAR1977 25, 15MAR1977 31', $photo->my->dailyViewCountsForLastMonth);
        $this->assertEqual(100*56 + 10*76 + 101, $photo->my->popularityCount);
    }

}

class TestPhoto extends Photo {
    public static function dateToString($dateObj) {
        return parent::dateToString($dateObj);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
