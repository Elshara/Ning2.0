dojo.provide('xg.forum.topic.show');

dojo.require('xg.shared.util');

/**
 * Behavior for the Show Topic page.
 */
xg.forum.topic.show = {

    /**
     * Prepares the comment_form_toggle links for use.
     */
    initializeCommentFormToggles: function() {
        // Performance optimization: Don't instantiate NewCommentForm widget until clicked on (BAZ-2560) [Jon Aquino 2007-05-08]
        dojo.lang.forEach(dojo.html.getElementsByClass('comment_form_toggle'), function(a) {
            if (a.getAttribute('comment_form_toggle_initialized') == 'Y') { return; }
            a.setAttribute('comment_form_toggle_initialized', 'Y');
            dojo.event.connect(a, 'onclick', function(event) {
                dojo.event.browser.stopEvent(event);
                var form = dojo.dom.getFirstAncestorByTag(a, 'form');
                if (form.getAttribute('dojoType')) { return; }
                form.setAttribute('dojoType', 'NewCommentForm');
                xg.shared.util.parseWidgets(form);
                dojo.widget.manager.getWidgetByNode(form).click();
            });
        });
    }

}

xg.forum.topic.show.initializeCommentFormToggles();

// TODO: Move this widget to ForumLinkToggle.js [Jon Aquino 2007-10-04]
dojo.provide('xg.forum.topic.ForumLinkToggle');

dojo.widget.defineWidget('xg.forum.topic.ForumLinkToggle', dojo.widget.HtmlWidget, {
    /** Id of the form that this toggle is connected to */
    _formId: '<required>',
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    /** Whether the current user is a pending member */
    _isPending: false,
    fillInTemplate: function(args, frag) {
        this.a = this.getFragNodeRef(frag);
        this.formContainer = dojo.byId(this._formId).parentNode;
        dojo.event.connect(this.a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.promptToJoin(this._joinPromptText, this._isPending, dojo.lang.hitch(this, function() {
                var span = this.a.getElementsByTagName('span')[0];
                if (this.formContainer.style.display != 'none') {
                    this.formContainer.style.display = 'none';
                    span.innerHTML = dojo.render.html.ie ? '&#9658;' : '&#9654;';
                } else {
                    this.formContainer.style.display = 'block';
                    span.innerHTML = '&#9660;';
                }
            }));
        }));
    }
});