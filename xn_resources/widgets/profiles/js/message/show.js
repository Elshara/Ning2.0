dojo.provide('xg.profiles.message.show');

/**
 * Behavior for the mailbox page
 */
xg.profiles.message.show = {

    // recipient more <span>
    recipientMore: null,
    // recipient rest <span>
    recipientRest: null,
    // message body quote attach point
    quoteAttach: null,

    // link to expand to see the full recipient list
    expandLink: null,
    // link to show more message
    showMoreLink: null,
    // show more message spinner
    spinner: null,

    /**
     * Sets up this object. May take a few seconds, so delay it as long as possible.
     */
    setup: function() {
        this.recipientMore = dojo.byId('xj_recipients_more');
        this.recipientRest = dojo.byId('xj_recipients_rest');
        this.expandLink = dojo.byId('xj_expand_recipients');
        this.showMoreLink = dojo.byId('xj_show_more');
        this.quoteAttach = dojo.byId('xj_quote_attach');
        this.spinner = dojo.byId('xj_spinner');

        // initialize only if all objects exist in the dom
        if (this.recipientMore && this.recipientRest && this.expandLink) {
            // connect expandLink onclick action
            dojo.event.connect(this.expandLink, 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                this.expandRecipientList();
            }));
        }

        // initialize 'show more' message body link action
        if (this.showMoreLink && this.quoteAttach) {
            dojo.event.connect(this.showMoreLink, 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                this.showRestOfMessageBody();
            }));
        } else if (this.showMoreLink) {
            // hide the show more link if the attach point does not exist
            this.showMoreLink.style.display = 'none';
        }
    },

    /**
     * Expand truncated recipient list on-demand
     */
    expandRecipientList: function() {
        this.recipientMore.style.display = 'none';
        this.recipientRest.style.display = '';
    },

    /**
     * Get the rest of the message body and inject it at the attach point
     */
    showRestOfMessageBody: function() {
        // hide the show more link
        this.showMoreLink.style.display = 'none';
        if (this.spinner) { this.spinner.style.visibility = ''; }
        dojo.io.bind({
            url: this.showMoreLink.getAttribute('_showMoreUrl'),
            method: 'post',
            content: { },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                // hide spinner
                if (this.spinner) { this.spinner.style.visibility = 'hidden'; }
                if ('restOfMessageBody' in data) {
                    this.quoteAttach.innerHTML = this.quoteAttach.innerHTML + data.restOfMessageBody;
                } else {
                    // an error occurred - do nothing
                }
            })
        });
    }

};

xg.profiles.message.show.setup();