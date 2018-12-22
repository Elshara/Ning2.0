<?php XG_App::ningLoaderRequire('xg.index.invitation.FriendList'); ?>
<div dojoType="FriendList" class="friend_list xj_friend_list"
        _showAvatars="true"
        _friendDataUrl="<%= xnhtmlentities($this->friendDataUrl) %>"
        _initialFriendSet="<%= $this->initialFriendSet %>"
        _numFriends="<%= $this->numFriends %>"
        _numSelectableFriends="<%= $this->numSelectableFriends %>"
        _numSelectableFriendsOnNetwork="<%= $this->numSelectableFriendsOnNetwork %>">
    <div class="xj_full_extent"><ul></ul></div>
    <?php /* Put hidden field inside the div, as it creates a space in IE7 when placed outside the div [Jon Aquino 2008-01-17] */ ?>
    <input type="hidden" name="friendSet" value="" />
    <input type="hidden" name="screenNamesIncluded" value="" />
    <input type="hidden" name="screenNamesExcluded" value="" />
</div>
<div class="friendlist_options">
    <p class="select-all">
        <%= xg_html('SELECT_COLON') %>
        <?php
        if ($this->showSelectAllFriendsLink) { ?>
            <a href="#" class="xj_all_friends"><%= xg_html('ALL_FRIENDS') %></a>,
        <?php
        }
        if ($this->showSelectFriendsOnNetworkLink) { ?>
            <a href="#" class="xj_network_friends"><%= xg_html('FRIENDS_ON_NETWORK') %></a>,
        <?php
        } ?>
        <a href="#" class="xj_none"><%= xg_html('NONE') %></a></p>
    <p class="count xj_selected_friend_count"></p>
</div>
