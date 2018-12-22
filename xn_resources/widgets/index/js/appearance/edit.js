dojo.provide('xg.index.appearance.edit');

dojo.require('xg.shared.BazelColorPicker');
dojo.require('xg.shared.BazelImagePicker');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

xg.index.appearance.edit = {
	initialized: 0,
    lastStyle: null,
    themeName: null,
    themeCustomized: false,
    transparentImg: 'url(' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/transparent.gif') + ')',

    fontFamilies: {
        'Andale Mono': '"Andale Mono", "Courier New", Courier, monospace',
        'Helvetica Neue': '"Helvetica Neue", Arial, Helvetica, sans-serif',
        'Arial Black': '"Arial Black", sans-serif',
        'Comic Sans MS': '"Comic Sans MS", sans-serif',
        'Courier New': '"Courier New", Courier, "Andale Mono", monospace',
        'Futura': 'Futura, "Avant Garde", "Century Gothic", "Gill Sans MT", sans-serif',
        'Georgia': 'Georgia, "Times New Roman", Times, serif',
        'Gill Sans': '"Gill Sans", "Gill Sans MT", "Gill", "Century Gothic", sans-serif',
        'Impact': 'Impact, sans-serif',
        'Lucida Grande': '"Lucida Grande", "Lucida Sans Unicode", Arial, clean, sans-serif',
        'Times New Roman': '"Times New Roman", Times, Georgia, serif',
        'Trebuchet MS': '"Trebuchet MS", sans-serif',
        'Verdana': 'Verdana, Helvetica, Arial, sans-serif'
    },

    initialize: function() {
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
        this.initialized = 1;
    },

    /**
     * Converts an HTML color value, including special color 'transparent' for transparent to
     * CSS string
     *
     * @param color string  HTML color value
     *
     * @return string  CSS
     */
    colorValueToCssString: function(color) {
        if (color.match(/^transparent(?:\s*!important)?$/i)) {
            // special transparent color
            return '{background-color:; background-image:' + this.transparentImg + '; background-repeat:repeat;}';
        }
        return '{background-color:' + color + '; background-image:; background-repeat:;}';
    },

    updatePreview: function() {
    	if (!this.initialized) {
    		return;
		}
        if (xg.index.appearance.edit.lastStyle) {
            xg.index.appearance.edit.lastStyle.parentNode.removeChild(xg.index.appearance.edit.lastStyle);
        }
        af = document.xg_appearance_form;
        style  = '#preview {background-color:' + af.siteBgColor.value + '; background-image:; background-repeat:;} ';
        style += '#preview .preview_link {color:' + af.siteLinkColor.value + ';} ';
        style += '#preview .preview_bar, #preview .preview_foot ' + this.colorValueToCssString(af.ningbarColor.value) + ' ';
        style += '#preview .preview_head ' + this.colorValueToCssString(af.headBgColor.value) + ' ';
        style += '#preview .selected {color:' + af.siteLinkColor.value + ';} ';
        style += '#preview .preview_body ' + this.colorValueToCssString(af.pageBgColor.value) + ' ';
        style += '#preview .preview_sitename {color:' + af.pageHeaderTextColor.value + ';} ';
        style += '#preview .preview_pagetitle {color:' + af.moduleBodyTextColor.value + ';} ';
        style += '#preview .preview_module_head ' + this.colorValueToCssString(af.moduleHeadBgColor.value) + ' ';
        style += '#preview .preview_options span ' + this.colorValueToCssString(af.moduleHeadBgColor.value) + ' ';
        style += '#preview .preview_module_body {color:' + af.moduleBodyTextColor.value + ';} ';
        xg.index.appearance.edit.lastStyle = dojo.style.insertCssText(style);
        xg.index.appearance.edit.showThemeCustomized();
    },

    updateHeadingFontPreview: function() {
        var af = document.xg_appearance_form;
        var font = af.headingFont.options[af.headingFont.selectedIndex].text;
        var family = xg.index.appearance.edit.fontFamilies[font];
        dojo.byId('xg_preview_heading_text').style.fontFamily = family;
        xg.index.appearance.edit.showThemeCustomized();
    },

    updateBodyFontPreview: function() {
        var af = document.xg_appearance_form;
        var font = af.textFont.options[af.textFont.selectedIndex].text;
        var family = xg.index.appearance.edit.fontFamilies[font];
        dojo.byId('xg_preview_body_text').style.fontFamily = family;
        xg.index.appearance.edit.showThemeCustomized();
    },

    applyTheme: function(name, appIsLaunched) {
        dojo.io.bind({
            url: '/index.php/main/appearance/getThemeSettings?theme=' + name + '&xn_out=json',
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                var currentCustomCss = dojo.string.trim(dojo.byId('customCss').value || '');
                if (appIsLaunched == "true" && currentCustomCss != '' && currentCustomCss != dojo.string.trim(data.customCss || '')) {
                    xg.shared.util.confirm({
                        title: 'Warning!',
                        bodyText: "Any CSS you've added on the Advanced tab will be removed if you change themes.",
                        onOk: function() { xg.index.appearance.edit._applyThemeProper(data, name); }
                    });
                } else {
                    xg.index.appearance.edit._applyThemeProper(data, name);
                }
            })
        });
    },

    _applyThemeProper: function(theme, name) {
        var af = document.xg_appearance_form;
        if (theme.colors) {
            //  Set all colors to the colors in the theme
            var widgets = dojo.widget.manager.getWidgetsByType('BazelColorPicker');
            for(var n = 0; n < widgets.length; n++) {
                if (theme.colors[widgets[n].fieldname]) {
                    var color = theme.colors[widgets[n].fieldname];
                    if (color.match(/^transparent(?:\s*!important)?$/i)) {
                        widgets[n]._pickTransparentColorQuick();
                    } else {
                        widgets[n]._pickColorQuick(theme.colors[widgets[n].fieldname]);
                        xg.index.appearance.edit.updateBodyFontPreview()
                    }
                }
            }
        }
        if (theme.fonts) {
            //  Set all fonts
            if (theme.fonts.textFont) {
                xg.index.util.FormHelper.select(theme.fonts.textFont, dojo.byId('xg_selectTextFont'));
            }
            if (theme.fonts.headingFont) {
                xg.index.util.FormHelper.select(theme.fonts.headingFont, dojo.byId('xg_selectHeadingFont'));
            }
        }
        if (theme.images) {
            //  Set all images
            var widgets = dojo.widget.manager.getWidgetsByType('BazelImagePicker');
            for(var n = 0; n < widgets.length; n++) {
                var fieldName = widgets[n].fieldname;
                if (theme.images[fieldName]) {
                    widgets[n].setImage(theme.images[fieldName]);
                }
                else if (fieldName !== 'logoImage') {
                    widgets[n].clearImage();
                }
                // set repeat status
                var select = dojo.byId('xg_' + fieldName + '_repeat');
                if (select) { select.value = theme.imageRepeat[fieldName]; }
            }
        }
        xg.index.appearance.edit.updatePreview();

        //  Indicate the theme applied above the preview
        if (name.length > 0) {
            xg.index.appearance.edit.themeName = name;
            xg.index.appearance.edit.themeCustomized = false;
        }
        else {
            xg.index.appearance.edit.themeName = ning.CurrentApp.name;
            xg.index.appearance.edit.themeCustomized = false;
        }
        var themeName = dojo.byId('xg_theme_name');
        if (themeName) {
            themeName.innerHTML = xg.index.appearance.edit.themeName;
            dojo.html.show(dojo.byId('xg_theme_name'));
        }

        if (theme.customCss) {
            af.customCss.value = theme.customCss;
        }
        else {
            af.customCss.value = '';
        }
    },

    showThemeCustomized: function() {
    	if (!this.initialized) {
    		return;
		}
        //  Label the theme as customized if one has been chosen
        if (xg.index.appearance.edit.themeName && !xg.index.appearance.edit.themeCustomized) {
            xg.index.appearance.edit.themeCustomized = true;
            var themeName = dojo.byId('xg_theme_name');
            if (themeName) {
                themeName.innerHTML = xg.index.appearance.edit.themeName
                        + ' (' + xg.index.nls.html('customized') + ')';
            }
        }
    },

    xg_showThemeSettings: function(a) {
        this.showTab(dojo.byId('xg_theme_settings_div'));
        this.hideTab(dojo.byId('xg_custom_css_div'));
        var themeTab = dojo.byId('xg_theme_settings_tab');
        var cssTab = dojo.byId('xg_custom_css_tab');
        themeTab.className = 'this';
        themeTab.innerHTML = '<span class="xg_tabs">' + xg.index.nls.html('themeSettings') + '</span>';
        cssTab.className = '';
        cssTab.innerHTML = '<a href="javascript:void(0);"'
                + ' onClick="xg.index.appearance.edit.xg_showCustomCss(this)">' + xg.index.nls.html('addYourOwnCss') + '</a>';
        themeTab = null;
        cssTab = null;
    },

    xg_showCustomCss: function(a) {
        this.hideTab(dojo.byId('xg_theme_settings_div'));
        this.showTab(dojo.byId('xg_custom_css_div'));
        var themeTab = dojo.byId('xg_theme_settings_tab');
        var cssTab = dojo.byId('xg_custom_css_tab');
        themeTab.className = '';
        themeTab.innerHTML = '<a href="javascript:void(0);"'
                + ' onClick="xg.index.appearance.edit.xg_showThemeSettings(this)">' + xg.index.nls.html('themeSettings') + '</a>';
        cssTab.className = 'this';
        cssTab.innerHTML = '<span class="xg_tabs">' + xg.index.nls.html('addYourOwnCss') + '</span>';
        themeTab = null;
        cssTab = null;
    },

    /**
     * Hides the given tab div.
     *
     * @param HTMLDivElement div  The tab body to hide
     */
    hideTab: function(div) {
        if (dojo.render.html.safari) {
            // Safari ignores form fields with display:none [Jon Aquino 2007-02-17]
            div.style.position = 'absolute';
            div.style.left = '-9999px';
        } else {
            dojo.html.hide(div);
        }
        if (div.id == 'xg_theme_settings_div') { dojo.html.hide(dojo.byId('preview').parentNode); }
    },

    /**
     * Shows the given tab div.
     *
     * @param HTMLDivElement div  The tab body to show
     */
    showTab: function(div) {
        div.style.position = 'static';
        div.style.left = '0px';
        dojo.html.show(div);
        if (div.id == 'xg_theme_settings_div') { dojo.html.show(dojo.byId('preview').parentNode); }
    },

    fixSafariHideBug: function() {
        if (dojo.render.html.safari) {
            var div = dojo.byId('xg_custom_css_div');
            div.style.position = 'absolute';
            div.style.left = '-9999px';
            dojo.html.show(div);
        }
    },

    submitForm: function() {
        dojo.byId('xg_appearance_form').submit();
    },

    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('xg_appearance_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.appearance.edit.submitForm();
    }

};

xg.addOnRequire(function() {
	var edit = xg.index.appearance.edit;

	edit.initialize();
    edit.fixSafariHideBug();
    edit.updatePreview();
    edit.updateHeadingFontPreview();
    edit.updateBodyFontPreview();
	edit.showThemeCustomized();
    var form = dojo.byId('xg_appearance_form');
    dojo.event.connect(form, 'onsubmit', xg.index.appearance.edit, 'submitForm');
});
