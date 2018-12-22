<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_Filter.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');

class Forum_FilterTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testGet() {
        $this->assertEqual(0, count(Forum_Filter::$nameToFilterMap));
        $this->assertEqual('Most Recent', Forum_Filter::get('mostRecent')->getDisplayText('JonathanAquino'));
        $this->assertEqual(1, count(Forum_Filter::$nameToFilterMap));
        $this->assertEqual('Most Popular', Forum_Filter::get('mostPopularDiscussions')->getDisplayText('JonathanAquino'));
        $this->assertEqual(2, count(Forum_Filter::$nameToFilterMap));
        $this->assertEqual('Discussions Started', Forum_Filter::get('discussionsStarted')->getDisplayText(XN_Profile::current()->screenName));
        $this->assertEqual(3, count(Forum_Filter::$nameToFilterMap));
        $this->assertEqual('Discussions Started', Forum_Filter::get('discussionsStarted')->getDisplayText('JonathanAquino'));
        $this->assertEqual(3, count(Forum_Filter::$nameToFilterMap));
        $this->assertEqual('Discussions I\'ve Replied To', Forum_Filter::get('discussionsAddedTo')->getDisplayText(XN_Profile::current()->screenName));
        $this->assertEqual(4, count(Forum_Filter::$nameToFilterMap));
    }

    public function testMostRecent() {
        list(, $topic) = XG_TestHelper::createTopic();
        XG_TestHelper::createComment($topic);
        $types = array();
        foreach (Forum_Filter::get('mostRecentDiscussions')->execute(XN_Query::create('Content')->end(5)->filter('my.test', '=', 'Y'), NULL) as $x) {
            $types[] = $x->type;
        }
        $this->assertEqual('Topic', implode(', ', $types));

        $types = array();
        $filter = Forum_Filter::get('mostRecent');
        foreach ($filter->execute(XN_Query::create('Content')->end(5)->filter('my.test', '=', 'Y'), NULL) as $x) {
            $types[] = $x->type;
        }
        $this->assertEqual('Comment, Topic', implode(', ', $types));
    }

    public function testMostPopularDiscussions() {
        list(, $topic) = XG_TestHelper::createTopic();
        $this->assertIdentical(0, $topic->my->xg_forum_commentCount);
        $topic->my->set('xg_forum_commentCount', NULL, XN_Attribute::NUMBER);
        $topic->save();
        list(, $topic) = XG_TestHelper::createTopic();
        $topic->my->set('xg_forum_commentCount', 0, XN_Attribute::NUMBER);
        $topic->save();
        list(, $topic) = XG_TestHelper::createTopic();
        $topic->my->set('xg_forum_commentCount', 5, XN_Attribute::NUMBER);
        $topic->save();
        list(, $topic) = XG_TestHelper::createTopic();
        $topic->my->set('xg_forum_commentCount', 3, XN_Attribute::NUMBER);
        $topic->save();
        list(, $topic) = XG_TestHelper::createTopic();
        $topic->my->set('xg_forum_commentCount', 10, XN_Attribute::NUMBER);
        $topic->save();
        $commentCounts = array();
        foreach (Forum_Filter::get('mostPopularDiscussions')->execute(XN_Query::create('Content')->end(5)->filter('my.test', '=', 'Y'), NULL) as $topic) {
            $commentCounts[] = $topic->my->raw('xg_forum_commentCount');
        }
        $this->assertEqual('10, 5, 3, 0', implode(', ', $commentCounts));
    }

    public function testPromoted() {
        list(, $topic1) = XG_TestHelper::createTopic('topic1');
        $topic1->save();
        list(, $topic2) = XG_TestHelper::createTopic('topic2');
        $topic2->save();
        list(, $topic3) = XG_TestHelper::createTopic('topic3');
        $topic3->save();
        list(, $topic4) = XG_TestHelper::createTopic('topic4');
        $topic4->save();
        list(, $topic5) = XG_TestHelper::createTopic('topic5');
        $topic5->save();
        XG_PromotionHelper::promote(W_Content::unwrap($topic3));
        $topic3->save();
        sleep(1);
        XG_PromotionHelper::promote(W_Content::unwrap($topic1));
        $topic1->save();
        sleep(1);
        XG_PromotionHelper::promote(W_Content::unwrap($topic5));
        $topic5->save();
        $titles = array();
        foreach (Forum_Filter::get('promoted')->execute(XN_Query::create('Content')->end(5)->filter('my.test', '=', 'Y'), NULL) as $topic) {
            $titles[] = $topic->title;
        }
        $this->assertEqual('topic5, topic1, topic3', implode(', ', $titles));
        $this->assertEqual(xg_text('FEATURED'), Forum_Filter::get('promoted')->getDisplayText('JonathanAquino'));
    }

    public function testNeedsInvalidationKeys() {
        $this->assertFalse(TestFilter::needsInvalidationKeys(XN_Query::create('Content')));
        $this->assertTrue(TestFilter::needsInvalidationKeys(XG_Query::create('Content')));
        $this->assertFalse(TestFilter::needsInvalidationKeys(XG_Query::create('Content')->addCaching(XG_Cache::key('type', 'Topic'))));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

abstract class TestFilter extends Forum_Filter {
    public static function needsInvalidationKeys($query) {
        return parent::needsInvalidationKeys($query);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
