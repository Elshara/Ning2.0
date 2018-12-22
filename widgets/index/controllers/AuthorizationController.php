<?php

/**
 * Dispatches requests pertaining to sign-in and sign-up.
 */
class Index_AuthorizationController extends XG_BrowserAwareController {

    // A nice feature of this design is that it maintains the "target" URL in a GET parameter,
    // when you move between the sign-in and sign-up pages. [Jon Aquino 2007-09-22]

    /** Whether to display two email-address fields (instead of two password fields). */
    const CONFIRM_EMAIL_ADDRESS = FALSE;

    /**
     * Runs code before each action.
     */
    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Index_AuthorizationHelper.php');
        XG_App::includeFileOnce('/lib/XG_LogHelper.php');
        XG_HttpHelper::trimGetAndPostValues();
    }

    /**
     * Pokes a hole in the app-wide privacy mechanism.
     *
     * @param $action string  The name of the action
     * @return boolean  Whether to bypass the privacy mechanism for the given action
     */
    public function action_overridePrivacy($action) {
        if (in_array($action, array('signIn', 'doSignIn', 'signOut', 'redirectToSignOut', 'ningId', 'invitationOnly', 'termsOfService', 'privacyPolicy', 'problemsSigningIn', 'requestPasswordReset', 'doRequestPasswordReset', 'passwordResetSent', 'editPassword', 'updatePassword', 'invitationOnly_iphone'))) {
            return true;
        }
        if (! XG_App::appIsPrivate()) { return true; }
        if (in_array($action, array('signUp', 'doSignUp', 'signUpNingUser', 'doSignUpNingUser'))) {
            return $this->currentUserCanSeeSignUpPage();
        }
        // 'newProfile', 'createProfile'
        return XN_Profile::current()->isLoggedIn() && ($this->associatedInvitation() || XG_App::allowJoinByAll());
    }

    /**
     * Displays a form for signing in.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *     emailAddress - (optional) initial value for the Email Address field
     *     invitationExpired - (optional) whether to display a message saying that the invitation has expired
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_signIn($errors = array()) {
        setcookie('xg_cookie_check','1');
        self::log($errors);
        $this->target = $_GET['target'] ? $_GET['target'] : xg_absolute_url('/');
        $this->groupToJoin = $_GET['groupToJoin'];
        $this->form = new XNC_Form(array('emailAddress' => $_GET['emailAddress']));
        $this->errors = $errors;
        $this->showSignUpLink = $this->currentUserCanSeeSignUpPage();
        $this->showInvitationExpiredMessage = $_GET['invitationExpired'];
    }
    //
    public function action_signIn_iphone($errors = array()) { # void
        $this->action_signIn($errors);
    }

    /**
     * Returns whether sign-up links are visible to the current user.
     *
     * @return boolean  whether the user can access the sign-up page
     */
    private function currentUserCanSeeSignUpPage() {
        return ! XG_App::appIsPrivate() || $this->associatedInvitation() || XG_App::allowJoinByAll();
    }

    /**
     * Processes the form for signing in.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *
     * Expected POST variables:
     *     emailAddress - email or username
     *     password - Ning password
     */
    public function action_doSignIn() {
        $this->doSignInProper('signIn', $_POST['emailAddress'], $_POST['password'], $_POST['tosAgree'], $_GET['target'], $_GET['groupToJoin']);
    }
    public function action_doSignIn_iphone() {
        $this->doSignInProper('signIn_iphone', $_POST['emailAddress'], $_POST['password'], $_POST['tosAgree'], $_GET['target'], $_GET['groupToJoin']);
    }

    /**
     * Processes the form for signing in.
     *
     * @param $formAction string  the name of the action that displays the form
     * @param $emailAddress string  email or username
     * @param $password string  Ning password
     * @param $tosAgree string The value indicating that the user has agreed to the TOS and Privacy Policy
     * @param $target string  (optional) URL to go to after sign-in
     * @param $groupToJoin string - (optional) URL of the group to make the user a member of
     */
    private function doSignInProper($formAction, $emailAddress, $password, $tosAgree, $target, $groupToJoin) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo($formAction, 'authorization', array('target' => $target, 'groupToJoin' => $groupToJoin)); }
        if (! $emailAddress) {
            return $this->forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin, array('emailAddress' => xg_html('PLEASE_ENTER_EMAIL_ADDRESS')));
        }
        if (! $password) {
            return $this->forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin, array('password' => xg_html('PLEASE_ENTER_PASSWORD')));
        }
        if (! $tosAgree) {
            return $this->forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin, array('tosAgree' => xg_html('PLEASE_AGREE_TOS')));
        }
        if (is_array($result = XN_Profile::signIn($emailAddress, $password, array('max-age' => 2 * 365 * 24 * 60 * 60)))) {
            return $this->forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin,
                    Index_AuthorizationHelper::errorMessage(key($result), $formAction, $this->associatedInvitation(), $target, $groupToJoin, $emailAddress));
        }
        XG_App::sendNoCacheHeaders(true);
        $this->forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin);
    }

    /**
     * Logs the user out. In this action, we do any necessary clean up,
     * such as clearing invitation cookies, and setting a special 'just logged out'
     * cookie, and redirect to the homepage. The homepage, if it notices the
     * special 'just logged out' cookie, unsets that cookie and displays the
     * "you have just signed out" message. (Putting the just-signed-out status
     * in a cookie keeps it out of the URL and potential bookmarks, etc.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-out
     */
    public function action_signOut() {
        if (XN_Profile::current()->isLoggedIn()) {
            if (! XG_SecurityHelper::checkCsrfToken()) {
                error_log('signOut failed - CSRF token invalid: ' . $_REQUEST['xg_token'] . ' @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
                throw new Exception('Not allowed (950700940)');
            }
            // Set the just-logged-out cookie
            XG_App::setLogoutCookie(true); // TODO: is the just-logged-out cookie used anymore? [Jon Aquino 2008-04-21]
            XN_Profile::signOut();
        }
        $target = $_GET['target'] ? $_GET['target'] : xg_absolute_url('/');
        header('Location: ' . $target);
    }
    public function action_signOut_iphone() {
        $this->action_signOut();
    }

    /**
     * Causes the client to redirect to the sign-out page. Prevents inadvertent
     * auto-sign-outs caused by bad CSS.
     *
     * @see BAZ-4529, BAZ-7028
     */
    public function action_redirectToSignOut() {
        if (! $this->_user->isLoggedIn()) {
            // Prevent search engines from seeing the meta-refresh-based redirection,
            // which can lower search-engine ranking. [Jon Aquino 2008-07-04]
            return $this->redirectTo(xg_absolute_url('/'));
        }
        $this->signOutUrl = XG_HttpHelper::signOutUrl();
    }
    public function action_redirectToSignOut_iphone() {
        if (! $this->_user->isLoggedIn()) {
            // Prevent search engines from seeing the meta-refresh-based redirection,
            // which can lower search-engine ranking. [Jon Aquino 2008-07-04]
            return $this->redirectTo(xg_absolute_url('/'));
        }
        $this->signOutUrl = XG_AuthorizationHelper::signOutUrl();
    }


    /**
     * Displays a form for signing up someone with a Ning account.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *     emailAddress - (optional) initial value for the Email Address field, or null to use that of the current user (if any)
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_signUpNingUser($errors = array()) {
        setcookie('xg_cookie_check','1');
        self::log($errors);
        $this->target = $_GET['target'] ? $_GET['target'] : xg_absolute_url('/');
        $this->invitation = $this->associatedInvitation();
        $this->groupToJoin = $_GET['groupToJoin'];
        $this->form = new XNC_Form(array('emailAddress' => $_GET['emailAddress'] ? $_GET['emailAddress'] : XN_Profile::current()->email));
        $this->errors = $errors;
    }

    /** Script for tracking EOC-144 issues. */
    const EOC_144_SCRIPT = "<script>
        (function() {
            var addParameter = function(url, name, value) {
                var delimiter = url.indexOf('?') > -1 ? '&' : '?';
                return url + delimiter + encodeURIComponent(name) + '=' + encodeURIComponent(value);
            }
            var form = document.getElementById('xg_body').getElementsByTagName('form')[0];
            form.onsubmit = function() {
                form.action = addParameter(form.action, 'eoc144', new Date().getTime());
                return true;
            }
        })();
    </script>";

    /**
     * Processes the form for signing up someone with a Ning account.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *
     * Expected POST variables:
     *     emailAddress - email or username
     *     password - Ning password
     */
    public function action_doSignUpNingUser() {
        $this->doSignInProper('signUpNingUser', $_POST['emailAddress'], $_POST['password'], $_POST['tosAgree'], $_GET['target'], $_GET['groupToJoin']);
    }

    /**
     * Displays a form for signing up.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-up
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *     emailAddress - (optional) initial value for the Email Address field
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_signUp($errors = array()) {
        setcookie('xg_cookie_check','1');
        self::log($errors);
        $this->target = $_GET['target'] ? $_GET['target'] : xg_absolute_url('/');
        $this->invitation = $this->associatedInvitation();
        $this->groupToJoin = $_GET['groupToJoin'];
        $this->form = new XNC_Form(array('emailAddress' => $_GET['emailAddress'], 'birthdateMonth' => '1', 'birthdateDay' => '1', 'birthdateYear' => '1975'));
        $this->errors = $errors;
        $this->captcha = XN_Auth_Captcha::create();
        list($this->yearOptions, $this->monthOptions, $this->dayOptions) = $this->birthdateOptions();
    }
    public function action_signUp_iphone($errors = array()) {
        $this->action_signUp($errors);
        $this->monthOptions = array(xg_text('MONTH'), xg_text('JANUARY_SHORT'), xg_text('FEBRUARY_SHORT'), xg_text('MARCH_SHORT'), xg_text('APRIL_SHORT'), xg_text('MAY_SHORT'), xg_text('JUNE_SHORT'), xg_text('JULY_SHORT'), xg_text('AUGUST_SHORT'), xg_text('SEPTEMBER_SHORT'), xg_text('OCTOBER_SHORT'), xg_text('NOVEMBER_SHORT'), xg_text('DECEMBER_SHORT'));
    }

    /**
     * Returns whether to display the second email-address field on the sign-up form.
     *
     * @return boolean  whether to display the Confirm Email Address field
     */
    protected function shouldConfirmEmailAddress() {
        return self::CONFIRM_EMAIL_ADDRESS;
    }

    /**
     * Returns whether to display the second password field on the sign-up form.
     *
     * @return boolean  whether to display the Confirm Password field
     */
    protected function shouldConfirmPassword() {
        return ! self::CONFIRM_EMAIL_ADDRESS;
    }

    /**
     * Processes the form for signing up
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *
     * Expected POST variables:
     *     emailAddress - email or username
     *     password - Ning password
     */
    public function action_doSignUp() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('signUp', 'authorization', array('target' => $_GET['target'], 'groupToJoin' => $_GET['groupToJoin'])); return; }
        $errors = array();
        if (! $_POST['tosAgree']) {
            $errors['tosAgree'] = xg_html('PLEASE_AGREE_TOS');
        }
        if ($errors) {
			// Suppress the profile check, because otherwise there is a way to sign in w/o ToS checkbox. [Andrey 2008-10-07]
            return $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin'], $errors, false);
        }
        if (true === XN_Profile::signIn($_POST['emailAddress'], $_POST['password'], array('max-age' => 2 * 365 * 24 * 60 * 60))) {
            XG_App::sendNoCacheHeaders(true);
            return $this->forwardOrRedirect('signIn', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin']);
        }
        XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
        if (! $_POST['emailAddress']) {
            $errors['emailAddress'] = xg_html('PLEASE_ENTER_EMAIL_ADDRESS');
        } elseif (! XG_ValidationHelper::isValidEmailAddress($_POST['emailAddress'])) {
            $errors['emailAddress'] = xg_html('EMAIL_NOT_VALID');
        } elseif ($this->shouldConfirmEmailAddress() && ! $_POST['emailAddressConfirmation']) {
            $errors['emailAddressConfirmation'] = xg_html('PLEASE_CONFIRM_EMAIL_ADDRESS');
        } elseif ($this->shouldConfirmEmailAddress() && ! XG_ValidationHelper::isValidEmailAddress($_POST['emailAddressConfirmation'])) {
            $errors['emailAddressConfirmation'] = xg_html('EMAIL_NOT_VALID');
        } elseif ($this->shouldConfirmEmailAddress() && $_POST['emailAddress'] != $_POST['emailAddressConfirmation']) {
            $errors['emailAddressConfirmation'] = xg_html('EMAILS_DO_NOT_MATCH');
        }
        if (! $_POST['password']) {
            $errors['password'] = xg_html('PLEASE_ENTER_PASSWORD');
        } elseif ($this->shouldConfirmPassword() && ! $_POST['passwordConfirmation']) {
            $errors['passwordConfirmation'] = xg_html('PLEASE_ENTER_PASSWORD_AGAIN');
        } elseif ($this->shouldConfirmPassword() && $_POST['password'] != $_POST['passwordConfirmation']) {
            $errors['passwordConfirmation'] = xg_html('PASSWORDS_DO_NOT_MATCH');
        }
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $birthdate = Index_ProfileInfoFormHelper::toBirthdate($_POST);
        if (! $birthdate) {
            $errors['birthdateMonth'] = xg_html('PLEASE_ENTER_BIRTHDAY');
        } elseif (strtotime($birthdate) > strtotime('13 years ago')) {
            return $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin'],
                    array('' => xg_html('INELIGIBLE_TO_REGISTER'))); // BAZ-7407 [Jon Aquino 2008-05-01]
        } elseif (! Index_ProfileInfoFormHelper::isBirthdateValid($_POST)) {
            $errors['birthdateMonth'] = xg_html('CHOOSE_VALID_BIRTHDAY');
        }
        if (! $_POST['captchaValue']) { $errors['captchaValue'] = xg_html('PLEASE_ENTER_CODE'); }
        if ($errors) {
            return $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin'], $errors);
        }
        $captcha = XN_Auth_Captcha::create($_POST['captchaToken']);
        $captcha->value = $_POST['captchaValue'];
        $profile = XN_Profile::create($_POST['emailAddress'], $_POST['password']);
        $profile->birthdate = $birthdate;
        if (is_array($result = $profile->save($captcha))) {
            return $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin'],
                    Index_AuthorizationHelper::errorMessage(key($result), 'signUp', $this->associatedInvitation(), $_GET['target'], $_GET['groupToJoin']));
        }
        if (is_array($result = XN_Profile::signIn($_POST['emailAddress'], $_POST['password'], array('max-age' => 2 * 365 * 24 * 60 * 60)))) {
            return $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin'],
                    Index_AuthorizationHelper::errorMessage(key($result), 'signUp', $this->associatedInvitation(), $_GET['target'], $_GET['groupToJoin']));
        }
        XG_App::sendNoCacheHeaders(true);
        $this->forwardOrRedirect('signUp', $_POST['emailAddress'], $_GET['target'], $_GET['groupToJoin']);
    }
    public function action_doSignUp_iphone() {
        $this->action_doSignUp();
    }

    /**
     * Displays a description of what a Ning ID is.
     *
     * Expected GET variables:
     *     noBack - (optional) whether to show a Close link (instead of a Back link)
     *     previousUrl - (optional) target for the Back link
     */
    public function action_ningId() {
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
    }

    /**
     * Displays a message saying that an invitation is required.
     */
    public function action_invitationOnly() {
        if (! $this->_user->isLoggedIn()) {
            $this->redirectTo(xg_absolute_url('/'));
            return;
        }
    }

    /**
     * Displays a message saying that an invitation is required. (iPhone-specific)
     */
    public function action_invitationOnly_iphone() {
        $this->action_invitationOnly();
    }

    /**
     * Displays the full text of the Ning Terms of Service
     *
     * Expected GET variables:
     *     previousUrl - (optional) target for the Back link
     *     noBack - (optional) whether to show a Close link (instead of a Back link)
     */
    public function action_termsOfService() {
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
        $this->privacyPolicyUrl = $this->_buildUrl('authorization', 'privacyPolicy', array('previousUrl' => $this->previousUrl));
        $this->networkNameHtml = xnhtmlentities(XN_Application::load()->name);
        $this->hasCustomTermsOfService = $this->_widget->config['plugin_termsOfService'];
    }
    public function action_termsOfService_iphone() {
        $this->action_termsOfService();
    }

    /**
     * Displays the full text of the Ning Privacy Policy
     *
     * Expected GET variables:
     *     previousUrl - (optional) target for the Back link
     *     noBack - (optional) whether to show a Close link (instead of a Back link)
     */
    public function action_privacyPolicy() {
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
        $this->termsOfServiceUrl = $this->_buildUrl('authorization', 'termsOfService', array('previousUrl' => $this->previousUrl));
        $this->networkNameHtml = xnhtmlentities(XN_Application::load()->name);
        $this->hasCustomPrivacyPolicy = $this->_widget->config['plugin_privacyPolicy'];
    }
    public function action_privacyPolicy_iphone() {
        $this->action_privacyPolicy();
    }

    /**
     * Displays the full text of the Ning Application Terms of Service
     */
    public function action_applicationTos() {
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
    }
    
    /**
     * Displays the full text of the Ning Application Developer Terms of Service
     */
    public function action_developerTos() {
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
    }
    
    /**
     * Displays troubleshooting tips for sign-in and sign-up.
     *
     * Expected GET variables:
     *     previousUrl - (optional) target for the Back link
     *     noBack - (optional) whether to show a Close link (instead of a Back link)
     */
    public function action_problemsSigningIn() {
        XG_LogHelper::logBasicFlows('problemsSigningIn.');
        if ( !($this->noBack = $_GET['noBack']) ) {
            $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        }
    }

    /**
     * Displays a form for requesting a reset-password email.
     *
     * Expected GET variables:
     *     previousUrl - (optional) target for the Back link
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_requestPasswordReset($errors = array()) {
        self::log($errors);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { XG_LogHelper::logBasicFlows('requestPasswordReset.'); }
        $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
        $this->form = new XNC_Form(array('previousUrl' => $this->previousUrl));
        $this->errors = $errors;
    }

    /**
     * Processes the form for requesting a reset-password email.
     *
     * Expected POST variables:
     *     emailAddress - email or username
     *     previousUrl - (optional) target for the Back link
     */
    public function action_doRequestPasswordReset() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('requestPasswordReset', 'authorization'); return; }
        if (! $_POST['emailAddress']) {
            return $this->forwardTo('requestPasswordReset', 'authorization', array(array('emailAddress' => xg_html('PLEASE_ENTER_EMAIL_ADDRESS'))));
        }
        try {
            if (! XN_Profile::load($_POST['emailAddress'])) {
                return $this->forwardTo('requestPasswordReset', 'authorization', array(array('emailAddress' => xg_html('NO_ACCOUNT_WITH_EMAIL_X', xnhtmlentities($_POST['emailAddress'])))));
            }
        } catch (Exception $e) {
            return $this->forwardTo('requestPasswordReset', 'authorization', array(array('emailAddress' => xg_html('NO_ACCOUNT_WITH_EMAIL_X', xnhtmlentities($_POST['emailAddress'])))));
        }

        $profile = XG_Cache::profiles($_POST['emailAddress']);
        $fullName = XG_UserHelper::getFullName($profile);
        $app = XN_Application::load();
        $appName = $app->name;

        $messageSubject = xg_text('RESET_PASSWORD_EMAIL_SUBJECT');
        $messageTemplate = xg_text('RESET_PASSWORD_EMAIL_BODY', $fullName, $appName);

        if (is_array($result = XN_Profile::resetPassword($_POST['emailAddress'], $messageSubject, $messageTemplate, $this->_buildUrl('authorization', 'editPassword')))) {
            return $this->forwardTo('requestPasswordReset', 'authorization', array(
                    Index_AuthorizationHelper::errorMessage(key($result), 'requestPasswordReset', $this->associatedInvitation())));
        }
        $this->redirectTo('passwordResetSent', 'authorization', array('previousUrl' => $_POST['previousUrl']));
    }

    /**
     * Displays a message saying that the password reset email has been sent.
     *
     * Expected GET variables:
     *     previousUrl - (optional) target for the Back link
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_passwordResetSent($errors = array()) {
        // Is $errors used? [Jon Aquino 2008-06-18]
        $this->previousUrl = $_GET['previousUrl'] ? $_GET['previousUrl'] : xg_absolute_url('/');
    }

    /**
     * Displays a form for editing one's password.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_editPassword($errors = array()) {
        self::log($errors);
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        $this->form = new XNC_Form();
        $this->errors = $errors;
    }

    /**
     * Processes the form for editing one's password.
     *
     * Expected POST variables:
     *     password - the new password
     */
    public function action_updatePassword() {
        XG_SecurityHelper::redirectToSignInPageIfSignedOut();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirectTo('editPassword', 'authorization'); return; }
        if (! $_POST['password']) {
            return $this->forwardOrRedirect('editPassword', null, null, null, array('password' => xg_html('CHOOSE_NEW_PASSWORD')));
        }
        $this->_user->password = $_POST['password'];
        if (is_array($result = $this->_user->save())) {
            return $this->forwardOrRedirect('editPassword', null, null, null, Index_AuthorizationHelper::errorMessage(key($result), 'editPassword', $this->associatedInvitation()));
        }
        $this->forwardOrRedirect('editPassword', XN_Profile::current()->email, null, null);
    }

    /**
     * Displays a form for filling out one's profile.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     newNingUser - "1" if the user's Ning account was just created
     *     groupToJoin - (optional) URL of the group to make the user a member of
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     */
    public function action_newProfile($errors = array()) {
        self::log($errors);
        XG_SecurityHelper::redirectToSignUpPageIfSignedOut();
        $this->errors = $errors;
        $this->target = $_GET['target'] ? $_GET['target'] : xg_absolute_url('/');
        $this->unfinishedProfile = $_GET['unfinishedProfile'];
        $this->newNingUser = $_GET['newNingUser'];
        $this->groupToJoin = $_GET['groupToJoin'];
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $this->showGenderField = Index_ProfileInfoFormHelper::isShowingGenderFieldOnCreateProfilePage();
        $this->showLocationField = Index_ProfileInfoFormHelper::isShowingLocationFieldOnCreateProfilePage();
        $this->showCountryField = Index_ProfileInfoFormHelper::isShowingCountryFieldOnCreateProfilePage();
        $this->aboutMeHtml = trim(xg_age_and_location_proper(
                null,
                $this->showGenderField ? XG_UserHelper::getGender($this->_user) : null,
                $this->showLocationField ? XG_UserHelper::getLocation($this->_user) : null,
                $this->showCountryField ? XG_UserHelper::getCountry($this->_user) : null,
                false));
    }
    public function action_newProfile_iphone($errors = array()) {
        $this->action_newProfile($errors);
    }

    /**
     * Processes the form for filling out one's profile.
     *
     * Expected GET variables:
     *     target - (optional) URL to go to after sign-in
     *     newNingUser - "1" if the user's Ning account was just created
     *     groupToJoin - (optional) URL of the group to make the user a member of
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
     *     TODO: Document POST variables for custom profile questions
     */
    public function action_createProfile() {
        XG_SecurityHelper::redirectToSignUpPageIfSignedOut();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('newProfile', 'authorization', array('target' => $_GET['target'], 'newNingUser' => $_GET['newNingUser'], 'groupToJoin' => $_GET['groupToJoin'])); }
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionFormHelper.php');
        if ($errors = array_merge(Index_ProfileInfoFormHelper::validateForm(), Profiles_ProfileQuestionFormHelper::validateForm())) {
            $this->forwardTo('newProfile', 'authorization', array($errors));
            return;
        }
        $user = User::loadOrCreate($this->_user);
        Index_ProfileInfoFormHelper::write($this->_user, $user, $_GET['newNingUser'], true);
        Profiles_ProfileQuestionFormHelper::write($user);
        if (is_array($result = $this->_user->save())) {
            return $this->forwardTo('newProfile', 'authorization', array(
                    Index_AuthorizationHelper::errorMessage(key($result), 'newProfile', $this->associatedInvitation(), $_GET['target'], $_GET['groupToJoin'])));
        }
        // no changes to user object allowed here until XN_Content reload issue is solved (NING-5370). [Andrey 2008-07-23]
        Index_MembershipHelper::onJoin($this->_user, $user, $this->associatedInvitation()); // Saves the User object [Jon Aquino 2007-09-24]
        if (! $_GET['target']) {
            header('Location: ' . xg_absolute_url('/'));
        } else {
            header('Location: ' . $_GET['target']);
        }
    }
    public function action_createProfile_iphone() {
        $this->action_createProfile();
    }

    /**
     * Displays the form for editing basic profile info. Called by the Create Profile and Edit Profile pages.
     *
     * @param $errors array  (optional) HTML error messages, optionally keyed by field name
     * @param $showSimpleUploadField boolean  whether to show a file upload control instead of a BazelImagePicker
     * @param $aboutMeHtml string  HTML to display in place of the fields; if null or empty, the fields will be displayed
     * @param $showBirthdateFields boolean  whether to display the Month, Day, and Year fields in the About Me section
     * @param $showDisplayAgeCheckbox boolean  whether to show the "Don't Display My Age" checkbox after the birthdate fields
     * @param $showGenderField boolean  whether to display the Male/Female field
     * @param $showLocationField boolean  whether to display the City/State field
     * @param $showCountryField boolean  whether to display the country combobox
     * @param $indicateRequiredFields boolean  whether to add a special marker to required fields
     */
    public function action_profileInfoForm($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $info = Index_ProfileInfoFormHelper::read($this->_user);
        $this->form = new XNC_Form($info);
        list($this->yearOptions, $this->monthOptions, $this->dayOptions) = $this->birthdateOptions();
        $this->countryOptions = array_merge(array('' => xg_text('SELECT')), Index_ProfileInfoFormHelper::popularCountries(), array('_2' => '----------------'), Index_ProfileInfoFormHelper::countries());
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['aboutQuestionsShown'] == 'Y') { $this->aboutMeHtml = null; }
    }
    public function action_profileInfoForm_iphone($args) {
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->_widget->includeFileOnce('/lib/helpers/Index_ProfileInfoFormHelper.php');
        $info = Index_ProfileInfoFormHelper::read($this->_user);
        if ($info['fullName'] == XN_Profile::current()->screenName) {
            $info['fullName'] = ''; // set name to empty string if it's equal to the screenName
        }
        $this->form = new XNC_Form($info);
        list($this->yearOptions, $this->monthOptions, $this->dayOptions) = xg_date_options();
        $this->countryOptions = array_merge(array('' => xg_text('SELECT')), Index_ProfileInfoFormHelper::popularCountries(), array('_2' => '----------------'), Index_ProfileInfoFormHelper::countries());
    }

    /**
     * Returns the values for the birthday select fields.
     *
     * @return array  year, month, and day options
     */
    private function birthdateOptions() {
        $currentYear = date('Y', time());
        $yearOptions = array('' => xg_text('YEAR'));
        for ($i = $currentYear; $i >= $currentYear - 100 ; $i--) { $yearOptions[$i] = $i; }
        $monthOptions = array(xg_text('MONTH'), xg_text('JANUARY'), xg_text('FEBRUARY'), xg_text('MARCH'), xg_text('APRIL'), xg_text('MAY'), xg_text('JUNE'), xg_text('JULY'), xg_text('AUGUST'), xg_text('SEPTEMBER'), xg_text('OCTOBER'), xg_text('NOVEMBER'), xg_text('DECEMBER'));
        $dayOptions = array('' => xg_text('DAY'));
        for ($i = 1; $i <= 31; $i++) { $dayOptions[$i] = $i; }
        return array($yearOptions, $monthOptions, $dayOptions);
    }

    /**
     * Displays the footer for the sign-in (except private apps) and sign-up pages
     *
     * @param $displayAvatars boolean  whether to display the avatars of some active users
     */
    public function action_footer($displayAvatars = true) {
        $minute = 60;
        // When adding a new argument, be sure to add it to the md5 hash below [Jon Aquino 2008-01-04]
        $this->setCaching(array(md5(implode(',', array(
                '/main/authorization/footer',
                $displayAvatars ? 'Y' : 'N',
                XG_App::appIsPrivate() ? 'Y' : 'N')))), 30 * $minute);
        /* BAZ-4927: show some avatars in public networks */
        if ($displayAvatars && ! XG_App::appIsPrivate()) {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
			$users = array();
			foreach(Profiles_UserHelper::getPromotedUsers(6) as $u) {
				$users[] = $u->contributorName;
			}
			if (count($users) < 6) {
                $active = array_values(Profiles_UserHelper::getActiveUsers(6, 'updatedDate'));
                $users = array_slice(array_unique( array_merge($users, $active) ), 0, 6);
            }
			$profiles = XG_Cache::profiles($users); // Profiles are returned in the random order, but we need to display featured first.
            $this->profilesToDisplay = array();
			foreach($users as $screenName) {
				$this->profilesToDisplay[$screenName] = $profiles[$screenName];
			}
        }
    }

    /**
     * Displays the footer for the sign-in page on private apps
     *
     * @see BAZ-3529
     */
    public function action_footerPrivateSignIn() {
    }

    /**
     * Forwards or redirects to an appropriate URL.
     *
     * @param $formAction string  name of the action for the original form: signIn, signUp, signUpNingUser
     * @param $emailAddress string  the email address entered by the user
     * @param $target string  URL for the page to land on eventually, or null to go to the homepage
     * @param $groupToJoin string - (optional) URL of the group to make the user a member of
     * @param $errors array  HTML error messages, optionally keyed by field name, or null if no errors occurred
	 * @param $profileCheck boolean	Check that the profile with this email address exists
     */
    private function forwardOrRedirect($formAction, $emailAddress, $target, $groupToJoin, $errors = null, $profileCheck = true) {
		$profile = $profileCheck ? XG_Cache::profiles($emailAddress) : NULL;
        $args = Index_AuthorizationHelper::nextAction(array(
                'formAction' => $formAction,
                'emailAddress' => $emailAddress,
                'target' => $target,
                'groupToJoin' => $groupToJoin,
                'errors' => $errors,
                'signUpAllowed' => $this->currentUserCanSeeSignUpPage(),
                'isNingUser' => $profile,
                'isPending' => $profile && User::isPending($profile),
                'isMember' => $profile && User::isMember($profile)));
        if ($args[0] == 'forward') {
            return $this->forwardTo($args[1], 'authorization', array($args[2]));
        } elseif ($args[0] == 'redirect') {
            $parameters = $args[2] ? $args[2] : array();
            if ($args[1] == 'newProfile') { $parameters['eoc144'] = $_GET['eoc144']; }
            return $this->redirectTo($args[1], 'authorization', $parameters);
        } else {
            throw new Exception('Assertion failed (1230500910) - ' . var_export($args, true));
        }
    }


    /**
     * Returns the invitation for the current user, specified in $_GET['target'].
     *
     * @return XN_Invitation  the invitation, or  null if the person does not have one
     */
    private function associatedInvitation() {
        static $checked = false;
        static $invitation = null;
        if (! $checked) {
            $checked = true;
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
            $invitation = Index_InvitationHelper::getUnusedInvitation($_GET['target']);
        }
        return $invitation;
    }

    /**
     * Logs the error messages.
     */
    private static function log($errors) {
        if (! $errors) { return; }
        $route = XG_App::getRequestedRoute();
        XG_LogHelper::logBasicFlows($route['actionName'] . '.', $errors);
    }

}
