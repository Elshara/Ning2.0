<?php

W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_PrivacyHelper.php');

/**
 * Dispatches requests to do with privacy (public/private and approval settings).
 */
class Index_PrivacyController extends W_Controller {

    /**
     * Offers an edit screen to the user allowing them to alter the privacy settings of their network.
     */
    public function action_edit() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { return $this->forwardTo('save'); }
        $this->setPrivacyUrl = $this->_buildUrl('bulk', 'setPrivacy', array('limit' => 30, 'id' => $this->page->id, 'xn_out' => 'json'));
        $this->setPrivacySuccessUrl = $this->_buildUrl('privacy', 'edit', '?saved=1');
        $this->privacyLevelPrivateChecked = XG_App::appIsPrivate();
        $this->privacyLevelPublicChecked = ! $this->privacyLevelPrivateChecked;
        $nonregVisibility = W_Cache::getWidget('main')->config['nonregVisibility'];
        $this->nonregVisibilityEverythingChecked = ($nonregVisibility === 'everything');
        $this->nonregVisibilityHomepageChecked = ($nonregVisibility === 'homepage');
        $this->nonregVisibilityMessageChecked = ($nonregVisibility === 'message');
        $this->allowInvitesChecked = XG_App::allowInvites();
        $this->allowRequestsChecked = XG_App::allowInviteRequests();
        $this->approveMediaChecked = XG_App::contentIsModerated();
        $this->approveMembersChecked = XG_App::membersAreModerated();
        $this->groupCreationChecked = XG_App::membersCanCreateGroups();
        $this->approveGroupsChecked = XG_App::groupsAreModerated();
        $this->eventCreationChecked = XG_App::membersCanCreateEvents();
        $this->enableMusicDownloadChecked = ! XG_App::musicDownloadIsDisabled();
        $this->allowJoinAllChecked = XG_App::allowJoinByAll();
        $this->allowCustomizeThemeChecked = XG_App::membersCanCustomizeTheme();
        $this->allowCustomizeLayoutChecked = XG_App::membersCanCustomizeLayout();
        $this->allow3rdPartyApplicationsChecked = XG_App::openSocialEnabled();
        $this->bulkInvitationUrl = Index_InvitationHelper::getBulkInvitationUrl();
    }

    /**
     *  Saves the the changes to privacty settings specified in $_POST.
     *
     * TODO: These variable details are incomplete ... update or remove comment [Thomas David Baker 2008-05-16]
     *  Expected $_POST var:
     *  privacyLevel = public/private
     *
     * Possible $_POST var:
     *  approveMedia = yes (or var not present for no)
     *
     * If 'public', expected:
     *  nonregVisibility = everything/homepage/message
     *
     * If 'private', possible but not required:
     *  allowInvites = yes (or var not present for no)
     *  allowRequests = yes (or var not present for no)
     */
    public function action_save() {
        XG_SecurityHelper::redirectIfNotOwner();
        Index_PrivacyHelper::setPrivacySettings($_POST);
    }

    /**
     * Creates a new value for the network's "bulk-invitation link", which
     * is a URL that can be reused by an unlimited number of people to gain access
     * to the network. Calling this function expires the current bulk-invitation URL.
     */
    public function action_generateBulkInvitationUrl() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('edit'); }
        Index_InvitationHelper::generateBulkInvitationUrl();
        $this->redirectTo('edit');
    }
}
