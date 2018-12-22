<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *  @param  $event
 *  @param  $this->rsvp             hash<event-id:status>
 *
 *  @param  $featuredMode
 *  @param  $showImage
 *  @param  $showExtra
 *  @param  $showWrap
 *  @param  $showRsvp
 *  @param	$imageSize
 *  @param  $tbWrap
 */
$url = $this->_buildUrl('event','show',"?id=$event->id");
$canSee = Events_SecurityHelper::currentUserCanAccessEventDetails($event, $this->rsvp[$event->id]);
?>
<li>
    <?php if($showImage) { ?>
    <div class="ib">
        <a href="<%=xnhtmlentities($url)%>">
            <?php if ($featuredMode) {
                echo $event->my->photoUrl ? '<img src="'.Events_TemplateHelper::photoUrl($event,180).'" alt="" />' : '';
            } else if (!isset($showImage) || $showImage) { ?>
    			<span class="image"<%=$event->my->photoUrl ? ' style="background-image:url(\''.Events_TemplateHelper::photoUrl($event,isset($imageSize) ? $imageSize : 100).'\');"': ''%>><!-- --></span>
            <?php } ?>
        </a>
    </div>
    <?php } ?>
    <?php if($tbWrap) { ?>
        <div class="tb">
    <?php } ?>
            <h3><a href="<%=qh($url)%>"><%=xnhtmlentities($event->title)%></a></h3>
            <p><?php
    			if ($canSee) {
    				echo xg_html('DATE_IN_LOCATION', Events_TemplateHelper::startDate($event), Events_TemplateHelper::location($event));
    			} else {
    				echo Events_TemplateHelper::startDate($event);
    			} ?>
    		</p>
    		<?php 
    			if (!isset($showExtra) || $showExtra) { ?>
                    <?php if ($canSee) {?><p class="info"><%=xg_excerpt($event->description, 200, $url)%></p><?php } ?>
                    <p class="xg_lightfont"><%= xg_html('ORGANIZED_BY_TYPE', Events_TemplateHelper::organizedBy($event), Events_TemplateHelper::type($event)) %></p>
                <?php } ?>
            <%= (!isset($showRsvp) || $showRsvp) && $this->_user->isLoggedIn() ? '<span><strong>' . Events_TemplateHelper::rsvp($event,$this->rsvp) . '</strong></span>' : '' %>
    <?php if($tbWrap) { ?>
        </div>
    <?php } ?>
</li>
