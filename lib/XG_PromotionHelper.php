<?php

/**
 * XG_PromotionHelper assists widgets with promoting content,
 * removing promotion from content, and querying for promoted content.
 *
 * Promotion status is stored in a widget attribute (owned by the index
 * widget) called 'promotedOn'. This is the date when the content was
 * promoted. When promotion is removed from a content object, its promotedOn
 * attribute is unset
 *
 */
class XG_PromotionHelper {

    /**
     * Whether queries for featured content should execute.
     * Can be used to switch off all such queries in case of performance problems (BAZ-6713).
     */
    protected static $queriesEnabled = true;

    /**
     * Returns whether queries for featured content should execute.
     * Can be used to switch off all such queries in case of performance problems.
     *
     * @return boolean whether  queries for promoted content are enabled
     */
    public static function areQueriesEnabled() {
        if ($_GET['test_baz_6713']) { return false; }
        return self::$queriesEnabled;
    }

    /**
     * Promote a content object. The object must be explicitly saved after
     * promoting.
     *
     * @param $content XN_Content|W_Content The content object to promote
     */
    public static function promote($content) {
        $content->my->set(self::attributeName(), gmdate('Y-m-d\TH:i:s\Z'), XN_Attribute::DATE);
        XN_Event::fire('feature/after', array($content));
    }

    /**
     * Add a promoted event to the latest activity list
     *
     * @param string $type Type of event being featured
     * @param XN_Content $content The content object for the item being featured
     *
     * @todo Tie this directly to promote() - need to figure out how to determine type reliably
     */
    public static function addActivityLogItem($type, $content) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(
            XG_ActivityHelper::CATEGORY_FEATURE,
            $type,
            $content->contributorName,
            array($content)
        );
    }


    /**
     * Remove promotion from a content object. The object must be explicitly
     * saved after promotion removal.
     *
     * @param $content XN_Content|W_Content The content object to demote
     */
    public static function remove($content) {
        XN_Event::fire('unfeature/before', array($content));
        $content->my->remove(self::attributeName());
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::removeFromActivityLogItem($content);
    }

    /**
     * Returns whether the content object is promoted
     *
     * @param XN_Content|W_Content $content The content object to check
     * @return boolean  whether the content object is featured
     */
    public static function isPromoted($content) {
        return ! is_null(self::promotedOn($content));
    }

    /**
     * Get the time a content object was promoted
     *
     * @param $content XN_Content|W_Content The content object to check
     * @return integer|null Returns an epoch timestamp representing promoted time
     *                      or null if the object is not promoted
     */
    public static function promotedOn($content) {
        $promotedOn = $content->my->raw(self::attributeName());
        return mb_strlen($promotedOn) ? strtotime($promotedOn) : null;
    }

    /**
     * Modify a query so that only promoted objects are retrieved
     * @param $query XN_Query
     * @return XN_Query
     */
    public static function addPromotedFilterToQuery($query) {
        return $query->filter('my->'.self::attributeName(), '<>', null, XN_Attribute::DATE);
    }

    /**
     * Modify a query so that only unpromoted objects are retrieved
     * @param $query XN_Query
     * @return XN_Query
     */
    public static function addUnpromotedFilterToQuery($query) {
        return $query->filter('my->'.self::attributeName(), '=', null, XN_Attribute::DATE);
    }

    /** The attribute name suffix where promotion is stored */
    const attributeNameSuffix = 'promotedOn';

    /**
     * Get the widget-prefixed attribute name where promoted time is stored
     * @return string The attribute name
     */
    public static function attributeName() {
        $widget = W_Cache::getWidget('main');
        return XG_App::widgetAttributeName($widget, self::attributeNameSuffix);
    }

    /**
     * Returns whether the current user is allowed to promote or unpromote the given object.
     *
     * @param $object XN_Content  The object to promote or unpromote
     * @return boolean  Whether permission is granted
     */
    public static function currentUserCanPromote($object) {
        return XG_SecurityHelper::userIsAdmin() && ($object->my->visibility == 'all' || is_null($object->my->visibility));
    }

}
