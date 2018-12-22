<?php

XG_App::includeFileOnce('/lib/XG_Message.php');

class Video_MessagingHelper {

    public static $testing = FALSE;
    public static $testMessageRecipients = array();

    public static function uploadedToPrivateApp($profile, $title) {
        $app           = XN_Application::load();
        if (mb_strlen($title) > 0) {
            $subject = xg_text('YOUR_VIDEO_TITLED_X_COULD_NOT', $title, $app->name);
        } else {
            $subject = xg_text('YOUR_VIDEO_COULD_NOT_BE_ADDED', $app->name);
        }

        $reason = mb_strtolower(mb_substr($subject,0,1)) . mb_substr($subject, 1) . '.';

        $body = xg_text('YOU_JUST_TRIED_TO_SEND_VIDEO', $app->name);

        self::sendMessage($profile->screenName, $subject, $subject . "\n\n" . $body, $reason);
    }

    public static function conversionFailed($video) {
        $details = "";
        if (mb_strlen($video->title)) {
            $details .= xg_text('TITLE') . " " . $video->title . "\n";
        }
        if (mb_strlen($video->description)) {
            $details .= xg_text('DESCRIPTION_COLON') . " " . $video->description . "\n";
        }

        $app     = XN_Application::load();
        $subject = xg_text('VIDEO_UPLOAD_DID_NOT_COMPLETE', $app->name);
        $msg     = xg_text('YOUR_VIDEO_UPLOAD_TO_X_DID_NOT_COMPLETE_DUE_TO_A_CONVERSION_ERROR', $app->name) . "\n\n" .
                   ($details ? $details . "\n" : "") .
                   xg_text('FOR_FURTHER_ASSISTANCE_2') . "\n" .
                   "http://www.ning.com/help/feedback.html\n" .
                   xg_text('TO_HELP_TROUBLESHOOT_PROVIDE_THIS_VIDEO_ID_X', $video->id);

        self::sendMessage($video->contributorName, $subject, $msg, $reason);

        $subject = xg_text('VIDEO_TRANSCODING_FAILED_FOR_X', $video->id, $app->relativeUrl);
        $msg     = xg_text('X_UPLOADED_A_VIDEO_THAT_FAILED', Video_FullNameHelper::fullName($video->contributorName)) . "\n\n" .
                   xg_text('ERROR_MESSAGE') . " " . $video->my->reasonForConversionFailure . "\n" .
                   xg_text('APPLICATION_NAME') . " " . $app->name . "\n" .
                   xg_text('APPLICATION_ID') . " " . $app->relativeUrl . "\n\n" .
                   xg_text('VIDEO_ID') . " " . $video->id . "\n";
        if (mb_strlen($video->title)) {
            $msg .= xg_text('TITLE') . " " . $video->title . "\n";
        }
        if (mb_strlen($video->description)) {
            $msg .= xg_text('DESCRIPTION_COLON') . " " . $video->description . "\n";
        }

        $reason = mb_strtolower(mb_substr($subject,0,1)) . mb_substr($subject, 1) . '.';

        self::sendMessage('Ning', $subject, $msg, $reason);
    }

    public static function conversionSucceeded($video, $addVideoApprovalRequired) {
        $app = XN_Application::load();
        if (!$addVideoApprovalRequired || ($video->contributorName == $app->ownerName)) {
            $subject = xg_text('VIDEO_UPLOAD_COMPLETE_ON_X', $app->name);
            $msg = xg_text('YOUR_VIDEO_WAS_SUCCESSFULLY_UPLOADED_TO_X', $app->name);
            $details = "";
            if (mb_strlen($video->title)) {
                $details .= xg_text('TITLE') . " " . $video->title . "\n";
            }
            if (mb_strlen($video->description)) {
                $details .= xg_text('DESCRIPTION_COLON') . " " . $video->description;
            }
            $msg .= ($details ? "\n\n$details" : "");
            //TODO we possibly want to put an i18n-ed date in this message: "Uploaded 17 January"

            $url = self::buildUrl('video', 'show', '?id=' . $video->id);
            $anchor = xg_text('TO_VIEW_THE_VIDEO_VISIT');

            self::sendMessage($video->contributorName, $subject, $msg, $reason, $url, $anchor);
        } else {
            self::videoAwaitingApproval($video, $video->contributorName);
        }
    }

    public static function videoApproved($video) {
        self::sendWasModeratedNotification($video);
    }

    public static function videoRejected($video) {
        self::sendWasModeratedNotification($video);
    }

    public static function commentCreated($comment, $video) {
        if ($comment->contributorName == $video->contributorName) {
            return;
        }
        $app = XN_Application::load();
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $thumbUrl = ($video->my->conversionStatus != 'in progress') ? Video_VideoHelper::previewFrameUrl($video) : null;
        $opts = array('viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                      'activity' => mb_strlen($video->title) ? xg_text('USER_COMMENTED_ON_YOUR_OBJECT_TITLE_ON_X',xg_username(XN_Profile::current()),
                                $video->type, $video->title, $app->name)
                                : xg_text('USER_COMMENTED_ON_YOUR_OBJECT_ON_X',xg_username(XN_Profile::current()),
                                $video->type, $app->name),
                      'content' => $video,
                      'reason' => xg_text('SOMEBODY_COMMENTED_VIDEO_ADDED_TO_X',$app->name),
                      'url' => W_Cache::current('W_Widget')->buildUrl('video', 'show', array('id' => $video->id)),
                      'thumb' => $thumbUrl);
        XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts)->send($video->contributorName);
    }

    public static function videoAwaitingApproval($video, $contributorName) {
        self::sendModerateNotification($video, self::buildUrl('video', 'listForApproval'));
    }

    private static function buildUrl($controller, $action, $qs = NULL) {
        if (self::$testing) { return 'test'; }
        return W_Cache::current('W_Widget')->buildUrl($controller, $action, $qs);
    }

    private static function sendMessage($screenName, $subject, $body, $reason, $url = null, $anchor = null) {
        try {
            $opts = array('subject' => $subject, 'body' => $body, 'reason' => $reason);
            if (isset($url)) { $opts['url'] = $url; }
            if (isset($anchor)) { $opts['anchor'] = $anchor; }
            $msg = new XG_Message_Generic($opts);
            $msg->send($screenName, XG_Message::siteReturnAddress());
        } catch (Exception $ex) {
            // We ignore it as we cannot really do anything
            error_log("Failed to send message {$msg->summary()} to $screenName: {$ex->getMessage()}");
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
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $thumbUrl = ($content->my->conversionStatus != 'in progress') ? Video_VideoHelper::previewFrameUrl($content) : null;
        $opts = array('content' => $content,
                      'moderationUrl' => $moderationUrl,
                      'thumb' => $thumbUrl,
                      'reason' => xg_text('SOMEBODY_UPLOADED_VIDEO_TO_X', $app->name));
        XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_NEW, $opts)
                ->send($app->ownerName, TRUE /* sendToAdmins */);
    }

    /**
     * Sends a message about whether a content object has been approved or will be deleted,
     * to the person who created it.
     *
     * @param $content XN_Content|W_Content The content object that has been approved or will be deleted
     */
    public static function sendWasModeratedNotification($content) {
        if ($content->my->approved == 'N') { return; } // BAZ-3017 [Jon Aquino 2007-05-21]
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $thumbUrl = ($content->my->conversionStatus != 'in progress') ? Video_VideoHelper::previewFrameUrl($content) : null;
        $opts = array('content' => $content,
                      'thumb' => $thumbUrl);
        XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_DECISION, $opts)->send($content->contributorName);
    }

}