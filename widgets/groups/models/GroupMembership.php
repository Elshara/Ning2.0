<?php
/**
 * An association between a Group and a User.
 * If Joe joins 3 groups, and Sarah adds 4, there will be
 * 7 GroupMemberships.
 *
 * GroupMemberships are used in the "My Groups" query on the X's Groups page.
 * It lists groups that you've joined.
 */
class GroupMembership extends W_Model {

    /**
     * Is this object public or private?
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

    /**
     * Date on which the user joined the group
     *
     * @var XN_Attribute::DATE optional
     */
    public $dateJoined;

    /**
     * Date on which the user was banned from the group
     *
     * @var XN_Attribute::DATE optional
     */
    public $dateBanned;

    /**
     * ID of the Group object
     *
     * @var XN_Attribute::STRING
     */
    public $groupId;

    /**
     * Screen name of the user.
     *
     * @var XN_Attribute::STRING optional
     */
    public $username;

    /**
     * Full name of the user.
     *
     * @var XN_Attribute::STRING optional
     */
    public $fullName;

    /**
     * Status of the person in relation to the group.
     * "nonmember" is equivalent to the GroupMembership object not existing.
     * "member" is more precisely "non-admin member".
     * "nonmember" is more precisely "non-banned non-member".
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $status;
    public $status_choices = array('nonmember', 'member', 'admin', 'banned');

    /**
     * Indicator of how active the user has been in the group.
     *
     * @var XN_Attribute::NUMBER
     */
    public $activityCount;

    /**
     * Whether the user has been welcomed to the group
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $welcomed;
    public $welcomed_choices = array('Y', 'N');

    /**
     * Whether the group is public or private
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $groupPrivacy;
    public $groupPrivacy_choices = array('public', 'private');

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Sets the status of the person in relation to the group.
     * The GroupMembership object does not need to be saved afterwards.
     *
     * @param $groupMembership XN_Content|W_Content  The GroupMembership object
     * @param $status string  The status: nonmember, member, admin, banned
     * @return W_Content  The GroupMembership relating the group to the user
     */
    public static function setStatus($groupMembership, $status) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        if ($groupMembership->my->status == $status) { return $groupMembership; }
        $updateCounts = false;
        $wasMember = self::isMember($groupMembership);
        $groupMembership->my->status = $status;
        $group = Group::load($groupMembership->my->groupId);
        if (! $wasMember && self::isMember($groupMembership)) {
            $updateCounts = true;
            $shouldLog = true;
            if ($group->contributorName == $groupMembership->my->username) $shouldLog = false;
            self::addGroupId($group->id, User::loadOrRetrieveIfLoaded($groupMembership->my->username), $shouldLog);
            Groups_InvitationHelper::setGroupInvitationStatus($group, XG_Cache::profiles($groupMembership->my->username)->email, 'accepted');
            $groupMembership->my->set('dateJoined', date('c'), XN_Attribute::DATE);
            if (! Group::userIsCreator($group, $groupMembership->my->username) && ! defined('UNIT_TESTING')) {
                XG_App::includeFileOnce('/lib/XG_Message.php');
                try {
                    XG_Message_Notification::create(XG_Message_Notification::EVENT_GROUP_WELCOME, array('profile' => XN_Profile::current(), 'group' => $group))->send(XN_Profile::current()->screenName);
                } catch (Exception $e) {
                    // Ignore exception (BAZ-1829) [Jon Aquino 2007-03-07]
                }
            }
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_BroadcastHelper.php');
            $groupBroadcastProfileSet = XN_ProfileSet::load(Groups_BroadcastHelper::profileSetId($group->id));
            if ($groupBroadcastProfileSet && Groups_BroadcastHelper::acceptingBroadcasts(User::loadOrRetrieveIfLoaded($groupMembership->my->username))) {
                $groupBroadcastProfileSet->addMembers($groupMembership->my->username);
            }
        }
        if ($wasMember && ! self::isMember($groupMembership)) {
            $updateCounts = true;
            self::removeGroupId($group->id, User::loadOrRetrieveIfLoaded($groupMembership->my->username));
            $groupMembership->my->welcomed = 'N';
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            XN_ProfileSet::removeMemberByLabel($groupMembership->my->username, Index_NotificationHelper::groupLabel($group->id));
            Index_NotificationHelper::stopFollowing($group, $groupMembership->my->username);
        }
        if ($status == 'banned') { $groupMembership->my->set('dateBanned', date('c'), XN_Attribute::DATE); }
        $groupMembership->save();
        if ($updateCounts) {
            $group->my->memberCount = Group::getMemberCount($group);
            $group->save();
            // Must save group *before* calling updateGroupCount, not after, because of NING-5370 (see BAZ-10338) [Jon Aquino 2008-09-25]
            W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_GroupHelper.php');
            if (User::loadOrRetrieveIfLoaded($groupMembership->my->username)) { Groups_GroupHelper::updateGroupCount(User::loadOrRetrieveIfLoaded($groupMembership->my->username)); }
        }
        return $groupMembership;
    }

    /**
     * Marks the User object as being a member of the specified group.
     * Saves the User object.
     *
     * @param $groupId string  ID of the group's content object
     * @param $user XN_Content|W_Content  The User object
     */
    protected static function addGroupId($groupId, $user, $log = true) {
        if (! $user && defined('UNIT_TESTING')) { return; }
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $user->my->{self::groupsWidgetAttributeName()} = XG_LangHelper::addToDelimitedString($groupId, $user->my->{self::groupsWidgetAttributeName()}, true);
        $user->save();
        if ($log) {
            $group = XN_Content::load($groupId);
            XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_CONNECTION, XG_ActivityHelper::SUBCATEGORY_GROUP, $user->contributorName, array($group));
        }
    }

    /**
     * Marks the User object as not being a member of the specified group.
     * Saves the User object.
     *
     * @param $groupId string  ID of the group's content object
     * @param $user XN_Content|W_Content  The User object
     */
    protected static function removeGroupId($groupId, $user) {
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        $user->my->{self::groupsWidgetAttributeName()} = XG_LangHelper::removeFromDelimitedString($groupId, $user->my->{self::groupsWidgetAttributeName()});
        $user->save();
    }

    /**
     * Returns the User attribute that stores the IDs of the groups that the person is a member of.
     *
     * @return string  the attribute name
     */
    private static function groupsWidgetAttributeName() {
        return XG_App::widgetAttributeName(W_Cache::getWidget('groups'), 'groups');
    }

    /**
     * Returns whether the user in the specified group-user relationship is a member of the group.
     *
     * @param $groupMembership XN_Content|W_Content  The GroupMembership specifying the relationship between a user and a group
     * @return boolean  Whether this relationship is one of membership
     */
    public static function isMember($groupMembership) {
        return $groupMembership->my->status == 'member' || $groupMembership->my->status == 'admin';
    }

    /**
    * Returns the GroupMembership for the given group and user.
    * or a new unsaved one if none yet exists.
    *
    * @param $group XN_Content|W_Content  The Group object
    * @param $username string  The screen name of the person
    * @return W_Content  The GroupMembership, or a new unsaved one if none exists yet.
    */
    public static function loadOrCreate($group, $username) {
        if (! $group->id || $_GET['test_baz_10481']) {
            error_log('BAZ-10481 @ Current user: ' . XN_Profile::current()->screenName . ' @ Current URL: ' . XG_HttpHelper::currentURL() . ' @ Referrer: ' . $_SERVER['HTTP_REFERER'] . ' @ User Agent: ' . $_SERVER['HTTP_USER_AGENT']); /** @allowed */
            xg_echo_and_throw('Group not saved (1298388384)');
        }
        if (! $username) { xg_echo_and_throw('Username unspecified (1198910000)'); }
        if (! is_string($username)) { xg_echo_and_throw('Username not string (2053087297)'); }
        $key = mb_strtolower($group->id . '+' . $username);
        if (! self::$groupMemberships[$key]) {
            $query = XN_Query::create('Content');
            if (XG_Cache::cacheOrderN()) {
                $query = XG_Query::create($query);
                $query->addCaching(XG_Cache::key('type', 'GroupMembership'));
            }
            $query->filter('type', '=', 'GroupMembership');
            $query->filter('owner');
            $query->filter('my.username', 'eic', $username);
            $query->filter('my.groupId', '=', $group->id);
            $query->end(1);
            if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
            $results = $query->execute();
            self::addToCache(array($results[0] ? W_Content::create($results[0]) : self::create($group, $username)));
        }
        return self::$groupMemberships[$key];
    }

    /** Mapping of groupId+username => GroupMembership */
    protected static $groupMemberships = array();

    /**
     * Adds an array of GroupMembership objects to the cache
     *
     * @param array  the GroupMemberships to add to the cache
     * @return array  the GroupMemberships
     */
    public static function addToCache($groupMemberships) {
        foreach ($groupMemberships as $groupMembership) {
            $key = mb_strtolower($groupMembership->my->groupId . '+' . $groupMembership->my->username);
            self::$groupMemberships[$key] = $groupMembership;
        }
        return $groupMemberships;
    }

    /**
     * Empties the cache. For testing.
     */
    protected static function clearCache() {
        self::$groupMemberships = array();
    }

    /**
     * Constructor.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person
     * @return W_Content  An unsaved content object of type 'GroupMembership'
     */
    public static function create($group, $username) {
        $groupMembership = W_Content::create('GroupMembership');
        // Explicitly specify 'groups' rather than using W_Cache::current('W_Widget')->dir,
        // as this function may be called early in the page load, and there may not be a current widget at this point [Jon Aquino 2007-04-27]
        $groupMembership->my->mozzle = 'groups';
        $groupMembership->isPrivate = true;
        $groupMembership->my->groupId = (string) $group->id;
        $groupMembership->my->status = 'nonmember';
        $groupMembership->my->activityCount = 0;
        $groupMembership->my->welcomed = 'N';
        $groupMembership->my->username = $username;
        $groupMembership->my->groupPrivacy = $group->my->groupPrivacy;
        //TODO can we afford to load User here?  Better to pass in fullName?
        $user = User::loadOrRetrieveIfLoaded($username);
        //TODO what if $user is null at this point?
        $groupMembership->my->fullName = XG_UserHelper::getFullName(XG_Cache::profiles($user));
        return $groupMembership;
    }

    /**
     * Returns the key used to invalidate query caches when a GroupMembership is created, updated, or deleted.
     *
     * @param $groupId string  the ID of the Group
     * @return string  the invalidation key
     * @see David Sklar, "Query Caching", internal wiki
     * @see XG_Query#setCaching
     */
    public static function groupMembershipChangedInvalidationKey($groupId) {
        if (! is_string($groupId) && ! is_numeric($groupId)) { xg_echo_and_throw('Not a string'); }
        return 'group-membership-changed-' . $groupId;
    }

    /**
     * Called after a content object has been saved or before a content object has been deleted.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function contentSavedOrDeleted($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::contentSavedOrDeleted($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if ($object->type == 'GroupMembership') {
            XG_Query::invalidateCache(self::groupMembershipChangedInvalidationKey($object->my->groupId));
        }
    }

    /**
     * Converts the given GroupMemberships to XN_Profiles
     *
     * @param $groupMemberships array  The GroupMembership XN_Content or W_Content objects
     * @return array  an array of username => XN_Profile
     */
    public function profiles($groupMemberships) {
        $usernames = self::usernames($groupMemberships);
        XG_Cache::profiles($usernames); // Prime the cache [Jon Aquino 2007-04-20]
        $profiles = array();
        foreach ($usernames as $username) {
            // Ensure order matches $usernames [Jon Aquino 2007-04-20]
            if (XG_Cache::profiles($username)) { $profiles[$username] = XG_Cache::profiles($username); }
        }
        return $profiles;
    }

    /**
     * Converts the given GroupMemberships to usernames
     *
     * @param $groupMemberships array  The GroupMembership XN_Content or W_Content objects
     * @return array  an array of usernames
     */
    public function usernames($groupMemberships) {
        $usernames = array();
        foreach ($groupMemberships as $groupMembership) {
            $usernames[] = $groupMembership->my->username;
        }
        return $usernames;
    }

    /**
     * Returns the IDs of the groups that the person is a member of.
     * Note that some of the Group objects may be hidden or deleted.
     *
     * @param $user XN_Content|W_Content  the User object
     * @return array  the content-object IDs of the Groups
     */
    public static function groupIds($user) {
        return $user->my->{self::groupsWidgetAttributeName()} ? explode(' ', $user->my->{self::groupsWidgetAttributeName()}) : array();
    }

    /**
     * Returns the GroupMemberships for the group administrators
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $n integer  max number of admins to return (default is 100)
     * @return array  the GroupMemberships of the admins
     */
    public static function admins($group, $n = 100) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create('Content')->end($n);
        } else {
            $query = XN_Query::create('Content')->end($n);
        }
        return Groups_GroupMembershipFilter::get('admin')->execute($query, $group->id);
    }

    //TODO should this be on an XG_DenormalizeHelper with more general methods that can be reused?
    /**
     * Denormalize fullName attribute by copying from User to GroupMembership for up to 50 GroupMembership objects.
     * It is expected that this code will mostly be called as a chained XN_Job and thus any more than 50 undenormalized
     * GroupMemberships are ignored until the next call to this routine.  To run the change for a whole network
     * manually you must call this method repeatedly until it returns 0.
     *
     * @return The number of GroupMembership objects that remain to denormalize.
     */
    public static function denormalizeFullName() {
        $query = XN_Query::create('Content')->filter('owner');
        $query->filter('type', '=', 'GroupMembership');
        $query->filter('my->fullName', '=', null);
        $query->begin(0);
        $query->end(50);
        $query->alwaysReturnTotalCount(true);
        $memberships = array();
        $usernames = array();
        $memberships = $query->execute();
        foreach ($memberships as $membership) {
            $usernames[] = $membership->my->username;
        }
        $users = User::loadMultiple(array_unique($usernames));
        $changed = 0;
        foreach ($memberships as $membership) {
            foreach ($users as $username => $user) {
                if ($username == $membership->my->username) {
                     $fullName = XG_UserHelper::getFullName(XG_Cache::profiles($user));
                     $membership->my->fullName = is_null($fullName) ? '' : $fullName;
                     $membership->save();
                     $changed++;
                     break;
                }
            }
        }
        if ($changed === 0 && $query->getTotalCount() !== 0) {
            throw new Exception("Tried to denormalize fullName but changed $changed when " . $query->getTotalCount() . " found.");
        }
        return max(0, $query->getTotalCount() - $changed);
    }

    /**
     * Helper method to update fullName on all of a user's GroupMembership objects when it is updated elsewhere.
     *
     * @param   $screenName  String Username of the user changing fullName.
     * @param   $fullName  String   Full name being changed to.s
     */
    public static function setFullName($screenName, $fullName) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $max = 50;
        $query = XN_Query::create('Content')->filter('owner');
        $query->filter('type', '=', 'GroupMembership');
        $query->filter('my->username', '=', $screenName);
        $query->filter('my->fullName', '<>', $fullName);
        $query->begin(0);
        $query->end($max);
        $query->alwaysReturnTotalCount(true);
        foreach ($query->execute() as $membership) {
            $membership->my->fullName = $fullName;
            $membership->save();
        }
        if ($query->getTotalCount() > $max) {
            // User belongs to more than $max groups so schedule a job to get the remainder finished, too.
            $job = self::createJobForSetFullName($screenName, $fullName);
            $result = $job->save();
            if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
        }
    }

    /**
     * Create an XN_Job to denormalize my->fullName from each User in the network to the related GroupMembership objects.
     */
    public static function scheduleDenormalizeFullName() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $job = self::createJobForDenormalizeFullName();
        // TODO: Use XG_SequencedjobController [Jon Aquino 2008-03-19]
        $result = $job->save();
        if (Index_InvitationHelper::isErrorArray($result)) { throw new Exception(Index_InvitationHelper::errorMessage(key($result))); }
    }

    /**
     * Create an XN_Job that will set the my->fullName attribute for GroupMemberships for the specified user.
     *
     * @param   $screenName  String     Screenname of user to adjust GroupMemberships of.
     * @param   $fullName  String       Name to set the value to.
     * @return  XN_Job                  Unsaved XN_Job - call save to schedule.
     */
    public static function createJobForSetFullName($screenName, $fullName) {
        $job = XN_Job::create();
        $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(XG_GroupHelper::buildUrl('groups', 'group', 'setFullName')), array('screenName' => $screenName, 'fullName' => $fullName)));
        return $job;
    }

    /**
     * Create an XN_Job that will continue the task of denormalizing my->fullname from User to GroupMembership.
     *
     * @return  XN_Job                  Unsaved XN_Job - call save to schedule.
     */
    public static function createJobForDenormalizeFullName() {
        $job = XN_Job::create();
        $job->addTask(XN_Task::create(XG_SecurityHelper::addCsrfToken(XG_GroupHelper::buildUrl('groups', 'group', 'denormalizeFullName')), array()));
        return $job;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('xn/content/save/after', array('GroupMembership', 'contentSavedOrDeleted'));
XN_Event::listen('xn/content/delete/before', array('GroupMembership', 'contentSavedOrDeleted'));
