<?php
/*  $Id: $
 *
 *  Display attendees grid
 *
 *  Parameters:
 *      $list           XG_PagingList<User|hash>    Users, or invitation properties
 *      $title
 *      $status         int
 *      $view           grid|list                   How to display the data
 *      $this->attendeesProfiles
 *      $this->event
 *
 */
if (!count($list)) {
    return;
}
?>
<div class="xg_module">
    <div class="xg_module_head"><h2><%=xnhtmlentities($title)%> (<%=$list->totalCount%>)</h2></div>
<?php if ($view == 'list') { ?>
    <div class="xg_module_body">
        <ul class="nobullets" id="peopleInvitedList">
        <?php foreach ($list as $item) {
            echo Events_TemplateHelper::attendeeListItem($item, $this->event);
        } ?>
        </ul>
    </div>
<?php } else { ?>
    <div class="xg_module_body vcard-48grid">
        <?php foreach ($list as $user) {
            echo xg_avatar($this->attendeesProfiles[$user->title], 48);
        } ?>
    </div>
<?php }?>
    <div class="xg_module_foot">
        <ul>
            <li class="right"><a href="<%=$this->_buildUrl('event','showAttendees',array('id'=>$this->event->id,'status'=>Events_EventHelper::rsvpToStr($status))) %>"><%=xg_html('VIEW_ALL')%></a></li>
        </ul>
    </div>
</div>
