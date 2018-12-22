<?php

class Index_NotificationHelper {
    const SITE_BROADCAST_TYPE = 'site_broadcast';
    const SITE_BROADCAST_ALIAS_NAME = 'xg_site_broadcast';

    const CONTENT_FOLLOW_ALIAS_LABEL = 'xg_content_follow';
    const CONTENT_FOLLOW_ALIAS_PREFIX = 'xg_content_follow_';

    /**
     * Can we send the specified type of message to this user?
     *
     * @param $type string
     * @param $user XN_Content (type User)
     */
    public function canSendToUser($type, $user) {
        switch ($type) {
            case self::SITE_BROADCAST_TYPE:
                if (mb_strlen($user->my->emailSiteBroadcastPref)) {
                    return ($user->my->emailSiteBroadcastPref != 'N');
                } else {
                    //  no site broadcast preference - fall back to general
                    //    messaging pref
                    return ($user->my->emailNewMessagePref != 'N');
                }
            default:
                throw new Exception("Unknown type $type");
        }
    }

    /**
     * Get the name of the notification alias for the specified content object
     *
     * @param $object XN_Content | W_Content
     */
    public function contentNotificationAliasName($object) {
        $id = strtr($object->id, ':', '_');
        return self::CONTENT_FOLLOW_ALIAS_PREFIX . $id;
    }

    /**
     * Returns the Profile Set label for notifications associated with the given Group.
     *
     * @param $groupId string  the ID of the Group object
     * @return string  the label
     */
    public static function groupLabel($groupId) {
        return 'xg_group_' . strtr($groupId, ':', '_');
    }

    /**
     * Is the specified user subscribed to notifications for (i.e. following)
     *   the specified content object
     *
     * @param $object XN_Content | W_Content
     * @param $profile XN_Profile | string (Optional) default current user
     * @return boolean
     */
    public function userIsFollowing($object, $profile = NULL) {
        if (isset($profile)) {
            if ($profile instanceof XN_Profile) {
                $screenName = $profile->screenName;
            } else {
                $screenName = $profile;
            }
        } else {
            $screenName = XN_Profile::current()->screenName;
        }
        return XN_ProfileSet::setContainsUser(
                self::contentNotificationAliasName($object), $screenName);
    }

    /**
     * Subscribe the specified user to notifications for (i.e. start following)
     *   the specified content object
     *
     * @param $object XN_Content | W_Content
     * @param $profile XN_Profile | string (Optional) default current user
     * @return boolean TRUE on success
     */
    public function startFollowing($object, $profile = NULL) {
        if (isset($profile)) {
            if ($profile instanceof XN_Profile) {
                $screenName = $profile->screenName;
            } else {
                $screenName = $profile;
            }
        } else {
            $screenName = XN_Profile::current()->screenName;
        }
        $labels = array(self::CONTENT_FOLLOW_ALIAS_LABEL);
        if ($object->my->groupId) { $labels[] = self::groupLabel($object->my->groupId); }
        $set = XN_ProfileSet::loadOrCreate(self::contentNotificationAliasName($object), $labels);
        if ($set && $set->addMembers($screenName)) {
            $user = User::load($screenName);
            if (!mb_strlen($user->my->isFollowing) || $user->my->isFollowing != 'Y') {
                $user->my->isFollowing = 'Y';
                $user->save();
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Unsubscribe the specified user from notifications for (i.e. stop following)
     *   the specified content object
     *
     * @param $object XN_Content | W_Content
     * @param $profile XN_Profile | string (Optional) default current user
     * @return boolean TRUE on success
     */
    public function stopFollowing($object, $profile = NULL) {
        if (isset($profile)) {
            if ($profile instanceof XN_Profile) {
                $screenName = $profile->screenName;
            } else {
                $screenName = $profile;
            }
        } else {
            $screenName = XN_Profile::current()->screenName;
        }

        $set = XN_ProfileSet::load(self::contentNotificationAliasName($object));
        if ($set) {
            return $set->removeMember($screenName);
        } else {
            return FALSE;
        }
    }

    /**
     * Remove the specified user from ALL content following notification lists
     *
     * @param $profile XN_Profile | string (Optional) default current user
     * @return boolean TRUE on success
     */
    public function stopAllFollowing($profile = NULL) {
        if (isset($profile)) {
            if ($profile instanceof XN_Profile) {
                $screenName = $profile->screenName;
            } else {
                $screenName = $profile;
            }
        } else {
            $screenName = XN_Profile::current()->screenName;
        }

        return XN_ProfileSet::removeMemberByLabel($screenName,
                self::CONTENT_FOLLOW_ALIAS_LABEL);
    }
}
