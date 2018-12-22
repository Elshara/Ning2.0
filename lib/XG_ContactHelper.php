<?php

/**
 * Useful functions for working with contacts, friends, and relationships.
 */
class  XG_ContactHelper {

   /**
     * Determines the friend status for the given user list, which may contain
     * XN_Content objects, XN_Profiles, or usernames.
     *
     * @param $screenName string The screenName to test
     * @param $users object|array  A User XN_Content, User W_Content, XN_Profile, screen name, or array of the aforementioned
     * @return array  An array of screen name => status string (contact | friend | pending | requested |
     *         groupie | blocked | not-friend); or just the status if a user object (not an array) was passed in
     */
    // The above list of statuses needs updating. Should refer to the XN_Profile constants.
    // Do we still have "contact" and "requested" statuses? [Jon Aquino 2008-06-12]
    public static function getFriendStatusFor($screenName, $users) {
        $friendStatus = self::getFriendInfoFor($screenName, $users, 'relationship');
        return $friendStatus;
    }

   /**
     * Determines the message_blocked status for the given user list, which may contain
     * XN_Content objects, XN_Profiles, or usernames.
     *
     * @param $screenName string The screenName to test
     * @param $users object|array  A User XN_Content, User W_Content, XN_Profile, screen name, or array of the aforementioned
     * @return array  An array of screen name => message_blocked status string (...);
     *                  or just the message_blocked if a user object (not an array) was passed in
     */
    public static function getMessageStatusFor($screenName, $users) {
        $messageStatus = self::getFriendInfoFor($screenName, $users, 'messages');
        return (!$messageStatus) ? 'not-block' : $messageStatus;
    }
    
    /**
     * Determines the friend info for the given user list, which may contain
     * XN_Content objects, XN_Profiles, or usernames.
     *
     * @param $screenName string The screenName to test
     * @param $users object|array  A User XN_Content, User W_Content, XN_Profile, screen name, or array of the aforementioned
     * @param $info string  Information about the user that is being seeked ('relationship', 'message_blocked')
     * @return array  An array of screen name => info; or just the info if a user object (not an array) was passed in
     */
    public static function getFriendInfoFor($screenName, $users, $info) {
        $returnScalar = false;
        if (! is_array($users)) { $returnScalar = true; $users = array($users); }
        if ($info && mb_strlen($screenName) && (count($users) > 0)) {
            $contacts = array();
            foreach ($users as $user) {
                if ($user instanceof XN_Content && $user->type == 'User') {
                    $contacts[] = $user->title;
                } elseif ($user instanceof W_Content && $user->type == 'User') {
                    $contacts[] = $user->title;
                } elseif ($user instanceof XN_Profile) {
                    $contacts[] = $user->screenName;
                } else {
                    $contacts[] = $user;
                }
            }
            $contacts = self::getContacts($screenName, $contacts);
            $result = array();
            if ($returnScalar) {
                if (count($contacts) == 1) {
                    // If core version is less than 6.13.2 - msg_status would not be part of xn_contact.php - so
                    // handle any exceptions that come out of it.
                    try {
                        return $contacts[0]->$info;
                    } catch (XN_IllegalArgumentException $e) {
                        return null;
                    }
                } else {
                    return null;
                }
            }
            foreach ($contacts as $contact) {
                // If core version is less than 6.13.2 - msg_status would not be part of xn_contact.php - so
                // handle any exceptions that come out of it.
                try {
                    $result[$contact->contact] = $contact->$info;
                } catch (XN_IllegalArgumentException $e) {
                    $result[$contact->contact] = null;
                }
                $result[$contact->contact] = $contact->$info;
            }
            return $result;
        } else {
            return $returnScalar ? null : array();
        }
    }

    /**
     * Loads the contacts for the given user.
     *
     * @param $owner string  screen name of the person whose contacts to retrieve
     * @param $contacts string  screen names of the contacts to retrieve
     * @return array  XN_Contact objects
     */
    protected static function getContacts($owner, $contacts) {
        $key = serialize(array($owner, $contacts));
        if (! array_key_exists($key, self::$getContactsResults)) {
            self::$getContactsResults[$key] = self::instance()->getContactsProper($owner, $contacts);
        }
        return self::$getContactsResults[$key];
    }

    /**
     * Clear the contacts cache - since the contact status might have been changed by Profile_UserHelper::createFriendRequest etc
     *
     * @param $screenName string The screenName to test
     * @param $users object|array  A User XN_Content, User W_Content, XN_Profile, screen name, or array of the aforementioned
     */
    public static function clearContactsCache($screenName, $users) {
        if (! is_array($users)) { $returnScalar = true; $users = array($users); }
        if (mb_strlen($screenName) && (count($users) > 0)) {
            $contacts = array();
            foreach ($users as $user) {
                if ($user instanceof XN_Content && $user->type == 'User') {
                    $contacts[] = $user->title;
                } elseif ($user instanceof W_Content && $user->type == 'User') {
                    $contacts[] = $user->title;
                } elseif ($user instanceof XN_Profile) {
                    $contacts[] = $user->screenName;
                } else {
                    $contacts[] = $user;
                }
            }
            $key = serialize(array($screenName, $contacts));
            unset(self::$getContactsResults[$key]);
        }
    }

    /**
     * Loads the contacts for the given user.
     *
     * @param $owner string  screen name of the person whose contacts to retrieve
     * @param $contacts string  screen names of the contacts to retrieve
     * @return array  XN_Contact objects
     */
    protected function getContactsProper($owner, $contacts) {
        return XN_Query::create('Contact')->filter('owner', '=', $owner)->filter('contact', 'in', $contacts)->execute();
    }

    /** getContacts() return values, keyed by its arguments serialized */
    protected static $getContactsResults = array();

    /** Singleton instance of this class. */
    protected static $instance;

    /**
     *  Returns the singleton instance of this class.
     *
     *  @return XG_ContactHelper   the ContactHelper, or a mock object for testing
     */
    private function instance() {
        if (! self::$instance) { self::$instance = new XG_ContactHelper(); }
        return self::$instance;
    }

    // Index_MessageHelper has more friend functions. Move them here if they are needed
    // outside of the index widget. [Jon Aquino 2008-01-01]

}
