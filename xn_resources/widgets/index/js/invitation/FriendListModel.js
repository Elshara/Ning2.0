dojo.provide('xg.index.invitation.FriendListModel');

dojo.require('xg.shared.util');

/**
 * Data for a FriendList.
 *
 * @param friendDataUrl  Endpoint for retrieving friends.
 * @param numFriends  The total number of friends that will appear in the list.
 */
xg.index.invitation.FriendListModel = function(args) {

    /** Endpoint for retrieving friends. */
    var friendDataUrl = args.friendDataUrl;

    /** The base set of friends: '', ALL_FRIENDS, or FRIENDS_ON_NETWORK */
    var friendSet = '';

    /** Array of friends: {thumbnailUrl, screenName, fullName, reasonToDisable, isMember}, or undefined if not yet loaded */
    var friends = new Array(args.numFriends);

    /** Name of the set of all friends in the FriendList. */
    this.ALL_FRIENDS = 'ALL_FRIENDS';

    /** Name of the set of friends who are members of the current network. */
    this.FRIENDS_ON_NETWORK = 'FRIENDS_ON_NETWORK';

    var _this = this;

    // ===== Private methods ==========================================

    /**
     * Updates the friend's selected attribute based on the current friendSet.
     *
     * @param friend  the friend object
     */
    var updateByFriendSet = function(friend) {
        if (friend.reasonToDisable) { return; }
        friend.selected = friendSet == _this.ALL_FRIENDS || (friendSet == _this.FRIENDS_ON_NETWORK && friend.isMember);
    }

    // ===== Public methods ==========================================

    /**
     * Returns the friend at the given index.
     *
     * @param i  the 0-based index
     */
    this.getFriend = function(i) {
        return friends[i];
    }

    /**
     * Returns the given set of friends.
     *
     * @param start  inclusive start index
     * @param end  exclusive end index
     * @param onAjaxStart  function called if an Ajax call is made
     * @param onLoad  function called with the requested friends
     */
    this.getFriends = function(args) {
        var slice = friends.slice(args.start, args.end);
        var friendsAlreadyLoaded = true;
        for (var i = 0; i < slice.length; i++) {
            if (! slice[i]) {
                friendsAlreadyLoaded = false;
                break;
            }
        }
        if (friendsAlreadyLoaded) { return args.onLoad(slice); }
        args.onAjaxStart()
        var add = xg.shared.util.addParameter;
        dojo.io.bind({
            url: add(add(friendDataUrl, 'start', args.start), 'end', args.end),
            method: 'get',
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event) {
                var i = args.start;
                dojo.lang.forEach(data.friends, function(friend) {
                    if (! friends[i]) {
                        friends[i] = friend;
                        updateByFriendSet(friends[i]);
                    }
                    ++i;
                });
                args.onLoad(data.friends);
            })
        });
    }

    /**
     * Sets the base set of friends.
     *
     * @param friendSet  '', ALL_FRIENDS, or FRIENDS_ON_NETWORK
     */
    this.setFriendSet = function(newFriendSet) {
        friendSet = newFriendSet;
        for (var i = 0; i < friends.length; i++) {
            if (friends[i]) {
                updateByFriendSet(friends[i]);
            }
        }
    }

    /**
     * Returns the friend data.
     *
     * @return
     *     - friendSet - base set of friends: '', ALL_FRIENDS, FRIENDS_ON_NETWORK
     *     - screenNamesExcluded - screen names of friends to exclude from the base set
     *     - screenNamesIncluded - screen names of friends to include with the base set
     */
    this.getData = function() {
        var data = { friendSet: friendSet, screenNamesExcluded: [], screenNamesIncluded: [] };
        for (var i = 0; i < friends.length; i++) {
            var friend = friends[i];
            if (! friend) { continue; }
            if (friendSet == this.ALL_FRIENDS) {
                if (! friend.selected) { data.screenNamesExcluded.push(friend.screenName); }
            }
            else if (friendSet == this.FRIENDS_ON_NETWORK) {
                if (! friend.selected && friend.isMember) { data.screenNamesExcluded.push(friend.screenName); }
                if (friend.selected && ! friend.isMember) { data.screenNamesIncluded.push(friend.screenName); }
            }
            else {
                if (friend.selected) { data.screenNamesIncluded.push(friend.screenName); }
            }
        }
        return data;
    }

    /**
     * Returns the total number of friends of the current user.
     */
    this.getNumFriends = function() {
        return friends.length;
    }

};



