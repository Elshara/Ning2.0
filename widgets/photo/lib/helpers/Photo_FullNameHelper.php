<?php
class Photo_FullNameHelper {

    private static $screenNameToProfileMap = array();
    private static $initialized = false;

    public static function _initialize() {
        if (XN_Profile::current()->isLoggedIn()) { self::$screenNameToProfileMap[XN_Profile::current()->screenName] = XN_Profile::current(); }
    }

    /**
     * @param $objectsOrScreenNamesOrProfiles Nulls and empty strings are skipped
     */
    public static function initialize($objectsOrScreenNamesOrProfiles) {
        self::$initialized = true;
        $screenNames = array();
        foreach ($objectsOrScreenNamesOrProfiles as $objectOrScreenNameOrProfile) {
            if (! $objectOrScreenNameOrProfile) { continue; }
            if (is_string($objectOrScreenNameOrProfile)) {
                $screenNames[$objectOrScreenNameOrProfile] = $objectOrScreenNameOrProfile;
            } elseif ($objectOrScreenNameOrProfile instanceof XN_Profile) {
                self::$screenNameToProfileMap[$objectOrScreenNameOrProfile->screenName] = $objectOrScreenNameOrProfile;
            } else {
                $screenNames[$objectOrScreenNameOrProfile->contributorName] = $objectOrScreenNameOrProfile->contributorName;
            }
        }
        if (! count($screenNames) || (count($screenNames) == 1 && $screenNames[0] == XN_Profile::current()->screenName)) { return; }
        //self::$screenNameToProfileMap = array_merge(self::$screenNameToProfileMap, XG_Cache::profiles($screenNames));
        foreach( XG_Cache::profiles($screenNames) as $key => $value) {
            self::$screenNameToProfileMap[$key] = $value;
        }
    }

    public static function fullName($screenName) {
        return self::profile($screenName) ? XG_UserHelper::getFullName(self::profile($screenName)) : $screenName;
    }

    public static function profile($screenName) {
        if (! self::$screenNameToProfileMap[$screenName]) {
            //self::$screenNameToProfileMap = array_merge(self::$screenNameToProfileMap, XG_Cache::profiles(array($screenName => $screenName)));
            foreach( XG_Cache::profiles(array($screenName => $screenName)) as $key => $value) {
                self::$screenNameToProfileMap[$key] = $value;
            }
        }
        return self::$screenNameToProfileMap[$screenName];
    }

}

Photo_FullNameHelper::_initialize();