dojo.provide('xg.photo.photo.list');

dojo.require('xg.photo.index._shared');
dojo.require('xg.index.util.FormHelper')
dojo.require('xg.index.bulk');
dojo.require('xg.shared.util');

/**
 * @deprecated Use xg.index.bulk.BulkActionLinkWithCheckbox instead.
 */
dojo.widget.defineWidget('xg.index.BulkDeleteActionLink', xg.index.BulkActionLink, {
    _checkboxUrl: '',
    _checkboxMessage: '',

    confirm: function() {
        this.body.innerHTML= ''+
'            <p>' + dojo.string.escape('html', this._confirmMessage) + '</p>'+
'            <fieldset class="nolegend">'+
'                <p>'+
'                    <label><input class="checkbox" type="checkbox" id="dialog_additional_checkbox">'+this._checkboxMessage+'</label>'+
'                </p>'+
'                <span class="right">'+
'                    <a href="#" class="button">'+ this._verb +'</a>'+
'                    <a href="#" class="button">' + xg.photo.nls.html('cancel') + '</a>'+
'                </span>'+
'            </fieldset>';
        var links = this.body.getElementsByTagName('a');
        dojo.event.connect(links[0],'onclick',dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            if(dojo.byId('dialog_additional_checkbox').checked){
                this._url = this._checkboxUrl;
            }
            this.execute();
        }));
        dojo.event.connect(links[1],'onclick',dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            this.hide();
        }));
        xg.shared.util.showOverlay();
        dojo.html.show(this.dialog);
        dojo.html.show(this.dialog.getElementsByTagName('div')[0]);
        xg.index.util.FormHelper.scrollIntoView(this.dialog);
    }
});
