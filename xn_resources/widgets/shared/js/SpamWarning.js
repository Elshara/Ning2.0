dojo.provide('xg.shared.SpamWarning');
dojo.require('xg.shared.util');

/**
 * A link that reveals more links.
 */
dojo.widget.defineWidget('xg.shared.SpamWarning', dojo.widget.HtmlWidget, {
 	/*
	 * Hash with message parts: part_name:part_text
	 * @var json
	 */
	_messageParts: "",
	/*
	 * List of forms ids to attach to.
	 * When any form is submitted all textarea with name ~ /message/ will be added as user message and sent to the server to check.
	 *
	 * @var json
	 */
	_attachTo: "",
    // TODO: Maybe remove the _url parameter, as it is always /main/invitation/checkMessageForSpam [Jon Aquino 2008-06-12]
	_url: "",

    /**
     * Initializes this widget.
     */
    fillInTemplate: function(args, frag) {
    	this._attachTo = dojo.json.evalJson(this._attachTo);
    	for(var i = 0;i<this._attachTo.length;i++) {
			this.installHandler(this._attachTo[i]);
		}
	},

	installHandler: function(id) {
		var el = dojo.byId(id), _this = this;
		if (el) {
			dojo.event.connect(el, 'onsubmit', function (event){
				_this.doCheck( event, el,
					function () { xg.shared.util.hideOverlay(); el.submit() },
					function () { xg.shared.util.hideOverlay(); },
					function () { });
			} );
		}
	},

	doCheck: function(event, form, okCallback, failCallback, preDisplayCallback) {
		if (event) {
			dojo.event.browser.stopEvent(event);
		}

		var ta = form.getElementsByTagName('textarea'), msgParts, msgs = [], _this = this;

		for (var i = 0; i<ta.length; i++) {
			if (ta[i].name.match(/message/i)) {
				msgs.push(ta[i].value);
			}
		}

		if (msgs.length > 1) {
			return alert("Assertion failed: SpamWarning form cannot contain more than 1 TEXTAREA with name ~ /message/");
		} else if (msgs.length) {
			msgParts = dojo.json.evalJson(this._messageParts);
			msgParts[xg.shared.nls.text('yourMessage')] = msgs[0];
			msgParts = dojo.json.serialize(msgParts);
		} else {
			msgParts = this._messageParts;
		}

		dojo.io.bind({
			url: this._url,
			mimetype: 'text/javascript',
			method: 'post',
			content: {xn_out:'json', messageParts: msgParts},
			encoding: 'utf-8',
			preventCache: true,
			load: function (type, data, event) {
				switch (data.status) {
					default: // default is accept.
					case 'ok':
						okCallback();
						break;
					case 'warning':
						_this.showDialog(xg.shared.nls.text('updateMessageQ'), xg.shared.nls.text('warningMessage'), data.messageParts, okCallback, failCallback, preDisplayCallback);
						break;
					case 'error':
						_this.showDialog(xg.shared.nls.text('updateMessage'), xg.shared.nls.text('errorMessage'), data.messageParts, undefined, failCallback, preDisplayCallback);
						break;
				}
			}
		});
	},

	showDialog: function(title, message, fails, okCallback, failCallback, preDisplayCallback) {
		var failsStr = "";
		// Format the list of failures
		for (var i in fails) {
			if (!fails[i].length) {
				continue;
			}
			for (var j = 0, lst = []; j<fails[i].length; j++) {
				lst[j] = '"' + fails[i][j].replace(/<\/?[\w-]+[^>]*>/g, '') + '"';
			}
			failsStr += '<p><strong>' + i + '</strong><br/>' + lst.join(', ') + '</p>';
		}

		var dialog = dojo.html.createNodesFromText(
			'<div class="xg_module xg_floating_module">'+
				'<div style="background-image: none;" class="xg_floating_container xg_module">'+
					'<div class="xg_module_head"><h2>'+title+'</h2></div>'+
					'<div class="xg_module_body">'+
						'<p>'+message+'</p>'+
						'<p>'+xg.shared.nls.text('removeWords')+'</p>'+
						failsStr+
						'<p class="buttongroup">'+
							'<input class="button button-primary" type="button" value="'+xg.shared.nls.text('goBack')+'"> '+
                            (okCallback ? '<input class="button" type="button" value="'+xg.shared.nls.text('sendAnyway')+'">' : '')+
						'</p>'+
					'</div>'+
				'</div>'+
			'</div>')[0];
		if (preDisplayCallback) {
			preDisplayCallback();
		}
		xg.shared.util.showOverlay();
        document.body.appendChild(dialog);

        // go back
        dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.dom.removeNode(dialog);
            failCallback();
		}));

		// send anyway
		if (okCallback) {
			dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[1], 'onclick', dojo.lang.hitch(this, function(event) {
				dojo.event.browser.stopEvent(event);
				dojo.dom.removeNode(dialog);
				okCallback();
			}));
		}
	}
});
/*
 *	Runs check-for-spam processing. params is a hash:
 *		url
 *		messageParts
 *		form
 *		onContinue
 *		onBack
 *		onWarning
 */
xg.shared.SpamWarning.checkForSpam = function(params) {
	var sw = new xg.shared.SpamWarning;
	sw._url = params.url;
	sw._messageParts = params.messageParts;
	sw.doCheck(null, params.form, params.onContinue, params.onBack, params.onWarning);
};
