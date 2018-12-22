<?php
/**
 * Displays the Popular Contributors module
 *
 * @param $users array  The User XN_Content objects
 * @param $showViewUsersLink boolean Whether to show the link to the Popular Contributors page
 */ ?>
<div class="xg_module">
    <div class="xg_module_body">
        <h3><%= xg_html('POPULAR_CONTRIBUTORS') %></h3>
        <?php
        foreach ($users as $user) { ?>
            <dl class="vcard">
                <dt><%= xg_avatar(XG_Cache::profiles($user->title), 32) %> <%= xg_userlink(XG_Cache::profiles($user->title), null, true) %></dt>
                <dd><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForContributor', array('user' => $user->title))) %>" class="nobr"><%= xg_html('VIEW_DISCUSSIONS') %></a></dd>
            </dl>
        <?php
        } ?>
    </div>
    <?php
    if ($showViewUsersLink) { ?>
        <div class="xg_module_foot">
            <p class="right"><a href="<%= xnhtmlentities($this->_buildUrl('user','list')) %>"><%= xg_html('VIEW_MORE_CONTRIBUTORS') %></a></p>
        </div>
    <?php
    } ?>
</div>

