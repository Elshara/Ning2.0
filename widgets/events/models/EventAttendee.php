<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  An association between a User and an Event.
 *  Attributes for the many-to-many relationship between users and events.
 *
 *  @cache_key      none
 *  @cache_label    none
 *  @cache_lock     none
 **/

class EventAttendee extends W_Model {
    /**
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     */
    public $title;

    /**
     * @var XN_Attribute::STRING optional
     * @rule length 0,4000
     */
    public $description;

    /**
     * Whether this object appears in the results of public API queries.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * The widget instance that created this object.
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

    /**
     * Content ID of the associated Event object.
     *
     * @var XN_Attribute::STRING
     */
    public $eventId;

    /**
     * Username of the last person (if any) who invited this person.
     *
     * @var XN_Attribute::STRING optional
     */
    public $inviter;

    /**
     * Username of the associated User object.
     *
     * @var XN_Attribute::STRING
     */
    public $screenName;

    /**
     *  Attendance status. See the list of constants below
     *
     * @var XN_Attribute::NUMBER
     */
    public $status;

    /** Not invited. EventAttendee objects with this status do not exist :) */
    const NOT_INVITED       = 0x0;

    /** Invited, but hasn't RSVPed. */
    const NOT_RSVP          = 0x1;

    /** Confirmed that they will attend. */
    const ATTENDING         = 0x2;

    /** Still unsure about whether they will attend. */
    const MIGHT_ATTEND      = 0x3;

    /** Decided that they will not attend. */
    const NOT_ATTENDING     = 0x4;

    /**
     *  Copy of the Event.endDate. Denormalized for query optimization
     *
     * @var XN_Attribute::STRING
     * @rule length 16,16
     */
    public $eventStartDate;

    /**
     *  Copy of the Event.endDate. Denormalized for query optimization
     *
     * @var XN_Attribute::STRING
     * @rule length 16,16
     */
    public $eventEndDate;

    /**
     *  Copy of the Event.eventType. Denormalized for query optimization
     *
     * @var XN_Attribute::STRING optional
     */
    public $eventType;

    /**
     *  ID of the existing activity log item that was created when the user changed his or her status.
     *  If this ID is not empty we do not log any further activity.
     *
     *  @var XN_Attribute::STRING optional
     */
    public $activityLogId;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/

    /** The name of the User attribute storing the user's event types */
    protected static $typeParam = '';

    /** The name of the User attribute storing the user's personal calendar */
    protected static $calendarParam = '';

    /** Max number of people to update synchronously (in the foreground) */
    public static $fgCount  = 10;

    /** Max number of people to update asynchronously (in the background) */
    public static $bgCount  = 30;

    /** Statuses that indicate that a person has RSVPed. */
    private static $RSVP_STATUSES = array(self::ATTENDING, self::MIGHT_ATTEND, self::NOT_ATTENDING);

    /**
     *  Constructor
     */
    public function  __construct() {
        if (!self::$typeParam) {
            self::$typeParam = XG_App::widgetAttributeName(W_Cache::getWidget('events'), 'eventTypes');
            self::$calendarParam = XG_App::widgetAttributeName(W_Cache::getWidget('events'), 'calendar');
        }
    }

    /**
     *  Updates user status for the specified event and optionally logs this in into the activity feed
     *  (only if user settings are allowed event logging and event is not private).
     *
     *  @param      $screenName string          User screenName
     *  @param      $event      W_Content       The Event
     *  @param      $status     status          One of the status constants
     *  @param		$log		bool			Log status change into the activity feed
     *  @param      $inviter    string          Screen name of the user inviting this user
     *  @return     void
     */
    public static function setStatus($screenName, $event, $status, $log = FALSE, $inviter = null) {
        if ($status > self::NOT_ATTENDING || $status < self::NOT_RSVP) {
            throw new Exception("Illegal status passed to setStatus (2343534534)");
        }
        $ea = self::_loadOrCreate($screenName, $event);

        // Sync user only if we create EA or change to/from NOT_ATTENDING status
        if ( (!$ea->id) || $ea->my->status == self::NOT_ATTENDING ) {
            $syncUser = ($status != self::NOT_ATTENDING);
        } else {
            $syncUser = ($status == self::NOT_ATTENDING);
        }
        if ($syncUser) {
            if ($status == EventAttendee::NOT_ATTENDING) {
                $props = array();
                $orig = array('eventType'=>$ea->my->eventType, 'startDate' => $ea->my->eventStartDate, 'endDate' => $ea->my->eventEndDate);
            } else {
                $props = array('eventType'=>$event->my->eventType, 'startDate' => $event->my->startDate, 'endDate' => $event->my->endDate);
                $orig = array();
            }
            self::_syncUsers(array($ea), $props, $orig, false);
        }
        $deleteInvitation = $ea->my->status == EventAttendee::NOT_RSVP && $status != EventAttendee::NOT_RSVP;
        if ($deleteInvitation && !Events_EventHelper::$isUnitTest) {
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
            Index_InvitationHelper::deleteInvitations(XN_Profile::load($screenName)->email, Events_InvitationHelper::eventInvitationLabel($event->id), self::rsvped($status));
        }
        $changes = array(
            'isPrivate'		=> $event->isPrivate,
            'status'        => $status,
            'eventStartDate'=> $event->my->startDate,
            'eventEndDate'  => $event->my->endDate,
            'eventType'     => "".$event->my->eventType,
            'inviter'       => mb_strlen($inviter) ? $inviter : $ea->my->inviter,
        );
        $user = User::load($screenName);
        // If status wasn't already logged and event is public and user allows the event logging
        if ($log && !$ea->my->activityLogId && $event->my->privacy == Event::ANYONE && $user->my->activityEvents != 'N') {
            $previous = (bool)($ea->my->status == self::ATTENDING || $ea->my->status == self::MIGHT_ATTEND);
            $current = (bool)($status == self::ATTENDING || $status == self::MIGHT_ATTEND);
            if (!$previous && $current) {
                XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                $logItem = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_STATUS_CHANGE, XG_ActivityHelper::SUBCATEGORY_EVENT, $screenName, array($event), $status);
                $changes['activityLogId'] = $logItem->id;
            }
        }
        Events_BroadcastHelper::statusChanged($screenName, $event->id, $ea->my->status, $status);
        Events_EventHelper::update(NULL, $ea, $changes);
        XN_Cache::invalidate(Event::cacheLabel($event->id));
    }

    /**
     *  Returns the list of user statuses for the specified events.
     *
     *  @param      $screenName string                  User screenName
     *  @param      ...         list<Event>|Event       One or more events
     *  @return     status | hash<event-id:status>      A status (if one Event is given), or a hash of statuses keyed by Event ID
     */
    public static function getStatuses($screenName /*, ...*/) {
        $args       = func_get_args(); array_shift($args);
        $eventIds   = array();
        $res        = array();
        foreach ($args as $events) {
            if (!$events) {
                continue;
            }
            foreach (is_array($events) ? $events : array($events) as $e) {
                $eventIds[]     = $e->id;
                $res[$e->id]    = self::NOT_INVITED;
            }
        }
        if (!$eventIds) {
            return array();
        }
        $query = Events_EventHelper::query('EventAttendee')
                    ->filter('my->screenName','=',$screenName)
                    ->filter('my->eventId','in', $eventIds);
        foreach ($query->execute() as $ea) {
            $res[$ea->my->eventId] = $ea->my->status;
        }
        return count($args) == 1 && !is_array($args[0]) ? $res[$args[0]->id] : $res;
    }

    /**
     *  Returns the list of event types that user attends. Events with status=not_attending are not included in this list.
     *
     *  @param      $screenName string          User screenName
     *  @return     hash<name:count>            Hash of event type => count
     */
    public static function getEventTypes($screenName) {
        $user = User::load($screenName);
        return ($user && ($v = $user->my->{self::$typeParam})) ? array_filter((array)unserialize($v)) : array();
    }

    /**
     *  Returns the list of user's upcoming events (excluding NOT_ATTENDING status)
     *
     *  @param      $screenName string          User screenName
     *  @param      $limit      int             Number of events to return
     *  @param      $noPage     bool        	Disables the page autodetection from _GET.
     *  @return     XG_PagingList<Event>        The list of events
     */
    public static function getUpcomingEvents($screenName, $limit, $noPage = false) {
        //!! negative browsing
        return self::_attendeesToEvents($limit, Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->status','<>',self::NOT_ATTENDING)
            ->filter('my->eventEndDate', '>', Events_EventHelper::dateToStr(null, false))
            ->order('my->eventStartDate','asc'), $noPage);
    }

    /**
     *  Filters out screen names of users who have not yet responded to the invitation.
     *
     *  @param      $screenNames    array   Usernames to check
     *  @param      $eventId        string  Content ID of the Event
     *  @return                     array   The screen names that have RSVPed, keyed by screen name
     */
    public static function getRsvpedScreenNames($screenNames, $eventId) {
        return self::screenNames(Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName', 'in', $screenNames)
            ->filter('my->eventId','=', $eventId)
            ->filter('my->status', '<>', self::NOT_RSVP)
            ->execute());
    }

    /**
     *  Returns the list of user's events that he/she attends.
     *
     *  @param      $screenName string          User screenName
     *  @param      $limit      int             Number of events to return
     *  @param      $noPage     bool        	Disables the page autodetection from _GET.
     *  @return     XG_PagingList<Event>        The list of events
     */
    public static function getAttendingEvents($screenName, $limit, $noPage = false) {
        //!! negative browsing
        return self::_attendeesToEvents($limit, Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->eventEndDate', '>', Events_EventHelper::dateToStr(null, false))
            ->filter('my->status','=', self::ATTENDING)
            ->order('my->eventStartDate','asc'), $noPage);
    }
    /**
     *  Returns the list of user's events that he/she decided not to attend.
     *
     *  @param      $screenName string          User screenName
     *  @param      $limit      int             Number of events to return
     *  @param      $noPage     bool        	Disables the page autodetection from _GET.
     *  @return     XG_PagingList<Event>        The list of events
     */
    public static function getNotAttendingEvents($screenName, $limit, $noPage = false) {
        //!! negative browsing
        return self::_attendeesToEvents($limit, Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->eventEndDate', '>', Events_EventHelper::dateToStr(null, false))
            ->filter('my->status','=', self::NOT_ATTENDING)
            ->order('my->eventStartDate','asc'), $noPage);
    }

    /**
     *  Return the list of user's events by type. Events with status=not_attending aren't returned.
     *
     *  @param      $screenName string          User screenName
     *  @param      $type       string          The event type
     *  @param      $limit      int             Number of events to return
     *  @return     XG_PagingList<Event>        The list of events
     */
    public static function getEventsByType($screenName, $type, $limit) {
        return self::_attendeesToEventsProper(self::createNegativePagingList($limit, Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->eventType','like', Events_EventHelper::typeFilter($type))
            ->filter('my->status','<>', self::NOT_ATTENDING)
            ->order('my->eventStartDate','asc')));
    }

    /**
     *  Creates a bi-directional PagingList based on the given query.
     *
     *  @param  $pageSize     integer     Number of items per page
     *  @param  $query        XN_Query    Query initialized with owner, type, and other filters (but not my->eventEndDate)
     */
    private static function createNegativePagingList($limit, $query) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_NegativePagingList.php');
        $list = new Events_NegativePagingList($limit);
        $neg = clone($query);
        $pos = clone($query);
        $neg->filter('my->eventEndDate', '<=', Events_EventHelper::dateToStr(null, false))->order('my->eventStartDate','desc');
        $pos->filter('my->eventEndDate', '>',  Events_EventHelper::dateToStr(null, false))->order('my->eventStartDate','asc');
        $list->setQueries($neg, $pos);
        return $list;
    }

    /**
     *  Returns the list of user's events for a given date.
     *
     *  @param      $screenName string          				User screenName
     *  @param      $date       string          				YYYY-MM-DD
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. If specified, a custom "per-date" paging list is returned.
     *  @return     XG_PagingList<Event>        				The list of events
     */
    public static function getEventsByDate($screenName, $date, $calendar = NULL) {
        $start = XG_DateHelper::format('Y-m-d',$date);
        $end = XG_DateHelper::format('Y-m-d',$date,'+1 day');

        $list = self::_attendeesToEvents(0, Events_EventHelper::query('EventAttendee')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->eventEndDate', '>=', $start)
            ->filter('my->eventStartDate', '<',  $end)
            ->filter('my->status','<>', self::NOT_ATTENDING)
            ->order('my->eventStartDate','asc'));
        if (NULL === $calendar) {
            return $list;
        }
        $customList = new XG_PagingList(0,'date');
        $customList->setResult($list->getList(), 0, array(
            'prevPage'  => self::_getPrevDate($screenName, $calendar, $date),
            'nextPage'  => self::_getNextDate($screenName, $calendar, $date),
        ));
        return $customList;
    }

    /**
     *  Returns the list of event attendees with a given status
     *
     *  @param      $event      Event       The Event
     *  @param      $status     status      One of the status constants
     *  @param      $limit      int         Number of events to return
     *  @param      $noPage     bool		Disables the page autodetection from _GET.
     *  @return     XG_PagingList<User>
     */
    public static function getAttendees($event, $status, $limit, $noPage = false) {
        $list = self::getAttendeesProper($event, $status, $limit, $noPage);
        $list->setResult(User::loadMultiple(self::screenNames($list->getList())), $list->totalCount);
        return $list;
    }

    /**
     *  Returns the list of event attendees with a given status
     *
     *  @param      $event      Event       The Event
     *  @param      $status     status      One of the status constants
     *  @param      $limit      int         Number of events to return
     *  @param      $noPage     bool		Disables the page autodetection from _GET.
     *  @return     XG_PagingList<EventAttendee>
     */
    public static function getAttendeesProper($event, $status, $limit, $noPage = false) {
        if ($status == self::NOT_RSVP) { throw new Exception('Use Events_InvitationHelper::getInvitations() instead - it includes invitees who are not members of the network (702034767)'); }
        $list   = new XG_PagingList($limit, $noPage ? '' : NULL);
        $query  = $list->processQuery( Events_EventHelper::query('EventAttendee')
            ->filter('my->eventId','=',$event->id)
            ->filter('my->status','=',$status))
            ->order('updatedDate','desc');
        $list->setResult($query->execute(), $query->getTotalCount());
        return $list;
    }

    /**
     *  Returns the user's personal event calendar for the specified period.
     *  Event dates are timezone-agnostic. Events with status=not_attending are not included in this list.
     *
     *  @param      $screenName string      User screenName
     *  @param      $start      string      YYYY-MM
     *  @param      $end        string      YYYY-MM
     *  @return     hash<yyyy-mm:hash<day:count>>   Event counts, keyed by YYYY-MM and D
     */
    public static function getCalendar($screenName, $start, $end) {
        $user = User::load($screenName);
        $calendar = ($user && $v = $user->my->{self::$calendarParam}) ? (array)unserialize($v) : array();
        $res = array();
        foreach(XG_DateHelper::monthRange($start, $end) as $ym) {
            list($y,$m) = explode('-',$ym);
            $monthData  = array_fill(1,XG_DateHelper::lastDay($y,$m),0);
            if (isset($calendar[$ym])) {
                foreach ($calendar[$ym] as $day=>$cnt) {
                    $monthData[intval($day)] = $cnt;
                }
            }
            $res["$y-$m"] = $monthData;
        }
        return $res;
    }

    /**
     *  Returns the user's personal event calendar for the current and next months
     *  Event dates are timezone-agnostic. Events with status=not_attending are not included in this list.
     *
     *  @param      $screenName string          User screenName
     *  @return     hash<yyyy-mm:hash<day:count>>   Event counts, keyed by YYYY-MM and D
     */
    public static function getDefaultCalendar($screenName) {
        return self::getCalendar($screenName, xg_date('Y-m'),xg_date('Y-m','+1 month'));
    }

    /**
     *  Returns min and max dates where user's events are present. Dates are returned in format YYYY-MM-DD
     *  If user doesn't have any events, empty dates are returned.
     *  These dates are NOT low-level and high-level marks (differs from EventWidget::getMinMaxEventDates())
     *
     *  @param      $screenName string          User screenName
     *  @return     list<min,max>       The YYYY-MM-DD dates
     */
    public static function getMinMaxEventDates ($screenName) {
        $user = User::load($screenName);
        if ( !$user || !is_array($v = unserialize($user->my->{self::$calendarParam}))) {
            return array('','');
        }
        $min = $max = '';
        foreach ($v as $ym=>$info) {
            foreach($info as $day=>$cnt) {
                if ($cnt < 0) {
                    continue;
                }
                $ymd = "$ym-".sprintf('%02d',$day);
                if (!$min || strcmp($ymd,$min) < 0) {
                    $min = $ymd;
                }
                if (!$max || strcmp($ymd,$max) > 0) {
                    $max = $ymd;
                }
            }
        }
        return array($min,$max);
    }

    /**
     *  Returns whether the user has indicated whether they will be attending the event.
     *
     *  @param      $status     string      Attendance status
     *  @return                 boolean     Whether the status is ATTENDING, MIGHT_ATTEND, or NOT_ATTENDING
     */
    public static function rsvped($status) {
        return in_array($status, self::$RSVP_STATUSES);
    }

    /**
     * Returns the EventAttendee object for the given User and Event,
     *
     *  @param   $screenName    string      User screenName
     *  @param   $event         W_Content   The Event
     *  @return  W_Content                  The EventAttendee, or null if one doesn't exist
     */
    public static function load($screenName, $event) { # EventAttendee
        $res = Events_EventHelper::query('EventAttendee')
                ->filter('my->screenName','=',$screenName)
                ->filter('my->eventId','=',$event->id)
                ->execute();
        return $res ? W_Content::create($res[0]) : null;
    }

    /**
     *  Removes attendee from an Event.
     *
     *  @param      $screenName string          User screenName
     *  @param      $event      W_Content       The Event
     *  @return     bool                        Whether an EventAttendee object was in fact deleted
     */
    public static function delete($screenName, $event) {
        if (!$ea = self::load($screenName, $event)) { return false; }
        self::deleteProper($ea);
        return true;
    }

    /**
     *  Removes attendee from an Event.
     *
     *  @param      $ea         W_Content       The EventAttendee
     */
    public static function deleteProper($ea) {
        $props = array();
        $orig = array('eventType'=>$ea->my->eventType, 'startDate' => $ea->my->eventStartDate, 'endDate' => $ea->my->eventEndDate);
        self::_syncUsers(array($ea), $props, $orig);
        Events_BroadcastHelper::statusChanged($ea->my->screenName, $ea->my->eventId, $ea->my->status, EventAttendee::NOT_INVITED);
        W_Content::delete($ea);
        XN_Cache::invalidate(Event::cacheLabel($ea->my->eventId));
    }


//** Event handlers
    /**
     *  Called before an event is saved or deleted. Syncs EventAttendee and User objects.
     *
     *  @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     *  @param   $event  W_Content               The Event
     *  @param   $props  hash                    The new attribute values
     *  @param   $orig   hash                    The old attribute values
     *  @return  void
     */
    public static function onBeforeEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        $cmd->eaChanged = array();
        $cmd->eaProps = array('.'=>0);  //
        $cmd->eaOrigs = array('.'=>0);  // avoid bug with http_build_query inside XN_Task
        // in case we process "event.delete" command, ID could be NULL in "after" handler
        if (!($cmd->eaEventId = $event->id)) {
            return;
        }
        foreach (array('startDate','endDate','eventType','isPrivate') as $k) {
            if ( $cmd->eaChanged[$k] = ($props[$k] != $orig[$k]) ) {
                $cmd->eaProps[$k] = $props[$k];
                $cmd->eaOrigs[$k] = $orig[$k];
            }
        }
    }

    /**
     *  Called after an event is saved or deleted. Syncs EventAttendee and User objects.
     *
     *  @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     *  @param   $event  W_Content               The Event
     *  @param   $props  hash                    The new attribute values
     *  @param   $orig   hash                    The old attribute values
     *  @return  void
     */
    public static function onAfterEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        if (!array_filter($cmd->eaChanged)) {
            return;
        }
        $list = XG_PagingList::create(self::$fgCount, Events_EventHelper::query('EventAttendee')->filter('my->eventId','=',$cmd->eaEventId), true);
        if ($list->pageCount == 0) {
            // do nothing
        } elseif ($list->pageCount == 1) { // Update them right now
            if ($cmd->eaChanged['startDate'] || $cmd->eaChanged['endDate'] || $cmd->eaChanged['eventType']) {
                self::_syncUsers($list->getList(), $cmd->eaProps, $cmd->eaOrigs);
            }
            self::_syncEventAttendees($list->getList(), $props ? $event : NULL);
        } else { // Too many people. Update in the background
            // the difference in milliseconds breaks unit tests sometimes:
            //		in content store we have: 03:02:01.534Z but in cdate: 03:02:01Z
            //		and objects cannot be found.
            $tasks = array();
            if ($props) { // update event
                $cdate = time()+1;
                for ($i = $j = 0; $i<$list->totalCount; $i+=self::$bgCount, $j++) {
                    $tasks[] = array(array(__CLASS__,'task_syncAttendees'), $j, $cmd->eaEventId, $cdate, $cmd->eaProps, $cmd->eaOrigs);
                }
            } else { // delete event
                $tasks[] = array(array(__CLASS__,'task_deleteAttendees'), $cmd->eaEventId, $cmd->eaProps, $cmd->eaOrigs);
            }
            XG_JobHelper::create($tasks);
        }
    }

//** Background callbacks
    /**
     *  Synchronizes event attendees with an Event object.
     *  Called as part of an async job.
     *
     *  @param      $page       int             Page number
     *  @param      $eventId    string          Content ID of the Event
     *  @param		$cdate		int				Date of job/task creation
     *  @param      $props      hash            eventType, startDate, endDate - new prop values
     *  @param      $orig       hash            eventType, startDate, endDate - old prop values
     *  @return     void
     */
    public static function task_syncAttendees($page, $eventId, $cdate, $props, $orig) {
        EventWidget::init();
        $list = Events_EventHelper::query('EventAttendee')
            ->filter('my->eventId', '=', $eventId)
            ->filter('createdDate', '<=', date('c',$cdate), XN_Attribute::DATE)
            ->order('createdDate', 'asc', XN_Attribute::DATE)
            ->begin($page*self::$bgCount)->end( ($page+1)*self::$bgCount )
            ->execute();
        $event = Event::byId($eventId);
        if ($props['startDate'] || $props['endDate'] || $props['eventType'] || $orig['startDate'] || $orig['endDate'] || $orig['eventType']) {
            self::_syncUsers($list, $props, $orig);
        }
        self::_syncEventAttendees($list, $event);
    }

    /**
     *  RSynchronizes event attendees with an Event object.
     *  Called as part of an async job.
     *
     *  @param      $eventId    string          Content ID of the Event
     *  @param      $props      hash            eventType, startDate, endDate - new prop values
     *  @param      $orig       hash            eventType, startDate, endDate - old prop values
     *  @return     void
     */
    public static function task_deleteAttendees($eventId, $props, $orig) {
        EventWidget::init();
        $list = Events_EventHelper::query('EventAttendee')->filter('my->eventId', '=', $eventId)->end(self::$bgCount)->execute();
        self::_syncUsers($list, $props, $orig);
        self::_syncEventAttendees($list, NULL);
        if (count($list)) {
            $task = array(array(__CLASS__,'task_deleteAttendees'), $eventId, $props, $orig);
            XG_JobHelper::create(array($task));
        }
    }

//** Implementation
    /**
     *  Syncronizes event attendees with an Event object.
     *
     *  @param      $list       list<EventAttendee>
     *  @param      $event      Event|NULL      Event or NULL for delete
     *  @return     void
     */
    protected static function _syncEventAttendees(array $list, $event) { # void
        foreach ($list as $ea) {
            if ($event) {
                $ea->isPrivate = $event->isPrivate;
                $ea->my->eventType = $event->my->eventType;
                $ea->my->eventStartDate = $event->my->startDate;
                $ea->my->eventEndDate = $event->my->endDate;
                $ea->save();
            } else {
                XN_Content::delete($ea);
            }
        }
    }

    /**
     *  Synchronizes the Users' personal calendars with the given Event object.
     *
     *  @param      $list       list<EventAttendee> The EventAttendees
     *  @param      $props      hash                eventType, startDate, endDate - new prop values
     *  @param      $orig       hash                eventType, startDate, endDate - old prop values
     *  @param      $skipNotAttending bool          If true NOT_ATTENDING users are skipped (normal mode)
     *  @return     void
     */
    protected static function _syncUsers($list, $props, $orig, $skipNotAttending = true) { # void
        $oldTypes = Events_EventHelper::typeToList($orig['eventType']);
        $newTypes = Events_EventHelper::typeToList($props['eventType']);
        $oldDates = XG_DateHelper::dayRange($orig['startDate'],$orig['endDate']);
        $newDates = XG_DateHelper::dayRange($props['startDate'],$props['endDate']);

        $users = array();
        foreach ($list as $ea) {
            if ( !($skipNotAttending && $ea->my->status == self::NOT_ATTENDING) ) {
                $users[$ea->my->screenName] = 0;
            }
        }
        $users = User::loadMultiple(array_keys($users));
        foreach ($users as $user) {

            // Sync event types
            if ($oldTypes || $newTypes) {
                $types = ($v = $user->my->{self::$typeParam}) ? (array)unserialize($v) : array();
                foreach ($oldTypes as $t) { Events_EventHelper::decrement($types, $t); }
                foreach ($newTypes as $t) { $types[$t]++; }
                $user->my->{self::$typeParam} = serialize($types);
            }

            // Sync Personal Calendar
            if ($oldDates || $newDates) {
                $calendar = ($v = $user->my->{self::$calendarParam}) ? (array)unserialize($v) : array();
                foreach ($oldDates as $dt) { list($y,$m,$d) = explode('-',$dt); $calendar["$y-$m"][(int)$d]-=1;} // cannot use "--". http://bugs.php.net/bug.php?id=20548
                foreach ($newDates as $dt) { list($y,$m,$d) = explode('-',$dt); $calendar["$y-$m"][(int)$d]++;}
                $user->my->{self::$calendarParam} = serialize($calendar);
            }

            $user->my->xg_events_eventCount = NULL;
            $user->my->xg_events_nextEventEndDate = NULL;
            $user->save();
        }
    }

    /**
     * Returns the EventAttendee object for the given User and Event,
     * or creates one if it doesn't exist.
     *
     *  @param   $screenName    string      User screenName
     *  @param   $event         W_Content   The Event
     *  @return  W_Content                  The EventAttendee
     */
    protected static function _loadOrCreate($screenName, $event) { # EventAttendee
        if ($res = self::load($screenName, $event)) {
            return $res;
        }
        $res = Events_EventHelper::create('EventAttendee');
        $res->my->screenName = $screenName;
        $res->my->eventId = $event->id;
        return $res;
    }

    /**
     *  Converts the set of EventAttendee objects to a list of the corresponding Event objects.
     *
     *  @param      $limit      int         Page size
     *  @param      $query      XN_Query    EventAttendee query
     *  @param      $noPage     bool        Disable the page autodetection from _GET.
     *  @return     XG_PagingList<Event>
     */
    protected static function _attendeesToEvents($limit, $query, $noPage = false) {
        return self::_attendeesToEventsProper(XG_PagingList::create($limit, $query, $noPage));
    }

    /**
     *  Converts the set of EventAttendee objects to a list of the corresponding Event objects.
     *
     *  @param      $list   XG_PagingList<Event>    The Events. Will be modified to contain EventAttendees.
     *  @return     XG_PagingList<EventAttendee>    Same as $list.
     */
    protected static function _attendeesToEventsProper($list) {
        $res        = array();
        foreach ($list->getList() as $ea) {
            $res[$ea->my->eventId] = NULL;
        }
        if ($res) {
            // keep the original order
            foreach (self::_eventHelper()->query('Event')->filter('id','in',array_keys($res))->execute() as $e) {
                $res[$e->id] = $e;
            }
            $list->setResult(array_values($res), $list->totalCount, $list->getExtraOptions());
        }
        return $list;
    }

    /** Useful functions related to Events. */
    protected static $eventHelper;

    /**
     *  Returns a helper containing useful functions related to Events.
     *
     *  @return Events_EventHelper   the EventHelper, or a mock object for testing
     */
    private function _eventHelper() {
        if (! self::$eventHelper) { self::$eventHelper = new Events_EventHelper(); }
        return self::$eventHelper;
    }

    /**
     *  Converts the set of EventAttendee objects to a list of screenNames.
     *
     *  @param  $eventAttendees array   The EventAttendee content objects
     *  @return                 array   The corresponding my->screenName values, keyed by screenName
     */
    public static function screenNames($eventAttendees) {
        $screenNames = array();
        foreach ($eventAttendees as $eventAttendee) {
            $screenNames[$eventAttendee->my->screenName] = $eventAttendee->my->screenName;
        }
        return $screenNames;
    }

    /**
     *  Returns the first date before $date which contains events or the
     *  null if nothing is found. Used for navigation over the Calendar.
     *
     *	@param		$screenName string							User
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. Used to avoid extra queries if possible.
     *  @param      $date       string                          YYYY-MM-DD
     *  @return                 string|null                     YYYY-MM-DD
     */
    protected static function _getPrevDate($screenName, $calendar, $date) {
        if ($prevDate = Event::_findNearestDateWithEvents($calendar, $date, -1)) {
            return $prevDate;
        }

        $query  = Events_EventHelper::query('EventAttendee')
            ->filter('my->eventStartDate', '<', XG_DateHelper::format('Y-m-d',$date))
            ->order('my->eventEndDate','desc')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->status','<>', self::NOT_ATTENDING)
            ->end(1);
        $query->alwaysReturnTotalCount(false);
        if (!list($prev) = $query->execute()) {
            return NULL;
        }
        $prev = mb_substr($prev->my->eventEndDate,0,10);
        // if event starts before and finishes after the date
        // just return the previous date
        return strcmp($prev, $date) >= 0 ? XG_DateHelper::format('Y-m-d',$date,'-1 day') : $prev;
    }

    /**
     *  Returns the first date after $date which contains events or the
     *  null if nothing is found. Used for navigation over the Calendar.
     *
     *	@param		$screenName string							User
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. Used to avoid extra queries if possible.
     *  @param      $date       string                          YYYY-MM-DD
     *  @return                 string|null                     YYYY-MM-DD
     */
    protected static function _getNextDate($screenName, $calendar, $date) {
        if ($nextDate = Event::_findNearestDateWithEvents($calendar, $date, +1)) {
            return $nextDate;
        }

        $query  = Events_EventHelper::query('EventAttendee')
            ->filter('my->eventEndDate', '>=', XG_DateHelper::format('Y-m-d',$date,'+1 day'))
            ->order('my->eventStartDate','asc')
            ->filter('my->screenName','=',$screenName)
            ->filter('my->status','<>', self::NOT_ATTENDING)
            ->end(1);
        $query->alwaysReturnTotalCount(false);
        if (!list($next) = $query->execute()) {
            return NULL;
        }
        $next = mb_substr($next->my->eventStartDate,0,10);
        // if event starts before and finishes after the date
        // just return the next date
        return strcmp($next, $date) <= 0 ? XG_DateHelper::format('Y-m-d',$date,'+1 day') : $next;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
?>
