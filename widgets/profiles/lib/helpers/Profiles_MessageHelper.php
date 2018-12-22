<?php

/**
 * Useful functions for working with messages.
 *
 * @see Confluence: In-Network Messages and Friend Requests v1
 */
class Profiles_MessageHelper {

    /**
     * Kill switch for the friend selector on the Compose, Reply, and Forward pages (BAZ-9788)
     * FALSE prevents the call to the expensive numberOfFriendsOnNetwork() function.
     */
    const FRIEND_LIST_DISPLAYED_ON_COMPOSE_PAGE = TRUE;

    /** Maximum number of characters for the subject line */
    const MAX_SUBJECT_LENGTH = 100;

    /** Maximum number of characters for the message body */
    const MAX_MESSAGE_LENGTH = 2000;

    /** Maximum number of recipients per message */
    const MAX_NUMBER_OF_RECIPIENTS = 100;

    /** Folder name constants */
    const FOLDER_NAME_INBOX = 'Inbox';
    const FOLDER_NAME_ALERTS = 'Alerts';
    const FOLDER_NAME_SENT = 'Sent';
    const FOLDER_NAME_ARCHIVE = 'Archive';

    const FOLDER_NAME_INBOX_IN_CORE = 'Inbox';
    const FOLDER_NAME_ALERTS_IN_CORE = 'Alerts';
    const FOLDER_NAME_SENT_IN_CORE = 'Sent';
    const FOLDER_NAME_ARCHIVE_IN_CORE = 'Saved';  // Note! Its called 'Saved' in the core

    /** Compose name constants */
    const COMPOSE_NEW = 'new';
    const COMPOSE_REPLY = 'reply';
    const COMPOSE_FORWARD = 'forward';

    /** display-related constants */

    /** Maximum number of characters to display of the subject line in list view */
    const MAX_SUBJECT_DISPLAY_LENGTH = 60;

    /** Maximum number of characters to display of the message body excerpt */
    const MAX_BODY_EXCERPT_DISPLAY_LENGTH = 80;

    /** Maximum quoting level to show by default */
    const MAX_BODY_QUOTING_DEPTH = 25;

    /** Number of messages shown on each page of the mailbox */
    const MESSAGE_LIST_PAGE_SIZE = 50;

    /** Maximum number of recipients to display */
    const MAX_RECIPIENTS_TO_DISPLAY = 5;
    const MIN_N_MORE = 2;   // min number to show .. and n more.

    /** Number of seconds to cache the inbox unread count. */
    const INBOX_UNREAD_MESSAGE_COUNT_CACHE_MAX_AGE = 300;

    /** Number of seconds to cache the alerts unread count. */
    const ALERTS_UNREAD_MESSAGE_COUNT_CACHE_MAX_AGE = 300;

    /**
     * Queries the given range of Inbox messages, most recent first.
     * The body of each XN_Message will be empty.
     *
     * @param $begin integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Messages and the total count
     */
    public static function getInboxMessages($begin, $end) {
        return self::getMessages(self::FOLDER_NAME_INBOX_IN_CORE, $begin, $end);
    }

    /** static var to store inbox unread message count */
    private static $_inboxUnreadMessageCount = null;

    /**
     * Queries the given range of Alerts messages, most recent first.
     * The body of each XN_Message will be empty.
     *
     * @param $begin integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Messages and the total count
     */
    public static function getAlertsMessages($begin, $end) {
        return self::getMessages(self::FOLDER_NAME_ALERTS_IN_CORE, $begin, $end);
    }

    /**
     * Queries the given range of Sent messages, most recent first.
     * The body of each XN_Message will be empty.
     *
     * @param $begin integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Messages and the total count
     */
    public static function getSentMessages($begin, $end) {
        return self::getMessages(self::FOLDER_NAME_SENT_IN_CORE, $begin, $end);
    }

    /**
     * Queries the given range of Archive messages, most recent first.
     * The body of each XN_Message will be empty.
     *
     * @param $begin integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Messages and the total count
     */
    public static function getArchiveMessages($begin, $end) {
        return self::getMessages(self::FOLDER_NAME_ARCHIVE_IN_CORE, $begin, $end);
    }

    /**
     * Queries the given range of messages.
     * The body of each XN_Message will be empty.
     *
     * @param $folder string  the name of the folder, e.g., Inbox
     * @param $begin integer  the start index (inclusive)
     * @param $end integer  the end index (exclusive)
     * @return array  the XN_Messages and the total count
     */
    private static function getMessages($folder, $begin, $end) {
        $query = XN_Query::create('Message')->filter('folder', '=', $folder)->begin($begin)->end($end)->alwaysReturnTotalCount(TRUE);
        try {
            $messages = $query->execute();
        } catch (Exception $e) {
            return array(array(), 0, (preg_match('/Invalid folder/u', $e->getMessage()) ? xg_text('UNKNOWN_FOLDER_NAME', $folder) : xg_text('ERROR_OPENING_FOLDER', $folder)));
        }

        return array($messages, $query->getTotalCount(), NULL);
    }

    /**
     * Returns the number of unread messages in the user's Inbox
     *
     * @param $forceRequest boolean  force multiple requests to occur
     *
     * @return integer  number of unread messages in the user's Inbox
     */
    public static function getInboxUnreadMessageCount($forceRequest = false) {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        if (is_null(self::$_inboxUnreadMessageCount) || $forceRequest) {
            self::$_inboxUnreadMessageCount = XG_CacheHelper::instance()->get(
                                                self::inboxUnreadMessageCountCacheId(XN_Profile::current()->screenName),
                                                null,
                                                XG_App::constant('Profiles_MessageHelper::INBOX_UNREAD_MESSAGE_COUNT_CACHE_MAX_AGE'),
                                                create_function('', '
                                                    $count = XN_MessageFolder::get(Profiles_MessageHelper::FOLDER_NAME_INBOX_IN_CORE)->unreadCount;
                                                    if (is_array($count)) { throw new Exception(reset($count)); }
                                                    return $count;
                                                '));
        }
        return self::$_inboxUnreadMessageCount;
    }

    /**
     * Clears the cached inbox unread message count for the given user.
     *
     * @param $screenName string  the user's screen name
     */
    public static function invalidateInboxUnreadMessageCountCache($screenName) {
        XN_Cache::remove(self::inboxUnreadMessageCountCacheId($screenName));
    }

    /**
     * Cache ID for the given user's unread inbox count
     *
     * @param $screenName string  the user's screen name
     */
    protected static function inboxUnreadMessageCountCacheId($screenName) {
        return 'inbox-unread-message-count-' . $screenName;
    }

    /**
     * Returns the number of unread messages in the user's Alerts folder
     *
     * @return integer  number of unread messages in the user's Alerts folder
     */
    public static function getAlertsUnreadMessageCount() {
        XG_App::includeFileOnce('/lib/XG_CacheHelper.php');
        return XG_CacheHelper::instance()->get(
            self::alertsUnreadMessageCountCacheId(XN_Profile::current()->screenName),
            'alerts-unread-message-count',
            XG_App::constant('Profiles_MessageHelper::ALERTS_UNREAD_MESSAGE_COUNT_CACHE_MAX_AGE'),
            create_function('', '
                $count = XN_MessageFolder::get(Profiles_MessageHelper::FOLDER_NAME_ALERTS_IN_CORE)->unreadCount;
                if (is_array($count)) { throw new Exception(reset($count)); }
                return $count;
            '));
    }

    /**
     * Clears the cached alerts folder unread message count for the given user.
     *
     * @param $screenName string  the user's screen name
     */
    public static function invalidateAlertsUnreadMessageCountCache($screenName) {
        XN_Cache::remove(self::alertsUnreadMessageCountCacheId($screenName));
    }

    /**
     * Cache ID for the given user's alerts folder unread message count
     *
     * @param $screenName string  the user's screen name
     */
    protected static function alertsUnreadMessageCountCacheId($screenName) {
        return 'alerts-unread-message-count-' . $screenName;
    }


    /**
     * Clears the cached folder unread message count for all mailboxes for the given user.
     *
     * @param $screenName string  the user's screen name
     */
    public static function invalidateUnreadMessageCountCache($screenName) {
        self::invalidateInboxUnreadMessageCountCache($screenName);
        self::invalidateAlertsUnreadMessageCountCache($screenName);
    }


    /**
     * Creates a new message.
     *
     * @param $recipients array  screen names and email addresses
     * @param $subject string  the subject line
     * @param $body string  the plain-text message
     * @param $destinationFolder string  recipient's folder to deliver the mail at - default is INBOX
     */
    public static function sendMessage($recipients, $subject, $body, $destinationFolder = self::FOLDER_NAME_INBOX) {
        $subject = mb_substr($subject, 0, self::MAX_SUBJECT_LENGTH);
        $body = mb_substr($body, 0, self::MAX_MESSAGE_LENGTH);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
        Index_MessageHelper::send($subject, $body, $recipients, $destinationFolder);
    }

    /**
     * Retrieves the XN_Message with the given ID.
     *
     * @param $id  message ID
     * @return XN_Message  the message
     */
    public static function loadMessage($id) {
        $result = XN_Message::load($id);
        if (is_array($result)) { throw new Exception(reset($result)); }
        return $result;
    }

    /**
     * For each message, returns the sender, or if the sender matches the current user, the first recipient.
     *
     * @param array  an array of XN_Message objects
     * @return array  a mapping of message ID to screen name or email address
     */
    public static function otherParties($messages) {
        // Note that the Archive box may contain messages both sent and received (BAZ-9861) [Jon Aquino 2008-09-18]
        $otherParties = array();
        foreach ($messages as $message) {
            $otherParties[$message->id] = $message->sender === XN_Profile::current()->screenName || $message->sender === XN_Profile::current()->email ? $message->recipients[0] : $message->sender;
        }
        return $otherParties;
    }

    /**
     * Cache the profiles of the sender and all recipients of a message
     *
     * @param $message XN_Message  the message for which the sender's and all recipients' profiles will be cached
     *
     * @return array(XN_Profile)  the profiles of the sender and all recipients
     */
    public static function cacheMessageProfiles($message) {
        $screenNames = array();
        $screenNames[$message->sender] = 1;
        foreach ($message->recipients as $recipient) {
            $screenNames[$recipient] = 1;
        }
        return XG_Cache::profiles(array_keys($screenNames));
    }

    /**
     * Cache the profiles of all first recipients in a batch of messages
     *
     * @param $messages  array(XN_Message)  the batch of messages
     *
     * @return array(XN_Profile)  the profiles of all first recipients in a batch of messages
     */
    public static function cacheFirstRecipientProfiles($messages) {
        $screenNames = array();
        foreach ($messages as $message) {
            $screenNames[$message->recipients[0]] = 1;
        }
        return XG_Cache::profiles(array_keys($screenNames));
    }

    /**
     * Moves the XN_Messages to the specified folder
     *
     * @param $ids array(message ID)  the ids of the messages to move to the specified folder
     * @param $folder string  the folder to move the messages to
     */
    public static function moveMessages($ids, $folder) {
		$result = XN_MessageFolder::get($folder)->move($ids);
        if (is_array($result)) { throw new Exception(reset($result)); }
        if (count($ids) > 0) {
            self::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        }
    }

    /**
     * Moves the XN_Messages to the Archive folder.
     *
     * @param $ids array(message ID)  the ids of the messages to move to the Saved folder
     */
    public static function archiveMessages($ids) {
        return self::moveMessages($ids, self::FOLDER_NAME_ARCHIVE_IN_CORE);
    }

    /**
     * Deletes the XN_Message.
     *
     * @param $id  message ID
     */
    public static function deleteMessages($ids) {
        $result = XN_Message::delete($ids);
        if (is_array($result)) { throw new Exception(reset($result)); }
        if (count($ids) > 0) {
            self::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        }
    }

    /**
     * Mark messages as read
     *
     * @param $ids array(message ID)  the ids of the messages to mark as read
     */
    public static function markMessagesAsRead($ids) {
        self::markMessagesAsReadProper($ids, TRUE);
    }

    /**
     * Mark messages as unread
     *
     * @param $ids array(message ID)  the ids of the messages to mark as unread
     */
    public static function markMessagesAsUnread($ids) {
        self::markMessagesAsReadProper($ids, FALSE);
    }

    /**
     * Mark messages as read or unread
     *
     * @param $ids array(message ID)  the ids of the messages to mark as read or unread
     * @param $read boolean  whether the message has been viewed
     */
    private static function markMessagesAsReadProper($ids, $read) {
        // TODO: Load the messages with a single query. Need the core to support an "id in" filter. [Jon Aquino 2008-08-01]
        foreach ($ids as $id) {
            $message = XN_Message::load($id);
            $message->read = $read;
            $message->save();
        }
        if (count($ids) > 0) {
            self::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        }
    }

    /**
     * formats an internal message containing quoting to use <blockquote></blockquote> construct.
     * quoting will use ^\s*> to denote a level of quoting.. ^\s*>\s*> for two levels, etc.  the
     * return will contain tags therefore we must escape html entities within this function.
     *
     * @param $message string  the message to properly format for display
     * @param $escapeHtml string  escapes the HTML characters
     * @param $startLevel integer  the starting level of quoting
     * @param $minLevel integer  return starting from this quoting level (0 to start at beginning)
     * @param $maxLevel integer|null  return stopping at this quoting level (inclusive, null for all levels)
     *
     * $startLevel sets the initial depth of quoting
     * extract only the portion of the message whose quoting depth is >= $minLevel and <= $maxLevel
     *
     * Examples:
     * self::formatMessageForDisplay($message, TRUE, 0, 0, 0) - extract only the non-quoted portion of $message
     * self::formatMessageForDisplay($message, TRUE, 1, 0, 0) - extract only the non-quoted portion of $message
     *                                                          but add one level of quoting to it
     * self::formatMessageForDisplay($message, TRUE, 0, 0, 1) - extract only the non-quoted and first quoted
     *                                                          response portion of $message
     * self::formatMessageForDisplay($message, TRUE, 1, 0, 1) - extract only the non-quoted and first quoted
     *                                                          response portion of $message and add one level of
     *                                                          quoting
     *
     * @return string  the formatted string containing nested <blockquote> tags as needed
     */
    public static function formatMessageForDisplay($message, $escapeHtml = TRUE, $startLevel = 0, $minLevel = 0, $maxLevel = self::MAX_BODY_QUOTING_DEPTH) {
        // the formatted message to return
        $formattedMessage = '';

        // starting level
        for ($i = 0; $i < $startLevel; $i++) {
            $formattedMessage .= '<blockquote>';
        }

        $prevLineLevel = max(0, $minLevel - 1);

        // state by level
        $state = array();

        // process line-by-line
        foreach (explode("\n", $message) as $line) {
            if (preg_match('/^((?:\s*>)+)\s*(.*)$/u', $line, $matches)) {
                $thisLineLevel = mb_strlen(preg_replace('/[^>]+/u', '', $matches[1]));
                $line = $matches[2];
            } else {
                $thisLineLevel = 0;
            }
            if (($thisLineLevel >= $minLevel) && (is_null($maxLevel) || ($thisLineLevel <= $maxLevel))) {
                if (! array_key_exists($thisLineLevel, $state)) {
                    $state[$thisLineLevel] = array();
                    // only look for parseable headers in quoted region of message
                    $state[$thisLineLevel]['inHeaders'] = $thisLineLevel > 0 ? 1 : 0;
                    $state[$thisLineLevel]['dispHeaders'] = 1;
                }
                if ($thisLineLevel > $prevLineLevel) {
                    for ($i = 0; $i < $thisLineLevel - $prevLineLevel; $i++) {
                        $formattedMessage .= '<blockquote>';
                    }
                } else if ($thisLineLevel < $prevLineLevel) {
                    for ($i = 0; $i < $prevLineLevel - $thisLineLevel; $i++) {
                        $formattedMessage .= '</blockquote>';
                        // pop state?
                    }
                }
                $prevLineLevel = $thisLineLevel;
                if ($state[$thisLineLevel]['inHeaders']) {
                    // require at least one \s between header key and header value
                    if (preg_match('/^\s*([^:]+):\s+(.+)$/u', $line, $matches)) {
                        $hdrKey = trim($matches[1]);
                        $hdrVal = trim($matches[2]);
                        if (in_array($hdrKey, array('Date', 'From', 'To'))) {
                            $state[$thisLineLevel][$hdrKey] = $hdrVal;
                        }
                        if ($state[$thisLineLevel]['dispHeaders'] && isset($state[$thisLineLevel]['Date']) && isset($state[$thisLineLevel]['From']) && isset($state[$thisLineLevel]['To'])) {
                            // output message headers
                            $recipientList = preg_split('/\s*,\s*/u', $state[$thisLineLevel]['To']);
                            $numRecipients = count($recipientList);
                            $formattedMessage .= xg_html('FROM_SENDER_TO_RECIPIENT_STYLED', count($recipientList), 'class="xg_lightfont"', $state[$thisLineLevel]['From'], 'class="xg_lightfont"', 'class="xg_lightfont"', implode(xg_html('RECIPIENT_SEPARATOR'), array_slice($recipientList, 0, $numRecipients - 1)), $recipientList[$numRecipients - 1]) . "<br />\n";
                            $date = $state[$thisLineLevel]['Date'];
                            $formattedDate = strtotime($date) !== false ?
                                                xg_elapsed_time($date) :
                                                xnhtmlentities($date);    // couldn't parse it
                            $formattedMessage .= '<span class="xg_lightfont">' . xg_html('SENT_T', $formattedDate) . '</span>' . "<br />\n";
                            $state[$thisLineLevel]['dispHeaders'] = 0;
                        }
                    } else {
                        $state[$thisLineLevel]['inHeaders'] = 0;
                        $formattedMessage .= $escapeHtml ? (xnhtmlentities($line) . "<br />\n") : $line;
                    }
                } else {
                    $formattedMessage .= $escapeHtml ? (xnhtmlentities($line) . "<br />\n") : $line;
                }
            }
        }

        // terminate remaining <blockquote>'s
        for ($i = 0; $i < $prevLineLevel + $startLevel; $i++) {
            $formattedMessage .= '</blockquote>';
        }

        if (! is_null($maxLevel)) {
            // insert attach point on inner-most <blockquote> tag if one exists
            $lastBlockquotePos = mb_strrpos($formattedMessage, '<blockquote>');
            if ($lastBlockquotePos !== false) {
                $formattedMessage = mb_substr($formattedMessage, 0, $lastBlockquotePos) . '<blockquote id="xj_quote_attach">' . mb_substr($formattedMessage, $lastBlockquotePos + mb_strlen('<blockquote>'));
            }
        }

        return $formattedMessage;
    }

    /**
     * returns the max quoting depth of the specified message
     *
     * @param $message string  the message body for which to return the max quoting depth
     *
     * @return integer  the max quoting depth
     */
    public static function getMaxQuotingDepth($message) {
        $maxDepth = 0;
        foreach (explode("\n", $message) as $line) {
            if (preg_match('/^((?:\s*>)+)\s*.*$/u', $line, $matches)) {
                $thisLineLevel = mb_strlen(preg_replace('/[^>]+/u', '', $matches[1]));
                if ($thisLineLevel > $maxDepth) { $maxDepth = $thisLineLevel; }
            }
        }
        return $maxDepth;
    }

    /**
     * loads and formats users (for display) associated with the specified message.
     * if the current user is the sender/recipient, his/her name is xg_text('YOU')
     * instead of his/her profile full name unless useYou is set to false.
     *
     * @param message XN_Message  the message for which to load and format the users
     * @param profiles Array(user => XN_Profile)  associative array mapping screenName/email to XN_Profile
     * @param useYou boolean  if a sender or recipient matches the current user substitute xg_text('YOU') (default: true)
     * @param linkNames boolean  if a sender or recipient is not the current user whether to return hyperlinked names (default: true)
     *
     * @return Array(sender, Array(recipients))  the formatted sender and array of formatted recipients
     */
    public static function loadFormattedMessageUsers($message, $profiles = null, $useYou = true, $linkNames = true) {
        if (! $message) { return array(null, null); }

        if (is_null($profiles)) {
            $profiles = Profiles_MessageHelper::cacheMessageProfiles($message);
        }

        $sender = strtolower(XN_Profile::current()->screenName) === strtolower($message->sender) ?    /** @non-mb */
                                    ($useYou ? xg_html('YOU') : xnhtmlentities(XG_UserHelper::getFullName(XN_Profile::current()))) :
                                    ($linkNames ? xg_userlink($profiles[$message->sender]) : xnhtmlentities(XG_UserHelper::getFullName($profiles[$message->sender])));
        $recipientList = array();
        foreach ($message->recipients as $recipient) {
            $screenName = array_key_exists($recipient, $profiles) ?
                                $profiles[$recipient]->screenName :
                                null;
            array_push($recipientList, strtolower(XN_Profile::current()->screenName) === strtolower($screenName) ?    /** @non-mb */
                                ($useYou ? xg_html('YOU') : xnhtmlentities(XG_UserHelper::getFullName(XN_Profile::current()))) :
                                (! is_null($screenName) ?
                                        ($linkNames ? xg_userlink($profiles[$recipient]) : xnhtmlentities(XG_UserHelper::getFullName($profiles[$recipient]))) :
                                        $recipient));
        }

        return array($sender, $recipientList);
    }

    /**
     * returns text quoting the reference message including headers
     *
     * @param $message XN_Message  the reference message
     *
     * @return string  quoted text of the referenced message including headers
     */
    public static function addQuotedMessage($message) {
        list ($sender, $recipients) = self::loadFormattedMessageUsers($message, null, false, false);
        $date = date("r", strtotime($message->createdDate));
        $quoted = "> Date: $date\n> From: $sender\n> To: " . implode(xg_text('RECIPIENT_SEPARATOR'), $recipients) . "\n>\n";

        // remove trailing newline/CRs
        $body = preg_replace('/[\r\n]+$/u', '', $message->body);
        foreach (explode("\n", $body) as $line) {
            $quoted .= "> " . $line . "\n";
        }
        return $quoted;
    }

    /**
     * returns the formatted body excerpt (excludes the quoted portion if present)
     *
     * @param $excerpt string  the message excerpt
     *
     * @return string  formatted body excerpt
     */
    public static function formatBodyExcerpt($excerpt) {
        $formattedExcerpt = '';
        $lines = explode("\n", $excerpt);
        $numLines = count($lines);
        $stop = false;
        for ($i = 0; ($i < $numLines) && ! $stop; $i++) {
            $line = $lines[$i];
            if (! preg_match('/^\s*>/u', $line)) {
                $formattedExcerpt .= $line . "\n";
            } else {
                $formattedExcerpt .= "...";
                $stop = true;
            }
        }
        return $formattedExcerpt;
    }

    /**
     * reduce a list of message recipients, removing duplicates and optionally, self.  an email
     * address mapping to a screenName that is also in the list is excluded as a duplicate.
     *
     * @param recipients string|Array(string)  one or more message recipients (can be screenNames or email addresses)
     * @param profiles Array(recipient => XN_Profile)  profiles of all recipients
     * @param removeSelf boolean  remove recipients that correspond to the current user unless it's the first recipient (default: true)
     *
     * @return Array(string)
     */
    public static function uniqueRecipients($recipients, $profiles, $removeSelf = true) {
        if (! is_array($recipients)) {
            $recipients = (array) $recipients;
        }
        $filter = $removeSelf ? array(XN_Profile::current()->screenName => 1) : array();

        $first = true;
        $uniqueRecipients = array();
        foreach ($recipients as $recipient) {
            $id = is_object($profiles[$recipient]) ? $profiles[$recipient]->screenName : mb_strtolower($recipient);
            // always accept the first recipient.  in reply mode it is the sender of the reply message (possibly self)
            if ($first || ! array_key_exists($id, $filter)) {
                $uniqueRecipients[] = $recipient;
                $filter[$id] = 1;
                $first = false;
            }
        }
        return $uniqueRecipients;
    }

    /**
     * Explodes the list of recipients.
     *
     * @param $list string  a list of screen names or email addresses, delimited by commas, whitespace, and semicolons
     * @param array  the corresponding array
     */
    protected static function parseRecipientList($list) {
        $results = array();
        foreach (preg_split('@[\s,;]+@u', $list) as $recipient) {
            if ($recipient) { $results[] = $recipient; } // BAZ-9860 [Jon Aquino 2008-09-18]
        }
        return $results;
    }

    /**
     * Displays the subject unless it's blank, then display NO_SUBJECT_PAREN
     *
     * @param subject string  the input subject
     *
     * @return string  the display subject
     */
    public static function formatSubjectForDisplay($subject) {
        $subject = ! is_null($subject) ? trim($subject) : '';
        if (mb_strlen($subject)) {
            return $subject;
        } else {
            return xg_text('NO_SUBJECT_PAREN');
        }
    }

    /**
     * determine the total number of recipients from the friendlist
     *
     * POST variables:
     * - friendSet/screenNamesIncluded/screenNamesExcluded
     *
     * @return integer  the number of recipients from the friendlist
     */
    public static function processFriendListRecipients($vars = array()) {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);

        $screenNamesExcluded = array();
        $screenNamesIncluded = mb_strlen($vars['screenNamesIncluded']) ?
                                    $json->decode($vars['screenNamesIncluded']) :
                                    array();
        $numSelectedFriends = count($screenNamesIncluded);
        if (strlen($vars['friendSet'])) {   /** @non-mb */
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
            $screenNamesExcluded = mb_strlen($vars['screenNamesExcluded']) ?
                                    $json->decode($vars['screenNamesExcluded']) :
                                    array();

            $numFriendsFromList = Index_MessageHelper::numberOfFriendsOnNetwork(XN_Profile::current()->screenName);
            $numSelectedFriends += ($numFriendsFromList - count($screenNamesExcluded));
        }

        return array('screenNamesIncluded' => $screenNamesIncluded,
                     'screenNamesExcluded' => $screenNamesExcluded,
                     'numSelectedFriends' => $numSelectedFriends,
                     'numFriendsFromList' => $numFriendsFromList);
    }

    /**
     * generate a complete recipient list from a variety of fields specifying recipients; if an email address maps
     * to a friend that has already been selected, it will be removed from the list of recipients (BAZ-9823)
     *
     * @param vars Array  $_POST variables
     * @param friendListData Array  pre-processed friendList data
     *
     * Possible POST variables:
     * - fixedRecipients :: one or more recipients which the sender cannot remove (screenName or email)
     * - presetRecipients :: one or more pre-determined recipients which the sender can remove (screenName or email)
     * - recipients :: additional email addresses, which the sender can specify (email)
     * - friendSet :: friend selector friends
     *
     * @return Array(Array(string), Array(string))  an array containing a unique list of recipients and a list of rejected email addresses
     */
    public static function buildMessageRecipientList($vars, $friendListData = array()) {
        $expandedList = array();
        $fixedRecipients = trim($vars['fixedRecipients']);
        if (mb_strlen($fixedRecipients)) {
            $expandedList = array_merge($expandedList, preg_split('/\s*,\s*/u', $fixedRecipients));
        }
        $presetRecipients = trim($vars['presetRecipients']);
        if (mb_strlen($presetRecipients)) {
            $expandedList = array_merge($expandedList, preg_split('/\s*,\s*/u', $presetRecipients));
        }
        // expand friend selector set
        if (array_key_exists('friendSet', $vars)) {
            $expandedList = array_merge($expandedList, $friendListData['screenNamesIncluded']);

            if (strlen($vars['friendSet'])) {   /** @non-mb */
                W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');

                // messaging only supports friends on network - ignore actual friendSet value
                $friendData = Index_MessageHelper::instance()->friendsOnNetwork(XN_Profile::current()->screenName, 0, $friendListData['numFriendsFromList']);
                $screenNames = Index_MessageHelper::instance()->removeAllFriendsOptOuts(array_diff_key(User::screenNames($friendData['profiles']), array_flip($friendListData['screenNamesExcluded'])));
                $expandedList = array_merge($expandedList, $screenNames);
            }
        }

        $additionalRecipients = trim($vars['recipients']);
        $rejectedEmailRecipients = array();
        if (mb_strlen($additionalRecipients)) {
            $emailAddresses = self::parseRecipientList($additionalRecipients);
            $emailProfiles = XG_Cache::profiles($emailAddresses);

            // BAZ-9823 check will use this hash of existing recipient screenNames
            $screenNameRecipients = array_flip($expandedList);

            XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
            foreach ($emailAddresses as $recipient) {
                // must only be email addresses - exclude screenNames
                if (XG_ValidationHelper::isValidEmailAddress($recipient)) {
                    if (is_null($emailProfiles[$recipient]) ||
                        (! is_null($emailProfiles[$recipient]) && ! array_key_exists($emailProfiles[$recipient]->screenName, $screenNameRecipients))) {
                        $expandedList[] = $recipient;
                    }
                } else {
                    $rejectedEmailRecipients[] = $recipient;
                }
            }
        }

        return array(array_unique($expandedList), array_unique($rejectedEmailRecipients));
    }
}
