<?php

/**
 * Useful functions for authorizing access to pages and other resources.
 */
class Events_SecurityHelper {

    /**
     * Returns whether a feed is available for comments on the given event.
     *
     * @param   $event  XN_Content|W_Content    The Event
     * @return boolean  Whether permission is granted
     */
    public static function commentFeedAvailable($event) {
        return $event->my->privacy == Event::ANYONE;
    }

    /**
     * Returns whether the current user can invite someone to the specified event.
     *
     * @param XN_Content|W_Content event the Event to invite people to
     * @param $status integer  attendance status, or null to query it
     * @return whether the current user is allowed to invite people to the event
     */
    public static function currentUserCanSendInvites($event, $status = null) {
        return self::currentUserCanSendInvitesProper(
                XG_App::canSendInvites(XN_Profile::current()),
                $status ? $status : EventAttendee::getStatuses(XN_Profile::current()->screenName, $event),
                $event->my->isClosed,
                XN_Profile::current()->screenName == $event->contributorName);
    }

    /**
     * Returns whether the current user can remove people from the specified event.
     *
     * @param XN_Content|W_Content event the Event to invite people to
     * @return whether the current user is allowed to invite people to the event
     */
    public static function currentUserCanDeleteAttendees($event) {
        if (! XN_Profile::current()->isLoggedIn()) { return false; }
        return XN_Profile::current()->screenName == $event->contributorName || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user can invite someone to the specified event.
     *
     * @param $canSendNetworkInvites boolean  whether the current user can invite people to join the network
     * @param $status integer  attendance status, e.g., MIGHT_ATTEND
     * @param $eventIsClosed boolean  whether the event is closed to new attendees.
     * @param $isEventCreator boolean  whether the current user created the event
     * @return whether the current user is allowed to invite people to the event
     */
    protected static function currentUserCanSendInvitesProper($canSendNetworkInvites, $status, $eventIsClosed, $isEventCreator) {
        if ($isEventCreator) { return true; }
        return $canSendNetworkInvites && ! $eventIsClosed && $status != EventAttendee::NOT_INVITED;
    }

    /**
     * Returns whether the current user is allowed to view details about the event.
     *
     * @param $event  XN_Content|W_Content  The Event object
     * @param $status integer  attendance status, e.g., MIGHT_ATTEND
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAccessEventDetails($event, $status) {
        if ($event->my->privacy == Event::ANYONE) { return true; }
        if (XN_Profile::current()->screenName == $event->contributorName) { return true; }
        return $status != EventAttendee::NOT_INVITED;
    }

    /**
     * Returns whether the current user is allowed to create a event
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanCreateEvent() {
        return XG_App::membersCanCreateEvents() || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to edit the event.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditEvent($event) {
        return XN_Profile::current()->screenName == $event->contributorName;
    }
    
    /**
     * Returns whether the current user is allowed to edit the location and event type
     * we don't show this control to the event creator, so we return false where that's the case.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanEditLocationEventType($event) {
        return XG_SecurityHelper::userIsAdmin() &&
                    (XN_Profile::current()->screenName != $event->contributorName);
    }

    /**
     * Returns whether the current user is allowed to delete the event.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanDeleteEvent($event) {
        return XN_Profile::current()->screenName == $event->contributorName || XG_SecurityHelper::userIsAdmin();
    }

    /**
     * Returns whether the current user is allowed to send a message
     * to event attendees.
     *
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanBroadcastMessage($event) {
        return XN_Profile::current()->screenName == $event->contributorName && !$event->my->disableRsvp;
    }

    /**
     * Returns whether the current user is allowed to add a comment to the event.
     *
     * @param $status integer  attendance status, e.g., MIGHT_ATTEND
     * @param $event  XN_Content|W_Content  The Event object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanAddComment($status, $event) {
        // If you change this policy, check whether the $htmlIfCannotAddComment conditions
        // also need to change in fragment_eventComments.php  [Jon Aquino 2008-03-31]

        // currently we allow commenting only invited people and for events with disableRsvp.
        return $status != EventAttendee::NOT_INVITED || $event->my->my->disableRsvp;
    }

    /**
     * Returns whether the current user is allowed to see the events that
     * the user has been invited to or has RSVPed to.
     *
     * @param $event  XN_Content|W_Content  The Event object
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanSeeUserEvents($screenName) {
        $current = XN_Profile::current();
        if ($current->screenName == $screenName) {
            return true;
        }
        $user = User::load($screenName);
        switch ($user->my->viewEventsPermission) {
            case NULL:
            case 'all': 	return true;
            case 'friends': return XG_UserHelper::isFriend($current, $screenName);
            default: 		return false;
        }
    }
}
