dojo.provide('xg.shared.PostLink');

dojo.require('xg.shared.util');

/**
 * Makes an <a> do a post submission. Useful for delete links, which if implemented
 * as a get would be at risk of inadvertent execution by search robots.
 * The server should throw an Exception if $_SERVER['REQUEST_METHOD'] != 'POST'.
 */
dojo.widget.defineWidget('xg.shared.PostLink', dojo.widget.HtmlWidget, {

    /** The URL to post to */
    _url: '<required>',

    /** Text for the confirmation prompt; leave unset to skip the prompt. */
    _confirmQuestion: '',

    /** Title for the confirmation prompt */
    _confirmTitle: '',

    /** OK-button text for the confirmation prompt */
    _confirmOkButtonText: xg.index.nls.text('ok'),

    /** Reload the current page? */
    _reload: false,

    /** Whether the POST is in progress */
    posting: false,

    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',

    /** Whether the current user is a pending member */
    _isPending: false,

	/** Whether to check that user is joined */
	_doPromptJoin: 1,

    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.style.show(a);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.posting) { return; }
			var f = dojo.lang.hitch(this, function() {
                if (! this._confirmQuestion) {
                    this.post();
                } else {
                    xg.shared.util.confirm({
                        title: this._confirmTitle,
                        bodyHtml: '<p>' + dojo.string.escape('html', this._confirmQuestion) + '</p>',
                        onOk: dojo.lang.hitch(this, this.post),
                        okButtonText: this._confirmOkButtonText
                    });
                }
            });
			this._doPromptJoin ? xg.shared.util.promptToJoin(this._joinPromptText, this._isPending, f) : f();
        }));
    },
    /**
     * Executes the POST operation
     */
    post: function() {
        this.posting = true;
        if (this._reload != false) {
            dojo.io.bind({
                url: this._url,
                method: 'post',
                encoding: 'utf-8',
                load: function(type, data, evt) {
                  window.location.reload(true);
                }
            });
        } else {
            var form = dojo.html.createNodesFromText('<form method="post"></form>')[0];
            form.action = this._url;
            form.appendChild(xg.shared.util.createCsrfTokenHiddenInput());
            document.body.appendChild(form);
            form.submit();
        }
    }
});
