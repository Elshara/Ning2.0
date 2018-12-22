dojo.provide('xg.opensocial.embed.ScrollLink');

dojo.widget.defineWidget('xg.opensocial.embed.ScrollLink', dojo.widget.HtmlWidget, {
    
    /** id attribute of the element to scroll to */
    _scrollToId: '',
    
    /** The node that should be scrolled to */
    target: '',
    
    fillInTemplate: function(args, frag) {
        var element = this.getFragNodeRef(frag);
        this.target = x$('#' + this._scrollToId)[0];
        if(this.target) {
            xg.listen(element, 'onclick', this, function(evt) {
                xg.stop(evt);
                this.target.scrollIntoView();
            });
        }
    }
});
