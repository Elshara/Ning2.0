<?php
/**
 * The form for uploading a logo image.
 */
XG_App::ningLoaderRequire('xg.shared.BazelImagePicker', 'xg.shared.BazelColorPicker'); ?>

<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('CUSTOMIZE_YOUR_SLIDESHOW_PLAYER')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="form_section" class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_2col first-child">
                    <div class="xg_module">
                        <form id="customize_player_form" action="<%= xnhtmlentities($this->_buildUrl('photo', 'doCustomizePlayer')) %>" method="post" enctype="multipart/form-data">
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
                                <div id="header_logo_section">
                                    <fieldset class="nolegend">
                                        <dl>
                                            <dt><%= xg_html('PLAYER_COLOR') %></dt>
                                            <?php $playerHeaderBackground = $this->_widget->privateConfig['playerHeaderBackground'] ? $this->_widget->privateConfig['playerHeaderBackground'] : '#111111' ?>
                                            <dd><div dojoType="BazelColorPicker" fieldName="player_header_background" defaultValue="<%= xnhtmlentities($playerHeaderBackground) %>">
                                            <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/button/palette.gif') %>"/></button></div></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="header_logo_file"><%= xg_html('ADD_A_LOGO') %></label></dt>
                                            <dd><div dojoType="BazelImagePicker" trimUploadsOnSubmit="1" fieldName="header_logo_file" allowTile="0"
                                            <?php
                                                echo ' currentImagePath="' . $this->currentLogo . '"';
                                            ?>></div></dd>
                                        </dl>
                                        <br class="clear" />
                                        <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                                        <p><%= xg_html('CUSTOMIZE_EMBED_INSTRUCTIONS', xg_html('SLIDESHOW_PLAYER')) %></p>
                                    </fieldset>
                                    <p class="buttongroup clear">
                                        <input type="submit" class="button" value="<%= xg_html('SAVE') %>" />
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="xg_1col">&nbsp;</div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.shared.BazelColorPicker'); ?>
<?php xg_footer(); ?>