dojo.provide('xg.page.embed.PageModule');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * The Page module, which displays recent or popular pages on the homepage and profile page.
 */
dojo.widget.defineWidget('xg.page.embed.PageModule', dojo.widget.HtmlWidget, {
    _setValuesUrl: '',
    _pageSet: '',
    _optionsJson: '',
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.page.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.showForm();
        }));
    },
    showForm: function() {
        var optionsHtml = '';
        dojo.lang.forEach(dj_eval(this._optionsJson), function(option) {
            optionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <a class="close" href="#">' + xg.page.nls.html('close') + '</a> \
                    <fieldset> \
                        <p> \
                            <label for="' + this.widgetId + '_select">' + xg.page.nls.html('displayPagePosts') + '</label><br /> \
                            <select id="' + this.widgetId + '_select"> \
                                ' + optionsHtml + ' \
                            </select> \
                        </p> \
                        <p class="actions"><input class="left button submit" type="submit" value="' + xg.page.nls.html('save') + '" /> <a class="right" href="#">' + xg.page.nls.html('cancel') + '</a></p> \
                    </fieldset> \
                </form> \
        '))[0];
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        dojo.dom.insertAfter(this.form, this.head);
        xg.index.util.FormHelper.fixPopupZIndexAfterShow(this.form);
        xg.index.util.FormHelper.select(this._pageSet, dojo.byId(this.widgetId + '_select'));
        xg.index.util.FormHelper.scrollIntoView(this.form.getElementsByTagName('fieldset')[0]);
        dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
            this.save(event);
        }));
        var refs = this.form.getElementsByTagName('a'), cb = dojo.lang.hitch(this, function(event) { this.cancel(event); });
        for (var i = 0; i<refs.length; i++) {
            dojo.event.connect(refs[i], 'onclick', cb);
        }
    },
    hideForm: function() {
        xg.index.util.FormHelper.fixPopupZIndexBeforeHide(this.form);
        dojo.dom.removeNode(this.form);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._pageSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_select')).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { pageSet: this._pageSet },
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
    },
    cancel: function(event) {
        dojo.event.browser.stopEvent(event);
        this.hideForm();
    }
});
