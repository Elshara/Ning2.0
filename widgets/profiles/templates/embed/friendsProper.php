<?php
xg_autodiscovery_link($this->feedUrl, xg_text('XS_FRIENDS', ucfirst(xg_username($this->profile))), 'atom'); ?>
<div class="xg_module module_members">
    <div class="xg_module_head">
        <h2><%= $this->embed->isOwnedByCurrentUser() ? xg_html('MY_FRIENDS') : xg_html('XS_FRIENDS', xnhtmlentities(ucfirst(xg_username($this->profile)))); %></h2>
    </div>

    <div class="xg_module_body body_small">
    <?php if (count($this->friendProfiles)) {
        echo "<ul class=\"clist\">";
        $n = 0;
        foreach ($this->friendProfiles as $friend) { ?>
            <li><a href="<%= User::quickProfileUrl($friend->screenName) %>" title="<%= xnhtmlentities(XG_UserHelper::getFullName($friend)) %>"><img class="<%= $n++ % 3 == 0 ? 'newrow ' : '' %>photo" src="<%= XG_UserHelper::getThumbnailUrl($friend, 48, 48) %>" alt="" height="48" width="48" style="width:48px; height:48px"></a></li>
    <?php }
        echo "</ul>\n";
    } else {
        if ($this->embed->isOwnedByCurrentUser() && XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
            <p><a href="/invite" class="button"><%= xg_html('INVITE_FRIENDS') %></a></p>
        <?php
        } elseif ($this->embed->isOwnedByCurrentUser()) {
            echo '<p>'.xg_html('YOU_DO_NOT_HAVE_ANY_FRIENDS').'</p>';
        } else {
            echo '<p>'.xg_html('X_DOES_NOT_HAVE_ANY_FRIENDS', xnhtmlentities(xg_username($this->profile))).'</p>';
        }
    } ?>
    </div>
    <?php
    $listItems = array();
    if ($this->embed->isOwnedByCurrentUser() && XG_App::canSeeInviteLinks(XN_Profile::current())) {
        $listItems[] = '<li class="left"><a href="/invite" class="add desc">' . xg_html('INVITE_MORE') . '</a></li>';
    } elseif ($this->embed->isOwnedByCurrentUser()) {
    }
    if (count($this->friendProfiles)) {
        $listItems[] = '<li class="right"><a href="' . User::quickFriendsUrl($this->profile->screenName) . '">' . xg_html('VIEW_ALL') . '</a></li>';
    }
    if ($listItems) { ?>
        <div class="xg_module_foot">
            <ul>
                <?php
                foreach ($listItems as $listItem) {
                    echo $listItem;
                } ?>
            </ul>
        </div>
    <?php
    } ?>
</div>
