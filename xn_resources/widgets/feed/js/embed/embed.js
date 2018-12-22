dojo.provide('xg.feed.embed.embed');
dojo.provide('xg.feed.embed.embed.FeedModule');
dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');

dojo.widget.defineWidget('xg.feed.embed.embed.FeedModule', dojo.widget.HtmlWidget, {
    setValuesUrl: '',
    updateEmbedUrl: '',
    feedUrl: '',
    itemCount: '',
    showDescriptions: '',
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.feed.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if ((! this.form) || (this.form.style.height == "0px")) {
                this.showForm();
            } else {
                this.hideForm();
            }
        }));
        var foot = xg.$('.xg_module_foot', this.module);
		if (foot && xg.$('.xj_add_rss', foot)) {
            xg.listen(xg.$('.xj_add_rss', foot), 'onclick', this, function(evt) {
                xg.stop(evt);
	            if ((! this.form) || (this.form.style.height == "0px")) {
    	            this.showForm();
				}
            });
        }

        dojo.html.addClass(this.module, 'initialized_feed_module');
    },
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
				<form class="xg_module_options" style="display:none"> \
                    <fieldset> \
                        <dl> \
                            <dt><label for="' + this.widgetId + '_title">' + xg.feed.nls.html('title') + '</label></dt> \
                            <dd><input id="' + this.widgetId + '_title" type="text" class="textfield" /></dd> \
                            <dt><label for="' + this.widgetId + '_feed_url">' + xg.feed.nls.html('feedUrl') + '</label></dt> \
                            <dd><input id="' + this.widgetId + '_feed_url" type="text" class="textfield" /></dd> \
                            <dt><label for="' + this.widgetId + '_show_descriptions">' + xg.feed.nls.html('display') + '</label></dt> \
                            <dd>\
                                <select id="' + this.widgetId + '_show_descriptions"> \
                                    <option value="1">' + xg.feed.nls.html('titlesAndDescriptions') + '</option> \
                                    <option value="0">' + xg.feed.nls.html('titles') + '</option> \
                                </select>\
                            </dd> \
                            <dt><label for="' + this.widgetId + '_item_count">' + xg.feed.nls.html('show') + '</label></dt> \
                            <dd>\
                                <select id="' + this.widgetId + '_item_count" class="short"> \
                                    <option value="0">0</option> \
                                    <option value="1">1</option> \
                                    <option value="2">2</option> \
                                    <option value="3">3</option> \
                                    <option value="4">4</option> \
                                    <option value="5">5</option> \
                                    <option value="10">10</option> \
                                    <option value="20">20</option> \
                                </select> ' + xg.feed.nls.html('items') + '\
                            </dd> \
                        </dl>\
                        <p class="buttongroup"> \
                            <input type="submit" value="' + xg.feed.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.feed.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
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
        this.form.style.height = "0px";
        dojo.byId(this.widgetId + '_title').value = dojo.html.renderedTextContent(this.h2);
        dojo.byId(this.widgetId + '_feed_url').value = this.feedUrl;
        dojo.require('xg.index.util.FormHelper');
        xg.index.util.FormHelper.select(this.itemCount, dojo.byId(this.widgetId + '_item_count'));
        xg.index.util.FormHelper.select(this.showDescriptions, dojo.byId(this.widgetId + '_show_descriptions'));
        // form has 0 height, so scroll fieldset into view [Jon Aquino 2006-11-20]
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },

    /**
     * Call-back function to update the module body
     *
     * @param ui  jQuery.ui Object      The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        dojo.io.bind({
            url: this.updateEmbedUrl,
            method: 'post',
            content: { maxEmbedWidth: maxEmbedWidth },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                if ('error' in data) {
                    ui.item.css('visibility', '');
                    xg.shared.util.alert(data.error);
                } else {
                    this.removeBodyAndFooter();
                    dojo.lang.forEach(dojo.html.createNodesFromText(data.moduleBodyAndFooterHtml), dojo.lang.hitch(this, function(node) {
                        this.module.appendChild(node);
                    }));
                    xg.shared.util.fixImagesInIE(this.module.getElementsByTagName('img'), false);

                    ui.item.css('visibility', '');

                    // fix hover drag icon
                    var handleDiv = this.module.getElementsByTagName('div')[0];
                    if (dojo.html.hasClass(handleDiv, 'xg_handle')) dojo.style.hide(handleDiv);
                }
            })
        });
    },

    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var title = dojo.byId(this.widgetId + '_title').value;
        this.h2.innerHTML = dojo.string.escape('html', title);
        this.feedUrl = dojo.byId(this.widgetId + '_feed_url').value;
        this.itemCount = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_item_count')).value;
        this.showDescriptions = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_show_descriptions')).value;
        this.removeBodyAndFooter();
        this.module.appendChild(dojo.html.createNodesFromText('<div class="xg_module_body">' + xg.feed.nls.html('loading') + '</div>')[0]);
        this.hideForm();
        dojo.io.bind({
            url: this.setValuesUrl,
            method: 'post',
            content: { title: title, feedUrl: this.feedUrl, itemCount: this.itemCount, showDescriptions: this.showDescriptions },
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                this.removeBodyAndFooter();
                dojo.lang.forEach(dojo.html.createNodesFromText(data.moduleBodyAndFooterHtml), dojo.lang.hitch(this, function(node) {
                    this.module.appendChild(node);
                }));
                xg.shared.util.fixImagesInIE(this.module.getElementsByTagName('img'), false);
            })
        });
    },
    removeBodyAndFooter: function() {
        dojo.lang.forEach(dojo.html.getElementsByClass('xg_module_body', this.module), function(moduleBody) {
            dojo.dom.removeNode(moduleBody);
        });
        dojo.dom.removeNode(dojo.html.getElementsByClass('xg_module_foot', this.module)[0]);
    }
});
