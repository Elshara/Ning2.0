dojo.provide('xg.forum.embed.ForumModule');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.EditUtil');
dojo.require('xg.shared.util');

/**
 * The Forum module, which displays recent or popular topics on the homepage and profile page.
 */
dojo.widget.defineWidget('xg.forum.embed.ForumModule', dojo.widget.HtmlWidget, {
    /** Whether categories are enabled */
    _categoriesEnabled: '',
    /** Whether to display Discussions or Categories */
    _viewOptionsJson: '',
    /** Which view is set(discussions or categories) */
    _viewSet: '',
    /** Endpoint for saving the values */
    _setValuesUrl: '',
    /** Which topics/comments to display (popular, recent, or promoted) */
    _topicSet: '',
    /** options for the number of items that can be displayed */
    _numOptionsJson: '',
    /** How to display the topics/comments (titles or detail) */
    _displayOptionsJson: '',
    /** Which display view for topics: titles only or detail */
    _displaySet: '',
    /** JSON array of objects, each with the following properties: label, value */
    _optionsJson: '',
    /** Number of topics/comments to display */
    _itemCount: '',
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.forum.nls.html('edit') + '</a></p>')[0], this.h2);
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
        dojo.html.addClass(editbutton, 'close');
        var displayOptions = dj_eval(this._displayOptionsJson);
        var options = dj_eval(this._optionsJson);
        var nonCategoriesOptions = dojo.lang.filter(options, function(option) { return ! option.value.match(/category_/); });
        var categoriesOptions = dojo.lang.filter(options, function(option) { return option.value.match(/category_/); });
        var numOptions = dj_eval(this._numOptionsJson);
        var viewOptions = dj_eval(this._viewOptionsJson);
        var viewOptionsHtml = '';
        dojo.lang.forEach(viewOptions, function(option) {
            viewOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var displayOptionsHtml = '';
        dojo.lang.forEach(displayOptions, function(option) {
            displayOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var optionsHtml = '';
        dojo.lang.forEach(nonCategoriesOptions, function(option) {
            optionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var numOptionsHtml = '';
        dojo.lang.forEach(numOptions, function(option) {
            numOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        if (categoriesOptions.length) {
            optionsHtml += '<optgroup label="' + xg.forum.nls.html('discussionsFromACategory') + '">';
            dojo.lang.forEach(categoriesOptions, function(option) {
                optionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
            });
            optionsHtml += '</optgroup>';
        }
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl>\
                            <div id="embedviewOptions"> \
                                <dt><label for="' + this.widgetId + '_view">' + xg.forum.nls.html('view') + '</label></dt>\
                                <dd>\
                                    <select id="' + this.widgetId + '_view"> \
                                        ' + viewOptionsHtml  + '\
                                    </select> \
                                </dd>\
                            </div> \
                            <div id="embedDiscussionOptions"> \
                                <dt><label for="' + this.widgetId + '_display">' + xg.forum.nls.html('display') + '</label></dt>\
                                <dd>\
                                    <select id="' + this.widgetId + '_display"> \
                                        ' + displayOptionsHtml  + '\
                                    </select> \
                                </dd>\
                                <dt><label for="' + this.widgetId + '_select">' + xg.forum.nls.html('from') + '</label></dt>\
                                <dd>\
                                    <select id="' + this.widgetId + '_select"> \
                                        ' + optionsHtml + ' \
                                    </select> \
                                </dd>\
                            </div> \
                            <dt><label for="' + this.widgetId + '_item_count">' + xg.forum.nls.html('show') + '</label></dt>\
                            <dd>\
                                <select id="' + this.widgetId + '_item_count" class="short"> \
                                        ' + numOptionsHtml + ' \
                                </select> ' + xg.forum.nls.html('items') + '\
                            </dd>\
                        </dl>\
                        <p class="buttongroup">\
                            <input type="submit" value="' + xg.forum.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.forum.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                        </p>\
                    </fieldset> \
                </form> \
                '))[0];
            this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
            dojo.dom.insertAfter(this.form, this.head);
            if (this._categoriesEnabled != "1") {
                dojo.style.hide(dojo.byId('embedviewOptions'));
            }
            this.formHeight = this.form.offsetHeight;
        	this.form.style.height = "0px";

            // connect the various events only once when the form is created
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_view'), 'onchange', dojo.lang.hitch(this, function(event) {
                this.toggleForumRelated(xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_view')).value == "categories");
            }));
        } else {
            dojo.html.removeClass(this.form, 'collapsed');
        }
        this.form.style.height = "0px";
        xg.index.util.FormHelper.select(this._viewSet, dojo.byId(this.widgetId + '_view'));
        xg.index.util.FormHelper.select(this._displaySet, dojo.byId(this.widgetId + '_display'));
        xg.index.util.FormHelper.select(this._topicSet, dojo.byId(this.widgetId + '_select'));
        xg.index.util.FormHelper.select(this._itemCount, dojo.byId(this.widgetId + '_item_count'));
        if (this._categoriesEnabled == "1" && this._viewSet == 'categories') {
            this.toggleForumRelated(true);
        }
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    toggleForumRelated: function(enabled) {
        dojo.byId(this.widgetId + '_display').disabled = enabled;
        dojo.byId(this.widgetId + '_select').disabled = enabled;
        if (enabled) {
            dojo.byId(this.widgetId + '_display').className = 'disabled';
            dojo.byId(this.widgetId + '_select').className = 'disabled';
        } else {
            dojo.byId(this.widgetId + '_display').className = '';
            dojo.byId(this.widgetId + '_select').className = '';
        }
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._viewSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_view')).value;
        this._displaySet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_display')).value;
        this._topicSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_select')).value;
        this._itemCount = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_item_count')).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { displaySet: this._displaySet, topicSet: this._topicSet, itemCount: this._itemCount, viewSet: this._viewSet },
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
