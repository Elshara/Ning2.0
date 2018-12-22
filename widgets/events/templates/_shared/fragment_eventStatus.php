<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *		$event		Event
 *		$rsvp		hash<event-id:count>
 *
 */
?>
<div class="xg_module">
    <div class="xg_module_head"><h2><%=xg_html('YOUR_RSVP')%></h2></div>
    <div class="xg_module_body">
        <form id="rsvpForm" method="post" action="<%=$this->_buildUrl('event','rsvp')%>">
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <input type="hidden" name="id" value="<%=xnhtmlentities($event->id)%>">
            <p><%=Events_TemplateHelper::rsvp($event,array($event->id => $rsvp))%></p>
<?php if ($rsvp > EventAttendee::NOT_RSVP) { ?>
            <p><a href="javascript:void(0);" id="changeStatus" onclick="dojo.style.hide('changeStatus');dojo.style.show('changeStatus2')"><%=xg_html('CHANGE_RSVP')%></a></p>
            <p style="display:none" id="changeStatus2">
<?php } else {?>
            <p>
<?php } ?>
                <label for="eventAttendingStatus"><%=xg_html('YOU_COLON')%></label><br />
                <input onclick="this.form.submit()" type="radio" name="rsvp" value="<%=Events_EventHelper::rsvpToStr(EventAttendee::ATTENDING)%>" <%= $rsvp==EventAttendee::ATTENDING ? 'checked="checked" ':'' %>/> <%=xg_html('ATTEND')%><br />
                <input onclick="this.form.submit()" type="radio" name="rsvp" value="<%=Events_EventHelper::rsvpToStr(EventAttendee::MIGHT_ATTEND)%>" <%= $rsvp==EventAttendee::MIGHT_ATTEND ? 'checked="checked" ':'' %>/> <%=xg_html('MIGHT_ATTEND')%><br />
                <input onclick="this.form.submit()" type="radio" name="rsvp" value="<%=Events_EventHelper::rsvpToStr(EventAttendee::NOT_ATTENDING)%>" <%= $rsvp==EventAttendee::NOT_ATTENDING ? 'checked="checked" ':'' %>/> <%=xg_html('NOT_ATTEND')%><br />
            </p>
        </form>
    </div>
</div>
