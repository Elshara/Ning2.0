dojo.provide('xg.shared.MapItLink');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * A link that reveals a small map, which the user clicks to choose a location.
 */
dojo.widget.defineWidget('xg.shared.MapItLink', dojo.widget.HtmlWidget, {

    /** Whether the map should be open initially. */
    _open: false,

    /** ID of the location input. */
    _locationInputId: '',

    /** The <div> representing the widget. */
    div: null,

    /** The <div> containing the map. */
    mapDiv: null,

    /** The <div> for error messages. */
    errorDiv: null,

    /** The GMap2. */
    map: null,

    /** The text input for the Find function. */
    textField: null,

    /**
     * Initializes this widget.
     */
    fillInTemplate: function(args, frag) {
        if (! GBrowserIsCompatible()) { return; }
        this.div = this.getFragNodeRef(frag);
        this.mapDiv = this.div.getElementsByTagName('div')[0];
        this.errorDiv = dojo.html.getElementsByClass('errordesc', this.div)[0];
        this.textField = dojo.html.getElementsByClass('textfield', this.div)[0];
        xg.shared.util.preventEnterFromSubmittingForm(this.textField, dojo.lang.hitch(this, this.find));
        dojo.event.connect(dojo.html.getElementsByClass('button', this.div)[0], 'onclick', dojo.lang.hitch(this, this.find));
        var mapCreated = false;
        var toggleMap = dojo.lang.hitch(this, function() {
            dojo.style.toggleShowing(this.mapDiv);
            if (! mapCreated) {
                mapCreated = true;
                this.createMap();
            }
            if (dojo.style.isShowing(this.mapDiv)) { xg.index.util.FormHelper.scrollIntoView(this.mapDiv); }
        });
        dojo.event.connect(this.div.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            toggleMap();
            if (dojo.style.isShowing(this.mapDiv)) { this.textField.focus(); }
        }));
        if (this._open) { toggleMap(); }
    },

    /**
     * Returns the object used to convert addresses to co-ordinates.
     *
     * @return  the GClientGeocoder
     */
    getGeocoder: function() {
        if (! xg.shared.MapItLink.geocoder) { xg.shared.MapItLink.geocoder = new GClientGeocoder(); }
        return xg.shared.MapItLink.geocoder
    },

    /**
     * Moves the map to the location that the user has specified in the Find box.
     */
    find: function() {
        this.findProper(this.textField.value);
    },

    /**
     * Moves the map to the specified location.
     *
     * @param location  a location, such as a city or street address
     */
    findProper: function(location) {
        this.setErrorHtml('');
        location = dojo.string.trim(location);
        if (location.length == 0) { return; }
        var thisFunction = arguments.callee;
        if (thisFunction.finding) { return; }
        thisFunction.finding = true;
        this.getGeocoder().getLatLng(location, dojo.lang.hitch(this, function(latLng) {
            thisFunction.finding = false;
            if (! latLng) { return this.setErrorHtml(xg.shared.nls.text('locationNotFound', dojo.string.escape('html', location))); }
            var looksLikeStreetAddress = location.match(/[0-9]/);
            this.setCoordinates(latLng, looksLikeStreetAddress ? 13 : 8);
        }));
    },

    /**
     * Shows or hides an error message.
     *
     * @param errorHtml  HTML to display, or an empty string to hide the error message.
     */
    setErrorHtml: function(errorHtml) {
        dojo.style.setShowing(this.errorDiv, errorHtml.length > 0);
        this.errorDiv.innerHTML = errorHtml;
    },

    /**
     * Builds the map object.
     */
    createMap: function() {
        this.map = new GMap2(dojo.dom.nextElement(dojo.dom.firstElement(this.mapDiv)));
        this.map.addControl(new GSmallMapControl());
        GEvent.addListener(this.map, 'click', dojo.lang.hitch(this, function(marker, latLng) {
            if (! marker) { this.setCoordinates(latLng, this.getZoom()); }
        }));
        GEvent.addListener(this.map, 'zoomend', dojo.lang.hitch(this, function(oldZoom, newZoom) {
            this.setCoordinates(new GLatLng(this.getLatitude(), this.getLongitude()), newZoom, false);
        }));
        this.setCoordinates(new GLatLng(this.getLatitude(), this.getLongitude()), this.getZoom(), false);
        if (this.getLocationTypeInput().value == 'skip') { this.findProper(dojo.byId(this._locationInputId).value); }
    },

    /**
     * Sets the current coordinates. Note that this function is called by editMultiple.js.
     *
     * @param latLng  the new latitude and longitude
     * @param zoom  the integer zoom level
     * @param setLocationType  whether to set the value in the locationType <input> to latlng; defaults to true
     */
    setCoordinates: function(latLng, zoom, setLocationType) {
        if (this.map) {
            this.map.clearOverlays();
            // Workaround for what seems to be a Google Maps bug: if you go to "Texas" (zoom = 8),
            // then go to "911 Fort St, Victoria BC, Canada" (zoom = 13), the zoom will be correct
            // but not the latLng. Calling setCenter twice seems to ensure that both the zoom
            // and the latLng are correct [Jon Aquino 2008-03-04]
            this.map.setCenter(latLng, zoom);
            this.map.setCenter(latLng, zoom);
            this.map.addOverlay(new GMarker(latLng));
        }
        this.getLatitudeInput().value = latLng.lat();
        this.getLongitudeInput().value = latLng.lng();
        this.getZoomInput().value = zoom;
        if (setLocationType !== false) { this.getLocationTypeInput().value = 'latlng'; }
    },

    /**
     * Returns the latitude.
     *
     * @return the latitude, in degrees
     */
    getLatitude: function() {
        return parseFloat(this.getLatitudeInput().value);
    },

    /**
     * Returns the longitude.
     *
     * @return the longitude, in degrees
     */
    getLongitude: function() {
        return parseFloat(this.getLongitudeInput().value);
    },

    /**
     * Returns the zoom level.
     *
     * @return the integer zoom level
     */
    getZoom: function() {
        return parseInt(this.getZoomInput().value);
    },

    /**
     * Returns the location type.
     *
     * @return "latlng" (location specified) or "skip" (no location specified)
     */
    getLocationType: function() {
        return this.getLocationTypeInput().value;
    },

    /**
     * Returns the hidden inputs.
     *
     * @return  this widget's hidden <input> fields
     */
    getHiddenInputs: function() {
        return dojo.lang.filter(this.div.getElementsByTagName('input'), function(input) { return input.type == 'hidden'; });
    },

    /**
     * Returns the hidden input for latitude.
     *
     * @return the <input>
     */
    getLatitudeInput: function() {
        var hiddenInputs = this.getHiddenInputs();
        return hiddenInputs[hiddenInputs.length - 4];
    },

    /**
     * Returns the hidden input for longitude.
     *
     * @return the <input>
     */
    getLongitudeInput: function() {
        var hiddenInputs = this.getHiddenInputs();
        return hiddenInputs[hiddenInputs.length - 3];
    },

    /**
     * Returns the hidden input for zoom.
     *
     * @return the <input>
     */
    getZoomInput: function() {
        var hiddenInputs = this.getHiddenInputs();
        return hiddenInputs[hiddenInputs.length - 2];
    },

    /**
     * Returns the hidden input for location type.
     * Its value is "latlng" (location specified) or "skip" (no location specified).
     *
     * @return the <input>
     */
    getLocationTypeInput: function() {
        var hiddenInputs = this.getHiddenInputs();
        return hiddenInputs[hiddenInputs.length - 1];
    }

});
