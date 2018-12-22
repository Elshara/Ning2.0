dojo.provide('xg.video.video.TabTrigger');

dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.video.video.TabTrigger', dojo.widget.HtmlWidget, {
    _tabId: '',
    _otherTabId: '',
    /** ID of the hidden input whose value to set to _tabId */
    _hiddenInputId: '',
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.html.hide(this._otherTabId.replace('_tab', '_section'));
            dojo.html.show(this._tabId.replace('_tab', '_section'));
            if (this._hiddenInputId) { dojo.byId(this._hiddenInputId).value = this._tabId; }
            dojo.html.removeClass(dojo.byId(this._otherTabId), 'this');
            dojo.html.addClass(dojo.byId(this._tabId), 'this');
            dojo.byId(this._otherTabId).innerHTML = '<a dojoType="TabTrigger" _tabId="' + this._otherTabId + '" _otherTabId="' + this._tabId + '"  _hiddenInputId="' + this._hiddenInputId + '" href="#">' + dojo.html.renderedTextContent(dojo.byId(this._otherTabId)) + '</a>';
            dojo.byId(this._tabId).innerHTML = '<span>' + dojo.html.renderedTextContent(dojo.byId(this._tabId)) + '</span>';
            xg.shared.util.parseWidgets(dojo.byId(this._otherTabId));
        }));
    }
});
