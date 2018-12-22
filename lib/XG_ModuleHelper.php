<?php

/**
 *  Functions for working with modules (mozzles)
 */

class XG_ModuleHelper {

    /**
     *   Get widget objects for all widgets containing mozzles which
     *     are enabled in this app
     *
     * @return array  an array of widget instance name (widget directory) to W_Widget objects
     */
    public static function getEnabledModules() {
        $moduleInfo = self::getModuleInfo();
        return $moduleInfo['enabled'];
    }


    /**
     *   Get widget objects for all widgets containing mozzles which
     *     are disabled in this app
     */
    public static function getDisabledModules() {
        $moduleInfo = self::getModuleInfo();
        return $moduleInfo['disabled'];
    }


    /**
     *   Get widget objects for all widgets containing mozzles in this app
     */
    public static function getAllModules() {
        $moduleInfo = self::getModuleInfo();
        return $moduleInfo['enabled'] + $moduleInfo['disabled'];
    }


    /**
     *   Get the widget objects for the widgets containing the mozzle
     *     with the specified name
     *
     * @param string name Module name
     */
    public static function getModule($name) {
        $moduleInfo = self::getModuleInfo();
        if (isset($moduleInfo['enabled'][$name])) {
            return $moduleInfo['enabled'][$name];
        }
        return NULL;
    }

    /**
     * Denotes that nav entries were generated from TabManager and should be rendered
     * using TabManager layout
     */
    const TABS_TAB_MANAGER = 'tabManager';

    /**
     * Denotes that nav entries were generated from the installed widgets and should be rendered using that logic
     */
    const TABS_WIDGETS = 'tabWidgets';

    /**
     * Determine the tabKey that should be highlighted, based on the current page and the tab layout
     * 
     * If a subtab matches, we highlight its parent tab to maintain the highlight on a tab that is always visible
     * Two classes of matches are required to account for the potential specicificity of TabManager links:
     * 
     * First, highlighting preference is given to tabs and sub tabs that have urls matching the current url.
     * This accounts for tabs and sub tabs that link to pages as well as deep links into a mozzle (for example, a tab link to a specific video)
     * This method is fragile; for example, pages that have url mappings like /profiles/Username do not match the REQUEST_URI
     * TODO: find a better way to highlight pages and deep links [dkf 2008-09-01]
     *
     * Second, if no url matches are found, highlight based on the previous method, checking if the tabKey (which is derived from module->dir)
     * matches the string passed into xg_header (which is normally module->dir)
     *
     * @param   $navEntries     array       result of (XG_ModuleHelper::getNavEntriesFromTabManager())['tabs']
     * @param   $highlight      string      the module->dir passes to xg_header to highlight
     * @return                  string      tabKey to highlight
     */
    public static function getNavHighlightForTabManager($tabs, $highlight) {
        $currentUrl = XG_HttpHelper::currentUrl();
        $mozzleMatch = false;
        foreach ($tabs as $tab) {
            if (xg_absolute_url($tab['url']) == $currentUrl) {
                return $tab['tabKey'];
            } elseif ($highlight == $tab['tabKey']) {
                $mozzleMatch = $tab['tabKey'];
            } elseif (count($tab['subTabs']) > 0) {
                foreach ($tab['subTabs'] as $subTab) {
                    if (xg_absolute_url($subTab['url']) == $currentUrl) {
                        return $tab['tabKey'];
                    } elseif ($highlight == $subTab['tabKey']) {
                        $mozzleMatch = $tab['tabKey'];
                    }
                }
            }
        }
        if($mozzleMatch) {
            return $mozzleMatch;
        } else {
            return $highlight;
        }
    }

    /**
     *   Returns an array describing the navigation links to be displayed
     *     based on the logged in user and the current set of enabled mozzles
     *
     *  @param $isAdmin boolean         (optional) is the current user an administrator?
     *
     * @return                  array of arrays of arrays
     *                          array(  'method' => which tab method we're using,
     *                                  'tabs' => array(array()...)
     */
    public static function getNavEntries($isAdmin = false) {
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        $tabLayout = XG_TabLayout::loadOrCreate(false);
        if ($tabLayout){
            return self::getNavEntriesFromTabManager($tabLayout, $isAdmin);
        } else {
            return self::getNavEntriesFromWidgets($isAdmin);
        }
    }

    /**
     * Returns nav entries visible to the current user from TabManager(tm)
     *
     * @param   $tabLayout      XG_TabLayout object     the current XG_TabLayout
     * @param   $isAdmin        boolean                 is the user an admin?
     */
    public static function getNavEntriesFromTabManager($tabLayout, $isAdmin = false) {
        $visibility = $isAdmin ? XG_Tab::TAB_VISIBILITY_ADMIN :
                        (XN_Profile::current()->isLoggedIn() ? XG_Tab::TAB_VISIBILITY_MEMBER : XG_Tab::TAB_VISIBILITY_ALL);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_TablayoutHelper.php');
        $navEntries = $tabLayout->getNestedTabStructure($visibility, Index_TablayoutHelper::MAX_TABS, Index_TablayoutHelper::MAX_SUBTABS_PER_TAB, Index_TablayoutHelper::$fixedTabsTop, Index_TablayoutHelper::$fixedTabsBottom);

        return array('method' => self::TABS_TAB_MANAGER, 'tabs' => $navEntries, 'subTabColors' => $tabLayout->getSubTabColors());
    }

    /**
     * Return a set of modules 'instance' => W_Widget in order based on the ordering
     * specified by 'instance' => weight.  The default will use addFeaturesSortOrder.
     * If an instance without a weight is sorted at the end.  In the case of a tie,
     * the instance name is used to determine ordering.
     *
     * @param $enabledModules Array(string => W_Widget)  input modules to sort
     * @param $ordering Array(string => integer)  a map of instance key to weight
     *
     * @return Array(string => W_Widget)  the input set of modules in sorted order
     */
    protected static function _getModulesInSortOrder($enabledModules, $ordering = null) {
        if (is_null($ordering)) {
            XG_App::includeFileOnce('/lib/XG_ConfigHelper.php');
            $main = W_Cache::getWidget('main');
            $ordering = unserialize($main->config[XG_ConfigHelper::ADD_FEATURES_SORT_ORDER_KEY]);
            if (! $ordering) { $ordering = array(); }
        }
        uksort($enabledModules, array(new XG_ModuleSortWrapper($ordering), 'moduleSortCmp'));
        return $enabledModules;
    }

    /**
     * Returns a list of instance names of first order features that should never have tabs (fake first order features)
     *
     * @return array  an array of instance names of fake first order features
     */
    public static function getFakeFirstOrderFeatures() {
        return array('profiles', 'activity', 'music', 'opensocial');
    }

    /**
     *   Returns an array describing the navigation links to be displayed
     *     based on the logged in user and the current set of enabled mozzles
     *
     *  @return array of arrays of (display name, link, key (for highlight))
     */
    public static function getNavEntriesFromWidgets($isAdmin = false) {
        $loggedIn = XN_Profile::current()->isLoggedIn();
        $mainWidget = W_Cache::getWidget('main');
        if (isset($mainWidget->config['navEntries'])) {
            return unserialize($widget->config['navEntries']);
        }
        $adminWidget = W_Cache::getWidget('admin');
        $profilesWidget = W_Cache::getWidget('profiles');
        // Default nav includes all enabled modules
        $navEntries = array();
        $navEntries[] = array(xg_text('MAIN_TAB_TEXT'), $mainWidget->buildUrl('index', 'index'), 'main');
        if (XG_App::canSeeInviteLinks(XN_Profile::current())) {
            $navEntries[] = array(xg_text('INVITE_TAB_TEXT'), '/invite', 'invite');
        }
        $navEntries[] = array(xg_text('MY_PAGE_TAB_TEXT'), $profilesWidget->buildUrl('index', 'index'), 'profile');
        $navEntries[] = array(xg_text('MEMBERS_TAB_TEXT'), $profilesWidget->buildUrl('members', ''), 'members');
        $enabledModules = self::_getModulesInSortOrder(self::getEnabledModules());
        foreach ($enabledModules as $name => $module) {
            if (isset($module->config['isFirstOrderFeature']) && $module->config['isFirstOrderFeature']) {
                if (! in_array($module->dir, self::getFakeFirstOrderFeatures())) {
                    $navEntries[] = array($module->title, $module->buildUrl('index', 'index'), $module->dir);
                } else if ($module->dir === 'profiles') {
                    if ($module->config['showBlogsTab'] == 1) {
                        $navEntries[] = array(xg_text('BLOGS_TAB_TEXT'), $module->buildUrl('blog', 'list'), 'blogs');                             
                    }
                }
            }
        }
        if ($isAdmin) {
            $navEntries[] = array(xg_text('MANAGE_TAB_TEXT'), $mainWidget->buildUrl('admin', 'manage'), 'manage');
        }
        return array('method' => self::TABS_WIDGETS, 'tabs' => $navEntries);
    }

    protected static function getModuleInfo() {
        // Discover all available modules
        static $enabledModules = array();
        static $disabledModules = array();

        if (count($enabledModules) == 0 && count($disabledModules) == 0) {
            foreach (W_Cache::allWidgets() as $widget) {
                if ($widget->config['isMozzle']) {
                    //  Every module should have a title and a display name but
                    //    use a reasonable default just in case
                    if (isset($widget->config['displayName'])) {
                        $widget->displayName = $widget->config['displayName'];
                    }
                    else {
                        $widget->displayName = ucwords($widget->dir);
                    }
                    if (isset($widget->config['title'])) {
                        $widget->title = XG_LanguageHelper::translateDefaultWidgetTitle($widget->config['title']);
                    }
                    else {
                        $widget->title = $widget->displayName;
                    }
                    $widget->description = $widget->config['description'];

                    //  Determine which modules are enabled
                    //  isEnabledDefault only matters if isEnabled isn't set
                    //  NOTE: isEnabled is a string so that we can determine whether
                    //    it's been set
                    if ($widget->privateConfig['isEnabled']
                            || (mb_strlen($widget->privateConfig['isEnabled']) == 0 && $widget->config['isEnabledDefault'])
                            // Second-order features like the RSS and HTML widgets are always enabled, regardless of their isEnabled setting [Jon Aquino 2007-05-07]
                            || ! $widget->config['isFirstOrderFeature']) {
                        $enabledModules[$widget->dir] = $widget;
                    }
                    else {
                        $disabledModules[$widget->dir] = $widget;
                    }
                } // isMozzle
            } // foreach
        }

        return array('enabled' => $enabledModules, 'disabled' => $disabledModules);

    } // getModuleInfo()

    /**
     * Run an action in all enabled modules, taking into account any special
     * rules that are relevant for group-enabled modules
     *
     * @param $controller string The controller name to use (e.g. "index")
     * @param $action string The action name to call (e.g. "searchConfiguration")
     * @param $args array optional array of arguments to pass to the action
     */
    public static function dispatchToEnabledModules($controller, $action, $args = null) {
        $enabledWidgets = XG_ModuleHelper::getEnabledModules();
        /* If groups is enabled, make sure to talk to widget instances that are
         * group-aware but could be inactive on the top level */
        if ($enabledWidgets['groups']) {
            foreach (XG_GroupHelper::groupEnabledWidgetInstanceNames() as $groupEnabledWidgetInstanceName) {
                $enabledWidgets[$groupEnabledWidgetInstanceName] = W_Cache::getWidget($groupEnabledWidgetInstanceName);
            }
        }
        foreach ($enabledWidgets as $widgetName => $widget) {
            if ($widget->controllerHasAction($controller,$action)) {
                list($r,$html) = $widget->dispatch($controller,$action,$args);
            }
        }
    }

}

/**
 * module sort function wrapper class - provides a method to sort an array of modules using
 * the module instance name and a reference array containing instance name weights.
 */
class XG_ModuleSortWrapper {
    private $ordering = array();

    function XG_ModuleSortWrapper($ordering) {
        $this->ordering = $ordering;
    }

    function moduleSortCmp($a, $b) {
        $aWeight = array_key_exists($a, $this->ordering) ? $this->ordering[$a] : null;
        $bWeight = array_key_exists($b, $this->ordering) ? $this->ordering[$b] : null;
        if (is_null($aWeight) && is_null($bWeight)) { return strcmp($a, $b); }
        else if (is_null($aWeight)) { return 1; }
        else if (is_null($bWeight)) { return -1; }
        if ($aWeight === $bWeight) { return strcmp($a, $b); }
        else if ($aWeight > $bWeight) { return 1; }
        else { return -1; }  // $aWeight < $bWeight
    }
}
