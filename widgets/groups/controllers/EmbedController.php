<?php

/**
 * Dispatches requests pertaining to "embeds", which are reusable
 * page components.
 */
class Groups_EmbedController extends XG_GroupEnabledController {

    /**
     * Displays a group-list module that spans 1 column.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }

    /**
     * Displays a group-list module that spans 2 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }

    /**
     * Displays a group-list module that spans 3 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed3($args) { $this->renderEmbed($args['embed'], 3); }

    /**
     * Displays a group-list module that spans the given number of columns.
     *
     * @param $embed XG_Embed  stores the module data.
     * @param $columnCount integer  the number of columns that the module will span
     */
    private function renderEmbed($embed, $columnCount) {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        if ($embed->getType() == 'profiles') {
            $this->options = array(array('label' => xg_text('MOST_RECENT'), 'value' => 'recent'));
        } else {
            $this->options = array(
                    array('label' => xg_text('MOST_RECENT'), 'value' => 'recent'),
                    array('label' => xg_text('MOST_MEMBERS'), 'value' => 'popular'),
                    array('label' => xg_text('LATEST_ACTIVITY'), 'value' => 'active'),
                    array('label' => xg_text('FEATURED'), 'value' => 'promoted'));
        }
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $this->updateEmbedUrl = $this->_buildUrl('embed', 'updateEmbed', array('id' => $embed->getLocator(), 'xn_out' => 'json'));
        $embed->set('groupSet', $embed->get('groupSet') ? $embed->get('groupSet') : 'recent');
        $this->embed = $embed;
        if (is_null($embed->get('itemCount'))) { $embed->set('itemCount', $columnCount == 1 ? 5 : 6); }
        $this->groups = $this->groups($embed, $totalCount);
        $this->showViewAllLink = TRUE;
        XG_Cache::profiles($this->groups);
        if ($_GET['test_no_groups']) { $this->groups = array(); }
        $this->title = xg_text('GROUPS');
        if ($embed->getType() == 'profiles') { $this->title = $this->embed->isOwnedByCurrentUser() ? xg_text('MY_GROUPS') : xg_text('USERS_GROUPS', xg_username(XG_Cache::profiles($this->embed->getOwnerName()))); }
        $this->columnCount = $columnCount;
        if ((! $this->groups && $this->embed->getType() == 'profiles') || (! $this->groups && ! $this->embed->isOwnedByCurrentUser()) || (!$this->embed->isOwnedByCurrentUser() && $this->embed->get('itemCount') == 0)) { return $this->renderNothing(); }
        $this->render('embed');
    }



    /**
     * Retrieves the groups for the group-list module.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $totalCount integer  Output for the total number of groups in the groupSet
     * @return array  The Group content objects for the module
     */
    protected function groups($embed, &$totalCount = null) {
        $itemCount = $embed->get('itemCount') == 0 && $embed->isOwnedByCurrentUser() ? 1 : $embed->get('itemCount');
        if ($itemCount == 0) { return array(); }
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        if (! XG_PromotionHelper::areQueriesEnabled() && $embed->get('groupSet') == 'promoted') {
            $totalCount = 0;
            return array();
        }
        $query = XN_Query::create('Content')->end($itemCount);
        if (($embed->getType() != 'profiles') || XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
        }
        $groups = self::filter($embed->get('groupSet'), $embed->getType())
                ->execute($query, $embed->getType() == 'profiles' ? $embed->getOwnerName() : null);
        $totalCount = $query->getTotalCount();
        return $groups;
    }

    /**
     * Returns the Groups_Filter for the specified group set and embed type.
     *
     * @param $groupSet string  which groups to display (popular, recent, or promoted)
     * @param $embedType string  location of the embed (homepage or profiles)
     * @return Groups_Filter  a query filter that filters and sorts Groups.
     */
    private static function filter($groupSet, $embedType) {
        if ($embedType == 'profiles') { return Groups_Filter::get('joined'); }
        if ($groupSet == 'popular') { return Groups_Filter::get('mostPopular'); }
        if ($groupSet == 'recent') { return Groups_Filter::get('mostRecent'); }
        if ($groupSet == 'active') { return Groups_Filter::get('latestActivity'); }
        if ($groupSet == 'promoted') { return Groups_Filter::get('promoted'); }
        throw new Exception('Invalid groupSet: ' . $groupSet);
    }

    /**
     * Update the module body and footer HTML - only called for Frink drop updates
     *
     * Expected GET variables:
     *     id - The embed instance ID, used to retrieve the module data
     *
     * Expected POST variables:
     *     columnCount - the number of columns this module spans (1, 2 or 3)
     *
     * @return string   JSON string containing the new module body and footer
     */
    public function action_updateEmbed() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner (606058973)'); }
        $groups = $this->groups($embed, $totalCount);
        XG_Cache::profiles($groups);
        ob_start();
        $this->renderPartial('fragment_moduleBodyAndFooter', array('groups' => $groups, 'embed' => $embed, 'showViewAllLink' => TRUE, 'columnCount' => $_POST['columnCount']));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();
    }

    /**
     * Configures the module to display the groups specified in $_POST['groupSet'] (popular, recent, or promoted).
     * The new HTML will be in the moduleBodyAndFooterHtml property of the JSON output.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *
     * Expected POST parameters:
     *     groupSet - Which groups to display (popular, recent, or promoted)
     *     columnCount - the number of columns that the module spans
     */
    public function action_setValues() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_Filter.php');
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner (606058973)'); }
        $embed->set('groupSet', $_POST['groupSet']);
        $embed->set('itemCount', $_POST['itemCount']);
        $groups = $this->groups($embed, $totalCount);
        XG_Cache::profiles($groups);
        ob_start();
        $columnCount = XG_Embed::getValueFromPostGet('columnCount');
        $this->renderPartial('fragment_moduleBodyAndFooter', array('groups' => $groups, 'embed' => $embed, 'showViewAllLink' => TRUE, 'columnCount' => $columnCount));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Displays a module for the top of the group page
     */
    public function action_embed3pagetitle() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->group = XG_GroupHelper::currentGroup();
    }

    /**
     * Displays a group comment wall
     */
    public function action_embed2chatterwall() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Groups_CommentHelper.php');
        $this->group = XG_GroupHelper::currentGroup();
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        // How many comments on each page
        $this->pageSize = 10;
        // Pages start at 1, not 0
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        if ($this->page < 1) { $this->page = 1; }
        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;
        $this->commentInfo = Comment::getCommentsFor($this->group->id, $this->start, $this->end, null, 'createdDate', 'desc');
        $this->numPages = ceil($this->commentInfo['numComments'] / $this->pageSize);
    }

    /**
     * Displays a module showing the group description and details
     */
    public function action_embed2description() {
        $this->group = XG_GroupHelper::currentGroup();
    }

    /**
     * Displays a module showing administrative and member controls
     */
    public function action_embed1controls() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        $this->group = XG_GroupHelper::currentGroup();
    }


    /**
     * Displays a module showing a forum summary
     */
    public function action_embed2forum() {
        $this->group = XG_GroupHelper::currentGroup();
    }

    /**
     * Displays a module showing group members.
     */
    public function action_embed1members() {
        $this->_widget->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $this->group = XG_GroupHelper::currentGroup();
        $query = XN_Query::create('Content')->end(30);
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
        }
        $this->profiles = Groups_GroupMembershipFilter::get('mostActive')->profiles($query, $this->group->id);
        $this->groupMemberCount = $query->getTotalCount();
        $this->inviteUrl = Groups_SecurityHelper::currentUserCanSeeInviteLinks($this->group) ? $this->_buildUrl('invitation','new', array('groupId' => $this->group->id)) : null;
        $this->viewAllUrl = $this->groupMemberCount > count($this->profiles) ? $this->_buildUrl('user','list', array('groupId' => $this->group->id)) : null;
    }

    /**
     * Displays a module showing the group creator.
     */
    public function action_embed1creator() {
        $this->group = XG_GroupHelper::currentGroup();
    }

}
