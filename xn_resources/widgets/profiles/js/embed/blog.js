dojo.provide('xg.profiles.embed.blog');
dojo.require('xg.index.util.FormHelper');

dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');

dojo.provide('xg.profiles.embed.blog.BlogModule');
dojo.widget.defineWidget('xg.profiles.embed.blog.BlogModule', dojo.widget.HtmlWidget, {
    _url: '',
    _updateUrl: '', // url of post action to update the module body
    _layoutType: '<required>',
    _displaySet: '',
    _displayOptionsJson: '',
    _sortSet: '',
    _sortOptionsJson: '',
    _postsSet: '',
    _postsOptionsJson: '',


    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.profiles.nls.html('edit') + '</a></p>')[0], this.h2);
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
        if(this._sortOptionsJson) {
            var sortOptionsHtml = '';
            dojo.lang.forEach(dj_eval(this._sortOptionsJson), function(option) {
                 sortOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
            });
        }
        if(this._postsOptionsJson) {
            var postsOptionsHtml = '';
            dojo.lang.forEach(dj_eval(this._postsOptionsJson), function(option) {
                 postsOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
            });
        }

        // Options on the homepage embed
        var options = { }
        if (this._layoutType == 'homepage') {
            options.recent = xg.profiles.nls.html('recentlyAdded');
            options.promoted = xg.profiles.nls.html('featured');
        }
        // Options on the profile page embed
        else {
            options.recent = xg.profiles.nls.html('iHaveRecentlyAdded');
            options.site = xg.profiles.nls.html('fromTheSite');
        }
        var optionsHtml = '';
        for (var value in options) {
            optionsHtml += '<option value="' + value + '">' + options[value] + '</option>';
        }
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        this.body = dojo.html.getElementsByClass('xg_module_body_wrapper', this.module)[0];
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
            <form class="xg_module_options"> \
                <fieldset> \
                    <dl>\
                        <dt><label for="' + this.widgetId + '_display">' + xg.profiles.nls.html('display') + '</label></dt>\
                        <dd> \
                            <select id="' + this.widgetId + '_display"> \
                                ' + displayOptionsHtml + ' \
                            </select> \
                        </dd> \
                        '+(this._sortOptionsJson?'\
                        <dt><label for="' + this.widgetId + '_sort">' + xg.profiles.nls.html('from') + '</label></dt>\
                        <dd> \
                            <select id="' + this.widgetId + '_sort"> \
                                ' + sortOptionsHtml + ' \
                            </select> \
                        </dd>':'')+'\
                        <dt><label for="' + this.widgetId + '_posts">' + xg.profiles.nls.html('show') + '</label></dt> \
                        <dd> \
                            <select id="' + this.widgetId + '_posts" class="short"> \
                                ' + postsOptionsHtml + ' \
                            </select> ' + xg.profiles.nls.html('posts') + '\
                        </dd> \
                    </dl>\
                    <p class="buttongroup"> \
                        <input type="submit" value="' + xg.profiles.nls.html('save') + '" class="button button-primary" /> \
                        <input type="button" value="' + xg.profiles.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                    </p> \
                </fieldset> \
            </form> \
            '))[0];
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
        xg.index.util.FormHelper.select(this._displaySet, dojo.byId(this.widgetId + '_display'  ));
        if(this._sortOptionsJson) {
            xg.index.util.FormHelper.select(this._sortSet   , dojo.byId(this.widgetId + '_sort'     ));
        }
        xg.index.util.FormHelper.select(this._postsSet, dojo.byId(this.widgetId + '_posts'  ));

        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },

    /**
     * Call-back function to update the module body
     *
     * @param ui    jQuery.ui Object      The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        dojo.io.bind({
            url: this._updateUrl,
            method: 'post',
            encoding: 'utf-8',
            mimetype: 'text/json',
            preventCache: true,
            content: {
                maxEmbedWidth: maxEmbedWidth
            },
            load: dojo.lang.hitch(this, function(type, data, event){
                this.module.getElementsByTagName('h2')[0].innerHTML = dojo.string.escape('html', data.embedTitle);
                dojo.html.getElementsByClass('xg_module_body_wrapper', this.module)[0].innerHTML = data.moduleBodyHtml;

                ui.item.css('visibility', '');

                // fix hover drag icon
                var handleDiv = this.module.getElementsByTagName('div')[0];
                if (dojo.html.hasClass(handleDiv, 'xg_handle')) dojo.style.hide(handleDiv);
            })
        });
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        this._displaySet    = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_display'  )).value;
        if(this._sortOptionsJson) {
            this._sortSet       = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_sort'     )).value;
        }
        this._postsSet      = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_posts'  )).value;
        this.body.innerHTML = xg.profiles.nls.html('loading');
        this.hideForm();
        dojo.io.bind({
            url: this._url,
            method: 'post',
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            content: {
                displaySet   : this._displaySet,
                sortSet      : this._sortSet,
                postsSet     : this._postsSet,
                maxEmbedWidth: maxEmbedWidth
            },
            load: dojo.lang.hitch(this, function(type, data, event){
                this.h2.innerHTML = dojo.string.escape('html', data.embedTitle);
                this.body.innerHTML = data.moduleBodyHtml;
                xg.shared.util.parseWidgets(this.body);
            })
        });
    }
});
