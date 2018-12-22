<?php

/**
 * Dispatches requests pertaining to the My Settings pages.
 */
class Profiles_SettingsController extends W_Controller {

    // TODO: Maybe merge this into ProfileController [Jon Aquino 2007-09-14]

    /**
     * Runs code before each action.
     */
    protected function _before() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AuthorizationHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Displays a form for changing one's email address.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_editEmailAddress($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember();
        $this->errors = $errors;
        $this->form = new XNC_Form(array('emailAddress' => XN_Profile::current()->email));
    }

    /**
     * Processes the form for editing one's email address.
     *
     * Expected POST variables:
     *     emailAddress - the new email address
     *     password - the password, for confirmation
     */
    public function action_updateEmailAddress() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('editEmailAddress', 'settings'); return; }
        if (! $_POST['emailAddress']) {
            return $this->forwardTo('editEmailAddress', 'settings', array(array('emailAddress' => xg_html('PLEASE_ENTER_EMAIL_ADDRESS'))));
        }
        $oldEmailAddress = $this->_user->email;
        if (strcasecmp($_POST['emailAddress'], $oldEmailAddress)) {
            XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
            if (! XG_ValidationHelper::isValidEmailAddress($_POST['emailAddress'])) {
                return $this->forwardTo('editEmailAddress', 'settings', array(array('emailAddress' => xg_html('X_IS_NOT_VALID_EMAIL_ADDRESS', $_POST['emailAddress']))));
            }
            if (! $_POST['password']) {
                return $this->forwardTo('editEmailAddress', 'settings', array(array('password' => xg_html('PLEASE_ENTER_PASSWORD'))));
            }
            if (is_array($result = XG_AuthorizationHelper::verifyPassword($oldEmailAddress, $_POST['password']))) {
                return $this->forwardTo('editEmailAddress', 'settings', array(Index_AuthorizationHelper::errorMessage(key($result), 'editEmailAddress', null, W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo'))));
            }
            $this->_user->email = $_POST['emailAddress'];
            if (is_array($result = $this->_user->save())) {
                return $this->forwardTo('editEmailAddress', 'settings', array(Index_AuthorizationHelper::errorMessage(key($result), 'editEmailAddress', null, W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo'))));
            }
            XG_App::includeFileOnce('/lib/XG_Message.php');
            $msg = new XG_Message_ChangedEmailAddress();
            $msg->send($oldEmailAddress);
            $msg = new XG_Message_ChangedEmailAddress();
            $msg->send($this->_user->email);
        }
        header('Location: ' . $this->_buildUrl('settings', 'editProfileInfo', array('saved' => '1')));
    }

    /**
     * Displays a form for changing one's password.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_editPassword($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember();
        $this->errors = $errors;
        $this->form = new XNC_Form();
    }

    /**
     * Processes the form for editing one's password.
     *
     * Expected POST variables:
     *     currentPassword - the old password
     *     newPassword - the desired password
     *     confirmPassword - the desired password again, for confirmation
     */
    public function action_updatePassword() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('editPassword', 'settings'); return; }
        if (! $_POST['currentPassword']) {
            return $this->forwardTo('editPassword', 'settings', array(array('currentPassword' => xg_html('PLEASE_ENTER_CURRENT_PASSWORD'))));
        }
        if (! $_POST['newPassword']) {
            return $this->forwardTo('editPassword', 'settings', array(array('newPassword' => xg_html('PLEASE_ENTER_NEW_PASSWORD'))));
        }
        if (! $_POST['confirmPassword']) {
            return $this->forwardTo('editPassword', 'settings', array(array('password' => xg_html('PLEASE_ENTER_NEW_PASSWORD_AGAIN'))));
        }
        if ($_POST['newPassword'] != $_POST['confirmPassword']) {
            return $this->forwardTo('editPassword', 'settings', array(array('password' => xg_html('NEW_AND_CONFIRMATION_PASSWORDS'))));
        }
        if (is_array($result = XG_AuthorizationHelper::verifyPassword($this->_user->email, $_POST['currentPassword']))) {
            return $this->forwardTo('editPassword', 'settings', array(Index_AuthorizationHelper::errorMessage(key($result), 'editPassword', null, W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo'))));
        }
        $this->_user->password = $_POST['newPassword'];
        if (is_array($result = $this->_user->save())) {
            return $this->forwardTo('editPassword', 'settings', array(Index_AuthorizationHelper::errorMessage(key($result), 'editPassword', null, W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo'))));
        }
        header('Location: ' . $this->_buildUrl('settings', 'editProfileInfo', array('saved' => '1')));
    }

    /**
     * Displays a form for editing one's profile info.  Also, all Account edits save and then redirect to this page to display success.
     *
     * Expected GET variables:
     *     saved - 1 if the settings were successfully changed.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_editProfileInfo($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember(null, true);
        $this->errors = $errors;
        $this->displaySavedNotification = $_GET['saved'];
        // Catch a possible exception if there is a backend error
        // retrieving the upload email address (BAZ-4668)
        try {
            $this->uploadEmailAddress = $this->_user->uploadEmailAddress;
        } catch (Exception $e) {
            $this->uploadEmailAddress = '';
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $this->numQuestions = count(Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles')));
    }

    /**
     * Processes the form for filling out one's profile.
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
     */
    public function action_updateProfileInfo() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('editProfileInfo', 'settings'); return; }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionFormHelper.php');
        $errors = array_merge(Index_ProfileInfoFormHelper::validateForm(), Profiles_ProfileQuestionFormHelper::validateForm());
        if ($errors) {
            return $this->forwardTo('editProfileInfo', 'settings', array($errors));
        }
        $user = User::load($this->_user);
        Index_ProfileInfoFormHelper::write($this->_user, $user, false);
        if (is_array($result = $this->_user->save())) {
            return $this->forwardTo('editProfileInfo', 'settings', array(Index_AuthorizationHelper::errorMessage(key($result), 'editProfileInfo')));
        }
        Profiles_ProfileQuestionFormHelper::write($user = User::load($this->_user));
        $user->save();
        // tidy up old profile image object. BAZ-5374
        if ($user->my->previousThumbnailId && $_POST['photo'] && ! $_POST['photo:status']) {
            try {
                XN_Content::delete($user->my->previousThumbnailId);
            } catch (Exception $e) {}
        }

        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
        //generate activity log item for the profile update (changed the answers)
        Profiles_ActivityLogHelper::createProfileUpdateItem($user);

        header('Location: ' . $this->_buildUrl('settings', 'editProfileInfo', array('saved' => '1')));
    }

    /**
     * Displays a form for changing one's profile URL.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_editProfileAddress($errors = array()) {
        XG_SecurityHelper::redirectIfNotMember();
        $this->errors = $errors;
        $this->form = new XNC_Form(array('profileAddress' => User::profileAddress($this->_user->screenName)));
    }

    /**
     * Processes the form for editing one's profile URL.  Saves changes or sends back to edit form as appropriate.
     *
     * Expected POST variables:
     *     profileAddress - last portion of the URL to the user's profile page,
     *     e.g., SilverSurfer in http://networkname.ning.com/profile/SilverSurfer
     */
    public function action_updateProfileAddress() {
        XG_SecurityHelper::redirectIfNotMember();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('editProfileAddress', 'settings'); return; }
        if (! $_POST['profileAddress']) {
            return $this->forwardTo('editProfileAddress', 'settings', array(array('profileAddress' => xg_html('PLEASE_ENTER_PAGE_ADDRESS'))));
        }
        if (! preg_match('/^[0-9a-zA-Z_]+$/u', $_POST['profileAddress'])) {
            return $this->forwardTo('editProfileAddress', 'settings', array(array('profileAddress' => xg_html('PAGE_ADDRESS_NOT_VALID'))));
        }
        if (mb_strlen($_POST['profileAddress']) > User::MAX_PROFILE_ADDRESS_LENGTH) {
            return $this->forwardTo('editProfileAddress', 'settings', array(array('profileAddress' => xg_html('ADDRESS_MUST_BE_SHORTER', User::MAX_PROFILE_ADDRESS_LENGTH))));
        }
        $user = User::load(XN_Profile::current());
        // Just bump them to the saved page if they haven't made a change.
        if ($user->my->profileAddress === $_POST['profileAddress']) {
            return $this->redirectTo('editProfileInfo', 'settings', array('saved' => '1'));
        }
        if (! $user->lockProfileAddress($_POST['profileAddress'])) {
            return $this->forwardTo('editProfileAddress', 'settings', array(array('profileAddress' => xg_html('PAGE_ADDRESS_TAKEN'))));
        }
        User::setProfileAddress($user, $_POST['profileAddress']);
        $user->save();
        return $this->redirectTo('editProfileInfo', 'settings', array('saved' => '1'));
    }

    public function action_index() {
        error_log('BAZ-4672');
        error_log('Current URL: ' . XG_HttpHelper::currentURL());
        error_log('Referrer: ' . $_SERVER['HTTP_REFERER']);
    }

}
