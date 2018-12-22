<?php

/**
 * Logic specific to the "context" of a photo: album, user, location, featured.
 */
abstract class Photo_Context {

    /**
     * Retrieves the context with the given name.
     *
     * @param string $name  Name of the context: album, user, location, featured
     */
    public static function get($name) {
        if (! self::$nameToContextMap[$name]) {
            $className = 'Photo_' . ucfirst($name) . 'Context';
            self::$nameToContextMap[$name] = new $className;
        }
        return self::$nameToContextMap[$name];
    }

    /**
     * Adds filters to the query based on the given context.
     *
     * @param $query XN_Query  the query to filter
     * @param $comparison string  < or >, for previous or next
     * @param $photo XN_Content|W_Content  the photo that the comparison is relative to
     * @param $begin integer  start index for the query; typically 0
     * @param $end integer  the number of adjacent photos to retrieve
     * @param $photoIds string  content IDs for Photos in an album
     * @return boolean  whether to proceed with the query, or to return no results
     */
    public abstract function filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds);

    /**
     * Returns the URL of the list page associated with this context.
     * $_GET may be used to supply additional information about the context.
     *
     * @param $photo XN_Content|W_Content  the photo
     * @return string  the URL
     */
    public abstract function getListPageUrl($photo);

    /** Mapping of context name to singleton Photo_Context instance */
    public static $nameToContextMap = array();

}

/**
 * Context for photos in a given album.
 */
class Photo_AlbumContext extends Photo_Context {

    public function filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds) {
        if ($comparison == '>') { $photoIds = array_reverse($photoIds); }
        $key = array_search($photo->id, $photoIds);
        if ($key === false) { return false; }
        $length = $end - $begin;
        $offset = $key + $begin + 1;
        $photoIds = array_slice($photoIds, $offset, $length);
        if (count($photoIds)==0) { return false; }
        if ($comparison == '>') { $photoIds = array_reverse($photoIds); }
        $query->filter('id', 'in', $photoIds);
        return true;
    }

    public function getListPageUrl($photo) {
        return W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $_GET['albumId']));
    }

}

/**
 * Context for photos owned by a given user.
 */
class Photo_UserContext extends Photo_Context {

    public function filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds) {
        $query->filter('contributorName', 'eic', $photo->contributorName);
        $query->filter('createdDate', $comparison, $photo->createdDate, XN_Attribute::DATE);
        $query->order('createdDate', $comparison == '>' ? 'asc' : 'desc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        return true;
    }

    public function getListPageUrl($photo) {
        return W_Cache::getWidget('photo')->buildUrl('photo', 'listForContributor', array('screenName' => $photo->contributorName));
    }

}

/**
 * Context for photos in a given location.
 */
class Photo_LocationContext extends Photo_Context {

    public function filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds) {
        $query->filter('my->location', 'eic', $photo->my->location);
        $query->filter('createdDate', $comparison, $photo->createdDate, XN_Attribute::DATE);
        $query->order('createdDate', $comparison == '>' ? 'asc' : 'desc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        return true;
    }

    public function getListPageUrl($photo) {
        return W_Cache::getWidget('photo')->buildUrl('photo', 'listForLocation', array('location' => $photo->my->location));
    }

}

/**
 * Context for featured photos.
 */
class Photo_FeaturedContext extends Photo_Context {

    public function filterQueryByContext($query, $comparison, $photo, $begin, $end, $photoIds) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_PromotionHelper::addPromotedFilterToQuery($query);
        $query->filter('my->' . XG_PromotionHelper::attributeName(), $comparison, $photo->my->raw(XG_PromotionHelper::attributeName()), XN_Attribute::DATE);
        $query->order('my->' . XG_PromotionHelper::attributeName(), $comparison == '>' ? 'asc' : 'desc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        return true;
    }

    public function getListPageUrl($photo) {
        return W_Cache::getWidget('photo')->buildUrl('photo', 'listFeatured');
    }

}

