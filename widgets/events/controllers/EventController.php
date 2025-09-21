<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');
XG_App::includeFileOnce('/lib/XG_DateHelper.php');
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
/**
 * Dispatches requests pertaining to Events.
 */
class Events_EventController extends W_Controller {

    /**
     * Constructor.
     *
     * @param   $widget     W_BaseWidget    The Events widget
     */
    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_TemplateHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_RequestHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        EventWidget::init();
    }

    /** Number of featured events to display. */
    const   NUM_FEATURED    = 5;

    /** Number of events per page. */
    const   PAGE_SIZE       = 10;

//** LISTS
    /**
     *  Displays a list of current and future events.
     */
    public function action_listUpcoming() {
        $this->_prepareSideBlock('home');
        if (!$this->eventTypes || (!count($this->eventList = Event::getUpcomingEvents(self::PAGE_SIZE)))) {
            $this->noAddLink = !Events_SecurityHelper::currentUserCanCreateEvent();
            $this->title = $this->pageTitle = xg_text('ALL_EVENTS');
            if (!$this->noAddLink) { $this->subHeader = xg_text('ADD_AN_EVENT'); }
            $this->message = xg_text('NOBODY_HAS_ADDED_EVENTS');
            return $this->render('listEmpty');
        }
        $this->featuredEvents   = $this->eventList->isFirstPage() ? Event::getFeaturedEvents(self::NUM_FEATURED, true) : new XG_PagingList();
        $this->title            = xg_text('ALL_EVENTS');
        $this->pageTitle        = xg_text('EVENTS');
        $this->_loadRsvp(NULL, $this->eventList->getList(), $this->featuredEvents->getList());
        $this->feedUrl          = (XG_App::appIsPrivate() ? null : $this->_buildUrl('event', 'feed', array('xn_auth' => 'no')));
        $this->render('listEvents');
    }


    /**
     *  Displays a list of events for a given event type.
     *
     *  @param    $type     string  The event type
     */
    public function action_listByType() {
        $type = Events_RequestHelper::readString($_GET, 'type');
        if ($type === '') {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->_prepareSideBlock('home');

        $this->featuredEvents	= new XG_PagingList();
        list($this->featuredEvent, $this->eventList) = Event::getEventsByType($type,self::PAGE_SIZE);
        $this->title            = xg_text('ALL_TYPE_EVENTS',$type);
        $this->pageTitle        = xg_text('EVENTS');
        $this->_loadRsvp(NULL, $this->eventList->getList(), $this->featuredEvent);
        $this->render('listEvents');
    }

    /**
     *  Displays a list of all event types
     */
    public function action_listAllTypes() {
        $this->_prepareSideBlock('home');
        $this->pageTitle = xg_text('EVENTS');
        $this->title = xg_text('ALL_EVENT_TYPES');
        $this->render('listAllTypes');
    }

    /**
     *  Displays a list of events for a given day.
     *
     *  @param    $date string      YYYY-MM-DD
     */
    public function action_listByDate() {
        $date = Events_RequestHelper::readDate($_GET);
        if ($date === null) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $start = XG_DateHelper::format('Y-m',$date,'-1 month');
        $end = XG_DateHelper::format('Y-m',$date,'+1 month');

        $this->_prepareSideBlock('home', NULL, TRUE);

        // get the calendar for 3 months (to increase the probability that we don't need to make an extra query)
        $this->featuredEvents   = new XG_PagingList();
        $this->calendar         = EventCalendar::getCalendar($start,$end);
        $this->calendarToday    = $date;
        list($this->featuredEvent, $this->eventList) = Event::getEventsByDate($date, $this->calendar);
        unset($this->calendar[$start]); // remove the previous month, we don't need it anymore.
        $this->wrapDate         = 0;
        $this->pageTitle        = xg_text('EVENTS');
        $this->title            = xg_date(xg_text('EVENT_TITLE_FMT'),Events_EventHelper::dateToTs($date));

        $this->_loadRsvp(NULL, $this->eventList->getList(), $this->featuredEvent);
        $this->render('listEvents');
    }

    /**
     *  Displays a list of events for a general location, such as "The Fillmore".
     *
     *  @param    $location   string    Location string (exact match)
     */
    public function action_listByLocation() {
        $location = Events_RequestHelper::readString($_GET, 'location');
        if ($location === '') {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->_prepareSideBlock('home');

        $this->featuredEvents = new XG_PagingList();
        list($this->featuredEvent, $this->eventList) = Event::getEventsByLocation($location,self::PAGE_SIZE);
        $this->pageTitle = xg_text('EVENTS');
        $this->title = xg_text('ALL_EVENTS_IN',$location);

        $this->_loadRsvp(NULL, $this->eventList->getList(), $this->featuredEvent);
        $this->render('listEvents');

    }

    /**
     *  Displays a list of featured events, sorted by event date.
     */
    public function action_listFeatured() {
        $this->_prepareSideBlock('home');

        $this->eventList        = Event::getFeaturedEvents(self::PAGE_SIZE);
        $this->featuredEvents   = new XG_PagingList();
        $this->pageTitle        = xg_text('EVENTS');
        $this->title            = xg_text('ALL_FEATURED_EVENTS');

        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of upcoming events for the current user.
     */
    public function action_listMyEvents() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_prepareSideBlock('my');

        $this->pageTitle = $this->title = xg_text('MY_EVENTS');

        if ((!count($this->eventList = EventAttendee::getUpcomingEvents($this->_user->screenName, self::PAGE_SIZE)))) {
            $this->noAddLink = !Events_SecurityHelper::currentUserCanCreateEvent();
            if (!$this->noAddLink) { $this->subHeader = xg_text('ADD_AN_EVENT'); }
            $this->message = xg_text('YOU_HAVEN_T_ADDED');
            return $this->render('listEmpty');
        }

        $this->featuredEvents 	= new XG_PagingList();
        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of events for a given event type, for the current user.
     *
     *  @param    $type     string  The event type
     */
    public function action_listMyEventsByType() {
        XG_SecurityHelper::redirectIfNotMember();
        $type = Events_RequestHelper::readString($_GET, 'type');
        if ($type === '') {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->_prepareSideBlock('my');

        $this->eventList        = EventAttendee::getEventsByType($this->_user->screenName, $type, self::PAGE_SIZE);
        $this->featuredEvents   = new XG_PagingList();
        $this->pageTitle        = xg_text('MY_EVENTS');
        $this->title            = xg_text('ALL_MY_TYPE_EVENTS',$type);

        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of my event types
     */
    public function action_listMyAllTypes() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_prepareSideBlock('my');
        $this->pageTitle = xg_text('MY_EVENTS');
        $this->title = xg_text('ALL_MY_EVENT_TYPES');
        $this->render('listAllTypes');
    }

    /**
     *  Displays a list of events for a given date, for the current user.
     *
     *  @param    $date string      YYYY-MM-DD
     */
    public function action_listMyEventsByDate() {
        XG_SecurityHelper::redirectIfNotMember();
        $date = Events_RequestHelper::readDate($_GET);
        if ($date === null) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $start = XG_DateHelper::format('Y-m',$date,'-1 month');
        $end = XG_DateHelper::format('Y-m',$date,'+1 month');

        $this->_prepareSideBlock('my', NULL, TRUE);

        $this->featuredEvents   = new XG_PagingList();
        $this->calendarToday    = $date;
        // get the calendar for 3 months (to increase the probability that we don't need to make an extra query)
        $this->calendar         = EventAttendee::getCalendar($this->_user->screenName, $start, $end);
        $this->eventList		= EventAttendee::getEventsByDate($this->_user->screenName, $date, $this->calendar);
        unset($this->calendar[$start]); // remove the previous month, we don't need it anymore.
        $this->wrapDate         = 0;
        $this->pageTitle        = xg_text('MY_EVENTS');
        $this->title            = xg_text('ALL_MY_EVENTS_AT',xg_date(xg_text('EVENT_TITLE_FMT'),Events_EventHelper::dateToTs($date)));

        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of events that the current user has decided not to attend.
     */
    public function action_listMyNotAttendingEvents() {
        XG_SecurityHelper::redirectIfNotMember();
        $this->_prepareSideBlock('my');

        $this->eventList        = EventAttendee::getNotAttendingEvents($this->_user->screenName, self::PAGE_SIZE);
        $this->featuredEvents   = new XG_PagingList();
        $this->pageTitle        = xg_text('MY_EVENTS');
        $this->title            = xg_text('EVENTS_I_AM_NOT_ATTENDING');

        //in this case it's rather obvious, because we asking for "not attending events" :) so we can avoid making the query
        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of upcoming events for the specified user.
     *
     *  @param	$user	string		User screenName
     */
    public function action_listUserEvents() {
        $user = Events_RequestHelper::readScreenName($_GET, 'user');
        if ($user === null || !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
		if ($this->_isMe($user)) {
			return $this->action_listMyEvents();
		}
        $this->_prepareSideBlock('user',$user);

        $this->title            = xg_text('USER_EVENTS', xg_username($user));
        $this->pageTitle        = $this->title;

        if ((!count($this->eventList = EventAttendee::getUpcomingEvents($user, self::PAGE_SIZE)))) {
            $this->noAddLink = true;
            $this->message = xg_html('X_HASN_T_ADDED', xg_username($user));
            return $this->render('listEmpty');
        }
        $this->featuredEvents 	= new XG_PagingList();

        $this->_loadRsvp($user, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of events for a given event type, for the current user.
     *
     *  @param	$user	string		User screenName
     *  @param	$type   string  	The event type
     */
    public function action_listUserEventsByType() {
        $user = Events_RequestHelper::readScreenName($_GET, 'user');
        if ($user === null || !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
                if ($this->_isMe($user)) {
                        return $this->action_listMyEventsByType();
                }
        $type = Events_RequestHelper::readString($_GET, 'type');
        if ($type === '') {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->_prepareSideBlock('user',$user);

        $this->eventList        = EventAttendee::getEventsByType($user, $type, self::PAGE_SIZE);
        $this->featuredEvents   = new XG_PagingList();
        $this->pageTitle        = xg_text('USER_EVENTS', xg_username($user));
        $this->title            = xg_text('ALL_USER_TYPE_EVENTS', xg_username($user), $type);

        $this->_loadRsvp($user, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of users event types
     *
     *  @param	$user	string		User screenName
     */
    public function action_listUserAllTypes() {
        $user = Events_RequestHelper::readScreenName($_GET, 'user');
        if ($user === null || !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
		if ($this->_isMe($user)) {
			return $this->action_listMyAllTypes();
		}
        $this->_prepareSideBlock('user',$user);
        $this->pageTitle = xg_text('USER_EVENTS', xg_username($user));
		$this->title = xg_text('ALL_USER_EVENT_TYPES', xg_username($user));
        $this->render('listAllTypes');
    }

    /**
     *  Displays a list of events for a given date, for the current user.
     *
     *  @param	$user	string		User screenName
     *  @param	$date 	string      YYYY-MM-DD
     */
    public function action_listUserEventsByDate() {
        $user = Events_RequestHelper::readScreenName($_GET, 'user');
        if ($user === null || !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
                if ($this->_isMe($user)) {
                        return $this->action_listMyEventsByDate();
                }
        $date = Events_RequestHelper::readDate($_GET);
        if ($date === null) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $start = XG_DateHelper::format('Y-m',$date,'-1 month');
        $end = XG_DateHelper::format('Y-m',$date,'+1 month');

        $this->_prepareSideBlock('user', $user, TRUE);

        $this->featuredEvents   = new XG_PagingList();
        $this->calendarToday    = $date;
        // get the calendar for 3 months (to increase the probability that we don't need to make an extra query)
        $this->calendar         = EventAttendee::getCalendar($user, $start, $end);
        $this->eventList		= EventAttendee::getEventsByDate($user, $date, $this->calendar);
        unset($this->calendar[$start]); // remove the previous month, we don't need it anymore.
        $this->wrapDate         = 0;
        $this->pageTitle 		= xg_text('USER_EVENTS', xg_username($user));
        $this->title            = xg_text('ALL_USER_EVENTS_AT', xg_username($user), xg_date(xg_text('EVENT_TITLE_FMT'),Events_EventHelper::dateToTs($date)));
        $this->_loadRsvp($user, $this->eventList->getList());
        $this->render('listEvents');
    }

    /**
     *  Displays a list of events that the current user has decided not to attend.
     *
     *  @param	$user	string		User screenName
     */
    public function action_listUserNotAttendingEvents() {
        $user = Events_RequestHelper::readScreenName($_GET, 'user');
        if ($user === null || !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
		if ($this->_isMe($user)) {
			return $this->action_listMyNotAttendingEvents();
		}
        $this->_prepareSideBlock('user',$user);

        $this->eventList        = EventAttendee::getNotAttendingEvents($user, self::PAGE_SIZE);
        $this->featuredEvents   = new XG_PagingList();
        $this->pageTitle 		= xg_text('USER_EVENTS', xg_username($user));
        $this->title            = xg_text('EVENTS_USER_NOT_ATTENDING', xg_username($user));

        //in this case it's rather obvious, because we asking for "not attending events" :) so we can avoid making the query
        $this->_loadRsvp($user, $this->eventList->getList());
        $this->render('listEvents');
    }
//** OTHER
    /**
     *  Displays events matching the given search terms
     *
     *  @param    $q        string      Search terms
     */
    public function action_search() {
        $this->_prepareSideBlock('home');

        $this->searchTerms      = Events_RequestHelper::readQuery($_GET, 'q');
        $this->eventList        = Event::searchEvents($this->searchTerms, self::PAGE_SIZE);
        $this->title            = xg_text('SEARCH_RESULTS');
        $this->pageTitle        = xg_text('EVENTS');

        $this->_loadRsvp(NULL, $this->eventList->getList());
        $this->render('search');
    }

    /**
     *  Displays the detail page for an event. $object can be Event or Comment object.
     *
     *  @param    $id               string      Event-ID
     *  @param    $rsvpConfirm      bool        Whether to display the "your rsvp is confirmed" msg.
     */
    public function action_show($object = NULL) {
        if ($object) {
            if ($object->type == 'Event') 		{ $this->event = $object; }
            elseif ($object->type == 'Comment') { $this->event = Event::byId($object->my->attachedTo); }
            else 								{ $this->event = NULL; }
        } else {
            $eventId = Events_RequestHelper::readEventId($_GET);
            $this->event = $eventId ? Event::byId($eventId) : null;
        }
        if (!$this->event) {
            $this->noAddLink = !Events_SecurityHelper::currentUserCanCreateEvent();
            $this->pageTitle = xg_text('EVENTS');
            $this->title = xg_text('CANCELLED_EVENT');
            $this->message = xg_text('WE_ARE_SORRY_EVENT_CANCELLED');
            return $this->render('listEmpty');
        }
        XG_CommentHelper::stopFollowingIfRequested($this->event);

        $eventAttendee                  = EventAttendee::load($this->_user->screenName, $this->event);
        $this->rsvp                     = $eventAttendee ? $eventAttendee->my->status : EventAttendee::NOT_INVITED;
        $this->inviter                  = $eventAttendee ? $eventAttendee->my->inviter : null;
        $this->isAdmin                  = XG_SecurityHelper::userIsAdmin();
        $this->isMyEvent                = $this->_user->screenName == $this->event->contributorName;
        $this->canAccessEventDetails    = Events_SecurityHelper::currentUserCanAccessEventDetails($this->event, $this->rsvp);
        $this->showInviteLink           = Events_SecurityHelper::currentUserCanSendInvites($this->event, $this->rsvp);

        list($this->prevEvent, $this->nextEvent) = Event::getPrevNextEvents($this->event);

        if (!$this->event->my->disableRsvp) {
            if (Events_RequestHelper::readBoolean($_GET, 'rsvpConfirm'))               { $this->rsvpMsg = $this->rsvp; }
            else if ($this->event->my->isClosed)    { $this->rsvpMsg = 'event_is_full'; }
            else if (!$this->canAccessEventDetails) { $this->rsvpMsg = $this->rsvp; }
            else if ($this->rsvp == EventAttendee::NOT_RSVP) { $this->rsvpMsg = $this->rsvp; }
            if ($this->canAccessEventDetails) {
                list(,$this->attendeesHtml) = $this->event->my->showGuestList || $this->isMyEvent || $this->isAdmin
                                        ? $this->_fetchEventAttendeesStats()
                                        : array('','
                        <div class="xg_module">
                            <div class="xg_module_head"><h2>'.xg_html('PRIVATE_GUEST_LIST').'</h2></div>
                            <div class="xg_module_body"><p>'.xg_html('USER_HID_GUESTS',xnhtmlentities(xg_username($this->event->contributorName))).'</p></div>
                        </div>');
            }
        }
        $this->title                    = $this->event->title;
    }

    /**
     *  Displays a list of users with the given RSVP status.
     *
     *  @param    $id       string      Event-ID
     *  @param    $status   string      RSVP string (see Events_EventHelper::rsvpToStr)
     */
    public function action_showAttendees() {
        $eventId = Events_RequestHelper::readEventId($_GET);
        if (!$eventId || !$this->event = Event::byId($eventId)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        $this->rsvp                     = EventAttendee::getStatuses($this->_user->screenName, $this->event);
        $this->canAccessEventDetails    = Events_SecurityHelper::currentUserCanAccessEventDetails($this->event, $this->rsvp);
        $this->showInviteLink           = Events_SecurityHelper::currentUserCanSendInvites($this->event, $this->rsvp);

        // Stop processing if the list of attendees is unavailable
        if ($this->event->my->disableRsvp || !$this->event->my->showGuestList || !$this->canAccessEventDetails) {
            return $this->redirectTo('show','event',array('id'=>$this->event->id));
        }

        $statusParam = Events_RequestHelper::readString($_GET, 'status');
        if ($statusParam === '' || !$this->status = Events_EventHelper::strToRsvp($statusParam)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if ($this->status == EventAttendee::NOT_RSVP) {
            $this->attendees            = Events_InvitationHelper::getInvitations($this->event->id, 20);
        } else {
            $this->attendees            = EventAttendee::getAttendees($this->event, $this->status, 20);
            $this->attendeesProfiles    = XG_Cache::profiles($this->_titles($this->attendees));
        }
        $page = Events_RequestHelper::readPage($_GET);
        if (count($this->attendees) == 0 && $page > 1) {
            // Get here after deleting the last person on the last page [Jon Aquino 2008-03-31]
            return $this->redirectTo(XG_HttpHelper::removeParameter(XG_HttpHelper::currentUrl(), 'page'));
        } elseif (count($this->attendees) == 0) {
            // Get here after deleting the last person [Jon Aquino 2008-03-31]
            return $this->redirectTo('show','event',array('id'=>$this->event->id));
        }
        list($this->counters)           = $this->_fetchEventAttendeesStats();
        $this->title                    = $this->event->title;
    }

    /**
     *  Removes a network member or invitee from the event.
     *
     *  @param    $id           string    Event-ID
     *  @param    $screenName   string    Username of the network member
     *  @param    $invitationId string    XN_Invitation ID of the invitee
     *  @param    $target       string    URL to redirect to
     */
    public function action_deleteAttendee() {
        $eventId = Events_RequestHelper::readEventId($_GET);
        if (!$eventId || !$event = Event::byId($eventId)) { return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404')); }
        if (!Events_SecurityHelper::currentUserCanDeleteAttendees($event)) { throw new Exception('Not allowed (600378279)'); }
        $screenName = Events_RequestHelper::readScreenName($_GET, 'screenName');
        $invitationId = Events_RequestHelper::readInvitationId($_GET);
        if ($invitationId) {
            $invitation = XN_Invitation::load($invitationId);
            $profile = XG_Cache::profiles($invitation->recipient);
            if ($profile) { EventAttendee::delete($profile->screenName, $event); }
            XN_Invitation::delete($invitation);
        }
        if ($screenName !== null) {
            EventAttendee::delete($screenName, $event);
        }
        $target = Events_RequestHelper::readRedirectTarget($_GET, 'target', $this->_buildUrl('event', 'show', array('id' => $event->id)));
        return $this->redirectTo($target ?: $this->_buildUrl('event', 'show', array('id' => $event->id)));
    }

    /**
     *  Outputs the ICS representation of the event
     *
     *  @param    $id       string      Event-ID
     */
    public function action_export() {
        $eventId = Events_RequestHelper::readEventId($_GET);
        if (!$eventId || !$event = Event::byId($eventId)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }

        $this->rsvp = EventAttendee::getStatuses($this->_user->screenName, $this->event);
        $this->canAccessEventDetails = Events_SecurityHelper::currentUserCanAccessEventDetails($this->event, $this->rsvp);
        if (!$this->canAccessEventDetails) {
            return $this->redirectTo('show','event',array('id'=>$this->event->id));
        }
        header('Content-type: text/calendar');
        header('Content-Disposition: attachment; filename="'.preg_replace('/[[:cntrl:][:punct:]]/u','_',$event->title).'.ics"');
        $this->_export($event);
    }


//** CRUD
    /**
     * Displays the form for a new event.
     *
     * @param $setValues	bool	If not empty, form initialized with values from REQUEST instead of the default values
     */
    public function action_new($errors = NULL) {
        XG_App::includeFileOnce('/lib/XG_Form.php');
        XG_SecurityHelper::redirectIfNotMember();

        if (!Events_SecurityHelper::currentUserCanCreateEvent()) {
            return $this->redirectTo('listUpcoming','event');
        }

        unset($_POST['photo']);
        if ($errors) {
            $this->errors   = $errors;
            // If photo was uploaded, remove it and highlight the photo field
            if (!isset($errors['photo'])) {
                $errors['photo'] = '';
            }
            $this->form     = new XG_Form($_POST, $errors);
        } elseif (Events_RequestHelper::readBoolean($_REQUEST, 'setValues')) {
            $this->form     = new XG_Form($_REQUEST);
        } else {
            $this->form     = new XG_Form(array(
                'organizedBy'   => xg_username($this->_user->screenName),
                'hideEnd'       => 1,
                'privacy'       => 'anyone',
            ));
            list(,,$h,$d,$m,$y) = localtime();
            $start = date('Y-m-d H:i',mktime(18,0,0,$m+1,$d+7,$y));
            $this->form->setDate('start', mb_substr($start,0,10));
            $this->form->setTime('start', mb_substr($start,11,5));
        }

        $cancelTarget = Events_RequestHelper::readRedirectTarget($_GET, 'cancelTarget', $this->_buildUrl('event','listUpcoming'));
        $this->cancelTarget = $cancelTarget ?: $this->_buildUrl('event','listUpcoming');
        $this->title        = xg_text('ADD_AN_EVENT');
        $this->render('new');
    }


    // handler for the "quick post" feature
    public function action_createQuick () { # void
        $this->action_create();
        $this->render('blank');
        if ($this->_event) { // _event is set if event was successfully created
            $this->status = 'ok';
            $this->viewUrl = $this->_buildUrl('event', 'show', array('id' => $this->_event->id));
            $this->viewText = xg_html('VIEW_THIS_EVENT');
            $this->message = xg_html('YOUR_EVENT_WAS_ADDED');
            unset($this->_event);
        } else {
            $this->status = 'fail';
            $this->message = xg_html('CANNOT_ADD_YOUR_EVENT');
        }
    }

    /**
     *  Processes the form for a new event
     *
     *  @param  $title
     *  @param  $photo
     *  @param  $description
     *  @param  $type
     *  @param  $location
     *  @param  $start/$end - dates
     *  @param  $hideEnd
     *  @param  $website, ...
     *  @param  $privacy        string      anyone|invited
     *  @param  $cancelTarget
     *  @param  $featureOnMain	bool		feature event
     */
    public function action_create() {
        // Used from action_createQuick()
        XG_SecurityHelper::redirectIfNotMember();
        if (!Events_SecurityHelper::currentUserCanCreateEvent()) {
            return $this->redirectTo('listUpcoming','event');
        }
        $errors = $this->_checkForm();
        if (!$this->_photo && !$errors['photo']) {
            $errors['photo']= xg_html('NO_EVENT_IMAGE');
        }
        if ($errors) {
            return $this->forwardTo('new','event',array($errors));
        }
        $this->_event = $event = Event::create(array(
            'title'         => $_POST['title'],
            'description'   => xg_scrub($_POST['description']),
            'eventType'     => $_POST['type'],
            'startDate'     => $this->_start,
            'endDate'       => $this->_end,
            'hideEndDate'   => $_POST['hideEnd'] ? 1 : 0,
            'location'      => $_POST['location'],
            'street'        => $_POST['street'],
            'city'          => $_POST['city'],
            'photoId'       => $this->_photo->id,
            'photoUrl'      => $this->_photo->fileUrl('data'),
            'website'       => Events_EventHelper::url($_POST['website']),
            'contactInfo'   => $_POST['contact'],
            'organizedBy'   => xg_username($this->_user->screenName) == $_POST['organizedBy'] ? $this->_user->screenName : $_POST['organizedBy'],
            'privacy'       => $_POST['privacy'] == 'anyone' ? Event::ANYONE : Event::INVITED,
            'disableRsvp'	=> $_POST['disableRsvp'] ? 1 : 0,
            'showGuestList' => $_POST['hideGuests'] ? 0 : 1,
            '_feature'		=> (bool)$_POST['featureOnMain'],
        ), TRUE);
        $user = User::load($event->contributorName);
        EventAttendee::setStatus($this->_user->screenName, $event, EventAttendee::ATTENDING);
        $this->redirectTo('new', 'invitation', array('eventId' => $event->id, 'creatingEvent' => 1));
    }


    /**
     *  Displays the form for editing an event.
     *
     *  @param    $id       string      Event-ID
     */
    public function action_edit($errors = NULL, $photo = NULL) {
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        $eventId = Events_RequestHelper::readEventId($_REQUEST);
        if (!$eventId || !$event = Event::byId($eventId)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (!Events_SecurityHelper::currentUserCanEditEvent($event)) {
            return $this->redirectTo('show','event',array('id'=>$event->id));
        }
        XG_App::includeFileOnce('/lib/XG_Form.php');
        if ($errors) {
            $this->errors = $errors;
            // If photo was uploaded, remove it and highlight the photo field
            if (isset($photo) && !isset($errors['photo'])) {
                $errors['photo'] = '';
            }
            $_POST['photo'] = $event->my->photoUrl;
            $this->form = new XG_Form($_POST, $errors);
        } else {
            $this->form = new XG_Form(array(
                'title'         => $event->title,
                'photo'         => $event->my->photoUrl,
                'description'   => $event->description,
                'type'          => XG_TagHelper::implode(Events_EventHelper::typeToList($event->my->eventType)),
                'hideEnd'       => $event->my->hideEndDate,
                'location'      => $event->my->location,
                'street'        => $event->my->street,
                'city'          => $event->my->city,
                'website'       => $event->my->website,
                'contact'       => $event->my->contactInfo,
                'organizedBy'   => $event->my->organizedBy == $event->contributorName ? xg_username($event->contributorName) : $event->my->organizedBy,
                'privacy'       => $event->my->privacy == Event::INVITED ? 'invited' : 'anyone',
                'disableRsvp'	=> $event->my->disableRsvp,
                'hideGuests'    => !$event->my->showGuestList,
                'isClosed'      => $event->my->isClosed,
            ));
            $this->form->setDate('start', mb_substr($event->my->startDate,0,10));
            $this->form->setTime('start', mb_substr($event->my->startDate,11,5));
            $this->form->setDate('end', mb_substr($event->my->endDate,0,10));
            $this->form->setTime('end', mb_substr($event->my->endDate,11,5));
        }
        $this->eventId      = $event->id;
        $cancelTarget = Events_RequestHelper::readRedirectTarget($_GET, 'cancelTarget', $this->_buildUrl('event','show', array('id'=>$event->id)));
        $this->cancelTarget = $cancelTarget ?: $this->_buildUrl('event','show', array('id'=>$event->id));
        $this->title        = xg_text('EDIT_EVENT');
    }

    /**
     *  Processes the form for editing an event. Parameters are the same as for create.
     *
     *  @param    $id       string      Event-ID
     */
    public function action_update() {
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_SecurityHelper::redirectIfNotMember();
        $id = Events_RequestHelper::readEventId($_REQUEST);
        if ($errors = $this->_checkForm()) {
            return $this->forwardTo('edit','event',array($errors,$this->_photo));
        }
        if (!$id || !$event = Event::byId($id)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (!Events_SecurityHelper::currentUserCanEditEvent($event)) {
            return $this->redirectTo('show','event',array('id'=>$id));
        }
        $changes = array(
            'title'         => $_POST['title'],
            'description'   => xg_scrub($_POST['description']),
            'eventType'     => $_POST['type'],
            'startDate'     => $this->_start,
            'endDate'       => $this->_end,
            'hideEndDate'   => $_POST['hideEnd'] ? 1 : 0,
            'location'      => $_POST['location'],
            'street'        => $_POST['street'],
            'city'          => $_POST['city'],
            'website'       => Events_EventHelper::url($_POST['website']),
            'contactInfo'   => $_POST['contact'],
            'organizedBy'   => xg_username($event->contributorName) == $_POST['organizedBy'] ? $event->contributorName : $_POST['organizedBy'],
            'privacy'       => $_POST['privacy'] == 'anyone' ? Event::ANYONE : Event::INVITED,
            'disableRsvp'	=> $_POST['disableRsvp'] ? 1 : 0,
            'showGuestList' => $_POST['hideGuests'] ? 0 : 1,
            'isClosed'      => $_POST['isClosed'] ? 1 : 0,
        );
        if ($this->_photo) {
            $changes['photoId'] = $this->_photo->id;
            $changes['photoUrl'] = $this->_photo->fileUrl('data');
        }
        Event::update($event, $changes, true);
        $this->redirectTo('show','event',array('id'=>$event->id));
    }

    /**
     *  Deletes the event, then redirects to the Events homepage.
     *
     *  @param    $id       string      Event-ID
     */
    public function action_delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $eventId = Events_RequestHelper::readEventId($_REQUEST);
            $event = $eventId ? Event::byId($eventId) : null;
            if ($event && Events_SecurityHelper::currentUserCanDeleteEvent($event)) {
                XG_App::includeFileOnce('/widgets/events/lib/helpers/Events_UserHelper.php');
                $user = User::load($event->contributorName);
                Event::delete($event);
            }
        }
        $this->redirectTo('listUpcoming','event');
    }

    /**
     *  Marks the event as Featured or Unfeatured, then redirects to the event detail page.
     *
     *  @param    $id       string      Event-ID
     *  @param    $set      int         0 - reset, 1 - set. Must be admin to do it.
     */
    public function action_setFeatured() {
        XG_SecurityHelper::redirectIfNotAdmin();
        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
        $eventId = Events_RequestHelper::readEventId($_REQUEST);
        if ($eventId && ($event = Event::byId($eventId))) {
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            if (Events_RequestHelper::readBoolean($_REQUEST, 'set')) {
                XG_PromotionHelper::promote($event);
                XG_PromotionHelper::addActivityLogItem(XG_ActivityHelper::SUBCATEGORY_EVENT, $event);
            } else {
                XG_PromotionHelper::remove($event);
            }
            Event::update($event,array());
        }
        $this->redirectTo('show','event',array('id'=>$event->id));
    }

    /**
     *  Changes the RSVP status for the current user, then redirects to the event detail page.
     *
     *  @param    $id       string      Event-ID
     *  @param    $rsvp     string      attending|might_attend|not_attending  ( ! but not not_rsvped )
     */
    public function action_rsvp() {
        $eventId = Events_RequestHelper::readEventId($_REQUEST);
        if (!$this->_user->isLoggedIn()) {
            if ($eventId) {
                return $this->redirectTo('show','event',array('id'=>$eventId));
            }
            return $this->redirectTo('listUpcoming','event');
        }
        $rsvpValue = Events_RequestHelper::readRsvp($_REQUEST);
        if (!$rsvpValue || (!$status = Events_EventHelper::strToRsvp($rsvpValue)) || $status == EventAttendee::NOT_RSVP) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if (!$eventId || !$event = Event::byId($eventId)) {
            return $this->redirectTo(W_Cache::getWidget('main')->buildUrl('error', '404'));
        }
        if ($event->my->disableRsvp) {
            return $this->forwardTo('show','event',array('id'=>$event->id));
        }
        $curRsvp = EventAttendee::getStatuses($this->_user->screenName, $event);
        if ($curRsvp == EventAttendee::NOT_INVITED) {
            if ($event->my->privacy == Event::INVITED || $event->my->isClosed) {
                return $this->forwardTo('show','event',array('id'=>$event->id));
            }
        }
        EventAttendee::setStatus($this->_user->screenName, $event, $status, TRUE);
        Event::update($event,array()); // update event to fix lastest activity
        $this->redirectTo('show','event',array('id'=>$event->id,'rsvpConfirm'=>1));
    }

    /**
     *  Returns the calendar for the specific period.
     *  To avoid extra queries, returns multiple calendars.
     *
     *  @param      $current    string  YYYY-MM
     *  @param      $show_user  string  returns user calendar instead of widget-wide
     *  @param		$embed		string	return calendar for embed module
     *  @param      $direction  string  forward|backward
     */
    public function action_getCalendar() {
        $user = Events_RequestHelper::readScreenName($_REQUEST, 'show_user');
        if ($user && !Events_SecurityHelper::currentUserCanSeeUserEvents($user)) {
            return;
        }
        $currentMonth = Events_RequestHelper::readMonth($_REQUEST, 'current');
        if ($currentMonth === null) {
            return;
        }
        $current        = XG_DateHelper::strToYm($currentMonth);
        list($min,$max) = $user ? EventAttendee::getMinMaxEventDates($user) : EventWidget::getMinMaxEventDates();
        $min            = XG_DateHelper::strToYm($min);
        $max            = XG_DateHelper::strToYm($max);
        $embed			= Events_RequestHelper::readEmbedFlag($_REQUEST, 'embed');
        $direction = Events_RequestHelper::readDirection($_REQUEST, 'direction');
        if ($direction == 'forward') {
            $start      = $current;
            $end        = min($current+2,$max);
            $this->more = $end < $max ? $end+1 : '';
        } else {
            $start      = max($current-2,$min);
            $end        = $current;
            $this->more = $start > $min ? $start-1 : '';
        }
        if ($this->more) {
            $this->more = XG_DateHelper::ymToStr($this->more);
        }
        $this->data = array();
        $start = XG_DateHelper::ymToStr($start);
        $end = XG_DateHelper::ymToStr($end);
        $calendar = $user ? EventAttendee::getCalendar($user, $start, $end) : EventCalendar::getCalendar($start, $end);
        foreach ($calendar as $month=>$days) {
            ob_start();
            $this->renderPartial('fragment_calendarMonth', '_shared', array( 'month' => $month, 'days' => $days, 'embed' => $embed, 'user' => $user));
            $this->data[] = trim(ob_get_clean());
        }
    }

    /**
     *  Performs an update to the Event location or Type via post operation from the event detail page
     *
     *  @param      $id    string  event id
     *  @param      $field  string  $_GET['location'] or $_GET['type']
     *  @param      $tags   string    the updated location string
     */

    public function action_updateLocationOrType() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $eventId = Events_RequestHelper::readEventId($_REQUEST);
            $event = $eventId ? Event::byId($eventId) : null;
            if ($event && Events_SecurityHelper::currentUserCanEditLocationEventType($event)) {
                W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_TemplateHelper.php');
                $field = Events_RequestHelper::readFieldKey($_GET, 'field');
                if ($field === 'type') {
                    $rawTags = Events_RequestHelper::readOptionalString($_POST, 'tags', false, Event::MAX_EVENT_TYPE_LENGTH + 1);
                    $tags = $rawTags ?? '';
                    if (mb_strlen($tags) > Event::MAX_EVENT_TYPE_LENGTH) { throw new Exception('Event Type name is too long'); }
                    Event::update($event, array('eventType'=>$tags));
                    $this->html = xg_html('EVENT_TYPE_COLON') . ' ' . Events_TemplateHelper::type($event);
                }
            }
        }
    }



    /**
     * Send a message to event attendees.
     *
     *  @param    $message          string  Text of the message
     *  @param    $id               string  Event-ID
     *  @param    $attending        integer Whether to send to people attending the event
     *  @param    $might_attend     integer Whether to send to people who are unsure about whether to attend
     *  @param    $not_attending    integer Whether to send to people who have decided not to attend
     *  @param    $not_rsvped       integer Whether to send to people who have been invited but have not yet RSVPed
     *  @param    $xn_auth          string  "json"
     *  @return   $success          integer 1 if the broadcast completed successfully
     */
    public function action_broadcast() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { throw new Exception('Not a POST (1985046009)'); }
        $eventId = Events_RequestHelper::readEventId($_REQUEST);
        $event = $eventId ? Event::byId($eventId) : null;
        if (! $event || ! Events_SecurityHelper::currentUserCanBroadcastMessage($event)) { throw new Exception('Not allowed (584763022)'); }
        $message = Events_RequestHelper::readMessage($_REQUEST, 'message', 200);
        Events_BroadcastHelper::broadcast(
            $message,
            $event,
            Events_RequestHelper::readBoolean($_REQUEST, 'attending'),
            Events_RequestHelper::readBoolean($_REQUEST, 'might_attend'),
            Events_RequestHelper::readBoolean($_REQUEST, 'not_attending'),
            Events_RequestHelper::readBoolean($_REQUEST, 'not_rsvped')
        );
        $this->success = 1;
    }

    /**
     * An atom feed of the most recently created events.
     */
    public function action_feed() {
        $this->setCaching(array('event-event-feed-' . md5(XG_HttpHelper::currentUrl())), 1800);
        if (Events_RequestHelper::readBoolean($_GET, 'test_caching')) { var_dump('Not cached'); }
        $this->events = Event::getEventsForFeed(W_Cache::getWidget('events')->config['maxEventsInFeed']);
        $this->title = xg_text('LATEST_EVENTS');
        header('Content-Type: application/atom+xml');
    }


//** Implementation
	// Checks whether user is the same as logged in user.
	protected function _isMe ($user) { # bool
		return strtolower($user) === strtolower(XN_Profile::current()->screenName); /** @non-mb */
    }

    /**
     *  Validates the form.
     *
     *  @return  hash    Mapping of field name to error message.
     */
    protected function _checkForm() {
        XG_App::includeFileOnce('/lib/XG_Form.php');
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');

        $errors = array();

        // Length checks are handled by the maxlength attribute [Jon Aquino 2008-04-02]
        if (mb_strlen($_POST['title']) > Event::MAX_TITLE_LENGTH) { throw new Exception('Title is too long'); }
        if (mb_strlen($_POST['type']) > Event::MAX_EVENT_TYPE_LENGTH) { throw new Exception('Event Type is too long - ' . mb_strlen($_POST['type'])); }
        if (mb_strlen($_POST['location']) > Event::MAX_LOCATION_LENGTH) { throw new Exception('Location name is too long'); }
        if (mb_strlen($_POST['street']) > Event::MAX_STREET_LENGTH) { throw new Exception('Street name is too long'); }
        if (mb_strlen($_POST['city']) > Event::MAX_CITY_LENGTH) { throw new Exception('City name is too long'); }
        if (mb_strlen($_POST['website']) > Event::MAX_WEBSITE_LENGTH) { throw new Exception('Website URL is too long'); }
        if (mb_strlen($_POST['contact']) > Event::MAX_CONTACT_INFO_LENGTH) { throw new Exception('Contact Info is too long'); }
        if (mb_strlen($_POST['organizedBy']) > Event::MAX_ORGANIZED_BY_LENGTH) { throw new Exception('Organized By name is too long'); }
        if (mb_strlen($_POST['description']) > Event::MAX_DESCRIPTION_LENGTH) { $errors['description'] = xg_html('DESCRIPTION_MUST_BE_SHORTER', Event::MAX_DESCRIPTION_LENGTH); }

        if (! $_POST['title'])          { $errors['title']          = xg_html('NO_EVENT_TITLE'); }
        if (! $_POST['description'])    { $errors['description']    = xg_html('NO_EVENT_DESCRIPTION'); }
        if (! $_POST['type'])           { $errors['type']           = xg_html('NO_EVENT_TYPE'); }
        if (! $_POST['location'])       { $errors['location']       = xg_html('NO_EVENT_LOCATION'); }

        if (!$this->_start = XG_Form::parseDate('start')) {
            $errors['start'] = xg_html('WRONG_EVENT_START');
        } else {
            $this->_start .= ' '.XG_Form::parseTime('start');
        }
        if ($_POST['hideEnd']) {
            $this->_end = '';
        } else {
            if (!$this->_end = XG_Form::parseDate('end')) {
                $errors['end'] = xg_html('WRONG_EVENT_END');
            } else {
                $this->_end .= ' '.XG_Form::parseTime('end');
            }
            $diff = xg_date('U',$this->_end) - xg_date('U',$this->_start);
            if ($diff < 0) {
                $errors['end'] = xg_html('WRONG_EVENT_END2');
            } elseif ($diff > 14*86400) {
                $errors['end'] = xg_html('WRONG_EVENT_END3',14);
            }
        }

        switch ($_POST['photo_action']) {
            case 'keep':
                $this->_photo = NULL;
                break;
            case 'add':
            default:
                if (!$_POST['photo']) {
                    $errors['photo']= xg_html('NO_EVENT_IMAGE');
                } elseif (!XG_FileHelper::isValidImageType('photo')) {
                    $errors['photo'] = xg_html('EVENT_IMAGE_IS_NOT_IMAGE');
                } else {
                    // TODO: Do this outside of _checkForm, which should focus on validation only [Jon Aquino 2008-04-10]
                    list($this->_photo) = XG_FileHelper::createUploadedFileObject('photo');
                }
                break;
        }
        return $errors;
    }

    /**
     *  Returns a pre-rendered HTML template for the Event Details page, and event attendees counters
     *
     *  @return array                       Counts keyed by status, and an HTML template
     */
    protected function _fetchEventAttendeesStats() { # list<counters,html>
        $key = "Event-".$this->event->id."-action_show-".'$Revision: 9558 $';
        if (XG_Cache::cacheOrderN() && $data = XN_Cache::get($key)) {
            return unserialize($data);
        }
        $attendeesAttending     = EventAttendee::getAttendeesProper($this->event, EventAttendee::ATTENDING, 15, false, true);
        $attendeesMight         = EventAttendee::getAttendeesProper($this->event, EventAttendee::MIGHT_ATTEND, 15, false, true);
        $attendeesNotAttending  = EventAttendee::getAttendeesProper($this->event, EventAttendee::NOT_ATTENDING, 9, false, true);
        $invitations            = Events_InvitationHelper::getInvitationsProper($this->event->id, 5, true);
        // Preload the XN_Profiles and User objects [Jon Aquino 2008-04-04]
        $this->attendeesProfiles = XG_Cache::profiles(EventAttendee::screenNames($attendeesAttending->getList()), EventAttendee::screenNames($attendeesMight->getList()), EventAttendee::screenNames($attendeesNotAttending->getList()), Index_InvitationHelper::recipients($invitations->getList()));
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $attendeesAttending->setResult(User::loadMultiple(EventAttendee::screenNames($attendeesAttending->getList())), $attendeesAttending->totalCount);
        $attendeesMight->setResult(User::loadMultiple(EventAttendee::screenNames($attendeesMight->getList())), $attendeesMight->totalCount);
        $attendeesNotAttending->setResult(User::loadMultiple(EventAttendee::screenNames($attendeesNotAttending->getList())), $attendeesNotAttending->totalCount);
        $invitations->setResult(Index_InvitationHelper::metadataForInvitations($invitations->getList()), $invitations->totalCount);
        $counters               = array(
            EventAttendee::ATTENDING        => $attendeesAttending->totalCount,
            EventAttendee::MIGHT_ATTEND     => $attendeesMight->totalCount,
            EventAttendee::NOT_ATTENDING    => $attendeesNotAttending->totalCount,
            EventAttendee::NOT_RSVP         => $invitations->totalCount,
        );
        ob_start();
        $this->renderPartial('fragment_attendeesGrid', '_shared', array( 'list' => $attendeesAttending,     'title' => xg_text('ATTENDING'),    'status' => EventAttendee::ATTENDING, ));
        $this->renderPartial('fragment_attendeesGrid', '_shared', array( 'list' => $attendeesMight,         'title' => xg_text('MIGHT_ATTEND'), 'status' => EventAttendee::MIGHT_ATTEND, ));
        $this->renderPartial('fragment_attendeesGrid', '_shared', array( 'list' => $attendeesNotAttending,  'title' => xg_text('NOT_ATTENDING'),'status' => EventAttendee::NOT_ATTENDING, ));
        $this->renderPartial('fragment_attendeesGrid', '_shared', array( 'list' => $invitations,            'title' => xg_text('NOT_YET_RSVP'), 'status' => EventAttendee::NOT_RSVP, 'view' => 'list'));
        $html = ob_get_clean();
        if (XG_Cache::cacheOrderN()) {
            XN_Cache::put($key, serialize(array($counters,$html)), Event::cacheLabel($this->event->id));
        }
        return array($counters,$html);
    }

    /**
     *  Load RSVP for the specified events
     *
     *	@param	$user	string		User. NULL for current user
     *  @return     void
     */
    protected function _loadRsvp($user /*, ...*/) {
        $this->rsvp = array();
        if ($user || $this->_user->isLoggedIn()) {
            $args = func_get_args();
            $args[0] = $this->_user->screenName;
            /*if (!$user) { -- requires listItem redesign, because of "You will ...";
                $args[0] = $this->_user->screenName;
            };*/
            $this->rsvp = call_user_func_array('EventAttendee::getStatuses', $args);
        }
        return;
    }

    /**
     *  Sets up the data for fragment_sideBlock template. If mode == 'my', user must be logged in.
     *
     *  @param  $mode	home|my|user 	Display mode (affects side block conent)
     *  @param	$user	string
     */
    protected function _prepareSideBlock($mode, $user = NULL, $noCalendar = FALSE) { # void
        $this->wrapDate = 1;
        $this->displayMode = $mode;
        switch ($mode) {
            case 'home':
                $this->screenName = NULL;
                $this->eventTypes = EventWidget::getEventTypes();
                $this->myUpcoming = $this->_user->isLoggedIn() && $this->eventTypes ? EventAttendee::getUpcomingEvents($this->_user->screenName, 3, true) : new XG_PagingList();
                if (!$noCalendar) {
                    $this->calendar = EventCalendar::getDefaultCalendar();
                }
                break;
            case 'my':
                $this->noSearch = 1;
                $this->screenName = XN_Profile::current()->screenName;
                $this->eventTypes = EventAttendee::getEventTypes($this->_user->screenName);
                $this->notAttending = EventAttendee::getNotAttendingEvents($this->_user->screenName, 3, true);
                if (!$noCalendar) {
                    $this->calendar = EventAttendee::getDefaultCalendar($this->_user->screenName);
                }
                break;
            case 'user':
                $this->noSearch = 1;
                // access check
                $this->screenName = $user;
                $this->eventTypes = EventAttendee::getEventTypes($this->screenName);
                $this->notAttending = EventAttendee::getNotAttendingEvents($this->screenName, 3, true);
                if (!$noCalendar) {
                    $this->calendar = EventAttendee::getDefaultCalendar($this->screenName);
                }
                break;
            default:
                throw new Exception("Assertion failed (158836576)");
        }
    }

    /**
     *  Returns the screen names for the given User objects.
     *
     *  @param  $users  array   W_Content objects for the users
     *  @return array           The corresponding usernames
     */
    protected function _titles($users) { # void
        $res = array();
        foreach ($users as $u) {
            $res[] = $u->title;
        }
        return $res;
    }

    /**
     *  Outputs the ICS representation of the event
     *
     *  @param      $event  W_Content   The Event
     */
    protected function _export($event) { # void
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_ExportHelper.php');
        $p = new Events_ExportHelper;

        $s = Events_EventHelper::dateToTs($event->my->startDate);
        $e = Events_EventHelper::dateToTs($event->my->endDate);
        if ($e == $s) { $e = $s + 3600; } // Otherwise will not display properly in Outlook [Jon Aquino 2008-04-08]

        $attendeesAttending     = EventAttendee::getAttendees($event, EventAttendee::ATTENDING, 50, true);
        $attendeesMight         = EventAttendee::getAttendees($event, EventAttendee::MIGHT_ATTEND, 50, true);
        XG_Cache::profiles($this->_titles($attendeesAttending), $this->_titles($attendeesMight));

        $p->param('BEGIN','VCALENDAR');
            $p->param('PRODID','NingEventWidget-v1');
            $p->param('VERSION','2.0');
            $p->param('METHOD','PUBLISH');//REQUEST
            $p->param('BEGIN','VEVENT');
                $p->param('UID', $event->id);
                $p->datetime('DTSTAMP',xg_date('U'));
                $p->param('SUMMARY', $event->title);
                $p->param('DESCRIPTION', $event->description . "\n\n" . xg_text('FOR_MORE_VISIT_URL',$this->_buildUrl('event','show',array('id'=>$event->id))));
                $p->datetime('DTSTART',$s);
                $p->datetime('DTEND',$e);
                $p->multi('CATEGORIES', Events_EventHelper::typeToList($event->my->eventType));
                $p->param('LOCATION',$event->my->location);
                $p->param('WEBSITE',$event->my->website);
                $p->param('URL',$event->my->website);
                $p->param('CONTACT',$event->my->contactInfo);
                if ($event->contributorName == $event->my->organizedBy) {
                    $p->profile('ORGANIZED',xg_username($event->contributorName));
                } else {
                    $p->param('ORGANIZED',$event->my->organizedBy);
                }

                $p->param('ATTACH',$event->my->photoUrl,array('FMTTYPE'=>'image/jpeg'));
                //!!CLASS:PRIVATE

                $attrs = array('ROLE'=>'REQ-PARTICIPANT','PARTSTAT'=>'ACCEPTED','RSVP'=>'TRUE');
                foreach ($attendeesAttending as $u) {
                    $p->profile('ATTENDEE',$u->title,$attrs);#!! - mail or mailto:http://
                }
                $attrs = array('ROLE'=>'REQ-PARTICIPANT','PARTSTAT'=>'TENTATIVE','RSVP'=>'TRUE');
                foreach ($attendeesMight as $u) {
                    $p->profile('ATTENDEE',$u->title,$attrs);
                }
            $p->param('END','VEVENT');
        $p->param('END','VCALENDAR');
    }
}
?>
