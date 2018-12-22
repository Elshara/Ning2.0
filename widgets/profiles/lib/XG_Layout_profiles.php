<?php

XG_App::includeFileOnce('/lib/XG_Layout.php');

/**
 * A profile-type layout
 */
class XG_Layout_profiles extends XG_Layout {

    /** Partial name of the attribute storing the XML on the User object; used to construct $attributeName */
    protected $attributeNameBase = 'layout';

    /** Name of the attribute storing the XML on the User object */
    protected $attributeName;

    /**
     *   As of 1.6 profile page layouts are versioned to allow new elements to
     *     be added programmatically.  When users are able to alter their own
     *     profile page layouts this mechanism will no longer be necessary (or
     *     advisable).
     *
     *   Layout versions do not correspond to code versions.  See Clearspace
     *     document 'Profile Page Versioning / Updating'.
     *
     *   As of 3.3 release, profile page layouts can be edited by users so additions
     *      to this routine should be aware that it may find a given profile
     *      page layout in any particular arrangement.
     */
    public function checkForUpdate() {
        //  Get the layout version from the attribute on the root ('layout') node
        $layoutNode = self::getLayout()->documentElement;
        if ($layoutNode) {
            $initialVersion = $version = $layoutNode->getAttribute('version');
            if (!$version) { $version = 0; }
            if ($version < 2) {
                //  1.6 additions / modifications
                $xpath = new DOMXPath(self::getLayout());
                $this->addMusicPlayerEmbed($xpath);
                $this->addGroupsEmbed($xpath);
                $this->moveRssEmbedToLeftOrAdd($xpath);

                //  Check for elements which might be missing from some very
                //    early profile layouts (RSS added above)
                $this->addForumEmbed($xpath);
                $version = 2;
            }
            if ($version < 3) {
                //  1.11 additions / modifications
                $xpath = new DOMXPath(self::getLayout());
                $this->addActivityStreamEmbed($xpath);
                $version = 3;
            }
            // we used to add the gadgets embed here, but it is now obsolete.  [Thomas David Baker 2008-09-08]
            if ($version < 5) {
                // 3.0 reordering of embeds
                $xpath = new DOMXPath(self::getLayout());
                $this->rearrangeLayout($xpath);
                $version = 5;
            }
            if ($version < 6) {
                // 3.1 add events
                $xpath = new DOMXPath(self::getLayout());
				$this->addEventsEmbed($xpath);
				$version = 6;
            }
        
            // NOTE: From this point on users are able to alter their own page layout.
            // So any additional embed adding or manipulating must take this into account.
            
            if ($version < 7) {
                // 3.6 shift position of user editable page title
                $xpath = new DOMXPath(self::getLayout());
				$this->addEditablePageTitle($xpath);
				$version = 7;
            }            
            
            if ($initialVersion != $version) {
                $layoutNode->setAttribute('version', $version);
            }
        }
    }

    /**
     * Rearrange the profile page layout to an order that goes better with 3.0 design.
     *
     * @param   $xpath  DOMXPath   The current layout wrapped in a DOMXPath for easy querying.
     * @return          void
     */
    public function rearrangeLayout($xpath) {
        list($cols, $colEmbeds, $colNodes) = array(array(), array(), array());
        $colEmbeds[1] = array('profiles-embed1smallbadge', 'profiles-embed2friends', 'music-embed1', 'groups-embed1', 'forum-embed1', 'feed-embed1');
        $colEmbeds[2] = array('profiles-embed3welcome', 'activity-embed2', 'profiles-embed1profileqa', 'html-embed2', 'photo-embed1', 'video-embed1',
                              'profiles-embed2blogposts', 'profiles-embed2chatterwall');
        for ($colNum = 1; $colNum <= 2; $colNum++) {
            $cols[$colNum] = "/layout/colgroup/column/colgroup/column[$colNum]";
            $colNodes[$colNum] = array();
            foreach ($this->getModulesByType($colEmbeds[$colNum]) as $rawEmbed => $nodeList) {
                // This is not significant enough an issue to throw an exception for.  We will just note it in the error log to help
                // debugging in case anyone ever reports that their profile page is in the "wrong" order.
                if ($nodeList->length > 1) { error_log("More than one node matching $rawEmbed in profile page layout - not updating"); return; }
                if (! $nodeList->item(0)) { error_log("$rawEmbed not found in existing profile page layout."); return; }
                $colNodes[$colNum][] = $this->normalizeWidth($nodeList->item(0), $colNum);
            }
        }
        //Must do this outside the loop, after we have gathered up all those we want to use otherwise
        // we might remove some modules before we have a reference to them and lose data.
        $this->replaceChildren($cols[1], $colNodes[1]);
        $this->replaceChildren($cols[2], $colNodes[2]);
    }

    /**
     * Inspects the action attribute of a node.  If the width implied by the action is inappropriate for the supplied $colNum
     * it is corrected.  Works only for columns 1 and 2 and embeds with width 1 and 2 - other embeds pass through unchanged.
     *
     * @param   $node   DOMNode XML node representing a module (must have an action attribute).
     * @param   $colNum int     1 or 2 for column 1 or 2 in the profile page layout.
     * @return          DOMNode With action modified to match column or unmodified $node as necessary.
     */
    public function normalizeWidth($node, $colNum) {
        if ($colNum !== 1 && $colNum !== 2) { throw new Exception("Tried to call normalizeWidth with colNum of $colNum"); }
        // We look for width 2 in col 1 and width 1 in col 2.  We ignore width 3 which is technically incorrect but works because we want to leave embed3welcome alone.
        $inappropriateWidth = ($colNum % 2) + 1;
        $action = $node->getAttribute('action');
        if (mb_strpos($action, 'embed' . $inappropriateWidth) !== false) {
            $node->setAttribute('action', str_replace('embed' . $inappropriateWidth, 'embed' . $colNum, $action));
        }
        return $node;
    }

    public function addActivityStreamEmbed($xpath) {
        //  Don't insert if there's already an activity embed
        $col2 = '/layout/colgroup/column/colgroup/column[2]';
        $existingNode = $xpath->query($col2 . '/module'
                . '[@widgetName="activity"][@action="embed2"]')->item(0);
        if ($existingNode) {
            return;
        }

        //  Insert after welcome box
        $referencePath = $col2 . '/module[@widgetName="profiles"][@action="embed3welcome"]';
        $instanceId = $this->insertModule('activity', 'embed2', $referencePath,
                NULL /* attributes */, TRUE /* after */);
    }

    public function addMusicPlayerEmbed($xpath) {
        //  Don't insert if there's already a music embed
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        $existingNode = $xpath->query($col1 . '/module'
                . '[@widgetName="music"][@action="embed1"]')->item(0);
        if ($existingNode) {
            return;
        }

        //  Insert after small badge
        $referencePath = $col1 . '/module[@widgetName="profiles"][@action="embed1smallbadge"]';
        $instanceId = $this->insertModule('music', 'embed1', $referencePath,
                NULL /* attributes */, TRUE /* after */);
    }

    public function addGroupsEmbed($xpath) {
        //  Don't insert if there's already a groups embed
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        $existingNode = $xpath->query($col1 . '/module'
                . '[@widgetName="groups"][@action="embed1"]')->item(0);
        if ($existingNode) {
            return;
        }

        //  Insert after profile question answers
        $referencePath = $col1 . '/module[@widgetName="profiles"][@action="embed1profileqa"]';
        $instanceId = $this->insertModule('groups', 'embed1', $referencePath,
                NULL /* attributes */, TRUE /* after */);
    }

    public function moveRssEmbedToLeftOrAdd($xpath) {
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        $col2 = '/layout/colgroup/column/colgroup/column[2]';

        //  Don't insert if there's already an RSS embed in the left column
        $existingNode = $xpath->query($col1 . '/module'
                . '[@widgetName="feed"][@action="embed1"]')->item(0);
        if ($existingNode) {
            return;
        }

        //  Find video embed in left column
        $videoEmbed = $xpath->query($col1 . '/module'
                . '[@widgetName="video"][@action="embed1"]')->item(0);
        if (!$videoEmbed) {
            return;
        }

        //  Find existing RSS embed in center column
        //  (Don't search by action - old layouts erroneously contained an
        //    embed1 in the second column)
        $rssEmbed = $xpath->query($col2 . '/module'
                . '[@widgetName="feed"]')->item(0);
        if (!$rssEmbed) {
            //  Not present - add below video
            $this->insertModule('feed', 'embed1', $col1 . '/module'
                    . '[@widgetName="video"][@action="embed1"]',
                    NULL /* attributes */, TRUE /* after */);
        } else {
            //  Move from center column to left
            $rssEmbed->parentNode->removeChild($rssEmbed);
            $rssEmbed->setAttribute('action', 'embed1');
            $videoEmbed->parentNode->insertBefore($rssEmbed,
                    $videoEmbed->nextSibling);
        }
    }

    public function addForumEmbed($xpath) {
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        //  Insert at bottom of the column if not already present
        $instanceId = $this->addModuleOnce('forum', 'embed1', $col1,
                NULL /* attributes */, TRUE /* after */);
    }

	public function addEventsEmbed($xpath) {
        $col1 = '/layout/colgroup/column/colgroup/column[1]';
        $referencePath = $col1 . '/module[@widgetName="feed"][@action="embed1"]';
        $instanceId = $this->insertModule('events', 'embed1', $referencePath,
                NULL /* attributes */, FAlSE /* before */);
    }

    public function addRssEmbed($xpath) {
        //  Don't insert if there's already an RSS embed
        $col2 = '/layout/colgroup/column/colgroup/column[2]';
        $existingNode = $xpath->query($col2 . '/module'
                . '[@widgetName="feed"][@action="embed2"]')->item(0);
        if ($existingNode) {
            return;
        }

        //  If there's an embed1 embed in the second column (as in most versions
        //    up to 1.6), change it to an embed2 (and don't add another)
        $existingNode = $xpath->query($col2 . '/module'
                . '[@widgetName="feed"][@action="embed1"]')->item(0);
        if ($existingNode) {
            $existingNode->setAttribute('action', 'embed2');
            return;
        }

        //  Insert at bottom of the column
        $instanceId = $this->addModule('feed', 'embed2', $col2,
                NULL /* attributes */, TRUE /* after */);
    }
    
    public function addEditablePageTitle($xpath) {
        $col2 = '/layout/colgroup/column/colgroup/column[2]';
        $existingNode = $xpath->query($col2 . '/module'
                . '[@widgetName="profiles"][@action="embed2pagetitle"]')->item(0);
        if ($existingNode) {
            return;
        }
        //  Insert at top of the column
        $instanceId = $this->addModuleOnce('profiles', 'embed2pagetitle', $col2,
                NULL /* attributes */, FALSE /* before */);
    }

    // Callbacks
    //
    // Each type of layout must implement four callbacks:
    // - load: this returns a layout if one is available or returns null
    // - setup: this populates the layout with appropriate defaults
    // - save: this persists the layout however appropriate
    // - getOwnerName: this returns the username of the person who owns this page
    // - isOwner: whether the specified user is considered an owner of this page.
    //
    // Callback names are callback_method, e.g. callback_load or callback_save

    /**
     * Returns the XML for the layout if one is available; otherwise returns null
     *
     * @param $name string  the username that identifies the layout instance, e.g., 'joe'
     * @return string  the layout XML, or null if the specified layout does not exist
     */
    protected function callback_load($name) {
        $user = User::load($name);
        // Set this here so it's available in the destructor (when there is no active widget)
        // This is explicitly the 'profiles' widget -- the code may run in the context of other widgets when saving
        // changes to layout config
        $this->attributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), $this->attributeNameBase);
        if ($user && (mb_strlen($user->my->{$this->attributeName}))) {
            return $user->my->{$this->attributeName};
        } else {
            return null;
        }
    }

    /**
     * Populates the layout with appropriate defaults
     *
     * @param $name string  the username that identifies the layout instance, e.g., 'joe'
     */
    protected function callback_setup($name) {
        /* The screen name is specified as a property for all these modules so that their content is not
         * explicitly connected to the layout in which they appear */
         $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $defaultLayout=<<<_XML_
<layout nextEmbedInstanceId="100" version="7">
    <colgroup locked="1">
        <column width="3">
          <module widgetName="profiles" action="embed3pagetitle" embedInstanceId="10"><screenName>$safeName</screenName></module>
          <colgroup>
            <column width="1">
              <module widgetName="profiles" action="embed1smallbadge" embedInstanceId="0"><screenName>$safeName</screenName></module>
              <module widgetName="profiles" action="embed1friends" embedInstanceId="6"><screenName>$safeName</screenName></module>
              <module widgetName="music" action="embed1" embedInstanceId="14" />
              <module widgetName="groups" action="embed1" embedInstanceId="13" />
              <module widgetName="forum" action="embed1" embedInstanceId="11" />
			  <module widgetName="events" action="embed1" embedInstanceId="20" />
              <module widgetName="feed" action="embed1" embedInstanceId="4"/>
            </column>
            <column width="2">
              <module widgetName="profiles" action="embed2pagetitle" embedInstanceId="10"><screenName>$safeName</screenName></module>
              <module widgetName="profiles" action="embed3welcome" embedInstanceId="12"><visible>1</visible></module>
              <module widgetName="activity" action="embed2" embedInstanceId="15"><screenName>$safeName</screenName></module>
              <module widgetName="profiles" action="embed2profileqa" embedInstanceId="3"><screenName>$safeName</screenName></module>
              <module widgetName="html" action="embed2" embedInstanceId="5" />
              <module widgetName="photo" action="embed2" embedInstanceId="1" />
              <module widgetName="video" action="embed2" embedInstanceId="2" />
              <module widgetName="profiles" action="embed2blogposts" embedInstanceId="7"><screenName>$safeName</screenName></module>
              <module widgetName="profiles" action="embed2chatterwall" embedInstanceId="8"><screenName>$safeName</screenName></module>
            </column>
          </colgroup>
        </column>
        <column width="1" locked="1">
          <module widgetName="main" action="embed1you" embedInstanceId="21" sitewide="1"/>
          <module widgetName="main" action="embed1ads" embedInstanceId="26" sitewide="1"/>
          <module widgetName="main" action="embed1createdBy" embedInstanceId="25" sitewide="1"/>
          <module widgetName="profiles" action="embed1badge" embedInstanceId="18" sitewide="1"/>
        </column>
    </colgroup>
</layout>
_XML_;
        $defaultLayout = self::removeWhitespaceBetweenTags($defaultLayout);
        $this->_layout->loadXML(trim($defaultLayout));

    }

    /**
     * Updates and saves the layout (e.g. to a content object, or to a file)
     *
     * @param $currentLayout  string  the layout XML
     */
    protected function callback_save($currentLayout) {
        // We don't want to mess with real User and XN_Profile data during tests so just skip this bit
        // if we are testing an XG_Layout_profiles.
        if (defined('UNIT_TESTING')) { return; }
        try {
            $user = User::load($this->_name);
            $user->my->{$this->attributeName} = $currentLayout;
            $user->save();
        } catch (Exception $e) {
            error_log("Can't save layout: " . $e->getMessage());
            if (is_callable(array($e,'getErrorsAsString'))) {
                error_log("Content error: " . $e->getErrorsAsString());
            }
        }
    }

    /**
     * Returns the username of the person who owns this page
     *
     * @return string  the screen name of the layout owner
     */
    protected function callback_getOwnerName() {
        return $this->_name;
    }

    /**
     * Returns whether the specified user is considered an owner of this page.
     *
     * @param $profile XN_Profile  the user
     * @return boolean  whether the user is considered to be an owner of the layout
     */
    public function callback_isOwner($profile) {
        return strcasecmp($profile->screenName, $this->getOwnerName()) == 0;
    }

    /**
     * Returns the maximum <embed> width for each column, ordered depth-first.
     *
     * @override
     */
    protected function getMaxEmbedWidthsForColumns() {
        return array(730, 171, 540, 173);
    }
    
    /**
     * The basic XML for this type of layout with all the optional/movable modules removed.
     * Called by the "clear" function when reconstructing a layout.
     */
    //TODO: It would be more consistent to ensure the presence of the sidebar on profiles layouts by including it getProfilesEmbedList, 
    //instead of in the default xml. [Thomas David Baker 2008-05-22]
    public function baseXml() {
        return '<layout iteration="1" nextEmbedInstanceId="100">
            <colgroup locked="1">
                <column width="3">
                    <colgroup>
                        <column width="1">
                        </column>
                        <column width="2">
                        </column>
                    </colgroup>
                </column>
                <column width="1" locked="1">
                    <module widgetName="main" action="sidebar" />
                </column>
            </colgroup>
        </layout>';
    }
}
