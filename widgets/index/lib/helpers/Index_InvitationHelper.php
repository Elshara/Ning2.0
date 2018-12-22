<?php

// Set up event handlers [Jon Aquino 2008-04-17]
try {
    W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_InvitationHelper.php');
} catch (Exception $e) {
    // BAZ-7301 [Jon Aquino 2008-04-17]
}
try {
    W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_InvitationHelper.php');
} catch (Exception $e) {
    // BAZ-7301 [Jon Aquino 2008-04-17]
}

/**
 * Utility functions for working with invitations
 *
 * @see Index_InvitationMode
 */
class Index_InvitationHelper {

    /**
     * Query-string parameter holding the invitation key.
     */
    const KEY  = 'xgi';

    /** Maximum number of characters that the message may contain. */
    const MAX_MESSAGE_LENGTH = 10000;

    /**
     * Query-string parameter indicating that, for the current invitation, we have asked the
     * user to sign in (if necessary).
     */
    const SIGN_IN_CHECK_DONE = 'xgkc';

    /**
     * Query-string parameter indicating that the URL is for a Share This message
     */
    const SHARING  = 'xgs';
    // TODO: Remove this constant, as it is no longer used [Jon Aquino 2008-03-31]

    /**
     * Label for network invitations and network sharing.
     */
    const NETWORK_INVITATION = 'network-invitation';

    /**
     * Label for the invitation associated with the bulk-invitation link.
     */
    const NETWORK_BULK_INVITATION = 'network-bulk-invitation';


    /**
     * Processes an invitation, if present. May redirect.
     *
     * @return boolean  whether an invitation was processed
     */
    public static function processInvitation() {
        $result = self::processInvitationProper();
        if ($result == 'error') {
            header('Location: ' . W_Cache::getWidget('main')->buildUrl('authorization', 'signIn', array('invitationExpired' => 1)));
            exit;
        }
        if ($result == 'processed') {
            header('Location:' . XG_HttpHelper::removeParameters(XG_HttpHelper::currentUrl(), array(self::SIGN_IN_CHECK_DONE, self::KEY)));
            exit;
        }
        return ! is_null($result);
    }

    /**
     * Processes an invitation, if present. May redirect.
     *
     * @return string  one of the following values:
     *     null - if no invitation was provided
     *     error - if there was an error accepting the invitation
     *     processed - if the invitation was successfully processed
     */
    private static function processInvitationProper() {
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        XG_App::includeFileOnce('/lib/XG_LogHelper.php');
        XG_App::includeFileOnce('/lib/XG_Message.php');
        if (! $_GET[self::KEY]) { return null; }
        $profile = XN_Profile::current();
        $invitation = XN_Invitation::load($_GET[self::KEY]);
        $invitationExists = $invitation instanceof XN_Invitation;
        if ($invitationExists) {
            $invitationMetadata = array('label' => $invitation->label, 'recipient' => $invitation->recipient);
        } elseif (!XG_App::appIsPrivate() || XG_App::allowJoinByAll()) {
            $invitationMetadata = array('label' => self::NETWORK_INVITATION, 'recipient' => null);
        } elseif (User::isMember($profile) || User::isPending($profile)) {
            return null; // BAZ-6608 [Jon Aquino 2008-03-31]
        } else {
            XG_LogHelper::logBasicFlows('Invitation error.', array('invite-key' => $_GET[self::KEY]));
            return 'error';
        }
        $inviteeAlreadySignedIn = $profile->isLoggedIn() && $profile->email == $invitationMetadata['recipient'];
        if ($inviteeAlreadySignedIn && (User::isMember($profile) || User::isPending($profile))) {
            if ($invitationExists) { self::consume($invitation); }
            return 'processed';
        }
        if ($_GET[self::SIGN_IN_CHECK_DONE]) {
            $willShowCreateProfilePage = ! User::isMember($profile) && ! User::isPending($profile);
            if ($invitationExists && ! $willShowCreateProfilePage) {
                $result = self::consume($invitation);
                if (self::isErrorArray($result)) {
                    XG_LogHelper::logBasicFlows('Invitation error.', array('status' => $result['xn:status']));
                    return 'error';
                }
            }
            return 'processed';
        }
        $target = XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), self::SIGN_IN_CHECK_DONE, 1);
        $inviteeIsNingUser = $inviteeProfile = XG_Cache::profiles($invitationMetadata['recipient']);
        $inviteeIsMember = $inviteeProfile && User::isMember($inviteeProfile);
        if ($inviteeIsNingUser && ! $inviteeIsMember && XG_Message::isPseudoEmailAddress($invitationMetadata['recipient'])) {
            // we can't pre-populate the email address in this case because we cannot load the full XN_Profile object for privacy reasons (BAZ-10380) [ywh 2008-09-25]
            header('Location: ' . XG_AuthorizationHelper::signUpUrl($target));
            exit;
        } elseif ($inviteeIsNingUser && ! $inviteeIsMember) {
            header('Location: ' . XG_AuthorizationHelper::signUpNingUserUrl($target, null, $invitationMetadata['recipient']));
            exit;
        } elseif ($inviteeIsNingUser && $inviteeIsMember) {
            header('Location: ' . XG_AuthorizationHelper::signInUrl($target, null, XG_Cache::profiles($invitationMetadata['recipient'])->email));
            exit;
        } else {
            header('Location: ' . XG_AuthorizationHelper::signUpUrl($target, null, $invitationMetadata['recipient']));
            exit;
        }
    }

    /**
     * Consumes the invitation; that is: increments the use count, and friends the inviter and invitee.
     *
     * @param $invitation  the invitation to consume
     * @return boolean|array  true on success, or an array of error messages
     */
    public static function consume(XN_Invitation $invitation) {
        $result = $invitation->consume(XN_Profile::current());
        if (! self::isErrorArray($result)) {
            XN_Event::fire('invitation/consume/after', array($invitation));
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
            // consume() friends the inviter and invitee [Jon Aquino 2008-09-12]
            Profiles_FriendHelper::instance()->invalidateCachesForCurrentUserAnd(array($invitation->inviter), XN_Profile::FRIEND, XN_Profile::GROUPIE);
        }
        return $result;
    }

    /**
     * Returns the invitation for the current user.
     *
     * @param $target string  URL of the page to land on; possibly contains an invitation key
     * @return XN_Invitation  the invitation, or  null if the person does not have one
     */
    public static function getUnusedInvitation($target) {
        if ($target) {
            $parts = parse_url($target);
            $parameters = array();
            if (isset($parts['query'])) { parse_str($parts['query'], $parameters); }
            if ($key = $parameters[self::KEY]) {
                $invitation = XN_Invitation::load($key);
                if ($invitation instanceof XN_Invitation) { return $invitation; }
            }
        }
        return null;
    }

    /**
     * Extracts the recipient field from the invitations.
     *
     * @param $invitations array  the XN_Invitations
     * @return array  the email addresses
     */
    public static function recipients($invitations) {
        $recipients = array();
        foreach ($invitations as $invitation) {
            $recipients[] = $invitation->recipient;
        }
        return $recipients;
    }

    /**
     * Returns metadata for unused invitations.
     *
     * @param $filters   array Array of filters to be applied when retrieving unused invitations.
     *                         For format see User::find.
     * @param $begin integer  0-based start index (inclusive)
     * @param $end integer  0-based end index (exclusive)
     * @param $totalCount integer  output for the total number of unused invitations
     * @param $profiles array  output for the invitee and inviter profiles, keyed by screen name
     * @return array  for each invitation, an array keyed by:
     *     displayName - recipient's display name
     *     screenName - recipient's Ning username, if any
     *     emailAddress - recipient's email address
     *     id - identifier for the invitation
     *     date - date on which the invitation was sent
     *     inviter - screen name of the person who have invited the recipient
     */
    public static function getUnusedInvitations($filters, $begin, $end, &$totalCount, &$profiles) {
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $query = XN_Query::create('Invitation');
        $query = XG_QueryHelper::applyFilters($query, $filters);
        $query->filter('label', '<>', self::NETWORK_BULK_INVITATION)
                ->begin($begin)
                ->end($end)
                ->order('createdDate', 'desc')
                ->alwaysReturnTotalCount(true);
        $invitations = array();
        $screenNamesAndEmailAddresses = array();
        foreach ($query->execute() as $invitation) {
            $invitations[] = $invitation;
            $screenNamesAndEmailAddresses[] = $invitation->inviter;
            $screenNamesAndEmailAddresses[] = $invitation->recipient;
        }
        $totalCount = $query->getTotalCount();
        $profiles = XG_Cache::profiles($screenNamesAndEmailAddresses);
        return self::metadataForInvitations($invitations, 'id');
    }

    /**
     * Cancels the given invitations.
     *
     * @param $ids array  identifiers returned by getUnusedInvitations();
     */
    public static function deleteUnusedInvitations($ids) {
        XN_Invitation::delete($ids);
    }

    /**
     * Re-sends the given invitations.
     *
     * @param $ids array  identifiers returned by getUnusedInvitations();
     */
    public static function resendUnusedInvitations($ids) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        if (self::isErrorArray($invitations)) { throw new Exception(self::errorMessage(key($invitations))); }
        foreach ($ids as $id) {
            $invitation = XN_Invitation::load($id);
            XN_Event::fire('invitation/resend', array($invitation));
        }
    }

    /**
     * Called when an invitation is being re-sent.
     *
     * @param $invitation  the invitation to consume
     */
    public static function onResend($invitation) {
        if ($invitation->label == self::NETWORK_INVITATION) {
            $message = new XG_Message_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_ON_X', XN_Application::load()->name),
                    'url' => XG_HttpHelper::addParameters(xg_absolute_url('/'), array(self::KEY => $invitation->id))));
            $message->send($invitation->recipient, $invitation->inviter);
        }
    }

    /**
     * Returns invitation metadata, used on the Manage Invitations and Manage Group Invitations pages.
     *
     * @param $invitations array  an array of XN_Invitations
     * @param $idAttribute string  name of the invitation attribute to use for the ID
     * @return array  for each invitation, an array keyed by:
     *     displayName - recipient's display name
     *     screenName - recipient's Ning username, if any
     *     emailAddress - recipient's email address
     *     id - identifier for the invitation
     *     date - date on which the invitation was sent
     *     inviter - Ning username of the person who sent the invitation
     */
    public static function metadataForInvitations($invitations, $idAttribute = 'id') {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
        $emailAddresses = array();
        foreach ($invitations as $invitation) {
            $emailAddresses[] = $invitation->recipient;
        }
        $profiles = XG_Cache::profiles($emailAddresses);
        $metadata = array();
        foreach ($invitations as $invitation) {
            $profile = $profiles[$invitation->recipient];
            $displayName = $invitation->name;
            if (! $displayName && $profile) { $displayName = XG_UserHelper::getFullName($profile); }
            if (! $displayName) { $displayName = Index_InvitationFormHelper::generateName($invitation->recipient); }
            $metadata[] = array(
                'displayName' => $displayName,
                'screenName' => $profile ? $profile->screenName : null,
                'emailAddress' => $invitation->recipient,
                'id' => $invitation->$idAttribute,
                'date' => $invitation->createdDate,
                'inviter' => $invitation->inviter);
        }
        return $metadata;
    }

    /**
     * Creates a new value for the network's "bulk-invitation link", which
     * is a URL that can be reused by an unlimited number of people to gain access
     * to the network. Calling this function expires the current bulk-invitation URL.
     *
     * @return string  a new bulk-invitation URL
     */
    public static function generateBulkInvitationUrl() {
        $results = XN_Query::create('Invitation')->filter('label', '=', self::NETWORK_BULK_INVITATION)->execute();
        if ($results) { XN_Invitation::delete($results); }
        $invitation = XN_Invitation::create(array('label' => self::NETWORK_BULK_INVITATION));
        $result = $invitation->save();
        if (self::isErrorArray($result)) { throw new Exception(self::errorMessage(key($result))); }
        return XG_HttpHelper::addParameters(xg_absolute_url('/'), array(self::KEY => $invitation->id));
    }

    /**
     * Returns the current bulk-invitation link, creating one if necessary.
     *
     * @return string  the current bulk-invitation URL.
     */
    public static function getBulkInvitationUrl() {
        return self::generateBulkInvitationUrlIfNecessary();
    }

    /**
     * Returns the current bulk-invitation link, creating one if necessary.
     *
     * @return string  the current bulk-invitation URL.
     */
    public static function generateBulkInvitationUrlIfNecessary() {
        if ($_GET['test_baz5489']) { throw new Exception('test_baz5489'); }
        $results = XN_Query::create('Invitation')->filter('label', '=', self::NETWORK_BULK_INVITATION)->execute();
        if (! $results) { return self::generateBulkInvitationUrl(); }
        return XG_HttpHelper::addParameters(xg_absolute_url('/'), array(self::KEY => $results[0]->id));
    }

    /**
     * Returns whether the given function result is an array of error messages.
     *
     * @param $result mixed  return value from a function
     * @param $result boolean  whether the looks like a standard Ning PHP API error array
     */
    public static function isErrorArray($result) {
        //TODO this routine is now used in lots of places ... find an appropriate place in the lib/XG_* libraries for it.
        return is_array($result) && isset($result['xn:status']);
    }

    /**
     * Returns the error message corresponding to the given error code.
     *
     * @param $errorCode string  error code returned by XN_ContactImportService, XN_ContactImportResult, XN_Invitation, or XN_Job
     * @return string  the HTML error message
     */
    public static function errorMessage($errorCode) {
        switch ($errorCode) {
            case 'contact-import:2': return xg_html('IMPORT_FAILED');
            case 'contact-import:auth:4': return xg_html('LOGIN_FAILED');
            case 'contact-import:data:1': return xg_html('UNABLE_TO_FIND_EMAIL');
            case 'contact-import:data:3': return xg_html('CANNOT_READ_VCARDS');
            default: return xg_html('ERROR_OCCURRED');
        }
    }

    /**
     * Creates a saved XN_Invitation, or returns an existing, equivalent one.
     *
     * @param $emailAddress string  email address of the recipient
     * @param $name string  real name of the recipient (optional)
     * @param $label string  a brief, queryable string to attach to the invitation
     * @return XN_Invitation  the new invitation
     */
    public static function createInvitation($emailAddress, $name, $label) {
        // Prevent duplicate invitations (BAZ-3937)  [Jon Aquino 2007-11-14]
        $invitations = XN_Query::create('Invitation')
                ->filter('author', '=', XN_Profile::current()->screenName)
                ->filter('recipient', '=', $emailAddress)
                ->filter('label', '=', $label)
                ->execute();
        if ($invitations) { return $invitations[0]; }
        $name = xg_excerpt($name, 100); // BAZ-7257 [Andrey 2008-04-24]
        $invitation = XN_Invitation::create(array('recipient' => $emailAddress, 'name' => $name, 'maxUse' => 1, 'label' => $label));
        $result = $invitation->save();
        if (self::isErrorArray($result)) { throw new Exception(self::errorMessage(key($result))); }
        return $invitation;
    }

    /** Singleton instance of this class. */
    protected static $instance;

    /**
     *  Returns the singleton instance of this class.
     *
     *  @return Events_InvitationHelper   the InvitationHelper, or a mock object for testing
     */
    private function _instance() {
        if (! self::$instance) { self::$instance = new Index_InvitationHelper(); }
        return self::$instance;
    }

    /**
     * Deletes invitations with the same recipient and label.
     * You can choose whether to delete the invitations or consume them.
     * Consuming will friend the recipient and the inviter.
     * Note that this function does not fire invitation/consume events.
     *
     * @param $recipient string  email address of the invitation recipient
     * @param $label string  the label of the invitation
     * @param $consume boolean  whether to consume rather than delete.
     */
    public static function deleteInvitations($recipient, $label, $consume = false) {
        if (! $recipient) { throw new Exception('Assertion failed (1931231699)'); }
        $recipients = array($recipient);
        $profile = XG_Cache::profiles($recipient);
        if ($profile) { $recipients[] = XG_Message::pseudoEmailAddress($profile->screenName); }
        $invitations = self::_instance()->_createInvitationQuery()
                ->filter('label', '=', $label)
                ->filter('recipient', 'in', $recipients)
                ->execute();
        if ($consume) {
            foreach ($invitations as $invitation) {
                $invitation->consume(XN_Profile::current());
            }
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendHelper.php');
            // consume() friends the inviter and invitee [Jon Aquino 2008-09-12]
            Profiles_FriendHelper::instance()->invalidateCachesForCurrentUserAnd(self::inviters($invitations), XN_Profile::FRIEND, XN_Profile::GROUPIE);
        } else {
            self::_instance()->_deleteInvitationsProper($invitations);
        }
    }

    /**
     * Returns the set of inviters for the given invitations.
     *
     * @param $invitations array  the XN_Invitation objects
     * @return array  the inviter screen names, with duplicates removed
     */
    protected static function inviters($invitations) {
        $inviters = array();
        foreach ($invitations as $invitation) {
            $inviters[] = $invitation->inviter;
        }
        return array_unique($inviters);
    }

    /**
     *  Creates an XN_Query for XN_Invitations.
     *
     *  @return     XN_Query
     *  @access     private
     */
    public static function _createInvitationQuery() {
        return XN_Query::create('Invitation');
    }

    /**
     * Deletes the XN_Invitation(s).
     *
     * Accepts a variable number of arguments. Each argument should be an XN_Invitation
     * object, the ID of an XN_Invitation object, or an array of objects or IDs.
     *
     * @return boolean|array  true on success, or an array of error messages
     */
    public static function _deleteInvitationsProper($invitations) {
        return XN_Invitation::delete($invitations);
    }

}

XN_Event::listen('invitation/resend', array('Index_InvitationHelper', 'onResend'));
