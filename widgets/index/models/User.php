<?php

class User extends W_Model {
   /**
    * Screenname
    *
    * @var XN_Attribute::STRING
    */
    public $title;

    /**
    * Lowercase screen name, for sorting
    *
    * @var XN_Attribute::STRING optional
    */
    public $lowercaseScreenName;
    // Note, however, that full names, not screen names, are displayed (BAZ-1286)  [Jon Aquino 2007-01-27]

    /**
    * Full name, for searching
    *
    * @var XN_Attribute::STRING optional
    * @feature indexing text
    */
    public $fullName;
    // 100 characters, to match XN_Profile->fullName max.
    // I didn't add "@rule length 1,100" as this restriction was not present
    // when the $fullName attribute was introduced  [Jon Aquino 2007-09-25]
    const MAX_FULL_NAME_LENGTH = 100;

    /**
     * Copied from system profile
     *
     * @var XN_Attribute::STRING optional
     */
    public $gender;

    /**
     * Copied from system profile
     * @rule length 1,100
     * @var XN_Attribute::STRING optional
     */
    public $location;
    const MAX_LOCATION_LENGTH = 100; // Matches XN_Profile->location max  [Jon Aquino 2007-09-25]

    /**
     * Copied from system profile
     *
     * @var XN_Attribute::STRING optional
     */
    public $country;

    /**
     * Copied from system profile
     *
     * @var XN_Attribute::STRING optional
     */
    public $description;

    /**
     * Copied from system profile
     *
     * @var XN_Attribute::STRING optional
     */
    public $birthdate;

    /**
     * Copied from system profile
     *
     * @var XN_Attribute::STRING optional
     */
    public $thumbnailUrl;

    /**
     * ID when the profile thumbnail is uploaded to the app
     *
     * @var XN_Attribute::STRING optional
     */
    public $thumbnailId;

    /**
     * ID of the previous thumbnail for the user if they had one.  Stored so that it can be deleted cleanly once the new
     * profile thumbnail has been added
     *
     * @var XN_Attribute::STRING optional
     */
    public $previousThumbnailId;

    /**
     * System attribute marking whether to make the content available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
    * The full name and the profile address, split to allow for partial text search of usernames. So for example 'jon' and 'foo'
    * would be stored as 'j jo jon f fo foo'. A 'like' search can then be done using the string
    * passed in the user search box.
    *
    * @var XN_Attribute::STRING optional
    * @feature indexing text
    */
    public $searchText;

    /**
     * Whether the attribute is a duplicate. For working around multiple-content-object-creation bug
     *
     * @var XN_Attribute::STRING optional
     */
    public $duplicate;

    /**
     * The mozzle that creates the User object
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

    /**
     * The level of notification on actions done to the content of the user.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailActivityPref;
    public $emailActivityPref_choices = array('activity', 'none');

    /**
     * In what way the user is notified if his/her content are approved/rejected.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailModeratedPref;
    public $emailModeratedPref_choices = array('each', 'none');

    /**
     * Does the user want to be notified when there is new content to moderate?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailApprovalPref;
    public $emailApprovalPref_choices = array('Y','N');

    /**
     * Does the user want to be notified when there are new comments to approve?
     *   (blog comments, profile comments (chatters))
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailCommentApprovalPref;
    public $emailCommentApprovalPref_choices = array('Y','N');

    /**
     * Does the user want to be notified when someone they invited joins?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailInviteeJoinPref;
    public $emailInviteeJoinPref_choices = array('Y','N');

    /**
     * Does the user want to be notified when they get a new friend request?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailFriendRequestPref;
    public $emailFriendRequestPref_choices = array('Y','N');


    /**
     * Does the user want to be notified when they have a new message?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailNewMessagePref;
    public $emailNewMessagePref_choices = array('Y','N');

    /**
     * Does the user want to be notified when someone sends a group invitation,
     * Share This, or message to All Friends?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailAllFriendsPref;
    public $emailAllFriendsPref_choices = array('Y','N');
    
    /**
     * Does the user want to recieve any email sent by an Application?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailViaApplicationsPref;
    public $emailViaApplicationsPref_choices = array('Y','N');
    
    /**
     * Does the user want to receive site broadcast messages? (new in 1.11)
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailSiteBroadcastPref;
    public $emailSiteBroadcastPref_choices = array('Y','N');


    /**
     * Does the user want to receive group broadcast messages? (new in 1.11)
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailGroupBroadcastPref;
    public $emailGroupBroadcastPref_choices = array('Y','N');

    /**
     * Does the user want to receive event broadcast messages? (new in 3.1)
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailEventBroadcastPref;
    public $emailEventBroadcastPref_choices = array('Y','N');


    /**
     * Is the user following replies to one or more content items?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $isFollowing;
    public $isFollowing_choices = array('Y','N');


    /**
     * Does the user want to automatically follow discussions when replying?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $autoFollowOnReplyPref;
    public $autoFollowOnReplyPref_choices = array('Y','N');

    /**
     * Does the user want to receive admin emails (feedback, report an issue).
     * Only takes effect for Network Creators and Admins.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailAdminMessagesPref;
    public $emailAdminMessagesPref_choices = array('Y', 'N');

    /**
     * Does the user want to suppress all messages from the app?
     * If this is set to 'Y', then all other email*Pref values are
     * ignored by XG_Message and friends
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $emailNeverPref;
    public $emailNeverPref_choices = array('Y','N');

    /**
     * Does the user wants to notify the dashboard when he creates new content?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityNewContent;
    public $activityNewContent_choices = array('Y','N');

    /**
     * Does the user wants to notify the dashboard when he adds comments or replies to a content?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityNewComment;
    public $activityNewComment_choices = array('Y','N');

    /**
     * Does the user wants to notify the dashboard when he joins a group?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityNewConnection;
    public $activityNewConnection_choices = array('Y','N');

    /**
     * Does the user want to notify the dashboard when he creates/updates events or changes rsvp?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityEvents;
    public $activityEvents_choices = array('Y','N');

    /**
     * Does the user want to notify the dashboard when they become friends with another user?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityFriendships;
    public $activityFriendships_choices = array('Y','N');

    /**
     * Does the user wants to notify the dashboard when he changes his profile?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $activityProfileUpdate;
    public $activityProfileUpdate_choices = array('Y','N');

    /**
     * The default visibility settings for content of this user.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $defaultVisibility;
    public $defaultVisibility_choices = array('all', 'friends', 'me');

    /**
     * Who is allowed to comment on the content of the user.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $addCommentPermission;
    public $addCommentPermission_choices = array('all', 'friends', 'me');

    /**
     * Who is allowed to see the events that the user has been invited to
     * or has RSVPed to.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $viewEventsPermission;
    public $viewEventsPermission_choices = array('all', 'friends', 'me');

    /**
     * Does the user want to ping weblog services when they write a blog post?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $blogPingPermission;
    public $blogPingPermission_choices = array('Y','N');

    /**
     * How many messages has this user sent while banned? (max 3)
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $numBannedMessagesSent;

    /**
     * Is the user a network administrator?
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $isAdmin;
    public $isAdmin_choices = array('Y','N');

    /**
     * How many chatters does the user currently have to approve?
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $chattersToApprove;

    /**
     * How many comments does the user currently have to approve?
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $commentsToApprove;

    /**
     * Which announcements has the user acknowledged?  (XG_Announcement.php)
     *
     * @var XN_Attribute::STRING optional
     */
    public $announcementsAcknowledged;

    /**
     * Last portion of the URL of the user's profile page,
     * e.g., SilverSurfer in http://networkname.ning.com/profile/SilverSurfer
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing phrase
     */
    public $profileAddress;
    const MAX_PROFILE_ADDRESS_LENGTH = 100;

    /**
     * Whether the attributes from the user's XN_Profile object have been copied
     * to this User object.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $syncdWithProfile;
    public $syncdWithProfile_choices = array('Y','N');

    /**
     * JSON array of Groups to which the user has been invited.
     * Each Group is represented by:
     *     id - content ID of the Group
     *     inviter - screen name of the user who sent the invitation
     *
     * @var XN_Attribute::STRING optional
     */
    public $groupsInvitedTo;

    /**
     * Integer value for use in sorting members by status.
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $memberStatus;

    /**
     * Whether to display the Welcome box to this user on the main page.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $showWelcomeBox;
    public $showWelcomeBox_choices = array('Y','N');

    /**
     * The screen name of the person who invited this user to the network.
     * Not set if the invitation was via the bulk-invitation URL.
     *
     * @var XN_Attribute::STRING optional
     * @since 3.2
     */
    public $inviter;

    /**
     * Whether to display the person's age
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @since 3.2
     */
    public $displayAge;
    public $displayAge_choices = array('Y','N');

    /**
     * Whether to display the person's gender
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @since 3.2
     */
    public $displayGender;
    public $displayGender_choices = array('Y','N');

    /**
     * Unix timestamp of the start of processing all sent friend requests.
     *
     * @var XN_Attribute::NUMBER optional
     * @see Profiles_SentFriendRequestUpdater
     * @since 3.6
     */
    public $sentFriendRequestUpdateStart;

    /**
     * Unix timestamp of the start of processing all received friend requests.
     *
     * @var XN_Attribute::NUMBER optional
     * @see Profiles_ReceivedFriendRequestUpdater
     * @since 3.6
     */
    public $receivedFriendRequestUpdateStart;

    /**
     * Serialized hash of metadata flags. Used to indicate when fixes have been applied,
     * e.g., array('baz-10144-fix-applied' => TRUE)
     *
     * @var XN_Attribute::STRING optional
     * @see User::setInternalFlag, User::isInternalFlagSet
     * @since 3.6
     */
    public $internalFlags;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Sets the given flag to TRUE. Flags are used to indicate when fixes have been applied.
     * Does not save the User object.
     *
     * @param $user XN_Content|W_Content  the User object to update
     * @param $name string  name of the flag, e.g., 'baz-10144-fix-applied'
     */
    public static function setInternalFlag($user, $name) {
        $internalFlags = $user->my->internalFlags ? unserialize($user->my->internalFlags) : array();
        $internalFlags[$name] = TRUE;
        $user->my->internalFlags = serialize($internalFlags);
    }

    /**
     * Returns whether the given flag has been set. Flags are used to indicate when fixes have been applied.
     *
     * @param $user XN_Content|W_Content  the User object
     * @param $name string  name of the flag, e.g., 'baz-10144-fix-applied'
     * @return boolean  whether the flag has been set.
     */
    public static function isInternalFlagSet($user, $name) {
        $internalFlags = $user->my->internalFlags ? unserialize($user->my->internalFlags) : array();
        return $internalFlags[$name] === TRUE;
    }

    /**
     * Cache of already-loaded User objects
     */
    protected static $screenNameToUserMap = array();

    /** Screen names for which User objects were not found. */
    protected static $screenNamesWithoutUserObjects = array();

    /**
     * Adds the screen name to the list of screen names that do not have
     * User objects.
     *
     * @param $screenName string  a screen name for which a User object does not exist.
     */
    public static function addScreenNameWithoutUserObject($screenName) {
        self::$screenNamesWithoutUserObjects[$screenName] = $screenName;
    }

    /**
     * Load or create a new User object for the provided screen name.
     * This function should not normally need to be called, as it
     * does not do everything needed to make someone a member.
     * See Index_MembershipHelper::onJoin.
     *
     * @param $profileOrScreenName string|XN_Profile
     * @return User
     */
    public static function loadOrCreate($profileOrScreenName) {
        return self::load($profileOrScreenName, true);
    }

    /**
     * Load or create a new User object for the provided screen name
     * based on argument
     *
     * @param $profileOrScreenName string|XN_Profile|User
     * @param $createIfNecessary boolean optional flag indicating whether to
     * create the User object if it doesn't exist
     * @return User
     */
    public static function load($profileOrScreenName, $createIfNecessary = false) {
        if (! $profileOrScreenName) {
            error_log('BAZ-9176 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
            xg_echo_and_throw('No profile or screen name provided');
        }
        if (is_string($profileOrScreenName)) {
            $screenName = $profileOrScreenName;
        }
        else if (is_numeric($profileOrScreenName)) {
            $screenName = $profileOrScreenName . "";
        }
        else if ($profileOrScreenName instanceof XN_Profile) {
            $screenName = $profileOrScreenName->screenName;
        }
        else if ((($profileOrScreenName instanceof XN_Content) || ($profileOrScreenName instanceof W_Content)) && ($profileOrScreenName->type == 'User')) {
            $screenName = $profileOrScreenName->title;
        }
        else {
            xg_echo_and_throw("Bad argument ($profileOrScreenName) provided for User::load()");
        }
        $user = self::$screenNameToUserMap[mb_strtolower($screenName)];
        if ($user) {
            // If the User object has not yet been saved and $createIfNecessary == false,
            // return null, because User::load() is often used to check if
            // a User object has been saved. [Jon Aquino 2007-05-23]
            return $user->id || $createIfNecessary ? $user : null;
        }
        $profile = XG_Cache::profiles($screenName);
        if (! $profile && ! $GLOBALS['UNIT_TEST_SKIP_PROFILE_CHECK_IN_USER_LOAD']) {
            return null;
        }
        if (self::$screenNamesWithoutUserObjects[$screenName] && ! $createIfNecessary) { return null; }
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create('Content');
            // Cache the retrieve user object until a User object changes
            $query->setCaching(XG_Cache::key('type','User'));
        } else {
            $query = XN_Query::create('Content');
        }
        $query->filter('owner');
        $query->filter('type', '=', 'User');
        $query->filter('title', 'eic', $screenName);
        $query->order('createdDate', 'asc', XN_Attribute::DATE);
        $results = $query->execute();
        if (count($results) > 0) {
            $user = W_Content::create($results[0]);
            // Workaround for VID-706  [Jon Aquino 2006-10-04]
            for ($i = 1; $i < count($results); $i++) {
                try {
                    XN_Content::delete($results[$i]);
                } catch (Exception $e) {
                    // Possibly deleted concurrently [Jon Aquino 2006-12-19]
                }
            }
        } elseif (! $createIfNecessary) {
            self::addScreenNameWithoutUserObject($screenName);
            return null;
        } else {
            if (! XN_Profile::current()->isLoggedIn()) {
                /* Some sugar to get the nice stack formatting from Exception::getTraceAsString() */
                try {
                    throw new XN_Exception("Anonymous attempt to create User object for '{$profile->screenName}'");
                } catch (Exception $e) {
                    NF::logException($e);
                }
            }

            // serialize this per-app to remove member count race condition (BAZ-6824) [ywh 2008-05-21]
            XG_App::includeFileOnce('/lib/XG_LockHelper.php');
            $lockKey = "newUser:lock";
            // not getting the lock is not a serious problem and should not prevent the creation from occurring
            $haveLock = XG_LockHelper::lock($lockKey);

            $user = W_Content::create('User');
            $user->setTitle($profile->screenName);
            $user->isPrivate  = true;
            $user->my->defaultVisibility = 'all';
            $user->my->addCommentPermission = 'all';
            $user->my->emailActivityPref = 'activity';
            $user->my->emailSiteBroadcastPref = 'Y';
            $user->my->emailGroupBroadcastPref = 'Y';
            $user->my->emailEventBroadcastPref = 'Y';
            $user->my->emailModeratedPref = 'each';
            $user->my->emailApprovalPref = 'Y';
            $user->my->emailInviteeJoinPref = 'Y';
            $user->my->emailFriendRequestPref = 'Y';
            $user->my->emailNewMessagePref = 'Y';
            $user->my->emailAllFriendsPref = 'Y';
            $user->my->emailViaApplicationsPref = 'Y';
            $user->my->emailAdminMessagesPref = 'Y';
            $user->my->emailNeverPref = 'N';
            $user->my->activityNewContent = 'Y';
            $user->my->activityNewComment = 'Y';
            $user->my->activityNewConnection = 'Y';
            $user->my->activityProfileUpdate = 'Y';
            $user->my->autoFollowOnReplyPref = 'N';
            $user->my->blogPingPermission = 'Y';
            $user->my->showWelcomeBox = 'Y';
            $user->my->mozzle = 'profiles';
            $user->my->displayAge = 'N';
            $user->my->displayGender = 'Y';
            User::setInternalFlag($user, 'baz-10144-fix-applied');
            // BAZ-8509: Mark all new users as unfinished. We mark them as completed inside Index_MembershipHelper::onJoin() call. [Andrey 2008-07-22]
            if (!$profile->isOwner()) {
                $user->my->xg_index_status = 'unfinished';
            }
            // @todo: this should be accomplished by dispatch out to all mozzles
            $blogModerationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateBlogComments');
            $user->my->{$blogModerationAttributeName} = 'N';
            $chatterModerationAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'moderateChatters');
            $user->my->{$chatterModerationAttributeName} = 'N';
            $memberNumberOffset = 0;
            try {
               // this will result in double-save but is needed to fix a race condition bug (BAZ-6824) [ywh 2008-05-21]
               $user->save();
            } catch (Exception $e) {
               // if unable to save, the code below will not include this user so we have to bump the count
               error_log('unable to save user: ' . $e->getMessage());
               $memberNumberOffset = 1;
            }

            // get member number; include pending users
            $userInfo = User::find(array(), 0, 1, null, null, false, false);
            $memberNumberAttributeName = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'memberNumber');
            $user->my->set($memberNumberAttributeName, $userInfo['numUsers'] + $memberNumberOffset, XN_Attribute::NUMBER);

            $blockEverything = 0;
            BlockedContactList::merge(XN_Profile::current()->email, XN_Profile::current()->screenName, $blockEverything);
            if ($blockEverything) {
                $user->my->emailNeverPref = 'Y';
            }

            // unlock if we have the lock
            if ($haveLock) { XG_LockHelper::unlock($lockKey); }
            XG_UserHelper::setThumbnailFromDefaultAvatarOrProfile($profile, $user);
            // Merge the existing black list with the screenName
        }
        self::$screenNameToUserMap[mb_strtolower($screenName)] = $user;
        if ($user->id && ! $user->my->fullName) { // [skip-Syntax7Test]
            $user->setFullName($profile->fullName); // [skip-Syntax7Test]
            $user->save();
        }
        self::upgradeUserObject($profile, $user);
        return $user;
    }

    /**
     * Perform any necessary upgrades to the User object.
     *
     * @param $profile XN_Profile  the system profile
     * @param $user XN_Content|W_Content  the User content object
     */
    private static  function upgradeUserObject($profile, $user) {
        static $checkedScreenNames = array();
        if ($checkedScreenNames[$user->title]) { return; }
        $checkedScreenNames[$user->title] = $user->title;

        // BAZ-4567: sync current user's profile if necessary
        if ($user->id && XN_Profile::current()->isLoggedIn() && ($user->title == XN_Profile::current()->screenName)) {
            User::syncWithProfile($user, XN_Profile::current());
        }
        // BAZ-4982: fix improperly syncd avatar urls  [Jon Aquino 2007-10-15]
        if ($user->id && ! $user->my->thumbnailId && XG_Cache::lock('avatar-sync-' . $user->title)) {
            XG_UserHelper::setThumbnailFromDefaultAvatarOrProfile($profile, $user);
            $user->save();
        }
    }

    /**
     * Returns a User object if it has already been loaded. Offers better performance than load(),
     * but is useful only for cases that use the User object opportunistically, i.e., if we don't have the User object,
     * it's not a big deal.
     */
    public static function retrieveIfLoaded($screenName) {
        return self::$screenNameToUserMap[mb_strtolower($screenName)];
    }

    /**
     * Loads a User object whether or not it's already been saved. This is an
     * alternative to the behavior in User::load() that returns null if a User
     * object exists in the $screenNameToUserMap but has not yet been saved
     */
    public static function loadOrRetrieveIfLoaded($profileOrScreenName) {
        $screenName = $profileOrScreenName instanceof XN_Profile ? $profileOrScreenName->screenName : $profileOrScreenName;
        if (! $screenName && defined('UNIT_TESTING')) { return null; }
        $user = User::load($screenName);
        if (! $user) {
            $user = self::retrieveIfLoaded($screenName);
        }
        return $user;
    }

    /**
     * Query for users. Blocked and pending users are filtered out by default.
     *
     * @param $filters array An array of filters keyed by attribute name k. Each array element is either:
     *              'v' to filter on k = v
     *              array('op','v') to filter on k op v
     *              array('op','v','type') to filter on k op v type
     * @param $begin integer optional result set start. Defaults to 0
     * @param $end integer   optional result set end.   Defaults to 10
     * @param $order mixed  optional field to order on. Defaults to null.
     *         null: no order specified
     *         string: the property to sort on
     *         array(name, type): sort on the named property with the given type, e.g., XN_Attribute::NUMBER
     * @param $dir string    optional ordering direction Defaults to null if $order is not specified, asc if order is specified
     * @param $caching mixed optional caching control information:
     *                       true: cache, use default max age and no additional invalidation keys
     *                       integer: cache, use provided integer as max age and no invalidation keys
     *                       array: cache, use optional 'maxAge' key as max age
     *                                     use optional 'keys' key as invalidation keys
     * @param $filterPendingUsers boolean optional switch for filtering pending users; defaults to true
     * @return array A two element array: 'users' => the requested users
     *                                    'numUsers' => the total number of users that match
     */
    public static function find($filters, $begin = 0, $end = 10, $order = null, $dir = null, $caching = null, $filterPendingUsers = true) {
        // TODO: Why do we have two ways to filter pending users: $filters and $filterPendingUsers?
        // Maybe fix the former and remove the latter? [Jon Aquino 2008-08-07]
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        $query = XN_Query::create('Content')
                    ->filter('owner')
                    ->filter('type','eic','User');

        $filterBlockedUsers = TRUE;
        if (isset($filters['promoted'])) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            if (! XG_PromotionHelper::areQueriesEnabled()) { return array('users' => array(), 'numUsers' => 0); }
            XG_PromotionHelper::addPromotedFilterToQuery($query);
            unset($filters['promoted']);
        }
        if (isset($filters['blocked'])) {
            $query->filter('my->xg_index_status', '=', 'blocked');
            $filterBlockedUsers = FALSE;
            unset($filters['blocked']);
        }
        if (isset($filters['admin'])) {
            $query->filter(XN_Filter::any(XN_Filter('my->isAdmin', '=', 'Y'), XN_Filter('contributorName', '=', XN_Application::load()->ownerName)));
            unset($filters['admin']);
        }
        if (isset($filters['pending'])) {
            $query->filter('my->xg_index_status', '=', 'pending');
            $filterPendingUsers = FALSE;
            unset($filters['pending']);
        }

        /* If there are arbitrary filters specified and we shouldn't be
         * caching "order N" queries (@see BAZ-2969), then turn caching off */
        if ((count($filters) > 0) && (! XG_Cache::cacheOrderN())) {
            $caching = false;
        }
        $query = XG_QueryHelper::applyFilters($query, $filters);
        if (! is_null($order)) {
            //TODO: implement sort descriptors like in photos/videos [ywh 2008-05-13]
            if ($order == 'random()') {
                $query->order($order);
            } else {
                $dir = is_null($dir) ? 'asc' : $dir;
                is_array($order) ? $query->order($order[0], $dir, $order[1]) : $query->order($order, $dir);
            }
        }
        $query->begin($begin);
        $query->end($end);
        $query->alwaysReturnTotalCount(true);
        if ($filterBlockedUsers) {
            self::addBlockedFilter($query);
        }
        if ($filterPendingUsers) {
            self::addPendingFilter($query);
        }
        if ( $filterBlockedUsers || $filterPendingUsers ) {
            // we include unfinished users only when we include pending AND blocked users. Otherwise we ignore them.
            self::addUnfinishedFilter($query);
        }

        /* If caching is desired, use it */
        if (! (is_null($caching) || ($caching === false))) {
            if ($caching === true) {
                $query = XG_Query::create($query);
                $query->addCaching(XG_Cache::key('type','User'));
            }
            else if (is_integer($caching)) {
                $query = XG_Query::create($query);
                $query->maxAge($caching);
            }
            else if (is_array($caching)) {
                $query = XG_Query::create($query);
                if (isset($caching['maxAge'])) {
                    $query->maxAge($caching['maxAge']);
                }
                if (isset($caching['keys'])) {
                    $query->setCaching($caching['keys']);
                }
            }
        }

        $users    = $query->execute();
        $numUsers = $query->getTotalCount();
        /* BAZ-4567: sync some user profiles if necessary */
        XG_UserHelper::addToSyncMap($users);
        self::insertIntoUserMap($users);
        return array('users' => $users, 'numUsers' => $numUsers);
    }

    public function setTitle($title) {
        $this->title = $title;
        $this->lowercaseScreenName = mb_strtolower($title);
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
        $this->updateSearchText();
    }

    private function updateSearchText() {
        $this->searchText = self::searchText($this->fullName, $this->profileAddress);
    }

    /**
     * The full name and the profile address, split to allow for partial text search of usernames
     *
     * @param $fullName string  the name of the user, e.g., jon
     * @param $profileAddress string  the last portion of the user's profile URL, e.g., foo
     * @return  split strings, e.g., j jo jon f fo foo
     */
    private static function searchText($fullName, $profileAddress) {
        $searchText = '';
        for ($i = 0; $i < mb_strlen($fullName); $i++) {
            $searchText = $searchText . ' ' . mb_substr($fullName, 0, $i+1);
        }
        for ($i = 0; $i < mb_strlen($profileAddress); $i++) {
            $searchText = $searchText . ' ' . mb_substr($profileAddress, 0, $i+1);
        }
        return $searchText;
    }

    /**
     * Sets the "profile address" - the last portion of the URL of the user's profile page.
     * Does not save the User object.
     *
     * @param XN_Content|W_Content  the User object to modify
     * @param $profileAddress string  the profile address, e.g., SilverSurfer in http://networkname.ning.com/profile/SilverSurfer
     */
    public static function setProfileAddress($user, $profileAddress) {
        $user->my->profileAddress = $profileAddress;
        $user->my->searchText = self::searchText($user->my->fullName, $user->my->profileAddress); // [skip-Syntax7Test]
    }

    /**
     * Determine whether a particular user is a member of the site -- they
     * have a User object that has not been marked as 'blocked'
     *
     * @param $p string|XN_Profile|User Either a string screen name, an XN_Profile
     * object, or a User object
     * @return boolean
     * @todo Refactor this to check the type of $p just once
     */
    public static function isMember($p) {
        /* An anonymous user can't be a member */
        if ((is_string($p) && (mb_strlen($p) == 0)) || (($p instanceof XN_Profile) && (mb_strlen($p->screenName) == 0))) {
            return false;
        }
        /* If $p is already a User object, just check if it's banned or the app owner */
        if ((($p instanceof XN_Content)||($p instanceof W_Content)) && ($p->type == 'User')) {
            if (($p->contributorName == XN_Application::load()->ownerName) || ((! User::isBanned($p)) && (! User::isPending($p)))) {
                return true;
            }
        }
        /* The app owner is always a member */
        if ($p instanceof XN_Profile) {
            if (XG_SecurityHelper::userIsOwner($p)) { return true; }
        } elseif (is_string($p)) {
            if (XN_Application::load()->ownerName === $p) { return true; }
        }

        try {
            /* Try to find a user object for the specified user that isn't banned */
            $screenName = ($p instanceof XN_Profile) ? $p->screenName : $p;
            $user = User::loadOrRetrieveIfLoaded($screenName);
            return ($user && (! User::isBanned($user)) && (! User::isPending($user)));
        } catch (Exception $e) {
            error_log('BAZ-9176 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
            error_log("User::isMember(): can't load User object for $screenName: " . $e->getMessage());
            error_log($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Is this user banned?
     *
     * @param $u User|XN_Profile|string
     * @return boolean
     */
     public static function isBanned($u) {
         /* $u might not be a User object */
         if (is_string($u)) {
             $u = User::loadOrRetrieveIfLoaded($u);
         }
         else if ($u instanceof XN_Profile) {
             $u = User::loadOrRetrieveIfLoaded($u->screenName);
         }
         return $u && ($u->my->xg_index_status == 'blocked');
     }

    /**
     * Is this user pending?
     *
     * @param $u User|XN_Profile|string
     * @return boolean
     */
     public static function isPending($u) {
         /* $u might not be a User object */
         if (is_string($u)) {
             $u = User::loadOrRetrieveIfLoaded($u);
         }
         else if ($u instanceof XN_Profile) {
             if (! mb_strlen($u->screenName)) { return false; }
             $u = User::loadOrRetrieveIfLoaded($u->screenName);
         }
         return $u && ($u->my->xg_index_status == 'pending');
     }

    /**
     * Set the user's status
     *
     * @param $user User The user object
     * @param $status string The new status
     */
     public static function setStatus($user, $status) {
         //  Never block the owner!
         if ((($status == 'blocked') || ($status == 'pending')) && $user->contributorName == XN_Application::load()->ownerName) {
             return;
         }
         $oldStatus = $user->my->xg_index_status;
         $user->my->xg_index_status = $status;
         XN_Event::fire('user/status/changed', array($user, $oldStatus));
     }

    /**
     *  Can the user send a message from the banned user page?  (true unless
     *    he's reached the lifetime limit)
     */
    public static function canSendBannedMessage($user) {
        if ($user->my->numBannedMessagesSent) {
            return $user->my->numBannedMessagesSent < 3;
        }
        else {
            return TRUE;
        }
    }

    /**
     *  Call when a user sends a message when banned - increment counter
     */
    public static function sentBannedMessage($user) {
        if ($user->my->numBannedMessagesSent) {
            $user->my->numBannedMessagesSent += 1;
        }
        else {
            $user->my->numBannedMessagesSent = 1;
        }
        $user->save();
    }

    /**
     *  Call when a user is un-banned
     */
    public static function clearBannedMessageCounter($user) {
        $user->my->numBannedMessagesSent = 0;
        $user->save();
    }

    /**
     * Returns whether the user is an admin. Note that this returns false for
     * the network creator. Moreover, it may return true for banned users.
     * It is better to use XG_SecurityHelper::userIsAdmin(),
     * which returns true if the user is an admin (including the network creator).
     */
    public static function isAdmin($user) {
        return $user->my->isAdmin == 'Y';
    }

    /**
     * Set the specified user's admin status to 'Y' (admin) if
     *   value is true, else 'N' (not admin).  Updates memberStatus field
     *   accordingly.
     *
     * @param $value boolean Should this user be admin?  default TRUE
     */
    public static function setAdminStatus($user, $value = TRUE) {
        // Network Creator is not affected by admin setting.
        if ($user->title == XN_Application::load()->ownerName) { return; }
        XG_App::includeFileOnce('/lib/XG_MembershipHelper.php');
        $user->my->isAdmin = ($value ? 'Y' : 'N');
        $user->my->memberStatus = ($value ? XG_MembershipHelper::ADMINISTRATOR : null);
    }

    public static function addBlockedFilter($query, $isSearch = false) {
        if ($isSearch) {
           $query->filter('my->xg_index_status', '!like', 'blocked');
        } else {
           $query->filter('my->xg_index_status', '<>', 'blocked');
        }
    }

    public static function addPendingFilter($query, $isSearch = false) {
        if ($isSearch) {
           $query->filter('my->xg_index_status', '!like', 'pending');
        } else {
           $query->filter('my->xg_index_status', '<>', 'pending');
        }
    }

    public static function addUnfinishedFilter($query, $isSearch = false) {
        if ($isSearch) {
           $query->filter('my->xg_index_status', '!like', 'unfinished');
        } else {
           $query->filter('my->xg_index_status', '<>', 'unfinished');
        }
    }

    /**
     * Retrieves the given widget-specific attribute on the given User object,
     * for the current widget.
     *
     * @param $user XN_Content|W_Content  The User object
     * @param $name string  Name of the attribute
     * @return string|integer  Value of the attribute
     */
    public static function getWidgetAttribute($user, $name) {
        return $user->my->raw(self::widgetAttributeName($name));
    }

    /**
     * Sets a widget-specific attribute on the given User object (or GroupMembership object, in a group context),
     * for the current widget.
     *
     * @param $user XN_Content|W_Content  The User object
     * @param $name string  Name of the attribute
     * @param $value string|integer Value of the attribute
     * @param $type XN_Attribute::STRING (default), XN_Attribute::NUMBER, or XN_Attribute::DATE
     * @return XN_Content|W_Content  The User object or GroupMembership object that was modified
     */
    public static function setWidgetAttribute($user, $name, $value, $type = XN_Attribute::STRING) {
        // @todo pull the group logic out of this method, as it is confusing that sometimes the User is modified
        // and other times the GroupMembership is modified [Jon Aquino 2007-05-23]
        $object = XG_GroupHelper::inGroupContext() ? GroupMembership::loadOrCreate(XG_GroupHelper::currentGroup(), $user->title) : $user;
        $object->my->set(self::widgetAttributeName($name), $value, $type);
        return $object;
    }

    /**
     * Returns an appropriately prefixed attribute name for User objects,
     * for the current widget.
     * @param $name string  The unprefixed attribute name
     * @return string  The prefixed attribute name
     */
    public static function widgetAttributeName($attributeName) {
        if (in_array($attributeName, array('defaultVisibility', 'addCommentPermission', 'emailActivityPref', 'emailModeratedPref'))) {
            // Get here from old Photos and Videos code  [Jon Aquino 2007-01-27]
            return $attributeName;
        }
        return XG_App::widgetAttributeName(W_Cache::current('W_Widget'), $attributeName);
    }

    /**
     *  Checks the validation endpoint to see if the supplied email address
     *    has been registered by a Ning user.
     *
     * @deprecated 2.0  Use XG_Cache::profiles() instead
     */
    public static function emailIsRegistered($email) {
        return XG_Cache::profiles($email);
    }

    /**
     * Returns the Users with the highest activityCount values for the current widget.
     *
     * @param n The number of users to return
     * @param $numActiveUsers output for the total number of active users found
     * @return The users with the most active user first
     */
    public static function getMostActiveUsersForCurrentWidget($n = 7, &$numActiveUsers = null) {
        if ($_GET['test_user_count'] === '0') { return array(); }
        // Note that this time we only want users that were active at all
        $query = XN_Query::create('Content')
                       ->filter('type', '=', 'User')
                       ->filter('owner')
                       ->filter('my->duplicate', '<>', 'Y')
                       ->order('my->' . self::widgetAttributeName('activityCount'), 'desc', XN_Attribute::NUMBER)
                       ->end($n)
                       ->alwaysReturnTotalCount(true);
        if (defined('UNIT_TESTING')) { $query->filter('my->test', '=', 'Y'); }
        self::addActivityFilter($query);
        self::addBlockedFilter($query);
        self::addPendingFilter($query);
        self::addUnfinishedFilter($query);

        // Add type-based caching to query
        $query = XG_Query::create($query);
        $query->setCaching(XG_Cache::key('type','User'));

        $mostActiveUsers = $query->execute();
        $numActiveUsers = $query->getTotalCount();
        if (isset($_GET['test_user_count'])) {
            $firstUser = $mostActiveUsers[0];
            $numActiveUsers = $_GET['test_user_count'];
            $mostActiveUsers = array();
            for ($i = 0; $i < min($n, $numActiveUsers); $i++) {
                $mostActiveUsers[] = $firstUser;
            }
        }
        return $mostActiveUsers;
    }

    /**
     * Filters out people who have not yet contributed content to the current widget.
     * Assumes that User objects have an activityCount attribute tracking the amount of
     * content that the person has added to the current widget.
     *
     * @param $query XN_Query  The query to add the filter to
     * @param $canFilterOutAppOwner boolean  Whether to filter out the app owner if she hasn't contributed content
     * @see VID-805 "A person shouldn't appear on the app until they contribute their first piece of content"
     */
    private static function addActivityFilter($query, $canFilterOutAppOwner = true) {
        if ($canFilterOutAppOwner) {
            $query->filter('my->' . self::widgetAttributeName('activityCount'), '>', 0, XN_Attribute::NUMBER);
        } else {
            $query->filter(XN_Filter::any(XN_Filter('my->' . self::widgetAttributeName('activityCount'), '>', 0, XN_Attribute::NUMBER), XN_Filter('title','=',XN_Application::load()->ownerName)));
        }
    }

    /**
     * Called before a content object has been deleted.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function beforeDelete($object) {
        if (is_array($object)) {
            foreach ($object as $x) { self::beforeDelete($x); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if ($object->type != 'User') { return; }
        unset(self::$screenNameToUserMap[mb_strtolower($object->title)]);
    }

    /**
     * The User object gets special search configuration so that the profile question
     * answers can be included in what's searchable. These attribute names are dynamic
     *
     * @return array The attribute name => indexing status map for this kind of content
     */
    public static function searchConfiguration() {
        XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
        /* First, start off with anything marked with @feature indexing */
        $indexing = XG_ShapeHelper::indexingFromAnnotationForModel('User');
        /* Make sure the defaults are included */
        $indexing = array_merge(XG_ShapeHelper::defaultIndexing(), $indexing);
        /* Include the user status so we can filter on blocked/banned users (BAZ-4024) */
        $indexing['my.xg_index_status'] = 'phrase';
        /* Add all the profile question attribute names */
        $profilesWidget = W_Cache::getWidget('profiles');
        $profilesWidget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $questions = Profiles_ProfileQuestionHelper::getQuestions($profilesWidget);
        foreach ($questions as $position => $question) {
            $attributeName = 'my.' . Profiles_ProfileQuestionHelper::attributeNameForQuestion($question, $profilesWidget);
            /* Ignore private questions */
            if (isset($question['private']) && ($question['private'])) {
                $indexing[$attributeName] = 'ignored';
            } else {
                $indexing[$attributeName] = 'text';
            }
        }
        return $indexing;
    }

    /**
     * Returns the User object with the given profileAddress.
     *
     * @param $profileAddress string  the profileAddress to search for
     * @return the User object, or null if none was found
     */
    public static function loadByProfileAddress($profileAddress) {
        if (! $profileAddress) { return null; }
        $user = self::loadByProfileAddressProper($profileAddress);
        if (! $user) { $user = User::load($profileAddress); } // Fallback to screenName (BAZ-4562)
        return $user;
    }

    /**
     * Returns the User object with the given profileAddress.
     *
     * @param $profileAddress string  the profileAddress to search for
     * @return the User object, or null if none was found
     */
    private static function loadByProfileAddressProper($profileAddress) {
        if (! $profileAddress) { return null; }
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create('Content');
            // These queries also need to be invalidated when User objects change (BAZ-4618)
            $query->addCaching(XG_Cache::key('type','User'));
        } else {
            $query = XN_Query::create('Content');
        }
        $query->filter('owner');
        $query->filter('type', '=', 'User');
        $query->filter('my->profileAddress', 'eic', mb_strtolower($profileAddress)); // mb_strtolower to minimize cache variations [Jon Aquino 2007-09-25]
        $results = $query->execute();
        $user = $results[0];
        if ($user) { self::$screenNameToUserMap[mb_strtolower($user->title)] = $user; }
        return $user;
    }

    /**
     * Returns the last portion of the URL to the person's profile page.
     *
     * @param $screenName string  the person's username
     * @return string  the profile address, or null if the user cannot be found
     */
    public static function profileAddress($screenName) {
        $user = User::load($screenName);
        return ($user && mb_strlen($user->my->profileAddress)) ? $user->my->profileAddress : $screenName;
    }

    /**
     * Returns the real profile URL
     *
     * @param $screenName  the person's username
     * @return string  a relative URL
     * @see quickProfileUrl
     */
    public static function profileUrl($screenName) {
        return '/profile/' . self::profileAddress($screenName);
    }

    /**
     * Returns a profile URL that is quick to compute.
     *
     * @param $screenName  the person's username
     * @return string  a relative URL that requires no queries to construct
     * @see profileUrl
     */
    public static function quickProfileUrl($screenName) {
        return XG_Browser::current()->rewriteUrl('/xn/detail/u_' . $screenName, true);
    }

    /**
     * Returns a friends URL that is quick to compute. The URL
     * goes to a page displaying the person's friends.
     *
     * @param $screenName  the person's username
     * @return string  a relative URL that requires no queries to construct
     */
    public static function quickFriendsUrl($screenName) {
        return XG_Browser::current()->rewriteUrl('/xn/detail/f_' . $screenName, true);
    }

    /**
     * Returns a suitable profile address that doesn't yet exist on the network.
     *
     * @param 	$user 	User  	User to generate a profile address for.
     * @return 	string  		The new profile address (locked for exclusive use by the calling routine for the next 30 seconds).
     */
    public static function generateProfileAddress($user) {
        $addr = XG_LangHelper::urlFriendlyStr(XG_UserHelper::getFullName(XG_Cache::profiles($user->title)));
        $addr = str_replace('-', '', $addr); // Don't need multibyte here are urlFriendlyStr returns ASCII.
        $addr = ($addr === '' ? $user->title : $addr);
        for ($i = 0; $i < 10; $i++) {
            $profileAddress = $addr . ($i ? mt_rand(2, 99) : '');
            if ($user->lockProfileAddress($profileAddress)) { return $profileAddress; }
        }
        $lastResort = $addr . mt_rand(); //TODO or use a UUID to reduce chance of collision yet further.
        if ($user->lockProfileAddress($lastResort)) {
            return $lastResort;
        } else {
            throw new Exception("Could not generate profile address, even $lastResort failed to lock.");
        }
    }

    /**
     * Attempts to lock the specified profile address for exclusive use by the caller for 2 minutes
     * and returns success/failure.  If this routine returns false the caller must NOT save the User
     * with the specified profile address.  This routine will return false for all addresses
     * currently locked or already taken, excluding the address of the current user.
     *
     * @param	$addr		String	URL-friendly address to attempt to lock.
     * @return 				boolean	true if address available and locked, false otherwise.
     */
    public function lockProfileAddress($addr) {
        $lockObtained = XG_Cache::lock('profile-address-' . md5(mb_strtolower($addr)));
        // loadByProfileAddress searches screenNames as a fallback (BAZ-4562) [Jon Aquino 2007-10-03]
        $matchingUser = User::loadByProfileAddress($addr);
        if ($matchingUser && $matchingUser->title === $this->title) { return true; }
        return ! $matchingUser && $lockObtained;
    }

    /** Number of users to load in each query, in loadMultiple(). */
    const LOAD_MULTIPLE_CHUNK_SIZE = 50;
    // Load Users in chunks of 50, to avoid "Data too big for cache ID - max allowed size is 1048576 bytes"
    // errors, e.g., on firefighternation.com [Jon Aquino 2007-10-07]

     /**
     * Recursively converts the given mixed-type arguments (objects and screen names) into User objects.
     * If content objects are given, their contributorNames are used.
     * Arrays are searched recursively. Empty strings and nulls are ignored.
     *
     * Typically used in action methods to prime the cache using several objects.
     *
     * @param $a, $b, $c, ...  XN_Content objects, XN_Profile objects, screenNames, and arrays of the aforementioned.
     * @return  An array of screenName => XN_Content User object, or if only one item was passed in, a single XN_Content User (or null if no profile was found).
     */
    public static function loadMultiple() {
        $args = func_get_args();
        $users = array();
        $nonUserArgs = array();
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        foreach (XG_LangHelper::arrayFlatten($args) as $arg) {
            if (is_null($arg) || $arg === '') { continue; }
            if (($arg instanceof XN_Content || $arg instanceof W_Content) && $arg->type == 'User') {
                $users[$arg->title] = $arg;
                self::$screenNameToUserMap[mb_strtolower($arg->title)] = $arg;
            } elseif ($user = self::$screenNameToUserMap[mb_strtolower(self::screenName($arg))]) {
                if ($user instanceof W_Content) {
                    $user = W_Content::unwrap($user);
                }
                $users[$user->title] = $user;
            } elseif (self::$screenNamesWithoutUserObjects[self::screenName($arg)]) {
                // Skip it [Jon Aquino 2008-01-02]
            } else {
                $nonUserArgs[] = $arg;
            }
        }
        foreach (array_chunk(self::screenNames($nonUserArgs), XG_App::constant('User::LOAD_MULTIPLE_CHUNK_SIZE')) as $screenNamesChunk) {
            foreach (self::loadMultipleProper($screenNamesChunk) as $user) {
                $users[$user->title] = $user;
                self::$screenNameToUserMap[mb_strtolower($user->title)] = $user;
                // Index_InvitationController::action_recipientTableBody assumes that Users are stored
                // in $screenNameToUserMap by screen name rather than by email address [Jon Aquino 2007-10-24]
            }
            foreach ($screenNamesChunk as $screenName) {
                if (! $users[$screenName]) { self::addScreenNameWithoutUserObject($screenName); }
            }
        }
        if (count($args) == 1 && ! is_array(reset($args))) {
            return count($users) ? reset($users) : null;
        }
        return $users;
    }

    /**
     * Returns the User objects corresponding to the given screen names.
     *
     * @param $screenNames array  Ning usernames
     * @return  An array of XN_Content User objects
     */
    private static function loadMultipleProper($screenNames) {
        if (count($screenNames) == 0) { return array(); }
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'User');
        $query->filter('title', 'in', $screenNames);
        /* We should probably cache this */
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->setCaching(XG_Cache::key('type','User'));
        }
        return $query->execute();
    }

    /**
     * Insert a user object that's been loaded elsewhere into the map
     *
     * @param User|array A user object or array of user objects
     */
     public static function insertIntoUserMap($users) {
         if (is_array($users)) {
             foreach ($users as $user) {
             self::$screenNameToUserMap[mb_strtolower($user->title)] = $user;
             }
         }
         else {
             self::$screenNameToUserMap[mb_strtolower($users->title)] = $users;
         }
     }

    /**
     * Recursively converts the given objects into screen names.
     * Content objects are converted to their contributorNames.
     * Arrays are searched recursively. Empty strings and nulls are ignored.
     *
     * @param $items array XN_Content objects, XN_Contact objects, XN_Profile objects, screenNames, and arrays of the aforementioned.
     * @return array  screenName => screenName
     */
    public static function screenNames($items) {
        $screenNames = array();
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        foreach (XG_LangHelper::arrayFlatten($items) as $item) {
            if (is_null($item) || $item === '') { continue; }
            $screenName = self::screenName($item);
            $screenNames[$screenName] = $screenName;
        }
        return array_unique($screenNames);
    }

    /**
     * Converts the given object into a screen name.
     * Content objects are converted to their contributorNames.
     *
     * @param $item array XN_Content|W_Content|XN_Content|XN_Contact|string  the object to convert
     * @return array  The screen name
     */
    private static function screenName($item) {
        if (!is_object($item)) {
            return $item;
        } elseif ($item instanceof XN_Profile || $item instanceof XN_Contact) {
            return $item->screenName;
        } else {
            return $item->contributorName;
        }
    }

   /**
     * Sync a user object with data from the system profile, if it hasn't
     * already been synchronized. This should generally be used on the
     * User object for the currently logged in user.
     *
     * @param User
     * @return boolean true if the user object was syncd, false otherwise
     *
     */
    public static function syncWithProfile($user, $profile) {
        // The code in this function is similar to Index_ProfileInfoFormHelper::write [Jon Aquino 2007-10-02]
        /* Don't bother if it's already been synchronized */
        if ($user->my->syncdWithProfile) { return false; }
        if (! $profile) { return false; }
        if (! XG_Cache::lock('profile-sync-' . $profile->screenName)) { return false; };
        if ($user instanceof XN_Content) { $user = W_Content::create($user); }
        if (! $user->my->thumbnailId) { XG_UserHelper::setThumbnailFromDefaultAvatarOrProfile($profile, $user); }
        $gender = array('m' => 'm', 'f' => 'f'); // Map 'x' to null [Jon Aquino 2007-09-13]
        foreach (array('gender','birthdate','location','country') as $field) {
            if (mb_strlen($profile->$field)) {
                $user->my->$field = $profile->$field;
            }
        }
        // Only update full name if it's not set on the user object
        if (! mb_strlen($user->my->fullName)) {        // [skip-Syntax7Test]
            if (mb_strlen($profile->fullName)) {       // [skip-Syntax7Test]
                $user->setFullName($profile->fullName); // [skip-Syntax7Test]
            }
            else {
                $user->setFullName($profile->screenName);
            }
        }
        $user->my->syncdWithProfile = 'Y';
        $user->save();
        return true;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('xn/content/delete/before', array('User', 'beforeDelete'));
