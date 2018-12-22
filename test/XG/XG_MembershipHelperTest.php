<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');

class XG_MembershipHelperTest extends UnitTestCase {

    public function testUseOwnerInsteadOfCreator() {
        // XG_MembershipHelper::CREATOR has been replaced by XG_MembershipHelper::OWNER [Jon Aquino 2008-01-25]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $contents = file_get_contents($file);
            $this->assertTrue(strpos($contents, 'XG_MembershipHelper::CREATOR') === false, $file);
        }
    }
    
    // See the comments in BAZ-6001 and NING-5231 for the reasoning behind this test.  If it fails it means NING-5231 is fixed
    // and we must accomodate that in memberStatus sort.  [Thomas David Baker 2008-02-07]
    public function testNullSortOrder() {
        $thing1 = XN_Content::create('TestThing');
        $thing1->my->text = 'hi';
        $thing1->my->status = 10;
        $thing1->save();
        $thing2 = XN_Content::create('TestThing');
        $thing2->my->text = 'hello';
        $thing2->save();
        $thing3 = XN_Content::create('TestThing');
        $thing3->my->text = 'goodbye';
        $thing3->my->status = 50;
        $thing3->save();
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'TestThing')->order('my->status', 'asc', XN_Attribute::NUMBER);
        $results = $query->execute();
        $this->assertEqual('hi', $results[0]->my->text);
        $this->assertEqual('goodbye', $results[1]->my->text);
        $this->assertEqual('hello', $results[2]->my->text);
        $this->assertEqual(10, $results[0]->my->status);
        $this->assertEqual(50, $results[1]->my->status);
        $this->assertEqual(null, $results[2]->my->status);
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'TestThing')->order('my->status', 'desc', XN_Attribute::NUMBER);
        $results = $query->execute();
        $this->assertEqual(null, $results[0]->my->status);
        $this->assertEqual(50, $results[1]->my->status);
        $this->assertEqual(10, $results[2]->my->status);
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
