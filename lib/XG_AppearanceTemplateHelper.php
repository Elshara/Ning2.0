<?php

/**
 * Helper class used to display the Edit Appearance page used in both main for Network Creators to edit the network's theme
 * and in profile pages to edit the them of one's own page.
 */
class XG_AppearanceTemplateHelper {

    /**
     * Outputs the Edit Appearance page.
     *
     * @param $networkAppearance        boolean Whether this is the network appearance page (true) or just for a profile page (false).
     * @param $themes                   array   Array of theme names.
     * @param $showNotification         boolean Whether to show notification message or not.
     * @param $notificationClass        string  CSS class for notification message.
     * @param $notificationTitle        string  Title text for notification message.
     * @param $notificationMessage      string  Notification message to show.
     * @param $defaults                 array   Dictionary of starting values for various appearance attributes:
     *                                              pageHeaderTextColor
     *                                              headBgColor
     *                                              headBgImage
     *                                              headBgImage_repeat
     *                                              logoImage
     *                                              ningbarColor
     *                                              siteBgColor
     *                                              siteBgImage
     *                                              siteBgImage_repeat
     *                                              headingFont
     *                                              moduleBodyTextColor
     *                                              textFont
     *                                              siteLinkColor
     *                                              moduleHeadTextColor
     *                                              moduleHeadBgColor
     *                                              pageBgColor
     *                                              pageBgImage
     *                                              pageBgImage_repeat
     *                                              customCss
     * @param $imagePaths               array   Dictionary of paths to various images used in current theme/customization:
     *                                              headBgImage
     *                                              logoImage
     *                                              siteBgImage
     *                                              pageBgImage
     * @param $ningLogoDisplayChecked   boolean Whether "Show Ning Logo?" is checked.
     * @param $fontOptions              array   Array of fontName=>CSS-style font list pairs.
     * @param $displayPrelaunchButtons  boolean Whether to show prelaunch buttons (are we in GYO state?)
     * @param $inJoinFlow               boolean Is the user currently in the process of joining the network?
     * @param $form                     XNC_Form TODO not really sure what this is, something to do with invite/join flow, probably obsolete.
     * @param $screenName               string  Screen name of the current user.
     * @param $appName                  string  Name of the network.
     * @param $submitUrl                string  URL to submit changes to
     */
    public static function outputEditAppearancePage($args) {
        // TODO figure out another way to do this: extract() is evil! [2008-09-03 - Travis Swicegood]
        extract($args);

        // Hide the Network Name color picker and preview area if the site has a logo
        $hideNetworkName = (W_Cache::getWidget('main')->config['logoImageUrl'] ? TRUE : FALSE);
        ?>
        <div class="xg_3col first-child">
            <?php if ($displayPrelaunchButtons) { ?>
                <div id="xg_setup_next_header_top">
                    <?php W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SetupHelper.php'); ?>
                    <%= Index_SetupHelper::nextButton(XG_App::getNextStepUrl(), false, xg_html('LAUNCH')); %>
                </div>
            <?php } ?>
            <?php if ($displayPrelaunchButtons) { ?>
                <h1><%= xg_html('CUSTOMIZE_APPEARANCE') %></h1>
                <p><big><%= xg_html('MAKE_NETWORK_STAND_OUT') %></big></p>
            <?php } elseif ($networkAppearance) { ?>
				<%= xg_headline(xg_text('APPEARANCE'))%>
				<%= $successMessageIfAny %>
            <?php } else { ?>
                 <%= xg_headline(xg_text('EDIT_THE_APPEARANCE'))%>
            <?php } ?>
            <div class="xg_module">
                <div class="xg_module_body">
                    <h3><%= xg_html('FIRST_CHOOSE_THEME') %></h3>
                    <div class="theme_selector">
                        <ul id="xg_theme_list">
                            <?php foreach ($themes as $name) { ?>
								<li><a href="javascript:void(0);" class="xj_theme" _theme="<%= qh($name) %>">
                                    <img src="<?php echo xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/themes/' . $name . '.png')) ?>" alt="" />
                                    <%= $name %></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="xg_module_body">
                    <?php if (! $networkAppearance) { ?>
                        <p style="margin-bottom:2em"><a href="javascript:void(0);" id="xj_apply_app_theme" class="button"><%= xg_html('APPLY_XS_THEME', xnhtmlentities($appName)) %></a></p>
                    <?php } ?>
                    <h3><%= xg_html('MAKE_UNIQUELY_YOURS') %></h3>
                        <?php
                        if ($showNotification) {
                            echo "<dl class='" . $notificationClass . " msg' id='xg_appearance_form_notify'>\n";
                            if ($notificationTitle) {
                                echo "<dt>" . xnhtmlentities($notificationTitle) . "</dt>\n";
                            }
                            echo "<dd><p>" . xnhtmlentities($notificationMessage) . "</p></dd>\n";
                            echo "</dl>\n";
                        } else {
                            echo "<dl class='errordesc msg' id='xg_appearance_form_notify' style='display: none'></dl>\n";
                        }
                        ?>
                    <form id="xg_appearance_form" name="xg_appearance_form" method="post" enctype="multipart/form-data" action="<%= $submitUrl %>">
                        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                        <input type="hidden" id="xg_app_is_launched" value="<%= XG_App::appIsLaunched() ? 'true' : 'false' %>" />
                        <?php if ($networkAppearance) { ?>
                            <input type="hidden" name="stepCompleted" value="0"/>
                            <input type="hidden" name="successTarget"/>
                        <?php } else { ?>
                            <?php /* TODO: is this and all stuff to do with "join flow" obsolete now?  Why would you visit the appearance page as part of join flow? */ ?>
                            <%= $inJoinFlow ? $form->hidden('joinTarget') : '' %>
                        <?php } ?>

                        <ul class="page_tabs">
                            <li id="xg_theme_settings_tab" class="this"><span class="xg_tabs"><%= xg_html('THEME_SETTINGS') %></span></li>
                            <?php /* TODO: Move inline javascript and CSS throughout this file to separate js file [Thomas David Baker 2008-02-28] */ ?>
                            <li id="xg_custom_css_tab"><a href="javascript:void(0);" id="xj_show_custom_css"><%= xg_html('ADVANCED') %></a></li>
                        </ul>
                        <div id="xg_theme_settings_div" class="block left">

                            <fieldset class="appearance clear">
                                <div class="legend xg_lightborder"><%= xg_html('FONTS') %></div>
                                <dl>
                                    <dt><%= xg_html('HEADING_FONT') %></dt>
                                    <dd>
                                        <div class="swatch_group">
											<select id="xg_selectHeadingFont" name="headingFont" disabled="disabled">
                                                <%= self::fontOptions($defaults['headingFont'], $fontOptions) %>
                                            </select>
                                            <h3 id="xg_preview_heading_text" style="display:inline;font-size:1.3em;"><%= xg_html('AABBCC') %></h3>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('BODY_FONT') %></dt>
                                    <dd>
                                        <div class="swatch_group">
											<select id="xg_selectTextFont" name="textFont" disabled="disabled">
                                                <%= self::fontOptions($defaults['textFont'], $fontOptions) %>
                                            </select>
                                            <span id="xg_preview_body_text"><%= xg_html('AABBCC') %></span>
                                        </div>
                                    </dd>
                                </dl>
                                <?php if ($networkAppearance) {
                                    $selectedValue = in_array(W_Cache::getWidget('main')->config['typography'], array('small','large')) ?
                                    W_Cache::getWidget('main')->config['typography'] :
                                    'normal';
                                    $options = array(
                                        array('value' => 'small', 'text' => xg_html('FONT_SIZE_SMALL')),
                                        array('value' => 'normal', 'text' => xg_html('FONT_SIZE_DEFAULT')),
                                        array('value' => 'large', 'text' => xg_html('FONT_SIZE_LARGE'))
                                    );
                                    ?>
                                    <dl>
                                        <dt><%= xg_html('FONT_SIZE') %></dt>
                                        <dd>
                                            <select id="xg_selectFontSize" name="fontSize">
                                                <?php foreach ($options as $option) {
                                                    $selected = $option['value'] === $selectedValue ? ' selected="selected"' : ""; ?>
                                                    <option value="<%= $option['value'] %>"<%= $selected %>><%= $option['text'] %></option>
                                                <?php } ?>
                                            </select>
                                        </dd>
                                    </dl>
                                <?php } ?>

                                <div class="legend xg_lightborder"><%= xg_html('HEADER_FOOTER_AND_SIDES') %></div>
                                <dl<%= ($hideNetworkName && ! $networkAppearance ? ' style="position:absolute;left:-9999px;"' : '') %>>
                                    <dt><%= xg_html('NETWORK_NAME') %></dt>
                                    <dd>
                                        <?php /* TODO: these two params are unnecessary as one follows from the other - make into one? [Thomas David Baker 2008-02-29] */ ?>
                                        <%= self::colorPicker('pageHeaderTextColor', $defaults['pageHeaderTextColor']) %>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('HEADER_BACKGROUND') %></dt>
                                    <dd>
                                        <%= self::colorPicker('headBgColor', $defaults['headBgColor'], true) %>
                                        <%= self::imagePicker('headBgImage', $defaults['headBgImage'], $imagePaths['headBgImage'], true, $defaults['headBgImage_repeat']) %>
                                        <p class="clear small"><%= xg_html('TO_FILL_THE_HEADER_USE_AN_IMAGE_955_PIXELS_WIDE') %></p>
                                    </dd>
                                </dl>
                                <?php if ($networkAppearance) { ?>
                                    <dl>
                                        <dt><%= xg_html('ADD_A_LOGO') %></dt>
                                        <dd>
                                            <%= self::imagePicker('logoImage', $defaults['logoImage'], $imagePaths['logoImage']) %>
                                            <p class="small clear"><%= xg_html('THIS_IMAGE_WILL_REPLACE') %></small>
                                        </dd>
                                    </dl>
                                <?php } ?>
                                <dl>
                                    <dt><%= xg_html('TOP_BAR_AND_FOOTER') %></dt>
                                    <dd>
                                        <%= self::colorPicker('ningbarColor', $defaults['ningbarColor']); %>
                                        <?php if ($networkAppearance) { ?>
                                            <label><input class="checkbox" type="checkbox" name="ningLogoDisplay" value="block" <%= $ningLogoDisplayChecked ? 'checked="checked"' : '' %> /><%= xg_html('SHOW_NING_LOGO') %></label>
                                        <?php } ?>
                                      </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('SIDES') %></dt>
                                    <dd>
                                        <%= self::colorPicker('siteBgColor', $defaults['siteBgColor']) %>
                                        <%= self::imagePicker('siteBgImage', $defaults['siteBgImage'], $imagePaths['siteBgImage'], true, $defaults['siteBgImage_repeat']) %>
                                    </dd>
                                </dl>
                            </fieldset>

                            <fieldset class="appearance">
                                <div class="legend xg_lightborder"><%= xg_html('BODY_AND_CONTENT_AREA') %></div>
                                <dl>
                                    <dt><%= xg_html('TEXT_COLOR') %></dt>
                                    <dd>
                                        <%= self::colorPicker('moduleBodyTextColor', $defaults['moduleBodyTextColor']) %>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('LINK_COLOR') %></dt>
                                    <dd>
                                        <%= self::colorPicker('siteLinkColor', $defaults['siteLinkColor']); %>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('SUBHEADER_COLOR') %></dt>
                                    <dd>
                                        <%= self::colorPicker('moduleHeadTextColor', $defaults['moduleHeadTextColor']); %>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('SUBHEADER_BACKGROUND') %></dt>
                                    <dd>
                                        <%= self::colorPicker('moduleHeadBgColor', $defaults['moduleHeadBgColor'], true); %>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><%= xg_html('BODY_BACKGROUND') %></dt>
                                    <dd>
                                            <%= self::colorPicker('pageBgColor', $defaults['pageBgColor'], true) %>
                                            <%= self::imagePicker('pageBgImage', $defaults['pageBgImage'], $imagePaths['pageBgImage'], true, $defaults['pageBgImage_repeat']) %>
                                    </dd>
                                </dl>
                            </fieldset>
                        </div>
                        <div class="<%= $displayPrelaunchButtons ? '' : 'block' %> right">
                            <%= self::preview($hideNetworkName); %>
                        </div>
                        <div id="xg_custom_css_div" style="display:none">
                            <p><%= xg_html($networkAppearance ? 'CUSTOMIZE_THE_APPEARANCE_NETWORK' : 'CUSTOMIZE_THE_APPEARANCE_PAGE', 'http://www.w3.org/Style/CSS/') %></p>
                            <textarea name="customCss" id="customCss" rows="25" cols="65" class="textarea margin-bottom code left" style="width:390px; margin-right:20px"><?php
                                if ($defaults['customCss']) {
                                    echo $defaults['customCss'];
                                }
                            ?></textarea>
                            <div class="left" style="width:315px">
                                <h4><%= xg_html($networkAppearance ? 'CSS_ON_YOUR_SOCIAL_NETWORK' : 'CSS_ON_YOUR_PAGE') %></h4>
                                <ul>
                                    <li><%= xg_html($networkAppearance ? 'ADDING_YOUR_OWN_CSS_NETWORK' : 'ADDING_YOUR_OWN_CSS_PAGE') %></li>
                                    <li><%= xg_html('CSS_STYLES_WILL_OVERRIDE') %></li>
                                    <li><%= xg_html('NEED_HELP_FIGURING_OUT_CSS', 'http://www.getfirebug.com/') %></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                    <?php if ($displayPrelaunchButtons && $networkAppearance) {
                        W_Cache::getWidget('main')->dispatch('embed', 'backNext', array('nextText' => xg_html('LAUNCH')));
                    } else if ($inJoinFlow && ! $networkAppearance) {
                        W_Cache::getWidget('main')->dispatch('embed', 'joinBackNext');
                    } else {
                        echo "<p class=\"buttongroup\"><a href=\"#\" onClick=\"xg.index.appearance.edit.submitForm(); return false;\" class=\"button button-primary\">" . xg_html('SAVE') . "</a> ";
                        echo " <a href=\"#\" onClick=\"window.location.reload(true); return false;\" class=\"button\">" . xg_html('CANCEL') . "</a></p>";
                    } ?>

                </div>
            </div>

        </div>
		<script>xg.addOnRequire(function() {
			var el;
			var themes = xg.$$('a.xj_theme','xg_theme_list');
			for (var i = 0;i<themes.length;i++) {
				themes[i].onclick = function() { xg.index.appearance.edit.applyTheme(this.getAttribute('_theme')) }
			}
			if (el = dojo.byId('xj_apply_app_theme')) {
				el.onclick = function() { xg.index.appearance.edit.applyTheme('') }
			}
			dojo.byId('xj_show_custom_css').onclick = function() { xg.index.appearance.edit.xg_showCustomCss(this) }

			el = dojo.byId('xg_selectHeadingFont');
			el.onchange = function() { xg.index.appearance.edit.updateHeadingFontPreview() }
			el.disabled = false;

			el = dojo.byId('xg_selectTextFont');
			el.onchange = function() { xg.index.appearance.edit.updateBodyFontPreview() }
			el.disabled = false;
		});</script>
        <?php
    }

    private static function colorPicker($fieldName, $defaultValue, $allowTransparent = false) {
        ob_start(); ?>
        <div dojoType="BazelColorPicker" fieldName="<%= $fieldName %>" defaultValue="<?php echo $defaultValue ?>" _allowTransparent=<%= ($allowTransparent ? 'true' : 'false') %> onChange="xg.index.appearance.edit.updatePreview()">
        <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/button/palette.gif')) %>"/></button></div>
        <?php
        return trim(ob_get_clean());
    }

    private static function imagePickerTileDropDown($name, $defaultTile) {
        $options = array('no-repeat' => xg_text('DONT_REPEAT_IMAGE'),
                         'repeat' => xg_text('TILE_IMAGE'),
                         'repeat-y' => xg_text('REPEAT_VERTICALLY'),
                         'repeat-x' => xg_text('REPEAT_HORIZONTALLY'));
        ?>
            <select name="<%= $name %>_repeat" id="xg_<%= $name %>_repeat">
            <?php
                foreach ($options as $key => $value) {
                    ?>
                    <option value="<%= $key %>"<%= ($defaultTile === $key ? ' selected="selected"' : '') %>><%= $value %></option>
                    <?php
                }
            ?>
            </select>
        <?php
    }

    private static function imagePicker($name, $default, $path, $allowTile=false, $defaultTile = 'no-repeat', $useDropDownTileSelection = true) {
        ob_start(); ?>
        <div dojoType="BazelImagePicker" trimUploadsOnSubmit="1" fieldName="<%= $name %>" allowTile="<%= ($allowTile ? '1' : '0') %>" useDropDownTileSelection=<%= ($useDropDownTileSelection ? 'true' : 'false') %> onChange="xg.index.appearance.edit.showThemeCustomized()"
        <%= ' currentImage="' . $default . '" currentImagePath="' . $path  . '"' . ($defaultTile == 'repeat' ? ' defaultTile="1"' : '') %>></div>
        <?php
        if ($allowTile && $useDropDownTileSelection) {
            self::imagePickerTileDropDown($name, $defaultTile);
        }
        return trim(ob_get_clean());
    }

    private static function fontOptions($font, $fonts) {
        $s = '';
        preg_match('@([\w ]+),?@u', $font, $matches);
        foreach ($fonts as $name => $family) {
            $s .= '<option value="' . xnhtmlentities($name) . '"';
            if ($name == $matches[1]) {
                $s .= ' selected="selected"';
            }
            $s .= ">" . xnhtmlentities($name) . "</option>";
        }
        return $s;
    }

    private static function preview($hideNetworkName) {
        ob_start(); ?>
        <h4 id="xg_theme_name" style="display:none">THEME NAME (Customized)</h4>
        <style type="text/css" id="xg_preview_stylesheet">
        </style>
        <div id="preview" class="preview_black">
            <div class="preview_xg">
                <div class="preview_bar">
                </div>

                <div class="preview_head">
                    <div class="preview_masthead">
                        <?php if (!$hideNetworkName) { ?>
                            <span class="preview_link preview_sitename"><%= xg_html('NETWORK_NAME') %></span>
                        <?php } ?>
                    </div>
                    <div class="preview_navigation">
                        <span class="preview_tab selected"><%= xg_html('MAIN') %></span>
                        <span class="preview_tab preview_link"><%= xg_html('MY_PAGE') %></span>
                        <span class="preview_tab preview_link"><%= xg_html('MEMBERS') %></span>
                    </div>
                </div>

                <div class="preview_body">
                    <span class="preview_pagetitle"><%= xg_html('PAGE_TITLE') %></span>
                    <div class="preview_3col">
                        <div class="preview_module wide">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_1', 'class="preview_link"') %></div>

                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_2') %></div>
                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_2') %></div>

                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_3') %></div>
                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>

                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_2') %></div>
                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_2') %></div>
                        </div>
                        <div class="preview_module">
                            <div class="preview_module_head"></div>
                            <div class="preview_module_body"><%= xg_html('LOREM_IPSUM_2') %></div>
                        </div>
                    </div>
                    <div class="preview_1col">
                        <div class="preview_options">
                            <span></span>
                            <span></span>
                            <span></span>

                        </div>
                        <div class="preview_module">
                            <div class="preview_module_body"></div>
                        </div>
                    </div>
                </div>

                <div class="preview_foot">
                </div>
            </div>

        </div>
        <?php
        return trim(ob_get_clean());
    }
}
