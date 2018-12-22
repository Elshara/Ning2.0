dojo.provide('xg.shared.PromptToJoinLink');

dojo.require('xg.shared.util');

/**
 * Makes an <a> prompt the user to join the network or current group before proceeding.
 */
dojo.widget.defineWidget('xg.shared.PromptToJoinLink', dojo.widget.HtmlWidget, {
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    /** Whether the current user is a pending member */
    _isPending: false,
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.promptToJoin(this._joinPromptText, this._isPending, function() {
                window.location = a.href;
            });
        }));
    }
});


