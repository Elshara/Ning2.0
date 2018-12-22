dojo.provide('xg.music.track.MetadataToggleLink');

/**
 * An <a> element that shows and hides the input fields for a track's metadata.
 */
dojo.widget.defineWidget('xg.music.track.MetadataToggleLink', dojo.widget.HtmlWidget, {

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        var open = false;
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            open = ! open;
            dojo.style.setShowing(dojo.dom.nextElement(a.parentNode), open);
            a.getElementsByTagName('span')[0].innerHTML = open ? '&#9660;' : (dojo.render.html.ie ? '&#9658;' : '&#9654;');
        }));
        dojo.style.setVisibility(a, true);
    }

});
