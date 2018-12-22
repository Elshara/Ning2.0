<?php

/**
 * Useful functions for working with bulk operations.
 */
class Groups_BulkHelper {


    /**
     * Changes the privacy level of a chunk of objects from the content store.
     * To be used as part of a network's switch from public->private or private->public. 
     *
     * @param   $limit integer          Maximum number of items to switch privacy level before returning.
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @return  array                   ['changed'] The number of content objects changed, 
     *                                  ['remaining'] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function setPrivacy($limit, $toPrivate) {
        // Groups have their own privacy settings and should not be monkeyed with during this process.
        return array('changed' => 0, 'remaining' => 0);
    }

    /**
     * Removes objects created by the specified user in the specified group
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function removeByUserForGroup($group, $limit, $user) {
        Group::setStatus($group, $user, 'banned');
        return Forum_BulkHelper::removeByUser($limit, $user);
    }

    /**
     * Deletes GroupMembership objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $user string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function removeGroupMemberships($limit, $user) {
        $changed = 0;
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'GroupMembership');
        $query->filter('my.mozzle', '=', 'groups');
        $query->filter('my.username', '=', $user);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        $query->begin($begin);
        $query->end($limit - $changed);
        foreach ($query->execute() as $object) {
            XN_Content::delete($object);
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }


}
