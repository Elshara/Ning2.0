<?php
/*  $Id: $
 *
 *	Display featured list
 *
 *  Parameters:
 *	@param	$list					XG_PagingList<Event>
 *	@param	$showViewAll
 *	@param	$this->rsvp				hash<event-id:status>
 *
 */
?>
<%= xg_headline(xg_text('FEATURED_EVENTS'))%>
<div class="xg_module">
	<div class="xg_module_body">
	  <div class="xg_list xg_list_events xg_list_events_feature">
  		<ul>
  			<?php $i = 0;foreach ($list as $event) {
  			?>
  				<li>
  				  <div class="bd">
  				    <div class="ib">
  				      <a href="<%=$this->_buildUrl('event','show',"?id=$event->id")%>"><img src="<%=$event->my->photoUrl ? Events_TemplateHelper::photoUrl($event,139) . '"': ''%>" height="139" width="139" alt="<%=xnhtmlentities($event->title)%>" class="xg_lightborder" /></a>
  				    </div>
  				    <div class="tb">
  				      <h3><a href="<%=$this->_buildUrl('event','show',"?id=$event->id")%>"><%=xnhtmlentities($event->title)%></a></h3>
  				      <p>
  				        <span class="item_time"><%=Events_TemplateHelper::startDate($event)%></span>
  				        <span class="item_status"><%= $this->_user->isLoggedIn() ? Events_TemplateHelper::rsvp($event,$this->rsvp) : '' %></span>
  				      </p>
  				    </div>
  				  </div>
  				</li>
  			<?php }?>
  		</ul>
		</div>
	</div>
	<?php if ($list->pageCount > 1 && $showViewAll) { ?>
	  <div class="xg_module_foot">
	    <p class="right"><a href="<%=$this->_buildUrl('event','listFeatured')%>"><%=xg_html('VIEW_ALL')%></a></p>
	  </div>
	<?php } ?>
</div>
