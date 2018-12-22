dojo.provide("xg.shared.BazelImagePicker");

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');
dojo.require('xg.shared.topic');

dojo.widget.defineWidget(
    // widget name and class
    "xg.shared.BazelImagePicker",

    // superclass
    dojo.widget.HtmlWidget,

    // properties and methods
    {
        // parameters
        fieldname:          'image',
        onChange:           function() { },
        allowTile:          1,
        useDropDownTileSelection: false,
        showUseNoImage:     1,
        defaultTile:        0,
        cssClass:           'swatch_group',
        trimUploadsOnSubmit: 1,
        currentImage:       '',
        currentImagePath:   '',
        open: false,
        swatchWidth:'45px',
        swatchHeight:'21px',
        saveParentFormOnChange: false,

        // nls - delayed until postMixInProperties
        nls: {
        },


        // attach points
        swatchGroup:        null,
        imagePickerDiv:     null,
        tileCheckboxSpan:   null,
        tileCheckbox:       null,
        actionKeepButton:   null,
        actionThemeButton:  null,
        actionRemoveButton: null,
        actionAddButton:    null,
        currentImageSpan:   null,
        themeImageSpan:     null,
        themeImage:         null,
        fileInput:          null,
        swatchImagePreview: null,
        swatchText:         null,
        currentImagePreview: null,
        themeImagePreview:  null,
        lastAction:         null,
        lastTile:           null,
        useNoImageSpan:     null,
        IeIframe:       	null,

        // settings
        widgetType:     "BazelImagePicker",
        templateString:   '<div dojoAttachPoint="swatchGroup" class="${this.cssClass}">\
    <a href="javascript:void(0);" dojoAttachEvent="onclick:showhide">\
        <img dojoAttachPoint="swatchImagePreview" class="swatch" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/x.gif') + '" style="width: ${this.swatchWidth}; height: ${this.swatchHeight};" alt="${this.nls.currentImage}" />\
        <span dojoAttachPoint="swatchText" style="display:none"></span>\
        <button type="button" class="icon" title="${this.nls.uploadAPhotoEllipsis}"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/button/image.gif') + '" alt="${this.nls.uploadAPhoto}" /></button>\
    </a>\
    <iframe dojoAttachPoint="IeIframe" class="picker image_picker" frameborder="0" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/x.gif') + '" style="display:none;z-index:100;"></iframe>\
    <div dojoAttachPoint="imagePickerDiv" class="picker image_picker" style="display:none;z-index:100;">\
        <h4>${this.nls.uploadAPhotoEllipsis}</h4>\
        <a href="javascript:void(0)" dojoAttachEvent="onclick:doCancel">${this.nls.cancel}</a>\
\
        <ul class="clear options">\
            <span dojoAttachPoint="currentImageSpan" style="overflow:hidden">\
                <li>\
                    <label><input dojoAttachPoint="actionKeepButton" name="${this.fieldname}_action" value="keep" type="radio" class="radio" />${this.nls.useExistingImage}</label><br />\
                    <img dojoAttachPoint="currentImagePreview" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/x.gif') + '" alt="${this.nls.existingImage}" />\
                </li>\
            </span>\
            <span dojoAttachPoint="themeImageSpan" style="overflow:hidden">\
                <li>\
                    <input dojoAttachPoint="themeImage" type="hidden" name="${this.fieldname}_themeImage" value=""/>\
                    <label><input dojoAttachPoint="actionThemeButton" name="${this.fieldname}_action" value="theme" type="radio" class="radio" />${this.nls.useThemeImage}</label><br />\
                    <img dojoAttachPoint="themeImagePreview" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/x.gif') + '" height="100" alt="${this.nls.themeImage}" />\
                </li>\
            </span>\
            <span dojoAttachPoint="useNoImageSpan">\
                <li>\
                    <label><input dojoAttachPoint="actionRemoveButton" name="${this.fieldname}_action" value="remove" type="radio" class="radio" />${this.nls.noImage}</label>\
                </li>\
            </span>\
            <li>\
                <input dojoAttachPoint="actionAddButton" name="${this.fieldname}_action" value="add" type="radio" class="radio" />${this.nls.uploadImageFromComputer}<br />\
                <input dojoAttachPoint="fileInput" dojoAttachEvent="onchange:selectAddAction" size="15" type="file" class="file" name="${this.fieldname}"/><br />\
                <span dojoAttachPoint="tileCheckboxSpan">\
                    <label class="tile"><input dojoAttachPoint="tileCheckbox" type="checkbox" class="checkbox" name="${this.fieldname}_repeat"/>${this.nls.tileThisImage}</label>\
                </span>\
            </li>\
            <p class="right"><input dojoAttachEvent="onclick:showhide" type="button" class="button" value="${this.nls.done}" /></p>\
        </ul>\
    </div>\
</div>\
',

        // methods
        postMixInProperties: function() {
            this.nls.uploadAPhoto = xg.shared.nls.html('uploadAPhoto');
            this.nls.uploadAPhotoEllipsis = xg.shared.nls.html('uploadAPhotoEllipsis');
            this.nls.cancel = xg.shared.nls.html('cancel');
            this.nls.useExistingImage = xg.shared.nls.html('useExistingImage');
            this.nls.existingImage = xg.shared.nls.html('existingImage');
            this.nls.useThemeImage = xg.shared.nls.html('useThemeImage');
            this.nls.themeImage = xg.shared.nls.html('themeImage');
            this.nls.noImage = xg.shared.nls.html('noImage');
            this.nls.uploadImageFromComputer = xg.shared.nls.html('uploadImageFromComputer');
            this.nls.tileThisImage = xg.shared.nls.html('tileThisImage');
            this.nls.done = xg.shared.nls.html('done');
            this.nls.currentImage = xg.shared.nls.html('currentImage');
            this.nls.noo = xg.shared.nls.html('noo');
            this.nls.none = xg.shared.nls.html('none');
        },

        fillInTemplate: function() {
            if ((this.allowTile == 0) || this.useDropDownTileSelection) {
                dojo.dom.removeNode(this.tileCheckboxSpan);
            }
            if (this.showUseNoImage == 0) {
                dojo.html.hide(this.useNoImageSpan);
            }
            if (this.currentImagePath) {
                dojo.html.show(this.currentImageSpan);
            }
            else {
                dojo.html.hide(this.currentImageSpan);
                if (this.showUseNoImage == 0) {
                    dojo.html.hide(this.actionAddButton);
                }
            }
            dojo.html.hide(this.themeImageSpan);
        },

        postCreate: function() {
            var form = dojo.dom.getFirstAncestorByTag(this.actionRemoveButton, 'form');

            if (this.trimUploadsOnSubmit == 1) {
            dojo.event.connect(form, 'onsubmit', this, 'onSubmit');
            }
            if (this.defaultTile == 1) {
                this.tileCheckbox.checked = true;
            }
            if (this.currentImagePath) {
                this.updateAttachPointFromImageUrl(this.currentImagePreview, this.currentImagePath);
                this.actionKeepButton.checked = true;
            }
            else {
                this.actionRemoveButton.checked = true;
            }
            this.updateSwatch();
        },

        showhide: function(evt) {
            dojo.event.browser.stopEvent(evt);
            if (this.open) {
                this._hide();

                // Current settings
                var curAction;
                if (this.actionKeepButton.checked) {
                    curAction = 'keep';
                }
                else if (this.actionThemeButton.checked) {
                    curAction = 'remove';
                }
                else if (this.actionRemoveButton.checked) {
                    curAction = 'remove';
                }
                else {
                    curAction = 'add';
                }

                if ((this.lastAction != curAction) && (this.fileInput.value.length > 0) && this.saveParentFormOnChange) {
                    var form = dojo.byId('settings_form');
                    if (form) { form.submit(); }
                }
            }
            else {
                this._show();
            }
        },
        _show: function() {
            //  Close all other pickers
            dojo.lang.forEach(dojo.widget.manager.getWidgetsByType(this.widgetType),
                    function (w) {
                        if (w.open) {
                            w._hide();
                        }
                    });

            //  Save current settings for potential cancel
            if (this.actionKeepButton.checked) {
                this.lastAction = 'keep';
            }
            else if (this.actionThemeButton.checked) {
                this.lastAction = 'remove';
            }
            else if (this.actionRemoveButton.checked) {
                this.lastAction = 'remove';
            }
            else {
                this.lastAction = 'add';
            }
            this.lastTile = this.tileCheckbox.checked;

            //  Open the picker
            this.imagePickerDiv.style.left = '0px';
            this.imagePickerDiv.style.zIndex = '100';
            dojo.html.show(this.imagePickerDiv);
            if (dojo.render.html.ie) {
                this._fixIeZIndexBug();
            }
            xg.index.util.FormHelper.scrollIntoView(this.imagePickerDiv);
            this.open = true;
            xg.shared.topic.publish('xg.shared.BazelImagePicker.shown', [this]);
        },

        _hide: function() {
            if (dojo.render.html.safari) {
                // Safari ignores form fields with display:none [Jon Aquino 2007-02-17]
                this.imagePickerDiv.style.zIndex = '-9999';
                this.imagePickerDiv.style.left = '-9999px';
            } else {
                dojo.html.hide(this.imagePickerDiv);
            }
            if (dojo.render.html.ie) {
                this._fixIeZIndexBug();
            }
            this.updateSwatch();
            this.open = false;
        },

        _fixIeZIndexBug: function() {
            if (dojo.html.isDisplayed(this.imagePickerDiv)) {
                //  show iframe
                //  Dynamically set the z-index of the swatch group, for IE
                this.swatchGroup.style.zIndex = '99';
                this.IeIframe.style.left = dojo.style.getComputedStyle(this.imagePickerDiv,'left')
                this.IeIframe.style.width = dojo.style.getInnerWidth(this.imagePickerDiv)
                        - dojo.style.getPaddingWidth(this.imagePickerDiv);
                this.IeIframe.style.height = dojo.style.getInnerHeight(this.imagePickerDiv)
                        - dojo.style.getPaddingHeight(this.imagePickerDiv);
                dojo.html.show(this.IeIframe);
            }
            else {
                // hide iframe
                this.swatchGroup.style.zIndex = '';
                dojo.html.hide(this.IeIframe);
            }
        },

        doCancel: function(evt) {
            //  Restore previous settings
            switch (this.lastAction) {
                case 'keep':
                    this.actionKeepButton.checked = true;
                    break;
                case 'theme':
                    this.actionThemeButton.checked = true;
                    break;
                case 'remove':
                    this.actionRemoveButton.checked = true;
                    break;
                case 'add':
                    this.actionAddButton.checked = true;
                    break;
            }
            this.tileCheckbox.checked = this.lastTile;
            this._hide();
        },

        selectAddAction: function(evt) {
            this.actionAddButton.checked = true;
        },

        updateSwatch: function() {
            //  Update swatch preview based on selected action
            if (this.actionKeepButton.checked) {
                dojo.html.hide(this.swatchText);
                this.swatchImagePreview.src = this.currentImagePreview.src;
                dojo.html.show(this.swatchImagePreview);
            }
            else if (this.actionThemeButton.checked) {
                dojo.html.hide(this.swatchText);
                this.swatchImagePreview.src = this.themeImagePreview.src;
                dojo.html.show(this.swatchImagePreview);
            }
            else if (this.actionRemoveButton.checked) {
                dojo.html.hide(this.swatchImagePreview);
                this.swatchText.innerHTML = '<strong>' + this.nls.none + '</strong>';
                this.swatchText.className = 'swatch none';
                dojo.html.show(this.swatchText);
            }
            else {
                dojo.html.hide(this.swatchImagePreview);
                this.swatchText.innerHTML = '<strong>' + this.nls.noo + '</strong>';
                this.swatchText.className = 'swatch new';
                dojo.html.show(this.swatchText);
            }
            this.onChange();
        },

        setImage: function(url) {
            this.actionThemeButton.checked  = true;
            this.themeImage.value = url;
            this.themeImagePreview.src = url;
            dojo.html.show(this.themeImageSpan);
            this.updateSwatch();
        },

        clearImage: function() {
            this.actionRemoveButton.checked = true;
            dojo.html.hide(this.themeImageSpan);
            this.updateSwatch();
        },

        onSubmit: function(evt) {
            if (this.actionAddButton.checked != true) {
                dojo.dom.removeNode(this.fileInput);
            }
        },

        updateAttachPointFromImageUrl: function(attachPoint, imageUrl) {
            attachPoint.src = imageUrl;
            var dims = { 'height': null, 'width': null };
            dojo.lang.forEach(['height','width'], function (dim) {
                var rx = new RegExp(dim + '=(\\d+)');
                var res = imageUrl.match(rx);
                if (res && res[1]) {
                    dims[dim] = res[1];
                }
            }, false);
            if ((dims.height !== null) && (dims.width !== null)) {
                var aspectRatio = dims.width / dims.height;
                if (dims.width > 100) {
                    attachPoint.width = 100;
                    attachPoint.height = parseInt(attachPoint.width / aspectRatio);
                } else {
                    attachPoint.width = dims.width;
                    attachPoint.height = dims.height;
                }
            } else {
                attachPoint.width = 100;
            }

        }
    }
);
