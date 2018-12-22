<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_Filter.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');

class Groups_FilterTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('groups');
    }

    public function testGet() {
        $this->assertEqual(0, count(Groups_Filter::$nameToFilterMap));
        $this->assertEqual('Latest', Groups_Filter::get('mostRecent')->getDisplayText('JonathanAquino'));
        $this->assertEqual(1, count(Groups_Filter::$nameToFilterMap));
        $this->assertEqual('Most Active', Groups_Filter::get('mostActive')->getDisplayText('JonathanAquino'));
        $this->assertEqual(2, count(Groups_Filter::$nameToFilterMap));
        $this->assertEqual('Featured', Groups_Filter::get('promoted')->getDisplayText(XN_Profile::current()->screenName));
        $this->assertEqual(3, count(Groups_Filter::$nameToFilterMap));
        $this->assertEqual('Featured', Groups_Filter::get('promoted')->getDisplayText('JonathanAquino'));
        $this->assertEqual(3, count(Groups_Filter::$nameToFilterMap));
    }

    public function testModerated() {
        $group = Group::create('groupA');
        $group->my->approved = 'N';
        $group->save();

        $group = Group::create('groupB');
        $group->my->approved = 'N';
        $group->save();

        $group = Group::create('groupC');
        $group->save();

        $group = Group::create('groupD');
        $group->my->approved = 'Y';
        $group->save();

        $titles = array();
        foreach (Groups_Filter::get('moderation')->execute(XN_Query::create('Content')->end(5), NULL) as $group) {
            $titles[] = $group->title;
        }
        $this->assertEqual('groupA, groupB', implode(', ', $titles));
    }

    public function testMostRecent() {
        Group::create('a')->save();
        sleep(1);
        Group::create('b')->save();
        $titles = array();
        foreach (Groups_Filter::get('mostRecent')->execute(XN_Query::create('Content')->end(5), NULL) as $x) {
            $titles[] = $x->title;
        }
        $this->assertEqual('b, a', implode(', ', $titles));
    }

    public function testMostPopular() {
        $group = Group::create('b');
        $this->assertIdentical(0, $group->my->memberCount);
        $group->my->set('memberCount', 0, XN_Attribute::NUMBER);
        $group->save();

        $group = Group::create('c');
        $group->my->set('memberCount', 5, XN_Attribute::NUMBER);
        $group->save();

        $group = Group::create('d');
        $group->my->set('memberCount', 3, XN_Attribute::NUMBER);
        $group->save();

        $group = Group::create('e');
        $group->my->set('memberCount', 10, XN_Attribute::NUMBER);
        $group->save();

        $memberCounts = array();
        foreach (Groups_Filter::get('mostPopular')->execute(XN_Query::create('Content')->end(5), NULL) as $topic) {
            $memberCounts[] = $topic->my->raw('memberCount');
        }
        $this->assertEqual('10, 5, 3, 0', implode(', ', $memberCounts));
    }

    public function testPromoted() {
        $group1 = Group::create('group1');
        $group1->save();
        $group2 = Group::create('group2');
        $group2->save();
        $group3 = Group::create('group3');
        $group3->save();
        $group4 = Group::create('group4');
        $group4->save();
        $group5 = Group::create('group5');
        $group5->save();
        XG_PromotionHelper::promote(W_Content::unwrap($group3));
        $group3->save();
        sleep(1);
        XG_PromotionHelper::promote(W_Content::unwrap($group1));
        $group1->save();
        sleep(1);
        XG_PromotionHelper::promote(W_Content::unwrap($group5));
        $group5->save();
        $titles = array();
        foreach (Groups_Filter::get('promoted')->execute(XN_Query::create('Content')->end(5), NULL) as $group) {
            $titles[] = $group->title;
        }
        $this->assertEqual('group5, group1, group3', implode(', ', $titles));
        $this->assertEqual(xg_text('FEATURED'), Groups_Filter::get('promoted')->getDisplayText('JonathanAquino'));
    }

    public function testJoined() {
        $groupA = Group::create('a');
        $groupA->save();
        $groupB = Group::create('b');
        $groupB->save();
        $this->assertEqual(0, count(Groups_Filter::get('joined')->execute(XN_Query::create('Content')->end(5), XN_Profile::current()->screenName)));

        Group::setStatus($groupB, XN_Profile::current()->screenName, 'banned');
        $this->assertEqual(0, $groupB->my->memberCount);
        $this->assertEqual(0, count(Groups_Filter::get('joined')->execute(XN_Query::create('Content')->end(5), XN_Profile::current()->screenName)));

        Group::setStatus($groupB, XN_Profile::current()->screenName, 'member');
        $this->assertEqual(1, $groupB->my->memberCount);
        $this->assertEqual(1, count($groups = Groups_Filter::get('joined')->execute(XN_Query::create('Content')->end(5), XN_Profile::current()->screenName)));
        $this->assertEqual('b', reset($groups)->title);

        Group::setStatus($groupB, XN_Profile::current()->screenName, 'admin');
        $this->assertEqual(1, $groupB->my->memberCount);
        $this->assertEqual(1, count(Groups_Filter::get('joined')->execute(XN_Query::create('Content')->end(5), XN_Profile::current()->screenName)));

        Group::setStatus($groupB, XN_Profile::current()->screenName, 'banned');
        $this->assertEqual(0, $groupB->my->memberCount);
        $this->assertEqual(0, count(Groups_Filter::get('joined')->execute(XN_Query::create('Content')->end(5), XN_Profile::current()->screenName)));
    }

    public function testNeedsInvalidationKeys() {
        $this->assertFalse(TestFilter::needsInvalidationKeys(XN_Query::create('Content')));
        $this->assertTrue(TestFilter::needsInvalidationKeys(XG_Query::create('Content')));
        $this->assertFalse(TestFilter::needsInvalidationKeys(XG_Query::create('Content')->addCaching(XG_Cache::key('type', 'Group'))));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

abstract class TestFilter extends Groups_Filter {
    public static function needsInvalidationKeys($query) {
        return parent::needsInvalidationKeys($query);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
