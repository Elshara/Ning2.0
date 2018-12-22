dojo.provide('xg.video.video.listForApproval');

dojo.require('xg.shared.util');

dojo.provide('xg.video.ApprovalListPlayer');
dojo.widget.defineWidget('xg.video.ApprovalListPlayer', dojo.widget.HtmlWidget, {
    _approvalListUrl: '<required>',
    _approveVideoUrl: '<required>',
    _deleteVideoUrl: '<required>',
    _approveAllVideosForUserUrl: '<required>',
    _deleteAllVideosForUserUrl: '<required>',
    _contributorName: '<required>',
    _contributorFullName: '<required>',
    row: null,
    spinner: null,
    fillInTemplate: function(args, frag) {
        this.row = this.getFragNodeRef(frag);
        var approveButton = xg.$('.xj_approve', this.row);
        var deleteButton = xg.$('.xj_delete', this.row);
        this.spinner = xg.$('.xj_spinner', this.row);
        this.addClickHandler(approveButton, this._approveVideoUrl, {
            _url: this._approveAllVideosForUserUrl,
            _verb: xg.video.nls.text('approve'),
            _progressTitle: xg.video.nls.text('approving'),
            _progressMessage: xg.video.nls.text('keepWindowOpenWhileApproving'),
            _successUrl: this._approvalListUrl
        });
        this.addClickHandler(deleteButton, this._deleteVideoUrl, {
            _url: this._deleteAllVideosForUserUrl,
            _verb: xg.video.nls.text('delete'),
            _progressTitle: xg.video.nls.text('deleting'),
            _progressMessage: xg.video.nls.text('keepWindowOpenWhileDeleting'),
            _successUrl: this._approvalListUrl
        });
        // Initialize other checkboxes as well [Jon Aquino 2006-12-09]
        this.updateAllCheckboxes();
    },
    addClickHandler: function(button, processVideoUrl, processAllVideosOptions) {
        dojo.event.connect(button, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (xg.video.ApprovalListPlayer.loading) { return; }
            xg.video.ApprovalListPlayer.loading = true;
            if (dojo.html.getElementsByClass('checkbox', this.domNode)[0].checked) {
                this.processAllVideosForUser(processAllVideosOptions);
            } else {
                this.processVideo(processVideoUrl);
            }
        }));
    },
    processAllVideosForUser: function(options) {
        dojo.widget.createWidget('BulkActionLink', options).execute();
    },
    processVideo: function(url) {
        dojo.html.show(this.spinner);
        dojo.io.bind({
            encoding:'utf-8',
            url: url,
            method: 'post',
            preventCache: true,
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, evt) {
                if (data.currentPageVideoCount == 0) {
                    document.location = this._approvalListUrl;
                    return;
                }
                dojo.require("dojo.lfx.*");
                dojo.lfx.fadeOut(this.row, 1000, dojo.lfx.easeIn, dojo.lang.hitch(this, function() {
                    var container = this.row.parentNode;
                    dojo.dom.removeNode(this.row);
                    var pagination = dojo.html.getElementsByClassName('pagination', container)[0];
                    if (pagination != null) { dojo.dom.removeNode(pagination); }
                    if (data.html.length > 0) {
                        // Use innerHTML rather than createNodesFromText, otherwise player won't appear in IE  [Jon Aquino 2006-09-14]
                        var newRow =  dojo.html.createNodesFromText(data.html)[0];
                        dojo.dom.insertAtPosition(newRow, container, 'last');
                        xg.video.fixImagesInIE(newRow.getElementsByTagName('img'), false);
                        xg.shared.util.parseWidgets(newRow);
                    }
                    if (data.pagination.length > 0) {
                        dojo.dom.insertAtPosition(dojo.html.createNodesFromText(data.pagination)[0], container, 'last');
                    }
                    // Call updateCheckbox *after* adding pagination [Jon Aquino 2006-12-09]
                    this.updateAllCheckboxes();
                    xg.video.ApprovalListPlayer.loading = false;
                })).play();
            })
        });
    },
    updateCheckbox: function() {
        var checkbox = dojo.html.getElementsByClass('checkbox', this.domNode)[0];
        if (dojo.html.getElementsByClassName('pagination', dojo.byId('xg_body')).length) {
            dojo.style.show(checkbox.parentNode);
            return;
        }
        var contributorNameCount = 0;
        dojo.lang.forEach(dojo.widget.manager.getWidgetsByType('ApprovalListPlayer'), dojo.lang.hitch(this, function(approvalListPlayer) {
            if (approvalListPlayer._contributorName == this._contributorName) { contributorNameCount++; }
        }));
        if (contributorNameCount > 1) {
            dojo.style.show(checkbox.parentNode);
            return;
        }
        checkbox.checked = false;
        dojo.style.hide(checkbox.parentNode);
    },
    updateAllCheckboxes: function() {
        dojo.lang.forEach(dojo.widget.manager.getWidgetsByType('ApprovalListPlayer'), function(approvalListPlayer) {
            approvalListPlayer.updateCheckbox();
        });
    }
});
xg.video.ApprovalListPlayer.loading = false;