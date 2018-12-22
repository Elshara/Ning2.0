<?php

// Neither XG_Tab nor XG_TabLayout are used independently of the other, hence they are combined here
class XG_Tab {

    /** Indicator that everyone is allowed to see the tab. */
    const TAB_VISIBILITY_ALL = 'all';

    /** Indicator that only network members are allowed to see the tab. */
    const TAB_VISIBILITY_MEMBER = 'member';

    /** Indicator that only network administrators are allowed to see the tab. */
    const TAB_VISIBILITY_ADMIN = 'admin';

    public $tabKey;
    public $url;
    public $label;
    public $visibility;
    public $windowTarget;
    public $isSubTab;
    public $pageId;

    /**
     * constructor
     *
     * @param tabKey string  unique tab identifier; for ning tabs it's the instance name
     * @param url string  url to access when clicking this tab
     * @param label string  the display label for the tab
     * @param visibility string  the minimum visibility level required to see the tab
     * @param windowTarget string  optional window target
     * @param isSubTab boolean  is this a sub-tab
     * @param pageId string  optional; if this tab references a created page store its id
     *
     * @return XG_Tab  the tab
     */
    function XG_Tab($tabKey, $url, $label, $visibility = self::TAB_VISIBILITY_ALL, $windowTarget = null, $isSubTab = false, $pageId = null) {
        $this->tabKey = $tabKey;
        $this->url = $url;
        $this->label = $label;
        $this->visibility = $visibility;
        $this->windowTarget = $windowTarget;
        $this->isSubTab = $isSubTab;
        $this->pageId = $pageId;
    }

    /**
     * returns true if the tab is visible given the specified visibility level
     *
     * @param userVisibility string  the user's current visibility level
     *
     * @return boolean  is the tab visible to the user?
     */
    public function isVisible($userVisibility) {
        // admin sees all
        if (($userVisibility === self::TAB_VISIBILITY_ADMIN) && in_array($this->visibility, array(self::TAB_VISIBILITY_ALL, self::TAB_VISIBILITY_MEMBER, self::TAB_VISIBILITY_ADMIN))) { return true; }
        // members see all but admin
        if (($userVisibility === self::TAB_VISIBILITY_MEMBER) && in_array($this->visibility, array(self::TAB_VISIBILITY_ALL, self::TAB_VISIBILITY_MEMBER))) { return true; }
        // all sees only tabs visible to all
        return (($userVisibility === self::TAB_VISIBILITY_ALL) && ($userVisibility === $this->visibility));
    }

    /**
     * returns true if the tab is visible to the specified user
     *
     * @param $user XN_Profile  the user to check
     *
     * @return boolean  is the tab visible?
     */
    public function isVisibleToUser($user) {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (XG_SecurityHelper::userIsAdmin($user)) {
            $visibility = self::TAB_VISIBILITY_ADMIN;
        } else if ($user->isLoggedIn()) {
            $visibility = self::TAB_VISIBILITY_MEMBER;
        } else {
            $visibility = self::TAB_VISIBILITY_ALL;
        }
        return $this->isVisible($visibility);
    }

    /**
     * returns array representation of this tab
     *
     * @return Array  this tab
     */
    public function toArray() {
        return array('tabKey' => $this->tabKey, 'url' => $this->url, 'label' => $this->label, 'visibility' => $this->visibility, 'windowTarget' => $this->windowTarget, 'isSubTab' => $this->isSubTab, 'pageId' => $this->pageId);
    }

}

class XG_TabLayout {

    const SITE_TAB_LAYOUT_KEY = 'siteTabLayout';
    const TAB_MANAGER_DISABLE_KEY = 'disableTabManager';

    /** Flattened tab layout structure */
    private $tabs; // tabKey => XG_Tab

    /** Hex representations of the sub-tab colors (e.g. #ff0000). Keys are textColor, textColorHover, backgroundColor, and backgroundColorHover. */
    private $subTabColors;

    /** default tab visibility settings */
    private static $_defaultVisibility = array('invite' => XG_Tab::TAB_VISIBILITY_MEMBER, 'manage' => XG_Tab::TAB_VISIBILITY_ADMIN);

    /**
     * constructor
     *
     * @param tabs Array  flattened tab structure
     * @param subTabColors Array  Hex representations of the sub-tab colors (e.g. #ff0000). Keys are textColor, textColorHover, backgroundColor, and backgroundColorHover.
     *
     * @return XG_TabLayout  the layout object
     */
    function XG_TabLayout($tabs, $subTabColors) {
        $this->tabs = $tabs;
        $this->subTabColors = $subTabColors;
    }

    /**
     * create default tab layout based on enabled modules; each top level tab
     * is an entry in the returned array.  each top level tab has the following
     * attributes:
     *  - url string  the destination url
     *  - label string  the text displayed in the tab
     *  - tabKey  an identifier for the tab so we can highlight it (use instance name for Ning widgets)
     *  - visibility  who can see the tab (see constants above)
     *  - windowTarget  where should the window open?
     *  - isSubTab  is this tab a sub-tab?
     *  - pageId  a reference to an associated page, if created from a page
     *
     * @return Array  default tabs (tabKey => XG_Tab)
     */
    private static function createDefaultLayout() {
        $tabs = array();

        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $navEntries = XG_ModuleHelper::getNavEntriesFromWidgets(true);
        $navTabs = $navEntries['tabs'];
        foreach ($navTabs as $navTab) {
            $label = $navTab[0];
            $url = xg_relative_url($navTab[1]);
            $tabKey = $navTab[2];
            $visibility = array_key_exists($tabKey, self::$_defaultVisibility) ?
                                self::$_defaultVisibility[$tabKey] :
                                XG_Tab::TAB_VISIBILITY_ALL;
            $tabs[$tabKey] = new XG_Tab($tabKey, $url, $label, $visibility);
        }

        return $tabs;
    }

    /**
     * create a new layout based on default enabled modules.  returns the
     * XG_TabLayout object only and does not replace the existing layout until saved
     *
     * @return XG_TabLayout  default site tab layout
     */
    public static function createDefaultLayoutObject() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_TablayoutColorHelper.php');
        return new XG_TabLayout(self::createDefaultLayout(), Index_TablayoutColorHelper::getDefaultColors());
    }

    /**
     * is tab manager enabled?
     *
     * @param widget W_Widget  the main widget; optional
     *
     * @return boolean  true if enabled, false if not
     */
    public static function isEnabled($main = null) {
        if (is_null($main)) {
            $main = W_Cache::getWidget('main');
        }
        return ! (array_key_exists(self::TAB_MANAGER_DISABLE_KEY, $main->config) && $main->config[self::TAB_MANAGER_DISABLE_KEY]);
    }

    /**
     * loads the current app's site layout, creating a new layout based on
     * default enabled modules if necessary.  forceCreate will create a new
     * default layout.
     *
     * @param createIfNecessary boolean  if the layout does not exist, create it
     *
     * @return XG_TabLayout  tab and subtab layout
     */
    public static function loadOrCreate($createIfNecessary = true) {
        $main = W_Cache::getWidget('main');
        $disabled = ! self::isEnabled($main);
        if (array_key_exists(self::SITE_TAB_LAYOUT_KEY, $main->config) && ! $disabled && (($siteTabLayout = unserialize($main->config[self::SITE_TAB_LAYOUT_KEY])) !== false)) {
            $subTabColors = $siteTabLayout['subTabColors'];
            $tabs = $siteTabLayout['tabs'];
            return new XG_TabLayout($tabs, $subTabColors);
        } else if ($createIfNecessary && ! $disabled) {
            $xgLayout = self::createDefaultLayoutObject();
            $xgLayout->save();
            return $xgLayout;
        } else {
            return null;
        }
    }

    /**
     * add a new tab to the current navigation
     *
     * @param tabKey string  unique identifier for the tab
     * @param url string  the destination url for the tab
     * @param label string  the tab label
     * @param visibility string  tab visibility (see constants above)
     * @param windowTarget string  where should the link open?
     * @param isSubTab boolean  is this tab a sub-tab?
     * @param pageId string  referenced page id, if any
     * @param replace boolean  if the specified tabKey exists should we replace it?
     *                         otherwise an exception will be thrown.
     *
     * @return XG_TabLayout  the updated layout with the new tab
     */
    public function addTab($tabKey, $url, $label, $visibility = XG_Tab::TAB_VISIBILITY_ALL, $windowTarget = null, $isSubTab = false, $pageId = null, $replace = false) {
        if (! $replace && $this->hasTab($tabKey)) {
            throw new Exception("addKey[$tabKey]; the specified tabKey already exists");
        }
        if (! $this->hasTab($tabKey)) {
            $this->tabs[$tabKey] = new XG_Tab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab, $pageId);
        } else {
            // reuse existing object
            $this->updateTab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab, $pageId);
        }
        return $this;
    }

    /**
     * add a new tab to the current navigation after the reference tab
     *
     * @param refTabKey string  reference tab key
     * @param tabKey string  unique identifier for the tab
     * @param url string  the destination url for the tab
     * @param label string  the tab label
     * @param visibility string  tab visibility (see constants above)
     * @param windowTarget string  where should the link open?
     * @param isSubTab boolean  is this tab a sub-tab?
     * @param pageId string  referenced page id, if any
     * @param replace boolean  if the specified tabKey exists should we replace it?
     *                         otherwise an exception will be thrown.
     *
     * @return XG_TabLayout  the updated layout with the new tab
     */
    public function addTabAfter($refTabKey, $tabKey, $url, $label, $visibility = XG_Tab::TAB_VISIBILITY_ALL, $windowTarget = null, $isSubTab = false, $pageId = null, $replace = false) {
        if (! $this->hasTab($refTabKey)) {
            throw new Exception("addTabAfter[$refTabKey, $tabKey]; reference tab key does not exist");
        }
        if ($this->hasTab($tabKey)) {
            if (! $replace) {
                throw new Exception("addTabAfter[$refTabKey, $tabKey]; the specified tabKey already exists");
            }
            // erase old tab
            $this->removeTab($tabKey);
        }

        // build new layout
        $newTabs = array();
        foreach ($this->tabs as $_tabKey => $_tab) {
            $newTabs[$_tabKey] = $_tab;
            if ($_tabKey === $refTabKey) {
                // insert element
                $newTabs[$tabKey] = new XG_Tab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab, $pageId);
            }
        }

        $this->tabs = $newTabs;
        return $this;
    }

    /**
     * insert a new tab to the current navigation before the reference tab.  if the reference tab
     * is the first tab, isSubTab will be set false regardless.
     *
     * @param refTabKey string  reference tab key
     * @param tabKey string  unique identifier for the tab
     * @param url string  the destination url for the tab
     * @param label string  the tab label
     * @param visibility string  tab visibility (see constants above)
     * @param windowTarget string  where should the link open?
     * @param isSubTab boolean  is this tab a sub-tab?
     * @param pageId string  referenced page id, if any
     * @param replace boolean  if the specified tabKey exists should we replace it?
     *                         otherwise an exception will be thrown.
     *
     * @return XG_TabLayout  the updated layout with the new tab
     */
    public function insertTabBefore($refTabKey, $tabKey, $url, $label, $visibility = XG_Tab::TAB_VISIBILITY_ALL, $windowTarget = null, $isSubTab = false, $pageId = null, $replace = false) {
        if (! $this->hasTab($refTabKey)) {
            throw new Exception("insertTabBefore[$refTabKey, $tabKey]; reference tab key does not exist");
        }
        if ($this->hasTab($tabKey)) {
            if (! $replace) {
                throw new Exception("insertTabBefore[$refTabKey, $tabKey]; the specified tabKey already exists");
            }
            // erase old tab
            $this->removeTab($tabKey);
        }

        // build new layout
        $currentKeys = array_keys($this->tabs);
        $firstKey = $currentKeys[0];
        if ($refTabKey === $firstKey) {
            $isSubTab = false;
        }
        $newTabs = array();
        foreach ($this->tabs as $_tabKey => $_tab) {
            if ($_tabKey === $refTabKey) {
                // insert element
                $newTabs[$tabKey] = new XG_Tab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab, $pageId);
            }
            $newTabs[$_tabKey] = $_tab;
        }

        $this->tabs = $newTabs;
        return $this;
    }

    /**
     * check if a tab key exists in the current navigation
     *
     * @param tabKey string  the tabKey to check
     *
     * @return boolean  true|false
     */
    public function hasTab($tabKey) {
        return array_key_exists($tabKey, $this->tabs);
    }

    /**
     * remove a top-level tab from the current navigation
     *
     * @param tabKey string  the top-level tab to remove
     * @param removeSubTabs boolean  remove sub-tabs? if false, action is decided by promoteSubTabs?
     * @param promoteSubTabs boolean  sub-tabs should become top-level tabs (true) or remain as sub-tabs (false)
     *
     * @return XG_TabLayout  the updated layout with the specified tab removed
     */
    public function removeTab($tabKey, $removeSubTabs = false, $promoteSubTabs = false) {
        if (! $this->hasTab($tabKey)) {
            throw new Exception("removeTab[$tabKey]; the specified tabKey does not exist");
        }
        $tabGroup = $this->_getTabWithSubTabs($tabKey, XG_Tab::TAB_VISIBILITY_ADMIN);
        unset($this->tabs[$tabKey]);
        unset($tabGroup[$tabKey]);
        if ($removeSubTabs) {
            foreach (array_keys($tabGroup) as $subTabKey) {
                unset($this->tabs[$subTabKey]);
            }
        } else if ($promoteSubTabs) {
            foreach (array_keys($tabGroup) as $subTabKey) {
                $this->tabs[$subTabKey]->isSubTab = false;
            }
        }
        return $this;
    }

    /**
     * updates a tab (does not affect associated sub-tabs, if any)
     *
     * @param tabKey string  tab to modify
     * @param url string  new url
     * @param label string  new tab text
     * @param visibility string  new visibility
     * @param windowTarget string  where the link should open
     * @param isSubTab boolean  is the a sub-tab?
     * @param pageId string  referenced page id, if any
     * @param create boolean  should we create the entry if it doesn't exist?
     *
     * @return XG_TabLayout  the updated layout with the specified tab updated
     */
    public function updateTab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab, $pageId, $create = false) {
        if (! $this->hasTab($tabKey)) {
            if (! $create) {
                throw new Exception("updateTab[$tabKey]; the specified tabKey does not exist");
            } else {
                $this->addTab($tabKey, $url, $label, $visibility, $windowTarget, $isSubTab);
            }
        } else {
            $this->tabs[$tabKey]->url = $url;
            $this->tabs[$tabKey]->label = $label;
            $this->tabs[$tabKey]->visibility = $visibility;
            $this->tabs[$tabKey]->windowTarget = $windowTarget;
            $this->tabs[$tabKey]->isSubTab = $isSubTab;
            $this->tabs[$tabKey]->pageId = $pageId;
        }

        return $this;
    }

    /**
     * save the current layout to the site content object
     *
     * @return boolean  true if saved, false if error
     */
    public function save() {
        try {
            $main = W_Cache::getWidget('main');
            $siteTabLayout = array('tabs' => $this->tabs, 'subTabColors' => $this->subTabColors);
            $main->config[self::SITE_TAB_LAYOUT_KEY] = serialize($siteTabLayout);
            $main->saveConfig();
            return true;
        } catch (Exception $e) {
            // error saving
            return false;
        }
    }

    /**
     * update the current layout from an array of tabs, assigning new tab keys
     * as needed.
     *
     * @param tabs Array  an array of tabs
     *
     * @return XG_TabLayout  the updated tab layout
     */
    public function updateFromArray($tabs) {
        // The input array may have empty tabKey values from new tabs so it is
        // not stored in the standard tabKey => tab format.  We have to map
        // from Array(tab) to our internal structure.
        $newTabs = array();
        $nextNewTabKey = $this->getNextNumericTabKey();

        foreach ($tabs as $tab) {
            // ignore tabs with empty labels - they won't be clickable
            if (mb_strlen($tab['label'])) {
                $tabKey = array_key_exists('tabKey', $tab) && mb_strlen($tab['tabKey']) ?
                            trim($tab['tabKey']) : null;
                if (is_null($tabKey)) {
                    $tabKey = 'xn' . $nextNewTabKey++;
                }
                $pageId = array_key_exists('tabPageId', $tab) ?
                            trim($tab['tabPageId']) : null;
                $newTabs[$tabKey] = new XG_Tab($tabKey, $tab['url'], $tab['label'], $tab['visibility'], $tab['windowTarget'], $tab['isSubTab'], $pageId);
            }
        }

        $this->tabs = $newTabs;
        return $this;
    }

    /**
     * return the nested tab structure from the current layout
     *
     * @param visibility string  visibility level
     * @param maxTopLevelTabs integer  the maximum number of top-level tabs to show (rest are excluded from the layout)
     * @param maxSubTabsPerTab integer  the maximum number of sub-tabs per top-level tab to show (rest are excluded from the layout)
     * @param fixedTabsTop Array  an array of tabKeys in order that should be fixed at the top (or left)
     * @param fixedTabsBottom Array  an array of tabKeys in order that should be fixed at the bottom (or right)
     *
     * @return Array  tabs in the current layout with nested 'subTabs' if present
     */
    public function getNestedTabStructure($visibility = XG_Tab::TAB_VISIBILITY_ADMIN, $maxTopLevelTabs = null, $maxSubTabsPerTab = null, $fixedTabsTop = array(), $fixedTabsBottom = array()) {
        $return = array();
        $currentParent = null;
        $currentParentTabKey = null;
        foreach ($this->getTabs($visibility, $maxTopLevelTabs, $maxSubTabsPerTab, $fixedTabsTop, $fixedTabsBottom) as $tabKey => $tab) {
            if (! $tab->isSubTab || is_null($currentParent)) {
                if (! is_null($currentParent) && ($currentParentTabKey !== $tab->tabKey)) {
                    $return[$currentParentTabKey] = $currentParent;
                }
                $currentParent = $tab->toArray();
                $currentParentTabKey = $tab->tabKey;
                $currentParent['subTabs'] = array();
            } else {
                $currentParent['subTabs'][$tab->tabKey] = $tab->toArray();
            }
        }
        $return[$currentParentTabKey] = $currentParent;
        return $return;
    }

    /**
     * return a flattened array of the specified tab and its visible subtabs, if any, up to the specified max (if any as well)
     *
     * @param tabKey string  the desired tab to extract from the layout
     * @param visibility string  visibility level to apply (XG_Tab::TAB_VISIBILITY_ADMIN sees all, etc)
     * @param maxSubTabsPerTab integer  the maximum number of sub-tabs per top-level tab to show (rest are excluded from the layout)
     *
     * @return Array  the specified tab and its sub-tabs from the layout;
     *                returns an empty array of the specified tabKey does not exist
     */
    private function _getTabWithSubTabs($tabKey, $visibility = XG_Tab::TAB_VISIBILITY_ADMIN, $maxSubTabsPerTab = null) {
        $tabs = array();
        $getSub = false;
        $numSubTabs = 0;
        foreach ($this->tabs as $_tabKey => $_tab) {
            if ($tabKey === $_tabKey) {
                $getSub = true;
                $tabs[$_tabKey] = $_tab;
            } else if ($getSub && $_tab->isSubTab) {
                if ($_tab->isVisible($visibility)) {
                    $tabs[$_tabKey] = $_tab;
                    $numSubTabs++;
                    if (! is_null($maxSubTabsPerTab) && ($numSubTabs >= $maxSubTabsPerTab)) {
                        break;
                    }
                }
            } else if ($getSub) {
                $getSub = false;
                break;
            }
        }
        return $tabs;
    }

    /**
     * returns the flattened tab structure from the current layout limited by visibility
     *
     * @param visibility string  visibility level to apply (XG_Tab::TAB_VISIBILITY_ADMIN sees all, etc)
     * @param onlyTopLevelTabs boolean  return only visible top-level tabs?
     * @param maxSubTabsPerTab integer  the maximum number of sub-tabs per top-level tab to show (rest are excluded from the layout)
     *
     * @return Array  visible tabs from the current layout according to options specified
     */
    public function getVisibleTabs($visibility = XG_Tab::TAB_VISIBILITY_ADMIN, $onlyTopLevelTabs = false, $maxSubTabsPerTab = null) {
        $visibleTabs = array();
        foreach ($this->tabs as $tabKey => $tab) {
            if (! $tab->isSubTab && $tab->isVisible($visibility)) {
                // top-level visible tab, add it + all visible subtabs unless $onlyTopLevelsTabs == true
                if (! $onlyTopLevelTabs) {
                    $tabGroup = $this->_getTabWithSubTabs($tabKey, $visibility, $maxSubTabsPerTab);
                    $visibleTabs = array_merge($visibleTabs, $tabGroup);
                } else {
                    $visibleTabs[$tabKey] = $tab;
                }
            }
        }
        return $visibleTabs;
    }

    /**
     * return the flattened tab structure from the current layout
     *
     * @param visibility string  visibility level
     * @param maxTopLevelTabs integer  the maximum number of top-level tabs to show (rest are excluded from the layout)
     * @param maxSubTabsPerTab integer  the maximum number of sub-tabs per top-level tab to show (rest are excluded from the layout)
     * @param fixedTabsTop Array  an array of tabKeys in order that should be fixed at the top (or left)
     * @param fixedTabsBottom Array  an array of tabKeys in order that should be fixed at the bottom (or right)
     *
     * @return Array  tabs in the current layout
     */
    public function getTabs($visibility = XG_Tab::TAB_VISIBILITY_ADMIN, $maxTopLevelTabs = null, $maxSubTabsPerTab = null, $fixedTabsTop = array(), $fixedTabsBottom = array()) {
        if (is_null($maxTopLevelTabs)) {
            return $this->getVisibleTabs($visibility, false, $maxSubTabsPerTab);
        } else {
            $limitedTabs = array();
            $numTopTabs = 0;
            // determine how many tabs from $fixedTabsBottom are visible
            $numVisibleFixedTabsBottom = 0;
            foreach ($fixedTabsBottom as $tabKey) {
                if (($tab = $this->getTab($tabKey)) && $tab->isVisible($visibility)) {
                    $numVisibleFixedTabsBottom++;
                }
            }
            // top
            foreach ($fixedTabsTop as $tabKey) {
                if ($numTopTabs >= $maxTopLevelTabs - $numVisibleFixedTabsBottom) { break; }
                if (($tab = $this->getTab($tabKey)) && $tab->isVisible($visibility)) {
                    $limitedTabs = array_merge($limitedTabs, $this->_getTabWithSubTabs($tabKey, $visibility, $maxSubTabsPerTab));
                    $numTopTabs++;
                }
            }
            // middle
            foreach (array_keys($this->getVisibleTabs($visibility, true)) as $tabKey) {
                if (! in_array($tabKey, $fixedTabsTop) &&
                    ! in_array($tabKey, $fixedTabsBottom) &&
                    ! $this->getTab($tabKey)->isSubTab) {
                    if ($numTopTabs === $maxTopLevelTabs - $numVisibleFixedTabsBottom) { break; }
                    $limitedTabs = array_merge($limitedTabs, $this->_getTabWithSubTabs($tabKey, $visibility, $maxSubTabsPerTab));
                    $numTopTabs++;
                }
            }
            // bottom
            foreach ($fixedTabsBottom as $tabKey) {
                if (($tab = $this->getTab($tabKey)) && $tab->isVisible($visibility)) {
                    $limitedTabs = array_merge($limitedTabs, $this->_getTabWithSubTabs($tabKey, $visibility, $maxSubTabsPerTab));
                }
            }
            return $limitedTabs;
        }
    }

    /**
     * Returns hex representations of the sub-tab colors (e.g. #ff0000)
     *
     * @return Array  the colors, with the following keys: textColor, textColorHover, backgroundColor, and backgroundColorHover
     */
    public function getSubTabColors() {
        return $this->subTabColors;
    }

    /**
     * Sets the hex representations of the sub-tab colors (e.g. #ff0000)
     *
     * @param $subTabColors  the colors, with the following keys: textColor, textColorHover, backgroundColor, and backgroundColorHover
     */
    public function setSubTabColors($subTabColors) {
        $this->subTabColors = $subTabColors;
    }

    /**
     * return the desired tab
     *
     * @param tabKey string  the desired tab
     *
     * @return XG_Tab|null  the desired tab, or null if the specified key does not exist
     */
    public function getTab($tabKey) {
        return array_key_exists($tabKey, $this->tabs) ?
                    $this->tabs[$tabKey] :
                    null;
    }

    /**
     * returns the next tabKey id to start at for new tabs
     *
     * @return integer  the next tabKey id
     */
    public function getNextNumericTabKey() {
        $last = -1;
        foreach (array_keys($this->tabs) as $tabKey) {
            if (preg_match('/^xn(\d+)$/u', $tabKey, $matches)) {
                if (intval($matches[1]) > $last) { $last = intval($matches[1]); }
            }
        }
        return $last + 1;
    }

    /**
     * Handle a feature add event, add a new tab
     *
     * @param   $widgetName       string       the widget name that has been added
     */
    public static function onFeatureAdd($widgetName) {
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        if (in_array($widgetName, XG_ModuleHelper::getFakeFirstOrderFeatures())) {
            // these are fake first order features and should not get tabs ever
            return;
        }
        $widget = W_Cache::getWidget($widgetName);
        if (isset($widget->config['isFirstOrderFeature']) && $widget->config['isFirstOrderFeature']) {
            $tabLayout = self::loadOrCreate(false);
            if ($tabLayout) {
                if (isset($widget->config['title'])) {
                    XG_App::includeFileOnce('/lib/XG_LanguageHelper.php');
                    $title = XG_LanguageHelper::translateDefaultWidgetTitle($widget->config['title']);
                } else {
                    if (isset($widget->config['displayName'])) {
                        $title = $widget->config['displayName'];
                    } else {
                        $title = ucwords($widget->dir);
                    }
                }

                $tabLayout->insertTabBefore('manage', $widget->dir, $widget->buildUrl('index', 'index'), $title)->save();
            }
        }
    }

    /**
     * Handle a feature remove event, remove an existing tab
     *
     * @param   $widgetName     string      the widget name that has been removed
     */
    public static function onFeatureRemove($widgetName) {
        $tabLayout = self::loadOrCreate(false);
        $widget = W_Cache::getWidget($widgetName);
        if($tabLayout && $tabLayout->hasTab($widget->dir)){
            // remove associated sub-tabs (BAZ-9506) [ywh 2008-09-01]
            $tabLayout->removeTab($widget->dir, true)->save();
        }
    }

    /**
     * get tabs by pageId
     *
     * @param pageId string  the page id you wish to search for
     *
     * @return Array(XG_Tab)  matching tabs
     */
    public function getTabsByPageId($pageId) {
        $matching = array();
        foreach ($this->tabs as $tabKey => $tab) {
            if (! is_null($tab->pageId) && ($pageId === $tab->pageId)) {
                $matching[] = $tab;
            }
        }
        return $matching;
    }
}

XN_Event::listen('feature/add/after', array('XG_TabLayout', 'onFeatureAdd'));
XN_Event::listen('feature/remove/after', array('XG_TabLayout', 'onFeatureRemove'));
