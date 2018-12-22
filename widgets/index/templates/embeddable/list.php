<?php
$app = XN_Application::load();
$appIconUrl = $app->iconUrl();
XG_App::addToSection('<link rel="image_src" href="' . $appIconUrl . '" type="image/jpeg" />');
XG_App::addToSection('<meta name="title" content="' . xnhtmlentities($app->name) .'" />');
 xg_header(null, $title = xg_text('BADGES_AND_WIDGETS'), null); ?>
<?php XG_App::ningLoaderRequire('xg.index.embeddable.EmbedField', 'xg.index.embeddable.list'); ?>
<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
$defaults = array();
$imagePaths = array();
Index_AppearanceHelper::getAppearanceSettings(NULL, $defaults, $imagePaths);
$this->appName = $app->name;
$this->appUrl = 'http://' . $_SERVER['HTTP_HOST'];
$disableOthers = file_exists(NF_APP_BASE . "/xn_private/disable_others") || file_exists(NF_APP_BASE . "/lib/disable_others");
if (!$disableOthers) {
    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ClearspringHelper.php');
?>
<!--Include Clearspring JavaScript library-->
<script src="http://widgets.clearspring.com/launchpad/include.js" type="text/javascript"></script>
<?php
}
?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <?php
                    if (XG_SecurityHelper::userIsAdmin()) { ?>
                        <ul class="page_tabs">
                            <li class="this"><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'list')) %>"><%= xg_html('GALLERY') %></a></li>
                            <li><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'edit')) %>"><%= xg_html('CUSTOMIZATION') %></a></li>
                        </ul>
                    <?php
                    } ?>
                    <h3><%= xg_html('BADGES') %></h3>
                    <?php
                    if (User::isMember(XN_Profile::current())) { ?>
                        <fieldset class="embed easyclear" id="xg_individual_badge_fieldset">
                            <div class="legend"><%= xg_html('MEMBER_BADGE') %></div>
                            <div class="block left">
                                <p><%= xg_html('WEAR_IT_WITH_PRIDE', xnhtmlentities(XN_Application::load()->name)) %></p>
                                <?php
                                ob_start();
                                W_Cache::getWidget('profiles')->dispatch('profile', 'embeddable', array(array('username' => XN_Profile::current()->screenName, 'includeFooterLink' => true)));
                                $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                                ob_end_clean(); ?>
                                <p>
                                    <label for="xg_badge_user_custom_text"><%= xg_html('CUSTOM_TEXT') %>:</label><br />
                                    <input id="xg_badge_user_custom_text" class="textfield" value="<%= xg_html('IM_A_MEMBER_OF') %>"/>
                                </p>
                                <?php
                                if (!$disableOthers) {
                                    $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                }
                                $this->renderPartial('fragment_share_services', 'embeddable', array(
                                    'widgetTitle' => xg_html('X_MEMBER_BADGE', $this->appName),
                                    'embedCode' => $embedCode,
                                    'widgetId' => '483ec89d3823f260',
                                    'config' => $config,
                                    'appUrl' => $this->appUrl,
                                    'linkUrl' => W_Cache::getWidget('profiles')->buildUrl('profile', 'show', array('screenName' => XN_Profile::current()->screenName)),
                                    'fbApp' => false,
                                    'disableOthers' => $disableOthers
                                ));
                                ?>
                                <p>
                                    <label for="xg_badge_user_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                    <input id="xg_badge_user_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"
                                        _beforeCopy="xg.index.embeddable.list.updateIndividualBadge('<%= xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('profile', 'embeddableWithPreview', array('xn_out' => 'json'))) %>')" />
                                </p>
                            </div>
                            <div class="block right align-center">
                                <div class="badge-container" id="gallery_member_badge" ><?php W_Cache::getWidget('profiles')->dispatch('profile', 'embeddable', array(array('username' => XN_Profile::current()->screenName))); ?></div>
                            </div>
                            <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                                <div class="block right align-center">
                                    <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                                </div>
                            <?php } ?>
                        </fieldset>
                    <?php
                    }
                    if (! XG_App::appIsPrivate()) { ?>
                        <hr/>
                        <fieldset class="embed easyclear">
                            <div class="legend"><%= xg_html('NETWORK_BADGE_LARGE') %></div>
                            <div class="block left">
                                <p><%= xg_html('SPREAD_THE_WORD_NETWORK_BADGE_LARGE', xnhtmlentities(XN_Application::load()->name)) %></p>
                                <?php
                                ob_start();
                                W_Cache::getWidget('main')->dispatch('embeddable', 'embeddable', array(array('large' => true, 'includeFooterLink' => true)));
                                $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                                ob_end_clean(); ?>
                                <?php
                                if (!$disableOthers) {
                                    $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                }
                                $this->renderPartial('fragment_share_services', 'embeddable', array(
                                    'widgetTitle' => xg_html('X_NETWORK_BADGE', $this->appName),
                                    'embedCode' => $embedCode,
                                    'widgetId' => '483ece063b670123',
                                    'config' => $config,
                                    'appUrl' => $this->appUrl,
                                    'linkUrl' => $this->appUrl,
                                    'fbApp' => false,
                                    'disableOthers' => $disableOthers
                                    ));
                                ?>
                                <p>
                                    <label for="xg_badge_network_large_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                    <input id="xg_badge_network_large_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"/>
                                </p>
                            </div>
                            <div class="block right align-center" id="gallery_badge">
                                <div class="badge-container"><?php W_Cache::getWidget('main')->dispatch('embeddable', 'embeddable', array(array('large' => true))); ?></div>
                            </div>
                            <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                                <div class="block right align-center">
                                    <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                                </div>
                            <?php } ?>
                        </fieldset>
                    <?php
                    } ?>
                    <hr/>
                    <fieldset class="embed easyclear">
                        <div class="legend"><%= xg_html('NETWORK_BADGE_SMALL') %></div>
                        <div class="block left">
                            <p><%= xg_html('SPREAD_THE_WORD_NETWORK_BADGE_SMALL', xnhtmlentities(XN_Application::load()->name)) %></p>
                            <?php
                            ob_start();
                            W_Cache::getWidget('main')->dispatch('embeddable', 'embeddable', array(array('large' => false, 'includeFooterLink' => true)));
                            $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                            ob_end_clean(); ?>
                            <?php
                            if (!$disableOthers) {
                                $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                            }
                            $this->renderPartial('fragment_share_services', 'embeddable', array(
                                'widgetTitle' => xg_html('X_NETWORK_BADGE', $this->appName),
                                'embedCode' => $embedCode,
                                'widgetId' => '483efbfb007a8a74',
                                'config' => $config,
                                'appUrl' => $this->appUrl,
                                'linkUrl' => $this->appUrl,
                                'fbApp' => false,
                                'disableOthers' => $disableOthers
                                ));

                            ?>
                            <p>
                                <label for="xg_badge_network_small_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                <input id="xg_badge_network_small_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"/>
                            </p>        <br />
                        </div>
                        <div class="block right align-center" id="gallery_badge_small">
                            <div class="badge-container"><?php W_Cache::getWidget('main')->dispatch('embeddable', 'embeddable', array(array('large' => false))); ?></div>
                        </div>
                        <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                            <div class="block right align-center">
                                <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                            </div>
                        <?php } ?>
                    </fieldset>
                </div>
                <?php

            $enabledModules = XG_ModuleHelper::getEnabledModules();
            if (($enabledModules['photo'])||($enabledModules['music'])||($enabledModules['video'])) {
                ?>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('WIDGETS') %></h3>
                    <?php
                    if ($enabledModules['photo']) {
                        XG_App::ningLoaderRequire('xg.index.embeddable.PhotoSlideshowFieldset');
                        $args = array('internal' => false, 'photoSet' => $this->defaultPhotoSlideshowSourceOption['photoSet'],
                                'width' => $this->defaultPhotoSlideshowSizeOption['width'],
                                'height' => $this->defaultPhotoSlideshowSizeOption['height'],
                                'wmode' => 'transparent',
                                'layout' => 'external_site',
                                'noPhotosMessage' => $this->defaultPhotoSlideshowSourceOption['noPhotosMessage'],
                                'contributorName' => XN_Profile::current()->screenName); ?>
                        <fieldset class="embed easyclear" dojoType="PhotoSlideshowFieldset"
                                _url="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'embeddableWithPreview', array_merge($args, array('xn_out' => 'json')))) %>"
                                _sourceOptions="<%= xnhtmlentities($this->json->encode($this->photoSlideshowSourceOptions)) %>"
                                _facebookSource="<%= $this->facebookPhotoSourceReference[XG_FacebookHelper::getFacebookDisplayType('photo')] %>"
                                _sizeOptions="<%= xnhtmlentities($this->json->encode($this->photoSlideshowSizeOptions)) %>">
                            <div class="legend"><%= xg_html('PHOTO_SLIDESHOW_PROPER') %></div>
                            <div class="block left">
                                <p><%= xg_html('DISPLAY_SELECTION_OF_PHOTOS') %></p>
                                <?php
                                ob_start();
                                W_Cache::getWidget('photo')->dispatch('photo', 'embeddable', array(array_merge($args, array('includeFooterLink' => true))));
                                $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                                ob_end_clean(); ?>
                                <?php
                                if (!$disableOthers) {
                                    $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                }
                                $this->renderPartial('fragment_share_services', 'embeddable', array(
                                    'widgetTitle' => xg_html('X_PHOTO_PLAYER', $this->appName),
                                    'embedCode' => $embedCode,
                                    'widgetId' => '483eff46f56776da',
                                    'config' => $config,
                                    'appUrl' => $this->appUrl,
                                    'linkUrl' => W_Cache::getWidget('photo')->buildUrl('photo', 'index'),
                                    'fbApp' => ($this->facebookPhotoSourceReference[XG_FacebookHelper::getFacebookDisplayType('photo')] == $this->defaultPhotoSlideshowSourceOption['photoSet']),
                                    'fbAppUrl' => XG_FacebookHelper::getFacebookEmbedAppUrl('photo'),
                                    'disableOthers' => $disableOthers
                                    ));
                                ?>
                                <p>
                                    <label for="xg_photo_player_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                    <input id="xg_photo_player_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"/>
                                </p>
                            </div>
                            <div class="block right align-center" id="gallery_photo_slideshow">
                                <?php W_Cache::getWidget('photo')->dispatch('photo', 'embeddable', array(array_merge($args, array('externalPreview' => true)))); ?>
                            </div>
                            <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                                <div class="block right align-center">
                                    <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                                </div>
                            <?php } ?>
                        </fieldset>
                    <?php
                    }
                    if ($enabledModules['music']) {
                        XG_App::ningLoaderRequire('xg.index.embeddable.MusicPlayerFieldset');
                        $args = array('internal' => false, 'width' => $this->defaultMusicPlayerSizeOption['width'],
                                'playlistUrl' => urldecode($this->defaultMusicPlayerSourceOption['url']),
                                'noMusicMessage' => $this->defaultMusicPlayerSourceOption['noMusicMessage'],
                                'displayContributor' => $this->displayMusicContributor,
                                'showPlaylist' => true); ?>
                        <?php if ($enabledModules['photo']) { echo '<hr/>'; } ?>
                        <fieldset class="embed easyclear" dojoType="MusicPlayerFieldset"
                                _url="<%= xnhtmlentities(W_Cache::getWidget('music')->buildUrl('playlist', 'embeddableWithPreview', array_merge($args, array('xn_out' => 'json')))) %>"
                                _sourceOptions="<%= xnhtmlentities($this->json->encode($this->musicPlayerSourceOptions)) %>"
                                _sizeOptions="<%= xnhtmlentities($this->json->encode($this->musicPlayerSizeOptions)) %>"
                                _facebookSource="<%= $this->facebookMusicSourceReference[XG_FacebookHelper::getFacebookDisplayType('music')] %>"
                                _showPlaylist="1">
                            <div class="legend"><%= xg_html('MUSIC_PLAYER') %></div>
                            <div class="block left">
                                <p><%= xg_html('PLAY_MUSIC_ON_WEBSITE') %></p>
                                <?php
                                ob_start();
                                W_Cache::getWidget('music')->dispatch('playlist', 'embeddable', array(array_merge($args, array('includeFooterLink' => true))));
                                $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                                ob_end_clean(); ?>
                                <?php
                                if (!$disableOthers) {
                                    $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                }
                                $this->renderPartial('fragment_share_services', 'embeddable', array(
                                    'widgetTitle' => xg_html('X_MUSIC_PLAYER', $this->appName),
                                    'embedCode' => $embedCode,
                                    'widgetId' => '483eff7f02255bec',
                                    'config' => $config,
                                    'appUrl' => $this->appUrl,
                                    'linkUrl' => W_Cache::getWidget('main')->buildUrl('index', 'index', array('fbmusic' => 1)),
                                    'fbApp' => ($this->facebookMusicSourceReference[XG_FacebookHelper::getFacebookDisplayType('music')] == $this->defaultMusicPlayerSourceOption['playlist']),
                                    'fbAppUrl' =>  XG_FacebookHelper::getFacebookEmbedAppUrl('music'),
                                    'disableOthers' => $disableOthers
                                    ));
                                ?>
                                <p>
                                    <label for="xg_music_player_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                    <input id="xg_music_player_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"/>
                                </p>
                            </div>
                            <div class="block right align-center" id="gallery_music_player">
                                <?php W_Cache::getWidget('music')->dispatch('playlist', 'embeddable', array($args)); ?>
                            </div>
                            <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                                <div class="block right align-center">
                                    <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                                </div>
                            <?php } ?>
                        </fieldset>
                        <?php
                    }
                    if ($enabledModules['video']) {
                        XG_App::ningLoaderRequire('xg.index.embeddable.VideoPlayerFieldset');
                        $args = array('videoID' => $this->defaultVideoPlayerSourceOption['videoID'],
                                'autoplay' => false,
                                'layout' => 'external_site',
                                'noVideosMessage' => $this->defaultVideoPlayerSourceOption['noVideosMessage'],
                                'contributorName' => XN_Profile::current()->screenName); ?>
                        <?php if ($enabledModules['music'] || $enabledModules['photo']) { echo '<hr/>'; } ?>
                        <fieldset class="embed easyclear" dojoType="VideoPlayerFieldset"
                                _url="<%= xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video', 'embeddableWithPreview', array_merge($args, array('xn_out' => 'json')))) %>"
                                _facebookSource="<%= $this->facebookVideoSourceReference[XG_FacebookHelper::getFacebookDisplayType('video')] %>"
                                _sourceOptions="<%= xnhtmlentities($this->json->encode($this->videoPlayerSourceOptions)) %>">
                            <div class="legend"><%= xg_html('VIDEO_PLAYER') %></div>
                            <div class="block left">
                                <p><%= xg_html('ADD_A_VIDEO_MORE_OPTIONS', W_Cache::getWidget('video')->buildUrl('index', 'index'), xnhtmlentities(XN_Application::load()->name)) %></p>
                                <?php
                                ob_start();
                                // Keep array_merge args in sync with action_embeddableWithPreview [Jon Aquino 2008-01-15]
                                W_Cache::getWidget('video')->dispatch('video', 'embeddable', array(array_merge($args, array('width' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_WIDTH, 'height' => XG_EmbeddableHelper::EXTERNAL_VIDEO_PLAYER_HEIGHT, 'includeFooterLink' => true))));
                                $embedCode = preg_replace('@[\n ]+@u', ' ', trim(ob_get_contents()));
                                ob_end_clean(); ?>
                                <?php
                                if (!$disableOthers) {
                                    $config = Index_ClearspringHelper::extractConfigJson($embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                }
                                $this->renderPartial('fragment_share_services', 'embeddable', array(
                                    'widgetTitle' => xg_html('X_VIDEO_PLAYER', $this->appName),
                                    'embedCode' => $embedCode,
                                    'widgetId' => '483eff31d1fe1a96',
                                    'config' => $config,
                                    'appUrl' => $this->appUrl,
                                    'linkUrl' => W_Cache::getWidget('video')->buildUrl('video', 'index'),
                                    'fbApp' => ($this->facebookVideoSourceReference[XG_FacebookHelper::getFacebookDisplayType('video')] == $this->defaultVideoPlayerSourceOption['videoID']),
                                    'fbAppUrl' => XG_FacebookHelper::getFacebookEmbedAppUrl('video'),
                                    'disableOthers' => $disableOthers
                                    ));
                                ?>
                                <p>
                                    <label for="xg_video_player_embed_code"><%= xg_html('HTML_EMBED_CODE') %></label><br />
                                    <input id="xg_video_player_embed_code" dojoType="EmbedField" class="textfield" type="text" value="<%= xnhtmlentities($embedCode) %>"/>
                                </p>
                            </div>
                            <div class="block right align-center" id="gallery_video_player">
                                <?php
                                // Keep array_merge args in sync with action_embeddableWithPreview [Jon Aquino 2008-01-15]
                                W_Cache::getWidget('video')->dispatch('video', 'embeddable', array(array_merge($args, array('width' => 300, 'height' => 253, 'externalPreview' => true, 'showDummyVideoIfNoneFound' => true)))); ?>
                            </div>
                            <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                                <div class="block right align-center">
                                    <p><a href="<%= $this->_widget->buildUrl('embeddable', 'edit') %>"><%= xg_html('CUSTOMIZE') %></a></p>
                                </div>
                            <?php } ?>
                        </fieldset>
                    <?php
                    } ?>
                </div><?php
            }
                ?>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
