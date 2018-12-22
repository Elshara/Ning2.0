dojo.provide('xg.index.invitation.chooseInvitationMethod');

dojo.require('xg.shared.util');
dojo.require('xg.index.util.FormHelper');
dojo.require('dojo.lfx.html');

/**
 * Behavior for the chooseInvitationMethod page.
 */
(function() {

    /** The form elements */
    var forms = dojo.byId('xg_body').getElementsByTagName('form');

    /** The FriendList widget. */
    var friendList;

    /**
     * Returns whether the given form is open.
     *
     * @param form  the form element to check
     * @return  true if the form is open; false if closed
     */
    var isOpen = function(form) {
        return dojo.html.isShowing(form.getElementsByTagName('div')[0]);
    };

    /**
     * Returns the section that is currently open.
     *
     * @return  the form element, or null if no form was open
     */
    var formCurrentlyOpen = function() {
        for (var i = 0; i < forms.length; i++) {
            if (isOpen(forms[i])) { return forms[i]; }
        }
        return null;
    };

    var updatePageLayout = function() {
        // TODO: Instead of checking whether the xg.index.invitation.pageLayout package has been defined
        // (a brittle check), define a flag, e.g., xg.useOldInvitationPageLayout = true. Then we can
        // dojo.require('xg.index.invitation.pageLayout') as usual. But we will need to update
        // miamiurbanlife.com, which uses the following hack to prevent the new layout: dojo .provide('xg.index.invitation.pageLayout') [Jon Aquino 2008-07-01]
        if(xg.index.invitation.pageLayout){
            xg.index.invitation.pageLayout.recompute();
        }
    };

    /**
     * Opens the form if it is closed, and vice versa.
     *
     * @param form  the form element to open or close
     */
    var toggle = function(form) {
        var duration = 200;
        var an;
        if (isOpen(form)) {
            form.getElementsByTagName('a')[0].getElementsByTagName('span')[0].innerHTML = '&#9658;';
	        // a workaround for FF scrollbar weirdness causing jumpiness in the animation (BAZ-9005) [ywh 2008-09-12]
			if (form.id == 'invite_friends_form') {
				var friendListDiv = xg.$('div.friend_list',form);
				var friendListOptions = xg.$('div.friendlist_options',form);
				var friendListOverflow = friendListDiv.style.overflow;
				var friendListOptionsOverflow = friendListOptions.style.overflow;
				friendListDiv.style.overflow = 'hidden';
				friendListOptions.style.overflow = 'hidden';

				var body = dojo.byId('xg_body');
				body.style.height = body.offsetHeight + 'px';
			}
			an = dojo.lfx.html.wipeOut(form.getElementsByTagName('div')[0], duration, null, function() {
				if (form.id == 'invite_friends_form') {
			        friendListDiv.style.overflow = friendListOverflow;
        			friendListOptions.style.overflow = friendListOptionsOverflow;
					body.style.height = 'auto';
				}
				updatePageLayout()
			});
        } else {
            form.getElementsByTagName('a')[0].getElementsByTagName('span')[0].innerHTML = '&#9660;';
            an = dojo.lfx.html.wipeIn(form.getElementsByTagName('div')[0], duration, null, function() {
                xg.index.util.FormHelper.scrollIntoView(form);
                updatePageLayout();
            });
        }
        an.play();
    };

    /**
     * Sets up the friend list, if it hasn't been set up already.
     */
    var initializeFriendListIfNecessary = function() {
        if (initializeFriendListIfNecessary.done) { return; }
        initializeFriendListIfNecessary.done = true;
        xg.addOnRequire(function() {
            // Use addOnRequire, to ensure that parseWidgets has already been called. [Jon Aquino 2008-07-10]
            friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', dojo.byId('invite_friends_form'))[0]);
            friendList.init();
        });
    };

    /**
     * Attaches the onclick handlers for accordion behavior.
     */
    dojo.lang.forEach(forms, function(form) {
        dojo.event.connect(form.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            toggle(form);
            if (form.id == 'invite_friends_form') { initializeFriendListIfNecessary(); }
        }));
    });

    xg.index.util.FormHelper.scrollIntoView(formCurrentlyOpen());

    var maxMsgLength = 200, inviteByEmails = dojo.byId('invite_by_emails'), inviteFriends = dojo.byId('invite_friends_form');

    xg.shared.util.setAdvisableMaxLength(inviteByEmails.message, maxMsgLength);
    dojo.event.connect(inviteByEmails.inviteEmailsSend, 'onclick', function(event) {
        if (inviteByEmails.message.value.length > maxMsgLength) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.alert({
                title: xg.index.nls.html('error'),
                bodyHtml: xg.index.nls.html('messageIsTooLong',maxMsgLength)
            });
        }
    });

    if (inviteFriends) {
        xg.shared.util.setAdvisableMaxLength(inviteFriends.inviteFriendsMessage, maxMsgLength);
        dojo.event.connect(inviteFriends.inviteFriendsSend, 'onclick', function (event) {
            if (inviteFriends.inviteFriendsMessage.value.length > maxMsgLength) {
                dojo.event.browser.stopEvent(event);
                xg.shared.util.alert({
                    title: xg.index.nls.html('error'),
                    bodyHtml: xg.index.nls.html('messageIsTooLong',maxMsgLength)
                });
            }
        });
    }

    /**
     * Initializes the Invite Friends form.
     */
    if (dojo.byId('invite_friends_form')) {
        if (isOpen(dojo.byId('invite_friends_form'))) { initializeFriendListIfNecessary(); }
        dojo.event.connect(dojo.byId('invite_friends_form'), 'onsubmit', function(event) {
            friendList.updateHiddenInputs();
        });
    }

    if (dojo.byId('bulk_invitation_url_field')) {
        xg.shared.util.selectOnClick(dojo.byId('bulk_invitation_url_field'));
    }
}());
