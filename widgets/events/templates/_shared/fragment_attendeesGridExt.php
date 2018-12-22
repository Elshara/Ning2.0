<?php
/*  $Id: $
 *
 *  Attendees grid (extended version for the "attendees" page)
 *
 *  Parameters:
 *      $list       XG_PagingList<User|hash>    Users, or invitation properties
 *      $status     int
 *      $statuses   hash<int:string>            Status => title
 *      $counters   hash<rsvp:count>
 *      $view       grid|list                   How to display the data
 *      $event      W_Content                   Event
 */
$size = 118;
$currentUserCanDeleteAttendees = Events_SecurityHelper::currentUserCanDeleteAttendees($event); ?>
<div class="xg_module">
    <div class="xg_module_head"><h2><%=$statuses[$status]%> (<%=$counters[$status]%>)</h2></div>
    <div class="xg_module_body<%=$status == EventAttendee::NOT_RSVP ? ' body_invited' : ''%>">
<?php
if ($view == 'list') {
    echo '<ul class="nobullets">';
    foreach ($list as $item) {
        echo Events_TemplateHelper::attendeeListItem($item, $event, $currentUserCanDeleteAttendees);
    }
    echo '</ul>';
} else {
        echo '<div class="vcards">';
        $i = 0;
        foreach ($list as $user) {
            $n = xnhtmlentities(xg_username($this->attendeesProfiles[$user->title]));
?>
    <div class="vcard left<%=0 == (($i++) % 5) ? ' clear':''%>">
        <h4><a class="fn url" href="<%=User::quickProfileUrl($user->title)%>">
            <img class="photo" src="<%=xnhtmlentities(XG_UserHelper::getThumbnailUrl($this->attendeesProfiles[$user->title],$size,$size))%>" height="<%=$size%>" width="<%=$size%>" alt="<%=$n%>"/><%=$n%></a>
            <%=$currentUserCanDeleteAttendees ? Events_TemplateHelper::uninviteLinkForMember($user->title, $event) : ''%>
        </h4>
    </div>
<?php
        }
        echo '</div>';
}
XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
XG_PaginationHelper::outputPagination($list->totalCount, $list->pageSize);
?>
    </div>
</div>
