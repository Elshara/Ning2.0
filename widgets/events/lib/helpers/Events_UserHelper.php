<?php

XG_App::includeFileOnce('/lib/XG_PagingList.php');
XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_EventHelper.php');

/**
 * Common code for saving and querying User objects.
 */
class Events_UserHelper {

    /**
     *  gets the value for the named attribute for the user.
     *
     * @param $user XN_Content | W_Content
     * @param $name string The attribute name
     * @return the value of the attribute
     */
    public static function get($user, $name) {
        return $user->my->raw(XG_App::widgetAttributeName(W_Cache::getWidget('events'),$name));
    }

    /**
     *  sets the value for the named attribute for the user.
     *
     * @param $user XN_Content | W_Content
     * @param $name string The attribute name
     * @param $value  The attribute value
     * @param $type   The attribute type; defaults to string
     * @return void
     */
    public static function set($user, $name, $value, $type = XN_Attribute::STRING) {
        $user->my->set(XG_App::widgetAttributeName(W_Cache::getWidget('events'),$name), $value, $type);
    }


    /**
     *  determines if the nominated user has any upcoming events.
     *
     * @param $user XN_Content | W_Content
     * @return numeric; the number of upcoming events
     */
    public static function determineUpcomingEventCount($user) {
        $eventCount = self::get($user,'eventCount');
        $nextEventEndDate = self::get($user,'nextEventEndDate');
        if (mb_strlen($eventCount) && $eventCount == 0) {
            return 0;
        } elseif ($nextEventEndDate > Events_EventHelper::dateToStr(null, false)) {
            return $eventCount;
        } else {
            XG_App::includeFileOnce('/lib/XG_LockHelper.php');
            if (!XG_LockHelper::lock('determine-upcoming-event-count-' . $user->title, 0)) { return $eventCount ? $eventCount : 0; }
            self::updateEventCount($user);
            return self::get($user,'eventCount');
        }
    }

    /**
     *  calculates a user's upcoming event count and nextEventEndDate and updates their user object accordingly
     *
     * @param $user XN_Content | W_Content
     * @param $saveUser boolean; option flag to save the user object; defaults to true
     * @param $extraEvent XN_Content | W_Content 	Extra event to count (used when user is not yet added to the event)
     * @return void
     */
    public static function updateEventCount($user, $saveUser = true, $extraEvent = NULL) {
        EventWidget::init();
        $events = EventAttendee::getUpcomingEvents($user->contributorName, 1, true);
        if ($events->totalCount) {
            $nextEvent = $events->getList();
            $nextEvent = $nextEvent[0];
            $eventCount = $events->totalCount;
            $endDate = $nextEvent->my->endDate;
        } else {
            $eventCount = 0;
            $endDate = null;
        }
        if ($extraEvent && Events_EventHelper::dateToTs($extraEvent->my->endDate) > time()) {
            $eventCount++;
            if (!$endDate || strcmp($extraEvent->my->endDate, $endDate) < 0) {
                $endDate = $extraEvent->my->endDate;
            }
        }
        self::set($user, 'eventCount', $eventCount, XN_Attribute::NUMBER);
        self::set($user, 'nextEventEndDate', $endDate);
        if ($saveUser) {
            $user->save();
        }
    }


}