<?php
/*  $Id: $
 *
 *  Displays comments for an event
 *
 *  Parameters:
 *      $event  W_Content   The Event object
 *      $status integer     Attendance status, e.g., EventAttendee::MIGHT_ATTEND
 */
$htmlIfCannotAddComment = '&nbsp;';
if (Events_SecurityHelper::currentUserCanAddComment($status, $event)) {
} else if ($event->my->privacy != Event::ANYONE || $event->my->isClosed || $event->my->disableRsvp) {
    // display nothing. there is no way to ask for invitation.
    // also if event is closed there is no reason to suggest to join.
    // or if RSVP is disabled, the user can't join and therefore can't comment
    $htmlIfCannotAddComment = '&nbsp;';
} else {
    if (!XN_Profile::current()->isLoggedIn()) {
        $htmlIfCannotAddComment =
            '<h3>' . xg_html('YOU_NEED_TO_RSVP_EVENT', xnhtmlentities($event->title)) . '</h3>' .
            '<p>' . xg_html('SIGN_UP_OR_SIGN_IN', 'href="' . xnhtmlentities(XG_HttpHelper::signUpUrl()) . '"', 'href="' . xnhtmlentities(XG_HttpHelper::signInUrl()) . '"') . '</p>';
    } else {
        XG_App::ningLoaderRequire('xg.events.comments');
        $htmlIfCannotAddComment =
            '<h3>' . xg_html('YOU_NEED_TO_RSVP_EVENT', xnhtmlentities($event->title)) . '</h3>' .
            '<p>' . xg_html('CLICK_HERE_TO_RSVP', 'href="#" id="xj_rsvp_link"') . '</p>';
    }
}
XG_CommentHelper::outputStandardComments(array(
    'attachedTo' => $event,
    'newestCommentsFirst' => true,
	'addCommentsHeader' => true,
    // for public events anybody can see comments, for private only invited people
    'currentUserCanSeeAddCommentSection' => Events_SecurityHelper::currentUserCanAddComment($status, $event) || $htmlIfCannotAddComment != '&nbsp;',
    'currentUserCanAddComment' => Events_SecurityHelper::currentUserCanAddComment($status, $event),
    'htmlIfCannotAddComment' => $htmlIfCannotAddComment,
    'showFeedLink' => Events_SecurityHelper::commentFeedAvailable($event))
);
?>
