<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  Record of the number of events for each day of a given month.
 *
 *  @cache_key      EventCalendar-$ym:cache     cached EventCalendar object
 *  @cache_label    EventCalendar-$ym           invalidates EventCalendar object
 *  @cache_lock     EventCalendar-$ym:lock      wraps code that modifies EventCalendar
 *
 **/
class EventCalendar extends W_Model {
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
     *  YYYY-MM (TZ-independent)
     *
     *  @var XN_Attribute::STRING
     *  @rule length 7,7
     */
    public $month;

    /**
     *  Serialized counters with the format "day_number => count". Only the days with events are stored, so
     *  you need to add empty dates if you want to get the full month.
     *
     *  @var XN_Attribute::STRING
     */
    public $eventCounts;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/

    /**
     *  Constructor.
     */
    public function  __construct() {
        EventWidget::init();
    }

    /**
     *  Returns the event-calendar data for the specified period.
     *  Event dates are timezone-agnostic.
     *
     *  @param      $start      string  YYYY-MM
     *  @param      $end        string  YYYY-MM
     *  @return     hash<yyyy-mm:hash<day:count>>   Event counts, keyed by YYYY-MM and D
     */
    public static function getCalendar($start, $end) {
        if (!preg_match('/^\d{4}-\d\d$/u',$start) || !preg_match('/^\d{4}-\d\d$/u',$end)) {
            throw new Exception("Wrong start/end");
        }

        $calendars = array();
        foreach(XG_DateHelper::monthRange($start, $end) as $ym) {
            $calendars[$ym] = self::cacheKey($ym);
        }
        if (!$calendars) {
            return array();
        }
        // Fetch them and the request the missing ones from content store
        if ($missed = array_flip(Events_EventHelper::fillFromCache($calendars))) {
            foreach (Events_EventHelper::query('EventCalendar')->filter('my->month', 'in', array_keys($missed))->execute() as $ec) {
                $calendars[$ec->my->month] = $ec;
                Events_EventHelper::cacheInsert(self::cacheKey($ec->my->month), $ec, 'EventCalendar-'.$ec->my->month);
                unset($missed[$ec->my->month]);
            }
            foreach ($missed as $ym=>$tmp) {
                Events_EventHelper::cacheInsert(self::cacheKey($ym), 'no-data', 'EventCalendar-'.$ym);
            }
        }

        // Create output with all months and all days
        $res = array();
        foreach ($calendars as $ym => $value) {
            list($y,$m) = explode('-',$ym);
            $monthData  = array_fill(1,XG_DateHelper::lastDay($y,$m),0);
            if (is_object($value)) {
                foreach(unserialize($value->my->eventCounts) as $day=>$cnt)
                    $monthData[$day] = $cnt;
            }
            $res[$ym] = $monthData;
        }
        return $res;
    }

    /**
     *  Returns the calendar for the current and next months
     *
     *  @return     hash<yyyy-mm:hash<day:count>>   Event counts, keyed by YYYY-MM and D
     */
    public static function getDefaultCalendar() {
        return self::getCalendar(xg_date('Y-m'),xg_date('Y-m','+1 month'));
    }

    /**
     *  Label for invalidating the cache for an EventCalendar object.
     */
    public static function cacheKey($ym) { # string
        return "EventCalendar-$ym:cache";
    }

//** Event handlers
    /**
     *  Called before an event is saved or deleted.
     *
     *  @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     *  @param   $event  W_Content               The Event
     *  @param   $props  hash                    The new attribute values
     *  @param   $orig   hash                    The old attribute values
     *  @return  void
     */
    public static function onBeforeEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        $newStart   = @$props['startDate'];
        $newEnd     = @$props['endDate'];

        $oldStart   = @$orig['startDate'];
        $oldEnd     = @$orig['endDate'];

        // if nothing significant happened, return
        if ($newStart == $oldStart && $newEnd == $oldEnd) {
            $cmd->calendarUpdate = 0;
            return;
        }

        $months = array_unique( array_merge(
                XG_DateHelper::monthRange(mb_substr($oldStart,0,7),mb_substr($oldEnd,0,7)),
                XG_DateHelper::monthRange(mb_substr($newStart,0,7),mb_substr($newEnd,0,7)) ) );

        $cmd->calendarUpdate    = 1;
        $cmd->calendars         = array();
        foreach ($months as $ym) {
            $k = self::cacheKey($ym);
            $cmd->calendars[$ym] = $k;
            $cmd->addLock("$k:lock");
        }
        return;
    }

    /**
     *  Called after an event is saved or deleted.
     *
     *  @param   $cmd    Events_EventCommand     Command object with locking support (mini-transaction).
     *  @param   $event  W_Content               The Event
     *  @param   $props  hash                    The new attribute values
     *  @param   $orig   hash                    The old attribute values
     *  @return  void
     */
    public static function onEventSaveOrDelete(Events_EventCommand $cmd, W_Content $event, array $props, array $orig) { # void
        if (!$cmd->calendarUpdate) {
            return;
        }
        $calendars  = $cmd->calendars;

        // Load existing calendar objects
        if ($missed = Events_EventHelper::fillFromCache($calendars)) {
            foreach(Events_EventHelper::query('EventCalendar')->filter('my->month', 'in', $missed)->execute() as $ec) {
                $calendars[$ec->my->month] = $ec;
            }
        }
        // Unserialize counters and create missing calendars
        $cnt = array();
        foreach ($calendars as $ym=>$cal) {
            if (!is_object($cal)) {
                $cnt[$ym]           = array();
                $cal                = Events_EventHelper::create('EventCalendar');
                $cal->my->month     = $ym;
                $calendars[$ym]     = $cal;
            } else {
                $cnt[$ym]           = unserialize($cal->my->eventCounts);
                $calendars[$ym]     = W_Content::create($cal);
            }
        }
        // decrease old counters
        foreach (XG_DateHelper::dayRange($orig['startDate'],$orig['endDate']) as $day) {
            list($y,$m,$d) = explode('-',$day);
            Events_EventHelper::decrement($cnt["$y-$m"], intval($d));
        }
        // increase new counters
        foreach (XG_DateHelper::dayRange($props['startDate'],$props['endDate']) as $day) {
            list($y,$m,$d) = explode('-',$day);
            $cnt["$y-$m"][intval($d)]++;
        }
        // update calendars
        foreach ($calendars as $ym=>$cal) {
            if ($cnt[$ym]) {
                Events_EventHelper::update(NULL, $cal, array('eventCounts'=>$cnt[$ym]));
            } else {
                Events_EventHelper::delete(NULL, $cal);
            }
            XN_Cache::invalidate('EventCalendar-'.$ym);
        }
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
?>
