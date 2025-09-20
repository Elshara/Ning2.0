<?php
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

/**
 * The methods in this controller provide wrappers for content/profile
 * addition during the initial setup process
 */
class Index_ContentController extends W_Controller {


    /**
     * For the app owner to add their initial profile
     */
    public function action_profile() {
        XG_SecurityHelper::redirectIfNotAdmin();

        /* Pass the form target URL to the profile/edit action so that the submission stays within the
         * pre-launch flow */
        W_Cache::getWidget('profiles')->dispatch('settings','editProfileInfo', array($this->_buildUrl('content','profile')));
    }

    /**
     * For the app owner to add initial pieces of content on app setup, or for
     * individual users to add initial content when joining the app
     *
     */
    public function action_content() {
        if (! $this->_user->isLoggedIn()) {
            $this->redirectTo('index','index');
            return;
        }

        /* get content entry form fragments from each mozzle */
        /* As new mozzles come online before 1.0, add them to the list here.
         * In the future, we'll determine this dynamically */
        $widgets = array('profiles','photo');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('saveContent','content',array($widgets));
            return;
        }

        $this->fragments = array();
        foreach ($widgets as $widgetName) {
            try {
                $widget = W_Cache::getWidget($widgetName);
                if ($widget && $widget->controllerHasAction('index','addContent')) {
                    list($r, $html) = $widget->capture('index','addContent');
                    $this->fragments[] = $html;
                }
            } catch (Exception $e) {
                error_log('Add content error: ' . $e->getMessage());
            }
        }

        // setup appropriate target if it's not prelaunch, but joinflow
        $joinTarget = isset($_GET['joinTarget']) ? $_GET['joinTarget'] : ( isset($_POST['joinTarget']) ? $_POST['joinTarget'] : null);
        if (isset($joinTarget)) {
            $this->inJoinFlow = true;
            $defaults['joinTarget'] = $joinTarget;
        } else {
            $this->inJoinFlow = false;
        }

        $this->form = new XNC_Form($defaults);

        /* Sets instance variables: manage, prelaunchSteps, requestedStep, backLink
         * if app is not launched yet */
        $this->setupPrelaunchBacklink();
    }

   public function action_saveContent($widgets = null) {
        // Save the content as appropriate
        if (is_array($widgets)) {
            foreach ($widgets as $widgetName) {
                try {
                    $widget = W_Cache::getWidget($widgetName);
                    if ($widget && $widget->controllerHasAction('index','addContent')) {
                        $widget->dispatch('index','addContent');
                    }
                } catch (Exception $e) {
                    error_log('Save content error: ' . $e->getMessage());
                }
            }
        }

        //  Mark the step completed if we haven't yet
        if (!XG_App::allStepsCompleted()) {
            //  Mark the prelaunch step as completed if necessary
            XG_App::markStepCompleted('Add Content');
            $nextStep = XG_App::currentLaunchStepRoute();
        } else {
            $nextStep = null;
        }

        // Setup appropriate target if it's not prelaunch, but joinflow


        //  Check for an explicit success target (e.g. launch)
        $successTarget = null;
        if (isset($_POST['successTarget']) && ! is_array($_POST['successTarget'])) {
            $successTarget = XG_HttpHelper::normalizeRedirectTarget($_POST['successTarget']);
        }
        if ($successTarget !== null) {
            header('Location: ' . $successTarget);
            exit;
        }

        $joinTarget = null;
        if (isset($_POST['joinTarget']) && ! is_array($_POST['joinTarget'])) {
            $joinTarget = XG_HttpHelper::normalizeRedirectTarget($_POST['joinTarget']);
        }
        if ($joinTarget !== null) {
            // After adding content, visit the invite page (BAZ-947)
            $this->redirectTo('invite','index', array('joinTarget' => $joinTarget));
            return;
        } else {
            if (is_null($nextStep)) {
                //  We're editing post-sequence - redisplay the form
                $this->redirectTo('content','content',array('saved' => 1));
            } else {
                //  Redirect to the new current step
                $this->redirectTo($nextStep['actionName'], $nextStep['controllerName']);
            }
        }
   }

    /**
     *  Called via dojo POST - subscribes the current user to the notification
     *    alias for the specified content object.
     *
     *  GET variables:
     *    id - ID of content object to start following
     */
    public function action_startFollowing() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            error_log('not a post');
            throw new Exception('startFollowing can only be called by POST!');
        }
        if (!isset($_GET['id']) || !($obj = XN_Content::load($_GET['id']))) {
            error_log('bad ID ' . $_GET['id']);
            throw new Exception('couldn\'t load object with ID ' . $_GET['id'] . '!');
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        Index_NotificationHelper::startFollowing($obj);
        $this->success = '1';
    }

    /**
     *  Called via dojo POST - unsubscribes the current user from the notification
     *    alias for the specified content object.
     *
     *  GET variables:
     *    id - ID of content object to stop following
     */
    public function action_stopFollowing() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new Exception('stopFollowing can only be called by POST!');
        }
        if (!isset($_GET['id']) || !($obj = XN_Content::load($_GET['id']))) {
            throw new Exception('couldn\'t load object with ID ' . $_GET['id'] . '!');
        }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        Index_NotificationHelper::stopFollowing($obj);
        $this->success = '1';
    }

   protected function setupPrelaunchBacklink() {
        $this->displayPrelaunchButtons = !XG_App::appIsLaunched();
        if ($this->displayPrelaunchButtons) {
            $this->backLink = XG_App::getPreviousStepUrl();
            $this->nextLink = XG_App::getNextStepUrl();
        }
   }
}
