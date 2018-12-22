<?php
/* $Id: $
 *
 */
abstract class XG_MessageHelper {
    static public $forceSparse = NULL;
    protected $msg, $message;

    public function __construct(XG_Message $msg, array &$message) {
        $this->msg = $msg;
        $this->message =& $message;
    }
    abstract public function header();
    abstract public function delimiter();
    abstract public function unsubscribe();
    abstract public function aboutNetwork($sparse);
    abstract public function userName($screenName);
    abstract public function shortUserName($screenName);

    //
    public static function getUserCounters($screenName) { # hash
        if (!$user = User::loadMultiple($screenName)) {
            return array();
        }
        $eventCount = 0;
        if (!$user->my->viewEventsPermission || $user->my->viewEventsPermission == 'all') {
            EventWidget::init();
            $eventCount = EventAttendee::getUpcomingEvents($screenName, 1, true)->totalCount;
        }
        $archive = $user->my->xg_profiles_blogPostArchive ? unserialize($user->my->xg_profiles_blogPostArchive) : array();
        $postCount = 0;
        foreach((array)$archive['all'] as $year) {
            $postCount += array_sum($year);
        }
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CachedCountHelper.php');
        $friendCount = Profiles_CachedCountHelper::instance()->getApproximateNumberOfFriendsOnNetworkFor($screenName);
        $counters = array_filter( array(
            $friendCount ? xg_text('N_FRIENDS', $friendCount) : '',
            $user->my->xg_photo_photoCount ? xg_text('N_PHOTOS_2', $user->my->xg_photo_photoCount) : '',
            $eventCount ? xg_text('N_EVENTS', $eventCount) : '',
            $user->my->xg_video_videoCount ? xg_text('N_VIDEOS_2', $user->my->xg_video_videoCount) : '',
            $user->my->xg_forum_activityCount ? xg_text('N_DISCUSSIONS', $user->my->xg_forum_activityCount) : '',
            $postCount ? xg_text('N_BLOG_POSTS', $postCount) : '',
            $user->my->xg_music_trackCount ? xg_text('N_MUSIC', $user->my->xg_music_trackCount) : '',
        ) );
        return $counters;
    }

    /**
     *  Returns message parts that are common for all emails.
     *  Used for checking messages for spam.
     *  In the hash that is returned keys are names used to display to user.
     *
     *  @return     hash
     */
    public static function getDefaultMessageParts() {
        return !XG_SecurityHelper::userIsAdmin() ? array() : array(
            xg_html('NETWORK_NAME') => XN_Application::load()->name,
            xg_html('NETWORK_TAGLINE') => XG_MetatagHelper::appTagline(),
            xg_html('NETWORK_DESCRIPTION') => XG_MetatagHelper::appDescription(),
        );
    }

    /**
     *  Initialize "about network" common block. Returns the list of related variables
     *
     *  @return     hash
     */
    public static function initAboutNetwork() { # hash
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $modules = XG_ModuleHelper::getEnabledModules();
        $types = XN_Query::create('Content_Count')->filter('owner')->rollup('type')->execute();
        $data = array();
        $data['enableImages'] = true;
        if ($response = @simplexml_load_string(XN_REST::get('/xn/atom/1.0/application/ranking'))) {
            $data['enableImages'] = ($response->entry[0]->children('xn',true)->adult != 'true');
        }
        $data['sparse'] = self::$forceSparse === NULL ? ($types['User'] < 5) : self::$forceSparse;
        if ($data['sparse']) {
            $data['net_features'] = array(xg_text('BLOGS'));

            $subset = array('music'=>xg_text('MUSIC'), 'forum'=>xg_text('DISCUSSIONS'), 'photo'=>xg_text('PHOTOS'),
                'video'=>xg_text('VIDEOS'), 'groups'=>xg_text('GROUPS'), 'events' => xg_text('EVENTS'));
            foreach ($modules as $k=>$v) {
                if (isset($subset[$v->root])) {
                    $data['net_features'][] = $subset[$v->name];
                }
            }
        } else {
            $threshold = 5;
            $userinfo = User::find(array(),0,1);
            $numUsers = $userinfo['numUsers'];
            $data['net_counters'] = array_filter( array(
                $numUsers > $threshold ? xg_text('N_MEMBERS', $numUsers) : '',
                $modules['photo'] && $modules['photo']->root == 'photo' && $types['Photo'] > $threshold ? xg_text('N_PHOTOS_2', $types['Photo']) : '',
                $modules['music'] && $modules['music']->root == 'music' && $types['Track'] > $threshold ? xg_text('N_MUSIC', $types['Track']) : '',
                $modules['video'] && $modules['video']->root == 'video' && $types['Video'] > $threshold ? xg_text('N_VIDEOS_2', $types['Video']) : '',
                $modules['forum'] && $modules['forum']->root == 'forum' && $types['Topic'] > $threshold ? xg_text('N_DISCUSSIONS', $types['Topic']) : '',
                $modules['events'] && $modules['events']->root == 'events' && $types['Event'] > $threshold ? xg_text('N_EVENTS', $types['Event']) : '',
                $types['BlogPost'] > $threshold ? xg_text('N_BLOG_POSTS', $types['BlogPost']) : '',
            ) );
        }
        return $data;
    }
}

class XG_MessageHelperText extends XG_MessageHelper {
    public function header() { # void
        echo $this->message['appName'], ": ",$this->message['appTagline'],"\n";
    }

    public function delimiter() { # void
        echo "--------------------","\n";
        echo "\n";
    }

    public function unsubscribe() { # void
        echo xg_text('TO_CONTROL_WHICH_EMAILS', $this->message['appName']) . "\n";
        echo $this->message['unsubscribeUrl'];
    }

    public function aboutNetwork($sparse) { # void
        if ( !($sparse ? $this->msg->net_features : $this->msg->net_counters) && trim($this->message['appDescription']) == '') {
            return;
        }
        $this->delimiter();
        echo xg_text('ABOUT_X', $this->message['appName']),"\n";
        echo xg_excerpt($this->message['appDescription'],250),"\n";
        echo "\n";
        if ($sparse) {
            if ($this->msg->net_features) {
                echo xg_text('X_INCLUDES_COLON', $this->message['appName']),"\n";
                foreach ($this->msg->net_features as $name) {
                    echo $name,"\n";
                }
                echo "\n";
            }
        } else {
            foreach ($this->msg->net_counters as $name) {
                echo $name,"\n";
            }
            echo "\n";
        }
    }
    public function userName($screenName) {
        return xg_username($screenName);
    }
    public function shortUserName($screenName) {
        return xg_excerpt(xg_username($screenName),36);
    }
}


class XG_MessageHelperHtml extends XG_MessageHelper {
    //
    public function header() { # void
?>
<table cellpadding="0" cellspacing="0" border="0" width="600">
    <tr>
        <td align="left" bgcolor="#<%=$this->message['cssDefaults']['moduleHeadBgColor']%>" height="44" valign="middle" style="padding-left:12px; color:#<%=$this->message['cssDefaults']['moduleHeadTextColor']%>;">
            <div style="font-weight:bold; font-size:18px;"><%=xnhtmlentities($this->message['appName'])%></div>
            <div style="font-size:12px;"><%=xnhtmlentities($this->message['appTagline'])%></div>
        </td>
    </tr>
</table>
<?php
    }

    public function delimiter() { # void
?>
<div style="border-bottom:1px solid #aaa; height:10px;">&nbsp;</div>
<?php
    }

    public function unsubscribe() { # void
?>
<div style="color:#777777; font-size:11px; padding-top: 5px;">
    <%=xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_CLICK_HERE', qh($this->message['appName']), 'href="'.qh($this->message['unsubscribeUrl']).'"')%>
</div>
<?php
    }

    //
    public function userUrl($from) { # void
        $userUrl = xnhtmlentities(xg_absolute_url(User::quickProfileUrl($from->screenName)));
?>
    <a href="<%=$userUrl%>"><img height="96" width="96" border="0" alt="<%=xnhtmlentities(xg_excerpt(xg_username($from->screenName),14))%>" src="<%=$this->msg->addImageByUrl(xg_url(XG_UserHelper::getThumbnailUrl($from,96,96), 'xn_auth=no'))%>"></a>
    <div style="padding-bottom:6px;text-align:center;font-size:12px"><a href="<%=$userUrl%>" style="text-decoration:none"><%=$this->shortUserName($from->screenName)%></a></div>
<?php
    }

    //
    public function aboutNetwork($sparse) { # void
        $appName = xnhtmlentities($this->message['appName']);
        if ( !($sparse ? $this->msg->net_features : $this->msg->net_counters) && trim($this->message['appDescription']) == '') {
            return;
        }
?>
      <div style="font-weight:bold; padding:8px 0;border-top:1px solid #aaa;"><%=xg_html('ABOUT_X',$appName)%></div>
      <?php if ($this->message['appDescription']) { ?>
        <div style="padding-bottom:12px"><%=xnhtmlentities(xg_excerpt($this->message['appDescription'],250))%></div>
      <?php } ?>
      <?php $tdstyle = "style=\"padding-right:10px; font-size: 12px;\"";
            if (($sparse && $this->msg->net_features) || (!$sparse && $this->msg->net_counters)) { ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
                <?php if ($this->msg->enableImages) {?>
                <td width="74" <%=$tdstyle%> valign="top">
                    <a href="<%=xnhtmlentities($this->msg->url)%>"><img height="64" width="64" border="0" alt="<%=$appName%>" src="<%=$this->msg->addImageByUrl(XN_Application::load()->iconUrl(96,96))%>"></a>
                </td>
                <?php }?>
                <td <%=$tdstyle%> valign="top">
                <?php if ($sparse) {?>
                    <?php $half = ceil(count($this->msg->net_features)/2);
                        if ($this->msg->net_features) {?>
                        <?php $i = 0; foreach($this->msg->net_features as $name) {
                            if ($i == $half) { echo '</td><td '.$tdstyle.' valign="top">'; }
                            echo $name, "<br/>";
                            $i++;
                        }?>
                    <?php }?>
                <?php } else {?>
                    <?php $half = ceil(count($this->msg->net_counters)/2);
                        if ($this->msg->net_counters) {?>
                        <?php $i = 0; foreach($this->msg->net_counters as $name) {
                            if ($i == $half) { echo '</td><td '.$tdstyle.' valign="top">'; }
                            echo $name, "<br/>";
                            $i++;
                        }?>
                    <?php }?>
                <?php }?>
                </td>
            </tr>
        </table>
    <?php } ?>
<?php
    }
    public function userName($screenName) {
        return xnhtmlentities( xg_username($screenName) );
    }
    public function shortUserName($screenName) {
        return nl2br( xg_excerpt( XG_LangHelper::htmlwrap( xnhtmlentities( xg_username($screenName) ), 13, "-\n" ), 36) );
    }
}
