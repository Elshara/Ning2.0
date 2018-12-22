dojo.provide('xg.page.page.NewCommentForm');
dojo.require('dojo.lfx.*');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * A form for adding posts and replies to a discussion.
 */
dojo.widget.defineWidget('xg.page.page.NewCommentForm', dojo.widget.HtmlWidget, {
    /** The maximum number of characters allowed for a comment. */
    _maxlength: '<required>',
    /** Whether the form is open or closed (rolled up) */
    _open: true,
    /** The error message to show if the person hasn't entered anything */
    _emptyDescriptionErrorMessage: '<required>',
    /** Whether to do a normal form submission instead of an AJAX or IFrame submission */
    _forceNormalFormSubmission: false,
    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.form = this.getFragNodeRef(frag);
        this.originallyOpen = this._open;
        this.legend = dojo.html.getElementsByClass('toggle', this.form, 'legend')[0];
        this.initFormToggling();
        dojo.style.show(this.form);
    },
    /**
     * Sets up the link that shows and hides the entire form.
     */
    initFormToggling: function() {
        this.setOpen(this._open);
        dojo.event.connect(this.legend.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.setOpen(! this._open);
            if (this._open) {
                xg.index.util.FormHelper.scrollIntoView(this.form);
                this.form.description.focus();
            }
        }));
    },
    /**
     * Shows or hides the form.
     *
     * @param boolean open  Whether to show or hide the form.
     */
    setOpen: function(open) {
        if (open && ! this.formInitialized) {
            // Lazily, to reduce page-load time  [Jon Aquino 2007-01-30]
            this.formInitialized = true;
            this.initUploadSectionToggling();
            this.initSubmitHandler();
        }
        this._open = open;
        var span = this.legend.getElementsByTagName('span')[0];
        dojo.style.show(span);
        span.innerHTML = this._open ? '&#9660;' : (dojo.render.html.ie ? '&#9658;' : '&#9654;');
        dojo.style.setShowing(dojo.html.getElementsByClass('form_body', this.form, 'div')[0], this._open);
    },
    /**
     * Sets up the link that shows and hides the upload section.
     */
    initUploadSectionToggling: function() {
        var a = dojo.html.getElementsByClass('upload_link', this.form, 'a')[0];
        this.uploadSection = dojo.dom.nextElement(a.parentNode);
        dojo.style.show(a.parentNode);
        dojo.style.hide(dojo.dom.nextElement(a.parentNode));
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.hide(a.parentNode);
            dojo.style.show(this.uploadSection);
            xg.index.util.FormHelper.scrollIntoView(this.form);
        }));
    },
    /**
     * Sets up the form to use normal submission, AJAX, or a hidden IFrame
     * as appropriate for the browser and the data.
     */
    initSubmitHandler: function() {
        dojo.dom.insertAtPosition(dojo.html.createNodesFromText('<dl class="errordesc msg" id="' + this.form.id + '_notify" style="display: none"></dl>')[0], this.form, 'first');
        this.submitting = false;
        dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.submitting) { return; }
            if (!xg.index.util.FormHelper.runValidation(this.form, dojo.lang.hitch(this, this.validate))) { return; }
            if (!xg.index.util.FormHelper.validateFileInputsSpeciallyForIE(this.form)) { return; }
            this.removeFileFieldsIfEmpty();
            this.submitting = true;
            if (!this._forceNormalFormSubmission && (!xg.index.util.FormHelper.hasFileFields(this.form) || xg.index.util.FormHelper.iframeTransportSupportsBrowser())) {
                this.submitFormAsynchronously();
            } else {
                this.form.submit();
            }
        }));
    },
    /**
     * Submits the form using AJAX or, if there are files to upload, the IFrameTransport.
     */
    submitFormAsynchronously: function() {
        var spinner = dojo.html.createNodesFromText('<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="spinner" />')[0];
        dojo.dom.insertAtPosition(spinner, dojo.html.getElementsByClass('buttongroup', this.form)[0], 'first');
		xg.shared.util.fixImagesInIE(spinner);
        xg.index.util.FormHelper.save(this.form, dojo.lang.hitch(this, function(data) {
            dojo.io.bind({
                // Retrieve the HTML in a separate request, as the IFrameTransport in IE has problems with returned HTML [Jon Aquino 2007-01-30]
                url: data.commentHtmlUrl,
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript',
                load: dojo.lang.hitch(this, function(type, data, event){
                    // HTML was escaped so IE6 wouldn't parse it (when we use the IFrameTransport). [Jon Aquino 2007-01-30]
                    var newCommentContainer = dojo.html.createNodesFromText(data.html)[0];
                    dojo.style.setOpacity(newCommentContainer, 0);
                    if (this.originallyOpen) {
                        dojo.style.show(dojo.byId('comments_heading'));
                        dojo.dom.insertAfter(newCommentContainer, dojo.dom.nextElement(dojo.byId('comments_heading')));
                    } else {
                        dojo.dom.insertAfter(newCommentContainer, commentContainer);
                    }
                    xg.shared.util.fixImagesInIE(newCommentContainer.getElementsByTagName('img'));
                    xg.shared.util.parseWidgets(newCommentContainer);
                    this.form.description.value = '';
                    this.replaceFileFields();
                    this.setOpen(this.originallyOpen);
                    xg.index.util.FormHelper.scrollIntoView(newCommentContainer);
                    dojo.lfx.fadeIn(newCommentContainer, 500, dojo.lfx.easeIn).play();
                    dojo.dom.removeNode(spinner);
                    this.submitting = false;
                })
            });
        }), this.form.action.replace('?', '/.txt?') + '&xn_out=json');
        // .txt to prevent IE6 from showing download dialog for IFrameTransport [Jon Aquino 2007-01-30]
    },
    /**
     * Removes the file fields if they are empty, so that dojo.io.bind will use AJAX instead of IFrameTransport.
     */
    removeFileFieldsIfEmpty: function() {
        if (dojo.string.trim(this.form.file1.value + this.form.file2.value + this.form.file3.value).length == 0) {
            dojo.dom.removeNode(this.form.file1);
            dojo.dom.removeNode(this.form.file2);
            dojo.dom.removeNode(this.form.file3);
            dojo.style.hide(this.uploadSection);
        }
    },
    /**
     * Resets the file fields, whether they were removed by removeFileFieldsIfEmpty(),
     * or populated during the form submission.
     */
     replaceFileFields: function () {
         var listItems = this.uploadSection.getElementsByTagName('li');
         listItems[0].innerHTML = '<input type="file" class="file" name="file1" />';
         listItems[1].innerHTML = '<input type="file" class="file" name="file2" />';
         listItems[2].innerHTML = '<input type="file" class="file" name="file3" />';
         dojo.style.setShowing(this.uploadSection, ! dojo.style.isShowing(dojo.html.getElementsByClass('upload_link', this.form, 'a')[0].parentNode));
     },
    /**
     * Checks the input for errors.
     *
     * @return object  A map of field name => HTML error message
     */
    validate: function() {
        var errors = {};
        if (dojo.string.trim(this.form.description.value).length > this._maxlength) {
            errors.description = xg.page.nls.html('numberOfCharactersExceedsMaximum', dojo.string.trim(this.form.description.value).length, this._maxlength);
        }
        if (dojo.string.trim(this.form.description.value).length == 0) {
            errors.description = dojo.string.escape('html', this._emptyDescriptionErrorMessage);
        }
        return errors;
    }
});
