<?php

/**
 * These methods help with the post archive -- the calculated number of
 * posts per month -- stored in the User object for each user
 */
class Profiles_BlogArchiveHelper {

    /**
     * What attribute on the User object is the archive info stored in?
     */
    private static $attributeName = 'blogPostArchive';

    /**
     * Add a post to the archive list. Posts with a publishStatus other than "publish"
     * are ignored, as the archive contains only published posts.
     *
     * @param $user User The user object in which the archive is stored
     * @param $post BlogPost The post to add
     */
    public static function addPostToArchiveIfEligible($user, $post) {
        if ($post->my->publishStatus != 'publish') { return; }
        $widget = W_Cache::getWidget($post->my->mozzle);
        $archive = self::getPostArchive($user, $widget);
        $visibilities = self::buildVisibilitiesFromPost($post);
        list($y,$m) = self::timestampToMonthAndYear($post->my->publishTime);
        foreach ($visibilities as $visibility) {
            if (! is_array($archive[$visibility])) {
                $archive[$visibility] = array();
            }
            if (! is_array($archive[$visibility][$y])) {
                $archive[$visibility][$y] = array($m => 0);
            } elseif (! isset($archive[$visibility][$y][$m])) {
                $archive[$visibility][$y][$m] = 0;
            }
            $archive[$visibility][$y][$m]++;
        }
        self::setPostArchive($user, $archive, $widget);
        BlogArchive::addPostIfEligible($post);
    }

    /**
     * Remove a post from the archive list. Posts with a publishStatus other than "publish"
     * are ignored, as the archive contains only published posts.
     *
     * @param $user User The user object in which the archive is stored
     * @param $post BlogPost The post to remove
	 * @param $save bool Save BlogArchive instance
     */
    public static function removePostFromArchiveIfEligible($user, $post, $save = true) {
        if ($post->my->publishStatus != 'publish') { return; }
        $widget = W_Cache::getWidget($post->my->mozzle);
        $archive = self::getPostArchive($user, $widget);
        $visibilities = self::buildVisibilitiesFromPost($post);
        list($y,$m) = self::timestampToMonthAndYear($post->my->publishTime);
        $needToUpdate = false;
        foreach ($visibilities as $visibility) {
            if (is_array($archive[$visibility]) && is_array($archive[$visibility][$y]) &&
                isset($archive[$visibility][$y][$m]) && ($archive[$visibility][$y][$m] > 0)) {
                $archive[$visibility][$y][$m]--;
                $needToUpdate = true;
            }
        }
        if ($needToUpdate) {
            self::setPostArchive($user, $archive, $widget);
        }
        BlogArchive::removePostIfEligible($post, $save);
    }

    /**
     * Get the archive list for a particular user
     *
     * @param $user User The user object that holds the archive
     * @param $widget W_Widget optional widget that maintains the archive. Defaults to the current widget
     * @return array The archive list
     */
    public static function getPostArchive($user, $widget = null) {
        $widget = is_null($widget) ? W_Cache::current('W_Widget') : $widget;
        $attribute = XG_App::widgetAttributeName($widget, self::$attributeName);
        $ser = $user->my->{$attribute};
        if (mb_strlen($ser)) {
            return unserialize($ser);
        } else {
            return array();
        }
    }

    /**
     * Set the archive list for a particular user
     *
     * @param $user User The user object that holds the archive
     * @param $archive array The archive list
     * @param $widget W_Widget optional widget that maintains the archive. Defaults to the current widget
     */
    private static function setPostArchive($user, $archive, $widget = null) {
        $widget = is_null($widget) ? W_Cache::current('W_Widget') : $widget;
        $attribute = XG_App::widgetAttributeName($widget, self::$attributeName);
        $user->my->{$attribute} = serialize($archive);
    }

    /**
     * Turn an ISO8601 timestamp such as 2006-03-10T19:45:22Z into a year-month
     * timestamp such as 2006-03. Uses the app's timezone to convert
     *
     * @param $timestamp string The ISO8601 timestamp to use
     */
    public static function timestampToMonthAndYear($timestamp) {
        return explode(',', xg_date('Y,m', $timestamp));
    }

    /**
     * Determine which archive visibilities to use based on the visibility
     * of a post
     *
     * @param $post Post
     * @return array
     */
    public static function buildVisibilitiesFromPost($post) {
        if ($post->my->visibility == 'me') {
            $ar = array('me');
        } elseif ($post->my->visibility == 'friends') {
            $ar = array('me','friends');
        } else {
            $ar = array('me','friends','all');
        }
        return $ar;
    }

}
