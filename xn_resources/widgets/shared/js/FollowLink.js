dojo.provide('xg.shared.FollowLink');

dojo.require('xg.shared.util');
dojo.require("dojo.lfx.html");

/**
 * A link for subscribing to / unsubscribing from email notifications.
 */
dojo.widget.defineWidget('xg.shared.FollowLink', dojo.widget.HtmlWidget, {
    _addUrl: "",
    _removeUrl: "",
    /** Link text for the subscribe action */
    _addLinkText: "",
    /** Link text for the unsubscribe action */
    _removeLinkText: "",
    /** Brief description of the subscribe action */
    _addDescription: "",
    /** Brief description of the unsubscribe action */
    _removeDescription: "",
    _isFollowed: "",
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    /** The url for the signup page; used with _joinPromptText above */
    _signUpUrl: '',
    /** Whether the current user is a pending member */
    _isPending: false,

    fillInTemplate: function(args, frag) {
        // Set _addLinkText and _removeLinkText here rather than above,
        // to ensure that ning.loader has loaded custom translations (BAZ-5598) [Jon Aquino 2008-01-10]
        this._addLinkText = this._addLinkText ? this._addLinkText : xg.shared.nls.text('follow');
        this._removeLinkText = this._removeLinkText ? this._removeLinkText : xg.shared.nls.text('stopFollowing');
        this.a = this.getFragNodeRef(frag);
        dojo.dom.insertAfter(document.createElement('span'), this.a);
        this.updateText(this.a);
        dojo.event.connect(this.a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this._joinPromptText.length || this._isPending) {
                xg.shared.util.promptToJoin(this._joinPromptText, this._isPending, dojo.lang.hitch(this, function() {
                    window.location = this._signUpUrl;
                }));
            } else {
                this.a.className = "desc working disabled";
                if (this.posting) { return; }
                this.post();
            }
        }));
    },

    post: function() {
        this.posting = true;
        if (this._isFollowed == 0) {
            url = this._addUrl;
        } else {
            url = this._removeUrl;
        }
        dojo.io.bind({
            url: url,
            method: 'post',
            encoding: 'utf-8',
            preventCache: true,
            load: dojo.lang.hitch(this, function(type, data, event){
                this._isFollowed = this._isFollowed == 0 ? 1 : 0;
                this.updateText(this.a);
				dojo.lfx.html.highlight(this.a, '#ffee7d', 1000).play();
				this.posting = false;
            })
        });
    },

    /**
     * Updates the text and CSS classes.
     */
    updateText: function() {
        if (this._isFollowed == 0) {
            this.a.className = "desc follow-add";
            this.a.innerHTML = dojo.string.escape('html', this._addLinkText);
            if (this._addDescription.length) {
                dojo.dom.nextElement(this.a, 'span').innerHTML = ' &ndash; ' + dojo.string.escape('html', this._addDescription);  
            }
        } else {
            this.a.className = "desc follow-remove";
            this.a.innerHTML = dojo.string.escape('html', this._removeLinkText);
            if (this._removeDescription.length) {
                dojo.dom.nextElement(this.a, 'span').innerHTML = ' &ndash; ' + dojo.string.escape('html', this._removeDescription);
            }
        }
    },

    /**
     * Update the display to show following.  Use if state changes externally.
     */
    showFollowing: function() {
        this._isFollowed = 1;
        this.updateText();
    },

    /**
     * Update the display to show not following.  Use if state changes externally.
     */
    showNotFollowing: function() {
        this._isFollowed = 0;
        this.updateText();
    }
});
