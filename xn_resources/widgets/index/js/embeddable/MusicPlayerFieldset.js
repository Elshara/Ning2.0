dojo.provide('xg.index.embeddable.MusicPlayerFieldset');

dojo.require('dojo.lfx.*');

/**
 * The control panel for the music-player embeddable.
 */
dojo.widget.defineWidget('xg.index.embeddable.MusicPlayerFieldset', dojo.widget.HtmlWidget, {
    /** The endpoint for retrieving the <embed> code */
    _url: '',
    /** JSON array for the Select Source combobox; each item specifies: label, playlist, selected */
    _sourceOptions: '',
    /** JSON array for the Player Size combobox; each item specifies: label, width, selected */
    _sizeOptions: '',
    _showPlaylist: '',
    /** Which choice of source the network is using*/
    _facebookSource: '',
    isContainer: true,
    fillInTemplate: function(args, frag) {
        var fieldset = this.getFragNodeRef(frag);
        var sourceOptions = dojo.json.evalJson(this._sourceOptions);
        var networkSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return ! option.userOwned; });
        var userSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return option.userOwned; });
        var sizeOptions = dojo.json.evalJson(this._sizeOptions);
        var dl1 = dojo.html.createNodesFromText(dojo.string.trim(' \
                <p><label for="musicplayer-source">' + xg.index.nls.html('selectSource') + '</label><br /> \
                    <select id="musicplayer-source"> \
                        ' + dojo.lang.map(networkSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + ' \
                        ' + (userSourceOptions.length ? '<optgroup label="' + xg.index.nls.html('myMusic') + '">' + dojo.lang.map(userSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + '</optgroup>' : '') + ' \
                    </select> \
                </p>'))[0];
        var dl2 = dojo.html.createNodesFromText(dojo.string.trim(' \
                <p><label for="musicplayer-size">' + xg.index.nls.html('playerSize') + '</label><br /> \
                    <select id="musicplayer-size"> \
                    ' + dojo.lang.map(sizeOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + ' \
                    </select><br /> \
                    <input id="musicplayer-showplaylist" name="playlist" type="checkbox" class="checkbox"' + (this._showPlaylist ? 'checked="checked"' : '') + '>' + xg.index.nls.html('showPlaylist') + ' \
                </p>'))[0];
        dojo.dom.insertAfter(dl1, fieldset.getElementsByTagName('p')[0]);
        dojo.dom.insertAfter(dl2, dl1);
        var sourceSelect = dl1.getElementsByTagName('select')[0];
        var sizeSelect = dl2.getElementsByTagName('select')[0];
        if (dojo.render.html.ie) {
            //  Workaround for BAZ-3749 - IE ignores the selected attributes on
            //    the options we're adding dynamically!  In fact, IE _lies_ about
            //    whether the option is selected!  Hooray for IE!
            dojo.lang.forEach(document.getElementsByTagName('option'), function(option) {
                if (dojo.html.getAttribute(option, '_makeSelected')) {
                    option.selected = true;
                }
            });
        }
        var showPlaylistCheckbox = dojo.byId('musicplayer-showplaylist');
        dojo.event.connect([sourceSelect, sizeSelect], 'onchange', dojo.lang.hitch(this, function(event) {
            dojo.html.getElementsByClass('share-on-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].playlist)?'none':'block';
            dojo.html.getElementsByClass('add-to-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].playlist)?'block':'none';
            dojo.io.bind({
                method: 'get',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8',
                url: this._url.replace(/width=\d+/, 'width=' + sizeOptions[sizeSelect.selectedIndex].width).
                    replace(/noMusicMessage=[^&]+/, 'noMusicMessage=' + encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].noMusicMessage)).
                    replace(/playlistUrl=[^&]+/, 'playlistUrl=' + sourceOptions[sourceSelect.selectedIndex].url).
                    replace(/displayContributor=[^&]+/, 'displayContributor=' +  encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].displayContributor)).
                    replace(/showPlaylist=[^&]+/, 'showPlaylist=' + (showPlaylistCheckbox.checked ? 'true' : '')),
                load: function(type, data, event) {
                    var embedField = dojo.html.getElementsByClass('textfield', fieldset, 'input')[0];
                    embedField.value = data.embedCode;
                    dojo.lfx.highlight(embedField, /*#ff6*/[255,255,102], 300).play(600);
                    dojo.html.getElementsByClass('right', fieldset, 'div')[0].innerHTML = data.previewEmbedCode;
                    //new playlist returned
                    var matches = data.embedCode.match('playlist_url=([^&]*)')
                    if ((matches) && (matches.length > 1)) new_playlist_url = encodeURIComponent(matches[1])

                    //replace playlist_url on the myspace link
                    var myspaceLink = dojo.html.getElementsByClass('service-myspace', fieldset)[0];
                    if (myspaceLink) {
                        var newonclick = myspaceLink.getAttributeNode('onClick').value.replace(
                            /playlist_url=[^&]*/ig,'playlist_url='+new_playlist_url).replace(/"/g,'&quot;')
                        newmyspaceLink = dojo.html.createNodesFromText('<a href="javascript:void(0);" onclick="' + newonclick +'" class="desc service-myspace">'+myspaceLink.innerHTML+'</a>')[0]
                        myspaceLink.parentNode.replaceChild(newmyspaceLink , myspaceLink);
                    }

                    //replace playlist_url on the launchpad menu
                    var clearspringlink = dojo.html.getElementsByClass('service-other', fieldset)[0];
                    if (clearspringlink) {
                        var launchpadmenu = eval('menu_'+clearspringlink.id)
                        launchpadmenu.Menu.options.config.playlist_url = new_playlist_url
                    }
                    var launchpadcontainer = document.getElementById('csLaunchpadTarget_'+clearspringlink.id)
                    if (launchpadcontainer) {
                        if (launchpadcontainer.firstChild) launchpadcontainer.removeChild(launchpadcontainer.firstChild)
                        csLaunchpad = $Launchpad.CreateMenu({
                          actionElement: clearspringlink,
                          servicesInclude: launchpadmenu.Menu.options.servicesInclude,
                          customCSS: launchpadmenu.Menu.options.customCSS,
                          wid: launchpadmenu.Menu.options.wid,
                          config: launchpadmenu.Menu.options.config,
                          targetElement: launchpadcontainer
                        });
                    }
                }
            });
        }));
        dojo.event.connect(showPlaylistCheckbox, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.io.bind({
                url: this._url.replace(/width=\d+/, 'width=' + sizeOptions[sizeSelect.selectedIndex].width).
                    replace(/playlistUrl=[^&]+/, 'playlistUrl=' + sourceOptions[sourceSelect.selectedIndex].url).
                    replace(/showPlaylist=[^&]+/, 'showPlaylist=' + (showPlaylistCheckbox.checked ? 'true' : '')),
                method: 'get',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8',
                load: function(type, data, event) {
                    var embedField = dojo.html.getElementsByClass('textfield', fieldset, 'input')[0];
                    embedField.value = data.embedCode;
                    dojo.lfx.highlight(embedField, /*#ff6*/[255,255,102], 300).play(600);
                    dojo.html.getElementsByClass('right', fieldset, 'div')[0].innerHTML = data.previewEmbedCode;
                }
            });
        }));
    }
});
