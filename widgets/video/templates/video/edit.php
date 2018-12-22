<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('EDIT_VIDEO')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
		<%= xg_headline($title)%>
        <div class="xg_module">
            <dl class="errordesc msg" id="edit_video_form_notify" style="display: none"></dl>
            <form id="edit_video_form" action="<?php echo $this->_buildUrl('video', 'update', '?id=' . $this->video->id); ?>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module_body">
                    <?php if ($_GET['new']) { ?>
                        <h3><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_VIDEO_1') %></h3>
                    <?php } else { ?>
                        <h3><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_VIDEO_2') %></h3>
                    <?php } ?>
                </div>
                <div class="xg_module_body nopad">
                    <fieldset>
                        <div class="xg_column xg_span-5">
                            <p class="pad5 align-center">
                                <%= $this->renderPartial('fragment_thumbnailProper', array('video' => $this->video, 'thumbWidth' => 218)); %>
                                <%= $this->filename ? xnhtmlentities($this->filename) : ''; %>
                            </p>
                        </div>
                        <div class="xg_column xg_span-6">
                            <div class="pad5">
                                <p>
                                    <label for="video_title"><%= xg_html('TITLE') %></label><br />
                                    <input id="video_title" style="width: 98%;" type="text" class="textfield" size="25" name="title" maxlength="200" value="<?php echo xnhtmlentities($this->video->title) ?>" />
                                </p>
                                <p>
                                    <label for="video_description"><%= xg_html('DESCRIPTION') %></label><br/>
                                    <textarea id="video_description" style="width: 98%" rows="5" cols="23" name="description"><?php echo xnhtmlentities($this->video->description) ?></textarea>
                                </p>
                                <p>
                                    <label for="video_tags"><%= xg_html('TAGS') %></label><br />
                                    <input id="video_tags" type="text" style="width: 90%;" class="textfield tags" size="22" name="tags" value="<%=xnhtmlentities(Video_VideoHelper::getTagStringForUser($this->_user, $this->video)) %>" maxlength="2000" />
                                    <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                                    <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                        <span class="context_help_popup" style="display:none">
                                            <span class="context_help_content">
                                                <%= xg_html('TAGS_ARE_SHORT_DESCRIPTIONS_VIDEO') %>
                                                <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                            </span>
                                        </span>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="xg_column xg_span-5 last-child">
                            <div class="pad5">
                                <fieldset>
                                    <div class="legend"><strong><%= xg_html('WHO_CAN_VIEW_VIDEO') %></strong></div>
                                    <ul class="options">
                                        <li><label><input type="radio" class="radio" name="visibility" value="all" <?php echo $this->video->my->visibility == 'all' ? 'checked="checked"' : '' ?> /> <?php echo Video_VideoHelper::visibilityDescription('all') ?></label></li>
                                        <li><label><input type="radio" class="radio" name="visibility" value="friends" <?php echo $this->video->my->visibility == 'friends' ? 'checked="checked"' : '' ?> /> <?php echo Video_VideoHelper::visibilityDescription('friends') ?></label></li>
                                        <li><label><input type="radio" class="radio" name="visibility" value="me" <?php echo $this->video->my->visibility == 'me' ? 'checked="checked"' : '' ?> /> <?php echo Video_VideoHelper::visibilityDescription('me') ?></label></li>
                                    </ul>
                                </fieldset>
                                <p>
                                    <label for="location"><%= xg_html('LOCATION') %></label><br/>
                                    <input id="location" style="width: 98%;" type="text" class="textfield" size="30" name="location" maxlength="200" value="<?php echo xnhtmlentities($this->video->my->location) ?>" />
                                </p>
                                <div>
                                <?php
                                xg_map_it_link(array(
                                        'locationInputId' => 'location',
                                        'latitude' => array('name' => "lat", 'id' => "video-latInput", 'value' => $this->video ? $this->video->my->lat : null),
                                        'longitude' => array('name' => "lng", 'id' => "video-lngInput", 'value' => $this->video ? $this->video->my->lng : null),
                                        'zoomLevel' => array('name' => "zoomLevel", 'id' => "video-zoomLevelInput", 'value' => $this->video ? $this->video->my->locationInfo : null),
                                        'locationType' => array('name' => "locationType"))); ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <p class="buttongroup">
                    <input type="submit" value="<%= xg_html('SAVE') %>" class="button button-primary"/>
                    <a href="<%= $this->_buildUrl('video', 'show', '?id=' . $this->video->id); %>" class="button"><%= xg_html('CANCEL') %></a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php XG_MapHelper::outputScriptTag(); ?>
<?php XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.edit'); ?>
<?php xg_footer(); ?>
