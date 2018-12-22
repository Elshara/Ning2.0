<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');
XG_App::includeFileOnce('/lib/XG_LangHelper.php');
XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');

/**
 * Useful functions for HTML output for layouts
 *
 * @see XG_Layout
 */
class XG_LayoutHelper {

    // the index position of the sidebar in a layout when querying for //column
    const LAYOUT_SIDEBAR_INDEX_POSITION = 3;

    // the cache key for sidebar caching for admins/NC
    const ADMIN_SIDEBAR_CACHE_KEY = 'sidebar-__admin__';

    // list of embed actions that are non-cacheable
    protected static $nonCacheableEmbedActions = array('embed1you');

    public static function invalidateAdminSidebarCache() {
        XN_Cache::remove(self::ADMIN_SIDEBAR_CACHE_KEY);
    }

    /**
     * Outputs the HTML for the specified layout.
     *
     * @param $xgLayout		XG_Layout	The layout to render.
     * @param $controller W_Controller  The current controller, specifying layoutName and layoutType
     */
    public static function renderLayout($xgLayout, $controller) {
        // TODO: Pass $xgLayout around instead of $controller
        $xgLayout = XG_LayoutHelper::updateSidebarIfNecessary($xgLayout);
        $xgLayout = XG_LayoutHelper::removeGadgetsEmbeds($xgLayout); // gadgets is an obsolete mozzle, remove any traces of it
        $layout = $xgLayout->getLayout()->documentElement;
        if ($layout) {
            foreach ($layout->childNodes as $colgroup) {
                if ($colgroup instanceof DOMElement) {
                    self::renderElement($colgroup, $controller, $xgLayout);
                }
            }
            if (XG_App::membersCanCustomizeLayout() && XG_LayoutEditHelper::viewingOwnProfilePage($xgLayout)) {
                XG_LayoutEditHelper::loadPageEditing($xgLayout);
            }
        }
    }

    private static function getSidebarEmbedElements(W_Controller $controller) {
        if (! XG_App::appIsLaunched()) { return array(); }

        $controller->app = XN_Application::load();

        $controller->enabledModules = XG_ModuleHelper::getEnabledModules();
        $controller->disabledModules = XG_ModuleHelper::getDisabledModules();

        $xgLayout = XG_Layout::load('index');
        $layout = $xgLayout->getLayout();

        $xpath = new DOMXPath($layout);

        //  Use the contents of the 'layout' element within the document
        $controller->layout = $xgLayout->getLayout()->documentElement;
        $controller->layoutName = $xgLayout->getName();
        $controller->layoutType = $xgLayout->getType();

        $nodeList = $xpath->query('/layout/colgroup/column[2]');
        return array($nodeList->item(0)->childNodes, $xgLayout);
    }

    public static function renderNonCacheableSidebarEmbeds(W_Controller $controller) {
        list ($elements, $xgLayout) = self::getSidebarEmbedElements($controller);
        foreach ($elements as $element) {
            if (in_array($element->getAttribute('action'), self::$nonCacheableEmbedActions)) {
                self::renderElement($element, $controller, $xgLayout);
            }
        }
    }

    /**
     * Outputs the HTML for modules in the sidebar.
     *
     * What is in the sidebar is determined by reading the PageLayout object's XML.
     *
     * @param	W_Controller    $controller	Current controller.
     * @param   boolean         $sitewide       Render the sitewide modules if true; non-sitewide if false
     * @return 					void
     */
    public static function renderSidebarEmbeds(W_Controller $controller, $sitewide = true) {
        list ($elements, $xgLayout) = self::getSidebarEmbedElements($controller);
        foreach ($elements as $element) {
            // render only selected (sitewide/non-sitewide) non-cacheable embed actions (BAZ-8304) [ywh 2008-07-15]
            if ((($element->getAttribute('sitewide') == '1') === $sitewide) &&
                ! in_array($element->getAttribute('action'), self::$nonCacheableEmbedActions)) {
                self::renderElement($element, $controller, $xgLayout);
            }
        }
    }

    /**
     * Read the supplied layout and check if it contains an old style sidebar of the form:
     *
     * <module widgetName="main" action="sidebar" embedInstanceId="1"/>
     *
     * If so, replace it with module elements for You, Ads, Created By and Badges.
     *
     * This function exists to ensure the backwards compatibility of old format PageLayout XML
     * now that we store sidebar modules (both permanent and moveable) in the XML as well as
     * main page items instead of just a single "sidebar" module.
     *
     * In the past we did not store the sidebar for the whole site in the index PageLayout object in
     * the content store.  Just the items to appear on the main page.  Sidebar items were
     * hardcoded.  To preserve the layouts of those networks that were created before v1.11
     * we check here for the presence of the old sidebar module.  If it is there we  update the XML
     * for the sidebar in the PageLayout object in the content store with the items that were
     * previously hardcoded.  This change only affects the homepage PageLayout ('index').
     * Other page layouts for My Page or Groups still call action_sidebar which (eventually)
     * renders the main page's sitewide sidebar modules.
     *
     * This function is also piggybacked to add the Activity dashboard widget to the top of the
     * center column on every homepage because it is being released at the same time.  If we
     * need to do this kind of thing again we will have to introduce versioning similar to that
     * on user pages.
     *
     * @param	$xgLayout	XG_Layout	XG_Layout that contains the site-wide sidebar.
     * @return  XG_Layout			The supplied layout if no changes are required.  The newly modified layout if changes have been made.
     */
    public static function updateSidebarIfNecessary($xgLayout) {
        // do not perform this check non non-homepage layouts
        if ($xgLayout->getName() !== 'index') {
            return $xgLayout;
        }

        // check if the main/sidebar exists in the layout
        $layoutXml = $xgLayout->getLayout();
        if (! self::hasEmbed($layoutXml, 'main', 'sidebar')) {
            return $xgLayout;
        }

        $sidebarModulePath = '/layout/colgroup/column[2]/module';
        $added = 0;
        foreach (array('embed1createdBy','embed1ads','embed1you') as $embedAction) {
            if (! self::hasEmbed($layoutXml, 'main', $embedAction)) {
                if ($xgLayout->insertModule('main', $embedAction, $sidebarModulePath, array('sitewide' => true))) {
                    $added++;
                } else {
                    error_log('updateSidebarIfNecessary; could not add missing sidebar module [' . $embedAction . ']');
                }
            }
        }
        if ($added > 0) {
            error_log('updateSidebarIfNecessary; added ' . $added . ' missing sidebar modules');
        }

        // add activity module
        if (! self::hasEmbed($layoutXml, 'activity', 'embed2')) {
            if (! $xgLayout->insertModule('activity', 'embed2', '/layout/colgroup/column/colgroup/column[2]/module', NULL)) {
                error_log("could not add activity dashboard");
            }
        }

        // remove sidebar module from XML
        $xgLayout->removeElement($sidebarModulePath . "[@action='sidebar']");
        $xgLayout = null; // Force save by calling XG_Layout's destructor. TODO Should actually add a save method to xg_layout to make this explicit.

        return XG_Layout::load('index');
    }

    /**
     * Scan the sidebar of the specified layout.  If it does not contain ads/created by and the current network is
     * not paying for "run your own ads" or "protect your network" then insert ads/created by in the sidebar.
     *
     * Devised to prevent users remaining ad-free after they stop paying their "run your own ads" payment or
     * similar with "protect your network".
     *
     * @param	$xgLayout	XG_Layout	XG_Layout that contains the site-wide sidebar.
     * @return  XG_Layout				The supplied layout if no changes are required.  The newly modified layout if changes have been made.
     */
    public static function putPayServicesInSidebarIfNecessary($originalXgLayout) {
        $xgLayout = $originalXgLayout;
        if ($xgLayout->getName() !== 'index') {
            return $xgLayout;
        }
        $runOwnAds = XG_App::runOwnAds();
        $protectYourNetwork = XG_App::protectYourNetwork();
        if (! $protectYourNetwork) {
            $xgLayout = XG_LayoutHelper::putCreatedByInSidebarIfNecessary($xgLayout);
        }
        if (! $runOwnAds) {
            $xgLayout = XG_LayoutHelper::putAdsInSidebarIfNecessary($xgLayout);
        }
        return $xgLayout;
    }

    /**
     * Scan the sidebar for an ads module.  If not found, add it to layout and reload.
     *
     * @param	XG_Layout	Layout to scan sidebar of.
     */
    private static function putAdsInSidebarIfNecessary($xgLayout) {
        if ($xgLayout->getName() !== 'index') {
            return $xgLayout;
        }
        return XG_LayoutHelper::putModuleInSidebarIfNecessary($xgLayout, 'embed1ads');
    }

    /**
     * Scan the sidebar for a "created by" module.  If not found, add it to layout and reload.
     *
     * @param	XG_Layout	Layout to scan sidebar of.
     */
    private static function putCreatedByInSidebarIfNecessary($xgLayout) {
        if ($xgLayout->getName() !== 'index') {
            return $xgLayout;
        }
        return 	XG_LayoutHelper::putModuleInSidebarIfNecessary($xgLayout, 'embed1createdBy');
    }

    /**
     * Scan the sidebar for a module with the specified action.  If not found, add it to layout and reload.
     *
     * @param	XG_Layout	Layout to scan sidebar of.
     * @param	string		Action to scan for.
     */
    private static function putModuleInSidebarIfNecessary($xgLayout, $action) {
        $xpath = new DOMXPath($xgLayout->getLayout());
        $sidebarPath = '/layout/colgroup/column[2]';
        $nodeList = $xpath->query($sidebarPath);
        foreach ($nodeList->item(0)->childNodes as $element) {
            if ($element->getAttribute('action') === $action && $element->getAttribute('sitewide') === true) {
                return $xgLayout;
            }
        }
        // Remove any instances of the module that are elsewhere in the layout.
        $modules = $xpath->query("//module[@action='{$action}']");
        for ($i = 0; $i < $modules->length; $i++) {
            $modules->item($i)->parentNode->removeChild($modules->item($i));
        }
        // Put module in below YOU on sidebar
        //TODO this can result in Created By apppearing /above/ ads in rare circumstances.
        if (! $xgLayout->insertModule('main', $action, $sidebarPath . '/module', array('sitewide' => true), TRUE /* insert 2nd */)) {
            error_log("Failed to return $action to it's rightful place in the sidebar.");
        }
        //TODO what it if is on the main page but NOT in the sidebar - it will now appear twice.
        // Would be nice to remove it from the main page.
        return XG_Layout::load('index');
    }

    /**
     * Adds the Welcome module to the homepage layout, if it is missing.
     *
     * @param $xgLayout XG_Layout  the homepage layout
     * @return integer  the embedInstanceId of the new module
     */
    public static function addWelcomeBoxIfNecessary($xgLayout) {
        return $xgLayout->addModuleOnce('main', 'embed2welcome', '/layout/colgroup/column/colgroup/column[2]');
    }
    
    /**
     * Removes any gadgets embed from the layout XML, if present.
     *
     * @param   $xgLayout   XG_Layout   The layout to alter.
     * @return              XG_Layout   The altered layout.
     */
    public static function removeGadgetsEmbeds($xgLayout) {
        $xpathQuery = "//module[@widgetName='gadgets']";
        $nodeList = $xgLayout->query($xpathQuery);
        for ($i = 0; $i < $nodeList->length; $i++) {
            $xgLayout->removeElement($xpathQuery);
        }
        return $xgLayout;
    }

    /**
     * Outputs the HTML for the specified module
     *
     * @param $element DOMElement  the module element
     * @param $controller W_Controller  the current controller, specifying layoutName and layoutType
     * @param $layoutName string The name that, with the type,
     *      uniquely identifies a layout instance, e.g., 'index', or the content ID of a User object
     * @param $layoutType string  optional layout type. 'homepage' is the default and
     *      is the front page layout. 'profiles' is the layout for a user profile page.
     * @param $xgLayout XG_Layout  The layout being rendered.
     */
    private static function renderModule(DOMElement $element, $controller, $layoutName, $layoutType, $xgLayout) {
        $widgetName = $element->getAttribute('widgetName');
        if ($_GET['test_embed']) { $widgetName = $_GET['test_embed']; }
        $action = $element->getAttribute('action');

        // prevent explicit rendering of the sidebar via layout (BAZ-9647) [ywh 2008-09-09]
        if (($widgetName === 'main') && ($action === 'sidebar')) {
            return;
        }

        $embedInstanceId = $element->getAttribute('embedInstanceId');
        $isActive = $element->getAttribute('isActive');
        /* Do not show "normal" embeds in the sidebar when not signed in to a private network BAZ-4433 [2007-09-14 bakert] */
        $alwaysShow = array('embed1you', 'embed1ads');
        $hidingPage = (XG_LangHelper::endsWith($_SERVER['SCRIPT_URI'], '/notAllowed') || XG_LangHelper::endsWith($_SERVER['SCRIPT_URI'], '/banned'));
        if ($hidingPage && ! in_array($action, $alwaysShow)) { return; }
        $actionArgs = array('maxEmbedWidth' => $xgLayout->getMaxEmbedWidth($element->parentNode), 'embed' => new XG_Embed($embedInstanceId, $layoutType, $layoutName));
        // Three cases: enabled module, disabled module, permanent module [Jon Aquino 2006-11-18]
        // some group modules now have an isActive attribute, so ensure that it's not set to 0
        if (array_key_exists($widgetName, $controller->disabledModules) && ! $element->getAttribute('forceDisplay')) {
            return;
        }
        try {
            $widget = W_Cache::getWidget($widgetName);
            if ($widget && $widget->controllerHasAction('embed', $action) && $isActive != '0') {
                ob_start();
                // Putting $actionArgs inside an array here provides a single argument of "named parameter"
                // array elements to the embed action
                $widget->dispatch('embed', $action, array($actionArgs));
                $embedHtml = ob_get_clean();
                if (XG_App::membersCanCustomizeLayout() && XG_LayoutEditHelper::viewingOwnProfilePage($xgLayout) && $layoutType == "profiles") {
                    $embedKey = XG_LayoutEditHelper::getEmbedKey($layoutType, $widgetName, $action);
                    $movable = XG_LayoutEditHelper::embedIsMovable($layoutType, $widget->dir, $action);
                    $embedHtml = XG_LayoutHelper::addPageEditingAttributes($embedHtml, $embedKey, $embedInstanceId, $movable);
                }
                echo $embedHtml;
            }
        } catch (Exception $e) {
            // Silently fail and don't render anything if the widget doesn't exist or the widget
            // doesn't have an embed controller or anything goes wrong generating the embed HTML.
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * Takes a string of HTML of an xg_module embed and decorates it with page editing attributes
     * (xg_handle div, xg_embed_key, xg_embed_instance_id attributes and movable class).
     *
     * @param   $html               string  Original HTML to decorate.
     * @param   $embedKey           string  Unique key for this embed type.
     * @param   $embedInstanceId    integer embed_instance_id of the embed.
     * @param   $movable            boolean Is the embed movable?
     * @return                      string  String of decorated HTML.
     */
    public static function addPageEditingAttributes($html, $embedKey, $embedInstanceId, $movable) {
        //TODO: This is fairly fragile.  We should compare speed when doing all decoration in jquery and just do it there if speeds are the same.
        // If this does make big performance improvements we may want to generalize this into "addClass" type function. [Thomas David Baker 2008-05-23]
        // refactor with slightly less fragile self::modifyModuleEmbedClasses [ywh 2008-07-16]
        if (! $movable) {
            if (mb_strpos("highlightbg", $html) !== false) { //TODO: hackish way of detecting Admin Options [Thomas David Baker 2008-05-30]
                return $html;
            }
            return preg_replace('/class="([^"]*)xg_module([ "][^>]*>)(.*)/u', 'class="\1xg_module no_drag \2<div class="xg_handle">No Drag</div>\3', $html);
        }
        return preg_replace('/class="([^"]*)xg_module([ "][^>]*>)(.*)/u', 'xg_embed_key="' . $embedKey
            . '" xg_embed_instance_id="' . $embedInstanceId . '" class="\1xg_module movable sortable \2<div class="xg_handle">Drag</div>\3', $html);
    }

    /** Name of the class containing the renderModule function; overridden by the unit tests. */
    protected static $renderModuleClass = 'XG_LayoutHelper';

    /**
     * Outputs the HTML for the specified element
     *
     * @param $element DOMElement  the element (of type "module", "column", or "colgroup")
     * @param $controller W_Controller  the current controller, specifying layoutName and layoutType
     * @param $xgLayout XG_Layout  The layout being rendered.
     */
    private static function renderElement($element, $controller, $xgLayout) {
        switch ($element->tagName) {
            case 'module':
                call_user_func(array(self::$renderModuleClass, 'renderModule'), $element, $controller, $controller->layoutName, $controller->layoutType, $xgLayout);
                break;
            case 'column':
                self::renderColumn($element, $controller, $xgLayout, XG_Layout::getColumnIndexInLayout($element, $xgLayout) === self::LAYOUT_SIDEBAR_INDEX_POSITION);
                break;
            case 'colgroup':
                self::renderContainer($element, $controller, $xgLayout);
                break;
        }
    }

    /**
     * Outputs the HTML for the specified container
     *
     * @param $element DOMElement  the container element (e.g., of type "colgroup")
     * @param $controller W_Controller  the current controller, specifying layoutName and layoutType
     * @param $xgLayout XG_Layout  The layout being rendered.
     */
    private static function renderContainer(DOMElement $element, $controller, $xgLayout) {
        echo '<div ';
        $classes = array('xg_' . $element->tagName);
        if (!$element->previousSibling) {
            $classes[] = 'first-child';
        }
        if (!$element->nextSibling) {
            $classes[] = 'last-child';
        }
        echo " class='" . join(' ', $classes) . "'>\n";
        $children = $element->childNodes;
        foreach ($children as $child) {
            self::renderElement($child, $controller, $xgLayout);
        }
        echo "</div>\n";
    }

    /**
     * Outputs the HTML for the specified column
     *
     * @param $element DOMElement  the column element
     * @param $controller W_Controller  the current controller, specifying layoutName and layoutType
     * @param $xgLayout XG_Layout  The layout being rendered.
     * @param $isSidebar boolean  is the column to be rendered the sidebar?
     */
    private static function renderColumn(DOMElement $element, $controller, $xgLayout, $isSidebar = false) {
        // TODO: Eliminate group-specific logic. Actually, the groups homepage
        // shouldn't use XG_Layout. [Jon Aquino 2008-02-29]
        if ($controller->layoutType == 'groups') {
            $colWidth = $element->getAttribute('width');
            $spanWidth = $colWidth * 4;
            echo '<div ';
            $classes = array('xg_span-' . $spanWidth, 'xg_column');
            // TODO: currently the first inner column should get at 'first-child', but doesn't
            // because the previousSibling test finds the preceeding module on group detail pages
            if (!$element->previousSibling) {
                $classes[] = 'first-child';
            }
            if (!$element->nextSibling && $element->previousSibling) {
                $classes[] = 'last-child';
            }
            echo " class='" . join(' ', $classes) . "'>\n";
            if (! $isSidebar) {
                $children = $element->childNodes;
                foreach ($children as $child) {
                    self::renderElement($child, $controller, $xgLayout);
                }
            } else {
                xg_sidebar($controller, $controller->isProfilePage, $controller->isMemberProfilePage);
            }
        } else {
            $maxEmbedWidth = $xgLayout->getMaxEmbedWidth($element);
            $colWidth = $element->getAttribute('width');
            echo '<div _maxEmbedWidth="' . $maxEmbedWidth . '" _columnCount="' . $colWidth . '" ';
            $classes = array('xg_' . $colWidth . 'col');
            if (!$element->previousSibling) {
                $classes[] = 'first-child';
            }
            if (!$element->nextSibling) {
                $classes[] = 'last-child';
            }
            //TODO: This is quite a hacky way to get the column IDs in there.  [Thomas David Baker 2008-05-14]
            if (in_array('xg_1col', $classes) && in_array('first-child', $classes)) {
                echo ' id="xg_layout_column_1"';
            }
            //TODO: This is quite a hacky way to get the column IDs in there.  [Thomas David Baker 2008-05-14]
            if (in_array('xg_2col', $classes)) {
                echo ' id="xg_layout_column_2"';
            }
            echo " class='" . join(' ', $classes) . "'>\n";
            if (! $isSidebar) {
                $children = $element->childNodes;
                foreach ($children as $child) {
                    self::renderElement($child, $controller, $xgLayout);
                }
            } else {
                xg_sidebar($controller, $controller->isProfilePage, $controller->isMemberProfilePage);
            }
        }
        echo "&nbsp;";  // placeholder to enforce width
        echo "</div>\n";
    }

    /**
     * Finds the attributes of module elements
     *
     * @param $element DOMElement  the root node to search
     * @param $attributes  array  array to which to add  the names and values
     *     of the attributes of $element (if it is of type "module") and its child module elements
     */
    public static function getAttributes($element, &$attributes) {
        if ($element->tagName == 'module') {
            $embed = array();
            $attrList = $element->attributes;
            if ($attrList) {
                $i = 0;
                while ($item = $attrList->item($i)) {
                    $embed[$item->name] = $item->value;
                    $i++;
                }
            }
            $attributes[$element->getAttribute('embedInstanceId')] = $embed;
        }
        else {
            foreach ($element->childNodes as $child) {
                self::getAttributes($child, $attributes);
            }
        }
    }

    /**
     * Modifies the module container classes according to the specified change list
     *
     * @param string|array changeSet  list of changes to apply (i.e., +class1, -class2)
     * @param string html  the HTML block to apply the changes to
     * @param string inject  HTML to inject after the modified div (optional)
     *
     * @return string  the modified HTML block
     */
    public static function modifyModuleEmbedClasses($changeSet, $html, $inject = null) {
        // if no changes, return
        if (is_null($changeSet) || (is_string($changeSet) && (mb_strlen(trim($changeSet)) < 1))) {
            return $html;
        }
        if (is_string($changeSet)) {
            $changeSet = explode(',', trim($changeSet));
        }

        // identify all <div ... class="... xg_module ..."> so we can modify the class set
        if (preg_match_all('/<div.+?>/imsu', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $originalDivStr = $match[0];
                if (preg_match('/\bclass\s*=\s*(\"(?:[^"]+?)\"|\'(?:[^\']+?)\')/imsu', $originalDivStr, $class)) {
                    $originalClassStr = $class[0];
                    $classesFlat = preg_replace('/^\s*["\']+|["\']+\s*$/', '', $class[1]);
                    $classes = preg_split('/\s+/u', $classesFlat);
                    if (in_array('xg_module', $classes)) {
                        $classHash = array();
                        foreach ($classes as $class) {
                            $classHash[$class] = 1;
                        }
                        foreach ($changeSet as $change) {
                            if (mb_substr($change, 0, 1) == '-') {
                                $class = mb_substr($change, 1);
                                if (array_key_exists($class, $classHash)) {
                                    unset($classHash[$class]);
                                }
                            } else {
                                $class = mb_substr($change, 0, 1) == '+' ? mb_substr($change, 1) : $change;
                                $classHash[$class] = 1;
                            }
                        }
                        $newClassStr = "class=\"" . implode(' ', array_keys($classHash)) . "\"";
                        $newDivStr = str_replace($originalClassStr, $newClassStr, $originalDivStr);
                        if (! is_null($inject)) { $newDivStr .= $inject; }
                        $html = str_replace($originalDivStr, $newDivStr, $html);
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Returns the names of the widgets represented in the given layout.
     *
     * @param $domDocument DOMDocument  the layout
     * @return array  widget names, e.g., photos
     */
    public static function widgetNamesInLayout($domDocument) {
        $xpath = new DOMXPath($domDocument);
        $widgetNames = array();
        $widgetNodes = $xpath->query('//module');
        for ($n = 0; $n < $widgetNodes->length; $n++) {
            $widgetNames[] = $widgetNodes->item($n)->getAttribute('widgetName');
        }
        return array_unique($widgetNames);
    }

    /**
     * check the specified layout for presence of the specified embed
     *
     * @param layout DOMDocument  the layout to check
     * @param widgetName string  the embed's widget name
     * @param action string|Array  the action or actions to search for
     *
     * @return boolean  true if the embed is present; false if it's not present
     */
    public static function hasEmbed($layout, $widgetName, $action = array()) {
        $xpath = new DOMXPath($layout);
        if (! is_array($action)) { $action = array($action); }
        $query = "//module[(@action='" . implode("' or @action='", $action) . "') and @widgetName='" . $widgetName . "']";
        $nodeList = $xpath->query($query);
        return $nodeList->length > 0;
    }

}
