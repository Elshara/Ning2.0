<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Query.php');

XN_Debug::allowDebug();

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(true);
    XG_Cache::clearStatistics();
}

class XG_Query6Test extends UnitTestCase {

    public function testBaz4090() {
        // First create some objects that are sooo big that a query
        // for all of them will be toooo big to cache
        $cacheMax = 1024 * 1024;
        $objectCount = 10;
        $totalSize = intval($cacheMax * 1.1);
        $perObject = ceil($totalSize / $objectCount);
        $s = str_repeat(md5(uniqid()), ceil($perObject / 32));
        for ($i = 0; $i < $objectCount; $i++) {
            $c = XN_Content::create('TestBaz4090');
            XG_TestHelper::markAsTestObject($c);
            $c->my->guts = $s;
            $c->save();
        }
        $q = XG_Query::create('Content')
                ->filter('owner')
                ->filter('type','eic','TestBaz4090')
                ->end($objectCount);
        $r = $q->execute();
        $this->assertTrue($objectCount, count($r));
    }

    public function testBaz5745() {
        $c = XN_Content::create('TestBaz5745');
        $s = md5(uniqid());
        XG_TestHelper::markAsTestObject($c);
        $c->title = $s;
        $c->save();

        /* Long enough so the query succeeds on its own, but not 
         * without the additional padding of a cache get */
        $longString = str_repeat('x',3915);

        // Regular ol' cached query works fine
        $q1 = XG_Query::create('Content')
            ->filter('owner')
            ->filter('type','eic','TestBaz5745')
            ->filter('title','=', $s);
        $d = $q1->uniqueResult();
        $this->assertEqual($c->id, $d->id);
        $this->assertPattern('/cached\? \[no\]/',$q1->debugHtml());

        // Regular ol' cached query works fine -- from the cache
        $q2 = XG_Query::create('Content')
            ->filter('owner')
            ->filter('type','eic','TestBaz5745')
            ->filter('title','=', $s);
        $e = $q2->uniqueResult();
        $this->assertEqual($c->id, $e->id);
        $this->assertPattern('/cached\? \[yes\]/',$q2->debugHtml());

        // Cached query with super-long URL works fine
        $q3 = XG_Query::create('Content')
            ->filter('owner')
            ->filter('type','eic','TestBaz5745')
            ->filter('title','=', $s)
            ->filter('description','!=',$longString);
        $f = $q3->uniqueResult();
        $this->assertEqual($c->id, $f->id);
        $this->assertPattern('/cached\? \[no\]/',$q3->debugHtml());

        // Cached query with super-long URL works fine -- from the cache
        $q4 = XG_Query::create('Content')
            ->filter('owner')
            ->filter('type','eic','TestBaz5745')
            ->filter('title','=', $s)
            ->filter('description','!=',$longString);
        $g = $q4->uniqueResult();
        $this->assertEqual($c->id, $g->id);
        $this->assertPattern('/cached\? \[yes\]/',$q4->debugHtml());


    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
