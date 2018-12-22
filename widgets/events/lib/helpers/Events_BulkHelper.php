<?php

/**
 * Useful functions for working with bulk operations.
 * Call EventWidget::init() before using the functions in this class.
 */
class Events_BulkHelper {

    /**
     * Deletes EventAttendee objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $screenName string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function removeEventAttendees($limit, $screenName) {
        $query = self::_eventHelper()->query('EventAttendee')->filter('my->screenName', '=', $screenName)->end($limit)->order('createdDate', 'asc', XN_Attribute::DATE);
        $changed = 0;
        foreach ($query->execute() as $eventAttendee) {
            self::_instance()->_deleteEventAttendee($eventAttendee);
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

    /**
     * Deletes Event objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $screenName string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function removeEvents($limit, $screenName) {
        $query = self::_eventHelper()->query('Event')->filter('contributorName', '=', $screenName)->end($limit)->order('createdDate', 'asc', XN_Attribute::DATE);
        $changed = 0;
        foreach ($query->execute() as $event) {
            self::_instance()->_deleteEvent($event);
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }

    /**
     *  Removes attendee from an Event.
     *
     *  @param  $eventAttendee  XN_Content  The EventAttendee
     */
    public static function _deleteEventAttendee($eventAttendee) {
        EventAttendee::deleteProper(W_Content::create($eventAttendee));
    }

    /**
     *  Removes the event
     *
     *  @param  $event          XN_Content  The Event
     */
    public static function _deleteEvent($event) {
        Event::delete(W_Content::create($event));
    }

    /** Singleton instance of this class. */
    protected static $instance;

    /** Useful functions related to Events. */
    protected static $eventHelper;

    /**
     *  Returns the singleton instance of this class.
     *
     *  @return Events_BulkHelper   the BulkHelper, or a mock object for testing
     */
    private function _instance() {
        if (! self::$instance) { self::$instance = new Events_BulkHelper(); }
        return self::$instance;
    }

    /**
     *  Returns a helper containing useful functions related to Events.
     *
     *  @return Events_EventHelper   the EventHelper, or a mock object for testing
     */
    private function _eventHelper() {
        if (! self::$eventHelper) { self::$eventHelper = new Events_EventHelper(); }
        return self::$eventHelper;
    }

}
