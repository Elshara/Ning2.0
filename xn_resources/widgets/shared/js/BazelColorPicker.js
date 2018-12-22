dojo.provide("xg.shared.BazelColorPicker");

dojo.require('xg.shared.util');

dojo.widget.defineWidget(
    // widget name and class
    "xg.shared.BazelColorPicker",

    // superclass
    dojo.widget.HtmlWidget,

    // properties and methods
    {
        // parameters
        fieldname:      'color',
        onChange:       function() { },
        defaultValue:   '#CCC',
        _allowTransparent: false,

        // nls - delayed until postMixInProperties
        nls: {
        },

        // attach points
        colorPickerDiv: null,
        hiddenField:    null,
        swatch:         null,
        IeIframe:       null,
        divSwatch:      null,
        divField:       null,

        // settings
        widgetType:      "BazelColorPicker",
        templateString: '<div class="swatch_group">\
    <span><input type="hidden" dojoAttachPoint="hiddenField" name="${this.fieldname}">\
        <a href="javascript:void(0);" dojoAttachEvent="onClick:showhide">\
            <span class="swatch" dojoAttachPoint="swatch"></span>\
            <button type="button" class="icon" title="${this.nls.openColorPicker}">\
                <img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/button/palette.gif') + '" alt="${this.nls.pickAColor}" />\
            </button>\
        </a>\
        <iframe dojoAttachPoint="IeIframe" class="picker color_picker" frameborder="0" src="" style="display:none;z-index:100;"></iframe>\
        <div dojoAttachPoint="colorPickerDiv" class="picker color_picker" style="display:none;z-index:100;"></div>\
    </span>\
</div>',
        transparentImg:  'url(' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/transparent.gif') + ')',

        // methods
        postMixInProperties: function() {
            this.nls.openColorPicker = xg.shared.nls.html('openColorPicker');
            this.nls.pickAColor =      xg.shared.nls.html('pickAColor');
            this.nls.transparent =     xg.shared.nls.html('transparent');
        },

        fillInTemplate: function(args, frag) {
            // initialization code - called after template substitution
            this._populate();
        },

        postCreate: function(args, frag) {
            if (this.defaultValue.match(/^transparent(?:\s*!important)?$/i)) {
                this.doPickTransparentColor(null);
            } else {
                this._pickColorQuick(this.defaultValue);
            }
        },

        blockSubmit: function(evt) {
            //on Safari Enter and numeric keypad Enter has different keycodes (safari uses 3 for numeric keypad enter)
            if ((evt.keyCode == 13) || (evt.keyCode == 3)) {
                dojo.event.browser.stopEvent(evt);
            }
        },

        valueChanged: function(evt) {
            color = evt.target.value;
            this._highlightColor(color);
            if ((evt.keyCode == 13) || (evt.keyCode == 3)) {
                // Hide the form in onkeyup rather than onkeypress; otherwise onkeyup will not fire. AppearancePanel uses onkeyup to determine
                // when to re-enable saving [Jon Aquino 2006-05-10]
                this._pickColor(color);
            }
        },

        showhide: function(evt) {
            dojo.event.browser.stopEvent(evt);
            if (dojo.html.isDisplayed(this.colorPickerDiv)) {
                this._hide();
            }
            else {
                this._show();
            }
        },

        _show: function() {
            //  Close all other pickers
            dojo.lang.forEach(dojo.widget.manager.getWidgetsByType(this.widgetType),
                    function (w) {
                        if (dojo.html.isDisplayed(w.colorPickerDiv)) {
                            w._hide();
                        }
                    });
            //  Reset displayed color
            this.divSwatch.style.backgroundColor = this._normalize(this.hiddenField.value);
            this.divField.value = this._normalize(this.hiddenField.value);

            dojo.html.show(this.colorPickerDiv);
            if (dojo.render.html.ie) {
                this._fixIeZIndexBug();
            }
        },

        _hide: function() {
            dojo.html.hide(this.colorPickerDiv);
            if (dojo.render.html.ie) {
                this._fixIeZIndexBug();
            }
        },

        _fixIeZIndexBug: function() {
            if (dojo.html.isDisplayed(this.colorPickerDiv)) {
                //  show iframe
                //  Dynamically set the z-index of the swatch group, because
                //    that's how much IE sucks
                this.hiddenField.parentNode.parentNode.style.zIndex = '99';
                dojo.html.show(this.IeIframe);
            }
            else {
                // hide iframe
                this.hiddenField.parentNode.parentNode.style.zIndex = '';
                dojo.html.hide(this.IeIframe);
            }
        },

        _move: function(left, top) {
            this.colorPickerDiv.style.left = left + 'px';
            this.colorPickerDiv.style.top = top + 'px';
            if (dojo.render.html.ie) {
                this._fixIeZIndexBug();
            }
        },

        doPickColor: function(evt) {
            color = dojo.html.getAttribute(evt.target, 'bgcolor');
            this._pickColor(color);
            dojo.event.browser.stopEvent(evt);
        },

        doPickTransparentColor: function(evt) {
            this._hide();
            this.hiddenField.value = 'transparent';
            this.swatch.style.backgroundColor = '';
            this.swatch.style.backgroundImage = this.transparentImg;
            this.onChange();
            if (evt) { dojo.event.browser.stopEvent(evt); }
        },

        _pickTransparentColorQuick: function() {
            this.hiddenField.value = 'transparent';
            this.swatch.style.backgroundColor = '';
            this.swatch.style.backgroundImage = this.transparentImg;
        },

        _pickColor: function(color) {
            this._hide();
            if (this._valid(color)) {
                color = this._normalize(color);
                this.hiddenField.value = color;
                this.swatch.style.backgroundImage = '';
                this.swatch.style.backgroundColor = color;
                this.onChange();
            }
        },

        _pickColorQuick: function(color) {
            if (this._valid(color)) {
                color = this._normalize(color);
                this.hiddenField.value = color;
                this.swatch.style.backgroundImage = '';
                this.swatch.style.backgroundColor = color;
            }
        },

        _valid: function(hex) {
            return this._normalize(hex) != null;
        },

        _normalize: function(hex) {
            if (hex.match(/^#?([A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9])$/)) {
                return ('#' + RegExp.$1).toUpperCase();
            }
            if (hex.match(/^#?([A-Fa-f0-9])([A-Fa-f0-9])([A-Fa-f0-9])$/)) {
                return ('#' + RegExp.$1 + RegExp.$1 + RegExp.$2 + RegExp.$2 + RegExp.$3 + RegExp.$3).toUpperCase();
            }
            if (hex.match(/^transparent(?:\s*!important)?$/i)) {
                return this.nls.transparent;
            }
            return null;
        },

        doHighlightColor: function(evt) {
            color = dojo.html.getAttribute(evt.target, 'bgcolor');
            this._highlightColor(color);
        },

        doHighlightTransparentColor: function(evt) {
            this.divSwatch.style.backgroundColor = '';
            this.divSwatch.style.backgroundImage = this.transparentImg;
            this.divField.value = this.nls.transparent;
            this.divField.disabled = true;
        },

        _highlightColor: function(color) {
            this.divField.value = color;
            this.divField.disabled = false;
            this._updatePreview(color);
        },

        _updatePreview: function(color) {
            if (! this._valid(color)) { return; }
            this.divSwatch.style.backgroundImage = '';
            this.divSwatch.style.backgroundColor = this._normalize(color);
        },

        _populate: function() {
            // Colors are from the color-picker dialog  of the Nvu web authoring system, http://www.nvu-composer.de/wk/images/3/33/ColorPicker.png, [Jon Aquino 2006-05-09]
            var colors = new Array(
                '#FFFFFF', '#FFCCCC', '#FFCC99', '#FFFF99', '#FFFFCC', '#99FF99', '#99FFFF', '#CCFFFF', '#CCCCFF', '#FFCCFF',
                '#CCCCCC', '#FF6666', '#FF9966', '#FFFF66', '#FFFF33', '#66FF99', '#33FFFF', '#66FFFF', '#9999FF', '#FF99FF',
                '#C0C0C0', '#FF0000', '#FF9900', '#FFCC66', '#FFFF00', '#33FF33', '#66CCCC', '#33CCFF', '#6666CC', '#CC66CC',
                '#999999', '#CC0000', '#FF6600', '#FFCC33', '#FFCC00', '#33CC00', '#00CCCC', '#3366FF', '#6633FF', '#CC33CC',
                '#666666', '#990000', '#CC6600', '#CC9933', '#999900', '#009900', '#339999', '#3333FF', '#6600CC', '#993399',
                '#333333', '#660000', '#993300', '#996633', '#666600', '#006600', '#336666', '#000099', '#333399', '#663366',
                '#000000', '#330000', '#663300', '#663333', '#333300', '#003300', '#003333', '#000066', '#330099', '#330033');
            var total = colors.length;
            var width = 10;
            var temp;
            divNode = this.colorPickerDiv;

            temp = document.createElement('a');
            temp.href = 'javascript:void(0);';
            dojo.event.connect(temp, 'onclick', dojo.lang.hitch(this, function() {
                this._pickColor(this.divField.value);
            }));
            temp.innerHTML = xg.shared.nls.html('ok');
            divNode.appendChild(temp);

            temp = document.createElement('a');
            temp.href = 'javascript:void(0);';
            dojo.event.connect(temp, 'onclick', this, '_hide');
            temp.innerHTML = xg.shared.nls.html('cancel');
            divNode.appendChild(temp);

            temp = document.createElement('h4');
            temp.innerHTML = xg.shared.nls.html('pickAColor');
            divNode.appendChild(temp);

            tableNode = document.createElement('table');
            divNode.appendChild(tableNode);

            var trNode = tableNode.insertRow(-1);
            var tdNode;
            // If the browser supports dynamically changing TD cells, add the fancy stuff
            if (document.getElementById) {
                var width1 = Math.floor(width/2);
                var width2 = width1;
                trNode.className = 'selected';
                tdNode = trNode.insertCell(-1);
                tdNode.colSpan = width1;
                this.divSwatch = tdNode;
                tdNode = trNode.insertCell(-1);
                tdNode.colSpan = width2;

                temp = document.createElement('input');
                temp.className = 'color_picker_selected_color_value textfield';
                dojo.event.connect(temp, 'onkeypress', this, 'blockSubmit');
                dojo.event.connect(temp, 'onkeyup', this, 'valueChanged');
                tdNode.appendChild(temp);
                this.divField = temp;
            }

            var use_highlight = (document.getElementById || document.all)?true:false;

            // if allowing transparent color selection, add the row
            if (this._allowTransparent) {
                trNode = tableNode.insertRow(-1);
                trNode.className = 'spacer';
                tdNode = trNode.insertCell(-1);
                tdNode.colSpan = width;
                trNode = tableNode.insertRow(-1);
                trNode.className = 'transparent';
                tdNode = trNode.insertCell(-1);
                tdNode.colSpan = width;
                dojo.event.connect(tdNode, 'onclick', this, 'doPickTransparentColor');
                if (use_highlight) {
                    dojo.event.connect(tdNode, 'onmouseover', this, 'doHighlightTransparentColor');
                }
            }

            for (var i=0; i<total; i++) {
                if ((i % width) == 0) {
                    trNode = tableNode.insertRow(-1);
                    trNode.className = 'swatches';
                }
                tdNode = trNode.insertCell(-1);
                tdNode.bgColor = colors[i];
                dojo.event.connect(tdNode, 'onclick', this, 'doPickColor');
                if (use_highlight) {
                    dojo.event.connect(tdNode, 'onmouseover', this, 'doHighlightColor');
                }
            }
        }
    }
);
