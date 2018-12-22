dojo.provide('xg.forum.topic.DeleteCommentLink');

dojo.require('dojo.lfx.html');
dojo.require('xg.shared.util');
dojo.require('xg.forum.topic.NewCommentForm');

/**
 * An <a> element that deletes a reply to a discussion
 */
dojo.widget.defineWidget('xg.forum.topic.DeleteCommentLink', dojo.widget.HtmlWidget, {

    /** Endpoint that deletes the comment */
    _deleteCommentUrl: '',

    /** Endpoint that deletes the comment and its subcomments */
    _deleteCommentAndSubCommentsUrl: '',

    /** Whether the comment has child comments */
    _hasChildComments: false,

    /** Whether the current user is allowed to delete the comment and its child comments */
    _currentUserCanDeleteCommentAndSubComments: false,

    /** The ID of the Comment object */
    _commentId: '',

    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',

    /** The <a> element */
    a: null,

    /** Whether the deletion is in progress */
    deleting: false,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.a = this.getFragNodeRef(frag);
        dojo.event.connect(this.a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.promptToJoin(this._joinPromptText, dojo.lang.hitch(this, function() {
                if (this.deleting) { return; }
                if (this._hasChildComments && this._currentUserCanDeleteCommentAndSubComments) {
                    this.deleteCommentAndSubComments();
                } else {
                    this.deleteComment();
                }
            }));
        }));
        // Detect Ajax-based addition of child comments [Jon Aquino 2007-04-04]
        xg.forum.topic.NewCommentForm.addCommentCreatedListener(dojo.lang.hitch(this, function(url) {
            if (url.match('comment.*create.*parentCommentId=' + encodeURIComponent(this._commentId))) {
                this._hasChildComments = true;
            }
        }));
    },

    /**
     * Prompts the user to confirm the deletion of the comment and subcomments, then deletes them.
     */
    deleteCommentAndSubComments: function() {
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
                <div class="xg_floating_module"> \
                    <div class="xg_floating_container xg_module"> \
                        <div class="xg_module_head"> \
                            <h2>' + xg.forum.nls.html('deleteReply') + '</h2> \
                        </div> \
                        <div class="xg_module_body"> \
                            <p>' + xg.forum.nls.html('doYouWantToRemoveReplies') + '</p> \
                            <form> \
                                <p class="buttongroup"> \
                                    <input type="submit" class="button" value="' + xg.forum.nls.html('yes') + '" /> \
                                    <input type="button" class="button" value="' + xg.forum.nls.html('no') + '" /> \
                                    <input type="button" class="button" value="' + xg.forum.nls.html('cancel') + '" /> \
                                </p> \
                            </form> \
                        </div> \
                    </div> \
                </div>'))[0];
        xg.shared.util.showOverlay();
        document.body.appendChild(dialog);
        dojo.event.connect(dialog.getElementsByTagName('form')[0], 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            this.deleteCommentAndSubCommentsProper();
        }));
        dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[1], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            xg.shared.util.hideOverlay();
            this.deleteCommentProper();
        }));
        dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[2], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            xg.shared.util.hideOverlay();
        }));
    },

    /**
     * Prompts the user to confirm the deletion of the comment, then deletes it and removes it from the page.
     */
    deleteComment: function() {
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
                <div class="xg_floating_module"> \
                    <div class="xg_floating_container"> \
                        <div class="xg_module_head"> \
                            <h2>' + xg.forum.nls.html('deleteReply') + '</h2> \
                        </div> \
                        <div class="xg_module_body"> \
                            <p>' + xg.forum.nls.html('deleteReplyQ') + '</p> \
                            <form> \
                                <p class="buttongroup"> \
                                    <input type="submit" class="button button-primary" value="' + xg.forum.nls.html('ok') + '" /> \
                                    <input type="button" class="button" value="' + xg.forum.nls.html('cancel') + '" /> \
                                </p> \
                            </form> \
                        </div> \
                    </div> \
                </div>'))[0];
        xg.shared.util.showOverlay();
        document.body.appendChild(dialog);
        dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[1], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            xg.shared.util.hideOverlay();
        }));
        dojo.event.connect(dialog.getElementsByTagName('form')[0], 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            xg.shared.util.hideOverlay();
            this.deleteCommentProper();
        }));
    },

    /**
     * Deletes the comment, then removes it from the page.
     */
    deleteCommentProper: function() {
        this.deleting = true;
        var spinner = dojo.html.createNodesFromText('<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="spinner" />')[0];
        dojo.dom.insertAfter(spinner, this.a);
        dojo.io.bind({
            url: this._deleteCommentUrl,
            method: 'post',
            encoding: 'utf-8',
            preventCache: true,
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                dojo.dom.removeNode(spinner);
                if (! ('html' in data)) {
                    // An error occurred [Jon Aquino 2007-04-03]
                    return;
                }
                var div = dojo.dom.getFirstAncestorByTag(this.a, 'dl');
                if (data.html) {
                    var newDiv = dojo.html.createNodesFromText(data.html)[0];
                    dojo.style.setOpacity(newDiv, 0);
                    div.parentNode.replaceChild(newDiv, div);
                    xg.shared.util.parseWidgets(newDiv);
                    dojo.lfx.html.fadeIn(newDiv, 500).play();
                } else {
                    dojo.lfx.html.fadeOut(div, 500, null, dojo.lang.hitch(this, function() {
                        dojo.dom.removeNode(div);
                    })).play();
                }
            })
        });
    },

    /**
     * Deletes the comment and its subcomments, then refreshes the page
     */
    deleteCommentAndSubCommentsProper: function() {
        this.deleting = true;
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
                <div class="xg_floating_module"> \
                    <div class="xg_floating_container"> \
                        <div class="xg_module_head"> \
                            <h2>' + xg.forum.nls.html('deletingReplies') + '</h2> \
                        </div> \
                        <div class="xg_module_body"> \
                            <img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="left" style="margin-right:5px" width="20" height="20"/> \
                            <p style="margin-left:25px">' + xg.forum.nls.html('pleaseKeepWindowOpen') + '</p> \
                        </div> \
                    </div> \
                </div>'))[0];
        document.body.appendChild(dialog);
        // Can't refer to a local function from inside the function, so put it in an array [Jon Aquino 2007-04-04]
        var f = [];
        f.push(dojo.lang.hitch(this, function(counter) {
            dojo.io.bind({
                    url: this._deleteCommentAndSubCommentsUrl,
                    method: 'post',
                    content: { 'counter' : counter },
                    mimetype: 'text/json',
                    load: dojo.lang.hitch(this, function(t,data,e) {
                        if (! ('contentRemaining' in data)) { throw 'contentRemaining not present in response'; }
                        if (data.contentRemaining > 0) {
                            f[0](counter+1);
                        } else {
                            window.location.reload(true);
                        }
                    })
            });
        }));
        f[0](0);
    }

});
