dojo.provide('xg.profiles.friendrequest.AbstractFriendRequest');

/**
 * Controls for a friend request.
 */
dojo.widget.defineWidget('xg.profiles.friendrequest.AbstractFriendRequest', dojo.widget.HtmlWidget, {

    /** Whether this widget contains other widgets. Tells the Dojo widget parser to parse the child elements. */
    isContainer: true,

    /** Whether an Ajax request is in progress. */
    processing: false,

    /** The form node */
    form: null,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.form = this.getFragNodeRef(frag);
        this.initializeClickHandlers();
    },

    /**
     * Sets up the click handlers.
     */
    initializeClickHandlers: function() {
        // Subclasses should override this method. [Jon Aquino 2008-06-11]
    },

    /**
     * Adds an onclick handler to a button.
     *
     * @param name  the name of the button
     * @param onSuccess  callback after a successful Ajax call
     * @param onFailure  (optional) callback after a failed Ajax call or an Ajax call that returns success = false
     */
    handleClick: function(args) {
        if (! args.onFailure) { args.onFailure = function() {}; }
        dojo.event.connect(this.form[args.name], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.processing) { return; }
            this.processing = true;
            var div = dojo.dom.getAncestors(this.form, function(node) { return dojo.html.hasClass(node, 'request'); }, true);
            var content = {};
            content[args.name] = 1;
            xg.post(this.form.action + '&xn_out=json', content, dojo.lang.hitch(this, function(http, data) {
                if (data.success) { args.onSuccess(div, data); }
                else { args.onFailure(div, data); }
                this.processing = false;
            }));
        }));
    }

});
