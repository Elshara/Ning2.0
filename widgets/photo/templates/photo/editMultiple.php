<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('EDIT_PHOTO')); ?>
<?php XG_App::ningLoaderRequire('xg.photo.photo.editMultiple') ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <div id="add_photos_module">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title)%>
            <div class="xg_module">
                <form id="editMultiplePhotosForm" action="<%= xnhtmlentities($this->_buildUrl('photo', 'updateMultiple')) %>" method="post">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <?php
                    // If we're on this page, then at least one file uploaded successfully
                    $skipUrl = count($this->photos) == 1 ? $this->_buildUrl('photo', 'show', array('id' => $this->photos[0]->id)) : $this->_buildUrl('photo', 'listForContributor', array('screenName' => $this->_user->screenName));
                    $names = NF_App::routeRequest(); ?>
                    <div class="xg_module_body">
                        <?php
                        $this->renderPartial('fragment_uploadErrors', 'photo', array('id' => 'editMultiplePhotosForm_notify', 'failedFiles' => $this->failedFiles, 'allHadErrors' => false));
                        if (preg_match('/editMultiple/u', $names['actionName'])) { ?>
                            <p><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_PHOTO_1') %></p>
                            <p><%= xg_html('EVERYTHING_IS_OPTIONAL') %></p>
                            <p class="right"><a href="<%= xnhtmlentities($skipUrl) %>"><strong><%= xg_html('SKIP_THIS_STEP') %>&nbsp;&#187;</strong></a></p>
                        <?php
                        } else { ?>
                            <p><%= xg_html('ADD_TITLE_DESCRIPTION_AND_OTHER_TO_PHOTO_2') %></p>
                        <?php
                        } ?>
                        <fieldset class="clear">
                            <?php
                            foreach ($this->photos as $i => $photo) {
                                if (mb_strlen($photo->my->visibility)) {
                                    $photoVisibility = $photo->my->visibility;
                                } else if (mb_strlen(Photo_UserHelper::get($this->user, 'defaultVisibility'))) {
                                    $photoVisibility = Photo_UserHelper::get($this->user, 'defaultVisibility');
                                } else {
                                    $photoVisibility = $this->_widget->config['defaultVisibility'];
                                }
                                Photo_HtmlHelper::fitImageIntoThumb($photo, $maxWidth = 218, $maxHeight = 218, $imgUrl, $imgWidth, $imgHeight); ?>
                                <input name="photo<%= $i %>-id" id="photo-<%= $i %>-id" type="hidden" value="<%= $photo->id %>"/>
                                <div class="xg_column xg_span-5">
                                    <p class="pad5">
                                        <img class="photo" src="<%= xnhtmlentities($imgUrl) %>" alt="<%= xnhtmlentities($photo->title) %>" />
                                        <br />
                                        <?php XG_App::ningLoaderRequire('xg.photo.photo.RotateLink'); ?>
                                        <a class="desc rotate" href="#"
                                            dojoType="RotateLink"
                                            _url="<%= xnhtmlentities($this->_buildUrl('photo', 'rotate', array('id' => $photo->id, 'xn_out' => 'json', 'maxWidth' => $maxWidth, 'maxHeight' => $maxHeight))) %>"
                                            ><%= xg_html('ROTATE_PHOTO') %></a>
                                        <input type="hidden" name="photo<%= $i %>-rotation" value="<%= xnhtmlentities($photo->my->rotation) %>" />
                                    </p>
                                </div>
                                <div class="xg_column xg_span-6">
                                    <div class="pad5">
                                        <p>
                                            <label for="photo-<%= $i %>-title"><%= xg_html('TITLE_NO_COLON') %></label><br />
                                            <input class="textfield" style="width: 98%;" maxlength="200" size="25" name="photo<%= $i %>-title" id="photo-<%= $i %>-title" type="text"  value="<%= xnhtmlentities($photo->title) %>" />
                                        </p>
                                        <p>
                                            <label for="photo-<%= $i %>-description"><%= xg_html('DESCRIPTION') %></label><br />
                                            <textarea style="width: 98%;" name="photo<%= $i %>-description" rows="5" cols="23" id="photo-<%= $i %>-description"><%= xnhtmlentities($photo->description) %></textarea>
                                        </p>
                                        <p>
                                            <label for="photo-<%= $i %>-tags"><%= xg_html('TAGS') %></label><br />
                                            <input style="width: 90%;" class="textfield tags" size="22" name="photo<%= $i %>-tags" id="photo-<%= $i %>-tags" type="text"  value="<%= xnhtmlentities($this->tags[$i]) %>" />
                                            <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                                            <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                                <span class="context_help_popup" style="display:none">
                                                    <span class="context_help_content">
                                                        <%= xg_html('TAGS_ARE_SHORT_DESCRIPTIONS_PHOTO') %>
                                                        <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                                    </span>
                                                </span>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="xg_column xg_span-5 last-child" style="width:220px">
                                    <div class="pad5">
                                        <fieldset>
                                            <p><label><strong><%= xg_html('WHO_CAN_VIEW_PHOTO') %></strong></label></p>
                                            <ul class="options">
                                                <li><label><input class="radio" type="radio" name="photo<%= $i %>-visibility" value="all" <%= $photoVisibility == 'all' ? 'checked="true"' : '' %> id="photo-<%= $i %>-visibilityAll" /><%= xg_html('ANYONE') %></label></li>
                                                <li><label><input class="radio" type="radio" name="photo<%= $i %>-visibility" value="friends" <%= $photoVisibility == 'friends' ? 'checked="true"' : '' %> id="photo-<%= $i %>-visibilityFriends" /><%= xg_html('JUST_MY_FRIENDS') %></label></li>
                                                <li><label><input class="radio" type="radio" name="photo<%= $i %>-visibility" value="me" <%= $photoVisibility == 'me' ? 'checked="true"' : '' %> id="photo-<%= $i %>-visibilityMe" /><%= xg_html('JUST_ME') %></label></li>
                                            </ul>
                                        </fieldset>
                                        <p>
                                            <label for="photo-<%= $i %>-location"><%= xg_html('LOCATION') %></label><br />
                                            <input class="textfield" maxlength="<%= Photo::MAX_LOCATION_LENGTH %>" size="30" style="width: 98%" name="photo<%= $i %>-location" id="photo-<%= $i %>-location" type="text"  value="<%= xnhtmlentities($photo->my->location) %>" />
                                        </p>
                                        <?php
                                        xg_map_it_link(array(
                                                'locationInputId' => "photo-$i-location",
                                                'latitude' => array('name' => "photo$i-lat", 'id' => "photo-$i-latInput", 'value' => $photo ? $photo->my->lat : null),
                                                'longitude' => array('name' => "photo$i-lng", 'id' => "photo-$i-lngInput", 'value' => $photo ? $photo->my->lng : null),
                                                'zoomLevel' => array('name' => "photo$i-zoomLevel", 'id' => "photo-$i-zoomLevelInput", 'value' => $photo ? $photo->my->locationInfo : null),
                                                'locationType' => array('name' => "photo$i-locationType"))); ?>
                                    </div>
                                </div>
                                <div style="clear: both;"/><!-- --></div>
                                <?php
                                if ($i == 0 && count($this->photos) > 1) { ?>
                                    <p style="clear:left; width:420px;left:170px;position:relative;text-align:center">
                                        <input id="apply_to_all_button" _photoCount="<%= count($this->photos) %>" type="button" class="button" value="<%= xg_html('APPLY_THIS_INFO_TO_PHOTOS') %>" />
                                    </p>
                                <?php
                                }
                            } ?>
                        </fieldset>
                        <p class="buttongroup">
                            <input type="submit" class="button button-primary" value="<%= xg_html('SAVE') %>" />
                            <a href="<%= xnhtmlentities($skipUrl) %>" class="button"><%= xg_html('CANCEL') %></a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php XG_MapHelper::outputScriptTag(); ?>
<?php xg_footer(); ?>
