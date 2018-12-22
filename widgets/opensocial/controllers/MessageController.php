<?php

W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_MessageHelper.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');

/**
 * Provides requestNavigateTo redirects
 */
class OpenSocial_MessageController extends W_Controller {
    /**
     * Get information about the people ('VIEWER_FRIENDS', 'OWNER_FRIENDS', ...) and the app
     *
     * @param $_GET['appUrl'] string   url of the app
     * @param $_GET['ownerId'] string  owner of the app
     * @param $_GET['viewerId'] string viewer of the app
     * @param $_GET['ids'] string      ids for which the people information is needed
     */
    public function action_getAppInfo() {
        $appUrl   = $_GET['appUrl'];
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ownerId  = $_GET['ownerId'] ? $_GET['ownerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ids      = $_GET['ids'] ? $_GET['ids'] : 'VIEWER';

        $friendData = OpenSocial_PersonHelper::getPeople($appUrl, $viewerId, $ownerId, explode(',',$ids), OpenSocial_PersonHelper::FRIEND_FORMAT);
        $this->people = $friendData['friends'];
        $this->numUsers = $friendData['numUsers'];
        $appdata = OpenSocialAppData::load($appUrl, $viewerId);
        $this->appdata = $appdata ? $appdata->getSettings() : array();
        $this->gadgetprefs = OpenSocial_GadgetHelper::readGadgetUrl($appUrl);
    }

    /**
     * Get information about the people ('VIEWER_FRIENDS', 'OWNER_FRIENDS', ...) in the 'friendData' format
     *
     * @param $_GET['appUrl'] string   url of the app
     * @param $_GET['ownerId'] string  owner of the app
     * @param $_GET['viewerId'] string viewer of the app
     * @param $_GET['ids'] string      ids for which the people information is needed
     */
    public function action_friendData() {
        $appUrl   = $_GET['appUrl'];
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ownerId  = $_GET['ownerId'] ? $_GET['ownerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ids      = $_GET['ids'] ? $_GET['ids'] : 'VIEWER';

        $friendData = OpenSocial_PersonHelper::getPeople($appUrl, $viewerId, $ownerId, explode(',',$ids), OpenSocial_PersonHelper::FRIEND_FORMAT);
        $this->friends = $friendData['friends'];
        $this->paginationHtml = $friendData['paginationHtml'];
        $this->numFriends = $friendData['numFriends'];
    }

    /**
     * Check the rate limit information for the app/user for the day
     *
     * @param $_GET['appUrl'] string    url of the app
     * @param $_GET['viewerId'] string  viewer of the app
     * @param $_GET['msgType'] string   type of message - requestSendMessage or ...
     */
    public function action_rateLimitCheck() {
        $appUrl   = $_GET['appUrl'];
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $msgType  = $_GET['msgType'];

        $this->rateLimitExceeded = OpenSocialAppData::rateLimitCheck($appUrl, $viewerId, $msgType);
    }

    /**
     * Check the rate limit information for the app/user for the day & log the current request
     *
     * @param $_GET['appUrl'] string    url of the app
     * @param $_GET['viewerId'] string  viewer of the app
     * @param $_GET['msgType'] string   type of message - requestSendMessage or ...
     */
    public function action_rateLimitCheckAndUpdate() {
        $appUrl   = $_GET['appUrl'];
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $msgType  = $_GET['msgType'];

        $this->rateLimitExceeded = OpenSocialAppData::rateLimitCheckAndUpdate($appUrl, $viewerId, $msgType);
    }

    /**
     * Initiate the send of message (by creating Tasks which will get executed later), with the following parameters.
     *
     * 1) If the user clicks Select None then selects some friends:
     *     a) $_POST['friendSet'] will be ''
     *     b) $_POST['screenNamesIncluded'] will contain a JSON array of screen names to include
     *
     * 2) If the user clicks Select All Friends then unselects some friends
     *     a) $_POST['friendSet'] will be Index_MessageHelper::ALL_FRIENDS
     *     b) $_POST['screenNamesExcluded'] will contain a JSON array of screen names to exclude
     *
     * @param $_GET['appUrl'] string       url to the OpenSource Application
     * @param $_GET['msgType'] string      type of message
     * @param $_GET['ownerId'] string      screenName of the owner of the OSApplication
     * @param $_GET['viewerId'] string     screenName of the viewer of the OSApplication
     * @param $_GET['xn_out'] string       (json)
     * @param $_POST['ids'] string         original set of recipient OpenSocial-Ids specified by the OSApplication
     * @param $_POST['numFriends'] string  actual number of friends calculated for the given OpenSocial-ids
     * @param $_POST['subject'] string     subject of the email
     * @param $_POST['message'] string     body of the email
     */
    public function action_sendQuick() {
        $this->status = 'fail';
        $this->message = xg_text('PROBLEM_SENDING_MESSAGE');
        $this->render('blank');

        $appUrl   = $_GET['appUrl'];
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ownerId  = $_GET['ownerId'] ? $_GET['ownerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        
        // if testing - then read from GET's
        if ($_GET['testing'] === '1') {
            $_POST['dontPromptBeforeSending'] = $_GET['dontPromptBeforeSending'];
            $_POST['screenNamesIncluded'] = $_GET['screenNamesIncluded'];
            $_POST['screenNamesExcluded'] = $_GET['screenNamesExcluded'];
            $_POST['friendSet'] = $_GET['friendSet'];
            $_POST['numFriends'] = $_GET['numFriends'];
            $_POST['subject'] = $_GET['subject'];
            $_POST['message'] = $_GET['message'];
            $_POST['ids'] = $_GET['ids'];
        }

        $exceeded = OpenSocialAppData::rateLimitCheckAndUpdate($appUrl, $viewerId, $_GET['msgType']);

        if ($exceeded) {
            $this->message = xg_text('RATE_LIMIT_EXCEEDED');
            return;
        }

        if (isset($_POST['dontPromptBeforeSending']))
            OpenSocialAppData::updateSetting($appUrl, $ownerId, 'promptBeforeSending', !$_POST['dontPromptBeforeSending']);

        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $screenNamesIncluded = $json->decode($_POST['screenNamesIncluded']);
        $screenNamesExcluded = $json->decode($_POST['screenNamesExcluded']);
        $count = $_POST['numFriends'];
        
        if (!$count) {  // no friends?  trivially a successful operation!
            $this->status = 'ok';
            $this->message = xg_text('YOUR_MESSAGE_HAS_BEEN');
            return;
        }

        if ((! $screenNamesIncluded && ! $_POST['friendSet']) || ($screenNamesExcluded && (count($screenNamesExcluded) == $count))) {
            $this->message = xg_text('PLEASE_CHOOSE_FRIENDS');
            return;
        }

        // $screenNamesIncluded and $screenNamesExcluded will never be set together - they are mutually exclusive
        
        if (mb_strlen($_POST['message']) > Index_InvitationHelper::MAX_MESSAGE_LENGTH) {
            $this->message = xg_text('MESSAGE_TOO_LONG', Index_InvitationHelper::MAX_MESSAGE_LENGTH);
            return;
        }
        
        if (!$_POST['friendSet']) {
            $count = count($screenNamesIncluded);
        }
        
        // Exclude the URL length for truncation...
        $message = $_POST['message'];
        $urlLength = 0;
        if (preg_match('@(http://(?:\S+))@u', $message, $matches))
            $urlLength = mb_strlen($matches[0]);
        $message = mb_substr($message, 0, $urllength+200);
        
        $taskContentTemplate = array(
                                'appUrl'    => $appUrl,
                                'ownerId'   => $ownerId,
                                'viewerId'  => $viewerId,
                                'msgType'   => $_GET['msgType'] ? $_GET['msgType'] : 'requestSendMessage',
                                'subject'   => $_POST['subject'],
                                'message'   => $message,
                                'friendSet' => $_POST['friendSet'],
                                'ids'       => $_POST['ids'],
                                'screenNamesIncluded' => $json->encode($screenNamesIncluded),
                                'screenNamesExcluded' => $json->encode($screenNamesExcluded));
        
        $taskCount = 0;
        $emailsPerTask = 50;
        $indexes = XG_LangHelper::indexes(0, $count, $emailsPerTask);
        $job = XN_Job::create();
        for ($i = 0; $i < count($indexes) - 1; $i++) {
            $taskContent = $taskContentTemplate;
            $taskContent['friendStart'] = $indexes[$i];
            $taskContent['friendEnd'] = $indexes[$i+1];
            $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(W_Cache::current('W_Widget')->buildUrl('message', 'sendProper')), $taskContent));
            $taskCount++;
        }

        $result = $job->save();
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(xg_text('ERROR_OCCURRED')); }
        
        if ($taskCount > 0) {
            $this->status = 'ok';
            $this->message = xg_text('YOUR_MESSAGE_HAS_BEEN');
        }
    }

    /**
     * Actual message send routine, which is invoked by each task scheduled in the send portion.
     *
     * @param $_POST['appUrl'] string      url to the OpenSource Application
     * @param $_POST['ownerId'] string     screenName of the owner of the OSApplication
     * @param $_POST['viewerId'] string    screenName of the viewer of the OSApplication
     * @param $_POST['msgType'] string     type of message
     * @param $_POST['subject'] string     subject of the email
     * @param $_POST['message'] string     body of the email
     * @param $_POST['friendSet'] string             parameter passed into original sendQuick request
     * @param $_POST['screenNamesIncluded'] string   parameter passed into original sendQuick request
     * @param $_POST['screenNamesExcluded'] string   parameter passed into original sendQuick request
     * @param $_POST['ids'] string                   original set of recipient OpenSocial-Ids specified by the OSApplication
     */
    public function action_sendProper() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (9123348300)'); }
        if (! User::isMember(XN_Profile::current())) { throw new Exception('Not a member (9123348301)'); }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $appUrl              = $_POST['appUrl'];
        $viewerId            = $_POST['viewerId'];
        $ownerId             = $_POST['ownerId'];
        $screenNamesIncluded = $json->decode($_POST['screenNamesIncluded']);
        $screenNamesExcluded = $json->decode($_POST['screenNamesExcluded']);
        $friendSet           = $_POST['friendSet'];
        $ids                 = explode(',', $_POST['ids']);
        $start               = $_POST['friendStart'];
        $end                 = $_POST['friendEnd'];
        $count               = $end - $start;
        
        $recipients = array();
        if (!$screenNamesIncluded) $screenNamesIncluded = array();
        if (!$screenNamesExcluded) $screenNamesExcluded = array();
        
        if ($friendSet) {
            $results = OpenSocial_PersonHelper::getPeople($appUrl, $viewerId, $ownerId, $ids, OpenSocial_PersonHelper::FULL_FORMAT, "all", "name", $start, $count);
            $people = $results['people'];
            foreach ($people as $p) {
                if (in_array($p['id'], $screenNamesExcluded)) continue;
                $recipients[] = $p['id'];
            }
        } else {
            // Get the Nth chunk from the screenName included list
            for ($i = $start; $i < $end; $i++) {
                $recipients[] = $screenNamesIncluded[$i];
            }
        }
        
        OpenSocial_MessageHelper::send($_POST['appUrl'], $_POST['subject'], $_POST['message'], $recipients);
    }

    /**
     * Form displayed to the user prior to sending a message
     * 
     * @param $_GET['appUrl'] string       url to the OpenSource Application
     * @param $_GET['appTitle'] string     title of the OpenSource Application
     * @param $_GET['msgType'] string      type of message
     * @param $_GET['ownerId'] string      screenName of the owner of the OSApplication
     * @param $_GET['viewerId'] string     screenName of the viewer of the OSApplication
     * @param $_GET['xn_out'] string       (json)
     * @param $_POST['ids'] string         original set of recipient OpenSocial-Ids specified by the OSApplication
     * @param $_POST['message'] string     body of the email
     * @param $_POST['subject'] string     subject of the email
     * @param $_POST['showFriends'] string show the friends select box
     */
    public function action_sendMessageForm() {
        $appUrl   = $_GET['appUrl'];
        $appTitle = $_GET['appTitle'];
        $msgType  = $_GET['msgType'];
        $ownerId  = $_GET['ownerId'] ? $_GET['ownerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $viewerId = $_GET['viewerId'] ? $_GET['viewerId'] : OpenSocial_PersonHelper::ANONYMOUS;
        $ids      = $_GET['ids'] ? $_GET['ids'] : 'VIEWER';
        $message  = $_GET['message'];
        $subject  = $_GET['subject'];
        $showFriends = $_GET['showFriends'];

        $peopleResults = OpenSocial_PersonHelper::getPeople($appUrl, $viewerId, $ownerId, explode(',',$ids));
        $this->people = $peopleResults['people'];
        $this->numUsers = $peopleResults['totalSize'];
        $this->renderPartial('fragment_sendMessageForm', '_shared', array('appUrl' => $appUrl, 'appTitle' => $appTitle,
                                                                          'viewerId' => $viewerId, 'ownerId' => $ownerId,
                                                                          'msgType' => $msgType, 'ids' => explode(',',$ids),
                                                                          'subject' => $subject, 'message' => $message,
                                                                          'showFriends' => $showFriends));

    }
}

?>
