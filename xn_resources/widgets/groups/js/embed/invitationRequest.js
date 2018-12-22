dojo.provide('xg.groups.embed.invitationRequest');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');

/**
 * Behavior for the invitation-request form
 */
(function() {

    var form = dojo.byId('invitation_request_form');
    var nameField = dojo.byId('invitation_request_name');
    var emailAddressField = dojo.byId('invitation_request_email_address');
    var messageField = dojo.byId('invitation_request_message');

    var validate = function() {
        var errors = {};
        if (nameField && dojo.string.trim(nameField.value).length == 0) { errors.name = xg.groups.nls.html('pleaseEnterName'); }
        if (emailAddressField && dojo.string.trim(emailAddressField.value).length == 0) { errors.emailAddress = xg.groups.nls.html('pleaseEnterEmailAddress'); }
        // Server-side validation has more stringent e-mail validation [Jon Aquino 2007-04-26]
        if (emailAddressField && emailAddressField.value.indexOf('@') == -1) { errors.emailAddress = xg.groups.nls.html('xIsNotValidEmailAddress', dojo.string.escape('html', emailAddressField.value)); }
        return errors;
    };

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        if (xg.index.util.FormHelper.runValidation(form, validate)) { form.submit(); }
    });

    if (nameField) { nameField.focus(); }
    else if (messageField) { messageField.focus(); }
    if (messageField) { xg.shared.util.setMaxLength(messageField, messageField.getAttribute('_maxlength')); }
}());

