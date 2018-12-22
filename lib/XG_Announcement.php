<?php

/**
 * This class stores announcements.  Initially these are sidebar notifications
 *   shown only to administrators which will be used to announce new features.
 * See BAZ-2654
 *
 * @ingroup XG
 */
class XG_Announcement {

    /**
     * Get the announcement to display to the current user (or NULL if
     *   no announcement should be displayed)
     *
     * @return array(string $id, string $message) | NULL
     */
    public static function getAnnouncement() {
        //  For now these are only for administrators
        if (!XG_SecurityHelper::userIsAdmin()) {
            return NULL;
        }

        //  Keys in this array and future announcement arrays must never
        //    be reused!
        $adminAnnouncements = array(
            'a1' => xg_html('NEW_FEATURES_GROUPS_MUSIC',
                    W_Cache::getWidget('main')->buildUrl('feature', 'add')),
            'a2' => xg_html('NEW_FEATURES_ACTIVITY_BADGES',
                    W_Cache::getWidget('main')->buildUrl('feature', 'add')),
        );

        $lastAnnouncement = end($adminAnnouncements);
        $lastAnnouncementId = key($adminAnnouncements);

        if (self::userHasAcknowledged($lastAnnouncementId)) {
            return NULL;
        }
        else {
            return array($lastAnnouncementId, $lastAnnouncement);
        }
    }

    /**
     * Has the current user acknowledged the specified announcement?
     *
     * @param $id string
     * @return boolean
     */
    public static function userHasAcknowledged($id) {
        if (!XN_Profile::current()->isLoggedIn()) {
            return FALSE;
        }
        $user = User::load(XN_Profile::current());

        $idsAcknowledged = unserialize($user->my->announcementsAcknowledged);
        if (!$idsAcknowledged || !is_array($idsAcknowledged)) {
            return FALSE;
        }
        return in_array($id, $idsAcknowledged);
    }

    /**
     * Mark the specified announcement as acknowledged by the current user
     *
     * @param $id string
     */
    public static function acknowledge($id) {
        if (!XN_Profile::current()->isLoggedIn()) {
            return FALSE;
        }
        $user = User::load(XN_Profile::current());

        $idsAcknowledged = unserialize($user->my->announcementsAcknowledged);
        if (!$idsAcknowledged || !is_array($idsAcknowledged)) {
            $user->my->announcementsAcknowledged = serialize(array($id));
            $user->save();
            return;
        }
        if (in_array($id, $idsAcknowledged)) {
            return;
        }
        $idsAcknowledged[] = $id;
        $user->my->announcementsAcknowledged = serialize($idsAcknowledged);
        $user->save();
    }

} // /XG_Announcement

