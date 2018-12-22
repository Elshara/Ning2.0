dojo.provide('xg.video.embed.embed');

dojo.require('xg.shared.EditUtil');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

dojo.provide('xg.video.embed.embed.VideoModule');
dojo.widget.defineWidget('xg.video.embed.embed.VideoModule', dojo.widget.HtmlWidget, {
    _setValuesUrl: '',
    _videoSet: '',
    _videoNum: '',
    _numOptionsJson: '',
    /** The kind of display: detail or player */
    _displayType: '',
    _videoSetOptionsJson: '',
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.video.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if ((! this.form) || (this.form.style.height == "0px")) {
                this.showForm();
            } else {
                this.hideForm();
            }
        }));
    },
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        var numOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._numOptionsJson), function(option) {
            numOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var videoSetOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._videoSetOptionsJson), function(option) {
            videoSetOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                    <form class="xg_module_options" style="overflow:hidden;"> \
                        <fieldset> \
                            <dl> \
                                <dt style="display:none"><label for="' + this.widgetId + '_display_type">' + xg.video.nls.html('display') + '</label></dt> \
                                <dd style="display:none"> \
                                    <select id="' + this.widgetId + '_display_type"> \
                                        <option value="detail">' + xg.video.nls.html('detail') + '</option> \
                                        <option value="player">' + xg.video.nls.html('player') + '</option> \
                                    </select> \
                                </dd> \
                                <dt><label for="' + this.widgetId + '_video_set">' + xg.video.nls.html('from') + '</label></dt> \
                                <dd> \
                                    <select id="' + this.widgetId + '_video_set"> \
                                        ' + videoSetOptionsHtml + ' \
                                    </select> \
                                </dd> \
                                <dt><label for="' + this.widgetId + '_num">' + xg.video.nls.html('show') + '</label></dt> \
                                <dd> \
                                    <select id="' + this.widgetId + '_num" class="short"> \
                                        ' + numOptionsHtml + ' \
                                    </select> ' + xg.video.nls.html('videos') + '\
                                </dd> \
                            </dl> \
                            <p class="buttongroup"> \
                                <input type="submit" value="' + xg.video.nls.html('save') + '" class="button button-primary"/> \
                                <input type="button" value="' + xg.video.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                            </p> \
                        </fieldset> \
                    </form> \
            '))[0];
            this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
            dojo.dom.insertAfter(this.form, this.head);
            this.formHeight = this.form.offsetHeight;
        	this.form.style.height = "0px";

            // connect the various events only once when the form is created
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
        } else {
            dojo.html.removeClass(this.form, 'collapsed');
         }
        this.form.style.height = 0;
        dojo.require('xg.index.util.FormHelper');
        xg.index.util.FormHelper.select(this._videoSet, dojo.byId(this.widgetId + '_video_set'));
        xg.index.util.FormHelper.select(this._videoNum, dojo.byId(this.widgetId + '_num'));
        xg.index.util.FormHelper.select(this._displayType, dojo.byId(this.widgetId + '_display_type'));
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._videoSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_video_set')).value;
        this._videoNum = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_num')).value;
        this._displayType = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_display_type')).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { videoSet: this._videoSet, videoNum: this._videoNum, displayType: this._displayType },
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, dojo.lang.hitch(this, function(type, data, event){
                dojo.lang.forEach(dojo.html.getElementsByClass('xg_module_body', this.module), function(moduleBody) {
                    dojo.dom.removeNode(moduleBody);
                });
                var footer = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
                if (footer) { dojo.dom.removeNode(footer); }
                dojo.lang.forEach(dojo.html.createNodesFromText(data.moduleBodyAndFooterHtml), dojo.lang.hitch(this, function(node) {
                    dojo.dom.insertAtPosition(node, this.module, 'last');
                }));
                xg.shared.util.fixImagesInIE(this.module.getElementsByTagName('img'));
            }))
        });
    }
});