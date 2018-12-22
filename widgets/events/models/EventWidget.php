<?php
/**	$Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *	The singleton object representing the Events widget.
 *      - keeps track of all event types.
 *      - keeps track of the min/max event date (in the case of event deletion, these dates ARE NOT UPDATED - they are like a low-level mark and a high-level mark)
 *
 *	@cache_key		EventWidget:cache		cached EventWidget object
 *	@cache_label	EventWidget				invalidates event widget cache
 *	@cache_lock		EventWidget:lock		wraps code that modifies EventWidget object
 *
 **/
class EventWidget extends W_Model {

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
     * Serialized event-type stats. eventType=>count
     *
     * @var XN_Attribute::STRING
     */
    public $eventTypes;

    /**
     * Earliest date with events (actually it's the low-level mark, because if an event is deleted, this information is not updated)
     *
     * @var XN_Attribute::STRING optional
     */
    public $eventMinDate;

    /**
     * Lattermost date with events (actually it's the high-level mark, because if an event is deleted, this information is not updated)
     *
     * @var XN_Attribute::STRING optional
     */
    public $eventMaxDate;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/

    /** Whether the Events widget has been initialized. */
    static protected $inited = 0;

    /** The singleton EventWidget content object. */
    static protected $widget;

    /**
     *  Constructor
     */
    public function  __construct() {
        EventWidget::init();
    }

    /**
     *  Returns a list of all event types existing in the widget.
     *
     *  @return     hash<name:count>    Event counts keyed by event name
     */
    public static function getEventTypes() {
        $obj = self::_getWidget(TRUE);
        return $obj ? unserialize($obj->my->eventTypes) : array();
    }

    /**
     *  Returns min and max dates where events are present. Dates are returned in format YYYY-MM-DD HH:II.
     *  For the empty Event widget, empty dates are returned.
     *  These dates are low-level and high-level marks, because if an event is deleted, the dates are not updated.
     *
     *  @return     list<min,max>       The YYYY-MM-DD HH:II dates
     */
    public static function getMinMaxEventDates () {
        $obj = self::_getWidget(TRUE);
        return $obj ? array($obj->my->eventMinDate,$obj->my->eventMaxDate) : array('','');
    }

//** Implementation
    /**
     * Called before an Event is saved or deleted.
     *
     * @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     * @param   $event  W_Content               The Event
     * @param   $props  hash                    The new attribute values
     * @param   $orig   hash                    The old attribute values
     */
    public static function onBeforeEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        $cmd->widgetUpdate = 0;

        if ($props['eventType'] != $orig['eventType']) {
            $cmd->widgetUpdate = 1;
        } else {
            list($min,$max) = self::getMinMaxEventDates();
            if ($props['startDate'] && $props['startDate'] && (!$min || strcmp($props['startDate'],$min) < 0)) {
                $cmd->widgetUpdate = 1;
            } elseif ($props['endDate'] && strcmp($props['endDate'],$max) > 0) {
                $cmd->widgetUpdate = 1;
            }
        }

        if ($cmd->widgetUpdate) {
            $cmd->addLock('EventWidget:lock');
        }
    }

    /**
     * Called before an Event is saved or deleted; updates the event-type counters.
     *
     * @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     * @param   $event  W_Content               The Event
     * @param   $props  hash                    The new attribute values
     * @param   $orig   hash                    The old attribute values
     */
    public static function onEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        if (!$cmd->widgetUpdate) {
            return;
        }

        if (!$widget = self::_getWidget(FALSE)) {
            $widget = Events_EventHelper::create('EventWidget');
            $types = array();
        } else {
            $types	= unserialize($widget->my->eventTypes);
            foreach (Events_EventHelper::typeToList($orig['eventType']) as $t) {
                Events_EventHelper::decrement($types, $t);
            }
        }

        foreach (Events_EventHelper::typeToList($props['eventType']) as $t) {
            $types[$t]++;
        }
        Events_EventHelper::update(NULL, $widget, array(
            'eventTypes'	=> $types,
            'eventMinDate'	=> !$widget->my->eventMinDate || ($props['startDate'] && strcmp($props['startDate'],$widget->my->eventMinDate)<0)
                                ? $props['startDate']
                                : $widget->my->eventMinDate,
            'eventMaxDate'	=> strcmp($props['endDate'],$widget->my->eventMaxDate)>0
                                ? $props['endDate']
                                : $widget->my->eventMaxDate,
        ));
        Events_EventHelper::cachePut('EventWidget:cache', W_Content::unwrap($widget), 'EventWidget');
    }

    /**
     * Returns the EventWidget object.
     *
     * @param   $readOnly       boolean     false to load the object if needed
     * @return  W_Content       the EventWidget, or null if it hasn't been loaded and $readOnly is true
     */
    protected static function _getWidget($readOnly) { # W_Content
        if ($readOnly && self::$widget !== false ) {
            return self::$widget;
        }
        $widget = array('x'=>'EventWidget:cache');
        if (Events_EventHelper::fillFromCache($widget)) {
            if ($widget['x'] = reset(Events_EventHelper::query('EventWidget')->execute())) {
                Events_EventHelper::cachePut('EventWidget:cache', $widget['x'], 'EventWidget');
            }
        }
        return self::$widget = $widget['x'] ? W_Content::create($widget['x']) : NULL;
    }

    /**
     *  Initializes the Events widget. Must be called upon any request to EventsWidget.
     *  Add your initialization code here.
     *
     *  @return     void
     */
    public static function init() { # void
        // must be here, because ::init() is called multiple times from tests
        self::$widget = false;

        if (self::$inited) {
            return;
        }

        self::$inited = 1;
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventCommand.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_BroadcastHelper.php');
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        XG_App::includeFileOnce('/lib/XG_DateHelper.php');
        XG_App::includeFileOnce('/lib/XG_PagingList.php');
		XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        Events_BroadcastHelper::init();

        // EventHelper
        Events_EventHelper::$isUnitTest = defined('UNIT_TESTING');

        // Event
        Events_EventCommand::register('event.save','Event::onBeforeEventSaveOrDelete','Events_EventHelper::update',NULL);
        Events_EventCommand::register('event.save',NULL,NULL,'Event::onAfterEventSaveOrDelete');
        Events_EventCommand::register('event.delete','Event::onBeforeEventSaveOrDelete','Events_EventHelper::delete',NULL);
        Events_EventCommand::register('event.delete',NULL,NULL,'Event::onAfterEventSaveOrDelete');
        Events_EventCommand::register('event.delete',NULL,NULL,'Event::onAfterEventDelete');

        // EventWidget
        Events_EventCommand::register('event.save','EventWidget::onBeforeEventSaveOrDelete','EventWidget::onEventSaveOrDelete',NULL);
        Events_EventCommand::register('event.delete','EventWidget::onBeforeEventSaveOrDelete','EventWidget::onEventSaveOrDelete',NULL);

        // Calendar
        Events_EventCommand::register('event.save','EventCalendar::onBeforeEventSaveOrDelete','EventCalendar::onEventSaveOrDelete',NULL);
        Events_EventCommand::register('event.delete','EventCalendar::onBeforeEventSaveOrDelete','EventCalendar::onEventSaveOrDelete',NULL);

        // EventAttendee
        $r = new EventAttendee;
        Events_EventCommand::register('event.save','EventAttendee::onBeforeEventSaveOrDelete',NULL,'EventAttendee::onAfterEventSaveOrDelete');
        Events_EventCommand::register('event.delete','EventAttendee::onBeforeEventSaveOrDelete',NULL,'EventAttendee::onAfterEventSaveOrDelete');
        if (Events_EventHelper::$isUnitTest) {
            EventAttendee::$fgCount = 1;
            EventAttendee::$bgCount = 1;
        }
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
?>
