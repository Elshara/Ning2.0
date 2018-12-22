<?php

class Profiles_CacheHelper {
    /**
     * Invalidate appropriate caches for the provided BlogPost
     */
    public static function getInvalidationConditionsForPost($post) {
        $cacheKeys = array();
        $widget = W_Cache::getWidget($post->my->mozzle);
        $widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
        $visibilities = Profiles_BlogArchiveHelper::buildVisibilitiesFromPost($post);
        list($yr,$mo) = Profiles_BlogArchiveHelper::timestampToMonthAndYear($post->my->publishTime);
        // Dear App, There is now content of this type
        $cacheKeys[] = 'xg-contenttype-blogpost';
        // Clear the cached post detail page
        $cacheKeys[] = "content-{$post->id}";
        $hashedContributorName = self::hash($post->contributorName);
        // If it's not a draft, it may show up on archive pages
        if ($post->my->publishStatus != 'draft') {
            // Clear any archives based on visibility
            foreach ($visibilities as $visibility) {
                $cacheKeys[] = "{$post->my->mozzle}-blog-{$hashedContributorName}-{$visibility}";
            }
            // Clear any archives based on month + visibility
            foreach ($visibilities as $visibility) {
                $cacheKeys[] = "{$post->my->mozzle}-blog-{$hashedContributorName}-{$yr}-{$mo}-{$visibility}";
            }
            // Clear out the general archive list shown to anonymous users
            if ($post->my->visibility == 'all') {
                $cacheKeys[] = "{$post->my->mozzle}-blog-{$yr}-{$mo}-all";
                $cacheKeys[] = "{$post->my->mozzle}-blog-all";
            }                
            // Clear the feed archive
            $cacheKeys[] = "profiles-blog-{$hashedContributorName}-feed";
            
            // Clear the blog comments cache
            $cacheKeys[] = "{$post->contributorName}-comment-moderated-blogpost";
        }
        return $cacheKeys;
    }
    
    /**
     * Hash screen names so cache keys aren't too long (@see BAZ-798)
     * @param $str string String to hash
     * @return string
     */
    public static function hash($str) {
        $str = mb_strtolower($str);
        $h = 5381;
        for ($i = 0, $j = mb_strlen($str); $i < $j; $i++) {
            /* Make sure it's not negative */
            $h &= 0xFFFFFFF;
            $h += ($h << 5) + ord($str[$i]);
        }
        $h = $h & 0xFFFFFFF;
        return base_convert($h, 10, 36);
    }
}
