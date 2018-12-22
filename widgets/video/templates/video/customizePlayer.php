<?php
/**
 * The form for uploading a logo image.
 */
XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>

<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('CUSTOMIZE_YOUR_VIDEO_PLAYER')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="form_section" class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <form id="customize_player_form" action="<%= xnhtmlentities($this->_buildUrl('video', 'doCustomizePlayer')) %>" method="post" enctype="multipart/form-data">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <div class="xg_module_body pad">
                                <dl class="errordesc msg" id="customize_player_form_notify" <%= $this->error ? '' : 'style="display: none"' %>>
                                    <?php
                                    if ($this->error) { ?>
                                        <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                                        <dd><ol><li><%= xnhtmlentities($this->error) %></li></ol></dd>
                                    <?php
                                    } ?>
                                </dl>
                                <p><%= xg_html('CHOOSE_HEADER_OR_WATERMARK') %></p>
                                <ul class="page_tabs">
                                    <?php
                                    if ($this->_widget->privateConfig['playerLogoType'] != 'watermark_image') {
                                    ?>
                                    <li class="this" id="header_logo_tab"><span class="xg_tabs"><%= xg_html('HEADER') %></span></li>
                                    <li id="watermark_logo_tab"><a dojoType="TabTrigger" _tabId="watermark_logo_tab" _otherTabId="header_logo_tab" href="#"><%= xg_html('WATERMARK') %></a></li>
                                    <?php
                                    } else {
                                    ?>
                                    <li id="header_logo_tab"><a dojoType="TabTrigger" _tabId="header_logo_tab" _otherTabId="watermark_logo_tab" href="#"><%= xg_html('HEADER') %></a></li>
                                    <li class="this" id="watermark_logo_tab"><span class="xg_tabs"><%= xg_html('WATERMARK') %></span></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                                <fieldset class="nolegend clear">
                                    <dl>
                                        <dt><%= xg_html('PLAYER_COLOR') %></dt>
                                        <?php $playerHeaderBackground = $this->_widget->privateConfig['playerHeaderBackground'] ? $this->_widget->privateConfig['playerHeaderBackground'] : '#111111' ?>
                                        <dd><div dojoType="BazelColorPicker" fieldName="player_header_background" defaultValue="<%= xnhtmlentities($playerHeaderBackground) %>">
                                        <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/button/palette.gif') %>"/></button></div></dd>
                                    </dl>
                                    <div id="header_logo_section" <%= ($this->_widget->privateConfig['playerLogoType'] == 'watermark_image')?'style="display: none"':'' %>>
                                        <dl>
                                            <dt><%= xg_html('HEADER_LOGO') %></dt>
                                            <dd><div dojoType="BazelImagePicker" trimUploadsOnSubmit="1" fieldName="header_logo_file" allowTile="0"<?php echo ' currentImagePath="' . $this->currentHeader . '"'; ?>></div></dd>
                                        </dl>
                                        <br class="clear"/>
                                        <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                        <p><%= xg_html('CUSTOMIZE_EMBED_INSTRUCTIONS', xg_html('VIDEO_PLAYER')) %></p>
                                    </div>
                                    <div id="watermark_logo_section" <%= ($this->_widget->privateConfig['playerLogoType'] != 'watermark_image')?'style="display: none"':'' %>>
                                        <dl>
                                            <dt><%= xg_html('WATERMARK_LOGO') %></dt>
                                            <dd><div dojoType="BazelImagePicker" fieldName="watermark_logo_file" allowTile="0"<?php echo ' currentImagePath="' . $this->currentWatermark . '"';?>></div></dd>
                                        </dl>
                                        <br class="clear"/>
                                        <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                        <p><%= xg_html('CUSTOMIZE_WATERMARK_INSTRUCTIONS') %></p>
                                    </div>
                                </fieldset>
                                <p class="buttongroup clear">
                                    <input type="submit" class="button" value="<%= xg_html('SAVE') %>" />
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.customizePlayer', 'xg.video.video.TabTrigger', 'xg.shared.BazelColorPicker', 'xg.shared.BazelImagePicker'); ?>
<?php xg_footer(); ?>
