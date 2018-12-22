<?php

/**
 * Helper class that deals with all things to do with the "Person" concept in OpenSocial.
 */
class OpenSocial_PersonHelper {

    /** Code that represents the screenName of the anonymous user for OpenSocial purposes. */
    const ANONYMOUS = "_anonymous";
    
    /** Format-type */
    const FULL_FORMAT = "fullFormat";
    const FRIEND_FORMAT = "friendFormat";

    /**
     * Get Person details for the people identified in $ids filtered, sorted, paged and validated as described by the other parameters.
     *
     * @param   $appUrl     string  URL of gadget XML.
     * @param   $viewerId   string  screenName of current app viewer or OpenSocial_PersonHelper::ANONYMOUS.
     * @param   $ownerId    string  screenName of current app owner or OpenSocial_PersonHelper::ANONYMOUS.
     * @param   $ids        array   Array of string ids and special codes to look for (OpenSocial_PersonHelper::ANONYMOUS, VIEWER, OWNER, VIEWER_FRIENDS, OWNER_FRIENDS).
     * @param   $format     string  Format to fetch the data in: Could be one of FULL_FORMAT or FRIEND_FORMAT.
     * @param   $filter     string  OpenSocial standard filter string.  One of all, hasApp or topFriends.  Empty string has the same effect as ALL.
     * @param   $sort       string  OpenSocial standard sort string.  One of name or topFriends.
     * @param   $first      int     0-based indicator of where in the search results to begin.
     * @param   $max        int     Maximum number of results to return (100 is the hard limit, values of $max over 100 will be treated as 100).
     * @return              array   array(offset => first result, totalSize => total number of people that you can paginate over, people => Array of dict arrays containing people with id, name, isViewer and isOwner attributes.
     */
    public static function getPeople($appUrl, $viewerId, $ownerId, $ids, $format = self::FULL_FORMAT, $filter="all", $sort="name", $first=0, $max=100) {
        $actualMax = $max;
        $actualSort = self::getSort($sort);
        list($filters, $idFilters, $results) = array(array(), array(), array());
        $anonymousIncluded = false;
        $app = OpenSocialApp::load($appUrl);
        $appMembers = OpenSocialApp::getMembers($app);


        foreach ($ids as $id) {
            if (self::requestForAnonymous($id, $viewerId, $ownerId)) {
                // The anonymous user is always the first result in the whole conceptual resultset if requested.
                if (! $anonymousIncluded && $first == 0) {
                    //TODO: automatically making the anonymous user first in the list is a bit rubbish.  Place in the correct place for the order? [Thomas David Baker 2008-07-17]
                    $results[] = array('id' => OpenSocial_PersonHelper::ANONYMOUS, 'name' => xg_text('YOU'),
                                       'isViewer' => ($viewerId == OpenSocial_PersonHelper::ANONYMOUS), 'isOwner' => false);
                    $actualMax--;
                    $anonymousIncluded = true;
                }
            } else if (! self::requestForAnonymousFriends($id, $viewerId, $ownerId)) {
                $idFilters[] = self::getIdFilter($id, $viewerId, $ownerId);
            }
        }
        if (! $idFilters) {
            // We are only getting the anonymous user.
            return array('offset' => $first, 'totalSize' => 1, 'people' => $results);
        }
        $filters[] = call_user_func_array(array('XN_Filter', 'any'), $idFilters);

        $paramFilter = self::getFilter($filter, $appUrl, $ids);
        if ($paramFilter) {
            $filters[] = $paramFilter;
        }

        // Ensure the ids are either viewer, owner or in viewer_friends or owner_friends
        $filters[] = self::onlyIncludeViewerOwnerAndTheirFriendsFilter($viewerId, $ownerId);

        // Get the appropriate User objects and parse them into the correct format.
        $userInfo = User::find($filters, $first, $first + $actualMax, $actualSort, 'ASC', FALSE /* not cached */);
        $profiles = XG_Cache::profiles($userInfo['users']);
        foreach ($userInfo['users'] as $user) {
            if ($filter === 'hasApp' && ! array_key_exists($user->title, $appMembers)) {
                continue;
            }
            if ($format === self::FRIEND_FORMAT) {
                $results[] = array('screenName' => $user->title, 'fullName' => XG_UserHelper::getFullName($profiles[$user->title]),
                                   'thumbnailUrl' => XG_UserHelper::getThumbnailUrl($profiles[$user->title], null, null),
                                   'isMember' => 'true');
            } else {
                $results[] = array('id' => $user->title, 'name' => XG_UserHelper::getFullName($profiles[$user->title]),
                                   'isViewer' => ($viewerId === $user->title), 'isOwner' => ($ownerId === $user->title),
                                   'thumbnailUrl' => array('type' => 'thumbnail image',
                                                           'address' => XG_UserHelper::getThumbnailUrl($profiles[$user->title], null, null),
                                                           'linkText' => XG_UserHelper::getFullName($profiles[$user->title])),
                                   'profileUrl' => array('type' => 'profile page',
                                                         'address' => xg_absolute_url(User::profileUrl($user->title)),
                                                         'linkText' => xg_text('VIEW_XS_PAGE_ON_Y', XG_UserHelper::getFullName($profiles[$user->title]), XN_Application::load()->name)));
            }
        }
        return ($format === self::FRIEND_FORMAT) ?  array('friends' => $results, 'paginationHtml' => '', 'numUsers' => $userInfo['numUsers']) :
                                                    array('offset' => $first, 'totalSize' => $userInfo['numUsers'], 'people' => $results);
    }

    /**
     * Get a filter that restricts the results to $viewerId, $ownerId and their friends.
     * The anonymous user does not have any friends.
     *
     * @param   $viewerId   string  screenName of current app viewer or OpenSocial_PersonHelper::ANONYMOUS.
     * @param   $ownerId    string  screenName of current app owner or OpenSocial_PersonHelper::ANONYMOUS.
     */
    public static function onlyIncludeViewerOwnerAndTheirFriendsFilter($viewerId, $ownerId) {
        $filters = array();
        if ($viewerId != OpenSocial_PersonHelper::ANONYMOUS) {
            $filters[] = XN_Filter('contributorName', 'in', XN_Query::FRIENDS($viewerId));
        }
        if ($ownerId != OpenSocial_PersonHelper::ANONYMOUS) {
            $filters[] = XN_Filter('contributorName', 'in', XN_Query::FRIENDS($ownerId));
        }
        $filters[] = XN_Filter('title', '=', $viewerId);
        $filters[] = XN_Filter('title', '=', $ownerId);
        return call_user_func_array(array('XN_Filter', 'any'), $filters);
    }

    /**
     * Determine an XN_Filter to be used from the supplied OpenSocial filter constant.  Actually ignored at this time.
     *
     * @param   $rawFilter  string              One of all, hasApp or topFriends.  Empty string treated as all.
     * @param   $appUrl     string              Application identifier, used with hasApp filters
     * @param   $ids        string              list of screenNames
     * @return              mixed               false if no filter required, null if invalid input, XN_Filter if filter required.
     */
    public static function getFilter($rawFilter, $appUrl, $ids) {
        if ($rawFilter === 'all') {
            return false;
        } else if ($rawFilter === 'hasApp') {
            return false; 
        } else if ($rawFilter === 'topFriends') {
            return false; // Ignored for now as Ning has no such feature.
        } else {
            return null;
        }
    }

    /**
     * Determine the Bazel sort value from the OpenSocial sort value.
     *
     * @param   $rawSort    string  OpenSocial sort value.  One of:
     *                                  topFriends - show 'best friends' first. If no 'best friends' metric exists, unordered
     *                                  name - natural sort by name
     */
    public static function getSort($rawSort) {
        // We do not respect topFriends yet as Ning has no such feature so for now we always do a name sort (the only other option).
        return 'my->fullName';
    }


    /**
     * Takes an "id" (either a screenName or an OpenSocial constant such as VIEWER_FRIENDS) and returns and XN_Filter
     * suitable for passing in to User::find to find the appropriate User objects.
     *
     * @param   $id         string      screenName to search for, OpenSocial_PersonHelper::ANONYMOUS for anonymous, or one of VIEWER, OWNER, VIEWER_FRIENDS, OWNER_FRIENDS.
     * @param   $viewerId   string      screenName of the current app viewer.
     * @param   $ownerId    string      screenName of the current app owner.
     * @param               XN_Filter   Filter suitable for passing to User::find to find the User(s) specified by $id.
     */
    public static function getIdFilter($id, $viewerId, $ownerId) {
        if ($id === 'VIEWER') {
            return XN_Filter('title', '=', $viewerId);
        } else if ($id === 'OWNER') {
            return XN_Filter('title', '=', $ownerId);
        } else if ($id === 'VIEWER_FRIENDS') {
            return XN_Filter('contributorName', 'in', XN_Query::FRIENDS($viewerId));
        } else if ($id === 'OWNER_FRIENDS') {
            return XN_Filter('contributorName', 'in', XN_Query::FRIENDS($ownerId));
        } else {
            return XN_Filter('title', '=', $id);
        }
    }

    /**
     * Determine if the specified $id represents a request for the details of the anonymous user.
     *
     * @param   $id         string      screenName to search for, OpenSocial_PersonHelper::ANONYMOUS for anonymous, or one of VIEWER, OWNER, VIEWER_FRIENDS, OWNER_FRIENDS.
     * @param   $viewerId   string      screenName of the current app viewer.
     * @param   $ownerId    string      screenName of the current app owner.
     * @return              boolean     true if this is a request for the anonymous user, false otherwise.
     */
    public static function requestForAnonymous($id, $viewerId, $ownerId) {
        return (($id == OpenSocial_PersonHelper::ANONYMOUS) || ($id === "VIEWER" && $viewerId == OpenSocial_PersonHelper::ANONYMOUS) || ($id == "OWNER" && $ownerId == OpenSocial_PersonHelper::ANONYMOUS));
    }

    /**
     * Determine if the specified $id represents a request for the details of the anonymous user's friends or not.
     *
     * @param   $id         string      screenName to search for, OpenSocial_PersonHelper::ANONYMOUS for anonymous, or one of VIEWER, OWNER, VIEWER_FRIENDS, OWNER_FRIENDS.
     * @param   $viewerId   string      screenName of the current app viewer.
     * @param   $ownerId    string      screenName of the current app owner.
     * @return              boolean     true if this is a request for the friends of the anonymous user, false otherwise.
     */
    public static function requestForAnonymousFriends($id, $viewerId, $ownerId) {
        return (($id === "VIEWER_FRIENDS" && $viewerId == OpenSocial_PersonHelper::ANONYMOUS) || ($id == "OWNER_FRIENDS" && $ownerId == OpenSocial_PersonHelper::ANONYMOUS));
    }

    /**
     * Determine if all of the non-special ids in $ids are found in $people.
     *
     * @param   $ids    array   Array of Ning screenNames.
     * @param   $people array   Array of dictionaries representing users.  Include an "id" attribute containing Ning screenName.
     * @return          boolean true if all ids are present, false otherwise.
     */
    public static function checkIds($ids, $people) {
        foreach ($ids as $id) {
            if (in_array($id, array('VIEWER', 'VIEWER_FRIENDS', 'OWNER', 'OWNER_FRIENDS', OpenSocial_PersonHelper::ANONYMOUS))) {
                continue;
            }
            $found = false;
            foreach ($people as $person) {
                if ($person['id'] == $id) {
                    $found = true;
                    break;
                }
            }
            if (! $found) { error_log("Did not find $id in results"); return false; }
        }
        return true;
    }
}
