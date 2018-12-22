<?php
/**
 * Model class for the object representing a Bazel group.
 */
class Group extends W_Model {

    /**
     * The title of the group. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING
     * @rule length 1,200
     * @feature indexing text
     */
    public $title;
    const MAX_TITLE_LENGTH = 200;


    /**
     * The custom group URL, e.g., MyCoolGroup
     *
     * @var XN_Attribute::STRING
     * @rule length 1,50
     */
    public $url;
    const MAX_URL_LENGTH = 50;

    /**
     * The group's description as set by the group owner. Be sure to escape this with xnhtmlentities() when displaying it.
     *
     * @var XN_Attribute::STRING optional
     * @rule length *,250
     * @feature indexing text
     */
    public $description;
    const MAX_DESCRIPTION_LENGTH = 250;

    /**
     * Group object privacy matches network (app) privacy since the group itself
     * is always visible regardless of groupPrivacy (which determines whether
     * the group is "public" - i.e. has open membership and content.)
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Public groups are entirely visible.  Private groups display only a not
     * allowed message and optionally a request form to non-members.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $groupPrivacy;
    public $groupPrivacy_choices = array('public', 'private');

    /**
     * Whether the group has been approved by the app owner.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $approved;
    public $approved_choices = array('Y', 'N');

    /**
     * Which mozzle created this object?  (always group)
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     *
     */
    public $mozzle;

    /**
     * Content ID of the group's icon.
     *
     * @var XN_Attribute::STRING optional
     */
    public $iconId;

    /**
     * Width of the group's icon, in pixels; or null if no icon has been uploaded.
     *
     * @var XN_Attribute::STRING optional
     */
    public $iconWidth;

    /**
     * URL of the group's icon. Not used if the icon object is private,
     * as URLs to private objects expire after a period of time.
     *
     * @var XN_Attribute::STRING optional
     */
    public $iconUrl;

    /**
     * Location
     *
     * @var XN_Attribute::STRING optional
     * @rule length 1,200
     * @feature indexing phrase
     */
    public $location;
    const MAX_LOCATION_LENGTH = 200;

    /**
     * Name of an external website
     *
     * @var XN_Attribute::STRING optional
     * @rule length 1,200
     * @feature indexing text
     */
    public $externalWebsiteName;
    const MAX_EXTERNAL_WEBSITE_NAME_LENGTH = 200;

    /**
     * GMT dates and view counts. Example: 11FEB1977 5, 12FEB1977 10, 07MAR1977 15.
     *
     * @var XN_Attribute::STRING optional
     */
    public $dailyActivityScoresForLastMonth;

    /**
     * The activity score for the Group.
     *
     * @var XN_Attribute::NUMBER optional
     * @rule range 0,*
     */
    public $activityScore;


    /**
     * The last activity date for the Group.
     *
     * @var XN_Attribute::DATE optional
     *
     */
    public $lastActivityOn;


    /**
     * URL of the external website
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,2000
     * @feature indexing phrase
     */
    public $externalWebsiteUrl;
    const MAX_EXTERNAL_WEBSITE_URL_LENGTH = 2000;

    /**
     * Whether members can invite others to the group.
     * Always "Y" if the group is public.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $allowInvitations;
    public $allowInvitations_choices = array('Y', 'N');

    /**
     * Whether members can send messages to the group.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $allowMemberMessaging;
    public $allowMemberMessaging_choices = array('Y', 'N');

    /**
     * Whether non-members can request an invitation to the group
     * Not applicable if the group is public.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     */
    public $allowInvitationRequests;
    public $allowInvitationRequests_choices = array('Y', 'N');

    /**
     *  Number of members
     *
     * @var XN_Attribute::NUMBER
     */
    public $memberCount;

    /**
     * "Y" indicates that this group is being hidden and is equivalent to deleted.
     * Deleted groups should not appear to the user.
     * We don't actually delete large groups, so that the user does not have to wait, and
     * so that we can resurrect groups if requested.
     * Small groups (less than 10 objects) are deleted immediately.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $deleted;
    public $deleted_choices = array('Y', 'N');

    /**
     * "Y" indicates that this group should be excluded from Ningbar and widget
     * search results. This is true of groups that are invisible or marked as deleted
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

/** xn-ignore-start e40ef5265dd4213fdcbfeb0735e1b8b0 **/
// Everything other than instance variables goes below here


    /**
     * Constants for activity score metrics.
     */
    const ACTIVITY_SCORE_COMMENT = 1;
    const ACTIVITY_SCORE_MEMBER_JOIN = 2;
    const ACTIVITY_SCORE_FORUM_TOPIC = 1;
    const ACTIVITY_SCORE_FORUM_COMMENT = 1;

    /**
     * Constructor.
     *
     * @param $title string  The name of the group
     * @param $description string  A description of the group (plain text)
     * @return W_Content  An unsaved content object of type 'Group'
     */
    public static function create($title = null, $description = null) {
        $group = W_Content::create('Group');
        $group->title = $title;
        $group->description = $description;
        $group->my->groupPrivacy = 'public';
        $group->my->mozzle = 'groups';
        $group->my->allowInvitations = 'Y';
        $group->my->allowInvitationRequests = 'Y';
        $group->my->allowMemberMessaging = 'Y';
        $group->my->memberCount = 0;
        $group->my->activityScore = 0;
        $group->my->set('lastActivityOn', gmdate('Y-m-d\TH:i:s\Z'), XN_Attribute::DATE);
        if (defined('UNIT_TESTING')) { $group->my->url = 'test'; }
        $group->isPrivate = self::determinePrivacy($group);
        return $group;
    }

    /**
     * Truncates the given title
     *
     * @param $title string  The Group title
     * @return string  The cleaned up Group title
     */
    public static function cleanTitle($title) {
        if (! trim($title)) { return xg_text('UNTITLED'); }
        return mb_substr(trim($title), 0, Group::MAX_TITLE_LENGTH);
    }

    /**
     * Truncates the given description
     *
     * @param $description string  The Group description
     * @return string  The cleaned up Group description
     */
    public static function cleanDescription($description) {
        if (! trim($description)) { return null; }
        return mb_substr(trim($description), 0, Group::MAX_DESCRIPTION_LENGTH);
    }

    /**
     * Truncates the given location
     *
     * @param $location string  The Group's location
     * @return string  The cleaned up Group location
     */
    public static function cleanLocation($location) {
        if (! trim($location)) { return ''; }
        return mb_substr(trim($location), 0, Group::MAX_LOCATION_LENGTH);
    }

    /**
     * Truncates the given externalWebsiteName
     *
     * @param $externalWebsiteName string  The Group externalWebsiteName
     * @return string  The cleaned up Group externalWebsiteName
     */
    public static function cleanExternalWebsiteName($externalWebsiteName) {
        if (! trim($externalWebsiteName)) { return null; }
        return mb_substr(trim($externalWebsiteName), 0, Group::MAX_EXTERNAL_WEBSITE_NAME_LENGTH);
    }

    /**
     * Truncates the given URL component
     *
     * @param $url string  the group-identifying component in the path of the group's URL
     * @return string  the cleaned up url
     */
    public static function cleanUrl($url) {
        return mb_substr(trim($url), 0, Group::MAX_URL_LENGTH);
    }

    /**
     * Truncates the given externalWebsiteUrl
     *
     * @param $externalWebsiteUrl string  The Group externalWebsiteUrl
     * @return string  The cleaned up Group externalWebsiteUrl
     */
    public static function cleanExternalWebsiteUrl($externalWebsiteUrl) {
        $externalWebsiteUrl = trim($externalWebsiteUrl);
        if (! $externalWebsiteUrl || $externalWebsiteUrl == 'http://') { return null; }
        $externalWebsiteUrl = mb_strpos($externalWebsiteUrl, '://') === false ? 'http://' . $externalWebsiteUrl : $externalWebsiteUrl;
        return mb_substr($externalWebsiteUrl, 0, Group::MAX_EXTERNAL_WEBSITE_URL_LENGTH);
    }

    /**
     * Returns whether the given name has already been taken by an existing group.
     * Case-insensitive. If an ID is given, returns false if the name matches.
     *
     * @param $title string  The name to check
     * @param $id string  Content ID of the current group, or null if the group is new
     * @return boolean  Whether the name (or a very similar one) is being used
     */
    public static function nameTaken($title, $id = null) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Group');
        $query->filter('my.mozzle', '=', 'groups');
        $query->filter('title', 'eic', trim($title));
        if ($id) { $query->filter('id', '<>', $id); }
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        return count(self::addToCache($query->execute())) > 0;
    }

    /**
     * Returns whether the given URL has already been taken by an existing group.
     * Case-insensitive.
     *
     * @param $url string  The URL to check
     * @return boolean  Whether the URL is being used
     */
    public static function urlTaken($url) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Group');
        $query->filter('my.mozzle', '=', 'groups');
        $query->filter('my.url', 'eic', trim($url));
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        return count(self::addToCache($query->execute())) > 0;
    }

    /**
     * Returns the ID for the group with the given URL
     *
     * @param $url string  the group-identifying component in the path of the group's URL
     * @return string  the content ID of the Group, or null if no such group exists
     */
    public static function urlToId($url) {
        $query = XN_Query::create('Content');
        if (XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
            $query->setCaching(self::groupChangedInvalidationKey($url));
        }
        $query->filter('owner');
        $query->filter('type', '=', 'Group');
        $query->filter('my.mozzle', '=', 'groups');
        $query->filter('my.url', 'eic', $url);
        $query->end(1);
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query);
        if (defined('UNIT_TESTING')) { $query->filter('my.test', '=', 'Y'); }
        $groups = self::addToCache($query->execute());
        return $groups[0] ? $groups[0]->id : null;
    }

    /**
     * Returns the key used to invalidate query caches when a Group is updated or deleted
     *
     * @param $url string the URL of the Group, e.g., mycoolgroup
     * @return string  the invalidation key
     */
    public static function groupChangedInvalidationKey($url) {
        return 'group-changed-' . $url;
    }

    /**
     * Returns the URL of the icon to use for the given group. A default icon is returned
     * if no icon has been uploaded.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $preferredWidth integer  The preferred width for the icon
     */
    public static function iconUrl($group, $width) {
        try {
            if (self::hasIcon($group)) {
                return $group->my->iconWidth <= $width ? self::iconUrlProper($group) : XG_HttpHelper::addParameters(self::iconUrlProper($group), array('width' => $width, 'crop' => '1:1'));
            }
        } catch (Exception $e) {
            // BAZ-3303 [Jon Aquino 2007-06-05]
        }
        return xg_cdn(W_Cache::getWidget('groups')->buildResourceUrl('gfx/avatar-group.png'));
    }

    /**
     * Returns the URL of the icon to use for the given group, or null if no icon has been uploaded
     *
     * @param $group XN_Content|W_Content  The Group object
     */
    public static function iconUrlProper($group) {
        if (! self::hasIcon($group)) { return null; }
        if ($group->my->iconUrl) { return XG_HttpHelper::addParameter($group->my->iconUrl, 'crop','1:1'); }
        return XG_HttpHelper::addParameter(XG_Cache::content($group->my->iconId)->fileUrl('data'), 'crop','1:1');
    }

    /**
     * Sets the status of the person in relation to the group.
     * The Group object does not need to be saved afterwards.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person
     * @param $status string  The status: nonmember, member, admin, banned
     */
    public static function setStatus($group, $username, $status) {
        return GroupMembership::setStatus(GroupMembership::loadOrCreate($group, $username), $status);
    }

    /**
     * Returns the profiles of the group administrators
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $n integer  max number of admins to return (default is 100)
     * @return array  the XN_Profiles of the admins
     */
    public static function adminProfiles($group, $n = 100) {
        return GroupMembership::profiles(GroupMembership::admins($group, $n));
    }

    /**
     * Returns the usernames of the group administrators
     *
     * @param $group XN_Content|W_Content  the Group object
     * @param $n integer  max number of admins to return (default is 100)
     * @return array  the usernames of the admins
     */
    public static function adminUsernames($group, $n = 100) {
        return GroupMembership::usernames(GroupMembership::admins($group, $n));
    }

    /**
     * Returns whether the group is a private group
     *
     * @param $group XN_Content|W_Content  The Group object
     * @return boolean  Whether people need an invite to join the group
     */
    public static function isPrivate($group) {
        return $group->my->groupPrivacy == 'private';
    }

    /**
     * Returns whether the group is a public group
     *
     * @param $group XN_Content|W_Content  The Group object
     * @return boolean  Whether peopl can join the group without an invite
     */
    public static function isPublic($group) {
        return $group->my->groupPrivacy == 'public';
    }

    /**
     * Returns whether the user is a member of the group.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  Whether the person is a member
     */
    public static function userIsMember($group, $username = null) {
        if (! XN_Profile::current()->isLoggedIn()) { return false; }
        $username = $username ? $username : XN_Profile::current()->screenName;
        return GroupMembership::isMember(GroupMembership::loadOrCreate($group, $username));
    }

    /**
     * Returns whether the user is a group administrator
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  Whether the person is an admin
     */
    public static function userIsAdmin($group, $username = null) {
        return self::status($group, $username) == 'admin';
    }

    /**
     * Returns whether the user created the group
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  Whether the person owns the group
     */
    public static function userIsCreator($group, $username = null) {
        $username = $username ? $username : XN_Profile::current()->screenName;
        return $username == $group->contributorName;
    }

    /**
     * Returns whether the user has been banned from this group
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  Whether the person is not allowed to join
     */
    public static function userIsBanned($group, $username = null) {
        return self::status($group, $username) == 'banned';
    }

    /**
     * Returns whether the user has been invited to this group
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  Whether the person has been invited
     */
    public static function userIsInvited($group, $username = null) {
        $username = $username ? $username : XN_Profile::current()->screenName;
        if (! $username) { return null; } // Signed out [Jon Aquino 2007-04-20]
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        return ! Group::userIsMember($group) && ! Group::userIsBanned($group) && Groups_InvitationHelper::getUnusedGroupInvitation($group, $username);
    }


    /**
     * Returns the url of a group when given an id
     *
     * @param $id string  The id of the group
     * @return string The url of the group
     */
    public static function idToUrl($id) {
        $group = self::load($id);
        return $group->my->url;

    }

    /**
     * Returns the person's status in the group: nonmember, member, admin, or banned
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $username string  The screen name of the person, or null to specify the current user
     * @return boolean  The person's status
     */
    public static function status($group, $username = null) {
        $username = $username ? $username : XN_Profile::current()->screenName;
        if (! $username) { return null; } // Signed out [Jon Aquino 2007-04-20]
        return GroupMembership::loadOrCreate($group, $username)->my->status;
    }

    /**
     * Returns the Group object with the given content ID.
     * Throws an exception if the group does not exist.
     *
     * @param $id string  the content-object ID
     * @return XN_Content|W_Content  the Group object
     */
    public static function load($id) {
        if (! self::$idToGroupMap[$id]) {
            $group = XG_Cache::content($id);
            if (! $group) { throw new Exception('Group not found: ' . $id . ' (536782629)'); }
            if ($group->my->deleted == 'Y') { throw new Exception('Group deleted: ' . $id . ' (1488297346)'); }
            if ($group->type != 'Group') { throw new Exception('Not a Group: ' . $group->type . ' (1439061785)'); }
            self::addToCache(array(W_Content::create($group)));
        }
        return self::$idToGroupMap[$id];
    }

    /** Cache of group IDs and Group W_Content objects */
    private static $idToGroupMap = array();

    /**
     * Adds an array of Group objects to the cache
     *
     * @param array  the Groups to add to the cache
     * @return array  the Groups
     */
    public static function addToCache($groups) {
        foreach ($groups as $group) {
            if (self::$idToGroupMap[$group->id]) { continue; }
            self::$idToGroupMap[$group->id] = $group;
        }
        return $groups;
    }

    /**
     * Sets the icon for the group. Does not save the Group object.
     *
     * @param $group XN_Content|W_Content  The Group object
     * @param $groupIcon XN_Content|W_Content  The GroupIcon object, or null to clear the icon
     */
    public static function setIcon($group, $groupIcon) {
        if (self::hasIcon($group)) {
            XN_Content::delete($group->my->iconId);
            $group->my->iconId = null;
            $group->my->iconUrl = null;
            $group->my->iconWidth = null;
        }
        if ($groupIcon) {
            $group->my->iconId = $groupIcon->id;
            $group->my->iconUrl = $groupIcon->isPrivate ? null : $groupIcon->fileUrl('data');
            list($width, $height) = $groupIcon->imageDimensions('data');
            $group->my->iconWidth = $width;
        }
    }

    /**
    * Selects the privacy of the group, based on app privacy and moderation if turned on
    *
    * @param $group XN_Content|W_Content  The Group object
    * @return boolean
    */
    private static function determinePrivacy($group) {
        if ($group->my->approved) {
            return (XG_App::appIsPrivate() && $group->my->approved != 'N');
        } else {
            if (XG_App::appIsPrivate() || XG_App::groupsAreModerated()) {
                return true;
            }
            return false;
        }
    }

    /**
     * Returns whether an icon has been uploaded for the group
     *
     * @param $group XN_Content|W_Content  The Group object
     */
    public static function hasIcon($group) {
        return $group->my->iconId;
    }

    /**
     * Returns the IDs of the groups that the person is a member of.
     * Note that some of the Group objects may be deleted or marked as deleted.
     *
     * @param $user XN_Content|W_Content  the User object
     * @return array  the content-object IDs of the Groups
     */
    public static function groupIds($user) {
        return GroupMembership::groupIds($user);
    }

    /**
     * Returns the IDs of groups that are being hidden and should be considered deleted.
     * Large groups are hidden instead of deleted, for speed and recoverability.
     */
    public static function idsOfGroupsMarkedAsDeleted() {
        $idsOfGroupsMarkedAsDeleted = W_Cache::getWidget('groups')->privateConfig['groupsMarkedAsDeleted'];
        return $idsOfGroupsMarkedAsDeleted ? explode(' ', $idsOfGroupsMarkedAsDeleted) : array();
    }

    /**
     * For the given array of content objects, returns a mapping of group-ID => Group.
     * The order is preserved.
     *
     * @param $objects array  XN_Content objects, some of which may have groupId attributes
     * @return array  A mapping of group IDs to Group content objects
     */
    public static function groupsForObjects($objects) {
        $groupIds = array();
        foreach ($objects as $object) {
            if ($object->my->groupId) { $groupIds[$object->my->groupId] = $object->my->groupId; }
        }
        return self::groupsWithIds($groupIds);
    }

    /**
     * Retrieves the groups with the given IDs, in the same order as the IDs
     *
     * @param $ids array  Content-object IDs
     * @return array  A mapping of group IDs to Group content objects
     */
    public static function groupsWithIds($ids) {
        if (count($ids) == 0) { return array(); }
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'Group');
        $query->filter('id', 'in', $ids);
        XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
        XG_QueryHelper::addDeletedFilter($query);
        $unsortedGroups = self::addToCache($query->execute());
        $idsToGroups = array();
        foreach ($unsortedGroups as $group) {
            $idsToGroups[$group->id] = $group;
        }
        $groups = array();
        foreach ($ids as $id) {
            if ($idsToGroups[$id]) { $groups[$id] = $idsToGroups[$id]; }
        }
        return $groups;
    }

    /**
     * Called after a content object has been saved.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function contentSaved($object) {
        if (is_array($object)) {
            foreach ($object as $o) { self::contentSavedOrDeleted($o); }
            return;
        }
        if (! ($object instanceof XN_Content || $object instanceof W_Content)) { return; }
        if ($object->type == 'Group') {
            XG_Query::invalidateCache(Group::groupChangedInvalidationKey($object->my->url));
        }
    }


    /**
     * Returns the current group member count
     *
     * @param $group XN_Content|W_Content  The Group object
     * @return numeric  The current member count
     */
    public static function getMemberCount($object) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_GroupMembershipFilter.php');
        $query = XN_Query::create('Content')->end(1);
        Groups_GroupMembershipFilter::get('unsorted')->execute($query, $object->id);
        return $query->getTotalCount();
    }

    /**
     * Helper function that converts a date object to a string.
     *
     * @param dateObj The date to convert
     * @return The string representation
     */
    private static function dateToString($dateObj) {
        return mb_strtoupper(gmdate('dMY', $dateObj));
    }

    /**
     * Sets the activity score per day for the last month.
     *
     * @param dailyActivityScoresForLastMonth An array of date string => view count
     */
    public static function setDailyActivityScoreForLastMonth($group, $dailyActivityScoresForLastMonth) {
        $x = array();
        foreach ($dailyActivityScoresForLastMonth as $dateString => $activityScore) {
            $x[] = $dateString . ' ' . $activityScore;
        }
        $group->my->dailyActivityScoresForLastMonth = implode(', ', $x);
    }

    /**
     * Returns the view counts per day for the last month.
     *
     * @return An array of date string => view count
     */
    public static function getDailyActivityScoresForLastMonth($group) {
        $x = array();
        if ($group->my->dailyActivityScoresForLastMonth) {
            foreach (explode(',', $group->my->dailyActivityScoresForLastMonth) as $dateStringAndActivityScore) {
                list($dateString, $activityScore) = explode(' ', trim($dateStringAndActivityScore));
                $x[$dateString] = $activityScore;
            }
        }
        return $x;
    }

    /**
     * updates the activity score for the Group.
     */
    public static function updateActivityScore($group, $score) {

        $time = time();
        $group->my->set('lastActivityOn', gmdate('Y-m-d\TH:i:s\Z'), XN_Attribute::DATE);

        $dailyActivityScoresForLastMonth = self::getDailyActivityScoresForLastMonth($group);
        $dailyActivityScoresForLastMonth[self::dateToString($time)] += $score;

        $secondsPerDay         = 24 * 3600;

        $activityScoreForLastDay   = 0;

        foreach ($dailyActivityScoresForLastMonth as $dateString => $activityScore) {
            // Truncate the time to midnight so the last-day window is at least 24 hours [Jon Aquino 2006-07-22]
            $age = strtotime(self::dateToString($time) . ' GMT') - strtotime($dateString . ' GMT');

            if ($age <= 1 * $secondsPerDay) {
                $activityScoreForLastDay = $activityScoreForLastDay + $activityScore;
            }
            if ($age > 31 * $secondsPerDay) {
                unset($activityScoreForLastMonth[$dateString]);
            }
        }

        self::setDailyActivityScoreForLastMonth($group,$dailyActivityScoresForLastMonth);
        $group->my->activityScore = $activityScoreForLastDay;
    }

/** xn-ignore-end e40ef5265dd4213fdcbfeb0735e1b8b0 **/

}

XN_Event::listen('xn/content/save/after', array('Group', 'contentSaved'));