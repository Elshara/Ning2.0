<?php
/**
 * Useful functions for working with the profile-info form
 *
 * @see Index_ProfileInfoFormHelperTest
 */
class Index_ProfileInfoFormHelper {

    /**
     * Extracts values from an XN_Profile object.
     *
     * @param $profile XN_Profile  the profile object
     * @return array  birthdateYear (int), birthdateMonth (int), birthdateDay (int), gender (m, f, x), doNotDisplayAge ('1', ''), doNotDisplayGender ('1', '')
     */
    public static function read(XN_Profile $profile) {
        $values = array();
        $values['fullName'] = XG_UserHelper::getFullName($profile);
        $birthdate = XG_UserHelper::getBirthdate($profile);
        if ($birthdate) {
            $parts = explode('-', $birthdate);
            $values['birthdateYear'] = (int) $parts[0];
            $values['birthdateMonth'] = (int) $parts[1];
            $values['birthdateDay'] = (int) $parts[2];
        }
        $values['gender'] = XG_UserHelper::getGender($profile);
        $values['location'] = XG_UserHelper::getLocation($profile);
        $values['country'] = XG_UserHelper::getCountry($profile);
        $values['doNotDisplayAge'] = XG_UserHelper::canDisplayAge($profile) ? '' : '1';
        $values['doNotDisplayGender'] = XG_UserHelper::canDisplayGender($profile) ? '' : '1';
        return $values;
    }

    /**
     * Returns an array of countries, keyed by XN_Profile country-code.
     *
     * @return array  the country codes and countries, e.g., AU => Australia
     */
    public static function countries() {
        if (! class_exists('XG_MessageCatalog_en_US')) {
            XG_App::includeFileOnce('/lib/XG_MessageCatalog_en_US.php');
        }
        $countries = array();
        foreach (XG_MessageCatalog_en_US::countryCodes() as $countryCode) {
            $countries[$countryCode] = xg_text('COUNTRY_' . $countryCode);
        }
        asort($countries);
        return $countries;
    }

    /**
     * Returns an array of favourite countries, keyed by XN_Profile country-code.
     *
     * @return array the country codes and countries.
     */
    public function popularCountries() {
        $popularCountries = array();

        foreach (XG_LanguageHelper::popularCountryCodes() as $countryCode) {
            $popularCountries[$countryCode] = xg_text('COUNTRY_' . $countryCode);
        }

        return $popularCountries;
    }

    /**
     * Validates the form for filling out one's profile.
     *
     * Expected POST variables:
     *     fullName - display name
     *     photo - (optional) uploaded avatar image
     *     aboutQuestionsShown - Y to save the gender, birthdate, location, and country
     *     gender - (optional) (m)ale or (f)emale
     *     birthdateMonth - (optional) 1 for January, etc.
     *     birthdateDay - (optional) 1-31
     *     birthdateYear - (optional) four-digit year
     *     location - (optional) city name
     *     country - (optional) 2-letter country code, e.g., AU
     *
     * @return array  HTML error messages, optionally keyed by field name
     */
    public static function validateForm() {
        $errors = array();
         $acceptedTypes = array('image/jpeg','image/pjpeg','image/gif','image/png','image/x-png');
        if (! $_POST['fullName']) { $errors['fullName'] = xg_html('PLEASE_ENTER_NAME'); }
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        if ($_POST['photo'] && $_POST['photo:status']) { $errors['photo'] = XG_FileHelper::uploadErrorMessage($_POST['photo']); }
        if ($_POST['photo'] && ! in_array($_POST['photo:type'], $acceptedTypes)) {$errors['photo'] = xg_html('PHOTO_MUST_BE_TYPE'); }
        if (! self::isBirthdateValid($_POST)) { $errors['birthdateMonth'] = xg_html('CHOOSE_VALID_BIRTHDAY'); }
        if (mb_strlen($_POST['fullName']) > User::MAX_FULL_NAME_LENGTH) { $errors['fullName'] = xg_html('NAME_MUST_BE_SHORTER', User::MAX_FULL_NAME_LENGTH); }
        if (mb_strlen($_POST['location']) > User::MAX_LOCATION_LENGTH) { $errors['location'] = xg_html('CITY_NAME_MUST_BE_SHORTER', User::MAX_LOCATION_LENGTH); }
        return $errors;
    }

    /**
     * Populates the profile object with the submitted values.
     *
     * Expected POST variables:
     *     fullName - display name
     *     photo - (optional) uploaded avatar image
     *     aboutQuestionsShown - Y to save the gender, birthdate, location, and country
     *     gender - (optional) (m)ale or (f)emale
     *     birthdateMonth - (optional) 1 for January, etc.
     *     birthdateDay - (optional) 1-31
     *     birthdateYear - (optional) four-digit year
     *     location - (optional) city name
     *     country - (optional) 2-letter country code, e.g., AU
     *     doNotDisplayAge - whether to hide the person's age
     *     doNotDisplayGender - whether to hide the person's gender
     *
     * @param $profile XN_Profile  the profile object to update
     * @param $user XN_Content|W_Content  the User object to update
     * @param $newNingUser boolean  whether the user's Ning account was just created
     * @param $newAppUser boolean  whether the user was just created on this app
     */
    public static function write(XN_Profile $profile, $user, $newNingUser, $newAppUser = false) {
        // The code in this function is similar to User::syncWithProfile [Jon Aquino 2007-10-02]
        // Populate profile values if they are empty - see BAZ-4716  [Jon Aquino 2007-10-02]
        XG_UserHelper::setFullName($profile, $_POST['fullName'], $profile->fullName == $profile->screenName || ! mb_strlen($profile->fullName));
        if ($_POST['photo'] && ! $_POST['photo:status']) { // Works with both simple file field and BazelImagePicker [Jon Aquino 2007-09-21]
            XG_UserHelper::setThumbnailFromPostParameter($profile, 'photo', $newNingUser, ! $newAppUser);
        } else {
            // only set image if it doesn't already exist
            if (!$user->my->thumbnailUrl) { // [skip-Syntax7Test]
                XG_UserHelper::setThumbnailFromDefaultAvatarOrProfile($profile, $user);
            }
        }
        if ($_POST['aboutQuestionsShown'] == 'Y') {
            $gender = array('m' => 'm', 'f' => 'f');
            XG_UserHelper::setGender($profile, $gender[$_POST['gender']], ! $profile->gender);
            $birthdate = self::toBirthdate($_POST);
            XG_UserHelper::setBirthdate($profile, $birthdate, ! $profile->birthdate);
            XG_UserHelper::setLocation($profile, $_POST['location'], ! $profile->location);
            XG_UserHelper::setCountry($profile, $_POST['country'], ! $profile->country);
            $user->my->displayAge = $_POST['doNotDisplayAge'] ? 'N' : 'Y';
            $user->my->displayGender = $_POST['doNotDisplayGender'] ? 'N' : 'Y';
        } else {
            XG_UserHelper::setGender($profile, $profile->gender, FALSE);
            XG_UserHelper::setBirthdate($profile, $profile->birthdate, FALSE);
            XG_UserHelper::setLocation($profile, $profile->location, FALSE);
            XG_UserHelper::setCountry($profile, $profile->country, FALSE);
        }
        $user->my->syncdWithProfile = 'Y';
    }

    /**
     * Creates a birthdate from the year, month, and day specified in the POST variables.
     *
     * @param $post array  birthdateYear (4 digits), birthdateMonth (1-12), and birthdateDay (1-31)
     * @return string  a YYYY-MM-DD date, or null if any date components are missing
     */
    public static function toBirthdate($post) {
        if ($post['birthdateYear'] && $post['birthdateMonth'] && $post['birthdateDay']) {
            return date('Y-m-d', strtotime($post['birthdateYear'] . '-' . $post['birthdateMonth'] . '-' . $post['birthdateDay']));
        }
        return null;
    }

    /**
     * Returns whether the birthdate is valid
     *
     * @param $post array  birthdateYear (4 digits), birthdateMonth (1-12), and birthdateDay (1-31)
     * @return boolean  whether the birthdate is valid or not (e.g., February 30)
     */
    public static function isBirthdateValid($post) {
        if (! $post['birthdateYear'] && ! $post['birthdateMonth'] && ! $post['birthdateDay']) { return true; }
        $date = $post['birthdateYear'] . '-' . $post['birthdateMonth'] . '-' . $post['birthdateDay'];
        return $date == date('Y-n-j', strtotime($date));
    }

    /**
     * Returns whether to show the Gender field during the sign-up process.
     *
     * @return boolean  whether to show the field on the Create Your Profile page
     */
    public static function isShowingGenderFieldOnCreateProfilePage($widget = null) {
        if (! $widget) { $widget = W_Cache::getWidget('main'); }
        $result = $widget->config['showGenderFieldOnCreateProfilePage'];
        return mb_strlen($result) == 0 || $result;
    }

    /**
     * Returns whether to show the Location field during the sign-up process.
     *
     * @return boolean  whether to show the field on the Create Your Profile page
     */
    public static function isShowingLocationFieldOnCreateProfilePage($widget = null) {
        if (! $widget) { $widget = W_Cache::getWidget('main'); }
        $result = $widget->config['showLocationFieldOnCreateProfilePage'];
        return mb_strlen($result) == 0 || $result;
    }

    /**
     * Returns whether to show the Country field during the sign-up process.
     *
     * @return boolean  whether to show the field on the Create Your Profile page
     */
    public static function isShowingCountryFieldOnCreateProfilePage($widget = null) {
        if (! $widget) { $widget = W_Cache::getWidget('main'); }
        $result = $widget->config['showCountryFieldOnCreateProfilePage'];
        return mb_strlen($result) == 0 || $result;
    }

}
