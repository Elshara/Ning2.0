<?php

/**
 * Helps with the tracking of page hits.
 */
class Video_TrackingHelper {

    public static function insertHeader() {
        $names = XG_App::getRequestedRoute();
        $trackingSubsection = $names['controllerName'] . '/' . $names['actionName'];
        // ts = tracking subsection, tst = tracking subsection time; the latter is
        // to prevent multiple hits resulting from page refreshes  [Jon Aquino 2006-09-11]
        if (in_array($_GET['ts'], array('Uploaded Video', 'Embedded Video')) && time() - $_GET['tst'] < 5) {
            $trackingSubsection = $_GET['ts'];
        }
        if ($_GET['test_tracking_subsection']) { echo("XN-Tracking-Subsection: $trackingSubsection"); }
        header("XN-Tracking-Subsection: $trackingSubsection");
    }

}
