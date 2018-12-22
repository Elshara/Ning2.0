dojo.provide('xg.shared.AddImageDialog');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.IframeUpload');

/**
 * A dialog box that prompts the user to specify an image to upload.
 */
xg.shared.AddImageDialog = {

    /**
     * Asks the user to specify an image to upload.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     * @param noOptions suppresses options block
     */
    promptForImage: function(callback, noOptions) {
        if (dojo.render.html.safari) {
            this.safariImage(callback);
        } else {
            this.showForm(callback, noOptions);
        }
    },

    /**
     * Asks the user to specify an image to upload, using a simplified prompt that works in Safari.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     */
    safariImage: function(callback) {
        var img = prompt("Please enter an image address", "http://");
        var tag;
        if (img != null) {
            tag = "<img src=\"" + img + "\" />";
        } else {
            tag = '';
        }
        callback(tag);
    },

    showSpinner: function() {
        dojo.html.hide('upload-form-container');
        dojo.html.show('shared-upload-progress');
    },
    /**
     * Process the response from the image upload.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     */
    uploadInsert: function(data, callback) {
        // Decode the encoding that gets around IE's funky JSON parsing
        data.html = decodeURIComponent(data.html);
        if (data.error) {
            dojo.html.hide('shared-upload-progress');
            dojo.byId('shared-upload-error-message').innerHTML = data.error;
            dojo.html.show('shared-upload-error');
        } else {
            xg.shared.util.hideOverlay();
            dojo.html.hide('shared-upload-progress');
            dojo.html.hide('shared-upload-module');
            dojo.html.hide('shared-upload-module-container');
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
            callback(data.html);
        }
    },

    uploadValidate: function(form) {
        var errors = { };
        errors = xg.index.util.FormHelper.validateRequired(errors, form, 'file', this.nls.pleaseSelectAFile);
        if (dojo.byId('upload-thumb').checked) {
            errors = xg.index.util.FormHelper.validateRequired(errors, form, 'thumb', this.nls.pleaseSpecifyAThumbnailSize);
            if (! errors['thumb']) {
                var s = dojo.string.trim(dojo.byId('upload-size').value);
                if (! s.match(/^\d+$/)) {
                    errors['size'] = this.nls.thumbnailSizeMustBeNumber;
                }
            }
        }
        return errors;
    },

    uploadOptionsDisable: function() {
        dojo.byId('upload-file').value = "";
        dojo.byId('upload-thumb').disabled = true;
        dojo.byId('upload-size').disabled = true;
        dojo.byId('upload-popup').disabled = true;
    },

    uploadOptionsEnable: function() {
        dojo.byId('existing-image').value = "http://";
        dojo.byId('upload-thumb').disabled = false;
        dojo.byId('upload-size').disabled = false;
        dojo.byId('upload-popup').disabled = false;
    },

    /**
     * Submits the image.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     */
    submitProcess: function(form, callback) {
        if ((dojo.byId('existing-image').value.length < 8) || (dojo.byId('upload-file').value.length > 0)) {
            // Clear any existing errors
            dojo.lang.forEach(dojo.html.getElementsByClass('error', form), function(validationElement) { dojo.html.removeClass(validationElement,'error'); });
            // Trim whitespace from form elements
            xg.index.util.FormHelper.trimTextInputsAndTextAreas(form);
            errors = this.uploadValidate(form);
            xg.index.util.FormHelper.hideErrorMessages(form);
            if (dojo.lang.isEmpty(errors)) {
                this.showSpinner();
                xg.index.util.FormHelper.save(form,dojo.lang.hitch(this, function(data) {
                       this.uploadInsert(data, callback);  }), form.action);
            } else {
                xg.index.util.FormHelper.showErrorMessages(form, errors, this.nls.pleaseCorrectErrors);
            }
        } else {
            var alignment = "left";
            var imageHtml = "";
            if (dojo.byId('right-align-radio').checked) {
                alignment = "right";
            }
            if (dojo.byId('text-wrap').checked) {
                imageHtml = "<img style='float:" + alignment + ";' src='"
                imageHtml += dojo.byId('existing-image').value + "' />";
            } else {
                imageHtml = "<p style='text-align:" + alignment + "'><img src='";
                imageHtml += dojo.byId('existing-image').value + "' /></p>";
            }

            dojo.html.hide('shared-upload-progress');
            dojo.html.hide('shared-upload-module-container');
            dojo.html.hide('shared-upload-module');
            xg.shared.util.hideOverlay();
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
            callback(imageHtml);
        }
    },
    /**
     * Asks the user to specify an image to upload.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     * @param noOptions suppresses options block
     */
    showForm: function(callback, noOptions) {
        // Prefix with "share-", as the Add Blog Post page has the same IDs.  [Jon Aquino 2007-08-16]
        // TODO: Make this a Dojo widget, so we can get rid of the IDs (including the "share-" IDs)  [Jon Aquino 2007-08-16]
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
        <div id="shared-upload-module" class="xg_floating_module"> \
            <div id="shared-upload-module-container" class="xg_floating_container xg_module"> \
                <div class="xg_module_head"> \
                    <h2 id="shared-upload-module-title">' + this.nls.addAnImage + '</h2> \
                </div> \
                <div id="shared-upload-module-body" class="xg_module_body"> \
                    <div id="upload-form-container"> \
                        <dl id="upload-form_notify"></dl> \
                        <form id="upload-form" method="post" enctype="multipart/form-data" action="/profiles/blog/upload/.txt?xn_out=json"> \
                            <input type="hidden" name="image" value="1"/> \
                            <fieldset class="nolegend"> \
                                <p> \
                                    <label for="upload-file">' + this.nls.uploadAnImage + '</label><br /> \
                                    <input id="upload-file" name="file" type="file" class="file wide" size="15" /> \
                                </p> \
                            </fieldset> \
                            <fieldset class="nolegend"> \
                                <p> \
                                    <label for="existing-image">' + this.nls.addExistingImage + '</label><br /> \
                                    <input id="existing-image" name="existing-image" type="text" class="textfield wide"  value="http://" /> \
                                </p> \
                            </fieldset> \
                            <fieldset> \
                                <legend class="toggle"'+(noOptions?' style="display:none"':'')+'> \
                                    <a id="upload-form-options-toggle" href="#" ><span id="upload-form-options-arrow"> \
                                    <!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span>' + this.nls.options + '</a> \
                                </legend> \
                                <div id="upload-form-options" style="display:none"> \
                                    <p> \
                                        <label><input name="wrap" id="text-wrap" type="checkbox" class="checkbox" value="yes" /> \
                                        <strong>' + this.nls.wrapTextAroundImage + '</strong></label><br /> \
                                        <label style="margin:10px 0 5px 20px; font-weight:lighter"><input checked="checked" name="align" \
                                        type="radio" class="radio" value="left" id="left-align-radio" />'+ this.nls.imageOnLeft + '</label><br /> \
                                        <label style="margin:5px 0 5px 20px; font-weight:lighter"><input name="align" type="radio" class="radio" \
                                         value="right" id="right-align-radio" />'+ this.nls.imageOnRight + '</label> \
                                    </p> \
                                    <p> \
                                        <label><input id="upload-thumb" name="thumb" type="checkbox" class="checkbox" \
                                         value="yes" /><strong>'+ this.nls.createThumbnail + '</strong></label><br /> \
                                        <label style="margin:10px 0 5px 20px; font-weight:lighter"><input id="upload-size" name="size" type="text" \
                                         class="textfield" size="4" value="300" /> '+ this.nls.pixels + '</label><br /> \
                                        <small style="margin:5px 0 5px 20px; font-weight:lighter; line-height:1.4em; \
                                        display:block;">'+ this.nls.createSmallerVersion + '</small> \
                                    </p> \
                                    <p> \
                                    <label><input name="popup" type="checkbox" class="checkbox" value="yes" id="upload-popup"/> \
                                    <strong>' + this.nls.popupWindow + '</strong></label><br /> \
                                        <small style="margin:0 0 5px 20px; font-weight:lighter; line-height:1.4em; \
                                        display:block;">' + this.nls.linkToFullSize + '</small> \
                                    </p> \
                                </div> \
                                <p class="buttongroup"> \
                                <input id="upload-submit" type="submit" class="button button-primary" value="'+this.nls.add+'" />&nbsp;\
								<input id="upload-cancel" type="button" class="button" value="'+this.nls.cancel+'" /> \
                                </p> \
                            </fieldset> \
                        </form> \
                    </div> \
                    <div id="shared-upload-progress" style="display:none"> \
                        <img class="left" width="20" height="20" style="margin-right: 5px;" alt="" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '"/> \
                        <p style="margin-left: 25px;">' + this.nls.keepWindowOpen + '</p> \
                        <p class="buttongroup"><input type="button" class="button" id="shared-upload-progress-button" value="' + this.nls.cancelUpload + '" /></p> \
                    </div> \
                    <div id="shared-upload-error" style="display: none"> \
                        <div class="errordesc"><p><big id="shared-upload-error-message"></big></p></div> \
                        <p><input id="shared-upload-error-ok" type="button" class="right" value="'+this.nls.ok+'" /></p> \
                    </div> \
                </div> \
            </div> \
        </div>'))[0];
        xg.shared.util.showOverlay();
        document.body.appendChild(dialog);
        dojo.byId('upload-form').appendChild(xg.shared.util.createCsrfTokenHiddenInput());
        dojo.event.connect(dojo.byId('upload-cancel'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.hideOverlay();
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
        }));
        dojo.event.connect(dojo.byId('upload-form'), 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var form = dojo.byId('upload-form');
            this.submitProcess(form, callback);
        }));
        // Attach other OK and cancel buttons
        dojo.event.connect(dojo.byId('shared-upload-error-ok'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.hideOverlay();
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
        }));
        dojo.event.connect(dojo.byId('shared-upload-progress-button'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.IframeUpload.stop();
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
            xg.shared.util.hideOverlay();
        }));

        // Attach to the image upload options toggle
        var uploadOptionsAreVisible = false;
        var showUploadOptions = function() {
            dojo.html.show('upload-form-options');
            dojo.byId('upload-form-options-arrow').innerHTML = '&#9660';
            uploadOptionsAreVisible = true;
        };
        var hideUploadOptions = function() {
            dojo.html.hide('upload-form-options');
            if (dojo.render.html.ie) {
                dojo.byId('upload-form-options-arrow').innerHTML = '&#9658;';
            } else {
                dojo.byId('upload-form-options-arrow').innerHTML = '&#9654;';
            }
            uploadOptionsAreVisible = false;
        };
        dojo.event.connect(dojo.byId('upload-form-options-toggle'),'onclick', function(evt) {
            dojo.event.browser.stopEvent(evt);
            uploadOptionsAreVisible ? hideUploadOptions() : showUploadOptions();
        });
        // toggle options for upload vs insert
        dojo.event.connect(dojo.byId('upload-file'),'onfocus', dojo.lang.hitch(this, function(event) {
            this.uploadOptionsEnable();
        }));
        dojo.event.connect(dojo.byId('existing-image'),'onfocus', dojo.lang.hitch(this, function(event) {
            this.uploadOptionsDisable();
        }));
    },

    // TODO: Eliminate this nls section by inlining each field. [Jon Aquino 2007-08-16]
    nls: {
        addAnImage: xg.shared.nls.html('addAnImage'),
        uploadAnImage: xg.shared.nls.html('uploadAnImage'),
        options: xg.shared.nls.html('options'),
        wrapTextAroundImage: xg.shared.nls.html('wrapTextAroundImage'),
        imageOnLeft: xg.shared.nls.html('imageOnLeft'),
        imageOnRight: xg.shared.nls.html('imageOnRight'),
        createThumbnail: xg.shared.nls.html('createThumbnail'),
        pixels: xg.shared.nls.html('pixels'),
        createSmallerVersion: xg.shared.nls.html('createSmallerVersion'),
        popupWindow: xg.shared.nls.html('popupWindow'),
        linkToFullSize: xg.shared.nls.html('linkToFullSize'),
        cancel: xg.shared.nls.html('cancel'),
        keepWindowOpen: xg.shared.nls.html('keepWindowOpen'),
        cancelUpload: xg.shared.nls.html('cancelUpload'),
        ok: xg.shared.nls.html('ok'),
        add: xg.shared.nls.html('add'),
        pleaseSelectAFile: xg.shared.nls.html('pleaseSelectAFile'),
        pleaseSpecifyAThumbnailSize: xg.shared.nls.html('pleaseSpecifyAThumbnailSize'),
        addExistingImage: xg.shared.nls.html('addExistingImage'),
        pleaseCorrectErrors: xg.shared.nls.html('pleaseCorrectErrors'),
        thumbnailSizeMustBeNumber: xg.shared.nls.html('thumbnailSizeMustBeNumber')
    }

};
