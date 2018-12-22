<?php

/**
 * Contains helper functions to do with widget config.
 */
class XG_ConfigHelper {

    const ADD_FEATURES_SORT_ORDER_KEY = 'addFeaturesSortOrder';

    // Ordering for add features page.  Names match those in FeatureController.
    // New widgets should be added to this list with an appropriate value and
    // new and old networks will update automatically.
    protected static $ADD_FEATURES_SORT_ORDER = array(
        'activity'      => 10,
        '_description'  => 20,
        '_members'      => 30,
        'photo'         => 40,
        'video'         => 50,
        'forum'         => 60,
        'events'        => 70,
        'groups'        => 80,
        'profiles'      => 90,
        'chat'          => 100,
        'music'         => 110,
        'notes'         => 120,
        'html'          => 130,
        'feed'          => 140,
        '_badges'       => 150,
        '_ads'          => 160,
        '_createdBy'    => 170,
        'gadgets'       => 180, //TODO: Remove? [Thomas David Baker 2008-10-04]
    );

    /**
     * Replace the network's current add features sort order, if any, with the
     * ordering defined above.
     */
    public static function resetAddFeaturesSortOrder() {
        $main = W_Cache::getWidget('main');
        $main->config[self::ADD_FEATURES_SORT_ORDER_KEY] = serialize(self::$ADD_FEATURES_SORT_ORDER);
        $main->saveConfig();
    }

    /**
     * Load the current addFeaturesSortOrder configuration and add any
     * values from the canonical order that do not already appear in the
     * sort order.  Existing values, customized or not, are not affected.
     * If the sort order does not exist at all a copy of the canonical
     * sort order is added to the main widget config.
     *
     * Used to determine sort order of features on the Add Features page.
     *
     * The canonical order contained in this routine should be updated
     * with a key->value pair for the any new widget type that is added
     * to Bazel.  If it is not added the new widget will sort dead last
     * on the Add Features page.
     */
    public static function updateAddFeaturesSortOrder() {
        $widget = W_Cache::getWidget('main');
        $currentOrder = unserialize($widget->config[self::ADD_FEATURES_SORT_ORDER_KEY]);
        if (! $currentOrder) { $currentOrder = array(); }
        // Pass in $currentOrder last so that we don't overwrite existing values.
        $newOrder = array_merge(self::$ADD_FEATURES_SORT_ORDER, $currentOrder);
        if ($currentOrder != $newOrder) {
            $widget->config[self::ADD_FEATURES_SORT_ORDER_KEY] = serialize($newOrder);
            $widget->saveConfig();
        }
    }
}
