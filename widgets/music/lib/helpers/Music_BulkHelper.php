<?php

/**
 * Useful functions for working with bulk operations.
 */
class Music_BulkHelper {

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
        $changed = 0;
        $excludedTypes = array('Track', 'AudioAttachment', 'ImageAttachment');
        $changed = XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'music', $excludedTypes);
        if ($changed >= $limit) {
            return array('changed' => $changed, 'remaining' => 1);
        }
        $changedTracks = XG_PrivacyHelper::setPrivacyAndGetIds($limit, $toPrivate, 'music', 'Track', 'my->enableProfileUsage', 'on');
        $changed += count($changedTracks);
        $changed += XG_PrivacyHelper::setRelatedObjectsPrivacy($toPrivate, 'music', array('AudioAttachment'), 'my->audio', $changedTracks);
        $changed += XG_PrivacyHelper::setRelatedObjectsPrivacy($toPrivate, 'music', array('ImageAttachment'), 'my->contentId', $changedTracks);
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }
}
