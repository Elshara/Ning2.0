<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ActivityHelper.php');
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class Index_ActivityController extends W_Controller {


    public function action_edit(){
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('save');
            return;
        }

        //Display Preferences checkbox values
        $this->logNewContentChecked     = XG_App::logNewContent();
        $this->logNewCommentsChecked    = XG_App::logNewComments();
        $this->logFriendshipsChecked    = XG_App::logFriendships();
        $this->logNewMembersChecked     = XG_App::logNewMembers();
        $this->logProfileUpdatesChecked = XG_App::logProfileUpdates();
		$this->logNewEventsChecked		= XG_App::logNewEvents();
		$this->logOpenSocialChecked		= XG_App::logOpenSocial();

        //FACTS DROPDOWN
        XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');

        $enabledModules = XG_ModuleHelper::getEnabledModules();
        $app                            = XN_Application::load();
        $appName                        = $app->name;
        $enabledModules                 = XG_ModuleHelper::getEnabledModules();
        $this->hasActivityFeature       = ($enabledModules['activity']!=null);
        $optiongroups                   = array();

        $optiongroups[xg_text('NETWORK')]   = array();

        //rollup query to get different counts
        $query = XN_Query::create('Content_Count')->filter('owner')->rollup('type');
        // get the user count directly since otherwise it would include banned members
        $users = User::find(array(),0,1);
        $membersCount = $users['numUsers'];
        $types = $query->execute();
        foreach ($types as $type => $count) {
            switch ($type){
                case 'Photo':       $photoCount     = $count; break;
                case 'Video':       $videoCount     = $count; break;
                case 'Track':       $trackCount     = $count; break;
                case 'Topic':       $topicCount     = $count; break;
                case 'BlogPost':    $blogCount      = $count; break;
                case 'Group':       $groupCount     = $count; break;
				case 'Event':		$eventCount		= $count; break;
				case 'Note':		$noteCount		= $count; break;
                default: break;
            }
        }

        //total number of members
        if($membersCount>1) $optiongroups[xg_text('NETWORK')][] = array(
                                'label' => xg_text('THERE_ARE_X_MEMBERS_ON_APPNAME', $membersCount, $appName),
								'html' => xg_html('THERE_ARE_X_LINK_MEMBERS_ON_APPNAME', $membersCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('members', '')) ),
                                'type'  => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE );

        //new users in the last week
        $query = XN_Query::create('Content')->filter('owner')->end(1)->alwaysReturnTotalCount(true)->filter('type', '=', 'User');
        $query->filter('createdDate', '>=', date('Y-m-d\TH:i:s\Z',strtotime('-7 days')));
        $lastWeekUsersQuery = $query->execute();
        $lastWeekUsers = $query->getTotalCount();

        if($lastWeekUsers > 0) $optiongroups[xg_text('NETWORK')][] = array(
                                'label'     => xg_text('X_NEW_MEMBERS_JOINED_PAST_WEEK', $lastWeekUsers),
                                'html'      => xg_html('X_NEW_LINK_MEMBERS_JOINED_PAST_WEEK', $lastWeekUsers, xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('members', '')) ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE );

        //photo
        if($enabledModules['photo']!=null) {
            $optiongroups[xg_text('PHOTOS')]      = array();

            //user with most photos
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'User')->end(1);
            $query->filter('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('photo'), 'photoCount'), '<>', NULL);
            $query->order('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('photo'), 'photoCount'), 'desc', XN_Attribute::NUMBER);
            $photoChampions = $query->execute();
            $photoChampion  = XG_FullNameHelper::fullName($photoChampions[0]->contributorName);

            if(count($photoChampions) > 0)  $optiongroups[xg_text('PHOTOS')][]  = array(
                                'label'     => xg_text('X_HAS_POSTED_THE_MOST_PHOTOS', $photoChampion),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_PHOTO_CHAMPION,
                                'content'   => $photoChampions[0]->contributorName );

            //most popular photo
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Photo')->end(1);
            $query->filter('my->popularityCount', '<>', NULL);
            $query->order('my->popularityCount', 'desc', XN_Attribute::NUMBER);
            $popularPhotos = $query->execute();

            if(count($popularPhotos) > 0)   $optiongroups[xg_text('PHOTOS')][]  = array(
                                'label'     => xg_text('X_IS_THE_MOST_POPULAR_PHOTO', xnhtmlentities($popularPhotos[0]->title?xg_excerpt($popularPhotos[0]->title,20):xg_text('UNTITLED'))),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_TOP_PHOTO,
                                'content'   => $popularPhotos[0]->id );

            //photo count
            if($photoCount > 0) $optiongroups[xg_text('PHOTOS')][]  = array(
                                'label'     => xg_text('THERE_ARE_X_PHOTOS_ON_APPNAME', $photoCount, $appName ),
                                'html'      => xg_html('THERE_ARE_X_LINK_PHOTOS_ON_APPNAME', $photoCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo','index')) ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
        }

        //video
        if($enabledModules['video']!=null) {
            $optiongroups[xg_text('VIDEOS')]      = array();

            //user with most videos
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'User')->end(1);
            $query->filter('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('video'), 'videoCount'), '<>', NULL);
            $query->order('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('video'), 'videoCount'), 'desc', XN_Attribute::NUMBER);
            $videoChampions = $query->execute();
            $videoChampion  = XG_FullNameHelper::fullName($videoChampions[0]->contributorName);

            if(count($videoChampions) > 0)  $optiongroups[xg_text('VIDEOS')][]  = array(
                                'label'     => xg_text('X_HAS_POSTED_THE_MOST_VIDEOS', $videoChampion),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_VIDEO_CHAMPION,
                                'content'   => $videoChampions[0]->contributorName );

            //most popular video
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Video')->end(1);
            $query->filter('my->popularityCount', '<>', NULL);
            $query->order('my->popularityCount', 'desc', XN_Attribute::NUMBER);
            $popularVideos = $query->execute();

            if(count($popularVideos) > 0)   $optiongroups[xg_text('VIDEOS')][]  = array(
                                'label'     => xg_html('X_IS_THE_MOST_POPULAR_VIDEO', $popularVideos[0]->title?xg_excerpt($popularVideos[0]->title,20):xg_text('UNTITLED')),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_TOP_VIDEO,
                                'content'   => $popularVideos[0]->id );

            //video count
            if($videoCount > 0) $optiongroups[xg_text('VIDEOS')][]  = array(
                                'label'     => xg_text('THERE_ARE_X_VIDEOS_ON_APPNAME', $videoCount, $appName ),
                                'html'      => xg_html('THERE_ARE_X_LINK_VIDEOS_ON_APPNAME', $videoCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video','index')) ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
        }

        //music
        if($enabledModules['music']!=null) {
            $optiongroups[xg_text('MUSIC')]       = array();

            //user with most tracks
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'User')->end(1);
            $query->filter('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('music'), 'trackCount'), '<>', NULL);
            $query->order('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('music'), 'trackCount'), 'desc', XN_Attribute::NUMBER);
            $musicChampions = $query->execute();
            $musicChampion  = XG_FullNameHelper::fullName($musicChampions[0]->contributorName);

            if(count($musicChampions) > 0)  $optiongroups[xg_text('MUSIC')][]   = array(
                                'label'     => xg_text('X_HAS_POSTED_THE_MOST_MUSIC', $musicChampion),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MUSIC_CHAMPION,
                                'content'   => $musicChampions[0]->contributorName );

            //most popular video
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Track')->end(1);
            $query->filter('my->popularityCount', '<>', NULL);
            $query->order('my->popularityCount', 'desc', XN_Attribute::NUMBER);
            $popularTracks = $query->execute();

            if(count($popularTracks) > 0)   $optiongroups[xg_text('MUSIC')][]   = array(
                                'label'     => xg_text('X_IS_THE_MOST_POPULAR_MUSIC', xnhtmlentities($popularTracks[0]->title?xg_excerpt($popularTracks[0]->title,20):xg_text('UNTITLED'))),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_TOP_MUSIC,
                                'content'   => $popularTracks[0]->id);

            //music count
            if($trackCount > 0) $optiongroups[xg_text('MUSIC')][]   = array(
                                'label'     => xg_text('THERE_ARE_X_SONGS_ON_APPNAME', $trackCount, $appName ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
        }

        //forum
        if($enabledModules['forum']!=null) {
            $optiongroups[xg_text('FORUM')]       = array();

            //topic with most replies
            $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->end(1);
            $query->filter('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('forum'), 'commentCount'), '<>', NULL);
            $query->order('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('forum'), 'commentCount'), 'desc', XN_Attribute::NUMBER);
            $topTopics = $query->execute();
            $topTopic  = $topTopics[0];

            if(count($topTopics) > 0)   $optiongroups[xg_text('FORUM')][]   = array(
                                'label'     => xg_text('X_IS_THE_MOST_POPULAR_TOPIC', xnhtmlentities($topTopic->title?xg_excerpt($topTopic->title,20):xg_text('UNTITLED'))),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_TOP_TOPIC,
                                'content'   => $topTopic->id );

            //topic count
            if($topicCount > 0) $optiongroups[xg_text('FORUM')][]   = array(
                                'label'     => xg_text('THERE_ARE_X_FORUM_TOPIC_ON_APPNAME', $topicCount, $appName ),
                                'html'      => xg_html('THERE_ARE_X_LINK_FORUM_TOPIC_ON_APPNAME', $topicCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('forum')->buildUrl('index','index')) ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
        }

		$this->hasEvents = 0;
        if ($enabledModules['events']) {
        	$this->hasEvents = 1;
        	$events = array();
	        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
			EventWidget::init();
			if ($list = end(Event::getEventsByDate(xg_date('Y-m-d')))->getList()) {
				$events[]   = array(
                	'label' => xg_text('EVENT_X_IS_HAPPENING_TODAY', xnhtmlentities(xg_excerpt($list[0]->title,20))),
                    'type' => XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT,
                    'content' => $list[0]->id);
			}
			if ($list = end(Event::getEventsByDate(xg_date('Y-m-d',time()+86400)))->getList()) {
				$events[]   = array(
                	'label' => xg_text('EVENT_X_IS_HAPPENING_TOMORROW', xnhtmlentities(xg_excerpt($list[0]->title,20))),
                    'type' => XG_ActivityHelper::SUBCATEGORY_FACT_TOMORROW_EVENT,
                    'content' => $list[0]->id);
			}
			if ($eventCount > 0) {
				$events[]   = array(
                	'label' => xg_text('THERE_ARE_X_EVENTS_ON_APPNAME', $eventCount, $appName ),
					'html' => xg_html('THERE_ARE_X_LINK_EVENTS_ON_APPNAME', $eventCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('events')->buildUrl('index','index')) ),
                    'type' => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
			}

            $optiongroups[xg_text('EVENTS_TAB_TEXT')] = $events;
		}
		
		//TODO: BAZ-8494.  Once we are out of pre-release mode we should instead check $enabledModules['opensocial'] here as we do with other modules [Thomas David Baker 2008-07-17]
		//opensocial
		$this->hasOpenSocial = (XG_App::openSocialEnabled() ? 1 : 0);

        //blogs
        $optiongroups[xg_text('BLOG')]        = array();

        //blog post with most comments
        $query = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'BlogPost')->end(1);
        $query->filter('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'commentCount'), '<>', NULL);
        $query->order('my->' . XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'commentCount'), 'desc', XN_Attribute::NUMBER);
        $topPosts = $query->execute();
        $topPost  = $topPosts[0];

        if(count($topPosts) > 0)    $optiongroups[xg_text('BLOG')][]    = array(
                                'label'     => xg_text('X_IS_THE_MOST_POPULAR_BLOG_POST', xnhtmlentities($topPost->title?xg_excerpt($topPost->title,20):xg_excerpt(BlogPost::getTextTitle($topPost),20))),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_TOP_BLOGPOST,
                                'content'   => $topPost->id );

        //blog post count
        if($blogCount > 0)  $optiongroups[xg_text('BLOG')][]    = array(
                                'label'     => xg_text('THERE_ARE_X_BLOG_POST_ON_APPNAME', $blogCount, $appName ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);

        //groups
        if($enabledModules['groups']!=null) {
            $optiongroups[xg_text('GROUPS')]      = array();
            if($groupCount > 0) $optiongroups[xg_text('GROUPS')][]  = array(
                                'label'     => xg_text('THERE_ARE_X_GROUPS_ON_APPNAME', $groupCount, $appName ),
                                'html'      => xg_html('THERE_ARE_X_LINK_GROUPS_ON_APPNAME', $groupCount, xnhtmlentities($appName), xnhtmlentities(W_Cache::getWidget('groups')->buildUrl('index','index')) ),
                                'type'      => XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE);
        }

        $this->optiongroups = $optiongroups;
        $this->characterLimit = 140;
    }


    /**
     *  Saves the the changes to activity log settings specified in $_POST.
     *
     * Possible $_POST var:
     *  logNewContent       = Y (or var not present for no)
     *  logNewComments      = Y (or var not present for no)
     *  logNewMembers       = Y (or var not present for no)
     *  logProfileUpdates   = Y (or var not present for no)
     *
     */
    public function action_save() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if($_POST['addMessage']!='true'){
        	$enabledModules = XG_ModuleHelper::getEnabledModules();
			if (!$enabledModules['events']) {
				$_POST['logNewEvents'] = XG_App::logNewEvents() ? 'Y' : null;
			}
			if (! XG_App::openSocialEnabled()) {
			    $_POST['logOpenSocial'] = XG_App::logOpenSocial() ? 'Y' : null;
		    }
            Index_ActivityHelper::setActivitySettings($_POST);
            $this->redirectTo('edit', 'activity', array('saved' => 1));
            return;
        } else {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            if(mb_strlen($_POST['message'])>0){
                if($_POST['custom_message']=='true') {
                    $choice = XG_ActivityHelper::SUBCATEGORY_MESSAGE;
                } else {
                    $choiceParts = explode(',',$_POST['choice']);
                    $choice = $choiceParts[0];
                }
                switch($choice){
					case XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT :
					case XG_ActivityHelper::SUBCATEGORY_FACT_TOMORROW_EVENT :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_TOP_PHOTO :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_TOP_VIDEO :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_TOP_MUSIC :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_TOP_TOPIC :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_TOP_BLOGPOST :
                        $item = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, $choice, null, array(XN_Content::load($choiceParts[1])), $_POST['message']);
                        break;
                    case XG_ActivityHelper::SUBCATEGORY_FACT_PHOTO_CHAMPION :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_VIDEO_CHAMPION :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_MUSIC_CHAMPION :
                        $item = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, $choice, $choiceParts[1], null, $_POST['message']);
                        break;
                    case XG_ActivityHelper::SUBCATEGORY_MESSAGE :
                    case XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE :
                    default:
                        $item = XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, $choice, null, null, $_POST['message']);
                }
                $this->redirectTo('edit', 'activity', array('saved' => 1));
                return;
            }
        }
        $this->redirectTo('edit', 'activity');
    } // action_save

}
?>