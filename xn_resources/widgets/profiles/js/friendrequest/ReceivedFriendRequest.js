dojo.provide('xg.profiles.friendrequest.ReceivedFriendRequest');

dojo.require('xg.profiles.friendrequest.AbstractFriendRequest');
dojo.require("dojo.lfx.*");
dojo.require('xg.shared.CountUpdater');
dojo.require('xg.shared.util');

/**
 * Controls for a friend request that the current user has received.
 */
dojo.widget.defineWidget('xg.profiles.friendrequest.ReceivedFriendRequest', xg.profiles.friendrequest.AbstractFriendRequest, {

    /** Message to display if the current user has too many friends on this network. */
    _friendLimitExceededMessage: '',

    /**
     * Accepting or ignoring a friend request should decrement the count dynamically (BAZ-9715) [ywh 2008-09-15]
     */
    decrementCount: function() {
        xg.shared.CountUpdater.decrement('friendrequestsreceived', 1);
    },

    /**
     * Sets up the click handlers.
     */
    initializeClickHandlers: function() {
        this.handleClick({
            name: 'accept',
            onSuccess: dojo.lang.hitch(this, function(div, data) {
                dojo.style.setOpacity(div, 0);
                dojo.html.addClass(div, 'accepted');
                dojo.html.addClass(div, 'easyclear');
                div.innerHTML = data.html;
                xg.shared.util.parseWidgets(dojo.dom.firstElement(div));
                dojo.lfx.html.fadeIn(div, 500, null, dojo.lang.hitch(this, function() {
                    this.decrementCount();
                })).play();
            }),
            onFailure: dojo.lang.hitch(this, function(div, data) {
                if (data.friendLimitExceeded) {
                    xg.shared.util.alert({
                        title: xg.profiles.nls.text('requestLimitExceeded'),
                        bodyHtml: dojo.string.escape('html', this._friendLimitExceededMessage)
                    });
                }
            })
        });
        this.handleClick({
            name: 'ignore',
            onSuccess: dojo.lang.hitch(this, function(div, data) {
                if (dojo.html.getElementsByClass('request', div.parentNode, 'div').length == 1) {
                    window.location.reload(true);
                    return;
                }
                dojo.lfx.html.fadeOut(div, 500, null, dojo.lang.hitch(this, function() {
                    dojo.dom.removeNode(div);
                    this.decrementCount();
                })).play();
            })
        });
    }

});
