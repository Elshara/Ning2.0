dojo.provide('xg.groups.admin.ApprovalLink');
dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.groups.admin.ApprovalLink', dojo.widget.HtmlWidget, {
    _processGroupUrl: '<required>',
    _processAllGroupsForUserUrl: '<required>',
    _verb: '<required>',
    _progressTitle: '<required>',
    _progressMessage: '<required>',
    _approvalListUrl: '<required>',
    fillInTemplate: function(args, frag) {
        this.button = this.getFragNodeRef(frag);
        dojo.event.connect(this.button, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (xg.groups.admin.ApprovalLink.loading) { return; }
            xg.groups.admin.ApprovalLink.loading = true;
            if (dojo.html.getElementsByClass('checkbox', this.button.parentNode)[0].checked) {
                this.processAllGroupsForUser();
            } else {
                this.processGroup();
            }
        }));
    },
    processAllGroupsForUser: function() {
        dojo.widget.createWidget('BulkActionLink', {
            _url: this._processAllGroupsForUserUrl,
            _verb: this._verb,
            _progressTitle: this._progressTitle,
            _progressMessage: this._progressMessage,
            _successUrl: this._approvalListUrl
        }).execute();
    },
    processGroup: function() {
        var spinner = dojo.html.getElementsByClass('approval_spinner', this.button.parentNode)[0];
        dojo.style.show(spinner);
        dojo.io.bind({
            url: this._processGroupUrl,
            method: 'post',
            mimetype: 'text/json',
            encoding: 'utf-8',
            preventCache: true,
            content: xg.shared.util.parseUrlParameters(this._processGroupUrl),
            load: dojo.lang.hitch(this, function(type, data, evt) {
                if (data && data.html) {
                    dojo.byId('column').innerHTML = data.html;
                    dojo.widget.createWidget('column');
                    xg.groups.admin.ApprovalLink.loading = false;
                    dojo.style.hide(spinner);
                }
            })
        });
    }
});
xg.groups.admin.ApprovalLink.loading = false;
