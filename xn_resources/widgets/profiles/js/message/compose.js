dojo.provide('xg.profiles.message.compose');
dojo.require('xg.shared.util');
dojo.require('xg.shared.SpamWarning');

/**
 * Behavior for the message/new page.
 */
(function() {
    var f = dojo.byId('xg_mail_compose');
    var maxRecipients = f.getAttribute('_maxRecipients');
    var submit = dojo.byId('xj_compose_submit');
    var friendList;
    var redirectTarget;
    var maxMessageLength = parseInt(f.getAttribute('_maxMessageLength'));
    xg.shared.util.setAdvisableMaxLength(f.message, maxMessageLength, xg.profiles.nls.text('supportsTextOnly'));

    /**
     * set the status message text and type
     *
     * @param message string  the message you wish to use
     * @param error boolean  use an error banner if true
     */
    var setMessageStatus = function(message, error) {
        var _body = xg.$('#xj_status_body');
        var _message = xg.$('#xj_status_message');
        if (_body && _message) {
            if (error) {
                dojo.html.removeClass(_body, 'success');
                dojo.html.addClass(_body, 'errordesc');
            } else {
                dojo.html.removeClass(_body, 'errordesc');
                dojo.html.addClass(_body, 'success');
            }
            _message.innerHTML = message;
        }
    }

    /**
     * hide the status message container
     */
    var hideMessageStatus = function() {
        var _status = xg.$('#xj_status');
        if (_status) {
            dojo.html.hide(_status);
        }
    }

    /**
     * set the desired status message text and type then display the message container
     *
     * @param message string  the message you wish to use
     * @param error boolean  use an error banner if true
     */
    var showMessageStatus = function(message, error) {
        var _status = xg.$('#xj_status');
        if (_status) {
            dojo.html.hide(_status);
            setMessageStatus(message, error);
            dojo.html.show(_status);
        }
    }

    /**
     * Checks the form for problems.
     *
     * @return  an error message, or null if no errors were found
     */
    var validateForm = function() {
        if (f.message.value.length > maxMessageLength) { return xg.profiles.nls.text('messageIsTooLong', maxMessageLength); }
        return null;
    }

    var redirectUser = function() {
        if (redirectTarget) {
            window.location = redirectTarget;
        }
    }

    // Friend list toggle
    if (xg.$('p.xj_toggle', f)) {
        var tgl = xg.$('p.xj_toggle', f).firstChild, arrow = tgl.firstChild, friends = xg.$('div.xj_friends', f);
        xg.listen(tgl, 'onclick', function(evt) {
            xg.stop(evt);
            if (friends.style.display != 'none') {
                arrow.innerHTML = '&#9658;';
                dojo.html.hide(friends);
            } else {
                arrow.innerHTML = '&#9660;';
                dojo.html.show(friends);
                if (! friendList) {
                    friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', f)[0]);
                    friendList.init();
                }
            }
        });
    }

    // Attach reply 'add more' link, if present
    var addMoreLink = xg.$('a.xj_add_more_link', f);
    if (addMoreLink) {
        xg.listen(addMoreLink, 'onclick', function(evt) {
            xg.stop(evt);
            var li = addMoreLink.parentNode.parentNode;
            dojo.lang.forEach(xg.$$('dd.xj_hidden_compose_field', f), function(dd) {
                dojo.html.show(dd);
            });
            dojo.html.hide(li);
        });
    }

    // Preset recipients delete action
    if (xg.$('a.recipient-delete', f)) {
        dojo.lang.forEach(xg.$$('a.recipient-delete', f), function(a) {
            xg.listen(a, 'onclick', function(evt) {
                xg.stop(evt);
				dojo.dom.removeNode((evt.target||evt.srcElement).parentNode);
                var remaining = xg.$$('a.recipient-delete', f).length;
                if (remaining == 1) {
                    var last = xg.$('a.recipient-delete', f);
                    // edge case: the last recipient's fullName has a ',' and all other recipients are removed
                    last.parentNode.lastChild.nodeValue = last.parentNode.lastChild.nodeValue.replace(/,\s*$/, '');
                } else if ((remaining < 1) && ! addMoreLink) {
                    // no more preset recipients so remove the containing <ul>
                    dojo.dom.removeNode(xg.$('ul.recipient-delete', f));
                }
            });
        });
    }

    // Attach submit action
    if (submit) {
        // TODO: Split up this code into small functions [Jon Aquino 2008-09-04]
        xg.listen(submit, 'onclick', function(evt) {
            xg.stop(evt);
            var error = validateForm();
            if (error) {
                showMessageStatus(error, true);
                return;
            }

            submit.disabled = true;

            // clear label errors
            dojo.lang.forEach(xg.$$('label.error', f), function(label) {
                dojo.html.removeClass(label, 'error');
            });
            hideMessageStatus();

            // update friend selector hidden inputs, if present
            if (friendList) { friendList.updateHiddenInputs(); }

            // generate list of remaining preset recipients
            var presetRecipients = xg.$('#presetRecipients', f);
            var presetRecipientNodes = xg.$$('a.recipient-delete', f);
            if (presetRecipients && (presetRecipientNodes.length > 0)) {
                var recipients = new Array();
                dojo.lang.forEach(presetRecipientNodes, function(recip) {
                    recipients.push(recip.getAttribute('_recipient'));
                });
                presetRecipients.value = recipients.join(',');
            }

            var content = {};
            var keys = ['fixedRecipients', 'presetRecipients', 'recipients', 'friendSet', 'screenNamesIncluded', 'screenNamesExcluded', 'subject', 'message'];
            for (var i in keys) {
                var key = keys[i];
                if (key in f) {
                    content[key] = dojo.string.trim(f[key].value);
                }
            }

            xg.shared.SpamWarning.checkForSpam({
                url: f.getAttribute('_spamUrl'),
                messageParts: f.getAttribute('_spamMessageParts'),
                form: f,
                onBack: function () { xg.shared.util.hideOverlay(); submit.disabled = false; },
                onWarning: function () { },
                onContinue: dojo.lang.hitch(this, function () {
                    dojo.io.bind({
                        url: f.action,
                        method: 'post',
                        content: content,
                        preventCache: true,
                        mimetype: 'text/json',
                        encoding: 'utf-8',
                        load: dojo.lang.hitch(this, function(type, data, event) {
                            if ('success' in data) {
                                // message sent successfully
                                if ('target' in data) {
                                    // redirect the user
                                    redirectTarget = data.target;
                                } else {
                                    redirectTarget = false;
                                }
                                xg.shared.util.showDialogAndRedirect({
                                    title: xg.profiles.nls.text('messageSent'),
                                    bodyHtml: xg.profiles.nls.html('yourMessageHasBeenSent'),
                                    target: redirectTarget
                                });
                            } else {
                                submit.disabled = false;

                                if ('error' in data) {
                                    var numKeys = 0;
                                    for (var k in data.error) { numKeys++; }
                                    var listMode = numKeys > 1;
                                    var errorString = listMode ? '<ul>' : '';
                                    for (var labelKey in data.error) {
                                        var label = dojo.byId('xj_label_' + labelKey);
                                        if (label) { dojo.html.addClass(label, 'error'); }
                                        if (listMode) { errorString += '<li>'; }
                                        errorString += data.error[labelKey]
                                        if (listMode) { errorString += '</li>'; }
                                    }
                                    errorString += listMode ? '</ul>' : '';
                                    showMessageStatus(errorString, true);
                                } else {
                                    // non-specific error
                                    showMessageStatus(xg.profiles.nls.html('unableToCompleteAction'), true);
                                }
                            }
                        })
                    });
                })
            });
        });
    }
}());