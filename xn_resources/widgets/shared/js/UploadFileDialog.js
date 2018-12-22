dojo.provide('xg.shared.UploadFileDialog');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.IframeUpload');

/**
 * A dialog box that prompts the user to specify a file to upload.
 */
xg.shared.UploadFileDialog = {

    /**
     * Asks the user to specify a file to upload.
     *
     * @param callback  function that takes one parameter: html of the resulting anchor for the file, or an empty string if the user cancels
     */
    promptForFile: function(callback) {
        if (dojo.render.html.safari) {
            this.safariFile(callback);
        } else {
            this.showForm(callback);
        }
    },

    /**
     * Asks the user to specify a file to upload, using a simplified prompt that works in Safari.
     *
     * @param callback  function that takes one parameter: html of the resulting anchor for the file, or an empty string if the user cancels
     */
    safariFile: function(callback) {
        var file = prompt(xg.shared.nls.html('pleaseEnterAFileAddress'), "http://");
        var tag;
        if (file != null) {
            tag = this.createLinkedFilename(file);
        } else {
            tag = '';
        }
        callback(tag);
    },

    createLinkedFilename: function(file) {
        var fileNameArray = file.split('/');
        var fileName = decodeURIComponent(fileNameArray[fileNameArray.length -1]);
        return "<a href=\"" + file + "\">" + fileName + "</a>";
    },

    showSpinner: function() {
        dojo.html.hide('upload-form-container');
        dojo.html.show('shared-upload-progress');
    },
    /**
     * Process the response from the image upload.
     *
     * @param callback  function that takes one parameter: html of the resulting anchor for the file, or an empty string if the user cancels
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
        errors = xg.index.util.FormHelper.validateRequired(errors, form, 'file', xg.shared.nls.html('pleaseSelectAFile'));
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
     * @param callback  function that takes one parameter: html of the resulting anchor for the file, or an empty string if the user cancels
     */
    submitProcess: function(form, callback) {
        if ((dojo.byId('existing-file').value.length < 8) || (dojo.byId('upload-file').value.length > 0)) {
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
                xg.index.util.FormHelper.showErrorMessages(form, errors, xg.shared.nls.html('pleaseCorrectErrors'));
            }
        } else {
            var fileHtml = this.createLinkedFilename(dojo.byId('existing-file').value);
            dojo.html.hide('shared-upload-progress');
            dojo.html.hide('shared-upload-module-container');
            dojo.html.hide('shared-upload-module');
            xg.shared.util.hideOverlay();
            dojo.dom.removeNode(dojo.byId('shared-upload-module'));
            callback(fileHtml);
        }
    },
    /**
     * Asks the user to specify an image to upload.
     *
     * @param callback  function that takes one parameter: html of the resulting <img> tag, or an empty string if the user cancels
     */
    showForm: function(callback) {
        // Prefix with "share-", as the Add Blog Post page has the same IDs.  [Jon Aquino 2007-08-16]
        // TODO: Make this a Dojo widget, so we can get rid of the IDs (including the "share-" IDs)  [Jon Aquino 2007-08-16]
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
        <div id="shared-upload-module" class="xg_floating_module"> \
            <div id="shared-upload-module-container" class="xg_floating_container xg_module"> \
                <div class="xg_module_head"> \
                    <h2 id="shared-upload-module-title">' + xg.shared.nls.html('uploadAFile') + '</h2> \
                </div> \
                <div id="shared-upload-module-body" class="xg_module_body"> \
                    <div id="upload-form-container"> \
                        <dl id="upload-form_notify"></dl> \
                        <form id="upload-form" method="post" enctype="multipart/form-data" action="/profiles/blog/upload/.txt?xn_out=json"> \
                            <input type="hidden" name="image" value="0"/> \
                            <fieldset class="nolegend"> \
                                <p> \
                                    <label for="upload-file">' + xg.shared.nls.html('uploadAFile') + '</label><br /> \
                                    <input id="upload-file" name="file" type="file" class="file" /> \
                                </p> \
                            </fieldset> \
                            <fieldset class="nolegend"> \
                                <p> \
                                    <label for="existing-file">' + xg.shared.nls.html('addExistingFile') + '</label><br /> \
                                    <input id="existing-file" name="existing-file" type="text" class="textfield wide" value="http://" /> \
                                </p> \
                            </fieldset> \
                            <fieldset> \
                                <p class="buttongroup"> \
								<input id="upload-submit" type="submit" class="button button-primary" value="'+xg.shared.nls.html('add')+'" /> \
								<input id="upload-cancel" type="button" class="button" value="'+xg.shared.nls.html('cancel')+'"/> \
                                </p> \
                            </fieldset> \
                        </form> \
                    </div> \
                    <div id="shared-upload-progress" style="display:none"> \
                        <img class="left" width="20" height="20" style="margin-right: 5px;" alt="" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '"/> \
                        <p style="margin-left: 25px;">' + xg.shared.nls.html('keepWindowOpen') + '</p> \
                        <p class="buttongroup"><input type="button" class="button" id="shared-upload-progress-button" value="' + xg.shared.nls.html('cancelUpload') + '" /></p> \
                    </div> \
                    <div id="shared-upload-error" style="display: none"> \
                        <div class="errordesc"><p><big id="shared-upload-error-message"></big></p></div> \
                        <p><input id="shared-upload-error-ok" type="button" class="right" value="'+xg.shared.nls.html('ok')+'" /></p> \
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
    }
};
