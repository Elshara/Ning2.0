<?php

XG_App::includeFileOnce('/lib/XG_Message.php');

/**
 * Contains utility functions for sending messages from the app.
 */
class Photo_MessagingHelper {
    public static function uploadedToPrivateApp($profile, $title) {
        $app           = XN_Application::load();
        if (mb_strlen($title) > 0) {
            $subject = xg_text('YOUR_PHOTO_TITLED_X_COULD_NOT',  $title, $app->name);
        } else {
            $subject = xg_text('YOUR_PHOTO_COULD_NOT_BE_ADDED', $app->name);
        }

        $reason = mb_strtolower(mb_substr($subject,0,1)) . mb_substr($subject, 1) . '.';

        $body = xg_text('YOU_JUST_TRIED_TO_SEND', $app->name);

        self::sendMessage($profile->screenName, $subject, $subject . "\n\n" . $body, $reason);
    }

    public static function photoApproved($photo) {
        self::sendWasModeratedNotification($photo);
    }

    public static function photoRejected($photo) {
        self::sendWasModeratedNotification($photo);
    }

    /**
     * Notifies the appropriate parties that the given photos need to be moderated.
     *
     * @param $photos array photos recently added (must contain at least one photo)
     * @param $contributorName string username of the person who added the photos
     */
    public static function photosAwaitingApproval($photos, $contributorName) {
        self::sendModerateNotification(reset($photos), self::buildUrl('photo', 'listForApproval'));
    }

    public static function commentCreated($comment, $photo) {
        if ($comment->contributorName == $photo->contributorName) {
            return;
        }
        $app = XN_Application::load();
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        Photo_HtmlHelper::getImageUrlAndDimensions($photo, $thumbUrl, $width, $height);
        $opts = array('viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                      'activity' => mb_strlen($photo->title) ? xg_text('USER_COMMENTED_ON_YOUR_OBJECT_TITLE_ON_X',xg_username(XN_Profile::current()),
                                $photo->type, $photo->title, $app->name)
                                : xg_text('USER_COMMENTED_ON_YOUR_OBJECT_ON_X',xg_username(XN_Profile::current()),
                                $photo->type, $app->name),
                      'content' => $photo,
                      'reason' => xg_text('SOMEBODY_COMMENTED_PHOTO_ADDED_TO_X',$app->name),
                      'url' => W_Cache::current('W_Widget')->buildUrl('photo', 'show', array('id' => $photo->id)),
                      'thumb' => $thumbUrl);
        XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts)->send($photo->contributorName);
    }

    private static function buildUrl($controller, $action, $qs = NULL) {
        return W_Cache::current('W_Widget')->buildUrl($controller, $action, $qs);
    }

    private static function sendMessage($screenName, $subject, $body, $reason) {
        try {
            $msg = new XG_Message_Generic(array('subject' => $subject, 'body' => $body, 'reason' => $reason));
            $msg->send($screenName, XG_Message::siteReturnAddress());
        } catch (Exception $ex) {
            // We ignore it as we cannot really do anything
            error_log("Failed to send message {$msg->summary} to $screenName: {$ex->getMessage()}");
        }
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
        Photo_HtmlHelper::getImageUrlAndDimensions($content, $thumbUrl, $width, $height);
        $opts = array('content' => $content,
                      'moderationUrl' => $moderationUrl,
                      'thumb' => $thumbUrl,
                      'reason' => xg_text('SOMEBODY_UPLOADED_PHOTO_TO_X', $app->name));

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
        if ($content->my->approved == 'N') { return; } // BAZ-3017 [Jon Aquino 2007-05-21]
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        Photo_HtmlHelper::getImageUrlAndDimensions($content, $thumbUrl, $width, $height);
        $opts = array('content' => $content,
                      'thumb' => $thumbUrl);
        XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_DECISION, $opts)->send($content->contributorName);
    }

}