<?php

/**
 * Common code for saving and querying User objects.
 */
class XG_UserHelper {

    /**
     * Determines whether the other user is a friend of the current user.
     *
     * @param profile    The profile of the current user
     * @param screenName The screen name of the other user
     * @return true iff the other user is a friend
     */
    public static function isFriend($profile, $screenName) {
        if ($profile->isLoggedIn()) {
            // The trick is to query for the user object and restrict the query
            // via a friends query against the owner of the user object (which is
            // the Ning user)
            $query = XN_Query::create('Content')
                             ->filter('type', '=', 'User')
                             ->filter('owner')
                             ->filter('title', 'eic', $screenName)
                             ->filter('contributorName', 'in', XN_Query::FRIENDS());
            return count($query->execute()) > 0;
        } else {
            return false;
        }
    }

    /**
     * Sets the display name on the profile object, the User object and GroupMemberships belonging to the user.
     *
     * @param $profile XN_Profile  the profile of the user to update
     * @param $fullName string  the name displayed on pages
     * @param $updateProfile boolean  whether to update the profile object
     */
    public static function setFullName($profile, $fullName, $updateProfile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($updateProfile) { $profile->fullName = $fullName; }
        if ($user && $user instanceof XN_Content) { W_Content::create($user)->setFullName($fullName); }
        if ($user && $user instanceof W_Content) { $user->setFullName($fullName); }
        GroupMembership::setFullName($profile->screenName, $fullName);
    }

    /**
     * Returns the display name on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return string  the name displayed on pages
     */
    public static function getFullName($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return ($user && $user->my->syncdWithProfile == 'Y') ? $user->my->fullName : $profile->fullName;
    }

    /**
     * Sets the avatar on the profile object, the User object, or both.
     *
     * @param $profile XN_Profile  the profile of the user to update
     * @param $postParameterName string  the name of the post variable containing the upload, e.g., 'photo'
     * @param $updateProfile boolean  whether to update the profile object
     * @param $createActivityLogItem boolean  whether to create an activity log item for this change
     */
    public static function setThumbnailFromPostParameter($profile, $postParameterName, $updateProfile, $createActivityLogItem = true) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1137980255)'); }
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($updateProfile) { $profile->thumbnailUrl = $_POST[$postParameterName]; }
        if ($user) {
            if ($user->my->thumbnailId) {
                // store for later deletion BAZ-5374
                $user->my->previousThumbnailId = $user->my->thumbnailId;
            }
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            list($uploadedFile, $filename, $fileSize, $mimeType) = XG_FileHelper::createUploadedFileObject($postParameterName);
            // Note that storing the URL is sub-optimal, as the URL may change in the future.
            // If it did change, we would need to run some migration script to update all networks.
            // But I talked with Martin and he said it is a reasonable solution for now. [Jon Aquino 2007-09-24]
            $user->my->thumbnailUrl = XG_HttpHelper::addParameters($uploadedFile->fileUrl('data'), array('width' => null, 'height' => null, 'size' => null));
            $user->my->thumbnailId = $uploadedFile->id;
            if ($createActivityLogItem) {
                self::addProfilePhotoChangeToActivityLog($user);
            }
        }
    }

    /**
     * Adds the profile photo update
     *
     * @param $user the user with the new profile photo
     */
    private static function addProfilePhotoChangeToActivityLog($user) {
        if ($user->my->xg_index_status == 'unfinished' || $user->my->xg_index_status == 'pending') {
            return;
        }
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        XG_ActivityHelper::logActivityIfEnabled(
            XG_ActivityHelper::CATEGORY_USER_PROFILE,
            XG_ActivityHelper::SUBCATEGORY_PROFILE_PHOTO,
            $user->contributorName,
            array($user)
        );
    }

    /**
     * Sets the avatar on the User object to a default avatar for the network.
     *
     * @param XN_Profile $profile  the profile of the user to update
     * @param User       $user     User object to update avatar for
     * @return void
     */
    public static function setThumbnailFromDefaultAvatarOrProfile($profile, $user) {
        if (! $profile || ! $user) { return null; }
        $widget = W_Cache::getWidget('main');
        if ($widget->config['defaultAvatarUrl'] && self::hasDefaultNingAvatar($profile)) {
            self::setThumbnailFromUrl($profile, $user, $widget->config['defaultAvatarUrl']);
        } else {
            self::setThumbnailFromProfile($profile);
        }
    }


    /**
     * Sets the avatar on the User object to the image in the profile object.
     *
     * @param $profile XN_Profile  the profile of the user to update
     */
    public static function setThumbnailFromProfile($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if (! $user) { return null; }

        // try three times; workaround for BAZ-5029
        $ok = self::setThumbnailFromProfileProper($profile, $user);
        if (! $ok) {
            $ok = self::setThumbnailFromProfileProper($profile, $user);
        }
        if (! $ok) {
            $ok = self::setThumbnailFromProfileProper($profile, $user);
        }
    }

    /**
     * Returns whether the user's avatar is not corrupt.
     *
     * @param $user XN_Content|W_Content  the User object whose thumbnail to examine
     * @param $log boolean  whether to log bad thumbnail data
     * @return boolean  false if corruption is detected.
     * @see BAZ-5029
     */
    public static function isThumbnailDataOk($user, $log = true) {
        $data = file_get_contents($user->my->thumbnailUrl);
        $ok = self::isThumbnailDataOkProper($data);
        if (!$ok && $log) {
            error_log('BAZ-5029'
                    . ', Screen Name: ' . $user->title
                    . ', Current URL: ' . XG_HttpHelper::currentURL()
                    . ', Referrer: ' . $_SERVER['HTTP_REFERER']
                    . ', Avatar URL: ' . $user->my->thumbnailUrl
                    . ', Avatar Data: ' . $data);
            $x = new Exception();
            error_log("Stack Trace:\n" . $x->getTraceAsString());
        }
        return $ok;
    }

    /**
     * Returns whether the user's avatar is not corrupt.
     *
     * @param $thumbnailData string  the binary data for the thumbnail
     * @return boolean  false if corruption is detected.
     * @see BAZ-5029
     */
    protected static function isThumbnailDataOkProper(&$thumbnailData) {
        return $thumbnailData && strpos($thumbnailData, 'i:') !== 0;
    }


    /**
     * determines whether the XN_Profile has the default Ning.com 'gray man' profile based on
     * whether the request for the avatar without the 'default' param returns a 404
     *
     * @param XN_Profile $profile  the profile of the user to update
     * @param boolean  whether the avatar is the default or not
     */
    public static function hasDefaultNingAvatar($profile) {
        $url = XG_HttpHelper::addParameters($profile->thumbnailUrl(50, 50), array('width' => null, 'height' => null, 'size' => null));
        $url = XG_HttpHelper::removeParameter($url, 'default');
        try {
            $data = XN_REST::get($url);
        } catch (Exception $e) {
            return true;
        }
        return false;
    }

    /**
     * Sets the avatar on the User object to the image from a url.
     *
     * @param XN_Profile $profile  the profile of the user to update
     * @param XN_Content|W_Content $user  the associated User object
     * @param string $url  the url of the avatar
     * @param boolean  whether the operation succeeded
     */
    public static function setThumbnailFromUrl($profile, $user, $url) {
        try {
            $data = XN_REST::get($url);
        } catch (Exception $e) {
            return false;
        }
        if (! self::isThumbnailDataOkProper($data)) { return false; }
        $headers = XN_REST::getLastResponseHeaders();
        $contentType = $headers['Content-Type'] ? $headers['Content-Type'] : self::imageMimeType($data);
        if (! $contentType) { return false; }
        if ($contentType == 'image/null') { return false; } // BAZ-5507 [Jon Aquino 2007-12-07]

        // BAZ-5031 test [Jon Aquino 2007-10-18]
        if (mb_strlen($_GET['test_thumbnail_failure_probability'])) {
            if (mb_strpos(XN_AtomHelper::$DOMAIN_SUFFIX, 'xna') === false) { xg_echo_and_throw('Only works on XNA'); }
            if (mt_rand(0, 100) < $_GET['test_thumbnail_failure_probability']) {
                $data = 'i:1192628831;';
                echo 'Setting bad avatar data . . .<br/>';
            }
        }
        $response = XN_REST::post( '/content?binary=true&type=UploadedFile', $data, $contentType);
        $uploadedFile = XN_AtomHelper::loadFromAtomFeed($response, 'XN_Content');
        $uploadedFile->isPrivate = XG_App::appIsPrivate();
        $uploadedFile->my->mozzle = W_Cache::current('W_Widget')->dir;
        $uploadedFile->save();
        $user->my->thumbnailUrl = XG_HttpHelper::addParameters($uploadedFile->fileUrl('data'), array('width' => null, 'height' => null, 'size' => null));
        $user->my->thumbnailId = $uploadedFile->id;
        return self::isThumbnailDataOk($user);
    }

    /**
     * Sets the avatar on the User object to the image in the profile object.
     *
     * @param XN_Profile $profile  the profile of the user to update
     * @param XN_Content|W_Content $user  the associated User object
     * @param boolean  whether the operation succeeded
     */
    public static function setThumbnailFromProfileProper($profile, $user) {
        $url = XG_HttpHelper::addParameters($profile->thumbnailUrl(50, 50), array('width' => null, 'height' => null, 'size' => null));
        return self::setThumbnailFromUrl($profile, $user, $url);
    }

    /**
     * Returns the MIME type for the given image data.
     *
     * @param $data  the binary data for the image
     * @return string  the MIME type, e.g., image/png
     */
    protected static function imageMimeType(&$data) {
        $tempFilePath = tempnam(NF_APP_BASE . '/xn_private/xn_volatile/', 'image-mime-type-');
        file_put_contents($tempFilePath, $data);
        $imageInfo = getimagesize($tempFilePath);
        unlink($tempFilePath);
        return $imageInfo['mime'];
    }

    /**
     * Returns the avatar URL on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile of the user
     * @param $width integer  an optional width for the returned image url
     * @param $height integer  an optional height for the returned image url
	 * @param $useNetworkDefault boolean	If profile doesn't have a network avatar and has a default ning avater, use default network avatar instead.
     * @return string  the URL for the user's photo
     */
    public static function getThumbnailUrl($profile, $width, $height, $useNetworkDefault = false) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($user && $user->my->syncdWithProfile == 'Y') {
            if (!$user->my->thumbnailUrl) {
                if (!$user->my->thumbnailId) {
                    // fallback to XN_Profile
                } else {
                    try {
                        $thumbnail = XN_Content::load($user->my->thumbnailId);
                        $user->my->thumbnailUrl = $thumbnail->fileUrl();
                    } catch(XN_Exception $e) {
                        $user->my->thumbnailId = '';
                    }
                    $user->save();
                }
            }
            if ($user->my->thumbnailUrl) {
                /* Support XN_Profile::PRESERVE_ASPECT_RATIO (BAZ-4640) */
                $opts = array();
                if ((! is_null($width)) && ($width !== XN_Profile::PRESERVE_ASPECT_RATIO)) {
                    $opts['width'] = $width;
                }
                if ((! is_null($width)) && ($height !== XN_Profile::PRESERVE_ASPECT_RATIO)) {
                    $opts['height'] = $height;
                }
                /* Make sure the image is nice and square (BAZ-4921) */
                $opts['crop'] = '1:1';
                return XG_HttpHelper::addParameters($user->my->thumbnailUrl, $opts);
            }
        }

        // TODO: this happens when user has thumbnailId but doesn't have thumbnailUrl. In this case
        if (!$profile instanceof XN_Profile) {
               $profile = XN_Profile::load($profile->title);
        }
        // if the network has a default avatar, return that, otherwise default to the profile->thumbnailUrl
		if ($useNetworkDefault && ($defaultAvatarUrl = W_Cache::getWidget('main')->config['defaultAvatarUrl']) && self::hasDefaultNingAvatar($profile)) {
            $opts = array();
            if ((! is_null($width)) && ($width !== XN_Profile::PRESERVE_ASPECT_RATIO)) {
                $opts['width'] = $width;
            }
            if ((! is_null($width)) && ($height !== XN_Profile::PRESERVE_ASPECT_RATIO)) {
                $opts['height'] = $height;
            }
            $opts['crop'] = '1:1';
            return XG_HttpHelper::addParameters($defaultAvatarUrl, $opts);
        } else {
            return  $profile->thumbnailUrl($width, $height);
        }
    }

    /**
     * Sets the gender on the profile object, the User object, or both.
     *
     * @param $profile XN_Profile  the profile for the user to update
     * @param $gender string  'm', 'f', or null
     * @param $updateProfile boolean  whether to update the profile object
     */
    public static function setGender($profile, $gender, $updateProfile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($updateProfile) { $profile->gender = $gender; }
        if ($user) { $user->my->gender = $gender; }
    }

    /**
     * Returns the gender on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return string  'm', 'f', or null
     */
    public static function getGender($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return ($user && $user->my->syncdWithProfile == 'Y') ? $user->my->gender : $profile->gender;
    }

    /**
     * Sets the birthdate on the profile object, the User object, or both.
     *
     * @param $profile XN_Profile  the profile of the user to update
     * @param $birthdate string  the birthday, e.g., 1977-02-15, or null
     * @param $updateProfile boolean  whether to update the profile object
     */
    public static function setBirthdate($profile, $birthdate, $updateProfile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($updateProfile) { $profile->birthdate = $birthdate; }
        if ($user) { $user->my->birthdate = $birthdate; }
    }

    /**
     * Returns the birthdate on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return string  the birthday, e.g., 1977-02-15, or null
     */
    public static function getBirthdate($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return ($user && $user->my->syncdWithProfile == 'Y') ? $user->my->birthdate : $profile->birthdate;
    }

    /**
     * Returns the age of the user, in years.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return integer  the age in years, or null
     */
    public static function getAge($profile) {
        $birthdate = self::getBirthdate($profile);
        if (! $birthdate) { return null; }
        // TODO: Handle leap-years [Jon Aquino 2008-02-07]
        return (integer) floor((time() - strtotime($birthdate)) / (60 * 60 * 24 * 365));
    }

    /**
     * Returns whether to display the person's age.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return boolean  whether to show the age
     */
    public static function canDisplayAge($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return $user ? $user->my->displayAge !== 'N' : false;
    }

    /**
     * Returns whether to display the person's gender.
     *
     * @param $profile XN_Profile  the profile of the user
     * @return boolean  whether to show the gender
     */
    public static function canDisplayGender($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return $user ? $user->my->displayGender !== 'N' : true;
    }

    /**
     * Sets the location on the profile object, the User object, or both.
     *
     * @param $profile XN_Profile  the profile for the user to update
     * @param $location string  the city name, or null
     * @param $updateProfile boolean  whether to update the profile object
     */
    public static function setLocation($profile, $location, $updateProfile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if ($updateProfile) { $profile->location = $location; }
        if ($user) { $user->my->location = $location; }
    }

    /**
     * Returns the location on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile for the user
     * @return string  the city name, or null
     */
    public static function getLocation($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        return ($user && $user->my->syncdWithProfile == 'Y') ? $user->my->location : $profile->location;
    }

    /**
     * Sets the country code on the profile object, the User object, or both.
     *
     * @param $profile XN_Profile  the profile for the user to update
     * @param $country string  2-letter country code, e.g., AU
     * @param $updateProfile boolean  whether to update the profile object
     */
    public static function setCountry($profile, $country, $updateProfile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        if (($country != '') && ($country[0] == '_')) {
          $country = '';
        }
        if ($updateProfile) { $profile->country = $country; }
        if ($user) { $user->my->country = $country; }
    }

    /**
     * Returns the country code on the profile object or the User object.
     *
     * @param $profile XN_Profile  the profile for the user
     * @return string  2-letter country code, e.g., AU
     */
    public static function getCountry($profile) {
        if (! $profile) { return null; }
        $user = User::loadOrRetrieveIfLoaded($profile);
        $country = ($user && $user->my->syncdWithProfile == 'Y') ? $user->my->country : $profile->country;
        // First 2 characters only, to prevent XSS attack (BAZ-5541)  [Jon Aquino 2007-12-12]
        return $country ? mb_substr($country, 0, 2) : $country;
    }

    /**
     * The maximum number of User objects that can be opportunistically sync'd
     * per request
     */
    protected static $maxUserSync = 5;

    /**
     * User objects that potentially should get syncd at the end of the
     * request.
     */
    protected static $userSyncMap = array();

    /**
     * Add one or more user objects to the sync map
     *
     * @param User|array object(s) to add
     */
    public static function addToSyncMap($users) {
        $users = is_array($users) ? $users : array($users);
        foreach ($users as $user) {
            if ($user->my->syncdWithProfile != 'Y') {
                self::$userSyncMap[$user->title] = $user;
            }
        }
    }

    /**
     * Sync some loaded User objects with data from the system profile, if they hasn't
     * already been synchronized. This should generally be used to opportunistically
     * sync some User objects that don't represent the currently logged in user.
     * This method has a ceiling of how many User objects it will sync per request.
     *
     */
    public static function syncMapWithProfiles() {
        static $_usersSyncd = 0;
        /* Has the per-request max been reached? */
        if ($_usersSyncd > self::$maxUserSync) { return; }
        /* Trim off any excess users from the list */
        $users = array_slice(self::$userSyncMap, 0, self::$maxUserSync - $_usersSyncd, true);
        /* Are there any users to sync? */
        if (! count($users)) { return; }
        /* Load profile objects for relevant users, they've probably
         * been loaded already at some point in the request */
        $profiles = XG_Cache::profiles(array_keys($users));
        foreach ($users as $user) {
            try {
                $sync = User::syncWithProfile($user, $profiles[$user->title]);
                if ($sync) {
                    $_usersSyncd++;
                }
            } catch (Exception $e) {
                // If something goes wrong with the sync, don't interrupt
                // normal request flow
                $_usersSyncd++;
            }
            if ($_usersSyncd > self::$maxUserSync) { return; }
        }
    }

    /**
     * Returns a unique array of User objects based on an input XN_Content|W_Content or
     * array of the same.
     *
     * @param XN_Content|W_Content|Array(XN_Content|W_Content) objects  The input content object(s)
     *
     * @return Array(XN_Content(User))  The unique set of user objects for the input content object(s)
     */
    public static function uniqueContributorUserObjects($objects) {
        return User::loadMultiple($objects);
    }

}
