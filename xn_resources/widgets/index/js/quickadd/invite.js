/** $Id: $
 * 	Quick Add Invite dialog scripting
 */
dojo.provide('xg.index.quickadd.invite');
dojo.require('xg.index.invitation.FriendList'); /** @explicit for quickadd/invite.php */

(function(){
    var qa = xg.index.quickadd, friendList;

    var resetForm = function() {
        var f = dojo.byId('xg_quickadd_invite');

        dojo.html.hide( xg.$('#xg_quickadd_invite_notify_success') );
        xg.index.util.FormHelper.hideErrorMessages(f);

        f.emailAddresses.value = '';
        f.cancel.value = xg.index.nls.text('cancel');

        if (friendList) friendList.reset(false);
    }

    xg.index.quickadd.listen('invite', 'load', function(){
        var f = dojo.byId('xg_quickadd_invite');

        //
        // Form basics
        //
        var validate = function() {
            var errors = {};
            if ( dojo.string.trim(f.emailAddresses.value) == '' && friendList.selectedFriendCount == 0) {
                errors.x = xg.index.nls.text('youNeedToAddEmailRecipient');
            }
            return errors;
        }
        var mo = xg.$('a.more_options',f);
        xg.listen(mo, 'onclick', function(evt){
            xg.stop(evt);
            if (friendList) friendList.updateHiddenInputs();
            qa.gotoMoreOptions(f, function() { f.setAttribute('action', mo.href); } );
        })
        xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);
        xg.listen(f, 'onsubmit', function(evt) {
            xg.stop(evt);
            if (friendList) friendList.updateHiddenInputs();
            dojo.html.hide( xg.$('#xg_quickadd_invite_notify_success') );
            if (!qa.validateForm(f, validate)) {
                return;
            }
            qa.submitFormXhr({
                form: f,
                title: xg.index.nls.text('sendingLabel'),
                text: xg.index.nls.text('yourMessageIsBeingSent'),
                success: function(content) {
                    if ("object" != typeof content || content.status != 'ok') {
                        return xg.index.quickadd.onDefaultServerResponse(content);
                    }
                    resetForm();

                    var s = xg.$('#xg_quickadd_invite_notify_success');
                    f.cancel.value = xg.index.nls.text('done');
                    s.innerHTML = content.message;
                    dojo.html.show(s);
                    qa.showDialog();
                }
            });
        });

        //
        // Friend list toggle
        //
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
                }
                xg.shared.util.fixDialogPosition(xg.parent(f,'div.xg_floating_module'));
            });
            friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', f)[0]);
            friendList.init();
        }

    });

    xg.index.quickadd.listen('invite', 'open', resetForm);

})();
