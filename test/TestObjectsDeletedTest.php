<?php
require $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/models/Comment.php');

/**
 * Checks that no test objects remain.
 */
class TestObjectsDeletedTest extends UnitTestCase {

    public function test() {
        $testStartMarkers = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'TestStartMarker')->execute();
        $this->assertTrue(count($testStartMarkers) > 0, 'No TestStartMarker found - note that this test should be launched from AllUnitTests.php');
        // Ignore AvatarGridImage - one is created immediately after updating a Bazel [Jon Aquino 2007-06-23]
        $newObjects = XN_Query::create('Content')->filter('owner')->filter('createdDate', '>=', $testStartMarkers[0]->createdDate)->filter('type', '<>', 'TestStartMarker')->filter('type', '<>', 'AvatarGridImage')->execute();
        $this->assertEqual(0, count($newObjects));
        foreach ($newObjects as $newObject) { echo $newObject->debugHTML(); }
        foreach ($testStartMarkers as $testStartMarker) { XN_Content::delete($testStartMarker); }

        // Ensure no test objects appear in cache
        XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
