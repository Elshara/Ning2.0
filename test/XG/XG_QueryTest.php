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

class XG_QueryTest extends UnitTestCase {

    public function testCreate() {
        $query = XG_Query::create('Content');
        $this->assertIsA($query, XG_Query);
    }

    public function testBasicResults() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest1',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $query = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest1')
                ->filter('title','=',$title);
        $this->assertIsA($query, XG_Query);
        $results = $query->execute();
        $this->assertEqual($content->id, $results[0]->id);
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
        $query2 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest1')
                ->filter('title','=',$title);
        $this->assertIsA($query2, XG_Query);
        $results2 = $query2->execute();
        $this->assertEqual($content->id, $results2[0]->id);
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[yes\]/',$query2->debugHtml());
*/
        XN_Content::delete($content);
    }

    public function testMaxAge() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest2',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $query = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest2')
                ->filter('title','=',$title);
        $this->assertIsA($query, XG_Query);
        $results = $query->execute();
        $this->assertEqual($content->id, $results[0]->id);
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());

        $query2 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest2')
                ->maxAge(2)->filter('title','=',$title);
        $this->assertIsA($query2, XG_Query);
        sleep(5);
        $results2 = $query2->execute();
        $this->assertEqual($content->id, $results2[0]->id);
        $this->assertPattern('/cached\? \[no\]/',$query2->debugHtml());
        XN_Content::delete($content);
    }

    public function testFromXnQuery() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest3',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $xn_query = XN_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest3')
                ->filter('title','=',$title);
        $query = XG_Query::create($xn_query);
        $res = $query->execute();

        $query_2 = XG_Query::create($xn_query);
        $res_2 = $query_2->execute();


        $this->assertEqual($content->id, $res[0]->id);
        $this->assertEqual($content->id, $res_2[0]->id);
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
        $this->assertPattern('/cached\? \[yes\]/',$query_2->debugHtml());
*/
        XN_Content::delete($content);
    }

    public function testInvalidate() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest4',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $query = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res = $query->execute();

        XG_Query::invalidateCache($query);

        $query_2 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res_2 = $query_2->execute();

        $this->assertEqual($content->id, $res[0]->id);
        $this->assertEqual($content->id, $res_2[0]->id);
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
        $this->assertPattern('/cached\? \[no\]/',$query_2->debugHtml());
        XN_Content::delete($content);
    }

    public function testWithKey() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest4',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $query = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res = $query->execute();

        $query_2 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res_2 = $query_2->execute();

        XG_Query::invalidateCache('alice');

        $query_3 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res_3 = $query_3->execute();

        $this->assertEqual($content->id, $res[0]->id);
        $this->assertEqual($content->id, $res_2[0]->id);
        $this->assertEqual($content->id, $res_3[0]->id);
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
        $this->assertPattern('/cached\? \[yes\]/',$query_2->debugHtml());
        $this->assertPattern('/cached\? \[no\]/',$query_3->debugHtml());
*/
        XN_Content::delete($content);
    }

     public function testWithKeys() {
        $title = uniqid();
        $c = XN_Content::create('XGQueryTest4',$title,'bob');
        $c->save();
        $content = XN_Content::load($c);
        $query = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice','bob')->filter('title','=',$title);
        $res = $query->execute();

        $query_2 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice')->filter('title','=',$title);
        $res_2 = $query_2->execute();

        $query_3 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('bob')->filter('title','=',$title);
        $res_3 = $query_3->execute();

        $query_4 = XG_Query::create('Content')->filter('owner')->filter('type','eic','XGQueryTest4')
                ->setCaching('alice','bob')->filter('title','=',$title);
        $res_4 = $query_4->execute();

        $this->assertEqual($content->id, $res[0]->id);
        $this->assertEqual($content->id, $res_2[0]->id);
        $this->assertEqual($content->id, $res_3[0]->id);
        $this->assertEqual($content->id, $res_4[0]->id);
/* Query caching has been turned off [Jon Aquino 2007-10-22]
        $this->assertPattern('/cached\? \[no\]/',$query->debugHtml());
        $this->assertPattern('/cached\? \[yes\]/',$query_2->debugHtml());
        $this->assertPattern('/cached\? \[yes\]/',$query_3->debugHtml());
        $this->assertPattern('/cached\? \[yes\]/',$query_4->debugHtml());
*/
        XN_Content::delete($content);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
