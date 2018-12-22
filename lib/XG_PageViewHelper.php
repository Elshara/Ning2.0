<?php

/**
 * Useful functions for working with page-view counts.
 */
class XG_PageViewHelper {

    /**
     * Increments the view count on the object.
     *
     * @param $object XN_Content|W_Content|W_Model  the content object to update
     * @param $save boolean  whether to save the object
     * @param $time used for unit testing
     */
    public function incrementViewCount($object, $save = true, $time = null) {
        // Convert XN_Content to W_Content so that attributes types are set correctly [Jon Aquino 2008-02-04]
        $object = $object instanceof XN_Content ? W_Content::create($object) : $object;
        $attributes = $object instanceof W_Model ? $object : $object->my;
        $time = $time ? $time : time();
        $attributes->viewCount = $attributes->viewCount + 1;
        $attributes->lastViewedOn = date('c', $time);
        $dailyViewCountsForLastMonth = self::getDailyViewCountsForLastMonth($object);
        $dailyViewCountsForLastMonth[self::dateToString($time)] += 1;
        $DAY = 24 * 3600;
        $viewCountForLastDay = 0;
        $viewCountForLastWeek = 0;
        $viewCountForLastMonth = 0;
        foreach ($dailyViewCountsForLastMonth as $dateString => $viewCount) {
            // Truncate the time to midnight so the last-day window is at least 24 hours [Jon Aquino 2006-07-22]
            $age = strtotime(self::dateToString($time) . ' GMT') - strtotime($dateString . ' GMT');
            if ($age <= 1 * $DAY) { $viewCountForLastDay = $viewCountForLastDay + $viewCount; }
            if ($age <= 7 * $DAY) { $viewCountForLastWeek = $viewCountForLastWeek + $viewCount; }
            if ($age <= 31 * $DAY) { $viewCountForLastMonth = $viewCountForLastMonth + $viewCount; }
            if ($age > 31 * $DAY) { unset($dailyViewCountsForLastMonth[$dateString]); }
        }
        self::setDailyViewCountsForLastMonth($object, $dailyViewCountsForLastMonth);
        $attributes->popularityCount = 100*$viewCountForLastDay + 10*$viewCountForLastWeek + $viewCountForLastMonth;
        if ($save) {
            // BAZ-1507: Don't invalidate cache here, or the cache gets blown away on each detail view
            XG_App::setInvalidateFromHooks(false);
            $object->save();
            XG_App::setInvalidateFromHooks(true);
        }
    }

    /**
     * Sets the view counts per day for the last month.
     *
     * @param $object XN_Content|W_Content|W_Model  the content object to update
     * @param $dailyViewCountsForLastMonth array  an array of date string => view count
     */
    public function setDailyViewCountsForLastMonth($object, $dailyViewCountsForLastMonth) {
        $attributes = $object instanceof W_Model ? $object : $object->my;
        $x = array();
        foreach ($dailyViewCountsForLastMonth as $dateString => $viewCount) {
            $x[] = $dateString . ' ' . $viewCount;
        }
        $attributes->dailyViewCountsForLastMonth = implode(', ', $x);
    }

    /**
     * Returns the view counts per day for the last month.
     *
     * @return array  an array of date string => view count
     */
    public function getDailyViewCountsForLastMonth($object) {
        $attributes = $object instanceof W_Model ? $object : $object->my;
        if (! $attributes->dailyViewCountsForLastMonth) { return array(); }
        $x = array();
        foreach (explode(',', $attributes->dailyViewCountsForLastMonth) as $dateStringAndViewCount) {
            list($dateString, $viewCount) = explode(' ', trim($dateStringAndViewCount));
            $x[$dateString] = $viewCount;
        }
        return $x;
    }

    /**
     * Converts a timestamp to a string.
     *
     * @param time integer  the timestamp to convert
     * @return string  the string representation
     */
    public static function dateToString($time) {
        return mb_strtoupper(gmdate('dMY', $time));
    }

}
