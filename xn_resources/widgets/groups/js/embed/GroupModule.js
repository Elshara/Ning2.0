dojo.provide('xg.groups.embed.GroupModule');

dojo.require('xg.shared.EditUtil');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * The Groups module, which displays recent or popular groups on the homepage and profile page.
 */
dojo.widget.defineWidget('xg.groups.embed.GroupModule', dojo.widget.HtmlWidget, {

    /** Endpoint for saving the values */
    _setValuesUrl: '',
    _updateEmbedUrl: '',

    /** Which groups to display (popular, recent, or promoted) */
    _groupSet: '',

    /** JSON array of objects, each with the following properties: label, value */
    _optionsJson: '',

    /** Number of groups to display */
    _itemCount: '',

    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.groups.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (! this.form || this.form.style.height == "0px") {
                this.showForm();
            } else {
                this.hideForm();
            }
        }));
    },
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        dojo.html.addClass(editbutton, 'close');
        if (!this.form) {
			var options = dojo.json.evalJson(this._optionsJson);
			var optionsHtml = '';
			dojo.lang.forEach(options, function(option) {
				optionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
			});
			this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl>\
                            <dt ' + (options.length > 1 ? '' : 'style="display:none"') + '><label for="' + this.widgetId + '_select">' + xg.groups.nls.html('from') + '</label></dt>\
                            <dd ' + (options.length > 1 ? '' : 'style="display:none"') + '>\
                                <select id="' + this.widgetId + '_select"> \
                                    ' + optionsHtml + ' \
                                </select> \
                            </dd>\
                            <dt><label for="' + this.widgetId + '_item_count">' + xg.groups.nls.html('show') + '</label></dt>\
                            <dd>\
                                <select id="' + this.widgetId + '_item_count" class="short"> \
                                    <option value="0">0</option> \
                                    <option value="1">1</option> \
                                    <option value="2">2</option> \
                                    <option value="3">3</option> \
                                    <option value="4">4</option> \
                                    <option value="5">5</option> \
                                    <option value="6">6</option> \
                                    <option value="10">10</option> \
                                    <option value="20">20</option> \
                                </select> ' + xg.groups.nls.html('groups') + '\
                            </dd>\
                        </dl>\
                        <p class="buttongroup">\
                            <input type="submit" value="' + xg.groups.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.groups.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                        </p>\
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
			this.cancel(event);
			}));
		}
        xg.index.util.FormHelper.select(this._groupSet, dojo.byId(this.widgetId + '_select'));
        xg.index.util.FormHelper.select(this._itemCount, dojo.byId(this.widgetId + '_item_count'));
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    /**
     * Call-back function to update the module body
     *
     * @param ui    jQuery.ui object    The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var columnCount = this.module.parentNode.getAttribute('_columncount');
        dojo.io.bind({
            url: this._updateEmbedUrl,
            method: 'post',
            content: { columnCount: columnCount },
            preventCache: true,
            mimetype: 'text/json',
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
                ui.item.css('visibility', '');
            }))
        });
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var columnCount = this.module.parentNode.getAttribute('_columncount');
        this._groupSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_select')).value;
        this._itemCount = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_item_count')).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { groupSet: this._groupSet, itemCount: this._itemCount, columnCount: columnCount },
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
