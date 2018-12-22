<?php
/**
 * Common code for saving and querying Album objects
 */
class Photo_AlbumHelper {
    /** Constant for sorting by most recent. */
    const SORT_ORDER_MOSTRECENT   = 'mostRecent';
    /** Constant for sorting by alphabetical. */
    const SORT_ORDER_ALPHABETICAL = 'alphabetical';
    /** Constant for sorting by most views. */
    const SORT_ORDER_MOSTVIEWS  = 'mostViews';
    /** Constant for random ordering. */
    const SORT_ORDER_RANDOM = 'random';

    /**
     * Returns the descriptors of the known sorting orders.
     *
     * @return The descriptors keyed by the internal name, containing:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getKnownSortingOrders() {
        return array(self::SORT_ORDER_MOSTRECENT => self::getMostRecentSortingOrder(),
                     self::SORT_ORDER_MOSTVIEWS => self::getMostViewsSortingOrder(),
                     self::SORT_ORDER_RANDOM => self::getRandomSortingOrder(),
                     self::SORT_ORDER_ALPHABETICAL => self::getAlphabeticalSortingOrder());
    }

    /**
     * Returns the descriptor of the alphabetical sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getAlphabeticalSortingOrder() {
        return array('name'      => xg_text('ALPHABETICAL'),
                     'attribute'   => 'title',
                     'alias'     => self::SORT_ORDER_ALPHABETICAL,
                     'direction' => 'asc',
                     'type'      => XN_Attribute::STRING,
                     'mainPageTitleKey' => 'ALPHABETICAL_ALBUMS');
    }

    /**
     * Returns the descriptor of the most views sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getMostViewsSortingOrder() {
        return array('name'      => xg_text('MOST_VIEWED'),
                     'attribute'   => 'my->viewCount',
                     'alias'     => self::SORT_ORDER_MOSTVIEWS,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::NUMBER,
					 'mainPageTitleKey' => 'MOST_VIEWED_ALBUMS');
    }

    /**
     * Returns the descriptor of the most-recent sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getMostRecentSortingOrder() {
        return array('name'      => xg_text('LATEST'),
                     'attribute'   => 'createdDate',
                     'alias'     => self::SORT_ORDER_MOSTRECENT,
                     'direction' => 'desc',
                     'type'      => XN_Attribute::DATE,
					 'mainPageTitleKey' => 'LATEST_ALBUMS');
    }

    /**
     * Returns the descriptor of the random sorting order.
     *
     * @return The descriptor which is an array:
     *         name      => The descriptive name of the sorting order
     *         attribute => Which attribute to sort
     *         direction => The direction to order the result
     *         type      => The data type of the sorted attribute
     *         mainPageTitleKey => I18N key for the title of the main page
     */
    public static function getRandomSortingOrder() {
        return array('name'      => xg_text('RANDOM'),
                     'alias'     => self::SORT_ORDER_RANDOM,
                     'mainPageTitleKey' => 'RANDOM_ALBUMS');
    }

    /**
     * Creates a new album object.
     *
     * @return The new album object
     */
    public static function create() {
        $album = W_Content::create('Album');
        $album->my->mozzle = W_Cache::current('W_Widget')->dir;
        $album->isPrivate      = XG_App::contentIsPrivate();
        $album->my->photoCount = 0;
        $album->my->viewCount  = 0;
        $album->my->hidden = 'Y';
        $album->my->excludeFromPublicSearch = 'Y';
        return $album;
    }

    /**
     * Loads an album.
     *
     * @param id The id of the album
     * @return The album object if it exists, or null
     */
    public static function load($id, $useCache=true) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        return Photo_ContentHelper::findByID('Album', $id, true, $useCache);
    }

    public static function delete($album) {
        XN_Content::delete(W_Content::unwrap($album));
    }

    /**
     * Populates the xg_photo_albumCount field on the User object.
     *
     * @param $user XN_Content|W_Content  the User object to update
     * @param $save boolean  whether to save the User object
     */
    public static function updateAlbumCount($user, $save = TRUE) {
        XG_App::includeFileOnce('/lib/XG_LockHelper.php');
        if (! XG_LockHelper::lock('update-album-count-' . $user->title, 0)) { return; }
        $albumData = Photo_AlbumHelper::getSortedAlbums(array('owner' => $user->title), NULL, 0, 1);
        $user->my->set('xg_photo_albumCount', $albumData['numAlbums'], XN_Attribute::NUMBER);
        if ($save) { $user->save(); }
    }

    /**
     * Returns an array of albums, sorted by recency.
     *
     * @param filters An array of filters to limit the returned photos:
     *                'owner'   => The owner of the album
     *                'includeHidden' => Whether to include hidden albums
     *                'title'   => The title of the album
     *                'photoId' => The id of a photo that the albums have to contain
     * @param begin   The number of the first album to return
     * @param end     The number of the album after the last album to return
     * @return An array 'albums' => the albums, 'numAlbums' => the total number of albums that match the query
     */
    public static function getAlbums($filters, $begin = 0, $end = 100) {

        // TODO: Delegate to getSortedAlbums() [Jon Aquino 2008-02-13]

        $query = XN_Query::create('Content')->filter('type', '=', 'Album')->filter('owner');
        if (! $filters) { $filters = array(); }
        if ($filters['owner']) { $query->filter('contributorName', 'eic', $filters['owner']); }
        if (! $filters['includeHidden']) { $query->filter('my->hidden', '<>', 'Y'); }
        if ($filters['photoId']) { $query->filter('my->photos', 'like', (string)$filters['photoId'] . " "); }
        if ($filters['title']) { $query->filter('title', '=', (string)$filters['title']); }
        $query->order('updatedDate', 'desc', XN_Attribute::DATE);
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        return array('albums' => $query->execute(), 'numAlbums' => $query->getTotalCount());
    }

    /**
     * Returns an array of sorted albums.
     *
     * @param filters An array of filters to limit the returned photos:
     *                'owner'   => The owner of the album
     *                'title'   => The title of the album
     *                'photoId' => The id of a photo that the albums have to contain
     *                'promoted' => Whether to return promoted albums only
     *                'includeHidden' => Whether to include hidden albums
     *                'searchTerms' => String of keywords to search on (using a Content query, not a Search query)
     * @param sort  The sort descriptor as returned by getKnownSortingOrders()
     * @param begin   The number of the first album to return
     * @param end     The number of the album after the last album to return
     * @return An array 'albums' => the albums, 'numAlbums' => the total number of albums that match the query
     */
    public static function getSortedAlbums($filters, $sort, $begin = 0, $end = 100) {
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        if ($filters['promoted'] && ! XG_PromotionHelper::areQueriesEnabled()) { return array('albums' => array(), 'numAlbums' => 0); }
        $query = XN_Query::create('Content')->filter('type', '=', 'Album')->filter('owner');
        if (! $filters) { $filters = array(); }
        if ($filters['owner']) { $query->filter('contributorName', 'eic', $filters['owner']); }
        if ($filters['photoId']) { $query->filter('my->photos', 'like', (string)$filters['photoId'] . " "); }
        if ($filters['title']) { $query->filter('title', '=', (string)$filters['title']); }
        if ($filters['promoted']) { XG_PromotionHelper::addPromotedFilterToQuery($query); }
        if (! $filters['includeHidden']) { $query->filter('my->hidden', '<>', 'Y'); }
        if (mb_strlen($filters['searchTerms'])) { XG_QueryHelper::addSearchFilter($query, $filters['searchTerms']); }
        if ($sort) {
            if ($sort['alias'] == self::SORT_ORDER_RANDOM) {
                $query->order('random()');
            } else {
                $query->order($sort['attribute'], $sort['direction'], $sort['type']);
            }
        } elseif ($filters['promoted']) {
            $query->order('my->' . XG_PromotionHelper::attributeName(), 'desc', XN_Attribute::DATE);
        }
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);

        // BAZ-6710: If we're just asking for promoted albums, cache and only expire
        // when album promotion changes
        if (($filters['promoted']) && (count($filters) == 1)) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_CacheExpiryHelper::promotedObjectsChangedCondition('Album'));
        }

        return array('albums' => $query->execute(), 'numAlbums' => $query->getTotalCount());
    }

    /**
     * Returns the names of all albums of the specified user that do not contain the indicated
     * photo (if given).
     *
     * @param $screenName string      The screen name of the user
     * @param $excludedPhotoId object The id of the photo that should not be in the albums (optional)
     * @return An array album id -> album name, or an empty array if $screenName is null
     */
    public static function getAllAvailableAlbums($screenName, $excludedPhotoId = null) {
        if (! $screenName) { return array(); }
        $allAlbumsFilter = array('owner' => $screenName, 'includeHidden' => true);

        if (isset($excludedPhotoId)) {
            $photoAlbumsFilter = array('owner'   => $screenName, 'includeHidden' => true,
                                       'photoId' => $excludedPhotoId);

            // For now, we have to use two (rolling) queries to get the albums that don't contain the photo
            $photoAlbums = array();
            $begin       = 0;
            do {
                $photoAlbumsData = Photo_AlbumHelper::getAlbums($photoAlbumsFilter, $begin, $begin + 100);
                $photoAlbums     = array_merge($photoAlbums, $photoAlbumsData['albums']);
                $begin           = $begin + 100;
            } while ($photoAlbumsData['numAlbums'] > $begin);
        }

        $albums = array();
        $begin  = 0;
        do {
            $allAlbumsData = Photo_AlbumHelper::getAlbums($allAlbumsFilter, $begin, $begin + 100);
            $albums        = array_merge($albums, $photoAlbums ? array_diff($allAlbumsData['albums'], $photoAlbums) : $allAlbumsData['albums']);
            $begin         = $begin + 100;
        } while ($allAlbumsData['numAlbums'] > $begin);

        $result = array();
        foreach ($albums as $album) {
            $result[$album->id] = $album->title;
        }
        return $result;
    }

    /**
     * Retrieves the cover photos for the given albums
     *
     * @param $albums The albums
     * @return An array of photo objects or null's in the same order as the albums
     */
    public static function getCoverPhotos($albums) {
        if (! $albums) { return array(); }
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $photoIds = array();
        foreach ($albums as $album) {
            if ($album->my->coverPhotoId) {
                $photoIds[] = $album->my->coverPhotoId;
            }
        }
        $photos = array();
        if (count($photoIds) > 0) {
            $photoData = Photo_PhotoHelper::getSpecificPhotos(XN_Profile::current(), $photoIds);
            foreach ($photoData['photos'] as $photo) {
                $photos[$photo->id] = $photo;
            }
        }
        $result = array();
        foreach ($albums as $album) {
            $result[] = $album->my->coverPhotoId ? $photos[$album->my->coverPhotoId] : null;
        }
        return $result;
    }

    /** Whether to update the current user's album count after the next save. */
    private static $updateAlbumCountAfterSave = FALSE;

    /**
     * Called before a content object is saved.
     *
     * @param $content XN_Content  The content object
     */
    public static function beforeSave($content) {
        if ($content->type == 'Photo' && $content->id && $content->my->approved == 'Y' && $content->my->visibility != 'me') {
            foreach (XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Album')->filter('my->photos', 'like', $content->id . " ")->filter('my->hidden', '=', 'Y')->execute() as $album) {
                $album->my->hidden = null; // null rather than 'N', for backwards compatibility with 1.6 [Jon Aquino 2007-05-22]
                $album->my->excludeFromPublicSearch = 'N';
                $album->save();
            }
        } elseif ($content->type == 'Album' && $content->my->hidden == 'Y' && $content->my->photos) {
            W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
            if (XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Photo')->filter('my.approved', '=', 'Y')->filter('my.visibility', '<>', 'me')->filter('id', 'in', Photo_ContentHelper::ids($content, 'photos'))->end(1)->execute()) {
                $content->my->hidden = null;
                $content->my->excludeFromPublicSearch = 'N';
                self::$updateAlbumCountAfterSave = TRUE;
            }
        }
    }

    /**
     * Called after a content object is saved.
     *
     * @param $content XN_Content  The content object
     */
    public static function afterSave($content) {
        if (self::$updateAlbumCountAfterSave) {
            self::$updateAlbumCountAfterSave = FALSE;
            self::updateAlbumCount(User::load(XN_Profile::current()));
        }
    }

    /**
     * Adds the photo to the album.
     *
     * @param XN_Profile profile The profile of the owner of the album
     * @param Album      album   The album
     * @param Photo      photo   The photo to add
     */
    public static function addPhoto($profile, $album, $photo) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');

        if ($album->my->photos) {
            if (Photo_ContentHelper::has($photo->id, $album, 'photos')) {
                return;
            }
        }
        self::updateAlbum($profile, $album);
        Photo_ContentHelper::add($photo->id, $album, 'photos', null, 100, false, false);
        $album->my->photoCount = Photo_ContentHelper::count($album, 'photos');
        if (!$album->my->startDate || ($album->my->startDate > $photo->createdDate)) {
            $album->my->startDate = $photo->createdDate;
        }
        if (!$album->my->endDate || ($album->my->endDate < $photo->createdDate)) {
            $album->my->endDate = $photo->createdDate;
        }
        $album->save();
    }

    /**
     * Sets photos in the album.
     *
     * @param album  The album
     * @param photos The photos
     */
    public static function setPhotos($album, $photos) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');

        // we want to preserve the addeddate for the photos that will remain in the album
        $photoIds = array();
        foreach ($photos as $photo) {
            $photoIds[] = $photo->id;
        }
        Photo_ContentHelper::changeTo($album, 'photos', $photoIds, null, 100);

        $startDate = $album->my->startDate;
        $endDate   = $album->my->endDate;

        foreach ($photos as $photo) {
            if (!$startDate || ($startDate > $photo->createdDate)) {
                $startDate = $photo->createdDate;
            }
            if (!$endDate || ($endDate < $photo->createdDate)) {
                $endDate = $photo->createdDate;
            }
        }
        $album->my->startDate  = $startDate;
        $album->my->endDate    = $endDate;
        $album->my->photoCount = Photo_ContentHelper::count($album, 'photos');
        $album->save();
    }

    /**
     * wrapper for logging the album creation as an activity item.
     *
     * @param $album XN_Content|W_Content   The album
     * @return void
     */
    public static function logAlbumCreation($album) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        self::logAlbumCreationProper($album, new XG_ActivityHelper());
    }


    /**
     * creates an activity dashboard item for the album creation
     *
     * @param $album XN_Content|W_Content   The album
     * @param $xgActivityHelper XG_ActivityHelper object
     * @return void
     */
    protected static function logAlbumCreationProper($album, $xgActivityHelper) {
        if($album->my->hidden != 'Y' && !$album->my->newContentLogItem && $album->contributorName) {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            $logItem = $xgActivityHelper->logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_CONTENT, XG_ActivityHelper::SUBCATEGORY_ALBUM, $album->contributorName, array($album));
            $album->my->newContentLogItem = $logItem->id;
            $album->save();
        }
    }


    /**
     * Removes the photo from the album.
     *
     * @param XN_Profile profile The profile of the owner of the album
     * @param Album      album   The album
     * @param Photo      photo   The photo to remove
     */
    public static function removePhoto($profile, $album, $photo) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');

        if ($album->my->photos) {
            Photo_ContentHelper::remove($photo->id, $album, 'photos');
            if ($album->my->coverPhotoId == $photo->id) {
                $album->my->coverPhotoId = null;
            }
            self::updateAlbum($profile, $album);
            $album->save();
        }
    }

    /**
     * Updates the album so that any no-longer existing photos are removed from it.
     *
     * @param XN_Profile profile The profile of the owner of the album
     * @param Album      album   The album
     */
    public static function updateAlbum($profile, $album) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');

        $ids = Photo_ContentHelper::ids($album, 'photos');
        if (count($ids) > 0) {
            // we get the photo in recency order so that we can easily
            // determine the start and end date
            $photosData = Photo_PhotoHelper::getSpecificPhotos($profile, $ids, Photo_PhotoHelper::getMostRecentSortingOrder(), 0, 100, true);
            $photos     = $photosData['photos'];

            $remainingIds = array();
            foreach ($photos as $photo) {
                $remainingIds[] = $photo->id;
            }

            $idsToRemove = array_diff($ids, $remainingIds);
            foreach ($idsToRemove as $id) {
                Photo_ContentHelper::remove($id, $album, 'photos');
                if ($album->my->coverPhotoId == $id) {
                    $album->my->coverPhotoId = null;
                }
            }
            $album->my->startDate  = $photos[count($photos)]->createdDate;
            $album->my->endDate    = $photos[0]->createdDate;
            $album->my->photoCount = Photo_ContentHelper::count($album, 'photos');
        } else {
            $album->my->startDate    = null;
            $album->my->endDate      = null;
            $album->my->coverPhotoId = null;
            $album->my->photoCount   = 0;
        }
    }
}

XN_Event::listen('xn/content/save/before', array('Photo_AlbumHelper', 'beforeSave'));
XN_Event::listen('xn/content/save/after', array('Photo_AlbumHelper', 'afterSave'));