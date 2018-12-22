dojo.provide('xg.music.embed.MusicModule');
dojo.require('xg.shared.EditUtil');

/**
 * The Music module, which plays playlists on the homepage and profile page.
 */
dojo.widget.defineWidget('xg.music.embed.MusicModule', dojo.widget.HtmlWidget, {

    /** Endpoint for saving the values */
    _setValuesUrl: '',

    /** Which playlist to play (playlist, recent, featured, popular, external) */
    _playlistSet: '',

    /** The url of the playlist that the player should use */
    _playlistUrl: '',

    /** Whether the player should start the first song automatically */
    _autoplay: '',

    /** Whether the player should play in a random order */
    _shuffle: '',

    /** If the player should display the playlist or only the current playing track */
    _showplaylist: '',

    _columnCount: 1,

    /** JSON array of objects, each with the following properties: label, value (for the playlist source dropdown) */
    _playlistOptionsJson: '',

    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        if(this.h2) {
            dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.music.nls.html('edit') + '</a></p>')[0], this.h2);
            dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if ((! this.form) || (this.form.style.height == "0px")) {
                    this.showForm();
                } else {
                    this.hideForm();
                }
            }));
        }
    },
    showForm: function() {
        var playlistOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._playlistOptionsJson), function(option) {
            playlistOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });

        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
            <form class="xg_module_options"> \
                <fieldset> \
                    <dl> \
                        <dt><input type="checkbox" class="checkbox" id="' + this.widgetId + '_autoplay" '+((this._autoplay=='true')?'checked="true"':'')+' /></dt> \
                        <dd><label for="' + this.widgetId + '_autoplay">' + xg.music.nls.html('autoplay') + '</label></dd> \
                        <dt><input type="checkbox" class="checkbox" id="' + this.widgetId + '_showplaylist" '+((this._showplaylist=='true')?'checked="true"':'')+' /></dt> \
                        <dd><label for="' + this.widgetId + '_showplaylist">' + xg.music.nls.html('showPlaylist') + '</label></dd> \
                        <dt><input type="checkbox" class="checkbox" id="' + this.widgetId + '_shuffle" '+((this._shuffle=='true')?'checked="true"':'')+' /></dt> \
                        <dd><label for="' + this.widgetId + '_shuffle">' + xg.music.nls.html('shufflePlaylist') + '</label></dd> \
                        <dt><label for="' + this.widgetId + '_sourceoption">' + xg.music.nls.html('playLabel') + '</label></dt> \
                        <dd> \
                            <select id="' + this.widgetId + '_sourceoption"> \
                                ' + playlistOptionsHtml + ' \
                            </select> \
                        </dd> \
                        <div id="'+this.widgetId + '_urlfield"> \
                            <dt><label for="' + this.widgetId + '_playlisturl">' + xg.music.nls.html('url') + '</label></dt> \
                            <dd> \
                                <input type="text" class="textfield" id="' + this.widgetId + '_playlisturl" value="'+this._playlistUrl+'"/><br/> \
                                <small>' + xg.music.nls.html('rssXspfOrM3u') + '</small> \
                            </dd> \
                        </div> \
                    </dl> \
                    <p class="buttongroup"> \
                        <input type="submit" value="' + xg.music.nls.html('save') + '" class="button button-primary"/> \
                        <input type="button" value="' + xg.music.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                    </p> \
                </fieldset> \
            </form>'))[0];

            this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
            dojo.dom.insertAfter(this.form, this.head);
            // BAZ-6294 - Huy Hong 02/29/08: subtracting 55px from the offsetHeight to compensate for extra space that I can't attribute to any HTML elements. Subtracts enough space except for in IE7.
            // TODO: Find out where the 55 comes from and fix it completely, including IE7.
            this.formHeight = this.form.offsetHeight - 55;
            this.form.style.height = "0px";
            dojo.event.connect(dojo.byId(this.widgetId + '_sourceoption'), 'onchange', dojo.lang.hitch(this, this.updateUrlField));

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
        dojo.require('xg.index.util.FormHelper');
        xg.index.util.FormHelper.select(this._playlistSet, dojo.byId(this.widgetId + '_sourceoption'));
        this.updateUrlField();
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },

    /**
     * Shows or hides the URL field depending on whether the podcast option is selected.
     */
    updateUrlField: function() {
        // TODO: Use a jQuery or Dojo function instead of settings style.display [Jon Aquino 2008-09-24]
        if(xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_sourceoption')).value != 'podcast'){
            dojo.byId(this.widgetId + '_urlfield').style.display='none';
        } else {
            dojo.byId(this.widgetId + '_urlfield').style.display='block';
        }
    },

    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._autoplay = dojo.byId(this.widgetId + '_autoplay').checked
        this._showplaylist = dojo.byId(this.widgetId + '_showplaylist').checked
        this._shuffle = dojo.byId(this.widgetId + '_shuffle').checked
        this._playlistSet = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_sourceoption')).value;
        this._playlistUrl = dojo.byId(this.widgetId + '_playlisturl').value
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            content: {  autoplay: this._autoplay,
                        shuffle: this._shuffle,
                        columnCount: this._columnCount,
                        showPlaylist: this._showplaylist,
                        playlistSet: this._playlistSet,
                        playlistUrl: this._playlistUrl},
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
//                if (this.showingMessage()) { return; }
                // var playerHTML = dojo.byId('playerHtml');
                // playerHTML.parentNode.innerHTML = playerHTML.value;
            })
        });

    },
    cancel: function() {

    }
});