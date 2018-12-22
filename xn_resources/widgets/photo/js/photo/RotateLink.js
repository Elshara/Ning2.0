dojo.provide('xg.photo.photo.RotateLink');

/**
 * The "Rotate Photo" link.
 */
dojo.widget.defineWidget('xg.photo.photo.RotateLink', dojo.widget.HtmlWidget, {

    /** The URL to post to */
    _url: '',

    /** Whether the POST is in progress */
    posting: false,

    /** The hidden input that stores the rotation of the photo. */
    rotationInput: null,

    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        this.rotationInput = dojo.dom.nextElement(a);
        dojo.style.show(a);
        dojo.style.setVisibility(a, true);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.posting) { return; }
            this.post(a);
        }));
    },

    post: function(a) {
        a.className = "desc working disabled";
        this.posting = true;
        this.setRotation((this.getRotation() + 90) % 360);
        dojo.io.bind({
            url: this._url,
            method: 'post',
            encoding: 'utf-8',
            preventCache: true,
            mimetype: 'text/javascript',
            content: { rotation: this.getRotation() },
            load: dojo.lang.hitch(this, function(type, data, event) {
                var img = dojo.dom.firstElement(a.parentNode);
                img.src = data.imgUrl;
                a.className = 'desc rotate';
                this.posting = false;
            })
        });
    },

    /**
     * Stores the rotation of the photo.
     *
     * @param rotation  0, 90, 180, or 270
     */
    setRotation: function(rotation) {
        this.rotationInput.value = rotation;
    },

    /**
     * Retrieves the rotation of the photo
     *
     * @return  0, 90, 180, or 270
     */
    getRotation: function() {
        return parseInt(this.rotationInput.value, 10);
    }
});

