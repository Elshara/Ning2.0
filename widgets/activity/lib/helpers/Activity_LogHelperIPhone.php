<?php
W_Cache::getWidget('activity')->includeFileOnce('/lib/helpers/Activity_LogHelper.php');

/**
 * Renders ActivityLogItem objects for display in the activity feed (iPhone-specific)
 */
abstract class ActivityRendererIPhone {

    /** Maximum number of characters for the title. */
    const MAX_TITLE_LENGTH = 160;

    /** Size of user avatar. */
    const AVATAR_SIZE = 48;

    /** Width of thumbnails. */
    const PHOTO_THUMB_WIDTH = 98;

    /** Height of thumbnails. */
    const PHOTO_THUMB_HEIGHT = 74;

    /**
     * Creates a new activity item renderer
     *
     * @param ActivityLogItem $item activity item to be displayed
     * @return ActivityRendererIPhone
     */
    public function ActivityRendererIPhone($item) {
        $this->item = $item;
        $this->members = explode(',',$item->my->members);
        $this->contentIds = explode(',',$item->my->contents);
    }

    /**
     * Generates data needed to display the activity item and attaches them to this object.
     * If function returns explicit FALSE, item won't be rendered and empty text is returned.
     *
     * $this->textArgs - String arguments to build the main message of the activity
     * $this->extraText - Additional string to display
     * $this->extraContent - Additional html elements in the feed item eg. images
     * $this->contentLink - URL of the activity feed link
     * @override
     */
    public abstract function prepareData();

    /**
     * Template for activity items. Contains placeholders for data to be
     * substituted using sprintf
     *
     * @return string html template
     * @override
     */
     public function getTemplate() {
        return
        '<li%6$s>
            <div class="ib">%3$s</div>
            <div class="tb"><span class="message">%1$s</span> <span class="lighter">%2$s</span>%4$s</div>
        </li>';
    }

    /**
     * Builds an array of arguments to be substituted into the template
     * using data items attached to this object
     *
     * @return array of strings
     */
    public function prepareTemplateData() {
        return array(
                        call_user_func_array('xg_html', $this->textArgs),
                        xg_elapsed_time($this->item->createdDate),
                        $this->getIcon(),
                        $this->extraContent,
                        $this->extraText,
                        ($this->contentLink ? ' _url="'. $this->contentLink .'" onclick="javascript:void(0)"': '')
                    );
    }

    /**
     * Gathers data for this activity item and renders using the objects template
     *
     * @return string the html output in the activity feed
     */
    public function render() {
		if ($this->prepareData() === false) {
			return '';
		}
        return call_user_func_array('sprintf', array_merge(array($this->getTemplate()), $this->prepareTemplateData()));
    }

    /**
     * Creates a link to the main user involved in this activity item,
     * showing the users full name
     *
     * @return string link to the user profile with the full name
     */
    public function getActorLink() {
        $username = $this->members[0];
        $fullname = XG_FullNameHelper::fullName($username);
        return '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($username))).'">'.xnhtmlentities($fullname).'</a>';
    }

    /**
     * Creates an image icon for the activity item
     *
     * @return string html element
     * @override
     */
    public function getIcon() {
        if ($this->hasAvatar()) {
            $username = $this->members[0];
            $link = xnhtmlentities(xg_absolute_url(User::quickProfileUrl($username)));
            return '<a href="' . $link . '"><img src="' . xnhtmlentities(XG_UserHelper::getThumbnailUrl(XG_Cache::profiles($username),self::AVATAR_SIZE,self::AVATAR_SIZE)) . '"/></a>';
        }
        return '';
    }

    /**
     * Creates a comma separated list of users for acitivity items that have multiple actors
     * eg. "user1, user2, and user3"
     *
     * @return string list of users
     */
    public function getUserSet() {
        $userSet = $this->getActorLink();
        if (count($this->members > 1)) {
            // remove dups so our count doesn't get weird
            $this->members = array_unique($this->members);
        }
        // TODO: Do not use xg_html('AND') - it is English-specific, and
        // not translatable to all languages. Instead use
        // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-18]
        if (count($this->members) == 2) {
            $userSet .= ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($this->members[1]))).'">'.xnhtmlentities(XG_FullNameHelper::fullName($this->members[1])).'</a>';
        }
        if (count($this->members) == 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($this->members[1]))).'">'.xnhtmlentities(XG_FullNameHelper::fullName($this->members[1])).'</a>' . ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($this->members[2]))).'">'.xnhtmlentities(XG_FullNameHelper::fullName($this->members[2])).'</a>';
        }
        if (count($this->members) > 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($this->members[1]))).'">'.xnhtmlentities(XG_FullNameHelper::fullName($this->members[1])).'</a>' . ', ' . '<a href="'.xnhtmlentities(xg_absolute_url(User::quickProfileUrl($this->members[2]))).'">'.xnhtmlentities(XG_FullNameHelper::fullName($this->members[2])).'</a> ' . xg_text('AND_X_OTHER_PEOPLE',count($this->members)-3) . ' ';
        }
        return $userSet;
    }

    /**
     * Determines if this activity item should be displayed with an avatar
     *
     * @return boolean true if item has an avatar, false otherwise
     */
    public function hasAvatar() {
        return Activity_LogHelper::hasAvatar($this->item) ||
            $this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FRIEND;
    }

    /**
	 *  Returns content object by ID. Returns NULL if ID is empty or object doesn't exist.
     *
     *  @param      $id   string    Object ID
     *  @return     XN_Content | NULL
     */
    public function content($id) {
        if (!$id) {
        	return NULL;
		}
		try {
			return XG_Cache::content($id);
		} catch(XN_Exception $e) {
			return NULL;
		}
    }
}

/**
 * Handles friend type activity feed items
 */
class FriendRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        // friend request accepted
        if($this->item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION) {
            // TODO: Support multiple friends. See Activity_LogHelper->friendshipMessageHtml() [Jon Aquino 2008-08-07]
            if (count($this->members) == 2) {
                $name = xnhtmlentities(XG_FullNameHelper::fullName($this->members[1]));
                $this->textArgs = array('X_AND_Y_ARE_NOW_FRIENDS', self::getActorLink(), $name);
                $this->contentLink = xg_absolute_url(User::quickProfileUrl($this->members[1]));
            }
		} else {
			return false;
		}
    }
}

/**
 * Handles group type activity feed items
 */
class GroupRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION) {
            $this->contentIds = array_unique($this->contentIds);
            if (count($this->contentIds) == 1) {
            	if (!$group = $this->content($this->contentIds[0])) {
            		return false;
				}
                $this->textArgs = array('X_JOINED_THE_GROUP_Y', self::getActorLink(), xnhtmlentities(xg_excerpt($group->title, self::MAX_TITLE_LENGTH)));
            } else {
                $this->textArgs = array('X_JOINED_Y_GROUPS');
            }
		} else if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
			if (!$group = $this->content($this->contentIds[0])) {
				return false;
			}
            if(count($this->contentIds) == 1){
                $this->textArgs = array('X_CREATED_A_GROUP_Y', self::getActorLink(), xnhtmlentities(xg_excerpt($group->title, self::MAX_TITLE_LENGTH)));
            } else {
                $this->textArgs = array('X_CREATED_Y_NEW_GROUPS', self::getActorLink(), count($this->contentIds));
            }
		} else {
			return false;
		}
    }
}

/**
 * Handles group topic type activity feed items
 */
class GroupTopicRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
            $titles = explode(',', $this->item->my->titles);
            $this->contentIds = array_unique($this->contentIds);
            if(count($this->contentIds) == 2){
				if (!$topic = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->textArgs = array('X_STARTED_A_DISCUSSION_Y_IN_GROUP', self::getActorLink(), qh($topic->title), xnhtmlentities(xg_excerpt(urldecode($titles[0]), self::MAX_TITLE_LENGTH)));
            } else {
                $this->textArgs = array('X_STARTED_Y_FORUM_POSTS_IN_GROUP', self::getActorLink(), count($this->contentIds)-1, xnhtmlentities(xg_excerpt(urldecode($titles[0]), self::MAX_TITLE_LENGTH)));
            }
		} else {
			return false;
		}
    }
}

/**
 * Handles blog type activity feed items
 */
class BlogRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
			if (!$blogPost = $this->content($this->contentIds[0])) {
				return false;
			}
            if(count($this->contentIds) == 1){
                $this->textArgs = array('X_ADDED_THE_BLOG_POST_Y', self::getActorLink(), BlogPost::getTextTitle($blogPost,self::MAX_TITLE_LENGTH));
            } else {
                $this->textArgs = array('X_ADDED_Y_BLOG_POSTS', self::getActorLink(), count($this->contentIds));
            }
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
			if (!$blogPost = $this->content($this->contentIds[1])) {
				return false;
			}
            $this->textArgs = array('X_COMMENTED_ON_BLOG_POST_TITLE', self::getActorLink(), BlogPost::getTextTitle($blogPost, self::MAX_TITLE_LENGTH));
		} else {
			return false;
		}
    }
}

/**
 * Handles forum type activity feed items
 */
class TopicRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        $this->contentLink = xg_absolute_url('/xn/detail/' . $this->contentIds[0]);
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
			if (!$topic = $this->content($this->contentIds[0])) {
				return false;
			}
            $this->textArgs = array('X_STARTED_A_DISCUSSION_Y', self::getActorLink(), xnhtmlentities(xg_excerpt($topic->title, self::MAX_TITLE_LENGTH)));
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
            W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
			if (!$topic = $this->content($this->contentIds[0])) {
				return false;
			}
            $titles = explode(',',$this->item->my->titles);
            $this->textArgs = array('X_COMMENTED_ON_POST_TITLE', self::getActorLink(), xnhtmlentities(xg_excerpt(urldecode($titles[0]), self::MAX_TITLE_LENGTH)));
		} else {
			return false;
		}
    }
}

/**
 * Handles photo type activity feed items
 */
class PhotoRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
			if (!$photo = $this->content($this->contentIds[0])) {
				return false;
			}
            if (!$photo->title) {
                $photo->title = xg_text('UNTITLED');
            }
            $this->textArgs = array('X_COMMENTED_ON_PHOTO_TITLE_NO_LINK', $this->getUserSet(), xnhtmlentities(xg_excerpt($photo->title, self::MAX_TITLE_LENGTH)));
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
            $this->textArgs = array('Y_ADDED_X_PHOTOS', count($this->contentIds), self::getActorLink());

            $thumbnails = array();
            $this->extraContent = '';
			for ($i = $j = 0; $i < count($this->contentIds) && $j < 4; $i++) {
				if (!$photo = $this->content($this->contentIds[$i])) {
					continue;
				}
                if ($photo->my->visibility == 'all') {
                    W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
                    Photo_HtmlHelper::fitImageIntoThumb($photo, self::PHOTO_THUMB_WIDTH, self::PHOTO_THUMB_HEIGHT, $imgUrl, $width, $height);
                    $thumbnails[] = '<img class="photo thumb" src="' . $imgUrl . '" width="' . $width . '" height="' . $height . '" alt="' . ($photo->title ? xnhtmlentities(xg_excerpt($photo->title, self::MAX_TITLE_LENGTH)) : xg_html('UNTITLED')) . '"/>';
					$j++;
                }
            }
            foreach ($thumbnails as $i => $thumbnail) {
                $photoLink = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('activityItemId' => $this->item->id, 'first' => $i, 'previousUrl' => XG_HttpHelper::currentUrl()));
                $this->extraContent .= '<a href="'. $photoLink .'">'. $thumbnail .'</a>';
            }
            $this->extraContent = (mb_strlen($this->extraContent) > 0 ? '<p>'. $this->extraContent .'</p>' : '');
            if (count($thumbnails) > 0) {
                $this->contentLink = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('activityItemId' => $this->item->id, 'previousUrl' => XG_HttpHelper::currentUrl()));
                if (count($thumbnails) > 4) $this->extraContent .= '<a href="#">'. xg_html('MORE_DOT_DOT_DOT') .'</a>';
            }
		} else {
			return false;
		}
    }
}


/**
 * Handles video type activity feed items
 */
class VideoRenderer extends ActivityRendererIPhone {

    public function prepareData() {
		if (!$video = $this->content($this->contentIds[0])) {
			return false;
		}
        if (!$video->title) {
            $video->title = xg_text('UNTITLED');
        }

		if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
            $this->textArgs = array('X_COMMENTED_ON_VIDEO_TITLE_NO_LINK', $this->getUserSet(), xnhtmlentities(xg_excerpt($video->title, self::MAX_TITLE_LENGTH)));
		} else if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
        	$this->extraContent = $this->extraText = $this->contentLink = '';
			for ($i = $j = 0; $i < count($this->contentIds) && $j < 3; $i++) {
				if (!$video = $this->content($this->contentIds[$i])) {
					continue;
				}
                if ($video->my->visibility == 'all') {
                    W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
                    $imgUrl = Video_VideoHelper::thumbnailUrl($video, self::PHOTO_THUMB_WIDTH, self::PHOTO_THUMB_HEIGHT);
                    $thumb = '<div><img class="photo thumb" src="' . qh($imgUrl) . '" width="' . self::PHOTO_THUMB_WIDTH . '" alt="' . qh($video->title ? xg_excerpt($video->title, self::MAX_TITLE_LENGTH) : xg_html('UNTITLED')) . '" /></div>';

					if (preg_match("/http:\/\/www.youtube.com\/v\/([0-9a-zA-Z_-]*)/ui", $video->my->embedCode, $matches)) {
                    	if (!$this->contentLink) {
                        	$this->contentLink = $matches[0];
						}
						$this->extraContent .= '<a href="' . qh($matches[0]) . '">' . $thumb . '</a>';
					} else {
						$this->extraContent .= $thumb;
					}
                    // $this->extraText .= ' '.xnhtmlentities(xg_excerpt($video->title, self::MAX_TITLE_LENGTH));
                    $j++;
                }
			}
            $this->textArgs = array('Y_ADDED_X_VIDEOS', count($this->contentIds), self::getActorLink());
		} else {
			return false;
		}
    }
}

/**
 * Handles photo album type activity feed items
 */
class AlbumRenderer extends ActivityRendererIPhone {

    /**
     * Generates data needed to display the activity item
     * and attaches them to this object
     *
     * $this->textArgs - String arguments to build the main message of the activity
     * $this->extraText - Additional string to display
     * $this->extraContent - Additional html elements in the feed item eg. images
     * $this->contentLink - URL of the activity feed link
     * @override
     */
    public function prepareData() {

        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
			if (!$album = $this->content($this->contentIds[0])) {
				return false;
			}
            if (!$album->title) {
                $album->title = xg_text('UNTITLED');
            }
            $this->textArgs = array('X_COMMENTED_ON_ALBUM_TITLE_NO_LINK', $this->getUserSet(), xnhtmlentities(xg_excerpt($album->title, self::MAX_TITLE_LENGTH)));
		} else if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
            if (count($this->contentIds) == 1) {
				if (!$album = $this->content($this->contentIds[0])) {
					return false;
				}
                if ($album->my->hidden != 'Y') {
                    W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
                    W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
                    $coverPhotos = Photo_AlbumHelper::getCoverPhotos($albums);
                    if ($coverPhotos[0] && $coverPhotos[0]->my->approved != 'N') {
                        Photo_HtmlHelper::fitImageIntoThumb($coverPhotos[$counter], self::PHOTO_THUMB_WIDTH, self::PHOTO_THUMB_HEIGHT, $coverPhotoUrl, $width, $height);
                    } else {
                        $coverPhotoUrl = xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/albums/default_cover_120x120.gif'));
                        $width = $height = self::PHOTO_THUMB_WIDTH;
                    }
                    $this->extraContent = '<div><img class="photo thumb" src="'. $coverPhotoUrl .'" width="'. $width .'" height="'. $height .'" alt="'. ($album->title ? xnhtmlentities(xg_excerpt($album->title, self::MAX_TITLE_LENGTH)) : xg_html('UNTITLED')) .'" /></div>';
                    $this->extraText = ' '.xnhtmlentities(xg_excerpt($album->title, self::MAX_TITLE_LENGTH));
                }
            }
            $this->textArgs = array('Y_ADDED_X_ALBUMS', count($this->contentIds), self::getActorLink());
		} else {
			return false;
		}
    }
}

/**
 * Handles event type activity feed items
 */
class EventRenderer extends ActivityRendererIPhone {

    public function prepareData() {
		if (!$event = $this->content($this->contentIds[0])) {
			return false;
		}

        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) {
            $this->textArgs = array('X_COMMENTED_ON_EVENT_TITLE_NO_LINK', $this->getActorLink(), xnhtmlentities($event->title));
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_STATUS_CHANGE) {
            $attend = (bool)($item->description == EventAttendee::ATTENDING);
            $might = (bool)($item->description == EventAttendee::MIGHT_ATTEND);
            $this->textArgs = array($attend ? 'X_CHANGED_EVENT_STATUS_ATTEND_NO_LINK' : 'X_CHANGED_EVENT_STATUS_MIGHT_NO_LINK', $this->getActorLink(), xnhtmlentities(xg_excerpt($event->title, self::MAX_TITLE_LENGTH)));
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
            $this->textArgs = array('X_CREATED_EVENT_TITLE_NO_LINK', $this->getActorLink(), xnhtmlentities(xg_excerpt($event->title, self::MAX_TITLE_LENGTH)));
		} elseif ($this->item->my->category == XG_ActivityHelper::CATEGORY_UPDATE) {
            $this->textArgs = array('X_UPDATED_EVENT_TITLE_NO_LINK', $this->getActorLink(), xnhtmlentities(xg_excerpt($event->title, self::MAX_TITLE_LENGTH)));
		} else {
			return false;
		}
    }
}

/**
 * Handles music type activity feed items
 */
class MusicRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT) {
            if ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_HOME_TRACK) {
                $this->textArgs = array('X_NEW_SONGS_ADDED_TO_APPNAME', count($this->contentIds), XN_Application::load()->name);
            } else {
                $this->textArgs = array('Y_ADDED_X_SONGS', count($this->contentIds), $this->getActorLink());
            }
            $this->extraContent = '';
			for ($i = $j = 0; $i < count($this->contentIds) && $j < 4; $i++) {
				if (!$track = $this->content($this->contentIds[$i])) {
					continue;
				}
                $showLink = XG_SecurityHelper::userIsAdmin() || ($track->my->enableDownloadLink);
                $this->extraContent .= ($showLink ? '<div><a href="'. xnhtmlentities($track->my->audioUrl) .'"><img alt="'. xg_html('PLAY') .'" src="'. xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') .'" width="21" height="16"/> ' : '<div>');
                $this->extraContent .= xnhtmlentities(xg_excerpt($track->my->artist, self::MAX_TITLE_LENGTH)) . ($track->my->artist && $track->my->trackTitle ? ' &mdash;' : '') . xnhtmlentities(xg_excerpt($track->my->trackTitle, self::MAX_TITLE_LENGTH));
                $this->extraContent .= ($showLink ? '</a></div>' : '</div>');
				$j++;
            }
		} else {
			return false;
		}
    }
}

/**
 * Handles profile type activity feed items
 */
class ProfileRenderer extends ActivityRendererIPhone {

    public function prepareData() {

        //member joined the network
        if($this->item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION) {
            $this->textArgs = array('X_JOINED_APPNAME_NO_LINK', self::getActorLink(), xnhtmlentities(xg_excerpt(XN_Application::load()->name, self::MAX_TITLE_LENGTH)));
            $this->contentLink  = xg_absolute_url(User::quickProfileUrl($this->members[0]));
		} else if ($this->item->my->category == XG_ActivityHelper::CATEGORY_UPDATE) { //member updated profile
            $this->textArgs = array('XS_PROFILE_CHANGED', self::getActorLink());
            $this->contentLink  = xg_absolute_url(User::quickProfileUrl($this->members[0]));
		} else if ($this->item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT) { //new chatter on the chatterwall
            $this->textArgs = array('X_LEFT_A_COMMENT_FOR_Y', self::getActorLink(), xnhtmlentities(xg_excerpt(XG_FullNameHelper::fullName($this->members[1]), self::MAX_TITLE_LENGTH)));
            $this->contentLink  = xg_absolute_url(User::quickProfileUrl($this->members[1]));
		} else {
			return false;
		}
    }
}

/**
 * Handles network type activity feed items
 */
class NetworkRenderer extends ActivityRendererIPhone {

    /**
     * Template for activity items. Contains placeholders for data to be
     * substituted using sprintf
     *
     * @return string html template
     * @override
     */
    public function getTemplate() {
        return
        '<li%6$s>
            <div class="ib">%3$s</div>
            <div class="tb"><h5>%1$s</h5><span class="message">%5$s</span> <span class="lighter">%2$s</span>%4$s</div>
        </li>';
    }

    /**
     * Returns icon html specific to network activity items
     *
     * @return string icon either bang or question mark
     * @override
     */
    public function getIcon() {
        $isFact = Activity_LogHelper::isFact($this->item);
        return '<div class="'. ($isFact ? 'question' : 'statement') .'"></div>';
    }

    /**
     * Generates data needed to display the activity item
     * and attaches them to this object
     *
     * $this->textArgs - String arguments to build the main message of the activity
     * $this->extraText - Additional string to display
     * $this->extraContent - Additional html elements in the feed item eg. images
     * $this->contentLink - URL of the activity feed link
     * @override
     */
    public function prepareData() {
        if ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE) {
            $this->textArgs = array('ANNOUNCEMENT');
            $this->extraText = xnhtmlentities(xg_excerpt($this->item->description,140));
        } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_QUESTIONS_UPDATE) {
            $this->textArgs = array('ANNOUNCEMENT');
            $this->extraText = xg_html('APPNAME_HAS_NEW_PROFILE_QUESTIONS_NO_LINK', xnhtmlentities(xg_excerpt(XN_Application::load()->name, self::MAX_TITLE_LENGTH)));
        } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_NEW_FEATURE) {
            if ($itemWidget = W_Cache::getWidget($this->item->my->widgetName)) {
                $itemWidgetIndex = $itemWidget->buildUrl('index', 'index');
            }
            $this->textArgs = array('ANNOUNCEMENT');
            $this->extraText = xg_html('APPNAME_NOW_HAS_'.mb_strtoupper($this->item->my->widgetName).'_NO_LINK', xnhtmlentities(xg_excerpt(XN_Application::load()->name, self::MAX_TITLE_LENGTH)));
        } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED && XN_Profile::current()->isOwner()) {
            $this->textArgs = array('CONGRATULATIONS');
            $this->extraText = xg_html('YOU_CREATED_APPNAME', xnhtmlentities(xg_excerpt(XN_Application::load()->name, self::MAX_TITLE_LENGTH)));
        } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED && ! XN_Profile::current()->isOwner()) {
            $this->textArgs = array('ANNOUNCEMENT');
            $this->extraText = xg_html('USER_CREATED_APPNAME', xnhtmlentities(xg_username(XG_Cache::profiles(XN_Application::load()->ownerName))), xnhtmlentities(xg_excerpt(XN_Application::load()->name, self::MAX_TITLE_LENGTH)));
        } else {
            $this->textArgs = array('DID_YOU_KNOW');
            if ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE) {
                $this->extraText = html_entity_decode($this->item->description);
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_PHOTO_CHAMPION) {
                $this->extraText = xg_html('X_HAS_POSTED_THE_MOST_PHOTOS', $this->getActorLink());
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_VIDEO_CHAMPION) {
                $this->extraText = xg_html('X_HAS_POSTED_THE_MOST_VIDEOS', $this->getActorLink());
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MUSIC_CHAMPION) {
                $this->extraText = xg_html('X_HAS_POSTED_THE_MOST_MUSIC', $this->getActorLink());
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_PHOTO) {
				if (!$photo = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->extraText = xg_html('X_IS_THE_MOST_POPULAR_PHOTO',  $photo->title ? qh($photo->title) : xg_text('UNTITLED'));
                W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
                Photo_HtmlHelper::fitImageIntoThumb($photo, self::PHOTO_THUMB_WIDTH, self::PHOTO_THUMB_HEIGHT, $imgUrl, $width, $height);
                $this->extraContent = '<div><img class="photo thumb" src="' . $imgUrl . '" width="' . $width . '" height="' . $height . '" alt="' . ($photo->title ? xnhtmlentities(xg_excerpt($photo->title, self::MAX_TITLE_LENGTH)) : xg_html('UNTITLED')) . '"/></div>';
                $this->contentLink = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id));
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_VIDEO) {
				if (!$video = $this->content($this->contentIds[0])) {
					return false;
				}
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
                $imgUrl = Video_VideoHelper::thumbnailUrl($video, self::PHOTO_THUMB_WIDTH, self::PHOTO_THUMB_HEIGHT);
                $this->extraContent = '<div><img class="photo thumb" src="' . $imgUrl . '" width="' . self::PHOTO_THUMB_WIDTH . '" alt="' . ($video->title ? xnhtmlentities(xg_excerpt($video->title, self::MAX_TITLE_LENGTH)) : xg_html('UNTITLED')) . '" /></div>';
                $this->extraText = xg_html('X_IS_THE_MOST_POPULAR_VIDEO', $video->title ? qh($video->title) : xg_text('UNTITLED'));
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_MUSIC) {
				if (!$track = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->extraText = xg_html('X_IS_THE_MOST_POPULAR_MUSIC', xnhtmlentities($track->my->artist) . ($track->my->artist && $track->my->trackTitle ? ' &mdash;' : '') . xnhtmlentities(xg_excerpt($track->my->trackTitle, self::MAX_TITLE_LENGTH)));
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_TOPIC) {
				if (!$topic = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->extraText = xg_html('X_IS_THE_MOST_POPULAR_TOPIC', qh($topic->title));
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_BLOGPOST) {
				if (!$blogPost = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->extraText = xg_html('X_IS_THE_MOST_POPULAR_BLOG_POST', $blogPost->title ? xnhtmlentities(xg_excerpt(BlogPost::getTextTitle($blogPost), self::MAX_TITLE_LENGTH)) : BlogPost::getTextTitle($blogPost));
            } elseif ($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT ||
                        $this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOMORROW_EVENT) {
				if (!$event = $this->content($this->contentIds[0])) {
					return false;
				}
                $this->extraText = xg_html($this->item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT ? 'EVENT_X_IS_HAPPENING_TODAY' : 'EVENT_X_IS_HAPPENING_TOMORROW', xnhtmlentities(xg_excerpt($event->title, self::MAX_TITLE_LENGTH)));
            }
        }
    }
}

/**
 * Handles gadget type activity feed items
 */
class GadgetRenderer extends ActivityRendererIPhone {

    public function prepareData() {
        if ($this->item->my->category == XG_ActivityHelper::CATEGORY_GADGET) {
            $this->textArgs = array(xnhtmlentities(xg_excerpt($this->item->title, self::MAX_TITLE_LENGTH)));
            $this->extraText = xnhtmlentities(xg_excerpt($this->item->description, self::MAX_TITLE_LENGTH));
		} else {
			return false;
		}
    }
}

/**
 * Renders each activity feed item
 */
class Activity_LogHelperIPhone {

    /**
     * Creates an activity item renderer based on the item type and executes the rendering
     *
     * @param ActivityLogItem $item the activity item to be displayed in the feed
     * @return string html code to be output in the feed
     */
    public static function renderItem($item) {
		switch ($item->my->subcategory) {
			case XG_ActivityHelper::SUBCATEGORY_FRIEND: $class = 'FriendRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_BLOG: $class = 'BlogRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_TOPIC: $class = 'TopicRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_PHOTO: $class = 'PhotoRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_GROUP_TOPIC: $class = 'GroupTopicRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_GROUP: $class = 'GroupRenderer'; break;
        	case XG_ActivityHelper::SUBCATEGORY_VIDEO: $class = 'VideoRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_ALBUM: $class = 'AlbumRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_EVENT: $class = 'EventRenderer'; break;
			case XG_ActivityHelper::SUBCATEGORY_HOME_TRACK:
			case XG_ActivityHelper::SUBCATEGORY_TRACK: $class = 'MusicRenderer';  break;
			case XG_ActivityHelper::SUBCATEGORY_PROFILE: $class = 'ProfileRenderer'; break;
			default:
				switch($item->my->category) {
					case XG_ActivityHelper::CATEGORY_NETWORK: $class = 'NetworkRenderer'; break;
					case XG_ActivityHelper::CATEGORY_GADGET: $class = 'GadgetRenderer'; break;
					default: return '';
				}
		}
		$renderer = new $class($item);
        return $renderer->render();
    }
}
?>