dojo.provide('xg.groups.group.DeclineInvitationLink');

dojo.require('dojo.lfx.html');

/**
 * A link that the user can use to decline an invitation to a group
 */
dojo.widget.defineWidget('xg.groups.group.DeclineInvitationLink', dojo.widget.HtmlWidget, {

    /** Endpoint for declining the invitation */
    _url: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.style.show(a);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.io.bind({ url: this._url, method: 'post', preventCache: true, mimetype: 'text/javascript', encoding: 'utf-8' });
            var module = dojo.dom.getAncestors(a, function(node) { return dojo.html.hasClass(node, 'xg_module'); }, true);
            var nodeToRemove = module.getElementsByTagName('dl').length == 1 ? module : dojo.dom.getAncestorsByTag(a, 'dl', true);
            dojo.lfx.html.fadeOut(nodeToRemove, 500, null, dojo.lang.hitch(this, function() {
                dojo.dom.removeNode(nodeToRemove);
            })).play();
        }));
    }

});

