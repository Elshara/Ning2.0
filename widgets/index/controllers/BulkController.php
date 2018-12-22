<?php

class Index_BulkController extends W_Controller {

    public function action_overridePrivacy($action) {
        /* Allow non-members to access removeByUser so they
         * can completely delete themselves (see BAZ-1637) */
        return ($action == 'removeByUser');
    }

    /**
     * Sets the privacy level for the network, one chunk of objects from the content store at a time.
     *
     * Should only be called via xn_out=json and by the app owner.
     *
     * Expected POST, with privacyLevel in querystring, with value of 'public' or 'private'.
     */
    public function action_setPrivacy() {
        // The following types should not have their privacy altered: Comment, Invitation, TopicCommenterLink, PageCommenterLink
        XG_SecurityHelper::redirectIfNotOwner();
        try {
            /* Requests must be POST, to impede spiders, but the privacyLevel is
             * passed in the query string to make it easier to provide the data to
             * the dojo widget that creates the link
             */
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception("Only POST requests permitted"); }
            $privacyLevel = $_GET['privacyLevel'];
            if ($privacyLevel !== 'public' && $privacyLevel !== 'private') { throw new Exception("Desired privacy level must be 'public' or 'private'"); }
            if (($privacyLevel === 'private') === XG_App::appIsPrivate()) {
                $this->contentChanged = 0;
                $this->contentRemaining = 0;
                return;
            }
            // Take the network offline to prevent people creating new content with the wrong privacy level while this is done.
            XG_App::setOnlineStatus(false); //TODO should only happen once and only then if desiredOnlineStatus is 'true'.

            $objectLimit = 30;
            $changed = 0;

            // First change all objects that have no mozzle or belong to 'main'.
            XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
            $noMozzle = null;
            $changed += XG_PrivacyHelper::setContentPrivacy($objectLimit, ($privacyLevel === 'private'), $noMozzle);
            if ($changed >= $objectLimit) {
                $this->contentChanged = $changed;
                $this->contentRemaining = 1;
                return;
            }
            $changed += XG_PrivacyHelper::setContentPrivacy($objectLimit - $changed, ($privacyLevel === 'private'), 'main');
            if ($changed >= $objectLimit) {
                $this->contentChanged = $changed;
                $this->contentRemaining = 1;
                return;
            }

            // Now loop through all the other mozzles and change their objects.
            $bulkInfo = $this->bulkAction('bulk', 'setPrivacy', $objectLimit - $changed, array($privacyLevel));
            // Set these so the JSON message contains them
            $this->contentChanged = $bulkInfo['changed'];
            $this->contentRemaining = $bulkInfo['remaining'];

            if (! $this->contentRemaining) {
                // OK, we're done, set the network to it's new privacy
                XG_App::setNetworkPrivacyLevel($privacyLevel);
                // and open it up to everyone else again, if that's necessary.
                if ($_GET['finalOnlineStatus'] === 'true') {
                    XG_App::setOnlineStatus(true);
                }
                XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            error_log($e->getMessage());
        }
    }

   /**
     * For removing all content by a user, one chunk at a time. Should only
     * be called via xn_out=json and by the app owner
     */
    public function action_removeByUser() {
        // How many content objects to delete per request
        $deleteLimit = 20;
        try {
            /* Requests must be POST, to impede spiders, but the user is passed
             * in the query string to make it easier to provide the data to
             * the dojo widget that creates the link
             */
            if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception("Only POST requests permitted"); }
            // Banning from app or group puts user in POST rather than GET [Jon Aquino 2007-05-01]
            $username = $_GET['user'] ? $_GET['user'] : ($_POST['user'] ? $_POST['user'] : null);
            if (! $username) { throw new Exception("No user specified"); }
            XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
            if (! XG_SecurityHelper::currentUserCanDeleteUser($username)) { throw new Exception("Permission denied."); }
	    
            $bulkInfo = $this->bulkAction('bulk','removeByUser', $deleteLimit, array($username));
            // Set these so the JSON message contains them
            $this->contentDeleted = $bulkInfo['changed'];
            $this->contentRemaining = $bulkInfo['remaining'];

        } catch (Exception $e) {
	    error_log("Removing user {$username} failed: {$e->getMessage()}");
	    $this->errorMessage = xg_text('BAN_FAILED_TRY_LATER');
        }
    }

    /**
     * For approving all content by a user, one chunk at a time. Should only
     * be called via xn_out=json and by the app owner.
     */
     public function action_approveByUser() {
        // How many content objects to approve per request
        $approveLimit = 20;
        try {
            if (! XG_SecurityHelper::userIsAdmin()) {
                throw new Exception("Permission denied.");
            }
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception("Only POST requests permitted");
            }
            if (! (isset($_GET['user']) && mb_strlen($_GET['user']))) {
                throw new Exception("No user specified");
            }
            $bulkInfo = $this->bulkAction('bulk','approveByUser', $approveLimit, array($_GET['user']));
            // Set these so the JSON message contains them
            $this->contentApproved = $bulkInfo['changed'];
            $this->contentRemaining = $bulkInfo['remaining'];
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
     }

    /**
     * For approving all content that needs to be approved in a widget, one chunk
     * at a time. Should only be called via xn_out=json and by the app owner
     */
     public function action_approveAll() {
        // How many content objects to approve per request
        $approveLimit = 20;
        try {
            if (! XG_SecurityHelper::userIsAdmin()) {
                throw new Exception("Permission denied.");
            }
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception("Only POST requests permitted");
            }
            if (! (isset($_GET['widget']) && mb_strlen($_GET['widget']))) {
                throw new Exception("No widget specified");
            }
            $bulkInfo = $this->bulkAction('bulk','approveAll', $approveLimit, array(), $_GET['widget']);
            // Set these so the JSON message contains them
            $this->contentApproved = $bulkInfo['changed'];
            $this->contentRemaining = $bulkInfo['remaining'];
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
     }

     /**
      * For exporting member profile information to .csv file
      * should only be called via xn_out=json and an admin
      */
      public function action_exportMemberData() {
          if (! XG_SecurityHelper::userIsAdmin()) {
              throw new Exception("Permission denied.");
          }
          try {
             /*
             *  Requests must be a POST operation by an administrator
             */
             if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception("Only POST requests permitted"); }
             $usersPerRequest = 50;
             // What is the starting and ending offset of this page?
             $start = $_POST['counter'] * $usersPerRequest;
             $end = $start + $usersPerRequest;
             $this->_widget->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
             if ($_POST['counter'] == 0) {
                 $csv = @fopen(Index_MembershipHelper::memberDataExportFile(),'w');
             } else {
                 $csv = @fopen(Index_MembershipHelper::memberDataExportFile(),'a');
             }

             if ($csv === false) {
                 throw new Exception("Can't open member data export file");
             }

             $profilesWidget = W_Cache::getWidget('profiles');
             $profilesWidget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
             $questions = Profiles_ProfileQuestionHelper::getQuestions($profilesWidget);

             if ($_POST['counter'] == 0) {
                 // should these headings use the message catalog? [ywh 2008-05-29]
                 $questionArray = array("Name", "Profile Address", "Email", "Gender", "Location", "Country", "Age", "Birthdate");
                 foreach ($questions as $question) {
                     $questionArray[] = $question['question_title'];
                 }
                 fputcsv($csv, $questionArray);
             }

             $filters = array();
             $members = User::find($filters, $start, $end,
                  'createdDate', 'desc', TRUE);

             foreach ($members['users'] as $user) {
                 // Would be more correct to use xg_username here, as it falls back to the profile fullName
                 // if the User fullName is not set. However, xg_username does a couple of queries per user
                 // (unless we preload the users with XG_Cache::profiles). Anyway, just use my->fullName
                 // for simplicity and performance.  [Jon Aquino 2007-10-30]
                 if ($user->my->fullName) { // [skip-Syntax7Test]
                     $fullname = $user->my->fullName; // [skip-Syntax7Test]
                 } else {
                     $fullname = $user->title;
                 }
                 $profileAddress = "http://" . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($user->contributorName);
                 $profile = XN_Profile::load($user->contributorName);
                 $country = XG_UserHelper::getCountry($profile);
                 $gender = mb_strtolower(XG_UserHelper::getGender($profile));
                 $genderDescription = ($gender === "m") ? xg_text('MALE') :
                                        (($gender === "f") ? xg_text('FEMALE') :
                                        $gender);
                 $answerArray = array($fullname, $profileAddress, $profile->email, $genderDescription, XG_UserHelper::getLocation($profile), $country ? xg_text('COUNTRY_' . $country) : '', XG_UserHelper::getAge($profile), XG_UserHelper::getBirthdate($profile));
                 foreach ($questions as $question) {
                     $attributeName = 'xg_profiles_answer_q' . $question['questionCounter'];
                     $answer = unserialize($user->my->$attributeName);
                     if (!$answer) {
                         $answer = $user->my->$attributeName;
                     }
                     if (is_array($answer)) {
                         $answer = implode(',',$answer);
                     }
                     $answerArray[] = $answer;
                 }
                 $changed++;
                 fputcsv($csv, $answerArray);
             }
             // Don't let $remaining get below 0
             $remaining = max(0, ($members['numUsers'] - $end));
             $this->contentChanged = $changed;
             $this->contentRemaining = $remaining;
             fclose($csv);

          } catch (Exception $e) {
              $this->errorMessage = $e->getMessage();
          }
      }

     /**
      * For sending a message to everyone in the site. This doesn't invoke anything
      * on target mozzles, just iterates through all the users and sends the message
      */
     public function action_broadcast() {
          $messageLimit = 40;
          try {
              if (! XG_SecurityHelper::userIsAdmin()) {
                  throw new Exception("Permission denied.");
              }
              if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                  throw new Exception("Only POST requests permitted");
              }
              if (! (isset($_POST['subject']) && mb_strlen(trim($_POST['subject'])))) {
                  throw new Exception("No subject specified");
              }
              if (! (isset($_POST['body']) && mb_strlen(trim($_POST['body'])))) {
                  throw new Exception("No subject specified");
              }
              if (! (isset($_POST['counter']) && mb_strlen($_POST['counter']) &&
              ctype_digit($_POST['counter']))) {
                  throw new Exception("No counter supplied");
              }

              $this->_widget->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
              /*  If we haven't yet created the site broadcast alias, create it
               *    now by iterating over the users in pages
               *  If counter > 1 we're iterating over users to build the alias
               */
              if (!XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME)
                      || $_POST['counter'] > 0) {
                  $set = XN_ProfileSet::loadOrCreate(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME);
                  $changed = 0;
                  $remaining = 0;
                  // What is the starting and ending offset of this page?
                  $start = $_POST['counter'] * $messageLimit;
                  $end = $start + $messageLimit;
                  // Get this page's worth of users
                  $userInfo = User::find(array(), $start, $end, null, null, true);
                  foreach ($userInfo['users'] as $user) {
                      //  If the user has site broadcast messages turned on, add
                      //    him to the alias
                      if (Index_NotificationHelper::canSendToUser(
                              Index_NotificationHelper::SITE_BROADCAST_TYPE, $user)) {
                          $set->addMembers($user->contributorName);
                      }
                      $changed++;
                  }
                  // Don't let $remaining get below 0
                  $remaining = max(0, ($userInfo['numUsers'] - $end));
                  $this->contentSent = $changed;
                  $this->contentRemaining = $remaining;
              } else {
                  //  No need to iterate - just send the message
                  $this->contentSent = 1;
                  $this->contentRemaining = 0;
              }

              // If we've finished building the alias, send the message
              if ($this->contentRemaining < 1) {
                  $set = XN_ProfileSet::load(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME);
                  // Build the message we're going to send
                  XG_App::includeFileOnce('/lib/XG_Message.php');
		          $subject = mb_substr(strip_tags(trim($_POST['subject'])), 0, 200);
        		  $body = mb_substr(strip_tags(trim($_POST['body'])), 0, 2000);
                  $msg = new XG_Message_Broadcast($subject, $body, XN_Profile::current());

                  //  Send the message to the notification alias
                  $msg->send(Index_NotificationHelper::SITE_BROADCAST_ALIAS_NAME . '@lists');
                  //  BAZ-4473 Send the message to whoever sent the broadcast message too (the default notification excludes the sender)
                  $msg->send(XN_Profile::current()->screenName, XG_Message::siteReturnAddress());
              }
          } catch (Exception $e) {
              $this->errorMessage = $e->getMessage();
          }
     }


    protected function bulkAction($controller, $action, $changeLimit, $args = array(), $widgetToUse = null) {
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        if (! is_null($widgetToUse)) {
            $enabledWidgets = array(W_Cache::getWidget($widgetToUse));
        } else {
            $enabledWidgets = XG_ModuleHelper::getEnabledModules();
            if ($enabledWidgets['groups']) {
                foreach (XG_GroupHelper::groupEnabledWidgetInstanceNames() as $groupEnabledWidgetInstanceName) {
                    $enabledWidgets[$groupEnabledWidgetInstanceName] = W_Cache::getWidget($groupEnabledWidgetInstanceName);
                }
            }
            uksort($enabledWidgets, array(self, 'sortWidgetsWithProfilesLast'));
        }
        $widgetCount = count($enabledWidgets);
        $i = 0;
        $contentChanged = 0;
        $contentRemaining = 0;
        $argsToPass = $args;
        array_unshift($argsToPass, null);
        reset($enabledWidgets);
        while (($i < $widgetCount) && ($contentChanged < $changeLimit)) {
            $widget = current($enabledWidgets);
            if (in_array($controller, $widget->getControllerNames()) && $widget->controllerHasAction($controller, $action)) {
                $argsToPass[0] = $changeLimit - $contentChanged;
                list($r, $html) = $widget->capture($controller,$action, $argsToPass);
                $contentChanged += $r['changed'];
                $contentRemaining += $r['remaining'];
            }
            $i++;
            next($enabledWidgets);
        }
        return array('changed' => $contentChanged, 'remaining' => $contentRemaining);
    }

    protected static function sortWidgetsWithProfilesLast($a,$b) {
        return ($a == 'profiles') ? 1 :
               (($b == 'profiles') ? -1 : strcasecmp($a,$b));
    }
}
