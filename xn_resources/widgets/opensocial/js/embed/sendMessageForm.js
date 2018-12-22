/** $Id: $
 *  Quick Add Invite dialog scripting
 */
dojo.provide('xg.opensocial.embed.sendMessageForm');
dojo.require('xg.opensocial.embed.requests');
dojo.require('xg.index.invitation.FriendList'); // !used by the quickadd/invite.php
dojo.require('xg.index.util.FormHelper');

(function(){
    var qa = xg.index.quickadd, friendList;

    var resetForm = function() {
        var f = dojo.byId('xg_quickadd_sendmessageform');

        dojo.html.hide( xg.$('#xg_quickadd_sendmessageform_notify_success') );
        xg.index.util.FormHelper.hideErrorMessages(f);

        f.cancel.value = xg.index.nls.text('cancel');

        friendList.reset(true);
    }

    xg.index.quickadd.listen('sendMessageForm', 'load', function(){
        var f = dojo.byId('xg_quickadd_sendmessageform');
        friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', f)[0]);
        friendList.init();
        //
        // Form basics
        //
        var validate = function() {
            var errors = {};
            if (friendList.selectedFriendCount == 0) {
                errors.x = xg.index.nls.text('youNeedToAddEmailRecipient');
            }
            return errors;
        }
        xg.listen(f.cancel, 'onclick', xg.opensocial.embed, function(evt) {
		    qa.cancelDialog();
			xg.opensocial.embed.requests.sendAborted({ status: false, code: 'cancelled', msg: xg.opensocial.nls.html('operationCancelled') });
		});
        xg.listen(f, 'onsubmit', function(evt) {
            xg.stop(evt);
            if (friendList) friendList.updateHiddenInputs();
            dojo.html.hide( xg.$('#xg_quickadd_sendmessageform_notify_success') );
            qa.submitFormXhr({
                form: f,
                title: xg.index.nls.text('sendingLabel'),
                text: xg.index.nls.text('yourMessageIsBeingSent'),
                success: function(content) {
                    if ("object" != typeof content || content.status != 'ok') {
                        return xg.index.quickadd.onDefaultServerResponse(content);
                    }
                    resetForm();
                    qa.cancelDialog();
                    xg.opensocial.embed.requests.sendCompleted({ status: true, code: 'ok', msg: '' });
                }
            });
        });
    });

    xg.index.quickadd.listen('sendMessageForm', 'open', resetForm);

})();
