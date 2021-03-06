dojo.provide('xg.index.embeddable.VideoPlayerFieldset');

dojo.require('dojo.lfx.*');

/**
 * The control panel for the video-player embeddable.
 */
dojo.widget.defineWidget('xg.index.embeddable.VideoPlayerFieldset', dojo.widget.HtmlWidget, {
    /** The endpoint for retrieving the <embed> code */
    _url: '',
    /** JSON array for the Select Source combobox; each item specifies: label, playlist, selected */
    _sourceOptions: '',
    /** Which choice of source the network is using for the facebook app*/
    _facebookSource: '',
    isContainer: true,
    fillInTemplate: function(args, frag) {
        var fieldset = this.getFragNodeRef(frag);
        var sourceOptions = dojo.json.evalJson(this._sourceOptions);
        var networkSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return ! option.userOwned; });
        var userSourceOptions = dojo.lang.filter(sourceOptions, function(option) { return option.userOwned; });
        var sourceSection = dojo.html.createNodesFromText(dojo.string.trim(' \
                <p><label for="videoplayer-source">' + xg.index.nls.html('selectSource') + '</label><br /> \
                    <select id="videoplayer-source"> \
                        ' + dojo.lang.map(networkSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + ' \
                        ' + (userSourceOptions.length ? '<optgroup label="' + xg.index.nls.html('myVideos') + '">' + dojo.lang.map(userSourceOptions, function(option) { return '<option' + (option.selected ? ' selected="selected" _makeSelected="1"' : '') + '>' + dojo.string.escape('html', option.label) + '</option>'; }).join(' ') + '</optgroup>' : '') + ' \
                    </select> \
                </p>'))[0];
        dojo.dom.insertAfter(sourceSection, fieldset.getElementsByTagName('p')[0]);
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
        var sourceSelect = sourceSection.getElementsByTagName('select')[0];
        dojo.event.connect(sourceSelect, 'onchange', dojo.lang.hitch(this, function(event) {
            dojo.html.getElementsByClass('share-on-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].videoID)?'none':'block';
            dojo.html.getElementsByClass('add-to-facebook', fieldset)[0].style.display = (this._facebookSource == sourceOptions[sourceSelect.selectedIndex].videoID)?'block':'none';
            dojo.io.bind({
                url: this._url.
                    replace(/noVideosMessage=[^&]+/, 'noVideosMessage=' + encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].noVideosMessage)).
                    replace(/videoID=[^&]+/, 'videoID=' + sourceOptions[sourceSelect.selectedIndex].videoID),
                method: 'get',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8',
                load: dojo.lang.hitch(this, function(type, data, event) {
                    var embedField = dojo.html.getElementsByClass('textfield', fieldset, 'input')[0];
                    embedField.value = data.embedCode;
                    dojo.lfx.highlight(embedField, /*#ff6*/[255,255,102], 300).play(600);
                    dojo.html.getElementsByClass('right', fieldset, 'div')[0].innerHTML = data.previewEmbedCode;

                    //new video player config returned
                    var matches = data.embedCode.match('config_url=([^&]*)')
                    if ((matches) && (matches.length > 1)) new_config_url = matches[1]

                    //replace config_url on the myspace link
                    var myspaceLink = dojo.html.getElementsByClass('service-myspace', fieldset)[0];
                    if (myspaceLink && new_config_url) {
                        var newonclick = myspaceLink.getAttributeNode('onClick').value.replace(
                            /config_url=[^&]*/ig,'config_url='+new_config_url).replace(/"/g,'&quot;')
                        newmyspaceLink = dojo.html.createNodesFromText('<a href="javascript:void(0);" onclick="' + newonclick +'" class="desc service-myspace">'+myspaceLink.innerHTML+'</a>')[0]
                        myspaceLink.parentNode.replaceChild(newmyspaceLink , myspaceLink);
                    }

                    //replace config_url on the launchpad menu
                    var clearspringlink = dojo.html.getElementsByClass('service-other', fieldset)[0];
                    if (clearspringlink && new_config_url) {
                        var launchpadmenu = eval('menu_'+clearspringlink.id)
                        launchpadmenu.Menu.options.config.config_url = new_config_url
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
