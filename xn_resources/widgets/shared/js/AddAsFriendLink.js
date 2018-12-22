dojo.provide('xg.shared.AddAsFriendLink');

dojo.require('xg.shared.util');
dojo.require('xg.shared.SpamWarning');
dojo.require('xg.index.util.FormHelper');

/**
 * An anchor tag that sends a friend request.
 */
dojo.widget.defineWidget('xg.shared.AddAsFriendLink', dojo.widget.HtmlWidget, {

    /** ID of the recipient */
    _screenName: '',

    /** Name of the recipient */
    _name: '',

    /** Maximum number of characters for the message body. */
    _maxMessageLength: 0,

    /** CSS classes to use for the Request Sent! span */
    _requestSentClasses: '',

    /** Message to display if the current user has too many friends on this network. */
    _friendLimitExceededMessage: '',

    /** Message to display if the current user has too many sent friend requests on this network. */
    _sentFriendRequestLimitExceededMessage: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (dojo.html.hasClass(a, 'working')) { return; }
            this.toggleDescIcon(a, 'working');
            dojo.io.bind({
                url: '/profiles/friendrequest/friendLimitExceeded?xn_out=json',
                mimetype: 'text/javascript',
                method: 'get',
                encoding: 'utf-8',
                preventCache: true,
                load: dojo.lang.hitch(this, function(type, data, event) {
                    if (data.friendLimitExceeded) {
                        xg.shared.util.alert({
                            title: xg.shared.nls.text('friendLimitExceeded'),
                            bodyHtml: dojo.string.escape('html', this._friendLimitExceededMessage)
                        });
                        this.toggleDescIcon(a, 'addfriend');
                        return;
                    }
                    if (data.sentFriendRequestLimitExceeded) {
                        xg.shared.util.alert({
                            title: xg.shared.nls.text('requestLimitExceeded'),
                            bodyHtml: dojo.string.escape('html', this._sentFriendRequestLimitExceededMessage)
                        });
                        this.toggleDescIcon(a, 'addfriend');
                        return;
                    }
                    this.showDialog(a);
                })
            });
        }));
    },

    /**
     * toggle spinner/addfriend desc icon
     */
    toggleDescIcon: function(a, desired) {
        if (desired) {
            if (desired == 'working') {
                dojo.html.removeClass(a, 'addfriend');
                dojo.html.addClass(a, 'working');
            } else {
                dojo.html.removeClass(a, 'working');
                dojo.html.addClass(a, 'addfriend');
            }
        }
    },

    /**
     * Displays the Add As Friend dialog box.
     *
     * @param a  the Add As Friend link
     */
    showDialog: function(a) {
        var defaultMessage = xg.shared.nls.html('typePersonalMessage');
        var dialog = xg.shared.util.confirm({
            title: xg.shared.nls.text('addNameAsFriend', this._name),
            bodyHtml: ' \
                <dl class="errordesc msg clear" style="display: none"></dl> \
                <p>' + xg.shared.nls.html('nameMustConfirmFriendship', dojo.string.escape('html', this._name)) + '</p> \
                <p><a href="#">' + xg.shared.nls.html('addPersonalMessage') + '</a></p> \
                <p style="display:none"><textarea name="message" cols="30" rows="3"></textarea></p>',
            okButtonText: xg.shared.nls.text('send'),
            closeOnlyIfOnOk: true,
            onCancel: dojo.lang.hitch(this, function() {
                this.toggleDescIcon(a, 'addfriend');
            }),
            onOk: dojo.lang.hitch(this, function(dialog) {
                var form = dialog.getElementsByTagName('form')[0];
                if (! this.validate(form)) { return false; }
                xg.shared.SpamWarning.checkForSpam({
                    url: '/main/invitation/checkMessageForSpam',
                    messageParts: '{}',
                    form: form,
                    onContinue: dojo.lang.hitch(this, function () {
                        dojo.style.hide(dialog);
                        if (form.message.value == defaultMessage) { form.message.value = ''; }
                        this.send(dialog, form, a);
                    }),
                    onBack: function () { dojo.style.show(dialog); },
                    onWarning: function () { dojo.style.hide(dialog); }
                });
            })
        });
        var form = dialog.getElementsByTagName('form')[0];
        xg.shared.util.setAdvisableMaxLength(form.message, this._maxMessageLength);
        var addPersonalMessageLink = form.getElementsByTagName('a')[0];
        dojo.event.connect(addPersonalMessageLink, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.hide(addPersonalMessageLink.parentNode);
            dojo.style.show(form.message.parentNode);
            form.message.value = defaultMessage;
            form.message.focus();
            dojo.html.selectInputText(form.message);
        }));
    },

    /**
     * Sends the friend request.
     *
     * @param dialog  the dialog-box div element
     * @param form  the form element
     * @param a  the Add As Friend anchor element
     */
    send: function(dialog, form, a) {
        dojo.style.hide(dialog);
        dojo.io.bind({
            url: '/profiles/friendrequest/create?xn_out=json&screenName=' + this._screenName,
            mimetype: 'text/javascript',
            formNode: form,
            method: 'post',
            encoding: 'utf-8',
            preventCache: true,
            load: dojo.lang.hitch(this, function(type, data, event) {
                if (! data.success) { return; }
                xg.shared.util.alert({
                    title: xg.shared.nls.text('friendRequestSent'),
                    bodyHtml: xg.shared.nls.html('yourFriendRequestHasBeenSent'),
                    autoCloseTime: 2000     // in milliseconds
                });
                var requestSent = dojo.html.createNodesFromText('<span class="' + this._requestSentClasses + '">' + xg.shared.nls.html('requestSent') + '</span>')[0];
                a.parentNode.replaceChild(requestSent, a);
            })
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
        if (dojo.string.trim(form.message.value).length > this._maxMessageLength) {
            errorMessages.push(xg.shared.nls.html('yourMessageIsTooLong', this._maxMessageLength));
            xg.index.util.FormHelper.showErrorMessage(form.message);
        }
        var errorDl = form.getElementsByTagName('dl')[0];
        errorDl.innerHTML = '<dt>' + xg.shared.nls.html('thereHasBeenAnError') + '</dt><dd><ol><li>' + errorMessages.join('</li><li>') + '</li></ol></dd>';
        dojo.style.setShowing(errorDl, errorMessages.length > 0);
        return errorMessages.length == 0;
    }

});
