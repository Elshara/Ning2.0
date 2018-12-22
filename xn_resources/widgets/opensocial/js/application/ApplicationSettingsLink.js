dojo.provide('xg.opensocial.application.ApplicationSettingsLink');

dojo.require('xg.shared.util');

/**
 * An <a> element that allows the user to update their application setttings.
 */
dojo.widget.defineWidget('xg.opensocial.application.ApplicationSettingsLink', dojo.widget.HtmlWidget, {

    /** Whether the NC currently allows logging of application activity */
    _logOpenSocial: false,
    /** Endpoint that saves the settings */
    _setValuesUrl: '',
    /** Whether the user currently allows this gadget to have a box on My Page */
    _isOnMyPage: false,
    /** Whether the user currently allows this gadget to add activities. */
    _canAddActivities: false,
    /** Whether the user currently allows this gadget to send messages. */
    _canSendMessages: false,
    
    /** Popup dialog for display to user to change application settings. */
    dialog: null,
    /** The <a> element */
    a: null,
    
    /** The form in the popup dialog */
    form: null,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.a = this.getFragNodeRef(frag);
        dojo.event.connect(this.a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.showForm();
        }));
    },

    /**
     * Display the application settings form.
     */
    showForm: function() {
        var s = '<div class="xg_floating_module"> \
                    <div class="xg_floating_container xg_floating_container_wide"> \
                        <div class="xg_module_head"> \
                            <h2>' + xg.opensocial.nls.html('applicationSettings') + '</h2> \
                        </div> \
                        <div class="xg_module_body"> \
                            <p>' + xg.opensocial.nls.html('allowThisApplicationTo') + '</p> \
                            <form method="post" action="' + this._setValuesUrl + '" id="xg_application_settings_form"> \
                      		    <ul class="nobullets"> \
                                    <li><label><input type="checkbox" name="isOnMyPage" value="1" class="checkbox" ' + (this._isOnMyPage ? 'checked="checked"' : '') + ' />' + xg.opensocial.nls.html('isOnMyPage') + '</label></li>';
        if (this._logOpenSocial) {
            s += '<li><label><input type="checkbox" name="canAddActivities" value="1" class="checkbox" ' + (this._canAddActivities ? 'checked="checked"' : '') + ' />' + xg.opensocial.nls.html('canAddActivities') + '</label></li>';
        }
        s +=  '                      <li><label><input type="checkbox" name="canSendMessages" value="1" class="checkbox" ' + (this._canSendMessages ? 'checked="checked"' : '') + ' />' + xg.opensocial.nls.html('canSendAlerts') + '</label></li> \
                      		    </ul> \
                                <p class="buttongroup"> \
                                    <input type="button" value="' + xg.opensocial.nls.html('updateSettings') + '" class="button submit"/> \
                                    <input type="button" value="' + xg.opensocial.nls.html('cancel') + '" class="button" /> \
				                </p> \
                            </form> \
                        </div> \
                    </div> \
                </div>';
        this.dialog = dojo.html.createNodesFromText(dojo.string.trim(s))[0];
        xg.shared.util.showOverlay();
        document.body.appendChild(this.dialog);
        xg.shared.util.fixDialogPosition(this.dialog);
        this.form = dojo.byId('xg_application_settings_form');
        this.form.appendChild(xg.shared.util.createCsrfTokenHiddenInput());
        dojo.event.connect(dojo.html.getElementsByClass('button', this.dialog)[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.hideOverlay();
            var content = {}, el = this.form.elements;
            for (var i = 0; i<el.length;i++) {
                if (el[i].name) {
                    content[el[i].name] = (el[i].checked ? 1 : 0);
                }
            }
            dojo.dom.removeNode(this.dialog);
            content['xn_out'] = 'json';
    	    dojo.io.bind({
                'method': 'post',
                mimetype: 'text/json',
                preventCache: true,
                encoding: 'utf-8',
                content: content,
	    		'url': xg.shared.util.addParameter(this._setValuesUrl, 'rand', new Date().getTime()), // adding the rand for IE6
	    		'load': dojo.lang.hitch(this, function(type, data, event2) {
                    x$('.topmsg').hide();
                    this._isOnMyPage = content['isOnMyPage'];
                    this._canAddActivities = content['canAddActivities'];
                    this._canSendMessages = content['canSendMessages'];
                    if (data && data['result'] && data['result']['addToMyPage'] === false) {
                        dojo.html.show(dojo.byId('xg_applications_settings_add_application_error'));
                        this._isOnMyPage = false;
                    } else {
                        dojo.html.show(dojo.byId('xg_applications_settings_updated_success'));
                    }

                })
            });
        }));
        dojo.event.connect(dojo.html.getElementsByClass('button', this.dialog)[1], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(this.dialog);
            xg.shared.util.hideOverlay();
        }));
    }
});
