dojo.provide('xg.index.message.new');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');

/**
 * Behavior for the message/new page.
 */
(function() {
    var form = dojo.byId('message_form');
    var friendList;
    xg.addOnRequire(function() {
        // Use addOnRequire, to ensure that parseWidgets has already been called. [Jon Aquino 2008-07-10]
        friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', form)[0]);
        friendList.init();
    });

    var validate = function() {
        var errors = {};
        if (friendList.selectedFriendCount == 0) {
            errors.friendList = dojo.string.escape('html', xg.index.nls.html('pleaseChooseFriends'));
        }
        if (dojo.string.trim(form.subject.value).length == 0) {
            errors.subject = dojo.string.escape('html', xg.index.nls.html('pleaseEnterASubject'));
        }
        if (dojo.string.trim(form.message.value).length == 0) {
            errors.message = dojo.string.escape('html', xg.index.nls.html('pleaseEnterAMessage'));
        }
        return errors;
    };

    dojo.event.connect(dojo.byId('cancel_button'), 'onclick', function(event) {
        dojo.event.browser.stopEvent(event);
        window.location = dojo.byId('cancel_button').getAttribute('_href');
    });
    xg.shared.util.setMaxLength(form.message, form.message.getAttribute('_maxlength'));
    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        if (! xg.index.util.FormHelper.runValidation(form, validate)) { return; }
        friendList.updateHiddenInputs();
        form.submit();
    });
}());
