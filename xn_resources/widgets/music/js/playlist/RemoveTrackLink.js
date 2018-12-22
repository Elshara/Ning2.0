dojo.provide('xg.music.playlist.RemoveTrackLink');

dojo.require('xg.music.playlist.edit');

/**
 * An <a> element that makes an Ajax request to remove a track.
 */
dojo.widget.defineWidget('xg.music.playlist.RemoveTrackLink', dojo.widget.HtmlWidget, {

    /** The Ajax endpoint */
    _url: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.io.bind({
                url: this._url,
                method: 'post',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8'
            });
            removeTrackEntry(a);
        }));
        dojo.style.setVisibility(a, true);
    }

});
