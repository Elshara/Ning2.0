<?php

/**
 * Useful functions for working with Share This messages.
 */
class Index_SharingHelper {

    /**
     * Returns the URL for the specified content.
     *
     * @param $itemInfo array  metadata for the content object
     * @param $indicateSent boolean  whether the URL should indicate that sharing emails were sent
     */
    public static function url($itemInfo, $indicateSent = false) {
        return XG_HttpHelper::addParameter($itemInfo['share_url'], 'shareInvitesSent', $indicateSent ? 1 : null);
    }

    /**
     * Returns the contentID for the Group containing the specified content.
     *
     * @param $itemInfo array  metadata for the content object
     */
    public static function groupId($itemInfo) {
        return $itemInfo['object']->type == 'Group' ? $itemInfo['object']->id : $itemInfo['object']->my->groupId;
    }

    /**
     * Returns the subject line for the email.
     *
     * @param $itemInfo array  metadata for the content object
     * @return  the subject line to use
     */
    public static function subject($itemInfo) {
        if (mb_strlen($itemInfo['share_title']) > 0) {
            return xg_text('CHECK_OUT_TITLE_ON_APPNAME', $itemInfo['share_title'], XN_Application::load()->name);
        } else {
            return xg_text('CHECK_OUT_UNTITLED_ON_APPNAME', mb_strtolower($itemInfo['share_type']), XN_Application::load()->name);
        }
    }

    /**
     * Returns a share-with-friends message object.
     *
     * @param $itemInfo array  metadata for the content object
     * @param $message string  optional message to include in the email
     * @return XG_Message_Invitation_Share  an object for sending an email
     */
    public static function createMessage($itemInfo, $message) {
        XG_App::includeFileOnce('/lib/XG_Message.php');
		return new XG_Message_Invitation_Share(Index_SharingHelper::subject($itemInfo), $message, $itemInfo);
    }

    /**
     * Returns the title for the page.
     *
     * @param $itemInfo array  metadata for the content object
     * @return  the page title to use
     */
    public static function pageTitle($itemInfo) {
        if (mb_strlen($itemInfo['share_title']) > 0) {
            return xg_text('SHARE_TITLE', $itemInfo['share_title']);
        } else {
            return xg_text('SHARE_TYPE', ucwords($itemInfo['share_type']));
        }
    }

    public static function getItemInfoFromIdOrRaw($id, $rawArgs) {
        if ($id) {
        	return Index_SharingHelper::getItemInfo($id);
		} else if ($rawArgs['docUrl']) {
			return Index_SharingHelper::getPageInfo($rawArgs['docUrl'], $rawArgs['docTitle'], $rawArgs['shareType']);
		} else {
			return Index_SharingHelper::getItemInfoFromHash($rawArgs);
		}
    }
    
	/**
     * Get relevant sharing information for the page specified using the url & type.
     * @TODO: Merge this with the getItemInfo - and eliminate getItemInfoFromIdOrRaw wrapper - [Mohan Gummalam 2008-09-12]
     * @param $appUrl string url of the app/feature
     */
    public static function getPageInfo($url, $title, $type) {
		switch ($type) {
			case 'url':
				$itemInfo['share_title'] = $title;
				$itemInfo['share_url'] = $url;
				$itemInfo['share_type'] = xg_text('URL');
				$itemInfo['share_raw_type'] = $type;
				break;
			case 'opensocialapp':
			    $opensocialWidget = W_Cache::getWidget('opensocial');
			    $opensocialWidget->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
			    $aboutUrl = $opensocialWidget->buildUrl('application','about', array('appUrl' => $url));
			    $gadgetDetails = OpenSocial_GadgetHelper::readGadgetUrl($url);
                $itemInfo['description'] = '<p>'.xnhtmlentities($gadgetDetails["description"]).'</p>';
			    $itemInfo['share_title'] = $title;
				$itemInfo['share_url'] = $aboutUrl;
                $itemInfo['share_thumb'] = $gadgetDetails["thumbnail"];
				$itemInfo['share_type'] = xg_text('APPLICATION');
				$itemInfo['share_raw_description'] = $gadgetDetails["description"];
				$itemInfo['share_raw_type'] = $type;
                $itemInfo['message_parts'] = XG_MessageHelper::getDefaultMessageParts();
				break;
			default:
				return null;
		}
        if ($itemInfo['share_thumb']) {
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            $width = ($type === 'opensocialapp') ? 120 : 110;
            $height = ($type === 'opensocialapp') ? 60 : 110;
            $itemInfo['display_thumb'] = XG_FileHelper::setImageUrlDimensions($itemInfo['share_thumb'], $width, $height);
        }
		return $itemInfo;
	}
    
	/**
     *  Get relevant sharing information for the object with the specified ID.
     *
     * @param $id string
     */
    public static function getItemInfo($id) {
        try {
            $item = XG_Cache::content($id);
        } catch (exception $e) {
            error_log('Could not find object to share (id ' . $id . ')!');
            return null;
        }
        return self::getItemInfoProper($item);
    }

    /**
     *  Get relevant sharing information for the arbitrary hash
     *
	 * @param $hash hash
     */
    public static function getItemInfoFromHash($hash) {
     	$object = (object)$hash;
        return self::getItemInfoProper($object);
    }

    /**
     *  Get relevant sharing information for the object with the specified ID.
     *
     * @param $item  XN_Content  the content object
     */
    protected static function getItemInfoProper($item) {
        // TODO: Someday/maybe rework this code to use the Strategy design pattern  [Jon Aquino 2007-10-25]
        // TODO: Remove keys that are no longer used  [Jon Aquino 2007-10-26]
        $itemInfo = array('id' => $item->id, 'object' => $item);
		$itemInfo['message_parts'] = XG_MessageHelper::getDefaultMessageParts();
		$descriptionTitle = '';
		$nameTitle = '';
        switch ($item->type) {
            case 'Photo':
                $photoWidget = W_Cache::getWidget('photo');
                $photoWidget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
                Photo_HtmlHelper::getImageUrlAndDimensions($item, $thumbUrl, $width, $height);
                $itemInfo['description'] = '<p>' . xnhtmlentities($item->description) . '</p>';
                $itemInfo['share_title'] = $item->title;
                $itemInfo['share_url'] = $photoWidget->buildUrl('photo', 'show', array('id' => $item->id));
                $itemInfo['share_type'] = xg_text('PHOTO');
                $itemInfo['share_thumb'] = $thumbUrl;
                $itemInfo['share_raw_description'] = $item->description;
                $itemInfo['share_raw_type'] = 'photo';
                $descriptionTitle = xg_html('PHOTO_DESCRIPTION');
                $nameTitle = xg_html('PHOTO_TITLE');
                break;
            case 'Album':
                $photoWidget = W_Cache::getWidget('photo');
                $photoWidget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
                $photoWidget->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
                $itemInfo['description'] = '<p>' . xnhtmlentities(xg_excerpt($item->description, 400)) . '</p>';
                $itemInfo['share_title'] = $item->title;
                $itemInfo['share_url'] = $photoWidget->buildUrl('album', 'show', array('id' => $item->id));
                $coverPhotos = Photo_AlbumHelper::getCoverPhotos(array($item));
                if (! is_null($coverPhotos[0])) {
                    Photo_HtmlHelper::getImageUrlAndDimensions($coverPhotos[0], $thumbUrl, $width, $height);
                    $itemInfo['share_thumb'] = $thumbUrl;
                }
                $itemInfo['share_type'] = xg_text('PHOTO_ALBUM');
                $itemInfo['share_raw_description'] = $item->description;
                $itemInfo['share_raw_type'] = 'album';
                $descriptionTitle = xg_html('ALBUM_DESCRIPTION');
                $nameTitle = xg_html('ALBUM_TITLE');
                break;
            case 'Video':
                $videoWidget = W_Cache::getWidget('video');
                $videoWidget->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
                $itemInfo['description'] = '<p>' . xnhtmlentities(xg_excerpt($item->description, 400)) . '</p>';
                $itemInfo['share_title'] = $item->title;
                $itemInfo['share_url'] = $videoWidget->buildUrl('video', 'show', array('id' => $item->id));
				// for video 'in-progress' we display a generic image
				$itemInfo['share_thumb'] = xg_absolute_url(Video_VideoHelper::thumbnailUrl($item, 96));
                $itemInfo['share_type'] = xg_text('VIDEO');
                $itemInfo['share_raw_description'] = $item->description;
                $itemInfo['share_raw_type'] = 'video';
				$descriptionTitle = xg_html('VIDEO_DESCRIPTION');
                $nameTitle = xg_html('VIDEO_TITLE');
                break;
            case 'Topic':
                $forumWidget = W_Cache::getWidget('forum');
                $itemInfo['description'] = '<blockquote style="margin:0"><p>' . xnhtmlentities(xg_excerpt($item->description, 400)) . '</p></blockquote>';
                $itemInfo['share_title'] = $item->title;
                if ($item->my->groupId) {
                    XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
                    $group = Group::load($item->my->groupId);
                    $itemInfo['share_url'] = XG_GroupHelper::buildUrl('forum','topic','show',array('groupId' => $item->my->groupId, 'id' => $item->id), $group->my->url);
                } else {
                    $itemInfo['share_url'] = $forumWidget->buildUrl('topic', 'show', array('id' => $item->id));
                }
                $user = XG_Cache::profiles($item->contributorName);
                $itemInfo['share_thumb'] = XG_UserHelper::getThumbnailUrl($user, XN_Profile::PRESERVE_ASPECT_RATIO, 110);
                $itemInfo['share_type'] = xg_text('DISCUSSION');
                $itemInfo['share_raw_description'] = $item->description;
                $itemInfo['share_raw_type'] = 'topic';
				$itemInfo['share_content_author'] = $item->contributorName;
                $descriptionTitle = xg_html('DISCUSSION_TEXT');
                $nameTitle = xg_html('DISCUSSION_TITLE2');
                break;
            case 'BlogPost':
                $profilesWidget = W_Cache::getWidget('profiles');
                //$x = $forumWidget->findInclude('lib/helpers/Forum_
                $itemInfo['description'] = '<blockquote style="margin:0"><p>' . xg_excerpt($item->description, 400) . '</p></blockquote>';
                $itemInfo['share_title'] = BlogPost::getTextTitle($item);
                $itemInfo['share_url'] = $profilesWidget->buildUrl('blog', 'show', array('id' => $item->id));
                $user = XG_Cache::profiles($item->contributorName);
                $itemInfo['share_thumb'] = XG_UserHelper::getThumbnailUrl($user, XN_Profile::PRESERVE_ASPECT_RATIO, 110);
                $itemInfo['share_type'] = xg_text('BLOG_POST');
                $itemInfo['share_raw_description'] = $item->description;
                $itemInfo['share_raw_type'] = 'post';
				$itemInfo['share_content_author'] = $item->contributorName;
				$descriptionTitle = xg_html('BLOG_POST_TEXT');
                $nameTitle = xg_html('BLOG_POST_TITLE');
                break;
            case 'User':
                $profilesWidget = W_Cache::getWidget('profiles');
                $profile = XG_Cache::profiles($item->title);
                $itemInfo['description'] = '<p><b>' . xnhtmlentities(xg_username($profile)) . '</b><br />' . xg_age_and_location($profile) . '</p>';
                $itemInfo['share_title'] = xg_text('XS_PAGE', xg_username($profile));
                $itemInfo['share_url'] = xg_absolute_url('/profile/' . User::profileAddress($profile->screenName));
                $itemInfo['share_thumb'] = XG_UserHelper::getThumbnailUrl($profile, XN_Profile::PRESERVE_ASPECT_RATIO, 110);
                $itemInfo['share_type'] = xg_text('PROFILE');
                $itemInfo['share_raw_type'] = 'user';
                break;
            case 'Group': // not used anymore?
                $groupWidget = W_Cache::getWidget('groups');
                $itemInfo['description'] = '<p>' . xnhtmlentities(xg_excerpt($item->description, 400)) . '</p>';
                $itemInfo['share_title'] = $item->title;
                $itemInfo['share_url'] = xg_absolute_url('/group/' . $item->my->url);
                $itemInfo['share_thumb'] = $item->my->iconUrl;
                $itemInfo['share_type'] = xg_text('GROUP');
                break;
            default:
				try {
					$type = $item->shareType;
				} catch(Exception $e) {
					$type = '';
				}
				switch ($type) {
					case 'url':
						$itemInfo['share_title'] = $item->docTitle;
		                $itemInfo['share_url'] = $item->docUrl;
		                $itemInfo['share_type'] = xg_text('URL');
		                $itemInfo['share_raw_type'] = 'url';
						break;
					default:
		                error_log('Unrecognized type: ' . $item->type . ' at ' . __FILE__ . ':' . __LINE__);
		                return null;
				}
        }
		// assume that only creator can change the object...
		if ($descriptionTitle && (XN_Profile::current()->screenName == $item->contributorName)) {
			// must be not shorter than the text in a email
			$itemInfo['message_parts'][$descriptionTitle] = xg_excerpt($itemInfo['share_raw_description'],140);
		}
		if ($nameTitle && (XN_Profile::current()->screenName == $item->contributorName)) {
			// must be not shorter than the text in a email
			$itemInfo['message_parts'][$nameTitle] = $itemInfo['share_title'];
		}
        if ($itemInfo['share_thumb']) {
            XG_App::includeFileOnce('/lib/XG_FileHelper.php');
            $itemInfo['display_thumb'] = XG_FileHelper::setImageUrlDimensions($itemInfo['share_thumb'], 110, 110);
        }
        return $itemInfo;
    }

    /**
     * can this item be displayed on the logged out share page?
     */
    public static function canDisplayToLoggedOut($obj) {
        // TODO: Someday/maybe rework this code to use the Strategy design pattern [Jon Aquino 2007-10-25]
        $canShare = false;
        switch ($obj->type) {
            case 'Photo':
            case 'Album':
                W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
                $canShare = Photo_PrivacyHelper::canCurrentUserSeeShareLinks($obj);
                break;
            case 'Video':
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
                $canShare = Video_PrivacyHelper::canCurrentUserSeeShareLinks($obj);
                break;
            case 'Topic':
                W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
                $canShare = Forum_SecurityHelper::currentUserCanSeeShareLinks($obj);
                break;
            case 'BlogPost':
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');
                $canShare = Profiles_PrivacyHelper::canCurrentUserSeeShareLinks($obj);
                break;
            case 'User':
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');
                $canShare = Profiles_PrivacyHelper::canCurrentUserSeeShareLinks();
                break;
            case 'Group':
                W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
                $canShare = Groups_SecurityHelper::currentUserCanSeeShareLinks($obj);
                break;
        }
        return $canShare;
    }

    public static function userCanShare($obj) {
        // TODO: Someday/maybe rework this code to use the Strategy design pattern [Jon Aquino 2007-10-25]
        $canShare = false;
        switch ($obj->type) {
            case 'Photo':
            case 'Album':
                W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PrivacyHelper.php');
                $canShare = Photo_PrivacyHelper::canCurrentUserShare($obj);
                break;
            case 'Video':
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_PrivacyHelper.php');
                $canShare = Video_PrivacyHelper::canCurrentUserShare($obj);
                break;
            case 'Topic':
                W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
                $canShare = Forum_SecurityHelper::currentUserCanShare($obj);
                break;
            case 'BlogPost':
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');
                $canShare = Profiles_PrivacyHelper::canCurrentUserShare($obj);
                break;
            case 'User':
                W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');
                $canShare = Profiles_PrivacyHelper::canCurrentUserSeeShareLinks();
                break;
            case 'Group':
                W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
                $canShare = Groups_SecurityHelper::currentUserCanShare($obj);
                break;
        }
        return $canShare;
    }

}
