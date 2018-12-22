dojo.provide('xg.shared.StarRater');

dojo.require('xg.shared.util');

/**
 * Ajax rating stars.
 */
dojo.widget.defineWidget('xg.shared.StarRater', dojo.widget.HtmlWidget, {

    /** The current rating, from 0 to 5 */
    _rating: 0,

    /** URL for the endpoint for changing a rating */
    _setRatingUrl: '',

    /** Whether the current user is a pending member */
    _isPending: false,

	/** Element ID to recieve rating results */
    _resultId: '',
    
    /** Element ID to set the value to the new rating on click */
    _setRatingId: '',
    
    /**
     * Initializes this widget.
     */
    fillInTemplate: function(args, frag) {
        var ul = this.getFragNodeRef(frag);
        dojo.lang.forEach(ul.getElementsByTagName('a'), dojo.lang.hitch(this, function(a) {
            dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if (this._isPending) { return xg.shared.util.promptIsPending(); }
                this.updateRating(a, ul);
            }));
        }));
    },

    /**
     * Saves the rating.
     *
     * @param a  the rating <a> that was clicked
     * @param ul  the <ul> containing the rating links
     */
    updateRating: function(a, ul) {
        var rating = parseInt(a.className.charAt(5)), self = this;
        dojo.html.getElementsByClass('current', ul)[0].style.width = 13*rating + 'px';
        // if there's a url we're rating a photo/video or similar and want to post to the appropriate endpoint
        // if there's no url, we are in a form and just want to set the hidden value.
        if (this._setRatingUrl) {
            dojo.io.bind({
                url: this._setRatingUrl,
                method: 'post',
                content: { rating: rating },
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript',
                load: function(e, data, xhr) {
    				if (self._resultId && ("undefined" != typeof data["html"])) {
    					var node = dojo.byId(self._resultId);
    					node.innerHTML = data.html;
                        dojo.style.setShowing(node, data.html);
                        xg.shared.util.fixImagesInIE(node.getElementsByTagName('img'));
    				}
    			}
            });
        } else if (dojo.byId(this._setRatingId)) {
            dojo.byId(this._setRatingId).value = rating;
        }
    },
    
    clearRating: function() {
        var ul = this.domNode;
        dojo.html.getElementsByClass('current', ul)[0].style.width = '0px';
        if (dojo.byId(this._setRatingId)) {
            dojo.byId(this._setRatingId).value = this._rating;
        }
    }

});
