dojo.provide('xg.shared.ContextHelpToggler');

dojo.require('xg.shared.util');

/**
 * Shows/hides a help bubble.
 */
dojo.widget.defineWidget('xg.shared.ContextHelpToggler', dojo.widget.HtmlWidget, {
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
		var popupA = dojo.dom.nextElement(a), popupAFixed = 0;
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (popupA && dojo.html.hasClass(popupA, 'context_help_popup')) {
				if (!popupAFixed) {
	 				popupA.parentNode.removeChild(popupA);
					popupA = dojo.byId('xg_body').appendChild(popupA);
					popupAFixed = 1;
				}
                this.togglePopup(popupA, a);
                return;
            }
			var popupB = dojo.dom.getAncestors(a, function(element){
    	    	return dojo.html.hasClass(element, 'context_help_popup');
			}, true);
            if (popupB) {
                this.togglePopup(popupB);
                return;
            }
        }));
    },
    togglePopup: function(popup, a) {
        if (! dojo.style.isShowing(popup)) {
            var popups = dojo.html.getElementsByClass('context_help_popup');
            for (var i=0; i<popups.length; i++) {
                dojo.style.hide(popups[i]);
            }
			var o = xg.shared.util.getOffsetX(a, popup);
			popup.style.left = o.x-3+'px'; // this magical numbers are from common.css: span.context_help_popup
			popup.style.top = o.y+12+'px';
		}

        dojo.style.toggleShowing(popup);
    }
});
