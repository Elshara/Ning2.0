dojo.provide('xg.shared.FavoriteLink');
dojo.require("dojo.lfx.*");



dojo.widget.defineWidget('xg.shared.FavoriteLink', dojo.widget.HtmlWidget, {
    _addurl: "",
    _removeUrl: "",
    _hasFavorite: "",
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        if (this._hasFavorite == 0) {
            a.className = "desc favorite-add";
            a.innerHTML = xg.shared.nls.text('addToFavorites');
        } else {
            a.className = "desc favorite-remove";
            a.innerHTML = xg.shared.nls.text('removeFromFavorites');
        }
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            a.className = "desc working disabled";
            if (this.posting) { return; }
            this.post(a);
        }));
    },
    post: function(a) {
        this.posting = true;
        if (this._hasFavorite == 0) {
            url = this._addurl;
        } else {
            url = this._removeUrl;
        }
        dojo.io.bind({
            url: url,
            method: 'post',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                if (this._hasFavorite == 0) {
                    a.className = "desc favorite-remove";
                    a.innerHTML = xg.shared.nls.text('removeFromFavorites');
                    this._hasFavorite = 1;
                } else {
                    a.className = "desc favorite-add"
                    a.innerHTML = xg.shared.nls.text('addToFavorites');
                    this._hasFavorite = 0;
                }
				dojo.lfx.html.highlight(a, '#ffee7d', 1000).play();
                this.posting = false;
            })
        });
    }
});