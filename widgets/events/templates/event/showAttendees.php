<?php
/*  $Id: $
 *
 *  Displays event attendees
 *
 *  Parameters:
 *      $this->attendees        XG_PagingList<Event>
 *      $this->event            Event
 *      $this->status           int
 *      $this->counters         hash<rsvp:count>
 *
 */
$statuses = array(
    EventAttendee::NOT_RSVP         => xg_html('NOT_YET_RSVP'),
    EventAttendee::ATTENDING        => xg_html('ATTENDING'),
    EventAttendee::MIGHT_ATTEND     => xg_html('MIGHT_ATTEND'),
    EventAttendee::NOT_ATTENDING    => xg_html('NOT_ATTENDING'),
);
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation','_shared') ?>
		<%=xg_headline($this->title)%>
        <div class="xg_column xg_span-16 xg_last">
            <div class="xg_column xg_span-12 first-child">
				<?php $this->renderPartial('fragment_eventInfo','_shared', array('event'=>$this->event, 'compact'=>true, 'showInviteLink'=>$this->showInviteLink, 'showBackLink' => true)) ?>
            </div>
            <div class="xg_column xg_span-4 last-child">
                <?php $this->renderPartial('fragment_guestLists','_shared', array(
                    'status'    => $this->status,
                    'event'     => $this->event,
                    'counters'  => $this->counters,
                    'statuses'  => $statuses)) ?>
            </div>
        </div>
        <div class="xg_column xg_span-16 xg_last">
            <?php
                $this->renderPartial('fragment_attendeesGridExt','_shared', array(
                    'list'      => $this->attendees,
                    'status'    => $this->status,
                    'counters'  => $this->counters,
                    'statuses'  => $statuses,
                    'event'     => $this->event,
                    'view'      => $this->status == EventAttendee::NOT_RSVP ? 'list' : 'grid',)) ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
