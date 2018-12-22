<?php

XG_App::includeFileOnce('/lib/XG_Message.php');

/**
 * Contains utility functions for sending messages from the app relating to groups.
 */
class Groups_MessagingHelper {

    public static function groupApproved($group) {
        self::sendWasModeratedNotification($group);
    }

    /**
     * Notifies the appropriate parties that newly created groups need to be moderated.
     *
     * @param $group xn_content | w_content the group requiring moderation
     */
    public static function groupAwaitingApproval($group) {
        self::sendModerateNotification($group, self::buildUrl('admin', 'listForApproval'));
    }

    /**
     * Sends a message about a new content object that needs to be moderated,
     * to the site owner and administrators.
     *
     * @param $content XN_Content|W_Content The new content object
     * @param $moderationUrl string URL of the moderation page where the object can be approved or deleted
     */
    public static function sendModerateNotification($content, $moderationUrl) {
        $app = XN_Application::load();
        $opts = array('content' => $content,
                      'moderationUrl' => $moderationUrl,
                      'reason' => xg_text('SOMEBODY_CREATED_GROUP_ON_X', $app->name));
        XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_NEW, $opts)
                ->send($app->ownerName, TRUE /*sendToAdmins */);
    }

    /**
     * Sends a message about whether a content object has been approved or will be deleted,
     * to the person who created it.
     *
     * @param $content XN_Content|W_Content The content object that has been approved or will be deleted
     */
    public static function sendWasModeratedNotification($content) {
        if ($content->my->approved == 'N') { return; } 
        $opts = array('content' => $content);
        XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_DECISION, $opts)->send($content->contributorName);
    }
    
    /**
     * Sends an email notification to users subscribed to the group.
     *
     * @param $object  XN_Content|W_Content  The object prompting the notification
     * @param $group  XN_Content|W_Content  The Group object
     */
    public static function notifyNewActivityFollowers($object, $group) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
        $notification = new XG_Message_New_Group_Activity();
        switch ($object->type) {
            case 'Topic':
                $activityUrl = XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'topic', 'show', array('id' => $object->id), $group->my->url);
                break;
            case 'GroupMembership':
                $activityUrl = XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'user', 'list', null, $group->my->url);
                break;
            default:
                $activityUrl = XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'group', 'show', null, $group->my->url);
                break;
        }
        $notification->send(Index_NotificationHelper::contentNotificationAliasName($group) . '@lists', $object, $group, $activityUrl,
                 XG_GroupHelper::buildUrl('groups', 'group', 'show', array('unfollow' => 1), $group->my->url));
    }    
    
    
    private static function buildUrl($controller, $action, $qs = NULL) {
        return W_Cache::current('W_Widget')->buildUrl($controller, $action, $qs);
    }

}