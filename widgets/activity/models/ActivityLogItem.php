<?php

/**
 * Represents an item of the activity stream, one update on the network history log
 */
class ActivityLogItem extends W_Model {

    /**
    * The type of activity update, the possible categories are: "new content", "new comment", "profile update", "connection", "network", "ning"
    *
    * @see http://clearspace.ninginc.com/clearspace/docs/DOC-1240
    * @var XN_Attribute::STRING
    */
    public $category;

    /**
    * A second level of category description used by updates of type "network", the possible values for subcategory are: "promotion", "message", "new feature"
    *
    * @see http://clearspace.ninginc.com/clearspace/docs/DOC-1240
    * @var XN_Attribute::STRING optional
    */
    public $subcategory;

    /**
    * A link for that activity, used by gadets, part of the OpenSocial spec
    *
    * @see http://code.google.com/apis/opensocial
    * @var XN_Attribute::STRING optional
    */
    public $link;

    /**
    * Used so far only to the "new feature added" activity item, contains the name of the widget added
    *
    * @var XN_Attribute::STRING optional
    */
    public $widgetName;

    /**
     * Comma separated list of the screennames of the members involved in the update
     *
     * @var XN_Attribute::STRING optional
     */
    public $members;

    /**
     * Comma separated list of the ids of the contents involved in the update
     *
     * @var XN_Attribute::STRING optional
     */
    public $contents;

    /**
     * Comma separated list of the urlencoded titles of the contents involved in the update
     *
     * @var XN_Attribute::STRING optional
     */
    public $titles;
    
    /**
     * System attribute marking whether the Log item is available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
    * @var XN_Attribute::STRING optional
    * @rule choice 1,1
    */
    public $visibility;
    public $visibility_choices = array('all', 'friends', 'me');
    
    /**
     * "Y" indicates that this activity should be excluded from Ningbar and widget
     * searches. "Y" is the default for all activitylogitems
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');    
    

/** xn-ignore-start e40ef5265dd4213fdcbfeb0735e1b8b0 **/

    /**
     * Called before a content object is saved.
     *
     * @param $content XN_Content  The content object
     */
    public static function beforeSave($content) {
        try {
            if (self::pseudoDeleted($content)) {
                self::deleteAssociatedActivityLogItems($content->id);
            }
        } catch (Exception $e) {
            // Activity-logging exceptions should not stop processing. [Jon Aquino 2007-08-28]
        }
    }

    /**
     * Returns whether the content object should be considered deleted.
     *
     * @param XN_Content  the content object to examine
     * @return boolean  whether the attributes of the object indicate that it is equivalent to deleted
     */
    protected static function pseudoDeleted($content) {
        if ($content->my->deleted == 'Y') { return true; }
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        if (Forum_CommentHelper::isMarkedAsDeleted($content)) { return true; }
        return false;
    }

    /**
     * Called before a content object has been deleted.
     *
     * @param $object mixed  The content object, an array, or possibly some other thing if the XN_Event API changes
     */
    public static function beforeDelete($object) {
        try {
            foreach (is_array($object) ? $object : array($object) as $content) {
                if ($content instanceof XN_Content || $content instanceof W_Content) {
                    if (!in_array($content->type, self::typesForDeletionProcessing())) { continue; }
                }
                self::deleteAssociatedActivityLogItems(is_string($content) ? $content : $content->id);
            }
        } catch (Exception $e) {
            // Activity-logging exceptions should not stop processing. [Jon Aquino 2007-08-28]
        }
    }
    
    /**
     * returns an array of object types that self::beforeDelete() should be run against
     * currently this is all Bazel types apart from ActivityLogItem; we should be able to shorten this list
     * considerably since there are many included here that don't get connected with activity items.
     *
     * @return array
     */
     private static function typesForDeletionProcessing() {
         return array('Event','EventAttendee','EventCalendar','EventWidget','Category','Topic','TopicCommenterLink','Group',
                    'GroupIcon','GroupInvitationRequest','GroupMembership','BlockedContactList','Comment','ContactList','InvitationRequest',
                    'User','AudioAttachment','ImageAttachment','Playlist','Track','Note','OpenSocialAppData','Page',
                    'Album','Photo','SlideshowPlayerImage','BlogArchive','BlogPost','FriendRequestMessage','Video',
                    'VideoAttachment','VideoPlayerImage','VideoPreviewFrame','WatermarkImage', 'OpenSocialApp', 'OpenSocialAppReview');
     }

    /**
     * Deletes ActivityLogItem objects associated with the specified content object.
     *
     * @param $id string  the ID of the content object
     */
    private static function deleteAssociatedActivityLogItems($id) {
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $result = XG_ActivityHelper::getUserActivityLog(null, 0, 100, null, $id);
        foreach($result['items'] as $item){
            $contents = explode(',', $item->my->contents);
            if(count($contents) <= 1) {
                XN_Content::delete($item);
            } else {
                $contents = array_diff($contents,array($id));
                $item->my->contents = implode(',', $contents);
                $item->save();
            }
        }
    }

/** xn-ignore-end e40ef5265dd4213fdcbfeb0735e1b8b0 **/

}
XN_Event::listen('xn/content/save/before', array('ActivityLogItem', 'beforeSave'));
XN_Event::listen('xn/content/delete/before', array('ActivityLogItem', 'beforeDelete'));
