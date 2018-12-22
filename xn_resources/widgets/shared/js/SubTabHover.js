dojo.provide('xg.shared.SubTabHover');
dojo.require('xg.shared.util');

/**
 * Sub tab hover handling
 */
dojo.widget.defineWidget('xg.shared.SubTabHover', dojo.widget.HtmlWidget, {

    /** main tab anchor */
    a: null,

    /** div container holding the subtab list */
    subTabDiv: null,

    /** li element that contains the main tab link and the subtab div */
    li: null,

    /** result of setTimeout for showing the subtab div */
    showTimeoutHandle: null,

    /** result of setTimeout for hiding the subtab div */
    hideTimeoutHandle: null,

    /** Milliseconds on mouseover before showing the tab */
    showSubTabTime: 150,

    /** Milliseconds on mouseout before hiding the tab */
    hideSubTabTime: 150,

    /**
     * Grab handles on dom elements, set up listeners
     */
    fillInTemplate: function(args, frag) {
        this.li = this.getFragNodeRef(frag);
        this.a = dojo.dom.firstElement(this.li);
        this.subTabDiv = dojo.dom.nextElement(this.a);
        xg.listen(this.a, 'onmouseover', this, function(evt) {
            clearTimeout(this.hideTimeoutHandle);
            this.showTimeoutHandle = setTimeout(dojo.lang.hitch(this, 'showSubTab'), this.showSubTabTime);
        });
        xg.listen(this.a, 'onmouseout', this, function(evt){
            clearTimeout(this.showTimeoutHandle);
            this.hideTimeoutHandle = setTimeout(dojo.lang.hitch(this, 'hideSubTab'), this.hideSubTabTime);
        });
        xg.listen(this.subTabDiv, 'onmouseover', this, function(evt){
            clearTimeout(this.hideTimeoutHandle);
        });
        xg.listen(this.subTabDiv, 'onmouseout', this, function(evt){
            this.hideTimeoutHandle = setTimeout(dojo.lang.hitch(this, 'hideSubTab'), this.hideSubTabTime);
        });
        /*if (dojo.render.html.ie) {
			this.ieiframe = document.body.appendChild(document.createElement(
				'<iframe name="xg_subtab" src="about:blank" frameborder="0" style="display:none; background:pink; position:absolute; filter:mask(); z-index:0;">'));
		}*/
    },

    /**
     * Display div with subtabs
     */
    showSubTab: function() {
        clearTimeout(this.hideTimeoutHandle);
        var o = xg.shared.util.getOffset(this.li, this.subTabDiv);
        dojo.html.addClass(this.a, "hovered");
        dojo.style.setStyleAttributes(this.subTabDiv, "z-index:100;position:absolute;display:block;left: "+(o.x)+"px; top:" + (o.y+parseInt(this.li.offsetHeight)) + "px;");
        /*if (this.ieiframe) {
            var s = this.ieiframe.style;
            o = xg.shared.util.getOffset(this.subTabDiv, this.ieiframe);
            s.height = this.subTabDiv.offsetHeight+'px';
            s.width = this.subTabDiv.offsetWidth+'px';
            s.left = o.x + 'px';
            s.top = o.y + 'px';
            s.display = 'block';
		}*/
    },

    /**
     * Hide div with subtabs
     */
    hideSubTab: function() {
        clearTimeout(this.showTimeoutHandle);
        dojo.style.hide(this.subTabDiv);
        if (this.ieiframe) {
            this.ieiframe.style.display = 'none';
        }
        dojo.html.removeClass(this.a, "hovered");
    }
});
