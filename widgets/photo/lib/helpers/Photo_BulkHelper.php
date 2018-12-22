<?php

/**
 * Useful functions for working with bulk operations.
 */
class Photo_BulkHelper {

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
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'photo', array('Photo'));
        $query = XG_PrivacyHelper::basicQuery($limit, $toPrivate, 'photo');
        $query->filter('type', 'eic', 'Photo');
        $query->filter('my->visibility', '=', 'all');
        $changed = 0;
        foreach ($query->execute() as $photo) {
            $photo->isPrivate = $toPrivate;
            $photo->save();
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => ($changed >= $limit ? 1 : 0));
    }
}