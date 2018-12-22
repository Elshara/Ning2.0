<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *      $event      Event
 *      $rsvpMsg    string
 *      $inviter    string      Username of the last person (if any) who invited the current user
 *
 */
$titleHtml = xnhtmlentities($event->title);
$hdr    = xg_html('WELCOME_TO_EVENT_X',$titleHtml);
$class  = 'success';
if ($rsvpMsg == 'event_is_full') {
        $class  = 'errordesc';
        $hdr    = xg_html('WERE_SORRY');
        $msg    = xg_html('EVENT_IS_FULL');
} elseif ($rsvpMsg == EventAttendee::NOT_INVITED && $event->my->privacy == Event::INVITED) {
        $class  = 'errordesc';
        $hdr    = xg_html('WERE_SORRY');
        $url    = '<a href="'.xnhtmlentities(User::quickProfileUrl($event->contributorName)).'">'.xnhtmlentities(xg_username($event->contributorName)).'</a>';
        $msg    = xg_html('USER_MADE_EVENT_PRIVATE', $url, $url);
} elseif ($rsvpMsg == EventAttendee::NOT_RSVP) {
        $class  = 'notification';
        $hdr    = null;
        ob_start();
        xg_avatar(XG_Cache::profiles($inviter), 48, 'left photo', 'style="margin-right:10px"');
        $avatar = trim(ob_get_contents());
        ob_end_clean();
        $msg    = '<strong>' . $avatar . xg_html('USER_HAS_INVITED_YOU_TO_EVENT', xg_userlink(XG_Cache::profiles($inviter)), $titleHtml) . '</strong>';
} elseif ($rsvpMsg == EventAttendee::ATTENDING) {
        $msg    = xg_html('YOU_ARE_ATTENDING_EVENT',$titleHtml);
} elseif ($rsvpMsg == EventAttendee::MIGHT_ATTEND) {
        $msg    = xg_html('YOU_MIGHT_ATTEND_EVENT',$titleHtml);
} elseif ($rsvpMsg == EventAttendee::NOT_ATTENDING) {
        $class  = 'errordesc';
        $msg    = xg_html('YOU_WONT_ATTEND_EVENT',$titleHtml);
}
if (!$msg) { return; }?>
<div class="xg_module">
    <div class="xg_module_body <%=$class%> topmsg">
        <%=$hdr ? "<h3>$hdr</h3>" : ''%>
        <p class="last-child">
            <%=$msg%>
        </p>
    </div>
</div>
