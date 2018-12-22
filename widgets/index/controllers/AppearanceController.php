<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');

class Index_AppearanceController extends W_Controller {

    public function action_edit() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('save');
            return;
        }

        $this->defaults = array();
        $this->imagePaths = array();
        Index_AppearanceHelper::getAppearanceSettings(NULL, $this->defaults,
                $this->imagePaths);
        $this->fontOptions = Index_AppearanceHelper::getFontAlternatives();
        $this->themes = Index_AppearanceHelper::getThemeNames();

        $this->ningLogoDisplayChecked = (isset($this->defaults['ningLogoDisplay'])
                && (mb_substr($this->defaults['ningLogoDisplay'], 0, 5) == 'block'));

        $app = XN_Application::load(); //TODO what is this for?  Remove it?

        $this->submitUrl = W_Cache::getWidget('main')->buildUrl('appearance', 'edit');

        //  If we're in the prelaunch ('gyo') sequence, the buttons at the bottom
        //    change
        $this->displayPrelaunchButtons = !XG_App::appIsLaunched();
        if ($this->displayPrelaunchButtons) {
            $this->backLink = XG_App::getPreviousStepUrl();
            $this->nextLink = XG_App::getNextStepUrl();
        }

        // TODO: use capturePartial() once available [2008-09-23 - Travis S]
        try {
            ob_start();
            $this->renderPartial('fragment_success', 'admin');
            $this->successMessageIfAny = ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }  // action_edit()


    public function action_save() {
        XG_SecurityHelper::redirectIfNotAdmin();
        Index_AppearanceHelper::setAppearanceSettings(NULL, $_POST);
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        XG_EmbeddableHelper::generateResources();

        //  Mark the step completed if we haven't yet
        if (!XG_App::allStepsCompleted()) {
            //  Mark the prelaunch step as completed if necessary
            XG_App::markStepCompleted('Appearance');
        }

        //  Check for an explicit success target (e.g. launch)
        if (isset($_POST['successTarget']) && mb_strlen($_POST['successTarget']) > 0) {
            header('Location: ' . $_POST['successTarget']);
            exit;
        }
        else {
            if (XG_App::appIsLaunched()) {
                //  We're editing post-sequence - redisplay the form
                $this->redirectTo('edit', 'appearance', array('saved' => 1));
            } else {
                //  Redirect to the new current step
                $nextStep = XG_App::currentLaunchStepRoute();
                $this->redirectTo($nextStep['actionName'], $nextStep['controllerName']);
            }
        }
    }  // action_save()

    /**
     *  Action called via dojo to get settings of a specific theme
     *
     *   Theme name is provided in the theme GET parameter - if it's absent
     *     we return the settings for the currently applied sitewide theme
     */
    public function action_getThemeSettings() {
        if (isset($_GET['theme']) && mb_strlen($_GET['theme']) > 0) {
            $themeCssPath = NF_APP_BASE . $this->_widget->buildResourceUrl('css/themes/'
                    . stripslashes($_GET['theme']) . '.css');
            $customCssPath = NF_APP_BASE . $this->_widget->buildResourceUrl('css/themes/'
                    . stripslashes($_GET['theme']) . '-custom.css');
        }
        else {
            $themeCssPath = Index_AppearanceHelper::getThemeCssFilename();
            $customCssPath = Index_AppearanceHelper::getCustomCssFilename();
        }
        if (@$themeCss = file_get_contents($themeCssPath)) {
            list($settings, $paths) = Index_AppearanceHelper::parseCss($themeCss);
            $fontKeys = array_flip(Index_AppearanceHelper::getFontAlternatives());
            $this->colors = array();
            $this->fonts = array();
            $this->images = array();
            $this->imageRepeat = array();
            foreach ($settings as $key => $value) {
                if (mb_substr($key, -mb_strlen('Color')) == 'Color') {
                    $this->colors[$key] = $value;
                }
                else if (mb_substr($key, -mb_strlen('Font')) == 'Font') {
                    $this->fonts[$key] = $fontKeys[$value];
                }
                else if (mb_substr($key, -mb_strlen('_repeat')) == '_repeat') {
                    $this->imageRepeat[mb_substr($key, 0, -mb_strlen('_repeat'))]
                            = $value;
                }
            }
            foreach ($paths as $key => $value) {
                $this->images[$key] = $value;
            }
            if (@$customCss = file_get_contents($customCssPath)) {
                $this->customCss = $customCss;
            }
        }
    } // action_getThemeSettings()

    public function action_index() {
        error_log('BAZ-2332');
        error_log('Current URL: ' . XG_HttpHelper::currentURL());
        error_log('Referrer: ' . $_SERVER['HTTP_REFERER']);
    }

}
