<?php
W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_MessageHelper.php');
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');

/**
 * Utility functions for sending opensocial messages to alerts folder.
 * NOTE: A huge section of the code here is copied from Index_MessageHelper.  We need to ensure that the two logics are in sync.
 */
class OpenSocial_MessageHelper {
    /**
     * Sends the message to the specified users
     *
     * @param $appUrl string  the url of the application that's sending the message
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNamesAndEmailAddresses array  usernames and email addresses of the recipients
     * @param $destinationFolder string  recipient's folder to deliver the mail at - default is ALERTS
     */
    public static function send($appUrl, $subject, $message, $screenNamesAndEmailAddresses, $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_ALERTS_IN_CORE) {
        XG_Cache::profiles($screenNamesAndEmailAddresses);
        $gadgetprefs = OpenSocial_GadgetHelper::readGadgetUrl($appUrl);
        $appTitle = $gadgetprefs["title"];
        $viewAppUrl = W_Cache::getWidget('opensocial')->buildUrl('application', 'about', array('appUrl' => $appUrl));
        $sender = XN_Profile::current();

        // Pre-cache profiles
        $profiles = XG_Cache::profiles($screenNamesAndEmailAddresses);
        
        foreach ($screenNamesAndEmailAddresses as $idx => $screenNameOrEmailAddress) {
            $recipient = $profiles[$screenNameOrEmailAddress];
            // If user has blocked sender from sending messages, or user and sender are not friends - remove from recipient list
            if (BlockedContactList::isSenderBlocked($recipient->screenName, array($sender->screenName, $sender->email)) ||
                ($sender->screenName != $recipient->screenName && !XG_UserHelper::isFriend($sender, $recipient->screenName)))
                unset($screenNamesAndEmailAddresses[$idx]);
        }
        
        if (count($screenNamesAndEmailAddresses) === 0) return;  // All recipients are blocked or not-friends!

        // strip html tags - except <br>
        $message = strip_tags($message, "<br>");

        self::postToMessagingEndpoint($appTitle, $viewAppUrl, $subject, $message, $screenNamesAndEmailAddresses, $destinationFolder);
        $notificationFailureCount = 0;
        foreach ($screenNamesAndEmailAddresses as $screenNameOrEmailAddress) {
            $user = User::load($screenNameOrEmailAddress);
            // User has set emailViaApp flag to 'N' - assume that we successfully sent the notification
            if ($user->my->emailViaApplicationsPref == 'N')
                continue;
            
            try {
                XG_App::includeFileOnce('/lib/XG_MessageHelper.php');
                self::sendNotification($appTitle, $viewAppUrl, $subject, $message, $screenNameOrEmailAddress, XG_Message_Notification::EVENT_OSAPP_NOTIFICATION);
            } catch (Exception $e) {
                $notificationFailureCount++;
            }
        }
        if ($notificationFailureCount == count($screenNamesAndEmailAddresses)) { throw new Exception('Could not send notifications (9021095123)'); }
    }
    
    /**
     * Sends the message to the specified users
     *
     * TODO: this documentaiton is out of sync with the code
     * @param $appUrl string  the url of the application that's sending the message
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNamesAndEmailAddresses array  usernames and email addresses of the recipients
     * @param $destinationFolder string  recipient's folder to deliver the mail at - default is INBOX
     */
    private static function postToMessagingEndpoint($appTitle, $viewAppUrl, $subject, $message, $screenNamesAndEmailAddresses, $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_ALERTS_IN_CORE) {
        if (is_null($destinationFolder)) $destinationFolder = Profiles_MessageHelper::FOLDER_NAME_ALERTS_IN_CORE;
        $sender = XN_Profile::current()->screenName;
        if (!$subject)
            $subject = xg_text("MESSAGE_VIA_APPNAME", $appTitle);
        $message = "\n" . xnhtmlentities($message) . "\n" .
                   "<br/><br/><em>".xg_html("MESSAGE_SENT_ON_BEHALF_BY_APP_HTML", qh(xg_username($sender)), 'href="' . qh($viewAppUrl) . '"', qh($appTitle)) . "</em>:\n";
        $recipients = array();
        $displayNames = array();

        // Pre-cache profiles
        $profiles = XG_Cache::profiles($screenNamesAndEmailAddresses);
        
        foreach ($screenNamesAndEmailAddresses as $screenNameOrEmailAddress) {
            $recipient = $profiles[$screenNameOrEmailAddress];
            $recipients[] = $screenNameOrEmailAddress;
            $displayNames[$screenNameOrEmailAddress] = $recipient ? XG_UserHelper::getFullName($recipient) : $screenNameOrEmailAddress;
        }
        if (count($recipients) > 0) {  // if no recipients, then no need to post
            $message = XN_Message::create(array('recipients' => $recipients, 'displayNames' => $displayNames, 'subject' => $subject, 'body' => $message, 'destinationFolder' => $destinationFolder));
            $result = $message->save();
            if (is_array($result)) { throw new Exception(reset($result)); }
        }
    }


    /**
     * Sends an email to the recipient, notifying her that she has a new message.
     *
     * @param $subject string  the subject line
     * @param $message string  the message text
     * @param $screenNameOrEmailAddress string  username or email address of the recipient
     * @param $event string  the event type, e.g., XG_Message_Notification::EVENT_USER_MESSAGE
     */
    public static function sendNotification($appTitle, $viewAppUrl, $subject, $message, $screenNameOrEmailAddress, $event) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        $message = XG_Message_Notification::create($event, array(
                'profile' => XN_Profile::current(),
                'sender' => $sender,
                'osAppTitle' => $appTitle,
                'viewOSAppUrl' => $viewAppUrl,
                'subject' => $subject,
                'body' => W_Cache::getWidget('main')->privateConfig['noMessageInNotification'] ? null : $message));
        $message->send($screenNameOrEmailAddress);
    }
    
}
?>
