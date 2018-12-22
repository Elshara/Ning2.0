dojo.provide('xg.profiles.embed.embed');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');

dojo.provide('xg.profiles.embed.embed.MembersModule');
dojo.widget.defineWidget('xg.profiles.embed.embed.MembersModule', dojo.widget.HtmlWidget, {
    _setValuesUrl: '',
    _displaySet: '',
    _displayOptionsJson: '',
    _sortSet: '',
    _sortOptionsJson: '',
    _rowsSet: '',
    _rowsOptionsJson: '',
    isContainer: true,
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit button"><a class="button" href="#">' + xg.profiles.nls.html('edit') + '</a></p>')[0], this.h2);
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
        var displayOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._displayOptionsJson), function(option) {
             displayOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var sortOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._sortOptionsJson), function(option) {
             sortOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var rowsOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._rowsOptionsJson), function(option) {
             rowsOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl> \
                            <dt><label for="' + this.widgetId + '_display">' + xg.profiles.nls.html('display') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_display"> \
                                    ' + displayOptionsHtml + ' \
                                </select> \
                            </dd> \
                            <dt><label for="' + this.widgetId + '_sort">' + xg.profiles.nls.html('from') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_sort"> \
                                    ' + sortOptionsHtml + ' \
                                </select> \
                            </dd> \
                            <dt><label for="' + this.widgetId + '_rows">' + xg.profiles.nls.html('show') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_rows" class="short"> \
                                    ' + rowsOptionsHtml + ' \
                                </select> ' + xg.profiles.nls.html('rows') + '\
                            </dd> \
                        </dl> \
                        <p class="buttongroup"> \
                            <input type="submit" value="' + xg.profiles.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.profiles.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                        </p> \
                    </fieldset> \
                </form> \
                '))[0];
            this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
            dojo.dom.insertAfter(this.form, this.head);
            this.formHeight = this.form.offsetHeight;
            this.form.style.height = "0px";
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
        }else {
            dojo.html.removeClass(this.form, 'collapsed');
         }
        this.form.style.height = "0px";
        xg.index.util.FormHelper.select(this._displaySet, dojo.byId(this.widgetId + '_display'  ));
        xg.index.util.FormHelper.select(this._sortSet   , dojo.byId(this.widgetId + '_sort'     ));
        xg.index.util.FormHelper.select(this._rowsSet, dojo.byId(this.widgetId + '_rows'  ));
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._displaySet    = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_display'  )).value;
        this._sortSet       = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_sort'     )).value;
        this._rowsSet       = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_rows'  )).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: {
                displaySet  : this._displaySet,
                sortSet     : this._sortSet,
                rowsSet  : this._rowsSet},
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
                xg.shared.util.parseWidgets(dojo.html.getElementsByClass('xg_module_body', this.module)[0])
            }))
        });
    }
});