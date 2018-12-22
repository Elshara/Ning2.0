<?php

/**
 * Utility functions for working with event invitations
 */
class Events_InvitationHelper {

    /**
     *  See XG_MessageHelper::getDefaultMessageParts() for details.
     *
     *  @param      $event	object	Event
     *  @return     hash
     */
    public function getMessageParts($event) {
        $messageParts = XG_MessageHelper::getDefaultMessageParts();
        if (XN_Profile::current()->screenName == $event->contributorName) {
            $messageParts[xg_html('EVENT_TITLE')] = $event->title;
            $messageParts[xg_html('EVENT_DESCRIPTION')] = $event->description;
            $messageParts[xg_html('EVENT_LOCATION')] = $event->my->location;
        }
        return $messageParts;
    }

    /**
     * Returns the properties of the invitations for the event.
     *
     *  @param      $eventId    string      Content ID of the Event
     *  @param      $limit      int         Number of invitations to return
     *  @param      $noPage     bool        Disable the page autodetection from _GET.
     *  @return     XG_PagingList<hash>     Metadata for each invitation
     *  @see Index_InvitationHelper::metadataForInvitations
     */
    public static function getInvitations($eventId, $limit, $noPage = false) {
        $list = self::getInvitationsProper($eventId, $limit, $noPage);
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
        $list->setResult(Index_InvitationHelper::metadataForInvitations($list->getList()), $list->totalCount);
        return $list;
    }

    /**
     * Returns the invitations for the event.
     *
     *  @param      $eventId    string      Content ID of the Event
     *  @param      $limit      int         Number of invitations to return
     *  @param      $noPage     bool        Disable the page autodetection from _GET.
     *  @return     XG_PagingList<XN_Invitation> Metadata for each invitation
     */
    public static function getInvitationsProper($eventId, $limit, $noPage = false) {
        $query = XN_Query::create('Invitation')
                ->filter('label', '=', self::eventInvitationLabel($eventId))
                ->order('createdDate', 'desc');
        $list = new XG_PagingList($limit, $noPage ? '' : NULL);
        $list->processQuery($query);
        $list->setResult($query->execute(), $query->getTotalCount());
        return $list;
    }

    /**
     * Returns the XN_Invitation label for the given Event.
     *
     * @param string $eventId  the content ID for the Event object
     * @return string  the label for invitations for the Event
     */
    public static function eventInvitationLabel($eventId) {
        if (! is_string($eventId)) { xg_echo_and_throw('Assertion failed (493244082)', true); }
        return 'event-invitation-' . $eventId;
    }

    /**
     * Returns the Event associated with the given invitation.
     *
     * @param $label  the invitation label
     * @return XN_Content|W_Content  the associated Event, or null if there is none
     */
    private static function event($label) {
        $eventId = self::eventId($label);
        return $eventId ? Event::byId($eventId) : null;
    }

    /**
     * Extracts the event ID from the label.
     *
     * @param $label  the invitation label
     * @return  the ID of the associated Event, or null if there is none
     */
    protected static function eventId($label) {
        return preg_match('@event-[^-]+-(.*)@u', $label, $matches) ? $matches[1] : null;
    }

    /**
     * Called when an invitation is consumed.
     *
     * @param $invitation  the invitation to consume
     */
    public static function onConsume($invitation) {
        $event = self::event($invitation->label);
        if ($event && ! EventAttendee::rsvped(EventAttendee::getStatuses(XN_Profile::current()->screenName, $event))) {
            EventAttendee::setStatus(XN_Profile::current()->screenName, $event, EventAttendee::NOT_RSVP, false, $invitation->inviter);
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationHelper.php');
            // Since the original XN_Invitation has been deleted (because consumed), create a new XN_Invitation
            // (without sending an email) so that the person continues to appear on the Not Yet RSVPed list
            // (which is a list of XN_Invitations for the event) (BAZ-7246) [Jon Aquino 2008-04-11]
            Index_InvitationHelper::createInvitation($invitation->recipient, $invitation->name, $invitation->label);
        }
    }

    /**
     * Called when an invitation is being re-sent.
     *
     * @param $invitation  the invitation to consume
     */
    public static function onResend($invitation) {
        $event = self::event($invitation->label);
        if ($event) {
            $message = new XG_Message_Event_Invitation(array(
                    'subject' => xg_text('COME_JOIN_ME_AT_EVENT', $event->title, XN_Application::load()->name),
                    'url' => W_Cache::getWidget('events')->buildUrl('event', 'show', array('id' => $event->id, Index_InvitationHelper::KEY => $invitation->id))));
            $message->send($invitation->recipient, $invitation->inviter, $event);
        }
    }

}

XN_Event::listen('invitation/consume/after', array('Events_InvitationHelper', 'onConsume'));
XN_Event::listen('invitation/resend', array('Events_InvitationHelper', 'onResend'));
