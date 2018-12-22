dojo.provide('xg.index.invitation.FriendListViewport');

dojo.require('xg.shared.util');

/**
 * The visible area of a FriendList.
 *
 * @param friendListNode  DOM node for the FriendList
 * @param model  the FriendListModel
 */
xg.index.invitation.FriendListViewport = function(args) {

    /** DOM node for the FriendList */
    var friendListNode = args.friendListNode;

    /** The FriendListModel. */
    var model = args.model;

    /** The initial number of friends to retrieve from the server. */
    var initialNumFriendsToRetrieve = 200;

    /** Height of each item, in pixels */
    var rowHeight = 41; // Provisional value; will be updated in calibrateRowHeight() [Jon Aquino 2008-07-10]

    /** Whether the rowHeight has been adjusted */
    var rowHeightCalibrated = false;

    /** Number of rows to load above the viewport */
    var rowCountAboveViewport = 30;

    /** Number of rows to load below the viewport */
    var rowCountBelowViewport = 60;
    if (rowCountAboveViewport + 5 + rowCountBelowViewport > 100) { alert('Assertion failed: Keep rowCountAboveViewport + 5 + rowCountBelowViewport <= 100 to avoid multiple queries.'); }

    /** The <ul> node, which moves with the viewport */
    var ul = friendListNode.getElementsByTagName('ul')[0];

    /** Inclusive index of the first friend in the <ul> */
    var ulStart = 0;

    /** Exclusive index of the last friend in the <ul> */
    var ulEnd = 0;

    /**
     * Fires the callback when the user scrolls the list of friends.
     *
     * @param callback  function to call when the list is scrolled
     */
    var onScroll = function(callback) {
        // Wait for the scrollbar to stop moving for 200 ms before rendering [Jon Aquino 2008-07-09]
        var timer = xg.shared.util.createQuiescenceTimer(200, callback);
        var scrollTop = friendListNode.scrollTop;
        setInterval(function() {
            if (friendListNode.scrollHeight == 0) { return; } // Widget is not visible, e.g., the QuickAdd dialog is closed between submissions. [Jon Aquino 2008-07-10]
            if (scrollTop == friendListNode.scrollTop) { return; }
            scrollTop = friendListNode.scrollTop;
            timer.trigger();
        }, 50); // Poll the scrollbar position every 50 ms. [Jon Aquino 2008-07-09]
    };

    // ===== Private methods ==========================================

    /**
     * Returns the position of the friend at the top of the viewport.
     *
     * @return  the inclusive index
     */
    var getViewportStart = function() {
        return Math.floor(model.getNumFriends() * friendListNode.scrollTop / friendListNode.scrollHeight);
    };

    /**
     * Returns the position of the friend at the bottom of the viewport.
     * Note that this is the exclusive index (1 + the index).
     *
     * @return  the exclusive index
     */
    var getViewportEnd = function() {
        return Math.min(model.getNumFriends(), 1 + Math.floor(model.getNumFriends() * (friendListNode.scrollTop + friendListNode.clientHeight) / friendListNode.scrollHeight));
    };

    /**
     * Returns the number of rows to fetch with Ajax.
     *
     * @return  the number of friends that each Ajax request retrieves
     */
    var getRowCountToRetrieve = function() {
        return rowCountAboveViewport + Math.ceil(friendListNode.clientHeight/rowHeight) + rowCountBelowViewport;
    };

    /**
     * Checks if the viewport needs data.
     *
     * @return  the start and end indexes of the friends needed, or null if no data is needed
     */
    var getRequiredFriendIndexes = function() {
        if (ulStart <= getViewportStart() && getViewportEnd() <= ulEnd) { return null; }
        var indexes = {};
        indexes.start = Math.max(0, getViewportStart() - rowCountAboveViewport);
        indexes.end = indexes.start + getRowCountToRetrieve();
        return indexes;
    };

    /**
     * Updates the scrollbar to match the total height of the list.
     */
    var updateScrollbarExtent = function() {
        var fullExtentDiv = dojo.html.getElementsByClass('xj_full_extent', friendListNode)[0];
        fullExtentDiv.style.height = (rowHeight * model.getNumFriends()) + 'px';
    }

    /**
     * Sets the rowHeight to the true row height.
     */
    var calibrateRowHeight = function() {
        if (rowHeightCalibrated) { return; }
        rowHeightCalibrated = true;
        var lis = ul.getElementsByTagName('li');
        if (lis.length == 0) { return; } // Shouldn't happen [Jon Aquino 2008-07-10]
        if (lis[0].offsetHeight < 5 || 100 < lis[0].offsetHeight) { return; } // Shouldn't happen [Jon Aquino 2008-07-10]
        rowHeight = lis[0].offsetHeight;
        updateScrollbarExtent();
    }

    // ===== Public methods ==========================================

    /**
     * Initializes the viewport.
     */
    this.init = function() {
        var friendList = dojo.widget.manager.byNode(friendListNode)
        friendList.render(0, initialNumFriendsToRetrieve);
        onScroll(function() {
            var requiredFriendIndexes = getRequiredFriendIndexes();
            if (! requiredFriendIndexes) { return; }
            friendList.render(requiredFriendIndexes.start, requiredFriendIndexes.end);
        });
    }

    /**
     * Notifies this viewport that the friends have been rendered.
     *
     * @param start  index of the first friend rendered
     */
    this.renderingDone = function(start) {
        calibrateRowHeight();
        ulStart = start;
        ulEnd = start + ul.getElementsByTagName('li').length;
        ul.style.top = (start * rowHeight) + 'px';
    }

    /**
     * Returns the <ul> node.
     *
     * @return  the <ul>, which moves with the viewport
     */
    this.getUl = function() {
        return ul;
    }

};
