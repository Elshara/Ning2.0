<?php
/*  $Id: $
 *
 *  Renders side block for event listings
 *
 *  Parameters:
 *	@param	$this->displayMode		Displays mode:
 *	@param	$noEventTypes
 *	home							all events
 *		@param	$this->eventTypes		all events types
 *		@param	$this->myUpcoming		my upcoming events
 *		@param	$this->calendar			widget calendar
 *	my								my events
 *	        @param  $this->screenName               current user
 *		@param	$this->eventTypes		my events types
 *		@param	$this->notAttending		my not attending events
 *		@param	$this->calendar			my calendar
 *	user							user's events
 *		@param	$this->screenName		user
 *		@param	$this->eventTypes               user events types
 *		@param	$this->notAttending		user not attending events
 *		@param	$this->calendar			user calendar
 *
 */
if (!isset($noEventTypes)) {
    $noEventTypes = 0;
}
switch ($this->displayMode) {
    case 'home':
        if (count($this->myUpcoming)) {
            $this->renderPartial('fragment_userEvents', '_shared', array(
                'list' => $this->myUpcoming,
                'title' => xg_text('MY_UPCOMING_EVENTS'),
                'viewAllUrl'=> $this->_buildUrl('event','listUserEvents', array('user' => $this->_user->screenName)),
            ));
        }
        if (!$noEventTypes && $this->eventTypes) {
            $this->renderPartial('fragment_eventTypes','_shared', array(
                'types' => $this->eventTypes,
                'title' => xg_text('POPULAR_EVENT_TYPES'),
                'urlPrefix'	=> $this->_buildUrl('event','listByType','?type='),
                'viewAllUrl' => $this->_buildUrl('event','listAllTypes'),
            ));
        }
        $this->renderPartial('fragment_calendar','_shared', array('calendar'=>$this->calendar) );
        break;
    case 'my':
    case 'user':
        if (!$noEventTypes && $this->eventTypes) {
            $this->renderPartial('fragment_eventTypes','_shared', array(
                'types'		=> $this->eventTypes,
                'title'		=> $this->displayMode === 'my' ? xg_text('MY_EVENT_TYPES') : xg_text('USER_EVENT_TYPES', xg_username($this->screenName)),
                'urlPrefix'	=> $this->_buildUrl('event','listUserEventsByType','?user='.urlencode($this->screenName).'&type='),
                'viewAllUrl' => $this->_buildUrl('event','listUserAllTypes','?user='.urlencode($this->screenName)),
            ));
        }
        if (count($this->notAttending)) {
            $this->renderPartial('fragment_userEvents', '_shared', array(
                'list'		=> $this->notAttending,
                'title'		=> $this->displayMode === 'my' ? xg_text('EVENTS_I_AM_NOT_ATTENDING') : xg_text('EVENTS_USER_NOT_ATTENDING', xg_username($this->screenName)),
                'viewAllUrl'=> $this->_buildUrl('event','listUserNotAttendingEvents',array('user'=>$this->screenName)),
            ));
        }
        $this->renderPartial('fragment_calendar','_shared', array('calendar'=>$this->calendar, 'showUser' => $this->screenName) );
        break;
}
?>
