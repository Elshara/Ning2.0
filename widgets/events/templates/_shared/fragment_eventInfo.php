<?php
/*  $Id: $
 *
 *  Displays event info
 *
 *  Parameters:
 *      $event
 *      $compact                Display compact view of the event
 *      $canAccessEventDetails  Whether the current user is allowed to view details about the event.
 *      $showInviteLink         Whether to display the Invite More People link
 *      $showBackLink			Whether to display the "back to event" link
 *      $nextEvent W_Content    the content object for the next event in the sequence (optional)
 *      $prevEvent W_Content    the content object for the previous event in the sequence (optional)
 *
 */
?>
<div class="xg_module eventDetails">
    <?php XG_CommentHelper::outputStoppedFollowingNotification(xg_html('NO_LONGER_FOLLOWING_EVENT')); ?>
    <div class="xg_module_head">
        <h2><%=xg_html('EVENT_DETAILS')%></h2>
    </div>
    <div class="xg_module_body nopad">
        <div class="xg_column xg_span-4 <%=$compact?'eventDetailsShorten':''%>"><?php if ($event->my->photoUrl){?><div class="pad5"><img src="<%=Events_TemplateHelper::photoUrl($event,180)%>" alt="" class="" /></div><?php }?></div>
        <div class="xg_column xg_span-8 last-child">
            <div class="pad5">
                <p>
                    <%=xg_html('TIME_COLON')%> <%=Events_TemplateHelper::startDate($event)%><br />
                    <?php if ($canAccessEventDetails) {?><span id="eventLocation"><%=xg_html('LOCATION_COLON')%> <%=Events_TemplateHelper::location($event)%></span><br /><?php }?>
                    <?php if (!$compact && $canAccessEventDetails) {?>
                        <?php if ($event->my->street) {?><%=xg_html('STREET_COLON')%> <strong><%=xnhtmlentities($event->my->street)%></strong><br /><?php } ?>
                        <?php if ($event->my->city) {?><%=xg_html('CITY_COLON')%> <strong><%=xnhtmlentities($event->my->city)%></strong><br /><?php } ?>
                        <?php if ($event->my->website) {?><%=xg_html('WEBSITE_OR_MAP_COLON')%> <a href="<%=xnhtmlentities($event->my->website)%>"><%=xg_excerpt(xnhtmlentities($event->my->website),30)%></a><br /><?php } ?>
                        <?php if ($event->my->contactInfo) { ?><%=xg_html('CONTACT_INFO_COLON')%> <strong><%=xnhtmlentities($event->my->contactInfo)%></strong><br /><?php } ?>
                    <?php } ?>
                    <span id="eventTypes"><%=xg_html('EVENT_TYPE_COLON')%> <%=Events_TemplateHelper::type($event)%></span><br />
                    <?php if ($event->my->organizedBy){?><%=xg_html('ORGANIZED_BY_COLON')%> <%=Events_TemplateHelper::organizedBy($event)%><br /><?php }?>
                    <%=xg_html('LATEST_ACTIVITY_COLON_TIME', '<strong>' . xg_elapsed_time($event->updatedDate) . '</strong>')%>
                </p>
                <?php $showExportLink = !$compact && $canAccessEventDetails; ?>
                <?php if ($showInviteLink || $showExportLink || $showBackLink) { ?>
                    <p>
                        <?php if ($showInviteLink) {?>
                            <a href="<%=qh($this->_buildUrl('invitation','new',array('eventId'=>$event->id)))%>" class="desc add"><%=xg_html('INVITE_MORE_PEOPLE')%></a> &nbsp;
                        <?php } ?>
                        <?php if ($showBackLink) {?>
							<a href="<%=qh($this->_buildUrl('event','show',array('id'=>$event->id)))%>" class="desc back"><%=xg_html('BACK_TO_EVENT')%></a> &nbsp;
						<?php }?>
                        <?php if ($showExportLink) {?>
                            <a href="<%=qh($this->_buildUrl('event','export',array('id'=>$event->id)))%>" class="desc download"><%=xg_html('EXPORT_TO_OUTLOOK')%></a>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php if (!$compact && $canAccessEventDetails) {?>
<div class="xg_module">
    <div class="xg_module_head">
        <h2><%=xg_html('EVENT_DESCRIPTION')%></h2>
    </div>
    <div class="xg_module_body">
        <p><%= xg_nl2br(xg_resize_embeds($event->description, 545)) %></p>
        <?php
        if ($prevEvent || $nextEvent) { ?>
            <ul class="pagination smallpagination">
            <?php
            if ($prevEvent) { ?>
                <li class="left"><a href="<%=xnhtmlentities($this->_buildUrl('event','show',array('id'=>$prevEvent->id)))%>" title="<%=xnhtmlentities($prevEvent->title)%>"><%= xg_html('PREVIOUS_EVENT') %></a></li>
            <?php
            }
            if ($nextEvent) { ?>
                <li class="right"><a title="<%=xnhtmlentities($nextEvent->title)%>" href="<%=xnhtmlentities($this->_buildUrl('event','show',array('id'=>$nextEvent->id)))%>"><%= xg_html('NEXT_EVENT') %></a></li>
            <?php
            } ?>
            </ul>
        <?php } ?>
    </div>
</div>
<?php } ?>
