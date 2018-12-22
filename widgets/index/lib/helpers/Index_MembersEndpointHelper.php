<?php

/**
 * Useful functions pertaining to the /xn/members/1.0 endpoint
 */
class Index_MembersEndpointHelper {

    /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Index_MembersEndpointHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Index_MembersEndpointHelper(); }
        return self::$instance;
    }

    /**
     * Returns the XML for the given array of users.
     *
     * @param $users array  the XN_Profile objects to represent in the feed
     * @return string  the contents of the feed
     */
    public function buildFeed($profiles) {
        $userData = array();
        foreach ($profiles as $profile) {
            $userData[] = array(
                'screenName' => $profile->screenName,
                'profileUrl' => xg_absolute_url(User::profileUrl($profile->screenName)),
                'fullName' => XG_UserHelper::getFullName($profile),
                'thumbnailUrl' => XG_UserHelper::getThumbnailUrl($profile, 40, 40),
            );
        }
        return $this->buildFeedProper($userData, time());
    }

    /**
     * Returns the XML for the given array of user data.
     *
     * @param $users array  array of arrays, each with screenName, fullName, and thumbnailUrl
     * @param $time integer  Unix timestamp for the current time
     * @return string  the contents of the feed
     */
    protected function buildFeedProper($users, $time) {
        $feed = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0"><title type="text" /><xn:size>' . count($users) . '</xn:size><updated>' . xg_xmlentities(date('c', $time)) . '</updated>';
        foreach ($users as $user) {
            $feed .= '<entry>'
                . '<id>' . xg_xmlentities($user['profileUrl']) . '</id>'
                . '<xn:id>' . xg_xmlentities($user['screenName']) . '</xn:id>'
                . '<title type="text">' . xg_xmlentities($user['fullName']) . '</title>'
                . '<link href="' . xg_xmlentities($user['thumbnailUrl']) . '" rel="icon"/>'
                . '</entry>';
        }
        $feed .= '</feed>';
        return $feed;
    }

}
