<?php
/*  $Id: $
 *
 *  Displays event
 *
 *  Parameters:
 *      $this->event            Event
 *      $this->prevEvent        Event       prev/next event
 *      $this->nextEvent        Event
 *      $this->attendeesHtml    string      Block with pre-rendered attendees html
 *      $this->rsvp             int         Current user RSVP
 *      $this->inviter          string      Username of the last person (if any) who invited the current user
 *      $this->isAdmin          bool
 *      $this->isMyEvent        bool
 *      $this->canAccessEventDetails bool   Whether the current user is allowed to view details about the event.
 *      $this->rsvpMsg          string      Display message at the "top"
 *      $this->showInviteLink   bool        Whether to display the Invite More People link
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title, NULL, array(
	'metaDescription' => $this->event->description,
	'metaKeywords' => join(', ',Events_EventHelper::typeToList($this->event->my->eventType)),
)); ?>
<div id="xg_body">
    <?php if (mb_strlen($this->rsvpMsg)) { $this->renderPartial('fragment_eventRsvpMsg', '_shared', array('event'=>$this->event, 'rsvpMsg' => $this->rsvpMsg, 'inviter' => $this->inviter)); } ?>
    <div class="xg_column xg_span-16">
        <?php
	    $this->renderPartial('fragment_navigation','_shared');
	    $contributor = XG_Cache::profiles($this->event->contributorName);
	?>
        <%= xg_headline($this->title, array(
				'avatarUser' => $contributor,
				'byline1Html' => xg_html('ADDED_BY_X', xg_userlink($contributor)),
				'byline2Html' => xg_message_and_friend_links($this->event->contributorName, $this->_buildUrl('event', 'listUserEvents', array('user' => $contributor->screenName)), xg_text('VIEW_EVENTS')))) %>
        <div class="xg_column xg_span-12">
            <?php $this->renderPartial('fragment_eventInfo', '_shared', array('event'=>$this->event, 'compact'=>false, 'canAccessEventDetails'=>$this->canAccessEventDetails, 'showInviteLink'=>$this->showInviteLink, 'nextEvent' => $this->nextEvent, 'prevEvent' => $this->prevEvent))?>
            <?php if ($this->canAccessEventDetails) { $this->renderPartial('fragment_eventComments', '_shared', array('event'=>$this->event, 'status'=>$this->rsvp)); } ?>
        </div>
        <div class="xg_column xg_span-4 xg_last">
            <?php if ($this->isAdmin || $this->isMyEvent) { $this->renderPartial('fragment_eventAdmin', '_shared', array('event' => $this->event, 'isMyEvent' => $this->isMyEvent, 'isAdmin' => $this->isAdmin)); } ?>
            <?php if (!$this->event->my->disableRsvp && $this->_user->isLoggedIn()) {
                if ($this->isMyEvent || ($this->event->my->privacy == Event::INVITED ? $this->canAccessEventDetails : !$this->event->my->isClosed)) {
                    $this->renderPartial('fragment_eventStatus', '_shared', array('event'=>$this->event, 'rsvp' => $this->rsvp));
                }
            }
            if ($this->canAccessEventDetails) {
                echo $this->attendeesHtml;
            } ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
