dojo.provide('xg.forum.topic.NewCommentForm');

dojo.require('dojo.lfx.*');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');
dojo.require('xg.shared.SimpleToolbar');
dojo.require('xg.forum.topic.show');

/**
 * A form for adding posts and replies to a discussion.
 */
dojo.widget.defineWidget('xg.forum.topic.NewCommentForm', dojo.widget.HtmlWidget, {
    /** The maximum number of characters allowed for a comment. */
    _maxlength: '<required>',
    /** Whether the form is open or closed (rolled up) */
    _open: true,
    /** The error message to show if the person hasn't entered anything */
    _emptyDescriptionErrorMessage: '<required>',
    /** Whether to do a normal form submission instead of an AJAX or IFrame submission */
    _forceNormalFormSubmission: false,
    /** Whether this form is on the first page */
    _firstPage: false,
    /** Whether this form is on the last page */
    _lastPage: false,
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    /** Whether to close the form after submitting a comment. */
    _autoClose: false,
    /** The <a> for opening and closing the form. */
    toggleLink: null,
    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.form = this.getFragNodeRef(frag);
        this.toggleLink = dojo.html.getElementsByClass('comment_form_toggle', this.form, 'a')[0];
        this.initFormToggling();
        dojo.style.setVisibility(this.form, true);
    },
    /**
     * Sets up the link that shows and hides the entire form.
     */
    initFormToggling: function() {
        this.setOpen(this._open);
        dojo.event.connect(this.toggleLink, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.click();
        }));
    },
    /**
     * Handles or simulates a click on the Reply to This line.
     */
    click: function() {
        xg.shared.util.promptToJoin(this._joinPromptText, dojo.lang.hitch(this, function() {
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
            // simple editor toolbar
            var taID = this.form.id.split("_");
            taID = "textarea_" + taID[2];
            var toolbar = dojo.widget.createWidget("SimpleToolbar", {_id: taID, _supressFileUpload:true});
        }
        this._open = open;
        var span = this.toggleLink.getElementsByTagName('span')[0];
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
        dojo.style.setVisibility(a.parentNode, true);
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
            if (data.commentsClosed) {
                dojo.style.show(dojo.byId('discussion_closed_module'));
                window.scrollTo(0, 0);
                dojo.dom.removeNode(spinner);
                return;
            }
            xg.forum.topic.NewCommentForm.fireCommentCreated(this.form.action);
            dojo.io.bind({
                // Retrieve the HTML in a separate request, as the IFrameTransport in IE has problems with returned HTML [Jon Aquino 2007-01-30]
                url: data.commentHtmlUrl,
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript',
                load: dojo.lang.hitch(this, function(type, data, event){
                    dojo.style.show(dojo.byId('discussionReplies'));
                    if (dojo.byId('upper_follow_link_container')) { dojo.style.hide(dojo.byId('upper_follow_link_container')); }
                    var commentContainer = dojo.dom.getAncestors(this.form, function(node) { return dojo.html.hasClass(node, 'discussion'); }, true);
                    // HTML was escaped so IE6 wouldn't parse it (when we use the IFrameTransport). [Jon Aquino 2007-01-30]
                    var newCommentContainer = dojo.html.createNodesFromText(data.html)[0];
                    dojo.style.setOpacity(newCommentContainer, 0);
                    if (data.positionOfNewComment == 'topOfPage') { this.insertAtTopOfPage(newCommentContainer); }
                    else if (data.positionOfNewComment == 'bottomOfPage') { this.insertAtBottomOfPage(newCommentContainer); }
                    else if (data.positionOfNewComment == 'firstChild') { this.insertAsFirstChild(newCommentContainer, commentContainer); }
                    else if (data.positionOfNewComment == 'lastChild') { this.insertAsLastChild(newCommentContainer, commentContainer); }
                    else { throw new Error('Shouldn\'t get here'); }
                    xg.shared.util.fixImagesInIE(newCommentContainer.getElementsByTagName('img'));
                    xg.shared.util.parseWidgets(newCommentContainer);
                    xg.forum.topic.show.initializeCommentFormToggles();
                    this.form.description.value = '';
                    this.replaceFileFields();
                    if (this._autoClose == true) {
                        this.setOpen(false);
                    }
                    xg.index.util.FormHelper.scrollIntoView(newCommentContainer);
                    dojo.lfx.fadeIn(newCommentContainer, 500, dojo.lfx.easeIn).play();
                    dojo.dom.removeNode(spinner);
                    this.submitting = false;
                })
            });
            if (data.userIsNowFollowing == 1) {
                dojo.lang.forEach(dojo.widget.manager.getWidgetsByType('FollowLink'), function (w) {
                    w.showFollowing();
                });
            }
        }), this.form.action.replace('?', '/.txt?') + '&xn_out=json&firstPage=' + (this._firstPage ? 1 : 0) + '&lastPage=' + (this._lastPage ? 1 : 0));
        // .txt to prevent IE6 from showing download dialog for IFrameTransport [Jon Aquino 2007-01-30]
    },
    /**
     * Inserts the given comment element at the top of the comment section.
     *
     * @param HTMLDivElement commentNode  The comment div
     */
    insertAtTopOfPage: function(commentNode) {
        dojo.dom.insertAfter(commentNode, dojo.byId('comments'));
    },
    /**
     * Inserts the given comment element at the bottom of the comment section.
     *
     * @param HTMLDivElement commentNode  The comment div
     */
    insertAtBottomOfPage: function(commentNode) {
        var children = this.allComments();
        if (children.length == 0) { this.insertAtTopOfPage(commentNode); }
        else { dojo.dom.insertAfter(commentNode, children.pop()); }
    },
    /**
     * Inserts the given comment as the first child of the given parent comment.
     *
     * @param HTMLDivElement commentNode  The comment div
     * @param HTMLDivElement parentCommentNode  The parent comment div
     */
    insertAsFirstChild: function(commentNode, parentCommentNode) {
        dojo.dom.insertAfter(commentNode, parentCommentNode);
    },
    /**
     * Inserts the given comment as the last child of the given parent comment.
     *
     * @param HTMLDivElement commentNode  The comment div
     * @param HTMLDivElement parentCommentNode  The parent comment div
     */
    insertAsLastChild: function(commentNode, parentCommentNode) {
        var children = this.childComments(parentCommentNode);
        if (children.length == 0) { this.insertAsFirstChild(commentNode, parentCommentNode); }
        else { dojo.dom.insertAfter(commentNode, children.pop()); }
    },
    /**
     * Returns all comment elements in the page.
     *
     * @return array  The HTMLDivElements of the comments
     */
    allComments: function() {
        return dojo.html.getElementsByClass('discussion', dojo.byId('discussionReplies')).slice(1);
    },
    /**
     * Returns the divs of the child comments of the given comment.
     *
     * @param HTMLDivElement parentCommentNode  The parent comment div
     * @return array  The HTMLDivElements of the comments
     */
    childComments: function(parentCommentNode) {
        var node = parentCommentNode;
        var childComments = [];
        while (node = dojo.dom.nextElement(node, 'div')) {
            if (this.indentLevel(node) <= this.indentLevel(parentCommentNode)) { break; }
            childComments.push(node);
        }
        return childComments;
    },
    /**
     * Returns the indentation level of the given comment.
     *
     * @return integer  0, 1, or 2
     */
    indentLevel: function(commentNode) {
        return parseInt(commentNode.className.match(/\bi(\d+)\b/)[1], 10)
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
            errors.description = xg.forum.nls.html('numberOfCharactersExceedsMaximum', dojo.string.trim(this.form.description.value).length, this._maxlength);
        }
        if (dojo.string.trim(this.form.description.value).length == 0) {
            errors.description = dojo.string.escape('html', this._emptyDescriptionErrorMessage);
        }
        return errors;
    }
});

// TODO: Replace the following code with xg.shared.topic.publish()/subscribe() [Jon Aquino 2008-09-11]
(function() {
    /** Callback functions to call after a comment is created via Ajax. */
    var commentCreatedListeners = [];

    /**
     * Registers a function to call after a comment is created via Ajax.
     *
     * @param callback  the callback function, which will be passed the Ajax URL
     */
    xg.forum.topic.NewCommentForm.addCommentCreatedListener = function(callback) {
        commentCreatedListeners.push(callback);
    }

    /**
     * Notifies the listeners that a comment has been created via Ajax.
     *
     * @param url  the Ajax URL to pass to the listeners.
     */
    xg.forum.topic.NewCommentForm.fireCommentCreated = function(url) {
        dojo.lang.forEach(commentCreatedListeners, function(listener) {
            listener(url);
        });
    }
})();



