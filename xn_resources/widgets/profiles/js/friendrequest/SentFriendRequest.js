dojo.provide('xg.profiles.friendrequest.SentFriendRequest');

dojo.require('xg.profiles.friendrequest.AbstractFriendRequest');
dojo.require("dojo.lfx.*");
dojo.require('xg.shared.CountUpdater');

/**
 * Controls for a friend request that the current user has sent.
 */
dojo.widget.defineWidget('xg.profiles.friendrequest.SentFriendRequest', xg.profiles.friendrequest.AbstractFriendRequest, {

    _maxSentRequests: null,

    /**
     * Sets up the click handlers.
     */
    initializeClickHandlers: function() {
        this.handleClick({
            name: 'withdraw',
            onSuccess: dojo.lang.hitch(this, function(div, data) {
                if (dojo.html.getElementsByClass('request', div.parentNode, 'div').length == 1) {
                    window.location.reload(true);
                    return;
                }
                dojo.lfx.html.fadeOut(div, 500, null, dojo.lang.hitch(this, function() {
                    dojo.dom.removeNode(div);
                    xg.shared.CountUpdater.decrement('friendrequestssent', 1);
                    if (xg.shared.CountUpdater._getCurrentValue('friendrequestssent') < this._maxSentRequests) {
                        // remove error message if user is no longer maxed out (BAZ-10274) [ywh 2008-09-24]
                        x$('div.xj_error_msg').remove();
                    }
                })).play();
            })
        });
    }

});

