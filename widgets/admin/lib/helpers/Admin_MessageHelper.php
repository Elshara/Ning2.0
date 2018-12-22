<?php

/**
 * Utility functions for testing messaging.
 */
class Admin_MessageHelper {
    protected static function content($type) { # void
        return reset(XN_Query::create('Content')->filter('owner')->filter('type', '=', $type)->execute());
    }

    public function getAllTypes () { # list<string>
        // grep for "case 'label':"
        preg_match_all('/^\s+case\s+\'([^\']+)\':/m',file_get_contents(__FILE__), $m, PREG_PATTERN_ORDER);
        return $m[1];
    }

    /**
     *  Display rendered message for debug purposes. Message is display in 3 formats: text, html and html source.
     *
     *  @return     void
     */
    protected static function _displayMessageDebug($headers, $textBody, $htmlBody) {
        $id_html = md5(uniqid());
        $id_text = md5(uniqid());
        $id_src = md5(uniqid());
        $id_header = md5(uniqid());

        echo 'Headers: <a href=# onclick="tgl(\''.$id_header.'\',\'\');return false">show</a> | <a href=# onclick="tgl(\''.$id_header.'\',\'none\');return false">hide</a>';
        echo '<div id="'.$id_header.'" style="display:none">';
        foreach($headers as $k=>$v) {
            echo "$k: ",xnhtmlentities($v),"<br>\n";
        }
        echo '</div>';

        $titles = array();
        $bodies = array();
        if ($htmlBody) {
            $titles[] = "<a href=# onclick=\"tgl('$id_src','none');tgl('$id_html','');tgl('$id_text','none');return false\">HTML</a>";
            $bodies[] = '<div id="'.$id_html.'" style="display:none">'.$htmlBody.'</div>';

            $titles[] = "<a href=# onclick=\"tgl('$id_src','');tgl('$id_html','none');tgl('$id_text','none');return false\">HTML(src)</a>";
            $bodies[] = '<pre id="'.$id_src.'" style="display:none">'.xnhtmlentities($htmlBody)."</pre>";
        }
        if ($textBody) {
            $titles[] = "<a href=# onclick=\"tgl('$id_src','none');tgl('$id_html','none');tgl('$id_text','');return false\">TEXT</a>";
            $bodies[] = '<pre id="'.$id_text.'" style="display:none">'.xnhtmlentities($textBody)."</pre>";
        }
        echo "<br>", join(" | ", $titles), "<br>", join('', $bodies);
        echo "<script>function tgl(id,v){var el = document.getElementById(id);if(el)el.style.display=v};tgl('".($htmlBody?$id_html:$id_text)."','')</script>";
    }

    /**
     *  Send message
     *
     *	@param		$type		string			Message type id. Usually it's the name of template + optional :subtype (like :video/:photo/etc)
     *	@param		$cmd		string			send|display
     *  @param      $opts		hash:
     *  							format: text|html|combined
     *  							custom_msg: bool
     *  							count_queries: bool
     *  							save_msgs: bool
     *  							sparse:bool
     *  @return     void
     */
    public static function sendMessage ($type, $cmd, $options) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_MessagingHelper.php');
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_MessagingHelper.php');
        EventWidget::init();
        // format
        switch($options['format']) {
            case 'text': W_Cache::getWidget('main')->privateConfig['ignoreForceHtml'] = '1'; W_Cache::getWidget('main')->privateConfig['sendHtmlMessages'] = 'N'; break;
            case 'html': W_Cache::getWidget('main')->privateConfig['ignoreForceHtml'] = '1'; W_Cache::getWidget('main')->privateConfig['sendHtmlMessages'] = 'Y'; break;
            case 'combined': W_Cache::getWidget('main')->privateConfig['ignoreForceHtml'] = '0'; W_Cache::getWidget('main')->privateConfig['sendHtmlMessages'] = 'Y'; break;
            default: throw new Exception("Invalid format #$format;");
        }
        XG_Message::$allowCaching = false;
        XG_Message::$storeInsteadOfSend = ($cmd == 'display');
        XG_Message::$storedMessages = array();
        XG_Message::$saveMessages = $options['save_msgs'];
        XG_MessageHelper::$forceSparse = $options['sparse'];
		$customBody = (bool)$options['custom_msg'] ?
						"Lorem ipsum\ndolor sit amet, <script>alert('hack!')</script> consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut."
						: '';
        if ($options['count_queries']) {
            #!! install request/after handler
        }
        switch($type) {
            case 'accepted':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_PENDING_ACCEPTED, array('profile' => XN_Profile::current()))->send(XN_Profile::current()->screenName);
                break;
            case 'broadcast':
                $msg = new XG_Message_Broadcast('Broadcast Test Subject', $customBody, XN_Profile::current());
                $msg->send(XN_Profile::current()->screenName, XG_Message::siteReturnAddress());
                break;
            case 'old-email-address':
                $msg = new XG_Message_ChangedEmailAddress();
                $msg->send(XN_Profile::current()->email, XN_Profile::current()->email);
                break;
            case 'feedback':
                $opts = array('body' => $customBody, 'feedbackSenderName' => XG_UserHelper::getFullName(XN_Profile::current()),
                      'heading' => xg_text('X_PROVIDED_THE_FOLLOWING_FEEDBACK_ON_Y', XG_UserHelper::getFullName(XN_Profile::current()), XN_Application::load()->name));
                XG_Message_Feedback::create($opts)->send(XN_Profile::current()->screenName);
                break;
            case 'user-message':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_USER_MESSAGE, array('body' => $customBody, 'profile' => 'NingDev'))->send('NingDev');
                break;
            case 'welcome':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_WELCOME, array('profile' => XN_Profile::current()))->send(XN_Profile::current());
                break;
            case 'group-invitation':
                if (!$group = self::content('Group')) { echo '<p>Warning: No Groups found</p>'; break; }
                $opts = array('subject' => 'Group Invitation Test Title', 'body' => $customBody, 'url' => xg_absolute_url('/'));
                $msg = new XG_Message_Group_Invitation($opts);
                $msg->send(XN_Profile::current()->screenName, XN_Profile::current()->screenName, $group);
                break;
            case 'report-this':
                $opts = array('category' => 'Bug', 'body' => $customBody, 'url' => $_SERVER['HTTP_REFERER']);
                XG_Message_ReportThis::create($opts)->send(XN_Profile::current()->screenName);
                break;
            case 'request-group-invitation':
                if ($group = self::content('Group') ) { XG_Message_Request_Group_Invitation::create()->send($group, 'Mr. T', XN_Profile::current()->screenName, 'Request Group Invitation Test Message'); }
                break;
            case 'friend-request':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_FRIEND_REQUEST, array('profile' => XN_Profile::current(), 'body' => $customBody))->send(XN_Profile::current()->screenName);
                break;
            case 'from-banned':
                $msg = XG_Message_From_Banned::create(array('body' => $customBody));
                $msg->send(XN_Profile::current());
                break;
            case 'group-broadcast':
                if (!$group = self::content('Group')) { break; }
                $msg = new XG_Message_Group_Broadcast('Group Broadcast Test Subject', $customBody, XN_Profile::current(), $group);
                $msg->send(XN_Profile::current()->screenName);
                break;
            case 'group-welcome':
                if (!$group = self::content('Group')) { break; }
                XG_Message_Notification::create(XG_Message_Notification::EVENT_GROUP_WELCOME, array('profile' => XN_Profile::current(), 'group' => $group))->send(XN_Profile::current()->screenName);
                break;
            case 'invitation':
                $opts = array('subject' => xg_text('COME_JOIN_ME_ON_X', XN_Application::load()->name), 'url' => xg_absolute_url('/'), 'body' => $customBody);
                $msg = new XG_Message_Invitation($opts);
                $msg->send(XN_Profile::current()->screenName, XN_Profile::current()->screenName, false);
                break;
            case 'invitee-join':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_JOIN, array('joiner' => XN_Profile::current()))->send(XN_Profile::current()->screenName);
                break;
            case 'message':
                break;
            case 'new-topic':
                if (!$topic = self::content('Topic')) { break; }
                $notification = new XG_Message_New_Topic();
                $notification->send(XN_Profile::current()->screenName, $topic, XG_GroupHelper::buildUrl('forum', 'topic', 'show', array('id' => $topic->id)), XG_GroupHelper::buildUrl('forum', 'index', 'index'));
                break;
            case 'notify-activity:profile':
                $commentReason = xg_text('X_ADDED_A_COMMENT_TO_YOUR_PAGE_ON_Y', xg_username(XN_Profile::current()), XN_Application::load()->name);
                $opts = array(
                    'viewActivity' => xg_text('TO_VIEW_YOUR_NEW_COMMENT_VISIT'),
                    'activity' => mb_strtoupper(mb_substr($commentReason,0,1)) . mb_substr($commentReason,1),
                    'content' => User::load(XN_Profile::current()),
                    'type' => null,
                    'subject' => xg_text('X_ADDED_A_COMMENT_TO_YOUR_PAGE_ON_Y', xg_username(XN_Profile::current()), XN_Application::load()->name),
                    'url' => 'http://test.test',
                    'reason' => xg_text('X_ADDED_A_COMMENT_TO_YOUR_PAGE_ON_Y', xg_username(XN_Profile::current()), XN_Application::load()->name));
                XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts)->send(XN_Profile::current()->screenName);
                break;
            case 'notify-activity:photo':
                if (!$photo = self::content('Photo')) { break; }
                $app = XN_Application::load();
                Photo_HtmlHelper::getImageUrlAndDimensions($photo, $thumbUrl, $width, $height);
                $opts = array(
                        'viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                        'activity' => mb_strlen($photo->title) ? xg_text('USER_COMMENTED_ON_YOUR_OBJECT_TITLE_ON_X',xg_username(XN_Profile::current()),
                                    $photo->type, $photo->title, $app->name)
                                    : xg_text('USER_COMMENTED_ON_YOUR_OBJECT_ON_X',xg_username(XN_Profile::current()),
                                    $photo->type, $app->name),
                        'content' => $photo,
                        'reason' => xg_text('SOMEBODY_COMMENTED_PHOTO_ADDED_TO_X', XN_Application::load()->name),
                        'url' => W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id)),
                        'thumb' => $thumbUrl);
                XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts)->send(XN_Profile::current()->screenName);
                break;
            case 'notify-activity:video':
                if (!$video = self::content('Video')) { break; }
                $thumbUrl = ($video->my->conversionStatus != 'in progress') ? Video_VideoHelper::previewFrameUrl($video) : null;
                $opts = array(
                    'viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                    'activity' => mb_strlen($video->title) ? xg_text('USER_COMMENTED_ON_YOUR_OBJECT_TITLE_ON_X',xg_username(XN_Profile::current()),
                                $video->type, $video->title, $app->name)
                                : xg_text('USER_COMMENTED_ON_YOUR_OBJECT_ON_X',xg_username(XN_Profile::current()),
                                $video->type, $app->name),
                    'content' => $video,
                    'reason' => xg_text('SOMEBODY_COMMENTED_VIDEO_ADDED_TO_X', XN_Application::load()->name),
                    'url' => W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id)),
                    'thumb' => $thumbUrl);
                XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts)->send(XN_Profile::current()->screenName);
                break;
            case 'notify-activity:forum':
                if (!$topic = self::content('Topic')) { break; }
                $opts = array(
                    'activity' => xg_text('USER_REPLIED_TO_DISCUSSION_TITLE_ON_APPNAME', xg_username(XN_Profile::current()), $topic->title, $app->name),
                    'content' => $topic,
                    'url' => 'http://test.test',
                    'viewActivity' => xg_text('TO_VIEW_THE_NEW_REPLY_VISIT'),
                    'unfollowLink' => W_Cache::getWidget('forum')->buildUrl('topic', 'show', array('id' => $topic->id, 'unfollow' => '1')),
                    'type' => mb_strtolower(xg_text('DISCUSSION')));
                XG_Message_Notification::create(XG_Message_Notification::EVENT_FOLLOW_ACTIVITY, $opts)->send('test@lists');
                break;
            case 'notify-activity:blog':
                if (!$topic = self::content('Topic')) { break; }
                $opts = array(
                    'content' => $topic,
                    'type' => 'blog post',
                    'url' => 'http://test.test',
                    'viewActivity' => xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT'),
                    'activity' => xg_text('X_ADDED_A_COMMENT_TO_THE_BLOG_POST_Y_ON_Z',  xg_username(XN_Profile::current()), $topic->title, $app->name),
                    'unfollowLink' => XG_HttpHelper::addParameter('http://test.test', 'unfollow', '1'));
                XG_Message_Notification::create(XG_Message_Notification::EVENT_FOLLOW_ACTIVITY, $opts)->send('test@lists');
                break;
            case 'notify-moderate-decision:photo':
                if ($photo = self::content('Photo')) { Photo_MessagingHelper::sendWasModeratedNotification($photo); }
                break;
            case 'notify-moderate-decision:video':
                if ($video = self::content('Video')) { Video_MessagingHelper::sendWasModeratedNotification($video); }
                break;
            case 'notify-moderate-member':
                XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_MEMBER, array('joiner' => XN_Profile::current()))->send(XN_Application::load()->ownerName, false);
                break;
            case 'notify-moderate:photo':
                if ($photo = self::content('Photo')) { Photo_MessagingHelper::sendModerateNotification($photo, 'http://test.test'); }
                break;
            case 'notify-moderate-video':
                if ($video = self::content('Video')) { Video_MessagingHelper::sendModerateNotification($video, 'http://test.test'); }
                break;
            case 'share:video':
            case 'share:photo':
            case 'share:photo album':
            case 'share:discussion':
            case 'share:blog post':
            case 'share:profile':
			case 'share:URL':
                list(,$rawType) = explode(':',$type);
                $msg = new XG_Message_Invitation_Share(
                    "Share $rawType Test Subject",
                    $customBody,
                    array(
                        'description' => 'Aliquam, eum te laoreet ea volutpat, exerci esse facilisis iusto commodo',
                        'share_url' => "http://test.test/$rawType",
                        'share_title' => 'Test Content',
                        'share_thumb' => 'http://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Adams_The_Tetons_and_the_Snake_River.jpg/96px-Adams_The_Tetons_and_the_Snake_River.jpg',
                        'share_type' => ucfirst($rawType),
                        'share_raw_description' => 'Aliquam, eum te laoreet ea volutpat, exerci esse facilisis iusto commodo',
                        'share_content_author' => XN_Profile::current()->screenName,
                        'share_raw_type' => $rawType,
                ));
                $msg->send(XN_Profile::current()->screenName, XN_Profile::current()->screenName);
                break;
            case 'friend-accepted':
                break;
            case 'event-invitation':
                if (!$event = self::content('Event')) { break; }
                $msg = new XG_Message_Event_Invitation(array(
                    'subject' => "Share Event",
                    'body' => $customBody,
                    'url' => 'http://test.test',
                ));
                $msg->send(XN_Profile::current()->screenName, XN_Profile::current()->screenName, $event);
                break;
        }
        if ($opts['count_queries']) {
            #!! uninstall request/after handler
        }
        if ($cmd=='display') {
            if (XG_Message::$storedMessages) {
                $msg = XG_Message::$storedMessages[0];
                self::_displayMessageDebug($msg['headers'], $msg['text_body'], $msg['html_body']);
            } else {
                echo '<div style="background-color:#F00;color:#000">NO MESSAGE</div>';
            }
            XG_Message::$storedMessages = array();
        } else {
            echo 'ok';
        }
    }
}
