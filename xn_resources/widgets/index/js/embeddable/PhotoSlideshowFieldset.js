dojo.provide('xg.index.embeddable.PhotoSlideshowFieldset');

dojo.require('dojo.lfx.*');

/**
 * The control panel for the photo-slideshow embeddable.
 */
dojo.widget.defineWidget('xg.index.embeddable.PhotoSlideshowFieldset', dojo.widget.HtmlWidget, {
    /** The endpoint for retrieving the <embed> code */
    _url: '',
    /** JSON array for the Select Source combobox; each item specifies: label, photoSet, selected */
    _sourceOptions: '',
    /** JSON array for the Player Size combobox; each item specifies: label, width, height, selected */
    _sizeOptions: '',
    /** Which choice of source the network is using for the facebook app*/
    _facebookSource: '',
    isContainer: true,
    fillInTemplate: function(args, frag) {
        var fieldset = this.getFragNodeRef(frag);
        var sourceOptions = dojo.json.evalJson(this._sourceOptions);
        var nonAlbumSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return ! option.photoSet.match(/album_/); });
        var albumSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return option.photoSet.match(/album_/); });
        var sizeOptions = dojo.json.evalJson(this._sizeOptions);
        var dl1 = dojo.html.createNodesFromText(dojo.string.trim(' \
                <p><label for="slideshow-source">' + xg.index.nls.html('selectSource') + '</label><br /> \
                    <select id="slideshow-source"> \
                        ' + dojo.lang.map(nonAlbumSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + ' \
                        ' + (albumSourceOptions.length ? '<optgroup label="' + xg.index.nls.html('myAlbums') + '">' + dojo.lang.map(albumSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + '</optgroup>' : '') + ' \
                    </select> \
                </p>'))[0];
        var dl2 = dojo.html.createNodesFromText(dojo.string.trim(' \
                <p><label for="slideshow-size">' + xg.index.nls.html('playerSize') + '</label><br /> \
                    <select id="slideshow-size"> \
                        ' + dojo.lang.map(sizeOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + ' \
                    </select> \
                </p>'))[0];
        dojo.dom.insertAfter(dl1, fieldset.getElementsByTagName('p')[0]);
        dojo.dom.insertAfter(dl2, dl1);
        if (sourceOptions.length == 1) { dl1.style.display = 'none'; }
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
        var sourceSelect = dl1.getElementsByTagName('select')[0];
        var sizeSelect = dl2.getElementsByTagName('select')[0];
        dojo.event.connect([sourceSelect, sizeSelect], 'onchange', dojo.lang.hitch(this, function(event) {
            dojo.html.getElementsByClass('share-on-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].photoSet)?'none':'block';
            dojo.html.getElementsByClass('add-to-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].photoSet)?'block':'none';
            var url = this._url.
                    replace(/photoSet=[^&]+/, 'photoSet=' + sourceOptions[sourceSelect.selectedIndex].photoSet).
                    replace(/noPhotosMessage=[^&]+/, 'noPhotosMessage=' + encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].noPhotosMessage)).
                    replace(/width=[^&]+/, 'width=' + sizeOptions[sizeSelect.selectedIndex].width).
                    replace(/height=[^&]+/, 'height=' + sizeOptions[sizeSelect.selectedIndex].height);
            dojo.io.bind({
                url: url,
                method: 'get',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8',
                load: dojo.lang.hitch(this, function(type, data, event) {
                    var embedField = dojo.html.getElementsByClass('textfield', fieldset, 'input')[0];
                    embedField.value = data.embedCode;
                    dojo.lfx.highlight(embedField, /*#ff6*/[255,255,102], 300).play(600);
                    dojo.html.getElementsByClass('right', fieldset, 'div')[0].innerHTML = data.previewEmbedCode;

                    //new slideshow feed returned
                    var matches = data.embedCode.match('feed_url=([^&]*)')
                    if ((matches) && (matches.length > 1)) new_feed_url = matches[1]

                    //replace feed_url on the myspace link
                    var myspaceLink = dojo.html.getElementsByClass('service-myspace', fieldset)[0];
                    if (myspaceLink && new_feed_url) {
                        var newonclick = myspaceLink.getAttributeNode('onClick').value.replace(
                            /feed_url=[^&]*/ig,'feed_url='+new_feed_url).replace(/"/g,'&quot;')
                        newmyspaceLink = dojo.html.createNodesFromText('<a href="javascript:void(0);" onclick="' + newonclick +'" class="desc service-myspace">'+myspaceLink.innerHTML+'</a>')[0]
                        myspaceLink.parentNode.replaceChild(newmyspaceLink , myspaceLink);
                    }

                    //replace feed_url on the launchpad menu
                    var clearspringlink = dojo.html.getElementsByClass('service-other', fieldset)[0];
                    if (clearspringlink && new_feed_url) {
                        var launchpadmenu = eval('menu_'+clearspringlink.id)
                        launchpadmenu.Menu.options.config.feed_url = new_feed_url
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
                })
            });
        }));
    }
});
