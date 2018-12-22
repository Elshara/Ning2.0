<?php

/**
 * Sends an email to selected attendees of an event.
 * Call EventWidget::init() before using the functions in this class.
 */
class Events_BroadcastHelper {

    /**
     *  Returns whether the user has opted into event broadcasts.
     *
     *  @param  XN_Content|W_Content|string $user  the User object or screen name
     *  @return boolean  Whether they have chosen to receive event-broadcast email
     */
    public static function acceptingBroadcasts($user) {
        if (is_string($user)) { $user = User::load($user); }
        if ($user->my->emailEventBroadcastPref) {
            return $user->my->emailEventBroadcastPref != 'N';
        } else {
            return $user->my->emailNewMessagePref != 'N';
        }
    }

    /** Singleton instance of this class. */
    protected static $i;

    /** Utility functions for working with event invitations. */
    protected static $invitationHelper;

    /** Useful functions related to Events. */
    protected static $eventHelper;

    /** Useful functions related to XN_Job. */
    protected static $jobHelper;

    /**
     *  Initializes the static variables.
     */
    public static function init() {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        self::$i = new Events_BroadcastHelper();
        self::$invitationHelper = new Events_InvitationHelper();
        self::$eventHelper = new Events_EventHelper();
        self::$jobHelper = new XG_JobHelper();
    }

    /**
     * Send a message to event attendees.
     *
     *  @param    $message          string      Text of the message
     *  @param    $event            W_Content   The Event
     *  @param    $attending        integer     Whether to send to people attending the event
     *  @param    $mightAttend      integer     Whether to send to people who are unsure about whether to attend
     *  @param    $notAttending     integer     Whether to send to people who have decided not to attend
     *  @param    $notRsvped        integer     Whether to send to people who have been invited but have not yet RSVPed
     */
    public static function broadcast($message, $event, $attending, $mightAttend, $notAttending, $notRsvped) {
        if ($attending) { self::$i->_send(self::_profileSetId($event->id, EventAttendee::ATTENDING) . '@lists', $event, $message); }
        if ($mightAttend) { self::$i->_send(self::_profileSetId($event->id, EventAttendee::MIGHT_ATTEND) . '@lists', $event, $message); }
        if ($notAttending) { self::$i->_send(self::_profileSetId($event->id, EventAttendee::NOT_ATTENDING) . '@lists', $event, $message); }
        if ($notRsvped) { self::$i->_broadcastToInvitees($message, $event->id); }
    }

    /**
     *  Sends a message to people invited to the event.
     *
     *  @param    $message          string      Text of the message
     *  @param    $eventId          string      Content ID of the Event
     */
    public static function _broadcastToInvitees($message, $eventId) {
        $invitations = self::$invitationHelper->getInvitations($eventId, self::$fgCount, true);
        if ($invitations->pageCount == 0) {
        } elseif ($invitations->pageCount == 1) {
            self::$i->_broadcastToInviteesProper($message, $eventId, $invitations);
        } else {
            $tasks = array();
            $cdate = self::$i->_time()+1;
            for ($i = $j = 0; $i < $invitations->totalCount; $i += self::$bgCount, $j++) {
                $tasks[] = array(array(__CLASS__,'task_broadcastToInvitees'), $j, $message, $eventId, $cdate);
            }
			self::$jobHelper->create($tasks);
        }
    }

    /**
     *  Sends a message to people invited to the event.
     *  Called as part of an async job.
     *
     *  @param      $page       int             Page number
     *  @param      $message    string          Text of the message
     *  @param      $eventId    string          Content ID of the Event
     *  @param      $cdate      int             Date of job/task creation
     */
    public static function task_broadcastToInvitees($page, $message, $eventId, $cdate) {
    	EventWidget::init();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
        $invitations = Index_InvitationHelper::metadataForInvitations(self::$i->_createInvitationQuery()
            ->filter('label', '=', Events_InvitationHelper::eventInvitationLabel($eventId))
            ->filter('createdDate', '<=', date('c', $cdate), XN_Attribute::DATE)
            ->order('createdDate', 'asc', XN_Attribute::DATE)
            ->begin($page * self::$bgCount)
            ->end(($page+1) * self::$bgCount)
            ->execute());
        self::$i->_broadcastToInviteesProper($message, $eventId, $invitations);
    }

    /**
     *  Sends a message to people invited to the event.
     *
     *  @param    $message          string      Text of the message
     *  @param    $eventId          string      Content ID of the Event
     *  @param    $invitations      array       Metadata for each invitation
     *  @see Index_InvitationHelper::metadataForInvitations
     */
    public static function _broadcastToInviteesProper($message, $eventId, $invitations) {
        $event = Event::byId($eventId);
        foreach ($invitations as $invitation) {
            self::_send($invitation['emailAddress'], $event, $message);
        }
    }

    /**
     * Sends the message.
     *
     *  @param    $to               string      Email address of the recipient
     *  @param    $event            W_Content   The Event
     *  @param    $message          string      Text of the message
     */
    public static function _send($to, $event, $message) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $subject = xg_text('A_MESSAGE_FROM_USERNAME_TO_EVENTNAME', xg_username(XN_Profile::current()), $event->title);
        $msg = new XG_Message_Event_Broadcast($subject, trim($message), XN_Profile::current(), $event);
        $msg->send($to);
    }

    /** Attendance statuses that have profile sets. */
    private static $statusesWithProfileSets = array(EventAttendee::ATTENDING, EventAttendee::MIGHT_ATTEND, EventAttendee::NOT_ATTENDING);

    /**
     *  Called when a person's RSVP status changes.
     *
     *  @param      $screenName string      User screenName
     *  @param      $eventId    string      Content ID of the Event
     *  @param      $oldStatus  integer     The original status, e.g., EventAttendee::NOT_RSVP
     *  @param      $newStatus  integer     The new status, e.g., EventAttendee::ATTENDING
     */
    public static function statusChanged($screenName, $eventId, $oldStatus, $newStatus) {
        if (! self::$i->acceptingBroadcasts($screenName)) { return; }
        if (in_array($oldStatus, self::$statusesWithProfileSets)) {
            self::_profileSet($eventId, $oldStatus)->removeMember($screenName);
        }
        if (in_array($newStatus, self::$statusesWithProfileSets)) {
            self::_profileSet($eventId, $newStatus)->addMembers($screenName);
        }
    }

    /**
     *  Returns the XN_ProfileSet for broadcast messages for the given event and status.
     *
     *  @param      $event      W_Content   The Event
     *  @param      $status     integer     The attendance status, e.g., EventAttendee::NOT_RSVP
     */
    public static function _profileSet($eventId, $status) {
        return self::$i->_loadOrCreateProfileSet(self::_profileSetId($eventId, $status),
                array(self::_eventLabel($eventId), self::EVENT_BROADCAST_LABEL));
    }

    /**
     *  Returns the XN_ProfileSet ID for broadcast messages for the given event and status.
     *
     *  @param      $event      W_Content   The Event
     *  @param      $status     integer     The attendance status, e.g., EventAttendee::NOT_RSVP
     */
    private static function _profileSetId($eventId, $status) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
        return 'xg_event_broadcast_' . strtr($eventId, ':', '_') . '_' . Events_EventHelper::rsvpToStr($status);
    }

    /**
     * Returns the Profile Set label for notifications associated with the given event.
     *
     * @param $eventId string  the ID of the Event object
     * @return string  the label
     */
    private static function _eventLabel($eventId) {
        return 'xg_event_' . strtr($eventId, ':', '_');
    }

    /** Label for all event-broadcast profile sets */
    const EVENT_BROADCAST_LABEL = 'xg_event_broadcast';

    /** Max number of items to update synchronously (in the foreground) */
    private static $fgCount = 10;

    /** Max number of items to update asynchronously (in the background) */
    private static $bgCount = 30;

    /**
     *  Enables event broadcasts for the specified user.
     *
     *  @param      $screenName string      User screenName
     */
    public static function allowBroadcasts($screenName) {
        $eventAttendees = self::$i->_createPagingList(self::$fgCount, Events_EventHelper::query('EventAttendee')->filter('my->screenName','=',$screenName), true);
        if ($eventAttendees->pageCount == 0) {
        } elseif ($eventAttendees->pageCount == 1) {
            self::$i->_allowBroadcastsProper($screenName, $eventAttendees);
        } else {
            $tasks = array();
            $cdate = self::$i->_time()+1;
            for ($i = $j = 0; $i < $eventAttendees->totalCount; $i += self::$bgCount, $j++) {
                $tasks[] = array(array(__CLASS__,'task_allowBroadcasts'), $j, $screenName, $cdate);
            }
            self::$jobHelper->create($tasks);
        }
    }

    /**
     *  Enables event broadcasts for the specified user.
     *  Called as part of an async job.
     *
     *  @param      $page       int             Page number
     *  @param      $screenName string          User screenName
     *  @param      $cdate      int             Date of job/task creation
     */
    public static function task_allowBroadcasts($page, $screenName, $cdate) {
    	EventWidget::init();
        $eventAttendees = self::$eventHelper->query('EventAttendee')
            ->filter('my->screenName', '=', $screenName)
            ->filter('createdDate', '<=', date('c', $cdate), XN_Attribute::DATE)
            ->order('createdDate', 'asc', XN_Attribute::DATE)
            ->begin($page * self::$bgCount)
            ->end(($page+1) * self::$bgCount)
            ->execute();
        self::$i->_allowBroadcastsProper($screenName, $eventAttendees);
    }

    /**
     *  Enables event broadcasts for the specified user.
     *
     *  @param  $eventAttendees XG_PagingList<EventAttendee>    The user's EventAttendee objects
     */
    public static function _allowBroadcastsProper($screenName, $eventAttendees) {
        foreach ($eventAttendees as $eventAttendee) {
            if (! in_array($eventAttendee->my->status, self::$statusesWithProfileSets)) { continue; }
            self::$i->_profileSet($eventAttendee->my->eventId, $eventAttendee->my->status)->addMembers($screenName);
        }
    }

    /**
     *  Returns the current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
     *
     *  @return     integer     current Unix timestamp
     */
    public static function _time() {
        return time();
    }

    /**
     *  Loads a profile set and creates it if it doesn't already exist
     *
     *  @param  $id     string          The profile set identifier
     *  @param  $labels string|array    String or array OPTIONAL label or array of labels to apply to the
     *                                  new alias (IF the alias is created as a result of this call. If the set
     *                                  already exists the labels will not be updated.)
     *  @return         XN_ProfileSet   The alias
     */
    public static function _loadOrCreateProfileSet($id, $labels) {
        return XN_ProfileSet::loadOrCreate($id, $labels);
    }

    /**
     *  Creates and initializes an XG_PagingList.
     *
     *  @param      $pageSize   int                 Number of items per page
     *  @param      $query      XN_Query|XG_Query   The query to wrap
     *  @param      $noPage     bool                Whether to disable page-number auto-detection from _GET
     *  @return     XG_PagingList                   The list wrapping the query results
     */
    public static function _createPagingList($pageSize, $query, $noPage = false) {
        return XG_PagingList::create($pageSize, $query, $noPage);
    }

    /**
     *  Creates an XN_Query for XN_Invitations.
     *
     *  @return     XN_Query
     */
    public static function _createInvitationQuery() {
        return XN_Query::create('Invitation');
    }

}
