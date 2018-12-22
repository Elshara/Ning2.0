<?php

/**
 * Useful functions for working with instances of the Page widget.
 */
class Page_InstanceHelper {

    /**
     * Returns data for the instances of the Page widget.
     *
     * @return array  array of arrays, each with title, directory, and displayTab
     */
    public static function load() {
        $data = array();
        foreach (self::pageWidgets(W_Cache::allWidgets()) as $widget) {
            $data[] = array('title' => $widget->config['title'], 'directory' => $widget->dir, 'displayTab' => ($widget->config['isEnabledDefault'] || $widget->privateConfig['isEnabled']));
        }
        return $data;
    }

    /**
     * Creates, updates, and deletes instances of the Page widget.
     *
     * @param $data array  the submitted data: an array of arrays, each with title, directory, and displayTab
     * @return array  HTML error messages keyed by instance index and field name
     */
    public static function save($data) {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        $newInstances = self::directoriesToAdd($data, array_keys(self::pageWidgets(W_Cache::allWidgets())));
        if ($errors = self::validate($data)) { return $errors; }
        foreach (self::directoriesToDelete($data, array_keys(self::pageWidgets(W_Cache::allWidgets()))) as $directory) {
            XG_TabLayout::onFeatureRemove($directory);
            XG_FileHelper::deltree($_SERVER['DOCUMENT_ROOT'] . '/instances/' . $directory);
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/xn_private/' . $directory . '-private-configuration.xml');
        }
        foreach ($data as $widgetData) {
            XG_Version::createNewInstance($widgetData['directory'], 'page', '<anyUserCanCreatePage type="number">0</anyUserCanCreatePage><usersCanComment type="number">1</usersCanComment>', 0, 1, 0);
        }
        // BAZ-7684: Make sure to rebuild the config cache after new page instance is added
        XG_App::includeFileOnce('/lib/XG_ConfigCachingApp.php');
        XG_ConfigCachingApp::rebuildData();
        foreach ($data as $widgetData) {
            $widget = W_Cache::getWidget($widgetData['directory']);
            $widget->config['title'] = $widgetData['title'];
            $displayTab = $widgetData['displayTab'] ? 1 : 0;
            $originalDisplayTab = $widget->config['isFirstOrderFeature'];
            $widget->config['isFirstOrderFeature'] = $widget->config['isEnabledDefault'] = $widget->privateConfig['isEnabled'] = $displayTab;
            $widget->saveConfig();
            if(array_key_exists($widgetData['directory'], $newInstances)){
                // handles creation of new page instances
                XG_TabLayout::onFeatureAdd($widget->dir);
            } else {
                if ($originalDisplayTab != $displayTab) {
                    // handles updates to "Display tab" setting for existing page instance
                    if ($displayTab) {
                        XG_TabLayout::onFeatureAdd($widget->dir);
                    } else {
                        XG_TabLayout::onFeatureRemove($widget->dir);
                    }
                }
            }
        }
        return array();
    }

    /**
     * Returns a list of Page directories to delete.
     *
     * @param $data array  the submitted data: an array of arrays, each with title, directory, and displayTab
     * @param $existingPageDirectories array  directories of existing Page widgets
     * @return array  existing Page directories not present in the submitted data
     */
    protected static function directoriesToDelete($data, $existingPageDirectories) {
        return array_values(array_diff($existingPageDirectories, array_map(create_function('$widgetData', 'return $widgetData["directory"];'), $data)));
    }

    /**
     * Returns a list of Page directories to add.
     * 
     * TODO make createNewInstance loop in save() smarter
     *
     * @param   $data                       array       the submitted data: an array of array(title, directory, displayTab)
     * @param   $existingPageDirectory      array       directories of existing Page widgets
     * @return                              array       array with added directories as keys for easy use of array_key_exists
     */
    protected static function directoriesToAdd($data, $existingPageDirectories) {
        return array_flip(array_values(array_diff(array_map(create_function('$widgetData', 'return $widgetData["directory"];'), $data), $existingPageDirectories)));
    }

    /**
     * Validates the data for the proposed Page widget instances.
     *
     * @param $data array  array of arrays, each with title, directory, and displayTab
     * @return array  HTML error messages keyed by instance index and field name
     */
    private static function validate($data) {
        return self::validateProper($data, self::nonPageWidgets(W_Cache::allWidgets()));
    }

    /**
     * Filters in Page widgets
     *
     * @param $widgets array  W_Widgets to filter
     * @return array  the Page widgets, keyed by directory name
     */
    protected static function pageWidgets($widgets) {
        $pageWidgets = array();
        foreach ($widgets as $widget) {
            if ($widget->root == 'page') { $pageWidgets[$widget->dir] = $widget; }
        }
        return $pageWidgets;
    }

    /**
     * Filters out Page widgets
     *
     * @param $widgets array  W_Widgets to filter
     * @return array  the non-Page widgets, keyed by directory name
     */
    protected static function nonPageWidgets($widgets) {
        $nonPageWidgets = array();
        foreach ($widgets as $widget) {
            if ($widget->root != 'page') { $nonPageWidgets[$widget->dir] = $widget; }
        }
        return $nonPageWidgets;
    }

    /**
     * Validates the data for the proposed Page widgets.
     *
     * @param $data array  array of arrays, each with title, directory, and displayTab
     * @param $nonPageWidgets array  non-Page widgets, keyed by directory name
     * @return array  HTML error messages keyed by instance index and field name
     */
    protected static function validateProper($data, $nonPageWidgets) {
        $errors = array();
        $lowercaseNonPageDirectories = array_map(create_function('$nonPageWidget', 'return mb_strtolower($nonPageWidget->dir);'), $nonPageWidgets);
        $lowercaseDirectories = array();
        $i = 0;
        foreach ($data as $instance) {
            if (! mb_strlen($instance['title'])) { $errors[$i]['title'] = xg_html('PLEASE_ENTER_TITLE_FOR_TAB'); }
            $lowercaseDirectory = mb_strtolower($instance['directory']);
            if (mb_strlen($lowercaseDirectories[$lowercaseDirectory])) { $errors[$i]['directory'] = xg_html('DIRECTORY_CANNOT_APPEAR_MORE'); }
            if (! mb_strlen($instance['directory'])) { $errors[$i]['directory'] = xg_html('PLEASE_ENTER_DIRECTORY'); }
            if (preg_match('@[^a-z0-9_]@ui', $instance['directory'])) { $errors[$i]['directory'] = xg_html('DIRECTORY_CAN_ONLY_CONTAIN'); }
            if ($lowercaseNonPageDirectories[$lowercaseDirectory])  { $errors[$i]['directory'] = xg_html('DIRECTORY_NAME_RESERVED'); }
            $lowercaseDirectories[$lowercaseDirectory] = $lowercaseDirectory;
            $i++;
        }
        if (! $lowercaseDirectories['page']) {
            $errors[0]['directory'] = xg_html('ONE_DIRECTORY_MUST_BE');
        }
        return $errors;
    }

}
