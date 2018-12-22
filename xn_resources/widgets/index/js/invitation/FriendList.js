dojo.provide('xg.index.invitation.FriendList');

dojo.require('xg.index.invitation.FriendListModel');
dojo.require('xg.index.invitation.FriendListViewport');
dojo.require('xg.shared.util');

/**
 * An Ajax list that can display large numbers of friends.
 * Implemented as a <ul> that shows only the friends in and near the
 * visible area (the "viewport").
 *
 * 1) If the user clicks Select None then selects some friends:
 *     a) $_POST['friendSet'] will be ''
 *     b) $_POST['screenNamesIncluded'] will contain a JSON array of screen names to include
 *
 * 2) If the user clicks Select All Friends then unselects some friends
 *     a) $_POST['friendSet'] will be Index_MessageHelper::ALL_FRIENDS
 *     b) $_POST['screenNamesExcluded'] will contain a JSON array of screen names to exclude
 *
 * 3) If the user clicks Select Friends on this Network then unselects some friends and selects friends not on the network:
 *     a) $_POST['friendSet'] will be Index_MessageHelper::FRIENDS_ON_NETWORK
 *     b) $_POST['screenNamesExcluded'] will contain a JSON array of screen names to exclude
 *     c) $_POST['screenNamesIncluded'] will contain a JSON array of screen names to include
 *
 * @see Index_MessageController::action_friendList
 */
dojo.widget.defineWidget('xg.index.invitation.FriendList', dojo.widget.HtmlWidget, {

    /** The initially selected set of friends: '', Index_MessageHelper::ALL_FRIENDS, or Index_MessageHelper::FRIENDS_ON_NETWORK */
    _initialFriendSet: '',

    /** The total number of friends that will appear in the list. */
    _numFriends: 0,

    /** The number of friends that can be selected. */
    _numSelectableFriends: 0,

    /** The number of friends (on the current network) that can be selected. Not used if the "Select Friends On This Network" link is hidden. */
    _numSelectableFriendsOnNetwork: 0,

    // TODO: Return _numFriends, _numSelectableFriends, and _numSelectableFriendsOnNetwork
    // in the first Ajax call to _friendDataUrl, because these values are expensive to compute
    // (unnecessarily, if the FriendList is kept hidden). We will probably need to do this anyway
    // when we implement search (BAZ-8948) [Jon Aquino 2008-08-26]

    /** Endpoint for retrieving friends. */
    _friendDataUrl: '',

    /** Whether to display user thumbnails, or remove them to improve performance. */
    _showAvatars: true,

    /** The FriendListModel */
    model: null,

    /** The FriendListViewport */
    viewport: null,

    /** Number of times render() has been called */
    renderCount: 0,

    /** The spinner image */
    spinner: null,

    /**
     * The number of friends that are currently selected.
     * Equal to _numSelectableFriends when the "Select All Friends" link is clicked
     * Equal to _numSelectableFriendsOnNetwork when the "Select Friends On This Network" link is clicked
     *
     * @access public
     */
    selectedFriendCount: 0,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var div = this.getFragNodeRef(frag);
        this.fixVariables();
        this.model = new xg.index.invitation.FriendListModel({friendDataUrl: this._friendDataUrl, numFriends: this._numFriends});
        this.viewport = new xg.index.invitation.FriendListViewport({friendListNode: div, model: this.model });
        this.clicked(this._initialFriendSet);
        this.initializeLink(xg.$('.xj_all_friends', div.parentNode), this.model.ALL_FRIENDS);
        this.initializeLink(xg.$('.xj_network_friends', div.parentNode), this.model.FRIENDS_ON_NETWORK);
        this.initializeLink(xg.$('.xj_none', div.parentNode), '');

    },

    // ===== Public methods ==========================================

    /**
     * Sets the values of the hidden inputs. Called when the form is about to be submitted.
     */
    updateHiddenInputs: function() {
        var data = this.model.getData();
        var form = dojo.dom.getFirstAncestorByTag(this.viewport.getUl(), 'form');
        form.friendSet.value = data.friendSet;
        form.screenNamesExcluded.value = dojo.json.serialize(data.screenNamesExcluded);
        form.screenNamesIncluded.value = dojo.json.serialize(data.screenNamesIncluded);
    },

    /**
     * Resets the widget as much as possible.
     *
     * @param selectAll  Whether to select all friends
     */
    reset: function(selectAll) {
        this.clicked(selectAll ? this.model.ALL_FRIENDS : '');
    },

    // ===== Private methods ==========================================

    /**
     * Converts the bound variables from objects to integers where needed.
     */
    fixVariables: function() {
        // Dojo sets these to objects; change them into numbers. [Jon Aquino 2008-08-12]
        this._numFriends *= 1;
        this._numSelectableFriends *= 1;
        this._numSelectableFriendsOnNetwork *= 1;
    },

    /**
     * Initializes the Select link.
     *
     * @param link  the <a> element, or null
     * @param friendSet  name of the associated set of friends
     */
    initializeLink: function(link, friendSet) {
        if (! link) { return; }
        dojo.event.connect(link, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.clicked(friendSet);
        }));
    },

    /**
     * Sets the number of friends that are currently selected.
     *
     * @param selectedFriendCount  the number of friends that the user has chosen
     */
    setSelectedFriendCount: function(selectedFriendCount) {
        this.selectedFriendCount = selectedFriendCount;
        xg.$('.xj_selected_friend_count', this.domNode.parentNode).innerHTML = xg.index.nls.html('nFriendsSelected', this.selectedFriendCount);
    },

    /**
     * Handles the clicking of the "Select All Friends", "Select Friends on this Network" and "Select None" links
     *
     * @param friendSet  base set of friends: null, this.model.ALL_FRIENDS, or this.model.FRIENDS_ON_NETWORK
     */
    clicked: function(friendSet) {
        this.model.setFriendSet(friendSet);
        this.setSelectedFriendCount(friendSet == this.model.ALL_FRIENDS ? this._numSelectableFriends : (friendSet == this.model.FRIENDS_ON_NETWORK ? this._numSelectableFriendsOnNetwork : 0));
        dojo.lang.forEach(this.viewport.getUl().getElementsByTagName('input'), dojo.lang.hitch(this, function(input) {
            if (! input.disabled) {
                input.checked = friendSet == this.model.ALL_FRIENDS || (friendSet == this.model.FRIENDS_ON_NETWORK && dojo.html.hasClass(input, 'xj_member'));
            }
        }));
    },

    /**
     * Finishes the initialization. May take a few seconds, so delay it as long as possible.
     */
    init: function() {
        this.viewport.init();
    },

    /**
     * Renders the friends from the start index to the end index.
     *
     * @param start  inclusive start index
     * @param end  exclusive start index
     */
    render: function(start, end) {
        var originalRenderCount = ++this.renderCount;
        this.model.getFriends({
            start: start,
            end: end,
            onAjaxStart: dojo.lang.hitch(this, function() {
                this.setSpinnerVisible(true);
            }),
            onLoad: dojo.lang.hitch(this, function(friends) {
                if (this.renderCount != originalRenderCount) { return; }
                this.renderProper(friends, start);
                this.viewport.renderingDone(start);
                this.setSpinnerVisible(false);
            })
        });
    },

    /**
     * Renders the friends
     *
     * @param friends  the friends to display
     * @param start  inclusive start index
     */
    renderProper: function(friends, start) {
        var i = start;
        var listItems = [];
        dojo.lang.forEach(friends, dojo.lang.hitch(this, function(friend) {
            // Array.join is 25x faster than + in IE [Jon Aquino 2007-12-29]
            listItems.push(['<li', friend.reasonToDisable ? ' class="member"' : '', '><label><input type="checkbox" class="checkbox', friend.isMember ? ' xj_member' : '', '" onclick="dojo.widget.manager.getWidgetById(\'', this.widgetId, '\').checkboxClicked(this, \'', i, '\')"', friend.selected ? ' checked="checked"' : '', friend.reasonToDisable ? ' disabled="disabled"' : '', ' /> ', this._showAvatars ? '' : '<!--', '<img src="', friend.thumbnailUrl, '" width="32" height="32" alt="" />', this._showAvatars ? '' : '-->', ' <span class="name">', dojo.string.escape('html', friend.fullName), friend.reasonToDisable ? ' <small>' + friend.reasonToDisable + '</small>' : '', '</span></label></li>'].join(''));
            ++i;
        }));
        this.viewport.getUl().innerHTML = listItems.join('');
    },

    /**
     * Called when the user clicks a checkbox.
     *
     * @param checkbox  the checkbox input element
     * @param i  index of the friend
     */
    checkboxClicked: function(checkbox, i) {
        var friend = this.model.getFriend(i);
        friend.selected = checkbox.checked;
        this.setSelectedFriendCount(Math.max(0, this.selectedFriendCount + (friend.selected ? 1 : -1)));
    },

    /**
     * Shows or hides the spinner.
     *
     * @param spinnerVisible  whether to show the spinner in the upper-left corner
     */
    setSpinnerVisible: function(spinnerVisible) {
        if (! this.spinner) {
            this.spinner = dojo.html.createNodesFromText('<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="spinner" style="position: absolute; width: 20px; height: 20px; padding: 10px;" />')[0];
            this.domNode.appendChild(this.spinner);
        }
        this.spinner.style.top = this.domNode.scrollTop + 'px';
        dojo.style.setShowing(this.spinner, spinnerVisible);
    }

});
