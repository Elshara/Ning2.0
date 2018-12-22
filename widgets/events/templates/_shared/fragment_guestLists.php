<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *		$status		int					Current rsvp status
 *		$statuses	hash<int:string>	Status => title
 *		$counters	hash<rsvp:count>
 *		$event		Event
 *
 */
$urlPrefix	= $this->_buildUrl('event','showAttendees',array('id'=>$event->id,'status'=>''));
?>
<div class="xg_module eventmodule">
	<div class="xg_module_head"><h2><%=xg_html('GUEST_LISTS')%></h2></div>
	<div class="xg_module_body">
		<p>
			<ul class="nobullets">
<?php foreach ($statuses as $s=>$text) {
			echo '<li>',$status != $s ? "<a href=\"$urlPrefix".Events_EventHelper::rsvpToStr($s)."\">$text</a>" : $text," (".$counters[$s].")", '</li>';
}?>
			</ul>
		</p>
		<p><a href="<%=qh($this->_buildUrl('invitation','new',array('eventId'=>$event->id)))%>" class="desc add"><%=xg_html('INVITE_MORE_PEOPLE')%></a></p>
	</div>
</div>
