<?php
/**
 * Generic message
 */
class XG_Message_Generic extends XG_Message {
    public function send($to, $from, $addlHeaders = NULL) {
    	parent::_sendProper($to, $from, $addlHeaders);
	}
}

/**
 * A broadcast message comes from a network administrator
 */
class XG_Message_Broadcast extends XG_Message {
    protected $_template = 'broadcast';

    public function __construct($subject, $body, $fromProfile) {
        parent::__construct($subject, $body);
        $app = XN_Application::load();
        $this->_data['from'] =  self::siteReturnAddress();
        // Include network name in subject (BAZ-1716)
        $this->_data['subject'] = xg_text('ON_X_COLON', $app->name) . ' ' . $this->_data['subject'];
    }

    public function send($to) {
        $this->_sendProper($to, $this->from, array('X-XN_ALLOW_ADMIN_ONLY' => 1));
    }

}

/*
 * A group broadcast message comes from the logged in admin
 */
class XG_Message_Group_Broadcast extends XG_Message {
    protected $_template = 'group-broadcast';

    /**
     * @param $subject string
     * @param $body string - Message to be included in the email
     * @param $fromProfile XN_Profile - Profile of the sending user
     * @param $group XN_Content - Group object
     */
    public function __construct($subject, $body, $fromProfile, $group) {
        $args = func_get_args();
        parent::__construct($subject, $body);
        $app = XN_Application::load();
        $this->_data['fromProfile'] = $fromProfile;
        $this->_data['from'] =  self::siteReturnAddress();
        $this->_data['app'] = $app;
        $this->_data['group'] = $group;
        $this->_data['groupUrl'] = W_Cache::getWidget('groups')->buildUrl('group', 'show', array('id' => $group->id));
        // Include network name in subject (BAZ-1716)
        $this->_data['subject'] = xg_text('ON_X_COLON', $this->app->name) . ' ' . $this->_data['subject'];
    }

    public function send($to) {
        $this->_sendProper($to, self::siteReturnAddress());
    }

}

/**
 * A message sent to selected attendees of an event
 */
class XG_Message_Event_Broadcast extends XG_Message {
    protected $_template = 'event-broadcast';

    /**
     * @param $subject string
     * @param $body string - Message to be included in the email
     * @param $fromProfile XN_Profile - Profile of the sending user
     * @param $event XN_Content - Event object
     */
    public function __construct($subject, $body, $fromProfile, $event) {
        $args = func_get_args();
        parent::__construct($subject, $body);
        $app = XN_Application::load();
        $this->_data['fromProfile'] = $fromProfile;
        $this->_data['from'] =  self::siteReturnAddress();
        $this->_data['app'] = $app;
        $this->_data['event'] = $event;
        $this->_data['eventUrl'] = W_Cache::getWidget('events')->buildUrl('event', 'show', array('id' => $event->id));
        // Include network name in subject (BAZ-1716)
        $this->_data['subject'] = xg_text('ON_X_COLON', $this->app->name) . ' ' . $this->_data['subject'];
        // TODO: The subject line, like all sentences, should be a single message;
        // otherwise it is hard to translate into some languages [Jon Aquino 2008-04-01]
    }

    public function send($to) {
        $this->_sendProper($to, self::siteReturnAddress());
    }

}

/**
 * A message that is an invitation may have a URL in it
 */
class XG_Message_Invitation extends XG_Message {
	protected $_template = 'invitation';

    public function __construct($subject, $body = null, $url = null) {
        parent::__construct($subject, $body);
        if (isset($url)) {
            $this->_data['url'] = $url;
        }
    }

	protected static $cacher;
	protected $_from;

    protected function _getCachedMessage($to, $from) {
		if (!self::$cacher) { self::$cacher = new XG_MessageCacher; }
    	return self::$cacher->getMessage($to, $from, $this->_data);
	}

	protected function _setCachedMessage($to, $from, $data) {
		return self::$cacher->setMessage($to, $from, $data, $this->_data);
	}

    protected function _initTemplateData($to, $from) {
		$this->_data += XG_MessageHelper::initAboutNetwork();
		$this->_data['counters'] = XG_MessageHelper::getUserCounters($this->_from);

		if (!$this->_data['sparse']) {
			$max = 5;
			$this->_data['members'] = array();
			// Friends
			W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
			$friends = Profiles_UserHelper::findFriendsOf($this->_from, 0, $max);
			foreach ($friends['friends'] as $user) {
				$this->_data['members'][$user->title] = $user;
				$max--;
			}
			// Promoted (ignore duplicates)
			if ($max > 0) {
				$members = Profiles_UserHelper::getPromotedUsers(5);
				foreach ($members as $user) {
					if (isset($this->_data['members'][$user->title])) { continue; }
					$this->_data['members'][$user->title] = $user;
					if(--$max == 0) { break; }
				}
			}
			// Random (ignore duplicates)
			if ($max > 0) {
				W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserSort.php');
				W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_FriendListHelper.php');
				$members = Profiles_UserSort::get('random')->findUsers(array(), 0, 5, true, new Profiles_FriendListHelper());
				foreach ($members['users'] as $user) {
					if (isset($this->_data['members'][$user->title])) { continue; }
					$this->_data['members'][$user->title] = $user;
					if(--$max == 0) { break; }
				}
			}
		}
        $this->_data['fromProfile'] = XG_Cache::profiles($this->_from);
	}

    public function summary() {
        return parent::summary() . "[$this->url]";
    }

	public function build($to, $from, $isHtml) {
		if ($to !== NULL || $from !== NULL) {
			throw new Exception("Calling ".__METHOD__."() with to/from != NULL is not allowed anymore.");
		}
		if ($isHtml != false) {
			throw new Exception("Calling ".__METHOD__."() for HTML emails is allowed anymore.");
		}
		$this->_initCommonData($to, $from);
		return $this->_build($isHtml ? 'html' : 'text');
	}

    public function send($to, $from, $requested = false) {
    	$this->_forceHtml = true;
		$this->_from = $from;
        $addlHeaders = array();
        if ($requested) {
        	$addlHeaders['X-XN_ALLOW_ADMIN_ONLY'] = 1;
		}
        $addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($from),'invitations');
        $addlHeaders['Reply-To'] = XG_Message::formatEmailAddress(XG_Cache::profiles($from)->email);
        $this->_sendProper($to, self::siteReturnAddress(), $addlHeaders);
    }
}

/**
 * A share-with-friends message is an invitation that may also reference
 * a content object
 */
class XG_Message_Invitation_Share extends XG_Message {
	protected $_template = 'share';
    /**
     *  @param      $itemInfo	struct{description, share_url, share_title, share_thumb, share_type, share_raw_description, share_raw_type}
     */
    public function __construct($subject, $body = null, $itemInfo = null) {
		parent::__construct($subject, $body);
		foreach($itemInfo as $k=>$v) {
			$this->_data[$k] = $v;
		}

		// Backward compatibility with old templates:
		$this->_data['url'] = $itemInfo['share_url'];
		$this->_data['title'] = $itemInfo['share_title'];
		$this->_data['thumb'] = $itemInfo['share_thumb'];
		$this->_data['type'] = $itemInfo['share_type'];

		$this->_data['share'] = true;
    }

	protected static $cacher;
	protected $_from;

    protected function _getCachedMessage($to, $from) {
		if (!self::$cacher) { self::$cacher = new XG_MessageCacher; }
    	return self::$cacher->getMessage($to, $from, $this->_data);
	}

	protected function _setCachedMessage($to, $from, $data) {
		return self::$cacher->setMessage($to, $from, $data, $this->_data);
	}

    protected function _initTemplateData($to, $from) {
		$this->_data += XG_MessageHelper::initAboutNetwork();
		XG_Cache::profiles($this->_from, $this->_data['share_content_author']);
		if ($this->share_raw_type == 'user') {
			$this->_data['counters'] = XG_MessageHelper::getUserCounters($this->_data['share_content_author']);
		}
        $this->_data['fromProfile'] = XG_Cache::profiles($this->_from);
	}

	public function build($to, $from, $isHtml) {
		if ($to !== NULL || $from !== NULL) {
			throw new Exception("Calling ".__METHOD__."() with to/from != NULL is not allowed anymore.");
		}
		if ($isHtml != false) {
			throw new Exception("Calling ".__METHOD__."() for HTML emails is allowed anymore.");
		}
		$this->_initCommonData($to, $from);
		return $this->_build($isHtml ? 'html' : 'text');
	}

    public function send($to, $from, $requested = false) {
    	$this->_forceHtml = true;
		$this->_from = $from;
        $addlHeaders = array();
        if ($requested) {
        	$addlHeaders['X-XN_ALLOW_ADMIN_ONLY'] = 1;
		}
        $addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($from),'share');
        $addlHeaders['Reply-To'] = XG_Message::formatEmailAddress(XG_Cache::profiles($from)->email);
        $this->_sendProper($to, self::siteReturnAddress(), $addlHeaders);
    }
}

/**
 * An invitation to a group
 */
class XG_Message_Group_Invitation extends XG_Message {
    /** Name of the template file */
    protected $_template = 'group-invitation';
	protected $_group, $_from;

    public function __construct($subject, $body = null, $url = null) {
        parent::__construct($subject, $body);
        if (isset($url)) {
            $this->_data['url'] = $url;
        }

	}

	protected static $cacher;
    protected function _getCachedMessage($to, $from) {
		if (!self::$cacher) { self::$cacher = new XG_MessageCacher; }
    	return self::$cacher->getMessage($to, $from, $this->_data);
	}

	protected function _setCachedMessage($to, $from, $data) {
		return self::$cacher->setMessage($to, $from, $data, $this->_data);
	}

    protected function _initTemplateData($to, $from) {
		$this->_data += XG_MessageHelper::initAboutNetwork();

		W_Cache::getWidget('groups')->includeFileOnce('/controllers/GroupController.php');
		$options = Groups_GroupController::getGroupDisplayOptions($this->_group);
		if (!$this->_data['sparse']) {
			$th = 0;
			// Fetch counters
			W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_Filter.php');
			// Kludge for the wierd Forum + XG_GroupHelper link...
			$discussionsQuery = XN_Query::create('Content')
				->filter('owner')
				->filter('type','=','Topic')
				->filter('my.xg_forum_deleted','=',null)
				->filter('my.groupId','=',$this->_group->id)
				->end(1)
				->alwaysReturnTotalCount(TRUE);
			$discussionsQuery->execute();
	        $this->_data['counters'] = array_filter( array(
				'members' => $this->_group->my->memberCount > $th ? xg_text('N_MEMBERS', $this->_group->my->memberCount) : '',
				'discussions' => $options['forum']=='yes' && $discussionsQuery->getTotalCount() > $th ? xg_text('N_DISCUSSIONS', $discussionsQuery->getTotalCount()) : '',
				'comments' => $options['groups']=='yes' && $this->_group->my->xg_groups_commentCount > $th ? xg_text('N_COMMENTS_LC', $this->_group->my->xg_groups_commentCount) : '',
			) );
		}

        $this->_data['fromProfile'] = XG_Cache::profiles($this->_from);
        $this->_data['group'] = $this->_group;
		$this->_data['groupName'] = $this->_group->title;
	}

    /**
     * Sends the email.
     *
     * @param $to string  e-mail address or screen name of the recipient
     * @param $from string  screen name of the sender
     * @param $group XN_Content|W_Content  the group
     */
    public function send($to, $from, $group) {
    	$this->_forceHtml = true;
    	$this->_group = $group;
		$this->_from = $from;
        $addlHeaders = array();
        $addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($from),'invitations');
        $addlHeaders['Reply-To'] = XG_Message::formatEmailAddress(XG_Cache::profiles($from)->email);
        $this->_sendProper($to, $from, $addlHeaders);
    }
}

/**
 * An invitation to a event
 */
class XG_Message_Event_Invitation extends XG_Message {
    /** Name of the template file */
    protected $_template = 'event-invitation';

	protected static $cacher;
    protected function _getCachedMessage($to, $from) {
		if (!self::$cacher) { self::$cacher = new XG_MessageCacher; }
    	return self::$cacher->getMessage($to, $from, $this->_data);
	}

	protected function _setCachedMessage($to, $from, $data) {
		return self::$cacher->setMessage($to, $from, $data, $this->_data);
	}

    protected function _initTemplateData($to, $from) {
		$this->_data += XG_MessageHelper::initAboutNetwork();
	}
    /**
     * Sends the email.
     *
     * @param $to string  e-mail address or screen name of the recipient
     * @param $from string  screen name of the sender
     * @param $event XN_Content|W_Content  the event
     */
    public function send($to, $from, $event) {
    	$this->_forceHtml = true;
		W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_EventHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_TemplateHelper.php');
        $this->_data['fromProfile'] = XG_Cache::profiles($from);
        $this->_data['event'] = $event;
        $addlHeaders = array();
        $addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($from),'events');
        $addlHeaders['Reply-To'] = XG_Message::formatEmailAddress(XG_Cache::profiles($from)->email);
        $this->_sendProper($to, $from, $addlHeaders);
    }
}

/**
 * An invitation request message is a request for an invitation, it is not
 * a subclass of XG_Message_Invitation
 */
class XG_Message_Request_Invitation extends XG_Message {
    protected $_template = 'request-invitation';

    public static function create($opts) {
         return new XG_Message_Request_Invitation($opts);
    }

    public function send($from) {
        if ($from instanceof XN_Profile) {
            $this->_data['fromName'] = xg_username($from);
            $this->_data['thumbUrl'] = XG_UserHelper::getThumbnailUrl($from,96,96);
        } else {
            $this->_data['fromName'] = $from[0];
            $this->_data['thumbUrl'] = null;
        }
        $this->_data['fromAddress'] = self::siteReturnAddress();
        // BAZ-2418 - Send user to sign in because they won't be allowed to see
        //   anything if signed out!
        $manageUrl = W_Cache::getWidget('main')->buildUrl('membership', 'listRequested');
        $this->_data['inviteUrl'] = XG_AuthorizationHelper::signInUrl($manageUrl);
        $app = XN_Application::load();
        $this->_data['subject'] = xg_text('X_WOULD_LIKE_TO_JOIN_X', $this->_data['fromName'], $app->name);

        // Send to each administrator if there are any
        $admins = XG_SecurityHelper::getAdministrators();
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                $this->_sendProper($admin->contributorName, $this->_data['fromAddress'],
                    array('X-XN_ALLOW_ADMIN_ONLY' => 1));
            }
        }
    }
}

/**
 * A request for an invitation to a group.
 */
class XG_Message_Request_Group_Invitation extends XG_Message {

    /** Name of the template file */
    protected $_template = 'request-group-invitation';

    /**
     * Constructs a group-invitation-request message object.
     *
     * @return XG_Message_Request_Group_Invitation  the message object
     */
    public static function create() {
         return new XG_Message_Request_Group_Invitation(NULL);
    }

    /**
     * Emails the group administrators to notify them of a person requesting an invitation.
     *
     * @param $group XN_Content|W_Content  the group
     * @param $name string  the person's name
     * @param $usernameOrEmailAddress string  the person's username or email address
     * @param $message string  optional message provided by the person requesting the invitation
     */
    public function send($group, $name, $usernameOrEmailAddress, $message) {
        // Keep it down to 20 to avoid possible timeouts [Jon Aquino 2007-04-26]
        foreach (Group::adminProfiles($group) as $admin) {
            try {
                $this->_data['subject'] = xg_text('USER_HAS_REQUESTED_MEMBERSHIP_OF_GROUP_ON_X', $name, $group->title, XN_Application::load()->name);
                $this->_data['body'] = $message;
                $this->_data['fromName'] = $name;
                $this->_data['thumbUrl'] = mb_strpos($usernameOrEmailAddress, '@') !== false ? null : XG_UserHelper::getThumbnailUrl(XG_Cache::profiles($usernameOrEmailAddress),96,96);
                $this->_data['manageUrl'] = XG_GroupHelper::buildUrl('groups', 'user','editInvitationRequests', array('groupId' => $group->id));
                $this->_data['groupName'] = $group->title;
                $this->_data['profileUrl'] = xg_absolute_url(User::profileUrl(XN_Profile::current()->screenName));
                $profile = XG_Cache::profiles($usernameOrEmailAddress);
                $addlHeaders = array();
                $addlHeaders['From'] = $addlHeaders['Reply-To'] = XG_Message::formatEmailAddress($profile ? $profile->email : $usernameOrEmailAddress);
                $this->_sendProper($admin->screenName, $usernameOrEmailAddress, $addlHeaders);
            } catch (Exception $e) {
                if (XN_Application::load()->ownerName == 'NingDev') { xg_echo_and_throw($e->getMessage(), true); }
                error_log($e->getMessage() . ' (1535765397)');
            }
        }
    }
}

/**
 * A message to the owner from a banned user, presumably petitioning to be un-
 *   banned
 */
class XG_Message_From_Banned extends XG_Message {
    protected $_template = 'from-banned';

    public static function create($opts) {
         return new XG_Message_From_Banned($opts);
    }

    public function send($from) {
        $this->_data['fromName'] = xg_username($from);
        $this->_data['fromAddress'] = self::siteReturnAddress();
        $this->_data['thumbUrl'] = XG_UserHelper::getThumbnailUrl($from,96,96);
        $this->_data['unblockUrl'] = W_Cache::getWidget('main')->buildUrl('membership','listBanned');
        $app = XN_Application::load();
        $this->_data['subject'] = xg_text('A_BANNED_MEMBER_HAS_SENT_YOU_A_MESSAGE_ON_X', $app->name);
        //  Network creator is in the list returned by getAdministrators
        $admins = XG_SecurityHelper::getAdministrators();
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                $this->_sendProper($admin->contributorName, $this->_data['fromAddress'],
                    array('X-XN_ALLOW_ADMIN_ONLY' => 1));
            }
        }
    }
}

/**
 * A message that is a notification always comes from a special address.
 */
class XG_Message_Notification extends XG_Message {
    /**
     * What are the events that cause notifications?
     */

    /** Activity on a content object */
    const EVENT_ACTIVITY = 'activity';
    /** Activity on a content object being followed */
    const EVENT_FOLLOW_ACTIVITY = 'follow-activity';
    /** New moderated object */
    const EVENT_MODERATION_NEW = 'moderate-new';
    /** Moderated object approved or rejected*/
    const EVENT_MODERATION_DECISION = 'moderate-decision';
    /** A new member has attempted to join and must be approved */
    const EVENT_MODERATION_MEMBER = 'moderate-member';
    /** Someone has joined and others should be notified */
    const EVENT_JOIN = 'join';
    /** A new user-to-user message has been sent */
    const EVENT_USER_MESSAGE = 'user-message';
    /** A new friend request has been sent */
    const EVENT_FRIEND_REQUEST = 'friend-request';
    /** A friend request has been accepted */
    const EVENT_FRIEND_ACCEPTED = 'friend-accepted';
    /** Someone has joined; welcome to the app */
    const EVENT_WELCOME = 'welcome';
    /** Someone has joined the group; welcome to the group */
    const EVENT_GROUP_WELCOME = 'group-welcome';
    /** Someone has had their pending membership accepted */
    const EVENT_PENDING_ACCEPTED = 'accepted';
    /** Notification that an OpenSocial application message has been sent */
    const EVENT_OSAPP_NOTIFICATION = 'osapp-notification';
    
    /**
     * An array of additional headers to include when sending.
     */
    private $addlHeaders;

    public function __construct($event, $opts, $addlHeaders = null) {
        parent::__construct($opts);
        $this->_data['event'] = $event;
        $this->addlHeaders = $addlHeaders;
    }

    public function summary() {
        return "[$this->event]" . parent::summary();
    }

    /**
     * Check notification-specific sending preferences
     */
    protected function canSendMessage($user) {
        // Don't send notifications if throttling has been turned on in the configuration
        if (W_Cache::getWidget('main')->privateConfig['notifications'] == 'off') {
            self::logMessage("Not sending {$this->summary()} because throttling is on");
            return false;
        }

        // If the message is being sent to an alias, allow it - there's nothing
        //   to check
        if (mb_substr($user, -6) == '@lists') {
            return TRUE;
        }

        // Don't send the message if the global settings forbid it
        if (! parent::canSendMessage($user)) {
            return false;
        }

        if ((($user instanceof XN_Content) || ($user instanceof W_Content)) && ($user->type == 'User')) {
          $userObject = $user;
        } else {
          $userObject = User::load($user);
        }
        $canSend = false;
        // What events might the user care about?
        switch ($this->event) {
            case self::EVENT_ACTIVITY:
                $canSend = ($userObject->my->emailActivityPref == 'activity');
                break;
            case self::EVENT_MODERATION_NEW:
                $type = mb_strtolower($this->_data['type']);
                if (in_array($type, array('comment', 'chatter'))
                        && mb_strlen($userObject->my->emailCommentApprovalPref)) {
                    $canSend = ($userObject->my->emailCommentApprovalPref != 'N');
                } else {
                    $canSend = ($userObject->my->emailApprovalPref != 'N');
                }
            break;
            case self::EVENT_MODERATION_DECISION:
                $canSend = ($userObject->my->emailModeratedPref == 'each');
                break;
            case self::EVENT_MODERATION_MEMBER:
                $canSend = ($userObject->my->emailApprovalPref != 'N');
                break;
            case self::EVENT_JOIN:
                $canSend = ($userObject->my->emailInviteeJoinPref != 'N');
                break;
            case self::EVENT_USER_MESSAGE:
                $canSend = ($userObject->my->emailNewMessagePref != 'N');
                break;
            case self::EVENT_FRIEND_REQUEST:
                $canSend = ($userObject->my->emailFriendRequestPref != 'N');
                break;
            case self::EVENT_FRIEND_ACCEPTED:
                /* BAZ-1591 -- no alerts on acceptance for now */
                $canSend = false;
                break;
            case self::EVENT_WELCOME:
            case self::EVENT_PENDING_ACCEPTED:
                $canSend = true;
                break;
            case self::EVENT_GROUP_WELCOME:
                $canSend = true;
                break;
            case self::EVENT_OSAPP_NOTIFICATION:
                $canSend = ($userObject->my->emailViaApplicationsPref != 'N');
                break;
            default:
                throw new Exception("Unknown event: $this->event");
        }
        self::logMessage("Can send notification {$this->summary()} to {$userObject->contributorName} ? " . intval($canSend));
        return $canSend;
     }

    /**
     * Send the notification message to the specified user
     *
     * @param $user User|XN_Profile|string The user to potentially send the notification to.
     * @param $copyToAdmins boolean Send a copy to each administrator?
     */
    public function send($user, $sendToAdmins = FALSE) {
        if ((($user instanceof XN_Content) || ($user instanceof W_Content)) && ($user->type == 'User')) {
          $userObject = $user;
        } else {
          $userObject = User::load($user);
        }
        if ($userObject) {
			$to = $userObject->contributorName ? $userObject->contributorName : $userObject->title; // if contributorName is empty, use title BAZ-8509 [Andrey 2008-07-22]
            // log if userObject->title != userObject->contributorName (EOC-192) [ywh 2008-07-28]
            if ($userObject->title !== $userObject->contributorName) {
                error_log('EOC-192: User [' . $userObject->id . '] has title [' . $userObject->title . '] but contributorName [' . $userObject->contributorName . ']');
            }
        }
        else {
            $to = $user;
        }

        $from = self::siteReturnAddress();

        //  BAZ-732: Send copies to administrators if requested
        if ($sendToAdmins) {
            $admins = XG_SecurityHelper::getAdministrators();
            if (count($admins) > 0) {
                foreach ($admins as $admin) {
                    if ($to == $admin->contributorName) { continue; }
                    $this->_sendProper($admin->contributorName, $from, $this->addlHeaders);
                }
            }
        }
        return $this->_sendProper($to, $from, $this->addlHeaders);
    }

    /**
     * Create a new XG_Message_Notification with standard subject and body depending
     * on the event type.
     *
     * @param $event string The event that the notification is for. Should be one of the
     *    XG_Message_Notification::EVENT_* constants.
     * @param $opts array An array of event-specific options and data to include in the message:
     * @param ... The rest of the parameters vary based on event type:
     *         EVENT_ACTIVITY: array('viewActivity' => , // Introduction to a link to the activity
     *             'activity' => , // A description of the activity: There is a new comment on your photo 'Nice pants.'
     *             'content' => , // The content object on which there was activity
     *             'thumb' => , // optional URL to a thumbnail image to include in the message
     *             'type' => , // optional What type there was activity, to go in the sentence "There was new activity on a X of yours." If left out, the lowercased $content->type is used
     *             'url' => , // optional The target URL for the message; defaults to the content object's detail page
     *             'reason' => ) // The reason why the message was sent: somebody commented on a photo you've added to The Group Name
     *         EVENT_MODERATION_NEW: array('content' => , // The content object that needs moderating
     *             'reason' => , // the reason why the message was sent: somebody has uploaded a photo to The Group Name
     *             'moderationUrl' => , // The URL where the moderation can happen
     *             'thumb' => , // optional URL to a thumbnail image to include in the message
     *             'type' => ) // optional What type to moderate, to go in the sentence "You have a new X to moderate!" If left out, the lowercased $content->type is used
     *         EVENT_MODERATION_DECISION: array('content' => , // The content object that was moderated
     *             'thumb' => , // optional URL to a thumbnail image to include in the message
     *             'type' => ) //  optional What type was moderated, to go in the sentence "Your X was approved/deleted." If left out, the lowercased $content->type is used
     *         EVENT_MODERATION_MEMBER: array('joiner' => ) // the screen name or User object or XN_Profile object of the user that wants to join
     *         EVENT_JOIN: array('joiner' => ) // the screen name or User object or XN_Profile object of the user that joined
     *         EVENT_USER_MESSAGE: array('profile' => ) // the screen name or User object or XN_Profile object of the message sender
     *         EVENT_FRIEND_REQUEST: array('profile' => ) // the screen name or User object or XN_Profile object of the friend requester
     *         EVENT_FRIEND_ACCEPTED: array('profile' => ) // the screen name or User object or XN_Profile object of the friend request accepter
     *         EVENT_WELCOME: array('profile' => ) // the screen name or User object or XN_Profile object of the person to welcome
     *         EVENT_GROUP_WELCOME: array('profile' => , 'group' =>) // the screen name or User object or XN_Profile object of the person to welcome
     *         EVENT_PENDING_ACCEPTED: array('profile' =>) // the screen name or User object or XN_Profile object of the user that was accepted
     */

    protected static $eventOpts = array(
            self::EVENT_ACTIVITY => array('must' => array('viewActivity', 'activity','content','reason'), 'may' => array('thumb','type', 'url'), 'template' => 'notify-activity'),
            self::EVENT_FOLLOW_ACTIVITY => array('must' => array('activity','content','unfollowLink','viewActivity'), 'may' => array('thumb', 'type', 'url'), 'template' => 'notify-follow-activity'),
            self::EVENT_MODERATION_NEW => array('must' => array('content','reason','moderationUrl'), 'may'  => array('thumb','type'), 'template' => 'notify-moderate'),
            self::EVENT_MODERATION_DECISION => array('must' => array('content'), 'may' => array('thumb','type'), 'template' => 'notify-moderate-decision'),
            self::EVENT_MODERATION_MEMBER => array('must' => 'joiner', 'template' => 'notify-moderate-member'),
            self::EVENT_JOIN => array('must' => 'joiner', 'template' => 'invitee-join'),
            self::EVENT_USER_MESSAGE => array('must' => 'profile', 'template' => 'user-message'),
            self::EVENT_FRIEND_REQUEST => array('must' => 'profile', 'template' => 'friend-request'),
            self::EVENT_FRIEND_ACCEPTED => array('must' => 'profile', 'template' => 'friend-accepted'),
            self::EVENT_WELCOME => array('must' => 'profile', 'template' => 'welcome'),
            self::EVENT_GROUP_WELCOME => array('must' => array('profile', 'group'), 'template' => 'group-welcome'),
            self::EVENT_PENDING_ACCEPTED => array('must' => 'profile', 'template' => 'accepted'),
            self::EVENT_OSAPP_NOTIFICATION => array('must' => array('profile', 'osAppTitle', 'viewOSAppUrl'), 'template' => 'osapp-notification')
            );

    public static function create($event, $opts) {
        // Make sure we know about the event
        if (! isset(self::$eventOpts[$event])) {
            throw new Exception("Unknown notification event: $event");
        }
        // Make sure all mandatory opts are present
        if (is_array(self::$eventOpts[$event]['must'])) {
            foreach (self::$eventOpts[$event]['must'] as $mandatory) {
                if (! isset($opts[$mandatory])) {
                    throw new Exception("Event $event missing $mandatory, needs " . implode(',',self::$eventOpts[$event]['must']));
                }
            }
        }

        // Fixup thumb URLs
        if (isset($opts['thumb'])) {
            $opts['thumb'] = XG_Message::prepareThumbnailUrl($opts['thumb']);
        }

        $app = XN_Application::load();

        // Do any additional event-specific work
        if ($event == self::EVENT_ACTIVITY) {
            if (! isset($opts['type'])) { $opts['type'] = mb_strtolower($opts['content']->type); }
            if (! isset($opts['subject'])) {
                $opts['subject'] = xg_text('USER_COMMENTED_ON_YOUR_OBJECT_ON_X', xg_username(XN_Profile::current()), $opts['type'], $app->name);
            }
            if (! isset($opts['url'])) { $opts['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/' . $opts['content']->id; }
        }
        else if ($event == self::EVENT_FOLLOW_ACTIVITY) {
            if (! isset($opts['type'])) { $opts['type'] = mb_strtolower($opts['content']->type); }
            if (! isset($opts['subject'])) {
                if (in_array(mb_strtolower($opts['type']), array('discussion', 'topic'))) {
                    $opts['subject'] = xg_text('USER_REPLIED_TO_A_DISCUSSION_ON_Y',
                            xg_username(XN_Profile::current()), $app->name);
                    $opts['viewActivity'] = xg_text('TO_VIEW_THE_NEW_REPLY_VISIT');
                } else if (in_array(mb_strtolower($opts['type']), array('blog post'))) {
                    $opts['subject'] = xg_text('X_ADDED_A_COMMENT_TO_A_BLOG_POST_ON_Y',
                            xg_username(XN_Profile::current()), $app->name);
                    $opts['viewActivity'] = xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT');
                } else {
                    $opts['subject'] = xg_text('USER_COMMENTED_ON_A_TYPE_ON_APPNAME',
                            xg_username(XN_Profile::current()), $opts['type'], $app->name, $opts['content']->title ? $opts['content']->title : xg_text('UNTITLED'));
                    $opts['viewActivity'] = xg_text('TO_VIEW_THE_NEW_COMMENT_VISIT');
                }
            }
            if (! isset($opts['url'])) { $opts['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/' . $opts['content']->id; }
        }
        else if ($event == self::EVENT_MODERATION_NEW) {
            $opts['contentAdder'] = XG_Cache::profiles($opts['content']->contributorName);
            if (! isset($opts['type'])) { $opts['type'] = mb_strtolower($opts['content']->type); }
            $opts['subject'] = xg_text('YOU_HAVE_A_NEW_X_TO_APPROVE_ON_Y', $opts['type'], $app->name);
        }
        else if ($event == self::EVENT_MODERATION_DECISION) {
            if (! isset($opts['type'])) { $opts['type'] = mb_strtolower($opts['content']->type); }
            $opts['subject'] = xg_text('YOUR_X_ON_Y_HAS_BEEN_APPROVED', $opts['type'], $app->name);
        }
        else if ($event == self::EVENT_MODERATION_MEMBER) {
            $opts['joiner'] = self::profileFromAnySource($opts['joiner']);
            $opts['subject'] = xg_text('YOU_HAVE_A_NEW_MEMBER_TO_APPROVE_ON_X', $app->name);
        }
        else if ($event == self::EVENT_JOIN) {
            $opts['joiner'] = self::profileFromAnySource($opts['joiner']);
            $opts['subject'] = xg_text('X_IS_NOW_A_MEMBER_OF_Y', xg_username($opts['joiner']), $app->name);
        }
        else if ($event == self::EVENT_USER_MESSAGE) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            // BAZ-1737: If the user has specified a subject, use it.
            if (! (isset($opts['subject']) && mb_strlen($opts['subject']))) {
                $opts['subject'] = xg_text('USER_HAS_SENT_YOU_A_MESSAGE_ON_X', xg_username($opts['profile']), $app->name);
            }
            $addlHeaders = array();
            $addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($opts['profile']));
        }
        else if ($event == self::EVENT_FRIEND_REQUEST) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            $opts['isMember'] = self::userIsMember($opts['profile']);
            $opts['subject'] = xg_text('X_HAS_ADDED_YOU_AS_A_FRIEND_ON_Y', xg_username($opts['profile']), $app->name);
            $addlHeaders = array();
			$addlHeaders['From'] = XG_Message::localEmail(XG_UserHelper::getFullName($opts['profile']));
        }
        else if ($event == self::EVENT_FRIEND_ACCEPTED) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            $opts['subject'] = xg_text('X_HAS_ACCEPTED_YOUR_FRIEND_REQUEST', xg_username($opts['profile']), $app->name);
        }
        else if ($event == self::EVENT_WELCOME) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            $opts['subject'] = xg_text('WELCOME_TO_X', XN_Application::load()->name);
        }
        else if ($event == self::EVENT_GROUP_WELCOME) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            $opts['subject'] = xg_text('WELCOME_TO_GROUP_X_ON_Y', $opts['group']->title, XN_Application::load()->name);
        }
        else if ($event == self::EVENT_PENDING_ACCEPTED) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            $opts['subject'] = xg_text('YOUR_X_MEMBERSHIP_HAS_BEEN_APPROVED', XN_Application::load()->name);
        }
        else if ($event == self::EVENT_OSAPP_NOTIFICATION) {
            $opts['profile'] = self::profileFromAnySource($opts['profile']);
            if (!isset($opts['subject']) || !mb_strlen($opts['subject'])) {
                $opts['subject'] = xg_text("MESSAGE_VIA_APPNAME", $opts['osAppTitle']);
            }
        }
        
        $msg = new XG_Message_Notification($event, $opts, $addlHeaders);

        // Set the template
        $msg->setTemplate(self::$eventOpts[$event]['template']);

        return $msg;
    }

    /**
     * Find the XN_Profile object associated with $source
     *
     * @param $source mixed Can be a screen name, XN_Profile object, or XN_Content object
     * @return XN_Profile
     */
    protected static function profileFromAnySource($source) {
        try {
            $profile = null;
            if ((($source instanceof XN_Content) || ($source instanceof W_Content)) && ($source->type == 'User')) {
                $profile = XG_Cache::profiles($source->contributorName);
            }
            else if ($source instanceof XN_Profile) {
                $profile = $source;
            }
            else {
                $profile = XG_Cache::profiles((string) $source);
            }
        } catch (Exception $e) {
        }
        if (! $profile) {
            throw new Exception("Can't turn $source into an XN_Profile object");
        }
        return $profile;
    }

    /**
     * Determine if the user is a member of the network
     *
     * @param $user
     * @return boolean
     */
    protected static function userIsMember($user) {
        try {
            $isMember = User::isMember($user);
            if ($isMember == true) {
                return true;
            } else {
                return false;
            }
        } catch(Exception $e) {
            // return false if the user object can't be loaded.
            return false;
        }
    }

}

/**
 * XG_Message_AdminWithCopy is for administrative messages that
 * get sent to the app administrators and a system address
 */
class XG_Message_AdminWithCopy extends XG_Message {
    protected $_reportCopyTo = 'support@ning.com';

    public function __construct() {
         $args = func_get_args();
         call_user_func_array(array('parent','__construct'), $args);
         $this->_data['to'] = XN_Application::load()->ownerName;
         if(!$this->_data['subject']) {
            $this->setDefaultSubject();
         }
    }

    public function send($from) {
        // Send to each administrator if there are any
        $admins = XG_SecurityHelper::getAdministrators();
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                $this->_sendProper($admin->contributorName, $from, array('X-XN_ALLOW_ADMIN_ONLY' => 1));
            }
        }

        // DON'T send a copy to Ning for now...  (BAZ-4416)
        // $this->_sendProper($this->_reportCopyTo, $from, array('X-XN_ALLOW_ADMIN_ONLY' => 1));
        
        // send copy now only if sendBccTo is passed in the params
        if (isset($this->_data['sendBccTo']))
            $this->_sendProper($this->_data['sendBccTo'], $from, array('X-XN_ALLOW_ADMIN_ONLY' => 1));
    }

    public function canSendMessage($user) {
        if ((($user instanceof XN_Content) || ($user instanceof W_Content)) && ($user->type == 'User')) {
            $userObject = $user;
        } else {
            $userObject = User::load($user);
        }
        if (! parent::canSendMessage($user)) {
            return false;
        }
        return ($userObject->my->emailAdminMessagesPref != 'N');
    }

    protected function setDefaultSubject() {
        if (XN_Profile::current()->isLoggedIn()) {
            $name = xg_username(XN_Profile::current());
        } else {
            $name = xg_text('SOMEBODY');
        }
        if (XN_Profile::current()->isLoggedIn()) {
            $name = xg_username(XN_Profile::current());
        }
        $this->_data['subject'] = xg_text('X_REPORTED_AN_ISSUE_ON_Y', $name, XN_Application::load()->name);
    }

    /**
     * Returns metadata such as network name, user's name, email, and user-agent.
     *
     * @return string  metadata suitable for inclusion as part of the message body.
     */
    protected function additionalInformation() {
        $name = XG_UserHelper::getFullName(XN_Profile::current());
        $name .= $name ? ' (' . XN_Profile::current()->screenName . ')' : XN_Profile::current()->screenName;
        return "\n"
                . "\nNetwork:    " . XN_Application::load()->relativeUrl
                . "\nUser:       " . $name
                . "\nEmail:      " . XN_Profile::current()->email
                . "\nUser-Agent: " . $_SERVER['HTTP_USER_AGENT'];
    }
}

/**
 * XG_Message_ReportThis is for the user-issue-report messages
 */
class XG_Message_ReportThis extends XG_Message_AdminWithCopy {
    protected $_template = 'report-this';
    protected $_reportCopyTo = 'support@ning.com';

    public static function create($opts) {
        $opts['body'] .= self::additionalInformation();
         return new XG_Message_ReportThis($opts);
    }
}

/**
 * XG_Message_Feedback is for general user feedback
 */
class XG_Message_Feedback extends XG_Message_AdminWithCopy {
    protected $_template = 'feedback';
    protected $_reportCopyTo = 'feedback@ning.com';

    public static function create($opts) {
        $opts['body'] .= self::additionalInformation();
        return new XG_Message_Feedback($opts);
    }
    protected function setDefaultSubject() {
       //TODO this pattern appears in a lot of places - create some shared code.
       if (XN_Profile::current()->isLoggedIn()) {
           $name = xg_username(XN_Profile::current());
       } else {
           $name = xg_text('SOMEBODY');
       }
       $this->_data['subject'] = xg_text('X_PROVIDED_FEEDBACK_ON_Y', $name, XN_Application::load()->name);
    }
}

 /**
 * Notification that a discussion has been started
 */
class XG_Message_New_Topic extends XG_Message {

    /** Name of the template file */
    protected $_template = 'new-topic';

    /**
     * Sends the email.
     *
     * @param $to string  e-mail address or screen name of the recipient
     * @param $topic XN_Content|W_Content  the Topic
     * @param $url string  the URL of the discussion
     * @param $unsubscribeUrl string  the URL of the page for stopping this notification
     */
    public function send($to, $topic, $url, $unsubscribeUrl) {
        $this->_data['subject'] = xg_text('X_STARTED_DISCUSSION_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($topic->contributorName)), XN_Application::load()->name);
        $this->_data['topic'] = $topic;
        $this->_data['url'] = $url;
        $this->_data['unsubscribeUrl'] = $unsubscribeUrl;
        $this->_sendProper($to, self::siteReturnAddress(), array('X-XN_ALLOW_ADMIN_ONLY' => 1));
    }
}

 /**
 * Notification of new activity for a Group
 */
class XG_Message_New_Group_Activity extends XG_Message {

    /** Name of the template file */
    protected $_template = 'new-group-activity';

    /**
     * Sends the email.
     *
     * @param $to string  e-mail address or screen name of the recipient
     * @param $object XN_Content|W_Content  the new activity object (member, discussion topic, or comment wall comment)
     * @param $group XN_Content|W_Content the group object for the activity
     * @param $url string  the URL of the activity
     * @param $unsubscribeUrl string  the URL of the page for stopping this notification
     */
    public function send($to, $object, $group, $url, $unsubscribeUrl) {
        $groupName = preg_replace('/&#039;/u', '\'', xnhtmlentities($group->title));
        switch($object->type) {
            case 'Topic':
                $this->_data['subject'] = xg_text('X_STARTED_DISCUSSION_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $groupName, XN_Application::load()->name);
                break;
            case 'Comment':
                $this->_data['subject'] = xg_text('X_LEFT_COMMENT_IN_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $groupName, XN_Application::load()->name);
                break;
            case 'GroupMembership':
                $this->_data['subject'] = xg_text('X_JOINED_GROUP_ON_APPNAME', XG_UserHelper::getFullName(XG_Cache::profiles($object->contributorName)), $groupName, XN_Application::load()->name);
                break;
        }
        $this->_data['object'] = $object;
        $this->_data['group'] = $group;
        $this->_data['url'] = $url;
        $this->_data['unfollowUrl'] = $unsubscribeUrl;
        $this->_sendProper($to, self::siteReturnAddress());
    }
}

/**
 * Notification that the user has changed her email address.
 * Can be sent to either the user's old or new email addresses.
 */
class XG_Message_ChangedEmailAddress extends XG_Message {

    /** Name of the template file */
    protected $_template = 'changed-email-address';

    /**
     * Sends the email.
     *
     * @param $to string  the old e-mail address
     */
    public function send($to) {
        $this->_data['subject'] = xg_text('YOU_CHANGED_YOUR_EMAIL_ON_X', XN_Application::load()->name);
        $this->_data['contactUsUrl'] = 'http://help.ning.com';
        $this->_data['signInUrl'] = W_Cache::getWidget('main')->buildUrl('authorization', 'signIn');
        $this->_sendProper($to, self::siteReturnAddress());
    }
}
