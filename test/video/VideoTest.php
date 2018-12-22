<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/models/Video.php');

class VideoTest extends UnitTestCase {

    public function testDateToString() {
        $this->assertEqual('05MAR1977', Video::dateToString(strtotime('1977-03-05T01:00Z')));
        $this->assertEqual('05MAR1977', Video::dateToString(strtotime('1977-03-05T23:00Z')));
    }

    public function testSetDailyViewCountsForLastMonth() {
        $video = W_Content::create('Video');
        $video->setDailyViewCountsForLastMonth(array());
        $this->assertEqual('', $video->my->dailyViewCountsForLastMonth);
        $video->setDailyViewCountsForLastMonth(array('05MAR1977' => 5, '10APR1977' => 10));
        $this->assertEqual('05MAR1977 5, 10APR1977 10', $video->my->dailyViewCountsForLastMonth);
         $video->my->dailyViewCountsForLastMonth = '05MAR1977 15, 10APR1977 20';
         $dailyViewCountsForLastMonth = $video->getDailyViewCountsForLastMonth();
         $this->assertEqual(2, count($dailyViewCountsForLastMonth));
         $this->assertEqual(15, $dailyViewCountsForLastMonth['05MAR1977']);
         $this->assertEqual(20, $dailyViewCountsForLastMonth['10APR1977']);
    }

    public function testIncrementViewCount() {
        $video = W_Content::create('Video');
        $video->my->viewCount = 0;
        $video->incrementViewCount();
        $this->assertEqual(1, $video->my->viewCount);
        $this->assertTrue(2 > time() - strtotime($video->my->lastViewedOn));
    }

    public function testIncrementViewCount2() {
        $video = W_Content::create('Video');
        $video->my->viewCount = 0;
        $video->incrementViewCount(strtotime('1977-03-15T00:00Z'));
        $this->assertEqual('15MAR1977 1', $video->my->dailyViewCountsForLastMonth);
        $this->assertEqual(100*1 + 10*1 + 1, $video->my->popularityCount);
        $video->my->dailyViewCountsForLastMonth = '11FEB1977 5, 12FEB1977 10, 07MAR1977 15, 08MAR1977 20, 14MAR1977 25, 15MAR1977 30';
        $video->incrementViewCount(strtotime('1977-03-15T18:00Z'));
        $this->assertEqual('12FEB1977 10, 07MAR1977 15, 08MAR1977 20, 14MAR1977 25, 15MAR1977 31', $video->my->dailyViewCountsForLastMonth);
        $this->assertEqual(100*56 + 10*76 + 101, $video->my->popularityCount);
    }


}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
