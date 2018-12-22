<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');

XN_Debug::allowDebug();

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(true);
    XG_Cache::clearStatistics();
}

class XG_PrivacyHelperTest extends UnitTestCase {

    public function testSetPrivacyAndGetIds() {
        XG_TestHelper::setCurrentWidget('music');
        $ids = array();
        $friendlyTrack = Track::create();
        $friendlyTrack->isPrivate = true;
        $friendlyTrack->my->enableProfileUsage = 'on';
        $friendlyTrack->save();
        $ids[] = $friendlyTrack->id;
        $unfriendlyTrack = Track::create();
        $unfriendlyTrack->isPrivate = true;
        $unfriendlyTrack->my->enableProfileUsage = 'off';
        $unfriendlyTrack->save();
        $ids[] = $unfriendlyTrack->id;
        $this->assertEqual(true, $friendlyTrack->isPrivate);
        $this->assertEqual(true, $unfriendlyTrack->isPrivate);

        $changedTracks = XG_PrivacyHelper::setPrivacyAndGetIds(30, false, 'music', 'Track', 'my->enableProfileUsage', 'on');
        $this->assertEqual(false, $friendlyTrack->isPrivate);
        $this->assertEqual(true, $unfriendlyTrack->isPrivate);
        $this->assertTrue(in_array($ids[0], $changedTracks));
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

XG_App::includeFileOnce('/test/test_footer.php');

if ($_GET['stats']) {
    XG_Cache::setGatherStatistics(false);
}
