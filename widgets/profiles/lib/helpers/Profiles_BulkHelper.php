<?php

/**
 * Useful functions for working with bulk operations.
 */
class Profiles_BulkHelper {

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
        $changed = 0;
        $excludedTypes = array('BlogPost', 'User');
        $changed += XG_PrivacyHelper::setContentPrivacy($limit, $toPrivate, 'profiles', $excludedTypes);
        if ($changed >= $limit) {
            return array('changed' => $changed, 'remaining' => ($changed >= $limit));
        }

        $query = XG_PrivacyHelper::basicQuery($limit, $toPrivate, 'profiles');
        $query->filter('type', 'eic', 'BlogPost');
        $query->filter('my->visibility', '=', 'all');
        $blogPosts = $query->execute();
        foreach ($blogPosts as $blogPost) {
            $blogPost->isPrivate = $toPrivate;
            $blogPost->save();
            $changed++;
        }
        if ($changed >= $limit) {
            return array('changed' => $changed, 'remaining' => 1);
        }
        
        $alwaysMakeUsersPrivate = true; // Even if we are making the network public.
        $query = XG_PrivacyHelper::basicQuery($limit, $alwaysMakeUsersPrivate, 'profiles');
        $query->filter('type', 'eic', 'User');
        foreach ($query->execute() as $user) {
            $user->isPrivate = $alwaysMakeUsersPrivate;
            $user->save();
            $changed++;
        }
        return array('changed' => $changed, 'remaining' => $changed >= $limit ? 1 : 0);
    }
}