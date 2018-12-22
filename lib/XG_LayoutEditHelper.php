<?php

/**
 * Class containing helper functions to do with editing Page Layouts.
 *
 * @see XG_Layout
 * @see XG_LayoutHelper
 */
class XG_LayoutEditHelper {

    /**
     * Whether drag and drop page layout editing is enabled.
     * Can be used to switch off all such activity in case of problems (BAZ-7722).
     */
    private static $layoutEditingEnabled = true;

    /**
     * Whether drag and drop page layout editing is enabled.
     * Can be used to switch off all such activity in case of problems.
     *
     * @return  boolean Whether drag and drop layout editing is enabled.
     */
    public static function layoutEditingEnabled() {
        if ($_GET['test_baz_7722']) { return false; }
        return self::$layoutEditingEnabled;
    }

    /**
     * Takes an original XML layout and a JSON-encoded string of the new desired layout and produces the appropriate new layout.
     *
     * @param   $originalLayout XG_Layout   Can be profiles or main page type (groups not yet supported).
     * @param   $newLayoutJson  string      String in format suitable for passing to NF_JSON->decode.  See XG_LayoutEditHelperTest for examples.
     * @return                  array       [0] => Number of embeds added to the layout (including preservation of existing embeds),
     *                                      [1] => Updated layout with old data preserved from $originalLayout in new layout defined by
     *                                             $newLayoutJson.  OR old layout if an error occurred (embeds added will be 0).
     */
    public static function determineNewLayout($originalLayout, $newLayoutJson) {
        // TODO: This function alters its arguments, and should be renamed to indicate so, e.g., rebuildLayout [Jon Aquino 2008-08-28]
        $initialLayoutXml = $originalLayout->getLayout()->saveXml();
        try {
            $xgLayout = $originalLayout;
            $previousLayout = new DOMXPath($xgLayout->getLayout());

            $newLayoutArr = self::getNewLayoutRepresentation($initialLayoutXml, $xgLayout, $newLayoutJson);
            self::checkOkToProceed($initialLayoutXml, $xgLayout, $newLayoutArr);

            // Clear the layout, preserving the version attribute.
            //TODO: wrap this up and genericize with get/setIteration [Thomas David Baker 2008-05-21]
            $version = $xgLayout->getLayout()->documentElement->getAttribute('version');
            $nextEmbedInstanceId = $xgLayout->getLayout()->documentElement->getAttribute('nextEmbedInstanceId');
            $xgLayout->clear();
            if ($version) { $xgLayout->getLayout()->documentElement->setAttribute('version', $version); }
            if ($nextEmbedInstanceId) { $xgLayout->getLayout()->documentElement->setAttribute('nextEmbedInstanceId', $nextEmbedInstanceId); }

            // Increment the iteration so that old versions (in browser cache or open in other browsers) become stale.
            XG_Layout::setIteration($xgLayout->getLayout(), $newLayoutArr['iteration'] + 1);

            // Build up the new layout by cycling through each column in the array representation adding embeds.
            $embedsAdded = 0;
            $columnDetails = array(
                array($newLayoutArr['banner'], '/layout/colgroup/column', 3, false),
                array($newLayoutArr['col1'], '/layout/colgroup/column/colgroup/column', 1, false),
                array($newLayoutArr['col2'], '/layout/colgroup/column/colgroup/column[2]', 2, false),
                array($newLayoutArr['sidebar'], '/layout/colgroup/column[2]', 1, true),
                array($newLayoutArr['col3'], '/layout/colgroup/column[2]', 1, false)
            );
            $embedsAvailable = self::getEmbedList($originalLayout->getType());
            foreach ($columnDetails as $column) {
                list($embeds, $path, $width, $sitewide) = $column;
                if (! $embeds) { continue; } // Not all layout types have all columns.
                $actionKey = 'col' . $width . 'Action';
                foreach ($embeds as $embed) {
                    $chosen = $embedsAvailable[$embed['xg_embed_key']];
                    if (! $chosen) { throw new Exception("Unknown embed type: " . print_r($embed, true)); }
                    if (! is_null($embed['xg_embed_instance_id'])) {
                        self::importExistingEmbed($previousLayout, $xgLayout, $path, $embed['xg_embed_instance_id'], $chosen[$actionKey], $sitewide);
                    } else {
                        self::addNewEmbed($xgLayout, $path, $chosen['widgetName'], $chosen[$actionKey], $sitewide);
                    }
                    $embedsAdded++;
                }
            }
            NF_Controller::invalidateCache(NF::INVALIDATE_ALL); // to ensure RSS embeds are properly sized if they've changed columns. BAZ-5213
            return array($embedsAdded, $xgLayout);
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            $originalLayout->loadXml($initialLayoutXml);
            return array(0, $originalLayout);
        }
    }

    /**
     * Get an array representation of the desired layout from the submitted JSON and the existing layout.
     * Ensures that settings are preserved across versions, that required embeds are present and that immovable embeds are not moved.
     *
     * @param   $newLayoutJson      string      JSON representation of desired new layout.
     * @param   $xgLayout           XG_Layout   Layout that will be manipulated.
     * @param   $initialLayoutXml   string      XML representation of the previous layout.
     * @return                      array       Representation of the desired layout.
     */
    private static function getNewLayoutRepresentation($initialLayoutXml, $xgLayout, $newLayoutJson) {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $newLayoutArr = $json->decode($newLayoutJson);
        if (!is_array($newLayoutArr)) $newLayoutArr = array();  // if bad json was passed in, handle it gracefully
        if ($xgLayout->getType() === 'profiles') { $newLayoutArr = self::addMissingEmbeds($initialLayoutXml, $newLayoutArr); }
        return $newLayoutArr;
    }

    /**
     * Checks that if we proceed with the specified changes we will not annoy the user.  That is, have they based the new layout
     * on their current layout and if not will they lose any settings/data by applying the new layout.  If the use will be
     * annoyed, we throw an Exception.
     *
     * @param   $initialLayoutXml   string      XML representation of current layout (before manipulation).
     * @param   $xgLayout           XG_Layout   Layout being manipulated.
     * @param   $newLayoutArr       array       Representation of desired new layout.
     * @return                      void
     */
    private static function checkOkToProceed($initialLayoutXml, $xgLayout, $newLayoutArr) {
        $currentIteration = XG_Layout::getIteration($xgLayout->getLayout());
        if ($newLayoutArr['iteration'] != $currentIteration && self::couldLoseData($initialLayoutXml, $newLayoutArr)) {
            throw new Exception("Cannot save this layout - it is based on another iteration (" . $newLayoutArr['iteration'] . "), "
                . "not the current iteration ($currentIteration) and not all the same embeds are present.");
        }
        if ($xgLayout->getType() === 'homepage' && ! self::requiredEmbedsPresent($newLayoutArr)) {
            throw new Exception("Required embeds are not present.");
        }
        if (self::embedLimitsExceeded(self::getEmbedList($xgLayout->getType()), $newLayoutArr)) {
            throw new Exception("Embed limit exceeded.");
        }
    }

    /**
     * Imports the specified embed with the specified details from $previousLayout to $xgLayout.
     *
     * @param   $previousLayout     DOMXPath    Representation of layout to import from.
     * @param   $xgLayout           XG_Layout   Layout to import into.
     * @param   $path               string      XPath location to import into.
     * @param   $embedInstanceId    string      embedInstanceId of embed to import.
     * @param   $action             string      Value to set for action attribute of embed.
     * @param   $sitewide           boolean     Value to set for sitewide attribute of embed.
     * @return              void        Called for side effects on $xgLayout.
     */
    private static function importExistingEmbed($previousLayout, $xgLayout, $path, $embedInstanceId, $action, $sitewide) {
        $query = "//module[@embedInstanceId='" . $embedInstanceId . "']";
        $module = $previousLayout->query($query)->item(0);
        if (! $module) { throw new Exception("Failed to find module with XPath query: " . $query); }
        $module->setAttribute('action', $action);
        $module->setAttribute('sitewide', $sitewide);
        self::checkEmbedDetails('existing', $module->getAttribute('widgetName'), $module->getAttribute('action'), $path);
        $xgLayout->importElement($module, $path, $path == '/layout/colgroup/column');
    }

    //TODO: addNewEmbed and importExistingEmbed are very similar and could perhaps be combined into one function.  [Thomas David Baker 2008-05-23]

    //TODO: differentiate functions that work on xgLayout and those that work on the array representation
    // either by splitting them into separate classes or altering the names of the functions in some way.  [Thomas David Baker 2008-05-23]

    /**
     * Adds a new embed with the specified attributes to $xgLayout.
     *
     * @param   $xgLayout   XG_Layout   Layout to add embed to.
     * @param   $path       string      XPath location to add to.
     * @param   $widgetName string      Value to set for widgetName attribute of embed.
     * @param   $action     string      Value to set for action attribute of embed.
     * @param   $sitewide   boolean     Value to set for sitewide attribute of embed.
     * @return              void        Called for side effects on $xgLayout.
     */
    private static function addNewEmbed($xgLayout, $path, $widgetName, $action, $sitewide) {
        self::checkEmbedDetails('new', $widgetName, $action, $path);
        $xgLayout->addModule($widgetName, $action, $path, array('sitewide' => $sitewide), TRUE /* append at bottom */);
        self::publicizeIfNecessary($xgLayout, $widgetName);
    }

    /**
     * Walk through the required embeds for profiles layouts.  Where one does not appear in $newLayoutArr, add it.  If it
     * DOES appear in $initialLayoutXml then preserve the xg_embed_instance_id from that instance.  Otherwise add it without an xg_embed_instance_id.
     *
     * @param   $initialLayoutXml   string  String of XML of the layout before manipulation.
     * @param   $newLayoutArr       array   Array representation of the new layout.
     * @return                      array   Array representation of the new layout with any missing embeds added in their default location.
     */
    private static function addMissingEmbeds($initialLayoutXml, $newLayoutArr) {
        $arr = $newLayoutArr;
        $embeds = self::getProfilesEmbedList();
        foreach ($embeds as $key => $details) {
            if (! self::findEmbed($arr, $key) && $details['required']) {
                $arr = self::addMissingEmbed($initialLayoutXml, $arr, $key, $details);
            }
        }
        return $arr;
    }

    /**
     * Search for an embed with xg_embed_key of $key in $layoutArr and return the array representing it, if found.
     *
     * @param   $layoutArr  array   Array representation of a page layout.
     * @param   $key        string  Embed key to search for.
     * @return              var     Array representation of embed if found, false if not found, null if layout is invalid.
     */
    protected static function findEmbed($layoutArr, $key) {
        if (! is_array($layoutArr)) { return null; }
        foreach ($layoutArr as $col) {
            if (! is_array($col)) { continue; }
            foreach ($col as $embed) {
                if ($embed['xg_embed_key'] === $key) {
                    return $embed;
                }
            }
        }
        return false;
    }

    /**
     * Add a missing embed to an array representation of a layout, returning a new representation with the embed within.
     * Preserve xg_embed_instance_id from earlier layout if present.
     *
     * @param   $initialLayoutXml   string  XML representation of layout before manipulation.
     * @param   $layoutArr          array   Array representation of a layout.
     * @param   $key                string  Embed key (see getEmbedList).
     * @param   $details            array   Array of embed details (see getEmbedList).
     */
    protected static function addMissingEmbed($initialLayoutXml, $layoutArr, $key, $details) {
        $doc = new DOMDocument();
        $doc->loadXml($initialLayoutXml);
        $xpath = new DOMXPath($doc);
        $query = "//module[(@action='" . $details['col1Action'] . "' or @action='" . $details['col2Action'] . "' or @action ='"
            . $details['col3Action'] . "') " . "and @widgetName='" . $details['widgetName'] . "']";
        $nodeList = $xpath->query($query);
        $embedInstanceId = null;
        if ($nodeList->length === 1) {
            $module = $nodeList->item(0);
            $embedInstanceId = $module->getAttribute('embedInstanceId');
        } else if ($nodeList->length > 1) {
            $embedInstanceId = $nodeList->item(0)->getAttribute('embedInstanceId');
            // This loop is a workaround for BAZ-7402 that ensures that if we DO get more than one node it is in fact the same node many times.
            foreach ($nodeList as $node) {
                if ($node->getAttribute('embedInstanceId') !== $embedInstanceId) {
                    throw new Exception("Found " . $nodeList->length . " embeds with XPath query: $query in " . $doc->saveXml());
                }
            }
        }
        return self::insertEmbed($layoutArr, array('xg_embed_instance_id' => $embedInstanceId, 'xg_embed_key' => $key), $details['location']);
    }

    /**
     * Add an embed to an array representation of a layout, returning a new representation with the embed within.
     *
     * @param   $layoutArr  array   Array representation of a layout.
     * @param   $embed      array   Array representation of the embed to insert.
     * @param   $location   array   array(<colname>, <zero-indexed-position-in-column>)
     */
    protected static function insertEmbed($layoutArr, $embed, $location) {
        $newLayoutArr = $layoutArr;
        list($col, $pos) = $location;
        $originalCol = $layoutArr[$col];
        $newCol = array();
        for ($i = 0; $i < min($pos, count($originalCol)); $i++) {
            $newCol[] = $originalCol[$i];
        }
        $newCol[] = $embed;
        for ($i = $pos; $i < count($originalCol); $i++) {
            $newCol[] = $originalCol[$i];
        }
        $newLayoutArr[$col] = $newCol;
        if (count($layoutArr[$col]) + 1 != count($newLayoutArr[$col])) {
            throw new Exception("Failed to insert " . print_r($embed, true) . " at " . print_r($location, true) . " in " . print_r($layoutArr, true));
        }
        return $newLayoutArr;
    }

    /**
     * Determine if the required homepage embeds are present in the specified array represenation of a layout.
     *
     * @param   $newLayoutArr   Array   Representation of new layout.
     * @return                  boolean true if all required embeds are present, false otherwise.
     */
    //TODO: This is a misleading name considering we have a 'required' attribute for embeds but this actually checks 'fixed' [Thomas David Baker 2008-08-27]
    protected static function requiredEmbedsPresent($newLayoutArr) {
        //TODO: Why not call this AFTER addMissingEmbeds and allow it to be called for homepage OR profiles layouts?
        $embeds = self::getHomepageEmbedList();
        foreach ($embeds as $embedKey => $details) {
            if (! $details['fixed']) { continue; }
            if (! self::findEmbed($newLayoutArr, $embedKey)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine if any of the embed limits detailed in $embedList are exceeded by the layout in $newLayoutArr.
     *
     * @param   $embedList      array   See getEmbedList for details.
     * @param   $newLayoutArr   array   Array representation of potential new layout.
     * @return                  boolean true if any limits are exceeded, false otherwise.
     */
    protected static function embedLimitsExceeded($embedList, $newLayoutArr) {
        $histogram = array();
        foreach ($newLayoutArr as $colName => $colEmbeds) {
            if (! is_array($colEmbeds)) { continue; }
            foreach ($colEmbeds as $embed) {
                $histogram[$embed['xg_embed_key']] = (isset($histogram[$embed['xg_embed_key']]) ? $histogram[$embed['xg_embed_key']] + 1 : 1);
            }
        }
        foreach ($histogram as $key => $count) {
            //BAZ-7993 for historical reasons we want to let it go if they have more than the limit of text boxes or rss feeds.
            if ($embedList[$key]['embedLimit'] == 1 && $count > $embedList[$key]['embedLimit']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Looks up the unique key of an embed type from it's widget name and action.  A reverse lookup of the details from getEmbedList.
     *
     * @param   $type       string  "profiles" or "index"
     * @param   $widgetName string  The name of the widget of the embed type to lookup.
     * @param   $action     string  The action of the embed type to lookup.
     * @return              string  feature name, or will throw an exception if the feature name cannot be found.
     */
    public static function getEmbedKey($type, $widgetName, $action) {
        $embedList = self::getEmbedList($type);
        foreach ($embedList as $name => $details) {
            if ($details['widgetName'] === $widgetName
                    && (in_array($action, array($details['col1Action'], $details['col2Action'])))) {
                return $name;
            }
        }
        // Some embeds are fixed in place like the sidebar and we don't include them in the list.  They therefore have no key.`
        // In a future more-flexible version they will have keys and reaching this line of code will be an error.
        return null;
    }

    /**
     * Check that the specified details are all present.  If not, throw an exception.
     *
     * @param   $status         'new' or 'existing'.
     * @param   $widgetName     Name of the widget under which this embed resides.
     * @param   $action         Action on widget's EmbedController fired by this embed.
     * @param   $path           XPath to module location in layout XML.
     * @return                  null, or will throw an exception if any details are missing.
     */
    protected function checkEmbedDetails($status, $widgetName, $action, $path) {
        if (! $widgetName || ! $action || ! $path) {
            throw new Exception("Invalid $status embed (widgetName=$widgetName, action=$action, path=$path)");
        }
    }

    //TODO: This should be a template fragment instead of being hardcoded in a string here. [Thomas David Baker 2008-05-23]
    public function hiddenLayoutDetails($xgLayout) {
        //TODO: getIteration doesn't make much sense as a static function should probably be a member function. [Thomas David Baker 2008-05-21]
        return '<div id="xg_layout" style="display: none" iteration="' . XG_Layout::getIteration($xgLayout->getLayout())
            . '" userName="' . XN_Profile::current()->screenName  . '"></div>';
    }

    /**
     * Determines if the embed with the specified action on the specified widget is movable when editing your profile page layout or not.
     *
     * @param   $type       string  Layout type.  Either 'profiles' or 'homepage'.
     * @param   $widgetName string  Name of the widget in question.
     * @param   $action     string  Action in question.
     * @return              boolean true if embed is movable, otherwise false.
     */
    public static function embedIsMovable($type, $widgetName, $action) {
        $embedKey = XG_LayoutEditHelper::getEmbedKey($type, $widgetName, $action);
        if (! $embedKey) { return false; } // Don't allow movement of embeds that are not in the master embed list.
        $embeds = XG_LayoutEditHelper::getEmbedList($type);
        $embed = $embeds[$embedKey];
        return (! $embed['fixed']);
    }

    /**
     * Determines if saving the layout in $layoutArr could cause us to lose user data found in $initialLayoutXml.
     * Will only return true unless all of the embedInstanceIds in $initialLayoutXml are also found in $layoutArr.
     *
     * @param   $initialLayoutXml   string  XML of currently stored layout.
     * @param   $layoutArr          array   Array representation of proposed layout.
     * @return                      boolean true if data loss is possible.
     */
    protected static function couldLoseData($initialLayoutXml, $layoutArr) {
        $doc = new DOMDocument();
        $doc->loadXML($initialLayoutXml);
        $xpath = new DOMXPath($doc);
        $nodesWithEmbedIds = $xpath->query("//*[@embedInstanceId]");
        $originalEmbedIds = array();
        foreach ($nodesWithEmbedIds as $node) {
            $originalEmbedIds[] = $node->getAttribute('embedInstanceId');
        }
        $newEmbedIds = array();
        foreach ($layoutArr as $col) {
            if (! is_array($col)) { continue; }
            foreach ($col as $embed) {
                if (! is_null($embed['xg_embed_instance_id'])) {
                    $newEmbedIds[] = $embed['xg_embed_instance_id'];
                }
            }
        }
        foreach ($originalEmbedIds as $id) {
            if (! in_array($id, $newEmbedIds)) {
                return true;
            }
        }
        return false;
    }

    //TODO: This does not belong here.  Can it find a home in XG_SecurityHelper? [Thomas David Baker 2008-05-21]
    /**
     * Determines if the current user is currently viewing their own profile page.
     *
     * @param   $xgLayout   XG_Layout   Layout of page to check.
     * @return              boolean     true if $xgLayout represents the user's own profile page, otherwise false.
     */
    public static function viewingOwnProfilePage($xgLayout) {
        return XN_Profile::current()->isLoggedIn() && $xgLayout->getType() === 'profiles' && $xgLayout->isOwner(XN_Profile::current());
    }

    //TODO: We should do more to combine the various forms of getEmbedList.  Inspect active modules instead of all this hardcoding?
    // Return values being different for different layout types and dispatch on type means this barely deserves to exist as a
    // function at all. [Thomas David Baker 2008-05-21]
    /**
     * Gets a list of valid module embeds for a layout from the layout's type.
     *
     * @param   $type   string  One of 'homepage', 'profiles'.  'groups' not supported at this time.
     * @return          array   Array of dicts with (guaranteed) 'widthOption', 'widgetName', 'embedLimit' and 'fixed' keys.
     *                          And as appropriate also 'col1Action', 'col2Action', 'title', 'about' and 'iconName' keys.
     */
    public static function getEmbedList($type) {
        if ($type === 'homepage') {
            return self::getHomepageEmbedList();
        } else if ($type === 'profiles') {
            return self::getProfilesEmbedList();
        } else {
            throw new Exception("Unrecognized layout type: $type");
        }
    }

    /**
     * Returns metadata for the mozzles that can be embedded on the main page.
     *
     * @return array  the sorted features, each an array with:
     *         - title - name of the feature
     *         - about - brief description of the feature
     *         - widgetName - internal widget-instance name (e.g., forum)
     *         - widthOption - w1, w2, or w12 indicating whether to allow column widths of 1, 2, or both
     *         - col1Action - name of the action for the 1-column-width display
     *         - col2Action - name of the action for the 2-column-width display
     *         - iconName - filename for the icon
     *         - embedLimit - maximum number of times this feature may appear on the page
     *         - fixed - whether the feature is not movable
     */
    private static function getHomepageEmbedList() {
        $embeds = array();
        $embeds['_members'] = array('title' => xg_text('MEMBERS'), 'widthOption' => 'w12',
                    'widgetName' => 'profiles', 'col1Action' => 'embed1activeMembers',
                    'col2Action' => 'embed2activeMembers',
                    'iconName' => 'members.gif', 'embedLimit' => 1, 'fixed' => false,
                    'about' => xg_text('A_LIST_OF_MEMBERS'));

        $firstOrderEmbeds = array();
        $secondOrderEmbeds = array();
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $modules = XG_ModuleHelper::getAllModules('addFeatures');
        foreach ($modules as $name => $module) {
            //  Don't include permanent mozzles
            if (((isset($module->config['isPermanent']) && $module->config['isPermanent'])
                 && $name !== 'profiles')) {
                continue;
            }
            // There is currently no homepage embed for the opensocial mozzle.
            if ($name === 'opensocial') {
                continue;
            }

            $moduleDesc = array();
            $moduleDesc['widgetName'] = $name;
            $moduleDesc['iconName'] = $name . '.gif';
            $moduleDesc['fixed'] = false;
             if ($module->dir === 'groups' && $module->root === 'groups') {
                $moduleDesc['title'] = xg_text('GROUPS');
                $moduleDesc['about'] = xg_text('ALLOW_MEMBERS_TO_CREATE_GROUPS');
            } else if ($module->dir === 'forum' && $module->root === 'forum') {
                $moduleDesc['title'] = xg_text('FORUM');
                $moduleDesc['about'] = xg_text('A_THREADED_DISCUSSION_FORUM');
            } else if ($module->dir === 'photo' && $module->root === 'photo') {
                $moduleDesc['title'] = xg_text('PHOTOS');
                $moduleDesc['about'] = xg_text('ALLOW_USERS_PHOTOS');
            } else if ($module->dir === 'profiles' && $module->root === 'profiles') {
                $moduleDesc['title'] = xg_text('BLOG');
                $moduleDesc['about'] = xg_text('FEATURE_POSTS_FROM_YOUR_BLOG');
            } else if ($module->dir === 'video' && $module->root === 'video') {
                $moduleDesc['title'] = xg_text('VIDEOS');
                $moduleDesc['about'] = xg_text('ALLOW_USERS_VIDEOS');
            } else if ($module->dir === 'music' && $module->root === 'music') {
                $moduleDesc['title'] = xg_text('MUSIC');
                $moduleDesc['about'] = xg_text('ALLOW_PEOPLE_TO_UPLOAD_AND_SHARE_AUDIO');
            } else if ($module->dir === 'html' && $module->root === 'html') {
                $moduleDesc['title'] = xg_text('TEXT_BOX');
                $moduleDesc['about'] = xg_text('PUT_ANY_TEXT_WIDGET_OR_HTML');
            } else if ($module->dir === 'feed' && $module->root === 'feed') {
                $moduleDesc['title'] = xg_text('RSS');
                $moduleDesc['about'] = xg_text('ADD_FEEDS_FROM_ACROSS_THE_WEB');
            } else if ($module->dir === 'activity' && $module->root === 'activity') {
                // Latest activity draggable should be labeled 'Activity' (BAZ-7824) [ywh 2008-06-04]
                $moduleDesc['title'] = xg_text('ACTIVITY');
                $moduleDesc['about'] = xg_text('FOLLOW_LATEST_ACTIVITY');
            } else if ($module->dir === 'events' && $module->root === 'events') {
                $moduleDesc['title'] = xg_text('EVENTS');
                $moduleDesc['about'] = xg_text('ALLOW_PEOPLE_ORGANIZE_RSVP_EVENTS');
            } else if ($module->dir === 'notes' && $module->root === 'notes') {
                $moduleDesc['title'] = xg_text('NOTES');
                $moduleDesc['about'] = xg_text('ALLOW_ADMINS_CREATE_EDIT_FEATURE_NOTES');
            } else if ($module->dir === 'chat' && $module->root === 'chat') {
                $moduleDesc['title'] = xg_text('CHAT');
                $moduleDesc['about'] = xg_text('ALLOW_PEOPLE_TO_CHAT');
            } else {
                $moduleDesc['title'] = $module->config['title'];
                $moduleDesc['about'] = $module->config['title'];
            }

            //  Determine possible widths
            $width = 'w';
            if ($module->controllerHasAction('embed', 'embed1')) {
                $width .= '1';
                $moduleDesc['col1Action'] = 'embed1';
            }
            if ($module->controllerHasAction('embed', 'embed2')) {
                $width .= '2';
                $moduleDesc['col2Action'] = 'embed2';
            }
            //  Ensure that there's at least one
            if ($width == 'w') { continue; }
            $moduleDesc['widthOption'] = $width;

            //  Include it as a first-order or second-order feature
            //    for placement
            if (isset($module->config['isFirstOrderFeature']) && $module->config['isFirstOrderFeature']) {
                $moduleDesc['embedLimit'] = 1;
                $firstOrderEmbeds[$name] = $moduleDesc;
            }
            else {
                $embedLimit = W_Cache::getWidget('main')->config['secondOrderEmbedLimit'];
                if (mb_strlen($embedLimit)) {
                    $moduleDesc['embedLimit'] = $embedLimit;
                }
                /* If the configuration variable isn't set */
                else if ($embedLimit == 0) {
                    $moduleDesc['embedLimit'] = 10;
                }
                $secondOrderEmbeds[$name] = $moduleDesc;
            }
        }

        $embeds += $firstOrderEmbeds;
        $embeds += $secondOrderEmbeds;
        $embeds['_description'] = array('title' => xg_text('DESCRIPTION'), 'widthOption' => 'w1',
                    'widgetName' => 'main', 'col1Action' => 'embed1siteDescription',
                    'col2Action' => 'embed2siteDescription', 'iconName' => 'sitedesc.gif',
                    'embedLimit' => 1, 'fixed' => false,
                    'about' => xg_text('SHOW_NETWORK_DESCRIPTION'));
        $embeds['_you'] = array('title' => xg_text('USERNAME'), 'widthOption' => 'w1',
                    'widgetName' => 'main', 'col1Action' => 'embed1you', 'iconName' => 'layout_account.gif',
                    'embedLimit' => 1, 'fixed' => true, 'about' => xg_text('INFORMATION_ABOUT_THE_USER'));
        $embeds['_ads'] = array('title' => xg_text('ADS'), 'widthOption' => 'w1',
                    'widgetName' => 'main', 'col1Action' => 'embed1ads', 'col2Action' => 'embed2ads',
                    'iconName' => 'ads.gif','embedLimit' => 1, 'fixed' => (! XG_App::runOwnAds()),
                    'about' => xg_text('MONETIZE_WITH_ADS'));
        $embeds['_createdBy'] = array('title' => xg_text('CREATED_BY'), 'widthOption' => 'w1',
                    'widgetName' => 'main', 'col1Action' => 'embed1createdBy', 'iconName' => 'createdby.gif',
                    'embedLimit' => 1, 'fixed' => (! XG_App::protectYourNetwork()),
                    'about' => xg_text('LET_MEMBERS_KNOW_WHO_CREATED_NETWORK'));
        $embeds['_badges'] = array('title' => xg_text('GET_BADGES'), 'widthOption' => 'w1',
                    'widgetName' => 'profiles', 'col1Action' => 'embed1badge',
                    'iconName' => 'badges.gif', 'embedLimit' => 1,
                    'fixed' => false, 'about' => xg_text('PROMOTE_NETWORK_WITH_BADGES'));
        return self::sortEmbeds($embeds);
    }

    /**
     * Returns metadata for the mozzles that can be embedded on a profile page.
     *
     * @return array  the sorted features, each an array with:
     *         - widgetName - internal widget-instance name (e.g., forum)
     *         - widthOption - w1, w2, or w12 indicating whether to allow column widths of 1, 2, or both
     *         - col1Action - name of the action for the 1-column-width display
     *         - col2Action - name of the action for the 2-column-width display
     *         - embedLimit - maximum number of times this feature may appear on the page
     *         - fixed - whether the feature is not movable
     */
    private static function getProfilesEmbedList() {
        $embeds = array();
        //TODO: There is too much duplication here, what is the point in having col1Action and col2Action separate?
        // Refactor.  [Thomas David Baker 2008-05-21]
        $embeds['_pagetitle'] = array('widthOption' => 'w3', 'widgetName' => 'profiles',
            'col3Action' => 'embed3pagetitle', 'embedLimit' => 1, 'required' => true, 'fixed' => true, 'location' => array('banner', 0));

        // col1
        $embeds['_badge'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed1smallbadge', 'col2Action' => 'embed2smallbadge',
            'embedLimit' => 1, 'required' => true, 'fixed' => true, 'location' => array('col1', 0));
        $embeds['_friends'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed1friends', 'col2Action' => 'embed2friends', 'embedLimit' => 1,
            'required' => true, 'fixed' => true, 'location' => array('col1', 1));
        $embeds['music'] = array('widthOption' => 'w12', 'widgetName' => 'music',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col1', 2));
        $embeds['groups'] = array('widthOption' => 'w12', 'widgetName' => 'groups',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col1', 3));
        $embeds['forum'] = array('widthOption' => 'w12', 'widgetName' => 'forum',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col1', 4));
        $embeds['events'] = array('widthOption' => 'w12', 'widgetName' => 'events',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col1', 5));
        $embeds['feed'] = array('widthOption' => 'w12', 'widgetName' => 'feed',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col1', 6));

        // col2
        //TODO: embed3welcome makes no sense at all - should be embed2welcome. [Thomas David Baker 2008-05-21]
        $embeds['_userpagetitle'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col2Action' => 'embed2pagetitle', 'embedLimit' => 1, 'required' => true, 'fixed' => true, 'location' => array('col2', 0));
        $embeds['_welcome'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed3welcome', 'col2Action' => 'embed3welcome', 'embedLimit' => 1,
            'required' => true, 'fixed' => true, 'location' => array('col2', 1));
        $embeds['activity'] = array('widthOption' => 'w12', 'widgetName' => 'activity',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 2));
        $embeds['_profileqa'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed1profileqa', 'col2Action' => 'embed2profileqa', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 3));
        $embeds['html'] = array('widthOption' => 'w12', 'widgetName' => 'html',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 4));
        $embeds['photo'] = array('widthOption' => 'w12', 'widgetName' => 'photo',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 6));
        $embeds['video'] = array('widthOption' => 'w12', 'widgetName' => 'video',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 7));
        $embeds['_blogposts'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed1blogposts', 'col2Action' => 'embed2blogposts', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 8));
        $embeds['_chatterwall'] = array('widthOption' => 'w12', 'widgetName' => 'profiles',
            'col1Action' => 'embed1chatterwall', 'col2Action' => 'embed2chatterwall', 'embedLimit' => 1,
            'required' => true, 'fixed' => false, 'location' => array('col2', 9));
        $embeds['opensocial'] = array('widthOption' => 'w12', 'widgetName' => 'opensocial',
            'col1Action' => 'embed1', 'col2Action' => 'embed2', 'embedLimit' => 5,
            'required' => false, 'fixed' => false, 'location' => array('col2', 10));
        return $embeds;
    }

    /**
     * Sort the specified embeds according to addFeaturesSortOrder (configuration option in main widget).
     *
     * @param   $embeds Array   'name' => W_Widget pairs to sort.
     * @return          Array   'name' => W_Widget pairs.
     */
    private static function sortEmbeds($embeds) {
        XG_App::includeFileOnce('/lib/XG_ConfigHelper.php');
        if (! W_Cache::getWidget('main')->config[XG_ConfigHelper::ADD_FEATURES_SORT_ORDER_KEY]) {
            XG_ConfigHelper::updateAddFeaturesSortOrder(); // BAZ-7990 [Jon Aquino 2008-06-09]
        }
        $sortOrder = unserialize(W_Cache::getWidget('main')->config[XG_ConfigHelper::ADD_FEATURES_SORT_ORDER_KEY]);
        $sortables = array();
        foreach ($embeds as $name => &$embed) {
            $sortValue = ($sortOrder[$name] ? $sortOrder[$name] : 9999);
            $sortables[] = array('name' => $name, 'embed' => $embed, 'sortValue' => $sortValue);
        }
        XG_LangHelper::aasort($sortables, 'sortValue_a,title_a');
        $sorted = array();
        foreach ($sortables as $sortable) {
            $sorted[$sortable['name']] = $sortable['embed'];
        }
        return $sorted;
    }

    /**
     * Load the necessary HTML and javascript to allow drag and drop page editing.
     *
     * @param   $xgLayout   XG_Layout   Layout that will be editable.
     * @return              void
     */
    public static function loadPageEditing($xgLayout) {
        $jqueryUrl = xg_cdn(W_Cache::getWidget('profiles')->buildResourceUrl('js/profile/jquery-1.2.5.min.js'));
        $jqueryUiUrl = xg_cdn(W_Cache::getWidget('profiles')->buildResourceUrl('js/profile/jquery-ui-1.5b3.packed.js'));
        //TODO: It would be better to use a packed verison of this file [Thomas David Baker 2008-05-25]
        $jqueryUiSortableUrl = xg_cdn(W_Cache::getWidget('profiles')->buildResourceUrl('js/profile/ui.sortable-uncompressed.js'));
        ?>
        <script src="<%= xnhtmlentities($jqueryUrl) %>" charset="utf-8"></script>
        <script src="<%= xnhtmlentities($jqueryUiUrl) %>" charset="utf-8"></script>
        <script src="<%= xnhtmlentities($jqueryUiSortableUrl) %>" charset="utf-8"></script>
        <?php
        echo XG_LayoutEditHelper::hiddenLayoutDetails($xgLayout);
        XG_App::ningLoaderRequire("xg.profiles.profile.editLayout");
    }

    //TODO: Both of these "activity"-related functions do not belong in here [Thomas David Baker 2008-04-25]
    private static function publicizeIfNecessary($xgLayout, $widgetName) {
        if ($xgLayout->getType() == 'homepage') {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            if (self::publicizeWidgetAddition($widgetName)) XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, XG_ActivityHelper::SUBCATEGORY_MESSAGE_NEW_FEATURE, null, null, null, $widgetName);
        }
    }

    private static function publicizeWidgetAddition($widgetName){
        // TODO: In the name, change "publicize" to "isPublicizing" which is more accurate [Jon Aquino 2008-08-28]
        return in_array($widgetName, array('photo', 'video', 'music', 'groups', 'forum', 'events', 'notes', 'chat'));
    }
}
