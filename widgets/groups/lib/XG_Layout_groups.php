<?php

XG_App::includeFileOnce('/lib/XG_Layout.php');

/**
 * Layout for the group page
 */
class XG_Layout_groups extends XG_Layout {

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
     * @param $groupId string  the content ID of the group
     * @return string  the layout XML, or null if the specified layout does not exist
     */
    protected function callback_load($groupId) {
        return Group::load($groupId)->my->{self::getAttributeName()};
    }

    /**
     * Populates the layout with appropriate defaults
     *
     * @param $groupId string  the content ID of the group
     */
    protected function callback_setup($groupId) {
        //TODO there is an old-fashioned sidebar module in this XML that we haven't removed.
        $escapedGroupId = htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8');
        // Use forceDisplay to ensure that the forum embed displays even if the forum widget is disabled,
        // i.e. tab and modules are not displayed. See "Bazel Code Structure", Ning internal wiki. [Jon Aquino 2007-05-07]
        $xml=<<<_XML_
<layout nextEmbedInstanceId="100">
    <column width="4">
        <module widgetName="groups" action="embed3pagetitle" embedInstanceId="1"></module>
        <column width="3">
            <module widgetName="groups" action="embed2description" embedInstanceId="2"></module>
            <module widgetName="html" action="embed2" embedInstanceId="7" isActive="1" />
            <module widgetName="forum" action="embed2" embedInstanceId="8" forceDisplay="1" isActive="1"/>
            <module widgetName="feed" action="embed2" embedInstanceId="9" isActive="0" />
            <module widgetName="groups" action="embed2chatterwall" embedInstanceId="10" isActive="1" />
        </column>
        <column width="1">
            <module widgetName="groups" action="embed1controls" embedInstanceId="6"></module>
            <module widgetName="groups" action="embed1members" embedInstanceId="3"></module>
        </column>
    </column>
    <column width="1" locked="1">
      <module widgetName="main" action="sidebar"/>
    </column>
</layout>
_XML_;
        $xml = self::removeWhitespaceBetweenTags($xml);
        $this->_layout->loadXML(trim($xml));

    }

    /**
     * Updates and saves the layout (e.g. to a content object, or to a file)
     *
     * @param $currentLayout  string  the layout XML
     */
    protected function callback_save($currentLayout) {
        $group = Group::load($this->_name);
        $group->my->{self::getAttributeName()} = $currentLayout;
        $group->save();
    }

    /**
     * Returns the username of the person who owns this page
     *
     * @return string  the screen name of the layout owner
     */
    protected function callback_getOwnerName() {
        return Group::load($this->_name)->contributorName;
    }

    /**
     * Returns whether the specified user is considered an owner of this page.
     *
     * @param $profile XN_Profile  the user
     * @return boolean  whether the user is considered to be an owner of the layout
     */
    public function callback_isOwner($profile) {
        return XG_GroupHelper::isGroupAdmin($profile->screenName);
    }

    /**
     * Updates the older style layout to the new groups style
     *
     * @param DOMXPath the old style DOM layout
     * @return DOMXPath the updated layout
     */
    public function updateGroupsLayout() {
        // TODO: Eliminate duplication between this XML and the XML above [Jon Aquino 2008-02-29]
        $newLayout=<<<_XML_
<layout nextEmbedInstanceId="100">
    <column width="4">
        <module widgetName="groups" action="embed3pagetitle" embedInstanceId="1"></module>
        <column width="3">
            <module widgetName="groups" action="embed2description" embedInstanceId="2"></module>
            <module widgetName="html" action="embed2" embedInstanceId="7" isActive="1" />
            <module widgetName="forum" action="embed2" embedInstanceId="8" forceDisplay="1" isActive="1"/>
            <module widgetName="feed" action="embed2" embedInstanceId="9" isActive="0" />
            <module widgetName="groups" action="embed2chatterwall" embedInstanceId="10" isActive="1" />
        </column>
        <column width="1">
            <module widgetName="groups" action="embed1controls" embedInstanceId="6"></module>
            <module widgetName="groups" action="embed1members" embedInstanceId="3"></module>
        </column>
    </column>
    <column width="1" locked="1">
      <module widgetName="main" action="sidebar"/>
    </column>
</layout>
_XML_;
        $newLayout = self::removeWhitespaceBetweenTags($newLayout);
        $this->_layout->loadXML(trim($newLayout));
    }

    /**
     * Returns the name of the attribute storing the XML on the Group object
     */
    private function getAttributeName() {
        // Explicitly set the widget, as we may not be in the groups widget when saving changes to the layout [Jon Aquino 2007-04-19]
        return XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'layout');
    }

    /**
     * Returns the maximum <embed> width for each column, ordered depth-first.
     *
     * @override
     */
    protected function getMaxEmbedWidthsForColumns() {
        return array(730, 545, 171, 173);
    }
}
