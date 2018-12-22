<?php

/**
 * Utility functions for working with the GUI framework for the invitation pages.
 *
 * @see Index_InvitationController
 * @see Index_InvitationHelper
 */
class Index_InvitationFormHelper {

    /**
     * Handles the submission of the Enter Email Addresses form.
     *
     * Expected POST variables:
     *     emailAddresses - email addresses separated by commas, semicolons, and whitespace
     *     message - optional message for the invitation
     *
     * @return array  an array containing:
     *     errorHtml - optional HTML error message
     *     contactList - an array of contacts, each being an array with keys "name" and "emailAddress"
     */
    public static function processEnterEmailAddressesForm() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
        $emailAddresses = self::extractEmailAddresses($_POST['emailAddresses']);
        if (! $emailAddresses) { return array('errorHtml' => xg_html('PLEASE_ENTER_EMAIL_ADDRESSES')); }
        $invalidEmailAddresses = array();
        foreach ($emailAddresses as $emailAddress) {
            if (! XG_ValidationHelper::isValidEmailAddress($emailAddress)) {
                $invalidEmailAddresses[] = $emailAddress;
            }
        }
        if ($invalidEmailAddresses) {
            $errorHtml = '<p>' . xg_html('FOLLOWING_DO_NOT_SEEM_VALID') . '</p><ul>';
            foreach (array_slice($invalidEmailAddresses, 0, 5) as $invalidEmailAddress) {
                $errorHtml .= '<li>' . xnhtmlentities($invalidEmailAddress) . '</li>';
            }
            $errorHtml .= '</ul>';
            return array('errorHtml' => $errorHtml);
        }
        if (mb_strlen($_POST['message']) > Index_InvitationHelper::MAX_MESSAGE_LENGTH) { return array('errorHtml' => xg_html('MESSAGE_TOO_LONG', Index_InvitationHelper::MAX_MESSAGE_LENGTH)); }
        return array('contactList' => self::emailAddressesToContactList($emailAddresses));
    }

    /**
     * Handles the submission of the Invite Friends form.
     *
     * Expected POST variables:
     *     friendSet - base set of friends: null, Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     screenNamesIncluded - JSON array of screen names of friends to include with the base set
     *     screenNamesExcluded - JSON array of screen names of friends to exclude from the base set
     *     inviteFriendsMessage - optional message for the invitation
     *
     * @return array  an array containing:
     *     errorHtml - optional HTML error message
     *     friendSet - base set of friends: null, Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     contactList - an array of contacts to include with the base set, each contact being an array with keys "name" and "emailAddress"
     *     screenNamesExcluded - an array of screen names to exclude from the base set
     */
    public static function processInviteFriendsForm() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $screenNamesIncluded = $json->decode($_POST['screenNamesIncluded']);
        $screenNamesExcluded = $json->decode($_POST['screenNamesExcluded']);
        if (! $screenNamesIncluded && ! $_POST['friendSet']) {
            return array('errorHtml' => xg_html('PLEASE_CHOOSE_FRIENDS'));
        }
        if (mb_strlen($_POST['inviteFriendsMessage']) > Index_InvitationHelper::MAX_MESSAGE_LENGTH) {
            return array('errorHtml' => xg_html('MESSAGE_TOO_LONG', Index_InvitationHelper::MAX_MESSAGE_LENGTH));
        }
        return array('friendSet' => $_POST['friendSet'],
                     'contactList' => self::screenNamesToContactList($screenNamesIncluded),
                     'screenNamesExcluded' => $screenNamesExcluded);
    }

    /**
     * Handles the submission of the Web Address Book form
     *
     * Expected GET params:
     *     id - content object ID for share; null for invite
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *
     * Expected POST variables:
     *     emailLocalPart - the part of the email address before the "@"
     *     emailDomain - the part of the email address after the "@"
     *     password - the password for the email address
     *
     * @return array  an array containing:
     *     errorHtml - HTML error message, if an error occurs
     *     target - URL to go to, if no error occurs
     */
    public static function processWebAddressBookForm() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        // Keep client-side and server-side validations in sync [Jon Aquino 2007-10-27]
        if (! $_POST['emailLocalPart']) { return array('errorHtml' => xg_html('PLEASE_ENTER_EMAIL_ADDRESS')); }
        if (! $_POST['emailDomain']) { return array('errorHtml' => xg_html('PLEASE_SELECT_SECOND_PART')); }
        if (mb_strpos($_POST['emailLocalPart'], '@') !== false) { return array('errorHtml' => xg_html('AT_SYMBOL_NOT_ALLOWED')); }
        if ($_POST['emailDomain'] == '(other)') { return array('errorHtml' => xg_html('SORRY_WE_DO_NOT_SUPPORT')); }
        if (! $_POST['password']) { return array('errorHtml' => xg_html('PLEASE_ENTER_PASSWORD_FOR_EMAIL', xnhtmlentities($_POST['emailLocalPart'] . '@' . $_POST['emailDomain']))); }
        $result = self::getImportService($_POST['emailDomain'])->import($_POST['emailLocalPart'] . '@' . $_POST['emailDomain'], $_POST['password']);
        if (Index_InvitationHelper::isErrorArray($result)) { return array('errorHtml' => Index_InvitationHelper::errorMessage(key($result))); }
        $currentRoute = XG_App::getRequestedRoute();
        $finalTarget = XG_GroupHelper::buildUrl($currentRoute['widgetName'], $currentRoute['controllerName'], 'editContactList', array('id' => $_GET['id'], 'groupId' => XG_GroupHelper::currentGroupId(), 'eventId' => $_GET['eventId']));
        if ($result->status == XN_ContactImportResult::IN_PROGRESS) {
            return array('target' => W_Cache::getWidget('main')->buildUrl('invitation', 'waitForImport', array('jobId' => $result->id, 'target' => $finalTarget)));
        }
        return array('target' => XG_HttpHelper::addParameters($finalTarget, array('contactListId' => ContactList::create(self::importedContactsToContactList(self::allImportedContacts($result)))->id)));
    }

    /**
     * Returns all contacts in the given contact-import result, automatically
     * paging if there are more than 5000.
     *
     * @param XN_ContactImportResult  response from an XN_ContactImportService
     * @return array  an array of XN_ImportedContact objects, or an array of error messages
     */
    public static function allImportedContacts($contactImportResult) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $importedContacts = array();
        $start = 0;
        do {
            $result = $contactImportResult->importedContacts($start, $start + 5000);
            if (Index_InvitationHelper::isErrorArray($result)) { return $result; }
            $importedContacts = array_merge($importedContacts, $result);
            $start += 5000;
        } while(count($importedContacts) < $contactImportResult->size);
        return $importedContacts;
    }

    /**
     * Returns the XN_ContactImportService corresponding to the specified email domain.
     *
     * @param $emailDomain string  the part of the email address after the "@"
     * @return string XN_ContactImportService  the service for loading email addresses
     */
    protected static function getImportService($emailDomain) {
        $importServices = XN_ContactImportService::listServices();
        return $importServices[self::$emailDomainsAndServiceCodes[$emailDomain]];
    }

    /**
     * Handles the submission of the Email Application form
     *
     * Expected GET params:
     *     id - content object ID for share; null for invite
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *
     * Expected POST variables:
     *     file - a file containing CSV or VCF data
     *
     * @return array  an array containing:
     *     errorHtml - HTML error message, if an error occurs
     *     target - URL to go to, if no error occurs
     */
    public static function processEmailApplicationForm() {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        if (! $_POST['file']) { return array('errorHtml' => xnhtmlentities(XG_FileHelper::uploadErrorMessage(4))); }
        if ($_POST['file:status']) { return array('errorHtml' => xnhtmlentities(XG_FileHelper::uploadErrorMessage($_POST['file:status']))); }
        $data = XN_Request::uploadedFileContents($_POST['file']);
        $importServices = XN_ContactImportService::listServices();
        if (self::isVCardData($data)) {
            if (! $importServices['vcf']) { return array('errorHtml' => xg_html('VCARD_IMPORT_UNAVAILABLE')); }
            $result = $importServices['vcf']->import($data);
        } else {
            if (! $importServices['csv']) { return array('errorHtml' => xg_html('CSV_IMPORT_UNAVAILABLE')); }
            $result = $importServices['csv']->import($data);
        }
        if (Index_InvitationHelper::isErrorArray($result)) { return array('errorHtml' => Index_InvitationHelper::errorMessage(key($result))); }
        $currentRoute = XG_App::getRequestedRoute();
        $finalTarget = XG_GroupHelper::buildUrl($currentRoute['widgetName'], $currentRoute['controllerName'], 'editContactList', array('id' => $_GET['id'], 'groupId' => XG_GroupHelper::currentGroupId(), 'eventId' => $_GET['eventId']));
        if ($result->status == XN_ContactImportResult::IN_PROGRESS) {
            return array('target' => W_Cache::getWidget('main')->buildUrl('invitation', 'waitForImport', array('jobId' => $result->id, 'target' => $finalTarget)));
        }
        return array('target' => XG_HttpHelper::addParameters($finalTarget, array('contactListId' => ContactList::create(self::importedContactsToContactList(self::allImportedContacts($result)))->id)));
    }

    /**
     * Returns whether the data looks like VCard data.
     *
     * @param $data string  contents of the uploaded file
     * @return boolean  whether the data is vcf (Virtual Card File) format
     */
    protected static function isVCardData(&$data) {
        // First 100 bytes, as stripos seems to be pass-by-value and $data may be large;
        // otherwise we may run out of memory [Jon Aquino 2007-12-11]
        $firstBytes = substr($data, 0, 100);
        return stripos($firstBytes, mb_convert_encoding('begin:vcard', 'UTF-8')) !== false ||
                stripos($firstBytes, mb_convert_encoding('begin:vcard', 'UTF-16BE')) !== false ||
                stripos($firstBytes, mb_convert_encoding('begin:vcard', 'UTF-16LE')) !== false ||
                stripos($firstBytes, mb_convert_encoding('begin:vcard', 'UTF-32BE')) !== false ||
                stripos($firstBytes, mb_convert_encoding('begin:vcard', 'UTF-32LE')) !== false;
    }

    /**
     * Handles the submission of the Contact List form.
     *
     * Expected GET variables:
     *     contactListId - content ID of a ContactList object
     *     id - the associated content ID for share messages; null for invitations
     *
     * Expected POST variables:
     *     contactListJson - a JSON array of contacts, each being an array with keys "name" and "emailAddress"
     *     message - optional message for the invitation
     *
     * @param $inviteOrShare string  the current context: "invite" or "share"
     * @param $groupId string  the content ID of the associated Group, or null if none.
     * @param $eventId string  the content ID of the associated Event, or null if none.
     */
    public static function processContactListForm($inviteOrShare, $groupId = null, $eventId = null) {
        try {
            XN_Content::delete(ContactList::load($_GET['contactListId']));
        } catch (Exception $e) {
            // Ignore [Jon Aquino 2007-10-25]
        }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        self::send(array(
                'inviteOrShare' => $inviteOrShare,
                'groupId' => $groupId,
                'eventId' => $eventId,
                'contactList' => $json->decode($_POST['contactListJson']),
                'message' => $_POST['message'],
                'contentId' => $_GET['id']));
    }

    /**
     * Sends messages to the specified people.
     *
     * @param $args array
     *     inviteOrShare - the current context: "invite" or "share"
     *     groupId - the content ID of the associated Group, or null if none.
     *     eventId - the content ID of the associated Event, or null if none.
     *     friendSet - base set of friends: null, Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK
     *     contactList - an array of contacts to include with the base set, each contact being an array with keys "name" and "emailAddress"
     *     screenNamesExcluded - list of screenNames to exclude from the base set
     *     message - optional custom message
     *     contentId - the associated content ID for share messages; null for invitations
     *     retry - whether to retry if errors occur (defaults to true)
     *     shareType - when sharing non-content objects specifies object type to share
     *     docUrl - arbitrary network URL to share
     *     docTitle - URL title
     * @return int - The number of sent invitations
     */
    public static function send($args) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        $count = 0;
        if (! $args['contactList'] && ! $args['friendSet']) { return 0; }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $job = XN_Job::create();
        $emailsPerTask = 50;
        $taskContentTemplate = array(
                    'inviteOrShare' => $args['inviteOrShare'],
                    'groupId' => $args['groupId'],
                    'eventId' => $args['eventId'],
                    'docUrl' => $args['docUrl'],
                    'docTitle' => $args['docTitle'],
                    'shareType' => $args['shareType'] ? $args['shareType'] : 'content',
                    'message' => mb_substr($args['message'], 0, 200),
                    'contentId' => $args['contentId'],
                    'retry' => isset($args['retry']) ? $args['retry'] : true);
        if ($args['friendSet']) {
            $count += Index_MessageHelper::numberOfFriendsAcrossNing(XN_Profile::load()->screenName);
            $indexes = XG_LangHelper::indexes(0, $count, $emailsPerTask);
            for ($i = 0; $i < count($indexes) - 1; $i++) {
                $taskContent = $taskContentTemplate;
                $taskContent['friendStart'] = $indexes[$i];
                $taskContent['friendEnd'] = $indexes[$i+1];
                $taskContent['friendSet'] = $args['friendSet'];
                $taskContent['screenNamesExcluded'] = implode(',', $args['screenNamesExcluded']);
                $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('main')->buildUrl('invitation', 'send')), $taskContent));
            }
        }
        if ($args['contactList']) {
            $count += count($args['contactList']);
            foreach (array_chunk($args['contactList'], $emailsPerTask) as $contactBatch) {
                $taskContent = $taskContentTemplate;
                $taskContent['contactList'] = $json->encode($contactBatch);
                $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(W_Cache::getWidget('main')->buildUrl('invitation', 'send')), $taskContent));
            }
        }
        $result = $job->save();
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
        return $count;
    }

    /**
     * Extracts email addresses from the given string.
     *
     * @param $s string  email addresses delimited by commas, semicolons, and whitespace
     * @return array  the email addresses
     */
    protected static function extractEmailAddresses($s) {
        if (! trim($s)) { return array(); }
        return explode(' ', trim(preg_replace('@([;,]|\s)+@u', ' ', $s)));
    }

    /**
     * Converts the email addresses to contact-list format.
     *
     * @param $emailAddresses array  an array of email addresses
     * @return array  an array of contacts, each an array with keys "name" and "emailAddress"
     */
    public static function emailAddressesToContactList($emailAddresses) {
        $contactList = array();
        foreach ($emailAddresses as $emailAddress) {
            $contactList[] = array('name' => null, 'emailAddress' => $emailAddress);
        }
        return $contactList;
    }

    /**
     * Converts the screen names to contact-list format, using
     * pseudo-email-addresses that the messaging core
     * knows to convert to real email addresses.
     *
     * @param $screenNames array  an array of screen names
     * @return array  an array of contacts, each an array with keys "name" and "emailAddress"
     */
    public static function screenNamesToContactList($screenNames) {
        return self::emailAddressesToContactList(self::screenNamesToPseudoEmailAddresses($screenNames));
    }

    /**
     * Converts the screen names to pseudo-email-addresses that the messaging core
     * knows to convert to real email addresses.
     *
     * @param $screenNames array  an array of screen names
     * @return array  the screen names with "@users" appended
     */
    public static function screenNamesToPseudoEmailAddresses($screenNames) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $pseudoEmailAddresses = array();
        foreach ($screenNames as $screenName) {
            $pseudoEmailAddresses[] = XG_Message::pseudoEmailAddress($screenName);
        }
        return $pseudoEmailAddresses;
    }

    /**
     * Converts the XN_ImportedContact to contact-list format.
     * If a contact matches the current user's email address, it is skipped.
     *
     * @param $importedContacts array  an array of XN_ImportedContact objects
     * @return array  an array of contacts, each an array with keys "name" and "emailAddress"
     */
    public static function importedContactsToContactList($importedContacts) {
        $currentUserEmail = XN_Profile::current()->email;
        $contactList = array();
        foreach ($importedContacts as $importedContact) {
            if (strcasecmp($currentUserEmail, $importedContact->email) === 0) { continue; }
            $contactList[] = array('name' => $importedContact->name, 'emailAddress' => $importedContact->email);
        }
        return $contactList;
    }

    /**
     * Returns the email domains corresponding to the available contact-import services.
     *
     * @return array  domain => domain, e.g., gmail.com => gmail.com
     */
    public static function getEmailDomains() {
        return self::getEmailDomainsProper(XN_ContactImportService::listServices());
    }

    /**
     * Returns the email domains corresponding to the given contact-import services.
     *
     * @param $importServices array  an array of XN_ContactImportServices, keyed by code, e.g., gmail
     * @return array  domain => domain, e.g., gmail.com => gmail.com
     */
    protected static function getEmailDomainsProper($importServices) {
        $emailDomains = array();
        foreach (self::$emailDomainsAndServiceCodes as $emailDomain => $serviceCode) {
            if ($importServices[$serviceCode]) { $emailDomains[$emailDomain] = $emailDomain; }
        }
        return $emailDomains;
    }

    /** Mapping of webmail domains to XN_ContactImportService codes */
    protected static $emailDomainsAndServiceCodes = array(
            'aol.com' => 'aol',
            'gmail.com' => 'gmail',
            'hotmail.com' => 'hotmail',
            'hotmail.co.uk' => 'hotmail',
            'msn.com' => 'hotmail',
            'yahoo.com' => 'yahoo',
            'yahoo.co.uk' => 'yahoo',
            'yahoo.ca' => 'yahoo');

    /**
     * Creates a name based on the given email address.
     *
     * @param string$emailAddress  the email address to examine
     * @return string  an auto-generated real name
     */
    public static function generateName($emailAddress) {
        if (mb_strpos($emailAddress, '@') === false) { return $emailAddress; }
        return trim(mb_substr($emailAddress, 0, mb_strpos($emailAddress, '@')));
    }

}
