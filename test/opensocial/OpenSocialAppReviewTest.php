<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocialAppReviewTest extends UnitTestCase {

    public function testCreate() {
        $url = 'http://example.com/testCreate';
        $review = OpenSocialAppReview::create($url, XN_Profile::current()->screenName, 1.0, 'This app is terrible');
        $review->save();
        $this->assertEqual($url, $review->my->appUrl);
        $this->assertEqual(1.0, $review->my->rating);
    }

    public function testLoadById() {
        $url = 'http://example.com/testCreate';
        $review = OpenSocialAppReview::create($url, XN_Profile::current()->screenName, 5.0, 'This app is great');
        $review->save();
        $review = OpenSocialAppReview::loadById($review->id);
        $this->assertEqual($url, $review->my->appUrl);
        $this->assertEqual(5.0, $review->my->rating);
    }

    public function testFind() {
        $url = 'http://example.com/testFind';
        for ($i = 1; $i <= 5; $i++) {
            $app = OpenSocialAppReview::create($url, 'Fred', $i, 'Medina');
            $app->save();
        }
        $reviewInfo = OpenSocialAppReview::find($url, 0, 6);
        $reviews = $reviewInfo['reviews'];
        $this->assertTrue($reviewInfo['numReviews'] >= 3);
        $this->assertEqual(5, count($reviews));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
