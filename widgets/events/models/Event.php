<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  A social gathering or activity (American Heritage Dictionary).
 *
 *  The following properties affect whether the current user can see the event:
 *      - my->privacy
 *      - is viewer owner/NC
 *      - my->isClosed
 *      - my->showGuestList
 *
 *  @cache_key      none
 *  @cache_label    Event-$id               invalidates all caches associated with an Event
 *  @cache_lock     Event-$id:lock          wraps code that modifies an Event object
 **/
class Event extends W_Model {
    /**
     * The name of the event.
     *
     * @var XN_Attribute::STRING
     * @feature indexing text
     */
    public $title;
    const MAX_TITLE_LENGTH = 200; // Max number of *multibyte* characters [Jon Aquino 2008-04-02]

    /**
     * A description of the event.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $description;
    const MAX_DESCRIPTION_LENGTH = 4000;

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
     * @feature indexing text
     */
    public $mozzle;

    /**
     * Event visibility:
     *     - ANYONE - (default) anyone can see and attend the event
     *     - INVITED - only invited people can see the event details and only invited people can attend.
     *
     * @var XN_Attribute::NUMBER
     */
    public $privacy;
    const ANYONE = 1;
    const INVITED = 2;

    /**
     * Event type(s). Stored in an internal format.
     *
     * @var XN_Attribute::STRING optional
     */
    public $eventType;

    /**
     * Event types as entered by the user. Used for searches only.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $eventTypeOrig;
    const MAX_EVENT_TYPE_LENGTH = 200;

    /**
     * The date and time at which the event begins.
     * Format: YYYY-MM-DD HH:MM (HH = [0,23])
     * Timezone independent
     *
     * @var XN_Attribute::STRING
     * @rule length 16,16
     */
    public $startDate;

    /**
     * The date and time at which the event ends.
     * Format: YYYY-MM-DD HH:MM (HH = [0,23])
     * Timezone independent. Cannot be empty.
     * If not specified, set to startDate.
     *
     * @var XN_Attribute::STRING
     * @rule length 16,16
     */
    public $endDate;

    /**
     * Whether to hide the end date (because the user has not specified it).
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $hideEndDate;

    /**
     *  Whether to show the guest list for the event.
     *
     *  @var XN_Attribute::NUMBER optional
     */
    public $showGuestList;

    /**
     *  Whether to disable RSVPing completely.
     *
     *  @var XN_Attribute::NUMBER optional
     */
    public $disableRsvp;

    /**
     *  Whether the event is closed to new attendees.
     *
     *  @var XN_Attribute::NUMBER optional
     */
    public $isClosed;

    /**
     * A general location, such as "The Fillmore".
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $location;
    const MAX_LOCATION_LENGTH = 255;

    /**
     * Street address.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $street;
    const MAX_STREET_LENGTH = 255;

    /**
     * The city in which the event is taking place.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $city;
    const MAX_CITY_LENGTH = 255;

    /**
     * URL of the event's website.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $website;
    const MAX_WEBSITE_LENGTH = 500;

    /**
     * Contact details, such as phone number, email address,
     * or instant messenger address.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $contactInfo;
    const MAX_CONTACT_INFO_LENGTH = 255;

    /**
     * Full name of the person hosting the event,
     * or the screen name of the event creator.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $organizedBy;
    const MAX_ORGANIZED_BY_LENGTH = 255;

    /**
     * Content ID of the event photo.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,255
     */
    public $photoId;

    /**
     * URL for the event photo.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,500
     */
    public $photoUrl;

    /**
     * "Y" indicates that this Event should be excluded from Ningbar and widget
     * search results. This is true of events with $privacy == INVITED.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
    /** Max number of comments/invitations to delete synchronously (in the foreground) */
    public static $fgCount  = 10;

    /** Max number of comments/invitations to delete asynchronously (in the background) */
    public static $bgCount  = 50;

    /**
     *  Constructor
     */
    public function  __construct() {
        EventWidget::init();
    }

    /**
     *  Creates and initializes an Event object.
     *
     *  @param      $props      hash        Event parameters, e.g., startDate and endDate
     *  @param      $log        bool        Log Event creation into the activity feed
     *  @return     Event                   The new Event
     */
    public static function create(array $props, $log = FALSE) {
        $event = Events_EventHelper::create('Event');
        $props = array_merge(array(
            'privacy'       => Event::ANYONE,
        ), $props);
        self::update($event, $props, $log);
        return $event;
    }

    /**
     *  Updates Event properties and saves it.
     *  Ensures that all necessary routines are called.
     *
     *  @param      $event      W_Content   Event
     *  @param      $props      array       Parameters to update. See @create for the list of parameters
     *  @param      $log        bool        Log Event creation into the activity feed
     *  @return     void
     */
    public static function update(W_Content $event, array $props, $log = FALSE) {
        $orig   = $event->export();

        if ($props['startDate']) {
            $props['startDate'] = Events_EventHelper::dateToStr($props['startDate']);
        }
        if ($props['endDate']) {
            $props['endDate'] = Events_EventHelper::dateToStr($props['endDate']);
        }
        if ($props['eventType']) {
            $props['eventTypeOrig'] = $props['eventType'];
            $props['eventType'] = Events_EventHelper::textToType($props['eventType']);
        }
        $props  = array_merge($orig,$props);
        $start  = Events_EventHelper::dateToTs("".$props['startDate']);
        $end    = Events_EventHelper::dateToTs("".$props['endDate']);
        if (!$end || $start > $end) {
            $props['endDate'] = Events_EventHelper::dateToStr($start);           // if no end date or end is invalid, set to start
        } elseif ($end-$start > 86400*14) {
            $props['endDate'] = Events_EventHelper::dateToStr($start+86400*14);  // no more that 2 weeks
        }
        $props['isPrivate'] = ($props['privacy'] == Event::INVITED) || XG_App::appIsPrivate();
        $props['excludeFromPublicSearch'] = $props['privacy'] == Event::INVITED ? 'Y' : 'N';
        $eventExists = (bool)$event->id;
        Events_EventCommand::execute('event.save', $event, $props, $orig);

        if ($log && $props['privacy'] == Event::ANYONE) {
            $user = User::load(XN_Profile::current()->screenName);
            if ($user->my->activityEvents != 'N') {
                XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                XG_ActivityHelper::logActivityIfEnabled( $eventExists ? XG_ActivityHelper::CATEGORY_UPDATE : XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_EVENT, $user->title, array($event));
            }
        }
        XN_Cache::invalidate(self::cacheLabel($event->id));
    }

    /**
     *  Returns cache label which invalidate all caches related to the specified Event.
     *
     *  @param      $eventId    string      The content ID of the Event
     *  @return     string                  The label
     */
    public static function cacheLabel($eventId) {
        if (! is_string($eventId)) { throw new Exception('Non-string passed to Event::cacheLabel. (939866013)'); }
        return "Event-$eventId";
    }

    /**
     *  Removes the event
     *
     *  @param      $event      W_Content   The Event to delete
     */
    public static function delete(W_Content $event) {
        Events_EventCommand::execute('event.delete', $event, array(), $event->export());
    }

    /**
     *  Retrieves an event.
     *
     *  @param      $id         string      The content ID of the Event
     *  @return     W_Content               The Event with the given ID
     */
    public static function byId($id) { # Event
        return W_Model::findById('Event',$id);
    }

    /**
     *  Returns a list of the upcoming events (only public) ORDER BY startDate DESC.
     *  We include featured events in the upcoming events.
     *
     *  @param      $limit   int            Number of events to retrieve
     *  @return     XG_PagingList<Event>    List of events, with paging support
     */
    public static function getUpcomingEvents($limit) {
        return XG_PagingList::create($limit, Events_EventHelper::query('Event')
            ->filter('my->privacy', '=', Event::ANYONE)
            ->filter('my->endDate', '>', Events_EventHelper::dateToStr(null, false))
            ->order('my->startDate','asc'));
    }

    /**
     *  Return a list of featured Events
     *
     *  @param      $limit   int            Number of events to retrieve
     *  @param      $noPage  bool           Disables the page autodetection from _GET.
     *  @return     XG_PagingList<Event>    List of events, with paging support
     */
    public static function getFeaturedEvents($limit, $noPage = false) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled()) { return new XG_PagingList($limit); }
        return XG_PagingList::create($limit, XG_PromotionHelper::addPromotedFilterToQuery( Events_EventHelper::query('Event')
            ->filter('my->endDate', '>', Events_EventHelper::dateToStr(null, false))
            ->order('my->startDate','asc') ), $noPage );
    }

    /**
     *  Returns events for a given date.
     *  The first value returned is a featured event (if any).
     *  The second value returned is a list of all events for the day (excluding the featured event).
     *
     *  @param      $date       string                          YYYY-MM-DD
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. If specified, a custom "per-date" paging list is returned.
     *  @return     list<featuredEvent, XG_PagingList<Event>>   The featured event (if any), and a list of events
     */
    public static function getEventsByDate($date, $calendar = NULL) {
        $start          = XG_DateHelper::format('Y-m-d',$date);
        $end            = XG_DateHelper::format('Y-m-d',$date,'+1 day');
        if (XG_PromotionHelper::areQueriesEnabled()) {
            $featured = XG_PromotionHelper::addPromotedFilterToQuery( Events_EventHelper::query('Event')
                ->filter('my->endDate', '>=', $start)
                ->filter('my->startDate', '<', $end)
                ->order('my->startDate','asc')
                ->end(1))->uniqueResult();
        }
        $query = Events_EventHelper::query('Event')
            ->filter('my->endDate', '>=', $start)
            ->filter('my->startDate', '<',  $end)
            ->order('my->startDate','asc');
        if ($featured) {
            $query->filter('id','<>',$featured->id);
        }
        if (NULL === $calendar) {
            $list       = XG_PagingList::create(0,$query);
        } else {
            $list       = new XG_PagingList(0,'date');
            $list->processQuery($query);
            $list->setResult($query->execute(), 0, array(
                'prevPage'  => self::_getPrevDate($calendar, $date),
                'nextPage'  => self::_getNextDate($calendar, $date),
            ));
        }
        # fetch all list
        return array($featured, $list);
    }

    /**
     *  Returns events for a given type.
     *  The first value returned is a featured event (if any).
     *  The second value returned is a list of all events for the type (excluding the featured event).
     *
     *  @param      $type       string                          The event type
     *  @param      $limit      int                             Number of events to retrieve
     *  @return     list<featuredEvent, XG_PagingList<Event>>   The featured event (if any), and a list of events
     */
    public static function getEventsByType($type, $limit) {
        if (XG_PromotionHelper::areQueriesEnabled()) {
            $featured = XG_PromotionHelper::addPromotedFilterToQuery( Events_EventHelper::query('Event')
                ->filter('my->eventType', 'like', Events_EventHelper::typeFilter($type))
                ->filter('my->endDate', '>', Events_EventHelper::dateToStr(null, false))
                ->order('my->startDate','asc')
                ->end(1))->uniqueResult();
        }
        return array($featured, self::createNegativePagingList($limit,
                Events_EventHelper::query('Event')->filter('my->eventType', 'like', Events_EventHelper::typeFilter($type)), $featured));
    }

    /**
     *  Returns events for a given location.
     *  The first value returned is a featured event (if any).
     *  The second value returned is a list of all events for the location (excluding the featured event).
     *
     *  @param      $type       string                          The event type
     *  @param      $limit      int                             Number of events to retrieve
     *  @return     list<featuredEvent, XG_PagingList<Event>>   The featured event (if any), and a list of events
     */
    public static function getEventsByLocation($location, $limit) {
        if (XG_PromotionHelper::areQueriesEnabled()) {
            $featured = XG_PromotionHelper::addPromotedFilterToQuery( Events_EventHelper::query('Event')
                ->filter('my->location', '=', $location)
                ->filter('my->endDate', '>', Events_EventHelper::dateToStr(null, false))
                ->order('my->startDate','asc')
                ->end(1))->uniqueResult();
        }
        return array($featured, self::createNegativePagingList($limit,
                Events_EventHelper::query('Event')->filter('my->location', '=', $location), $featured));
    }


    /**
     *  Creates a bi-directional PagingList based on the given query.
     *
     *  @param  $pageSize     integer     Number of items per page
     *  @param  $query        XN_Query    Query initialized with owner, type, and other filters (but not my->endDate)
     *  @param  $eventToSkip  XN_Content  Event to exclude from the results, or null
     */
    private static function createNegativePagingList($limit, $query, $eventToSkip) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_NegativePagingList.php');
        $list = new Events_NegativePagingList($limit);
        $neg = clone($query);
        $pos = clone($query);
        $neg->filter('my->endDate', '<=', Events_EventHelper::dateToStr(null, false))->order('my->startDate','desc');
        $pos->filter('my->endDate', '>',  Events_EventHelper::dateToStr(null, false))->order('my->startDate','asc');
        if ($eventToSkip) {
            $neg->filter('id','<>', $eventToSkip->id);
            $pos->filter('id','<>', $eventToSkip->id);
        }
        $list->setQueries($neg, $pos);
        return $list;
    }

    /**
     *  For the specified event returns prev and next events in chronological order.
     *
     *  @param      $name   type    desc
     *  @return     list<prev:Event, next:Event>
     */
    public static function getPrevNextEvents($event) {
        // We skip events starting at the same moment (very rare case). Otherwise we can get into the endless loop.
        $prev = Events_EventHelper::query('Event')->filter('my->startDate','<',$event->my->startDate)->order('my->startDate','desc')->end(1)->uniqueResult();
        $next = Events_EventHelper::query('Event')->filter('my->startDate','>',$event->my->startDate)->order('my->startDate','asc')->end(1)->uniqueResult();
        return array($prev,$next);
    }

    /**
     * Gets the $max public events which haven't yet ended in the order they start.
     *
     * @param   $max    integer     Maximum number of events to return
     * @return          array       W_Event objects sorted by start date.
     */
    public static function getEventsForFeed($max=10) {
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
        if (XG_App::appIsPrivate()) { return array(); }
        if (! $max) { $max = 10; } // null may be passed in here from config overriding default 10
        $query = XG_Query::create('Content')->filter('owner')->begin(0)->end($max)
            ->filter('type', '=', 'Event')
            ->filter('my->privacy', '=', Event::ANYONE)
            ->filter('my->endDate', '>', Events_EventHelper::dateToStr(null, false))
            ->order('my->startDate', 'asc');
        return $query->execute();
    }

    /**
     *  Returns events containing the specific terms
     *
     *  @param      $search     string      Search terms
     *  @param      $limit      int         Number of events to retrieve
     *  @return     XG_PagingList<Event>    The list of events
     */
    public static function searchEvents($search, $limit) {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $list   = new XG_PagingList($limit);
        $query  = NULL;

        if (XG_QueryHelper::getSearchMethod() == 'search') {
            try {
                $query = $list->processQuery(Events_EventHelper::query('Event','Search'));
                XG_QueryHelper::addSearchFilter($query, $search, true);
                XG_QueryHelper::addExcludeFromPublicSearchFilter($query, true);
                $list->setResult(XG_QueryHelper::contentFromSearchResults($query->execute(), false),$query->getTotalCount());
            } catch (Exception $e) {
                // do nothing
            }
        }

        if (!$query) {
            $query = $list->processQuery(Events_EventHelper::query('Event','Content'));
            XG_QueryHelper::addSearchFilter($query, $search);
            XG_QueryHelper::addExcludeFromPublicSearchFilter($query);
            $list->setResult($query->execute(), $query->getTotalCount());
        }

        return $list;
    }

//** Background callbacks
    public static function task_deleteComments($eventId, $count) { # void
		EventWidget::init();
        $res = Comment::getCommentsFor($eventId, 0, $count);
        foreach($res['comments'] as $comment) {
            // we do not use Comment::remove() because we don't need to update counters
            XN_Content::delete($comment);
        }
        if ($res['numComments'] > $count) {
            $task = array(array(__CLASS__,'task_deleteComments'), $eventId, self::$bgCount);
			XG_JobHelper::create(array($task));
        }
    }

    public static function task_deleteInvitations($eventId, $count) { # void
    	EventWidget::init();
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
        $event->id = $eventId; // stdClass
        $invitations = Events_InvitationHelper::getInvitations($event->id, $count, true);
        $ids = array();
        foreach ($invitations->getList() as $i) {
            $ids[] = $i['id'];
        }
        if ($ids) {
            XN_Invitation::delete($ids);
        }
        if ($invitations->totalCount > $count) {
            $task = array(array(__CLASS__,'task_deleteInvitations'), $event->id, self::$bgCount);
            XG_JobHelper::create(array($task));
        }
    }

//** Implementation
    /**
     *  Scans thru the calendar in order to find the date with events nearest to the specified one.
     *  If we find the required date we don't need to do an extra query to the content store.
     *
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar
     *  @param      $date       string                          YYYY-MM-DD
     *  @param      $sign       int                             Whether to search before the date (-1) or after (+1)
     *  @access     friend
     *  @return     void
     */
    public static function _findNearestDateWithEvents($calendar, $date, $sign) { # string
        $dest = '';
        foreach ($calendar as $ym=>$days) {
            if ($sign * strcmp($ym,mb_substr($date,0,7)) < 0) { // skip the useless months
                continue;
            }
            foreach (array_filter($days) as $day=>$count) {     // scan thru the days with events
                $d = $ym . sprintf('-%02d',$day);
                // if the date is less (or more) then the required, and closer then that already found, use it.
                if ($sign * strcmp($d, $date) > 0 && (!$dest || -$sign * strcmp($d, $dest) > 0)) {
                    $dest = $d;
                }
            }
        }
        return $dest;
    }

    /**
     *  Returns the first date before $date which contains events or the
     *  null if nothing is found. Used for navigation over the Calendar.
     *
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. Used to avoid extra queries if possible.
     *  @param      $date       string                          YYYY-MM-DD
     *  @return                 string|null                     YYYY-MM-DD
     */
    protected static function _getPrevDate($calendar, $date) {
        if ($prevDate = self::_findNearestDateWithEvents($calendar, $date, -1)) {
            return $prevDate;
        }

        $query  = Events_EventHelper::query('Event')
            ->filter('my->startDate', '<', XG_DateHelper::format('Y-m-d',$date))
            ->order('my->endDate','desc')
            ->end(1);
        $query->alwaysReturnTotalCount(false);
        if (!list($prev) = $query->execute()) {
            return NULL;
        }
        $prev = mb_substr($prev->my->endDate,0,10);
        // if event starts before and finishes after the date
        // just return the previous date
        return strcmp($prev, $date) >= 0 ? XG_DateHelper::format('Y-m-d',$date,'-1 day') : $prev;
    }

    /**
     *  Returns the first date after $date which contains events or the
     *  null if nothing is found. Used for navigation over the Calendar.
     *
     *  @param      $calendar   hash<yyyy-mm:hash<day:count>>   Calendar. Used to avoid extra queries if possible.
     *  @param      $date       string                          YYYY-MM-DD
     *  @return                 string|null                     YYYY-MM-DD
     */
    protected static function _getNextDate($calendar, $date) {
        if ($nextDate = self::_findNearestDateWithEvents($calendar, $date, +1)) {
            return $nextDate;
        }

        $query  = Events_EventHelper::query('Event')
            ->filter('my->endDate', '>=', XG_DateHelper::format('Y-m-d',$date,'+1 day'))
            ->order('my->startDate','asc')
            ->end(1);
        $query->alwaysReturnTotalCount(false);
        if (!list($next) = $query->execute()) {
            return NULL;
        }
        $next = mb_substr($next->my->startDate,0,10);
        // if event starts before and finishes after the date
        // just return the next date
        return strcmp($next, $date) <= 0 ? XG_DateHelper::format('Y-m-d',$date,'+1 day') : $next;
    }

//** Event handlers
    /**
     * Called before an Event is saved or deleted.
     *
     * @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     * @param   $event  W_Content               The Event
     * @param   $props  hash                    The new attribute values
     * @param   $orig   hash                    The old attribute values
     */
    public static function onBeforeEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        if ($event->id) {
            $cmd->addLock("Event-$event->id:lock");
        }
    }

    /**
     * Called after Event is deleted.
     *
     * @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     * @param   $event  W_Content               The Event
     * @param   $props  hash                    The new attribute values
     * @param   $orig   hash                    The old attribute values
     */
    public static function onAfterEventDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        self::task_deleteComments($event->id, self::$fgCount);
        self::task_deleteInvitations($event->id, self::$fgCount);
    }

    /**
     * Deletes the Event's old photo, if it is being replaced.
     *
     * @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     * @param   $event  W_Content               The Event
     * @param   $props  hash                    The new attribute values
     * @param   $orig   hash                    The old attribute values
     */
    public static function onAfterEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        if ($orig['photoId'] && $orig['photoId'] != $props['photoId']) {
            if ($photo = XN_Content::load($orig['photoId'])) {
                XN_Content::delete($photo);
            }
        }
    }
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
?>
