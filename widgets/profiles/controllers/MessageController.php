<?php

/**
 * Dispatches requests pertaining to messaging.
 */
class Profiles_MessageController extends XG_BrowserAwareController {

    /**
     * Runs code before each action.
     */
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_MessageHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
    }

    public function action_new_iphone() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->user = mb_strlen($_GET['screenName']) ? User::load($_GET['screenName']) : User::loadByProfileAddress($_GET['id']);
        $this->profile = XG_Cache::profiles($this->user->contributorName);
        list($this->userAgeSex, $this->userLocation) = Profiles_UserHelper::getPrivateUserInfo($this->profile);
    }

    /**
     * generates paginated view of user's inbox
     */
    public function action_index() {
        return $this->forwardTo('listInbox');
    }

    /**
     * generates paginated view of user's inbox
     */
    public function action_listInbox() {
        Profiles_MessageHelper::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        $this->forwardTo('list', 'message', array(Profiles_MessageHelper::FOLDER_NAME_INBOX, $_GET['page']));
    }

    /**
     * generates paginated view of user's alerts mailbox
     */
    public function action_listAlerts() {
        Profiles_MessageHelper::invalidateAlertsUnreadMessageCountCache(XN_Profile::current()->screenName);
        $this->forwardTo('list', 'message', array(Profiles_MessageHelper::FOLDER_NAME_ALERTS, $_GET['page']));
    }

    /**
     * generates paginated view of user's sent mailbox
     */
    public function action_listSent() {
        $this->forwardTo('list', 'message', array(Profiles_MessageHelper::FOLDER_NAME_SENT, $_GET['page']));
    }

    /**
     * generates paginated view of user's archived mailbox
     */
    public function action_listArchive() {
        $this->forwardTo('list', 'message', array(Profiles_MessageHelper::FOLDER_NAME_ARCHIVE, $_GET['page']));
    }

    /**
     * generates paginated view of a user's mailbox folder
     *
     * @param folder string  the folder to view; must be one of 'Inbox', 'Sent', 'Archive'
     * @param page integer  the page number to view, 1-based
     *
     * GET parameters:
     * @param status string  status from the redirecting page (optional)
     */
    public function action_list($folder = Profiles_MessageHelper::FOLDER_NAME_INBOX, $page = 1) {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->numUnreadMsgs = Profiles_MessageHelper::getInboxUnreadMessageCount();
        $this->numUnreadAlertsMsgs = Profiles_MessageHelper::getAlertsUnreadMessageCount();
        $this->page = intval($page);
        $this->page = $this->page < 1 ? 1 : $this->page;
        $this->folder = $folder;
        $this->title = xg_html(strtoupper($folder));    /** @non-mb */
        $this->status = $_GET['status'];
        $this->listView = true;

        // load page of messages
        list ($this->messages, $this->totalMessages, $this->errorMessage) = self::loadMessagesByFolderPage($folder, $this->page, Profiles_MessageHelper::MESSAGE_LIST_PAGE_SIZE);
        $this->otherParties = Profiles_MessageHelper::otherParties($this->messages);
        $this->profiles = XG_Cache::profiles($this->otherParties);
        $this->pageSize = Profiles_MessageHelper::MESSAGE_LIST_PAGE_SIZE;
    }

    /**
     * compose a message (new, new with recipient(s), reply, and/or forward)
     *
     * @param action string  'new','reply','forward' - type of compose action
     * @param recipients string|Array(string)  pre-defined recipients
     * @param allowRecipientChange boolean  should the user be allowed to modify the pre-defined recipient list?
     * @param message XN_Message  for 'reply','forward', the message being replied to or forwarded
     * @param folder string  reference folder name
     * @param page integer  page number on reference folder
     * @param allFriends boolean  true if friend selector should be opened by default with all friends selected
     */
    public function action_compose($action = Profiles_MessageHelper::COMPOSE_NEW, $recipients = array(), $allowRecipientChange = true, $message = null, $folder = Profiles_MessageHelper::FOLDER_NAME_INBOX, $page = 1, $allFriends = false) {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->numUnreadMsgs = Profiles_MessageHelper::getInboxUnreadMessageCount();
        $this->numUnreadAlertsMsgs = Profiles_MessageHelper::getAlertsUnreadMessageCount();
        $this->action = $action;
        $this->profiles = XG_Cache::profiles((array) $recipients);
        $this->recipients = Profiles_MessageHelper::uniqueRecipients($recipients, $this->profiles);
        $this->senderProfile = XN_Profile::current();
        $this->allowRecipientChange = $allowRecipientChange;
        $this->message = $message;
        $this->folder = $folder;
        $this->title = $action === Profiles_MessageHelper::COMPOSE_NEW ? $allowRecipientChange ? xg_html('COMPOSE') : xg_html('SEND_A_MESSAGE') : xg_html(strtoupper($action));     /** @non-mb */
        $this->page = $page;
        $this->allFriends = $allFriends;
        $this->maxRecipients = Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS;
        XG_App::includeFileOnce('/lib/XG_MessageHelper.php');
        $this->messageParts = XG_MessageHelper::getDefaultMessageParts();
        if ($message) {
            // also load referenced message's sender and recipients
            $this->profiles = array_merge($this->profiles, XG_Cache::profiles($message->recipients));
            $this->profiles[$message->sender] = XG_Cache::profiles($message->sender);
            list ($this->msgSender, $this->msgRecipientList) = Profiles_MessageHelper::loadFormattedMessageUsers($message, $this->profiles);
        }
    }

    /**
     * compose a new message - stub
     *
     * GET parameters:
     * @param recipient string|Array  pre-defined set of recipients to use (email addresses or screenNames)
     * @param folder string  reference folder (optional)
     * @param page integer  page number on reference folder (optional)
     * @param allFriends integer  whether to open friend selector with all friends selected (1) or not (0)
     */
    public function action_new() {
        $recipients = is_array($_GET['recipient']) ?
                            $_GET['recipient'] :
                            explode(',', $_GET['recipient']);
        $allFriends = array_key_exists('allFriends', $_GET) && ($_GET['allFriends'] == 1);

        // filter list to only valid screenNames
        if (count($recipients) > 0) {
            XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
            $profiles = XG_Cache::profiles($recipients);
            $validRecipients = array();
            foreach ($recipients as $recipient) {
                $profile = array_key_exists($recipient, $profiles) ?
                                $profiles[$recipient] :
                                null;
                if (! is_null($profile)) {
                    // TODO: filter by whether the screenName is a friend?
                    // TODO: Yes we should do that at some point [Jon Aquino 2008-09-08]
                    $validRecipients[] = $profile->screenName;
                } else if (XG_ValidationHelper::isValidEmailAddress($recipient)) {
                    $validRecipients[] = $recipient;
                }
            }
        }
        // TODO: support multiple screenNames? probably should check that screenNames are valid
        $folder = array_key_exists('folder', $_GET) ? $_GET['folder'] : null;
        $page = array_key_exists('page', $_GET) ? intval($_GET['page']) : null;
        return $this->forwardTo('compose', 'message', array(Profiles_MessageHelper::COMPOSE_NEW, $validRecipients, true, null, $folder, $page, $allFriends));
    }

    /**
     * compose a new message from a profile - stub
     *
     * GET parameters:
     * @param screenName string  The screenName of the user to send a message to
     */
    public function action_newFromProfile() {
        if (! array_key_exists('screenName', $_GET)) {
            error_log('action_newFromProfile: accessed from [' . getenv('HTTP_REFERER') . '] without screenName [' . getenv('SCRIPT_URI') . ']');
            return $this->redirectTo('listInbox', 'message');
        }
        try {
            $user = User::load($_GET['screenName']);
        } catch (Exception $e) {
            error_log('action_newFromProfile: accessed from [' . getenv('HTTP_REFERER') . '] with invalid screenName [' . $_GET['screenName'] . ']');
            return $this->redirectTo('listInbox', 'message');
        }
        return $this->forwardTo('compose', 'message', array(Profiles_MessageHelper::COMPOSE_NEW, $user->contributorName, false, null));
    }

    /**
     * compose a reply to the sender and optionally all other recipients - stub
     *
     * GET parameters:
     * @param id string  message id beign replied to
     * @param folder string  reference folder (optional)
     * @param page integer  page number on reference folder (optional)
     * @param allFriends integer  whether to open friend selector with all friends selected (1) or not (0)
     * @param replyAll integer  whether to reply to the sender and all recipients (1), or the sender alone (0)
     */
    public function action_reply() {
        try {
            if (! array_key_exists('id', $_GET) || ! array_key_exists('folder', $_GET)) {
                throw new Exception('Call to action_reply without id[' . $_GET['id'] . '] or folder[' . $_GET['folder'] . ']');
            }
            $id = $_GET['id'];
            $message = Profiles_MessageHelper::loadMessage($id);
        } catch (Exception $e) {
            if ($id && $folder) {
                $this->redirectTo('show', 'message', array('id' => $id, 'folder' => $folder, 'error' => 1));
            } else {
                if (! $folder) { $folder = Profiles_MessageHelper::FOLDER_NAME_INBOX; }
                $this->redirectTo('list' . $folder, 'message');
            }
            return;
        }
        $folder = array_key_exists('folder', $_GET) ? $_GET['folder'] : null;
        $page = array_key_exists('page', $_GET) ? intval($_GET['page']) : null;
        $allFriends = array_key_exists('allFriends', $_GET) && ($_GET['allFriends'] == 1);
        $recipients = array_key_exists('replyAll', $_GET) && ($_GET['replyAll'] == 1) ?
                            array_unique(array_merge(array($message->sender), $message->recipients)) :
                            array($message->sender);
        return $this->forwardTo('compose', 'message', array(Profiles_MessageHelper::COMPOSE_REPLY, $recipients, true, $message, $folder, $page, $allFriends));
    }

    /**
     * forward a message - stub
     *
     * GET parameters:
     * @param id string  message id to forward
     * @param folder string  reference folder (optional)
     * @param page integer  page number on reference folder (optional)
     * @param allFriends integer  whether to open friend selector with all friends selected (1) or not (0)
     */
    public function action_forward() {
        try {
            if (! array_key_exists('id', $_GET) || ! array_key_exists('folder', $_GET)) {
                throw new Exception('Call to action_reply without id[' . $_GET['id'] . '] or folder[' . $_GET['folder'] . ']');
            }
            $id = $_GET['id'];
            $message = Profiles_MessageHelper::loadMessage($id);
        } catch (Exception $e) {
            if ($id && $folder) {
                $this->redirectTo('show', 'message', array('id' => $id, 'folder' => $folder, 'error' => 1));
            } else {
                if (! $folder) { $folder = Profiles_MessageHelper::FOLDER_NAME_INBOX; }
                $this->redirectTo('list' . $folder, 'message');
            }
            return;
        }
        $folder = array_key_exists('folder', $_GET) ? $_GET['folder'] : null;
        $page = array_key_exists('page', $_GET) ? intval($_GET['page']) : null;
        $allFriends = array_key_exists('allFriends', $_GET) && ($_GET['allFriends'] == 1);
        return $this->forwardTo('compose', 'message', array(Profiles_MessageHelper::COMPOSE_FORWARD, array(), true, $message, $folder, $page, $allFriends));
    }

    /**
     * send a message asynchronously and return the results via JSON
     *
     * GET parameters:
     * @param id  string  reference message id (if replying/forwarding; optional)
     * @param action string  compose action ('new', 'reply', 'forward')
     * @param folder string  reference folder
     * @param page integer  page number of reference folder
     * @param target string  where to redirect the user after a successful send (default is folder list view)
     * @param xn_out string  must be 'json'
     *
     * POST parameters:
     * @param fixedRecipients string  comma-delimited list of recipients that cannot be changed by the sender
     * @param presetRecipients string  comma-delimited list of pre-set recipients that *can* be changed by the sender
     * @param recipients string  comma-delimited list of email addresses
     * @param friendSet string  null/empty or FRIENDS_ON_NETWORK (friendList reference set)
     * @param screenNamesIncluded string  JSON-encoded string of additional screenNames to include
     * @param screenNamesExcluded string  JSON-encoded string of recipients to exclude from the reference set
     * @param subject string  subject of the message (if not replying or forwarding)
     * @param message string  message contents (excluding quoted reference message if replying or forwarding)
     *
     * AJAX response:
     * - success: the message was sent successfully
     * - target: where the user sould be redirected after sending
     *    -or-
     * - error: array of key => error message where key corresponds to the form field resulting in the error
     */
    public function action_send() {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        XG_HttpHelper::trimGetAndPostValues();

        $action = $_GET['action'];

        // pre-process friendList recipients, if any
        $friendListData = Profiles_MessageHelper::processFriendListRecipients($_POST);
        $this->error = array();
        // check if the number of friend selector friends is > max to short-circuit loading of all friends (BAZ-9868) [ywh 2008-09-13]
        if ($friendListData['numSelectedFriends'] > Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS) {
            $this->error['recipients'] = xg_html('SORRY_TOO_MANY_RECIPIENTS_FROM_FRIENDLIST', Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS);
            return;
        }
        // build unique recipient list
        list ($recipients, $rejectedEmailRecipients) = Profiles_MessageHelper::buildMessageRecipientList($_POST, $friendListData);
        if (count($rejectedEmailRecipients) > 0) {
            $this->error['recipients'] = xg_html('SORRY_INVALID_EMAIL_ADDRESSES');
            return;
        }
        if (count($recipients) < 1) {
            $this->error['recipients'] = xg_html('PLEASE_ENTER_AT_LEAST_ONE_RECIPIENT');
            return;
        } else if (count($recipients) > Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS) {
            $this->error['recipients'] = xg_html('SORRY_ENTERED_N_RECIPIENTS', Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS, count($recipients) - Profiles_MessageHelper::MAX_NUMBER_OF_RECIPIENTS);
            return;
        }

        // build target
        if (array_key_exists('target', $_GET)) {
            $this->target = $_GET['target'];
        } else {
            $folder = array_key_exists('folder', $_GET) ? $_GET['folder'] : Profiles_MessageHelper::FOLDER_NAME_INBOX;
            $folder = ucfirst(strtolower($folder)); /** @non-mb */
            $page = array_key_exists('page', $_GET) ? intval($_GET['page']) : 1;
            $this->target = $this->_buildUrl('message', 'list' . $folder, array('page' => $page));
        }

        // message contents
        $message = $_POST['message'];

        // load referenced message (if any)
        $refMessage = null;
        if (array_key_exists('id', $_GET)) {
            try {
                $id = $_GET['id'];
                $refMessage = Profiles_MessageHelper::loadMessage($id);
                // API should throw an excetion if it can't load the specified id
                // this check is temporary
                if ($refMessage->id != $id) { throw new Exception('Error loading message id [' . $id . ']'); }

                // replies/forwards inherit subject from referenced message
                $subject = $refMessage->subject;
                $message .= "\n" . Profiles_MessageHelper::addQuotedMessage($refMessage);
            } catch (Exception $e) {
                $this->error['unknown'] = xg_html('UNABLE_TO_COMPLETE_ACTION');
                return;
            }
        } else {
            $subject = $_POST['subject'];
        }

        // create message
        try {
            Profiles_MessageHelper::sendMessage($recipients, $subject, $message);
            $this->success = 1;
            if (! is_null($refMessage) && ($action === Profiles_MessageHelper::COMPOSE_REPLY)) {
                $refMessage->hasReplies = true;
                $refMessage->save();
            }
        } catch (Exception $e) {
            $this->error['unknown'] = $e->getMessage();
        }
    }

    /**
     * show message details
     *
     * GET parameters:
     * @param id string  message id to show
     * @param folder string  the Folder from which the user came
     * @param error integer  1=an error occurred on the previous action (optional)
     */
    public function action_show() {
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        XG_SecurityHelper::redirectIfNotMember(null, true);
        // redirect to inbox if no message id
        if (! array_key_exists('id', $_GET)) {
            $this->redirectTo('listInbox');
        }
        try {
            $id = $_GET['id'];
            $message = Profiles_MessageHelper::loadMessage($id);
            // API should throw an exception if it can't load the specified id
            // this check is temporary
            if ($message->id != $id) { throw new Exception('Error loading message id [' . $id . ']'); }
        } catch (Exception $e) {
            // invalid id or error loading message
            W_Cache::getWidget('main')->dispatch('error', '404');
            exit;
        }

        // load message profiles
        $this->showStatus = $_GET['error'] || $_GET['status'];
        $this->isError = $_GET['error'] ? true : false;
        $this->statusMessage = $this->isError ?
                                    xg_html('UNABLE_TO_COMPLETE_ACTION') :
                                    xg_html('STATUS_MESSAGE_' . strtoupper($_GET['status'])); /** @non-mb */
        $this->folder = array_key_exists('folder', $_GET) ? $_GET['folder'] : Profiles_MessageHelper::FOLDER_NAME_INBOX;
        $this->page = array_key_exists('page', $_GET) ? intval($_GET['page']) : 1;
        $this->message = $message;
        $this->profiles = Profiles_MessageHelper::cacheMessageProfiles($message);
        list ($this->sender, $this->recipientList) = Profiles_MessageHelper::loadFormattedMessageUsers($message, $this->profiles);

        $this->friendStatus = XG_ContactHelper::getFriendStatusFor(XN_Profile::current()->screenName, $this->message->sender);
        $this->isBlocked    = BlockedContactList::isSenderBlocked(XN_Profile::current()->screenName, $this->message->sender);

        // xg_excerpt for long subjects?
        $this->title = qh(Profiles_MessageHelper::formatSubjectForDisplay(xg_excerpt($message->subject, Profiles_MessageHelper::MAX_SUBJECT_DISPLAY_LENGTH)));
        if ($this->folder == Profiles_MessageHelper::FOLDER_NAME_INBOX) { Profiles_MessageHelper::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName); }
        else if ($this->folder == Profiles_MessageHelper::FOLDER_NAME_ALERTS) { Profiles_MessageHelper::invalidateAlertsUnreadMessageCountCache(XN_Profile::current()->screenName); }
        $this->numUnreadMsgs = Profiles_MessageHelper::getInboxUnreadMessageCount();
        $this->numUnreadAlertsMsgs = Profiles_MessageHelper::getAlertsUnreadMessageCount();
    }

    /**
     * AJAX handler to return the remainder of the specified message's body
     *
     * Expected GET parameters:
     * @param xn_out string  must be 'json'
     * @param id integer  message id
     */
    public function action_getRestOfMessageBody() {
        if (! XN_Profile::current()->isLoggedIn()) { throw new Exception('Not signed in (action_getRestOfMessageBody)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (action_getRestOfMessageBody)'); }
        try {
            $id = $_GET['id'];
            $message = Profiles_MessageHelper::loadMessage($id);
            // API should throw an exception if it can't load the specified id
            // this check is temporary
            if ($message->id != $id) { throw new Exception('Error loading message id [' . $id . ']'); }

            $this->restOfMessageBody = Profiles_MessageHelper::formatMessageForDisplay($message->body, TRUE, 0, Profiles_MessageHelper::MAX_BODY_QUOTING_DEPTH + 1, null);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * generate a compose link to the specified recipient
     *
     * @param recipient string  The intended recipient of the message
     *
     * @return string  hyperlinked recipient
     */
    private function composeLink($recipient) {
        return '<a href="' . xnhtmlentities($this->_buildUrl('message', 'new', array('recipient' => $recipient))) . '">' . xnhtmlentities($recipient) . '</a>';
    }

    /**
     * load messages and total message count for indicated folder, page number, and page size
     *
     * @param folder string  desired folder; must be one of 'Inbox', 'Sent', 'Archive'
     * @param page integer  page number; 1-based
     * @param pageSize integer  maximum number of messages per page
     *
     * @return array  first element is array of messages, second element is total number of messages
     */
    private function loadMessagesByFolderPage($folder = Profiles_MessageHelper::FOLDER_NAME_INBOX, $page = 1, $pageSize) {
        $page = intval($page);
        $page = $page > 1 ? $page - 1 : 0;
        $pageSize = intval($pageSize);

        // load page of messages
        $begin = $page * $pageSize;
        $end = $begin + $pageSize;
        return call_user_func(array('Profiles_MessageHelper', 'get' . $folder . 'Messages'), $begin, $end);
    }

    /**
     * archives a message (moves the message from the Inbox to the Archive folder)
     * then redirects the user to the folder from which they came (Inbox).
     *
     * GET parameters:
     * @param id string  the message id to move to the Archive folder
     * @param folder string  the originating folder (Inbox)
     */
    public function action_archive() {
        $id = $_GET['id'];
        $folder = $_GET['folder'];
        $this->applyMessageAction('archive', $id, $folder);
    }

    /**
     * moves a message to the inbox then redirects the user to the folder from which
     * they came (Archive).
     *
     * GET parameters:
     * @param id string  the message id to move to the Archive folder
     * @param folder string  the originating folder (Inbox)
     */
    public function action_moveToInbox() {
        $id = $_GET['id'];
        $folder = $_GET['folder'];
        $this->applyMessageAction('moveToInbox', $id, $folder);
    }

    /**
     * deletes a message then redirects the user to the folder from which they came
     *
     * GET parameters:
     * @param id string  the message id to delete
     * @param folder string  the originating folder
     */
    public function action_delete() {
        $id = $_GET['id'];
        $folder = $_GET['folder'];
        $this->applyMessageAction('delete', $id, $folder);
    }

    /**
     * block the sender of a message
     *
     * GET parameters:
     * @param id string  the message id of the message whose sender will be blocked
     * @param folder string  the originating folder
     * @param screenName string  the screenName of the sender
     */
    public function action_blockSender() {
        $id = $_GET['id'];
        $folder = $_GET['folder'];
        $screenName = $_GET['screenName'];
        $this->applyMessageAction('blockSender', $id, $folder, $screenName);
    }

    /**
     * performs some specified action on the specified message.  for all actions
     * other than blocking the sender, we will then redirect the user to the folder
     * from which they came.  for blocking senders, we will redirect the user to
     * the message detail page.  in both cases we will display a status message to
     * let the user know the action succeeded.  if there was an error, we redirect
     * to the message detail page with a generic error message.
     *
     * @param action string  the desired action to perform
     * @param id string  the message id to which the action should be applied
     * @param folder string  the folder from which the user came
     * @param screenName string  if blocking sender, the screenName to block
     */
    private function applyMessageAction($action, $id, $folder = Profiles_MessageHelper::FOLDER_NAME_INBOX, $screenName = null) {
        if ($id && $folder) {
            try {
                if ($action === 'archive') {
                    Profiles_MessageHelper::archiveMessages(array($id));
                    if ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) { Profiles_MessageHelper::markMessagesAsRead(array($id)); }
                } else if ($action === 'delete') {
                    Profiles_MessageHelper::deleteMessages(array($id));
                } else if ($action === 'moveToInbox') {
                    Profiles_MessageHelper::moveMessages(array($id), Profiles_MessageHelper::FOLDER_NAME_INBOX);
                } else if ($action === 'blockSender') {
                    if (is_null($screenName) || (strtolower($screenName) === strtolower(XN_Profile::current()->screenName))) {  /** @non-mb */
                        throw new Exception('Invalid blockSender screenName');
                    }
                    // We're now setting it in blocked contact list instead of setting contactRelationship  [Mohan 2008-09-02]
                    Profiles_FriendHelper::blockMessagesAndDeleteFriendRequests($screenName);
                } else {
                    // unknown action
                    throw new Exception('Unknown action [' . $action . ']');
                }
            } catch (Exception $e) {
                // an error occurred
                $this->redirectTo('show', 'message', array('id' => $id, 'folder' => $folder, 'error' => 1));
                return;
            }
        }
        if ($action !== 'blockSender') {
            $this->redirectTo('list' . $folder, 'message', array('status' => $action));
        } else {
            $this->redirectTo('show', 'message', array('id' => $id, 'folder' => $folder, 'status' => $action));
        }
    }

    /**
     * performs bulk actions on sets of messages and returns via JSON an updated
     * mailbox list body and pagination.  must be called with xn_out=json
     *
     * GET parameters:
     * @param xn_out string  must have value 'json'
     *
     * POST parameters:
     * @param action string  action to perform; one of 'markRead', 'markUnread', 'blockSender', 'archive', 'delete'
     * @param folder string  folder viewing
     * @param page integer  page number viewing
     * @param ids string  JSON-encoded string; for blockSender, an array of screennames; else an array of message ids
     */
    public function action_bulkActionUpdate() {
        // must be xn_out=json
        $_GET['xn_out'] = 'json';

        // decode JSON data
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $ids = $json->decode($_POST['ids']);
        $screenNames = array_unique($json->decode($_POST['screenNames']));
        $folder = $_POST['folder'];
        $action = $_POST['action'];
        $page = intval($_POST['page']);

        // perform action
        try {
            if ($action === "markRead") { Profiles_MessageHelper::markMessagesAsRead($ids); }
            elseif ($action === "markUnread") { Profiles_MessageHelper::markMessagesAsUnread($ids); }
            elseif ($action === "blockSender") {
                $numBlocked = 0;
                foreach ($screenNames as $screenName) {
                    // TODO: can it be done in bulk?
                    if (strtolower($screenName) !== strtolower(XN_Profile::current()->screenName)) {  /** @non-mb */
                        // We're now setting it in blocked contact list instead of setting contactRelationship  [Mohan 2008-09-02]
                        Profiles_FriendHelper::blockMessagesAndDeleteFriendRequests($screenName);
                        $numBlocked++;
                    }
                }
                if ($numBlocked < count($screenNames)) {
                    if ($numBlocked > 0) {
                        $this->warning = xg_html('YOU_CANNOT_BLOCK_SELF_OTHERS_BLOCKED');
                    } else {
                        $this->error = xg_html('YOU_CANNOT_BLOCK_SELF');
                    }
                }
            }
            elseif ($action === "archive") {
                Profiles_MessageHelper::archiveMessages($ids);
                if ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) { Profiles_MessageHelper::markMessagesAsRead($ids); }
            }
            elseif ($action === "inbox") { Profiles_MessageHelper::moveMessages($ids, Profiles_MessageHelper::FOLDER_NAME_INBOX); }
            elseif ($action === "delete") { Profiles_MessageHelper::deleteMessages($ids); }
        } catch (Exception $e) {
            // an error occurred with the action
            $this->error = $e->getMessage();
            return;
        }

        // build selected message id hash
        $selected = array();
        foreach ($ids as $id) {
            $selected[$id] = 1;
        }

        // now update folder view, pagination, and page number as necessary
        list ($messages, $totalMessages) = self::loadMessagesByFolderPage($folder, $page, Profiles_MessageHelper::MESSAGE_LIST_PAGE_SIZE);
        if (($page > 1) && ($page > ceil($totalMessages / Profiles_MessageHelper::MESSAGE_LIST_PAGE_SIZE))) {
            // deleting/moving messages may result in reduction of the number of pages such that the
            // current page is no longer valid; in this case, take the user to the previous page
            $page--;
            $this->refreshUrl = $this->_buildUrl('message', 'list' . $folder, '?page=' . $page);
        } else {
            $otherParties = Profiles_MessageHelper::otherParties($messages);
            $profiles = XG_Cache::profiles($otherParties);
            // get updated list body
            ob_start();
            $this->renderPartial('fragment_listBody', 'message', array('messages' => $messages, 'profiles' => $profiles, 'otherParties' => $otherParties, 'selected' => $selected, 'folder' => $folder, 'page' => $page, 'pageSize' => Profiles_MessageHelper::MESSAGE_LIST_PAGE_SIZE, 'totalMessages' => $totalMessages));
            $this->listBodyHTML = trim(ob_get_clean());
        }
    }

    /**
     * Sends a message to one Ning user.
     *
     * Expected GET parameters:
     *     - screenName - screen name of the user
     *     - xn_out - "json"
     *
     * Expected POST parameters:
     *     - message - the plain-text message body
     */
    public function action_createForScreenName() {
        if (! XN_Profile::current()->isLoggedIn()) { throw new Exception('Not signed in (382900801)'); }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1833299222)'); }
        if (mb_strpos($_GET['screenName'], '@') !== FALSE) { throw new Exception('Not a screen name (1455421891)'); }
        XG_App::includeFileOnce('/lib/XG_UserHelper.php');
        $subject = xg_text('X_HAS_SENT_YOU_MESSAGE_ON_APPNAME', XG_UserHelper::getFullName(XN_Profile::current()), XN_Application::load()->name);
        Profiles_MessageHelper::sendMessage(array($_GET['screenName']), $subject, $_POST['message']);
        $this->success = TRUE;
    }

    public function action_createForScreenName_iphone() {
        $this->action_createForScreenName();
        $this->redirectTo('show', 'profile', array('notification' => xg_html('MESSAGE_SENT'), 'screenName' => $_GET['screenName']));
	}
}
