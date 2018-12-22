dojo.provide('xg.shared.PopupMap');

dojo.require('xg.index.util.FormHelper');

/**
 * A "Show Map" link that displays a map when clicked.
 */
dojo.widget.defineWidget('xg.shared.PopupMap', dojo.widget.HtmlWidget, {

    /** The latitude. */
    _lat: '',

    /** The longitude. */
    _lng: '',

    /** The zoom level. */
    _zoom: '',

    /** The <span> containing the map. */
    container: null,

    /**
     * Initializes this widget.
     */
    fillInTemplate: function(args, frag) {
        this._lat = parseFloat(this._lat);
        this._lng = parseFloat(this._lng);
        this._zoom = parseInt(this._zoom);
        var a = this.getFragNodeRef(frag);
        var mapCreated = false;
        this.container = dojo.byId('map_container');
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.toggleShowing(this.container);
            if (! mapCreated) {
                mapCreated = true;
                this.createMap(a);
            }
            if (dojo.style.isShowing(this.container)) {
                a.innerHTML = xg.shared.nls.html('hideMap');
                xg.index.util.FormHelper.scrollIntoView(this.container);
            } else {
                a.innerHTML = xg.shared.nls.html('showMap');
            }
        }));
    },

    /**
     * Builds the map object.
     *
     * @param a  the <a> tag
     */
    createMap: function(a) {
        if (! GBrowserIsCompatible()) { return; }
        var map = new GMap2(this.container);
        map.setCenter(new GLatLng(this._lat, this._lng), this._zoom);
        map.addOverlay(new GMarker(new GLatLng(this._lat, this._lng)));
        map.addControl(new GSmallMapControl());
    }

});
