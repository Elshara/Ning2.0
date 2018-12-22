<?php
/** $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  Useful functions related to Events.
 *
 **/
class Events_EventHelper {

    /** Indicates that a unit test is being run. */
    static public $isUnitTest = 1; // prevent accidential access. Call EventWidget::init()

    /** Number of queries. For unit testing. */
    static public $count = 0;

    /**
     *  Cleans up a URL entered by the user.
     *  Returns an empty string if the URL is just "http://".
     *
     *  @param      $url   string
     *  @return     string the trimmed URL, with http:// prepended if missing.
     */
    public static function url($url) {
        $url = trim($url);
        if (preg_match('#^(https?://)?$#ui',$url)) {
            return '';
        }
        if (!preg_match('#^(https?|ftp)://#ui',$url)) {
            $url = 'http://'.$url;
        }
        return $url;
    }

    /**
     *  Converts the date from string to Unix timestamp
     *
     *  @param      $dateStr    string|int      Any of: Unix timestamp, YYYY-MM-DD, YYYY-MM-DD HH:II, or null (for the current time)
     *  @return     int                         Unix timestamp
     */
    public static function dateToTs($dateStr = null) {
        if ($dateStr === NULL) {
            return xg_date('U');
        }
        if (is_numeric($dateStr)) {
            return $dateStr;
        }
        if (!preg_match('/^(\d{4})-(\d\d)-(\d\d)(?: (\d\d):(\d\d))?$/u', $dateStr, $m)) {
            return 0;
        }
        return xg_mktime($m[4],$m[5],0,$m[2],$m[3],$m[1]);
    }

    /**
     *  Converts the date to a string with format YYYY-MM-DD HH:II
     *
     *  @param      $ts         int     Unix timestamp, or null for the current time
     *  @param      $showTime   boolean date as of now (not as start of day)
     *  @return     string              YYYY-MM-DD HH:II
     */
    public static function dateToStr($ts = null, $showTime = true) {
        if ($ts === NULL) {
            $ts = time();
        }
        if (!is_numeric($ts)) {
            $ts = self::dateToTs($ts);
        }
        return $showTime ? xg_date('Y-m-d H:i',$ts) : xg_date('Y-m-d',$ts);
    }

    /**
     *  Returns the string for filtering queries by type. Used with the "like" query filter.
     *
     *  @param      $type       string  The Event type
     *  @return     string              "like" argument for querying Event->my->eventType
     */
    public static function typeFilter($type) {
        return '#' . base64_encode($type) . '#';
    }

    /**
     *  Converts the internal format to a list of event-types.
     *
     *  @param      $type       string  Event-type string, such as that in Event->my->eventType
     *  @return     list<string>        Array of event types
     */
    public static function typeToList($type) {
        return $type ? array_map('base64_decode',explode('#',trim($type,'#'))) : array();
    }

    /**
     *  Converts the list of values to the internal format
     *
     *  @param      $list   list<string>    Array of event types
     *  @return     type                    Event-type string, such as that in Event->my->eventType
     */
    public static function listToType($list) {
        return '#' . join('#',array_map('base64_encode',array_unique($list))) . '#';
    }

    /**
     *  Converts a user-entered, comma-delimited list of event types
     *  to the internal format (for storing in the content store).
     *  Text format is the same as for tags.
     *
     *  @param      $text   string      Text separated by commas or quotes.
     *  @return     type                Event-type string, such as that in Event->my->eventType
     */
    public static function textToType($text) {
        return '#' . join('#',array_map('base64_encode',array_unique(XN_Tag::parseTagString($text)))) . '#';
    }

    /**
     *  Converts a status code to a value for passing in urls
     * (to make them more human-friendly)
     *
     *  @param      $rsvp   int     One of EventAttendee::___ constants
     *  @return     string          A URL parameter value, e.g., might_attend
     */
    public static function rsvpToStr($rsvp) {
        switch ($rsvp) {
            case EventAttendee::ATTENDING:      return 'attending';
            case EventAttendee::MIGHT_ATTEND:   return 'might_attend';
            case EventAttendee::NOT_ATTENDING:  return 'not_attending';
            case EventAttendee::NOT_RSVP;       return 'not_rsvped';
            default:                            return 'unknown';
        }
    }

    /**
     *  Converts a URL parameter value to a status code.
     *
     *  @param      $rsvp   string  A URL parameter value, e.g., might_attend
     *  @return     int             One of EventAttendee::___ constants
     */
    public static function strToRsvp($rsvp) { # string
        // These constants are also used in BroadcastEventMessageLink.js [Jon Aquino 2008-04-01]
        switch ($rsvp) {
            case 'attending':       return EventAttendee::ATTENDING;
            case 'might_attend':    return EventAttendee::MIGHT_ATTEND;
            case 'not_attending':   return EventAttendee::NOT_ATTENDING;
            case 'not_rsvped':      return EventAttendee::NOT_RSVP;
            default:                return 0;
        }
    }

    /**
     * Decrements the count for the given key, and removes it if it's become zero.
     *
     * @param   $arr    hash    keys and counts
     * @param   $idx    string  name of the key to decrement
     */
    public static function decrement(array& $arr, $idx) { # void
        if (!isset($arr[$idx])) {
            return;
        }
        if ($arr[$idx] > 1) {
            $arr[$idx]--;
        } else {
            unset($arr[$idx]);
        }
    }

    /**
     * Creates a content object with the given type.
     * Sets the mozzle and isPrivate attributes.
     *
     * @param   $type   string  The content type
     * @return  W_Content       The unsaved content object
     */
    public static function create($type) { # W_Content
        $object = W_Content::create($type);
        $object->my->mozzle     = W_Cache::getWidget('events')->dir;
        $object->isPrivate      = XG_App::appIsPrivate();
        return $object;
    }

    /**
     *  Updates an object's properties and saves it. Properties are automatically
     *  prefixed with my->.
     *
     *  @param      $command    Events_EventCommand Not used
     *  @param      $object     W_Content           The object to update
     *  @param      $props      hash                New properties; array values will be serialized
     *  @return     void
     */
    public static function update($command, W_Content $object, array $props) {
        // This function lives here unless we find some better place.
        foreach ($props as $k=>$v) {
            if ($k == 'title' || $k == 'description' || $k == 'isPrivate') {
                $object->$k = $v;
			} else if ($k == '_feature') {
				if ($v && XG_SecurityHelper::userIsAdmin()) {
			        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
					XG_PromotionHelper::promote($object);
				}
			} else {
                $object->my->$k = is_array($v) ? serialize($v) : $v;
            }
        }
        if (/*self::$isUnitTest && */$errs = $object->validate()) {
            var_dump($errs, $object->export());
            echo "<pre>";
            debug_print_backtrace();
            echo "</pre>";
        }
        $object->save();
    }

    /**
     *  Removes the object.
     *
     *  @param      $command    Events_EventCommand Not used.
     *  @param      $object     W_Content           The object to delete.
     *  @return     void
     **/
    public static function delete($command, W_Content $object) {
        W_Content::delete($object);
    }

    /**
     *  Creates a query for a given content type.
     *
     *  @param      $type       string      The content type
     *  @param      $method     string      Content or Search
     *  @return     XN_Query
     */
    public static function query($type, $method = 'Content') {
        switch($method) {
            case 'Content': $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', $type); break;
            case 'Search':  $query = XN_Query::create('Search')->filter('type', 'like', $type); break;
            default:        throw new Exception('Assertion failed (1668909308)');
        }
        if (self::$isUnitTest) {
            $query->filter('my->test','=','Y');
            self::$count++;
        }
        return $query;
    }

    /**
     *  Fills $keys from cache and returns the list of missed IDs. ID is any unique value.
     *
     *  @param      $keys   hash<id:cache-key>  Cache keys with arbitrary hash keys
     *  @return     list<string>                Hash keys for cache keys with no cache entries
     */
    public static function fillFromCache(array &$keys) {
        if (!XG_Cache::cacheOrderN() || self::$isUnitTest) {
            return array_keys($keys);
        }
        $res        = XN_Cache::get($keys);
        $missing    = array();
        foreach ($keys as $k=>$v) {
            if (isset($res[$v])) {
                $keys[$k] = $res[$v];
            } else {
                $missing[] = $k;
            }
        }
        return $missing;
    }

    /**
     *  The same as XN_Cache::put, but disabled during unit testing.
     *
     *  @return     void
     */
    public static function cachePut($key, $object, $labels) {
        if (XG_Cache::cacheOrderN() && !self::$isUnitTest) {
            XN_Cache::put($key, $object, $labels);
        }
    }
    /**
     *  The same as XN_Cache::insert, but disabled during unit testing
     *
     *  @return     void
     */
    public static function cacheInsert($key, $object, $labels) {
        if (XG_Cache::cacheOrderN() && !self::$isUnitTest) {
            XN_Cache::insert($key, $object, $labels);
        }
    }
}
?>
