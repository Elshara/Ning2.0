dojo.provide('xg.opensocial.embed.OpenSocialModule');
dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');
dojo.require('xg.opensocial.embed.moduleBodyAndFooter');

dojo.widget.defineWidget('xg.opensocial.embed.OpenSocialModule', dojo.widget.HtmlWidget, {
    logOpenSocial: null,
    setValuesUrl: '',
    appUrl: '',
    removeBoxUrl: '',
    removeAppUrl: '',
    canSendMessages: '',
    canAddActivities: '',
    gadgetTitle: '',
    installedByUrl: '',
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.opensocial.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if ((! this.form) || (this.form.style.height == "0px")) {
                this.showForm();
            } else {
                this.hideForm();
            }
        }));
        dojo.html.addClass(this.module, 'initialized_opensocial_module');
    },
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        if(!this.form) {
            s = ' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl>'
            if (this.installedByUrl) {
                s += '<dt><label for="' + this.widgetId + '_app_url">' + xg.opensocial.nls.html('appUrl') + '</label></dt> \
                      <dd><input type="text" class="textfield" disabled="disabled" id="' + this.widgetId + '_app_url" /></dd>';
            } else {
                s += '<input type="hidden" id="' + this.widgetId + '_app_url" />';
            }
            if (this.logOpenSocial) {
                s +=           '<dt><input id="' + this.widgetId + '_canAddActivities" type="checkbox" class="checkbox" /></dt> \
                                <dd class="item_text"><label for="' + this.widgetId + '_canAddActivities">' + xg.opensocial.nls.html('canShowActivities') + '</label></dd>';
            }
            s +=           '<dt><input id="' + this.widgetId + '_canSendMessages" type="checkbox" class="checkbox" /></dt> \
                            <dd class="item_text"><label for="' + this.widgetId + '_canSendMessages">' + xg.opensocial.nls.html('allowSendAlerts') + '</label></dd> \
                        </dl>\
                        <p class="buttongroup" style="border-top: 0; border-bottom: 1px solid #DDDDDD; padding-bottom: 0.7em; margin-bottom: 0.5em"> \
                            <input type="submit" value="' + xg.opensocial.nls.html('save') + '" class="button submit"/> \
                            <input type="button" value="' + xg.opensocial.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                        </p> \
                        <p class="align-right">\
                            <a id="' + this.widgetId + '_remove_box" href="#">' + xg.opensocial.nls.html('removeBox') + '</a>\
                            |\
                            <a id="' + this.widgetId + '_remove_application" href="#">' + xg.opensocial.nls.html('removeApplication') + '</a>\
                            <a id="' + this.widgetId + '_whats_this" href="#">\
                            <img title="' + xg.opensocial.nls.html('whatsThis') + '" src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/help.gif') + '" alt="?" class="help" />\
                            </a>\
                        </p>\
                    </fieldset> \
                </form>';
            this.form = dojo.html.createNodesFromText(dojo.string.trim(s))[0];
            dojo.dom.insertAfter(this.form, this.head);

            //TODO: making forms like this is disgusting, must abstract this out, clean it up
            this.removeBoxForm = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form id="' + this.widgetId + '_remove_box_form" style="display:none" method="POST" action="' + this.removeBoxUrl +'"> <input type="hidden" name="appUrl" value="' + this.appUrl + '" /></form>\
            '))[0];
            dojo.dom.prependChild(xg.shared.util.createCsrfTokenHiddenInput(), this.removeBoxForm);
            dojo.dom.insertAfter(this.removeBoxForm, this.form);

            this.removeApplicationForm = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form id="' + this.widgetId + '_remove_application_form" style="display:none" method="POST" action="' + this.removeAppUrl +'"> <input type="hidden" name="appUrl" value="' + this.appUrl + '" /></form>\
            '))[0];
            dojo.dom.prependChild(xg.shared.util.createCsrfTokenHiddenInput(), this.removeApplicationForm);
            dojo.dom.insertAfter(this.removeApplicationForm, this.removeBoxForm);

            this.formHeight = this.form.offsetHeight;

            // connect the various events only once when the form is created
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_remove_box'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                xg.shared.util.confirm({title: xg.opensocial.nls.html('removeBox'),
                                        bodyHtml: xg.opensocial.nls.html('removeBoxText', this.gadgetTitle),
                                        okButtonText: xg.opensocial.nls.html('removeBox'),
                                        onOk: dojo.lang.hitch(dojo.byId(this.widgetId + '_remove_box_form'), function() { this.submit();})
                                       });
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_remove_application'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                xg.shared.util.confirm({title: xg.opensocial.nls.html('removeApplication'),
                                        bodyText:  xg.opensocial.nls.html('removeApplicationText'),
                                        okButtonText: xg.opensocial.nls.html('removeApplication'),
                                        onOk: dojo.lang.hitch(dojo.byId(this.widgetId + '_remove_application_form'), function() { this.submit();})
                                       });
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_whats_this'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                xg.shared.util.alert({title: xg.opensocial.nls.html('removeBoxAndRemoveApplication'),
                                        bodyHtml: xg.opensocial.nls.html('removeBoxAndRemoveApplicationHelp')
                                       });
            }));
        } else {
            dojo.html.removeClass(this.form, 'collapsed');
        }
        this.form.style.height = "0px";
        dojo.byId(this.widgetId + '_app_url').value = this.appUrl;
        if (dojo.byId(this.widgetId + '_canAddActivities')) {
            dojo.byId(this.widgetId + '_canAddActivities').checked = this.canAddActivities;
        }
        dojo.byId(this.widgetId + '_canSendMessages').checked = this.canSendMessages;
        // form has 0 height, so scroll fieldset into view [Jon Aquino 2006-11-20]
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this.canSendMessages = dojo.byId(this.widgetId + '_canSendMessages').checked;
        if (dojo.byId(this.widgetId + '_canAddActivities')) {
            this.canAddActivities = dojo.byId(this.widgetId + '_canAddActivities').checked;
        }
        //this.removeBodyAndFooter();
        //this.module.appendChild(dojo.html.createNodesFromText('<div class="xg_module_body">' + xg.opensocial.nls.html('loading') + '</div>')[0]);
        this.hideForm();
        dojo.io.bind({
            url: this.setValuesUrl,
            method: 'post',
            // the following three attrs must be above content or they get flagged by a broken unit test (Syntax05CmdlineTest::testDojoIoBindProperties) [ywh 2008-09-11]
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            content: {
                    appUrl: this.appUrl,
                    // You can't be editing the embed settings if you can't see them so this should always be true.
                    // We cannot omit it because that would evaluate to false and the box would be removed.
                    isOnMyPage: true,
                    canSendMessages: (this.canSendMessages ? 1 : 0),
                    canAddActivities: (this.canAddActivities ? 1 : 0)
            },
            load: dojo.lang.hitch(this, function(type, data, event){
                /*this.removeBodyAndFooter();
                dojo.lang.forEach(dojo.html.createNodesFromText(data.moduleBodyAndFooterHtml), dojo.lang.hitch(this, function(node) {
                    this.module.appendChild(node);
                }));
                xg.shared.util.fixImagesInIE(this.module.getElementsByTagName('img'), false);
                xg.opensocial.embed.moduleBodyAndFooter.renderGadgets({index: data.gadget.index,
                                                          url: data.gadget.appUrl,
                                                          domain: data.gadget.domain,
                                                          secureToken: data.gadget.secureToken,
                                                          baseUrl: data.baseUrl,
                                                          renderUrl: data.renderUrl,
                                                          viewParams: data.viewParams,
                                                          view: data.openSocialView,
                                                          viewerId: data.gadget.viewerName,
                                                          ownerId: data.gadget.ownerName,
                                                          iframeUrl: data.gadget.iframeUrl});*/
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
