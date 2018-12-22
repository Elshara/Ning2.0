dojo.provide('xg.events.BroadcastEventMessageLink');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.SpamWarning');

/**
 * A link that opens a dialog box for sending a message to event attendees.
 */
dojo.widget.defineWidget('xg.events.BroadcastEventMessageLink', dojo.widget.HtmlWidget, {

    /** Endpoint for sending the message */
    _url: '',
    _spamUrl: '',
    _spamMessageParts: '',
    maxMsgLength: 200,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag), _this = this;
        dojo.style.show(a);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var dlg = xg.shared.util.confirm({
                title: xg.events.nls.text('sendMessageToGuests'),
                bodyHtml: ' \
                    <dl class="errordesc msg clear" style="display: none"></dl> \
                    <fieldset> \
                        <ul class="options"> \
                            <li><label>' + xg.events.nls.html('sendMessageToGuestsThat') + '</label></li> \
                            <li><label><input name="attending" type="checkbox" class="checkbox" checked="checked" />' + xg.events.nls.html('areAttending') + '</label></li> \
                            <li><label><input name="might_attend" type="checkbox" class="checkbox" checked="checked" />' + xg.events.nls.html('mightAttend') + '</label></li> \
                            <li><label><input name="not_rsvped" type="checkbox" class="checkbox" />' + xg.events.nls.html('haveNotYetRsvped') + '</label></li> \
                            <li><label><input name="not_attending" type="checkbox" class="checkbox" />' + xg.events.nls.html('areNotAttending') + '</label></li> \
                        </ul> \
                        <p> \
                            <label>' + xg.events.nls.html('yourMessage') + '</label><br /> \
                            <textarea name="message" cols="30" rows="4" style="width:230px"></textarea> \
                        </p> \
                    </fieldset>',
                okButtonText: xg.events.nls.text('send'),
                closeOnlyIfOnOk: true,
                onOk: function(dialog) {
                    var form = dialog.getElementsByTagName('form')[0];
					if (! _this.validate(form)) { return; }
                    xg.shared.SpamWarning.checkForSpam( {
                    	url: _this._spamUrl,
						messageParts: _this._spamMessageParts,
						form: form,
                        onContinue: function () { dojo.style.hide(dialog); _this.process(dialog,form); },
                        onBack: function () { dojo.style.show(dialog); },
                        onWarning: function () { dojo.style.hide(dialog); }
					} );
                }
            });
			xg.shared.util.setAdvisableMaxLength(dlg.getElementsByTagName('textarea')[0], this.maxMsgLength);
        }));
    },

	process: function(dialog, form) {
		dojo.dom.removeNode(dialog);
		var progressDialog = xg.shared.util.progressDialog({
			title: xg.events.nls.text('sending'),
			bodyHtml: xg.events.nls.html('yourMessageIsBeingSent')
		});
		dojo.io.bind({
			url: this._url,
			mimetype: 'text/javascript',
			formNode: form,
			method: 'post',
			encoding: 'utf-8',
			preventCache: true,
			load: function(type, data, event) {
				if (! data.success) { return; }
				progressDialog.hide();
				xg.shared.util.alert({
					title: xg.events.nls.text('messageSent'),
					bodyHtml: xg.events.nls.html('yourMessageHasBeenSent')
				});
			}
		});
	},

    /**
     * Validates the input.
     *
     * @param form  the form node
     */
    validate: function(form) {
        var errorMessages = [];
        dojo.lang.forEach(dojo.html.getElementsByClass('error', form), function(element) { dojo.html.removeClass(element, 'error'); });
        if (! (form.attending.checked || form.might_attend.checked || form.not_rsvped.checked || form.not_attending.checked)) {
            errorMessages.push(xg.events.nls.html('chooseRecipient'));
        }
        if (dojo.string.trim(form.message.value).length == 0) {
            errorMessages.push(xg.events.nls.html('pleaseEnterAMessage'));
            xg.index.util.FormHelper.showErrorMessage(form.message);
		} else if (dojo.string.trim(form.message.value).length > this.maxMsgLength) {
        	errorMessages.push(xg.events.nls.html('messageIsTooLong', this.maxMsgLength));
        	xg.index.util.FormHelper.showErrorMessage(form.message);
        }
        var errorDl = form.getElementsByTagName('dl')[0];
        errorDl.innerHTML = '<dt>' + xg.events.nls.html('thereHasBeenAnError') + '</dt><dd><ol><li>' + errorMessages.join('</li><li>') + '</li></ol></dd>';
        dojo.style.setShowing(errorDl, errorMessages.length > 0);
        return errorMessages.length == 0;
    }
});
