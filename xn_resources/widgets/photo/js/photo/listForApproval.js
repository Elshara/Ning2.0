dojo.provide('xg.photo.photo.listForApproval');

dojo.require('xg.photo.index._shared');
dojo.provide('xg.photo.ApprovalLink');
dojo.widget.defineWidget('xg.photo.ApprovalLink', dojo.widget.HtmlWidget, {
    _processPhotoUrl: '<required>',
    _processAllPhotosForUserUrl: '<required>',
    _verb: '<required>',
    _progressTitle: '<required>',
    _progressMessage: '<required>',
    _approvalListUrl: '<required>',
    fillInTemplate: function(args, frag) {
        this.button = this.getFragNodeRef(frag);
        dojo.event.connect(this.button, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (xg.photo.ApprovalLink.loading) { return; }
            xg.photo.ApprovalLink.loading = true;
            if (dojo.html.getElementsByClass('checkbox', this.button.parentNode)[0].checked) {
                this.processAllPhotosForUser();
            } else {
                this.processPhoto();
            }
        }));
    },
    processAllPhotosForUser: function() {
        dojo.widget.createWidget('BulkActionLink', {
            _url: this._processAllPhotosForUserUrl,
            _verb: this._verb,
            _progressTitle: this._progressTitle,
            _progressMessage: this._progressMessage,
            _successUrl: this._approvalListUrl
        }).execute();
    },
    processPhoto: function() {
        var spinner = dojo.html.getElementsByClass('approval_spinner', this.button.parentNode)[0];
        dojo.style.show(spinner);
        dojo.io.bind({
            url: this._processPhotoUrl,
            method: 'post',
            mimetype: 'text/json',
            encoding: 'utf-8',
            content: xg.photo.parseUrlParameters(this._processPhotoUrl),
            load: dojo.lang.hitch(this, function(type, data, evt) {
                if (data && data.html) {
                    dojo.byId('column').innerHTML = data.html;
                    xg.photo.fixImagesInIE(dojo.byId('column'), false)
                    // reparse dojo widgets (BAZ-10515) [ywh 2008-09-29]
                    xg.shared.util.parseWidgets(dojo.byId('column'));
                    xg.photo.ApprovalLink.loading = false;
                    dojo.style.hide(spinner);
                }
            })
        });
    }
});
xg.photo.ApprovalLink.loading = false;
