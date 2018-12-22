dojo.provide('xg.shared.MoreLink');

/**
 * A link that reveals more links.
 */
dojo.widget.defineWidget('xg.shared.MoreLink', dojo.widget.HtmlWidget, {

    /**
     * Initializes this widget.
     */
    fillInTemplate: function(args, frag) {
        var link = this.getFragNodeRef(frag);
        dojo.event.connect(link, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.show(dojo.dom.nextElement(link));
            dojo.style.hide(link);
        }));
    }

});
