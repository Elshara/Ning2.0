<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *		$isMyEvent
 *		$isAdmin
 *		$event
 *
 */
?>
<div class="xg_module adminbox">
    <div class="xg_module_head">
        <h2><%=xg_html('ADMIN_OPTIONS')%></h2>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets last-child">
            <?php if ($isAdmin) { ?>
                <?php if (XG_PromotionHelper::isPromoted($event)){?>
                    <li><a class="desc feature-remove" href="<%=qh($this->_buildUrl('event','setFeatured',array('id'=>$event->id,'set'=>0)))%>"><%=xg_html('REMOVE_FROM_HOME')%></a></li>
                <?php } else { ?>
                    <li><a class="desc feature-add" href="<%=qh($this->_buildUrl('event','setFeatured',array('id'=>$event->id,'set'=>1)))%>"><%=xg_html('FEATURE_ON_HOME')%></a></li>
                <?php }?>
            <?php } ?>
            <?php if (Events_SecurityHelper::currentUserCanEditLocationEventType($event)) { ?>
                <?php XG_App::ningLoaderRequire('xg.shared.TagLink'); ?>
                <li dojoType="TagLink"
                    _actionUrl="<%= xnhtmlentities($this->_buildUrl('event', 'updateLocationOrType', array('id' => $event->id, 'field' => 'type', 'xn_out' => 'json'))); %>"
                    _allowEmptySubmission="false"
                    _editKey="editTypes"
                    _emptySubmissionMessage="<%= xg_html('NO_EVENT_TYPE') %>"
                    _maxlength="<%= Event::MAX_EVENT_TYPE_LENGTH %>"
                    _updateId="eventTypes"
                    _tags="<%= xnhtmlentities($event->my->eventTypeOrig); %>">
                    <a class="desc edit" href="#"><%= xg_html('EDIT_EVENT_TYPE') %></a>
                </li>
            <?php }?>
            <?php if (Events_SecurityHelper::currentUserCanEditEvent($event)) { ?>
                <li><a href="<%=qh($this->_buildUrl('event','edit',array('id'=>$event->id)))%>" class="desc edit"><%=xg_html('EDIT_EVENT')%></a></li>
            <?php }?>
            <?php if (Events_SecurityHelper::currentUserCanBroadcastMessage($event)) {
                XG_App::ningLoaderRequire('xg.events.BroadcastEventMessageLink');?>
                <li><a dojoType="BroadcastEventMessageLink" href="#"
                    _url="<%=xnhtmlentities($this->_buildUrl('event', 'broadcast', array('id'=>$event->id, 'xn_out' => 'json')))%>"
                    _spamUrl="<%=xnhtmlentities(W_Cache::getWidget('main')->buildUrl('invitation','checkMessageForSpam'))%>"
					_spamMessageParts="<%=xnhtmlentities(json_encode(array(xg_text('NETWORK_NAME') => XN_Application::load()->name, xg_text('EVENT_TITLE')=>$event->title)))%>"
                    class="desc sendmessage"><%=xg_html('SEND_MSG_TO_GUESTS')%></a></li>
            <?php } ?>
            <?php if (Events_SecurityHelper::currentUserCanDeleteEvent($event)) { ?>
                <?php XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                <li><a href="#" class="desc delete" dojoType="PostLink"
                    _confirmTitle="<%= xg_html('DELETE_EVENT') %>"
                    _confirmQuestion="<%= xg_html('ARE_YOU_SURE_DELETE_EVENT') %>"
                    _url="<%= qh($this->_buildUrl('event','delete',array('id'=>$event->id))) %>"
                    _reload="false"><%= xg_html('DELETE_EVENT') %></a></li>
            <?php }?>
        </ul>
    </div>
</div>
