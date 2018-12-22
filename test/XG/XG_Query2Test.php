<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Query.php');

XN_Debug::allowDebug();

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(true);
    XG_Cache::clearStatistics();
}

/** This stub class exposes protected XG_Query info so it can be tested */
class XG_QueryTestStub extends XG_Query {
    public static function estimateSerializedSize($arg) { return parent::estimateSerializedSize($arg); }
    public static function estimateSerializedContentSize($arg) { return parent::estimateSerializedContentSize($arg); }
    public static function setMaxResultSerializedSize($size = null) { parent::setMaxResultSerializedSize($size); }
    public static function getMaxResultSerializedSize() { return parent::getMaxResultSerializedSize(); }
    public static function getSerializedSizeMemoryRatio() { return parent::getSerializedSizeMemoryRatio(); }
}

class XG_Query2Test extends UnitTestCase {

    public function testGetMaxAge() {
        $query = XG_Query::create('Content');
        $age = 126;
        $query->maxAge($age);
        $this->assertEqual($query->getMaxAge(), $age);
        $query_2 = XG_Query::create('Content');
        $this->assertIdentical($query_2->getMaxAge(), null);
    }

    public function testGetAndAddCaching() {
        $query = XG_Query::create('Content');
        $query->setCaching('alice');
        $this->assertEqual($query->getCaching(), array('alice'));
        $query->setCaching('bob');
        $this->assertEqual($query->getCaching(), array('bob'));
        $query->addCaching('charlie');
        $this->assertEqual($query->getCaching(), array('bob','charlie'));
        $query->addCaching('david','edgar');
        $this->assertEqual($query->getCaching(), array('bob','charlie','david','edgar'));
        $query->setCaching('frank','george');
        $this->assertEqual($query->getCaching(), array('frank','george'));
    }

    public function testResultFromToSize() {
        $title1 = uniqid();
        $title2 = uniqid();
        $c1 = XN_Content::create('XGQueryTest6',$title1,'alice');
        $c1->save();
        $d1 = XN_Content::load($c1);
        $c2 = XN_Content::create('XGQueryTest6',$title2,'bob');
        $c2->save();
        $d2 = XN_Content::load($c2);
        $query1 = XG_Query::create('Content')->filter('owner')->filter('title','in',array($title1,$title2))->order('title','asc');
        $res1 = $query1->execute();
        $this->assertPattern('/cached\? \[no\]/',$query1->debugHtml());
        $this->assertEqual($res1[0]->id, $d1->id);
        $this->assertEqual($res1[1]->id, $d2->id);
        $this->assertEqual($query1->getResultFrom(), 0);
        $this->assertEqual($query1->getResultTo(), 2);
        $this->assertEqual($query1->getResultSize(), 2);
        $this->assertEqual($query1->getTotalCount(), 2);

        $query2 = XG_Query::create('Content')->filter('owner')->filter('title','in',array($title1,$title2))->order('title','asc');
        $res2 = $query2->execute();
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[yes\]/',$query2->debugHtml());
*/
        $this->assertEqual($res2[0]->id, $d1->id);
        $this->assertEqual($res2[1]->id, $d2->id);
        $this->assertEqual($query2->getResultFrom(), 0);
        $this->assertEqual($query2->getResultTo(), 2);
        $this->assertEqual($query2->getResultSize(), 2);
        $this->assertEqual($query2->getTotalCount(), 2);

        XN_Content::delete($d1);
        XN_Content::delete($d2);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
