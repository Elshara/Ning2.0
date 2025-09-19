<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  DESCRIPTION:
 *
 *      Useful functions for the Event templates.
 *
 **/

class Events_TemplateHelper {
    /**
     *  Returns the event start date in a human-readable format.
     *
     *  @param      $event  W_Content   The Event
     *  @param      $noUrl  boolean     Whether to return plain text instead of an <a>
     *  @return     string              The start date, in plain text or as an HTML link
     */
    public static function startDate($event, $noUrl = false) {
        $startDate = mb_substr($event->my->startDate,0,10);
        $startUrl = 'href="'.xnhtmlentities(W_Cache::getWidget('events')->buildUrl('event','listByDate','?date='.$startDate)).'"';
        $start = strtotime($event->my->startDate);
        $dateFmt = xg_text('EVENT_DATE_FMT');
        $timeFmt = xg_text('EVENT_TIME_FMT');

        if ($event->my->hideEndDate) {
            // [DATE] time
            $res = xg_html('EVENT_START', $startUrl, date($dateFmt,$start), self::_smartTime($timeFmt,$start));
        } else {
            $endDate = mb_substr($event->my->endDate,0,10);
            $end = strtotime($event->my->endDate);

            if ($startDate == $endDate) {
                $res = xg_html('EVENT_START_FROM_TO_TIME', $startUrl, date($dateFmt,$start), self::_smartTime($timeFmt,$start), self::_smartTime($timeFmt,$end));
            } else {
                $endUrl = 'href="'.xnhtmlentities(W_Cache::getWidget('events')->buildUrl('event','listByDate','?date='.$endDate)).'"';
                $res = xg_html('EVENT_START_FROM_TO_DATE', $startUrl, date($dateFmt,$start), self::_smartTime($timeFmt,$start), $endUrl, date($dateFmt,$end), self::_smartTime($timeFmt,$end));
            }
        }
        return $noUrl ? strip_tags($res) : $res;
    }
	// The same as date(fmt,time), but strips :00 to make a time looks better.
    protected static function _smartTime($fmt, $time) { # void
		return str_replace(':00','',date($fmt,$time));
    }


    /**
     *  Returns HTML for the location of the event.
     *
     *  @param      $event  W_Content   The Event
	 *  @param		$noUrl	bool		Return w/o URL
	 *  @param		$encode	bool		(only of $noUrl is enabled) do not encode html entities
     *  @return     string              The location link
     */
    public static function location($event, $noUrl = false, $encode = true) { # string
		if ($noUrl) {
			return $encode ? xnhtmlentities($event->my->location) : $event->my->location;
		}
        $url    = W_Cache::getWidget('events')->buildUrl('event','listByLocation',array('location'=>$event->my->location));
        return "<a href=\"$url\">".xnhtmlentities($event->my->location)."</a>";
    }

    /**
     *  Returns an <li> for an attendee list.
     *
     *  @param      $item               W_Content|XN_Content|hash   A User, or hash of invitation properties
     *  @param      $event              W_Content                   The Event
     *  @param      $showUninviteLink   string                      Whether to show an <a> for removing the person from the event
     *  @return     string                                          The <li> HTML
     *  @see Index_InvitationHelper::metadataForInvitations
     */
    public static function attendeeListItem($item, $event, $showUninviteLink = false) {
        $isInvitation = is_array($item);
        if ($showUninviteLink) {
            $uninviteLink = $isInvitation ? self::uninviteLinkForInvitee($item['id'], $event) : self::uninviteLinkForMember($item->title, $event);
        }
        if ($isInvitation && $item['screenName'] && $user = User::load($item['screenName'])) {
            $item = $user;
        }
        if ($item instanceof XN_Content || $item instanceof W_Content) {
            return '<li><a href="'.xnhtmlentities(User::quickProfileUrl($item->title)).'">'.xnhtmlentities(xg_username($item->title)).'</a> ' . $uninviteLink . '</li>';
        } else {
            return '<li>' . xnhtmlentities($item['displayName']) . ' ' . $uninviteLink . '</li>';
        }
    }

    /**
     *  Returns HTML for the host of the event.
     *
     *  @param      $event  W_Content   The Event
	 *  @param		$noUrl	bool		Return w/o URL
	 *  @param		$encode	bool		(only of $noUrl is enabled) do not encode html entities
     *  @return     string              The name of the host, linked if possible
     */
	public static function organizedBy($event, $noUrl = false, $encode = true) { # string
		$text = $event->contributorName == $event->my->organizedBy ? xg_username($event->contributorName) : $event->my->organizedBy;
		if ($noUrl) {
			return $encode ? xnhtmlentities($text) : $text;
		}
		return ($event->contributorName == $event->my->organizedBy)
            ? '<a href="'.xnhtmlentities(User::quickProfileUrl($event->my->organizedBy)).'">'.xnhtmlentities($text).'</a>'
            : xnhtmlentities($text);
    }

    /**
     *  Returns HTML for the event types.
     *
     *  @param      $event  W_Content   The Event
     *  @return     string              Comma-delimited list of event-type links
     */
    public static function type($event) { # string
        // TODO: Rename to types() [Jon Aquino 2008-03-21]
        $urlPrefix  = W_Cache::getWidget('events')->buildUrl('event','listByType',array('type'=>''));
        $res        = array();
        foreach (Events_EventHelper::typeToList($event->my->eventType) as $t) {
            $res[] = '<a href="'.$urlPrefix.urlencode($t).'">'.xnhtmlentities($t).'</a>';
        }
        return join(', ',$res);
    }

    /**
     *  Returns the URL for the event image.
     *
     *  @param      $event  W_Content   The Event
     *  @param      $size   integer     Width and height in pixels
     *  @return     string              The URL of the image, which will be square-cropped
     */
    public static function photoUrl($event, $size) { # string
        return $event->my->photoUrl."?size=$size&crop=1:1";
    }

    /**
     *  Returns human-readable text for RSVP status
     *
     *  @param      $event      W_Content               The Event
     *  @param      $status     hash<event-id:status>   Mapping of event IDs to status codes
     *  @return     string                              e.g., "You Are Attending"
     */
    public static function rsvp($event, array $status) {
        switch ($status[$event->id]) {
            case EventAttendee::NOT_INVITED:
                if ($event->my->privacy == Event::INVITED) {
                    return xg_html('YOU_ARE_NOT_INVITED');
                }

                return $event->my->disableRsvp ? null : xg_html('YOU_HAVE_NOT_RSVPED');
            case EventAttendee::NOT_RSVP:       return xg_html('YOU_HAVE_NOT_RSVPED');
            case EventAttendee::ATTENDING:      return xg_html('YOU_ARE_ATTENDING');
            case EventAttendee::MIGHT_ATTEND:   return xg_html('YOU_MIGHT_ATTEND');
            case EventAttendee::NOT_ATTENDING:  return xg_html('YOU_WILL_NOT_ATTEND');
            default:
                return xg_html('YOU_WILL_NOT_ATTEND');
        }
    }

    /**
     *  Returns a link for removing a network member from an event.
     *
     *  @param      $screenName string          User screenName
     *  @param      $event      W_Content       The Event
     */
    public static function uninviteLinkForMember($screenName, $event) {
        return self::uninviteLink($screenName, null, $event);
    }

    /**
     *  Returns a link for removing an invittee from an event.
     *
     *  @param      $invitationId   string          XN_Invitation ID of the invitee
     *  @param      $event          W_Content       The Event
     */
    public static function uninviteLinkForInvitee($invitationId, $event) {
        return self::uninviteLink(null, $invitationId, $event);
    }

    /**
     *  Returns a link for removing a network member or invitee from an event.
     *
     *  @param  $screenName     string          Username of the network member
     *  @param  $invitationId   string          XN_Invitation ID of the invitee
     *  @param  $event          W_Content       The Event
     */
    private static function uninviteLink($screenName, $invitationId, $event) {
        XG_App::ningLoaderRequire('xg.shared.PostLink');
        return '<a href="#" dojoType="PostLink" class="desc delete" title="' . xg_html('UNINVITE') . '"
            _url="' . xnhtmlentities(W_Cache::getWidget('events')->buildUrl('event', 'deleteAttendee', array('id' => $event->id, 'screenName' => $screenName, 'invitationId' => $invitationId, 'target' => XG_HttpHelper::currentUrl()))) . '"
            _confirmTitle="' . xg_html('UNINVITE_USER', xnhtmlentities(xg_username(XG_Cache::profiles($screenName)))) . '"
            _confirmQuestion="' . xg_html('SURE_UNINVITE_PERSON') . '"
            _confirmOkButtonText="' . xg_html('UNINVITE') . '"
            >' . ($invitationId ? '' : xg_html('UNINVITE')) . '</a>';
    }
}
?>
