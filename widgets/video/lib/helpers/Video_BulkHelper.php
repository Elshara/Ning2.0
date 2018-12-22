<?php

/**
 * Useful functions for working with bulk operations.
 */
class Video_BulkHelper {

    /**
     * Changes the privacy level of a chunk of objects from the content store.
     * To be used as part of a network's switch from public->private or private->public. 
     *
     * @param   $limit integer          Approximate maximum number of items to switch privacy level before returning.
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @return  array                   ['changed'] The number of content objects changed, 
     *                                  ['remaining'] 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public static function setPrivacy($limit, $toPrivate) {
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        $excludedTypes = array('Video', 'VideoAttachment', 'VideoPreviewFrame');
        XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'video', $excludedTypes);
        if ($changed >= $limit) {
            return array('changed' => $changed, 'remaining' => 1);
        }
        $changedVideos = XG_PrivacyHelper::setPrivacyAndGetIds($limit, $toPrivate, 'video', 'Video', 'my->visibility', 'all');
        $changed += count($changedVideos);
        $changed += XG_PrivacyHelper::setRelatedObjectsPrivacy($toPrivate, 'video', array('VideoAttachment', 'VideoPreviewFrame'), 'my->video', $changedVideos);
        return array('changed' => $changed, 'remaining'  => ($changed >= $limit ? 1 : 0));
    }    
}
