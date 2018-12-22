dojo.provide('xg.photo.embed.PhotoModule');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.EditUtil');
dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.photo.embed.PhotoModule', dojo.widget.HtmlWidget, {

    /** Endpoint for Ajax requests. */
    _setValuesUrl: '',
    _updateEmbedUrl: '',

    /** Whether to display the (slideshow) photos in random order. */
    _random: false,

    /** The current display type, e.g., thumbnails */
    _type: '',

    /** The current photo set, e.g., popular */
    _photoSet: '',

    /** The current album set, e.g., featured */
    _albumSet: '',

    /** The current row count */
    _num: '',

    /** JSON for the display type: slideshow, thumbnails, or albums */
    _typeOptions: '',

    /** JSON for the sets for the Slideshow and Thumbnails types */
    _photoSetOptions: '',

    /** JSON for the sets for the Albums type */
    _albumSetOptions: '',

    /** JSON for the number-of-rows options */
    _numOptions: '',

    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        if(this._setValuesUrl){
            dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.photo.nls.html('edit') + '</a></p>')[0], this.h2);
            dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if (! this.form || this.form.style.height == "0px") {
                    this.showForm();
                } else {
                    this.hideForm();
                }
            }));
        }
    },
    showForm: function() {
        var albumSetOptions = dj_eval(this._albumSetOptions);
        var photoSetOptions = dj_eval(this._photoSetOptions);
        var photoSetNonAlbumOptions = dojo.lang.filter(photoSetOptions, function(option) { return ! option.value.match(/album_/); });
        var photoSetAlbumOptions = dojo.lang.filter(photoSetOptions, function(option) { return option.value.match(/album_/); });
        var typeHtml = '';
        dojo.lang.forEach(dj_eval(this._typeOptions), function(option) {
            typeHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var numOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._numOptions), function(option) {
            numOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var albumSetOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._albumSetOptions), function(option) {
            albumSetOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        var photoSetOptionsHtml = '';
        dojo.lang.forEach(photoSetNonAlbumOptions, function(option) {
            photoSetOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        if (photoSetAlbumOptions.length) {
            photoSetOptionsHtml += '<optgroup label="' + xg.photo.nls.html('photosFromAnAlbum') + '">';
            dojo.lang.forEach(photoSetAlbumOptions, function(option) {
                photoSetOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
            });
            photoSetOptionsHtml += '</optgroup>';
        }
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        if(!this.form) {
            // A couple of the <dl>s have display:none; otherwise, formHeight will be too large [Jon Aquino 2008-03-07]
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl> \
                            <dt><label for="' + this.widgetId + '_type">' + xg.photo.nls.html('display') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_type"> \
                                    ' + typeHtml + ' \
                                </select> \
                            </dd> \
                        </dl> \
                        <dl id="'  + this.widgetId + '_photo_set_container"> \
                            <dt><label for="' + this.widgetId + '_photo_set">' + xg.photo.nls.html('from') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_photo_set"> \
                                    ' + photoSetOptionsHtml + ' \
                                </select> \
                            </dd> \
                            <dd class="nobr"><input id="' + this.widgetId + '_random" type="checkbox" class="checkbox" /> <label for="' + this.widgetId + '_random">' + xg.photo.nls.html('randomOrder') + '</label></dd> \
                        </dl> \
                        <dl id="'  + this.widgetId + '_album_set_container" style="display:none"> \
                            <dt><label for="' + this.widgetId + '_album_set">' + xg.photo.nls.html('from') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_album_set"> \
                                    ' + albumSetOptionsHtml + ' \
                                </select> \
                            </dd> \
                        </dl> \
                        <dl id="'  + this.widgetId + '_num_container" style="display:none"> \
                            <dt><label for="' + this.widgetId + '_num">' + xg.photo.nls.html('show') + '</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_num" class="short"> \
                                    ' + numOptionsHtml + ' \
                                </select> ' + xg.photo.nls.html('rows') + '\
                            </dd> \
                        </dl> \
                        <p class="buttongroup"> \
                            <input type="submit" value="' + xg.photo.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.photo.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
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
            dojo.event.connect(dojo.byId(this.widgetId + '_type'), 'onchange', dojo.lang.hitch(this, function(event) {
                this.updateFieldDisplay();
            }));
        } else {
            dojo.html.removeClass(this.form, 'collapsed');
         }
        this.form.style.height = "0px";
        xg.index.util.FormHelper.select(this._photoSet, dojo.byId(this.widgetId + '_photo_set'));
        xg.index.util.FormHelper.select(this._albumSet, dojo.byId(this.widgetId + '_album_set'));
        xg.index.util.FormHelper.select(this._num, dojo.byId(this.widgetId + '_num'));
        xg.index.util.FormHelper.select(this._type, dojo.byId(this.widgetId + '_type'));
        dojo.byId(this.widgetId + '_random').checked = this._random;
        var editButton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editButton);
        this.updateFieldDisplay();
    },
    /**
     * Shows or hides various fields, as appropriate.
     */
    updateFieldDisplay: function() {
        dojo.html.setShowing(dojo.byId(this.widgetId + '_num_container'), dojo.byId(this.widgetId + '_type').value != 'slideshow');
        dojo.html.setShowing(dojo.byId(this.widgetId + '_random').parentNode, dojo.byId(this.widgetId + '_type').value == 'slideshow');
        dojo.html.setShowing(dojo.byId(this.widgetId + '_album_set_container'), dojo.byId(this.widgetId + '_type').value == 'albums');
        dojo.html.setShowing(dojo.byId(this.widgetId + '_photo_set_container'), dojo.byId(this.widgetId + '_type').value != 'albums');
    },
    hideForm: function() {
        var editButton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editButton);
    },

    /**
     * Call-back function to update the module body
     *
     * @param ui    jQuery.ui Object      The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        var columnCount = this.module.parentNode.getAttribute('_columncount');
        dojo.io.bind({
            url: this._updateEmbedUrl,
            method: 'post',
            content: { maxEmbedWidth: maxEmbedWidth, columnCount: columnCount },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                if ('error' in data) {
                    ui.item.css('visibility', '');
                    xg.shared.util.alert(data.error);
                } else {
                    dojo.lang.forEach(dojo.html.getElementsByClass('xg_module_body', this.module), function(nd) {
                        dojo.dom.removeNode(nd);
                    });
                    var footer = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
                    if (footer) { dojo.dom.removeNode(footer); }
                    var container = dojo.html.getElementsByClass('container', this.module)[0];
                    if (container) { dojo.dom.removeNode(container); }
                    container = dojo.html.createNodesFromText('<div class="container"></div>')[0];
                    this.module.appendChild(container);
                    container.innerHTML = data.moduleBodyAndFooterHtml;
                    if (dojo.byId('playerHtml')) {
                        dojo.byId('playerHtml').parentNode.innerHTML = dojo.byId('playerHtml').value;
                    }

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
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        var columnCount = this.module.parentNode.getAttribute('_columncount');
        this._photoSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_photo_set')).value;
        this._albumSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_album_set')).value;
        this._num = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_num')).value;
        this._type = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_type')).value;
        this._random = this._type == 'slideshow' && dojo.byId(this.widgetId + '_random').checked;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { maxEmbedWidth: maxEmbedWidth, columnCount: columnCount, photoSet: this._photoSet, albumSet: this._albumSet, num: this._num, type: this._type, random: this._random ? 1 : 0 },
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                dojo.lang.forEach(dojo.html.getElementsByClass('xg_module_body', this.module), function(nd) {
                    dojo.dom.removeNode(nd);
                });
                var footer = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
                if (footer) { dojo.dom.removeNode(footer); }
                var container = dojo.html.getElementsByClass('container', this.module)[0];
                if (container) { dojo.dom.removeNode(container); }
                container = dojo.html.createNodesFromText('<div class="container"></div>')[0];
                this.module.appendChild(container);
                container.innerHTML = data.moduleBodyAndFooterHtml;
                if (dojo.byId('playerHtml')) {
                    dojo.byId('playerHtml').parentNode.innerHTML = dojo.byId('playerHtml').value;
                }
            })
        });
    }
});
