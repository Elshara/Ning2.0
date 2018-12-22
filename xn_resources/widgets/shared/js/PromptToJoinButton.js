dojo.provide('xg.shared.PromptToJoinButton');

dojo.require('xg.shared.util');

/**
 * Makes a <button> prompt the user to join the network or current group before proceeding.
 */
dojo.widget.defineWidget('xg.shared.PromptToJoinButton', dojo.widget.HtmlWidget, {
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    /** URL of the page that the button should load */
    _url: '',
    /** Whether the current user is a pending member */
    _isPending: false,

    fillInTemplate: function(args, frag) {
        var button = this.getFragNodeRef(frag);
        dojo.event.connect(button, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.promptToJoin(this._joinPromptText, this._isPending, dojo.lang.hitch(this, function() {
                window.location = this._url;
            }));
        }));
    }
});


