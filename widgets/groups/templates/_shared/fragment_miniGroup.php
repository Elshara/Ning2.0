<?php
 /**
 * Brief summary of a group, for embeds.
 *
 * @param $group XN_Content|W_Content  the Group object
 * @param $avatarWidth integer  the width to use for avatars, in pixels
 * @param $showCreatedBy boolean  whether to show the name of the person who created the group
 * @param $lastChild boolean whether to add the last-child class to the list item
 * @
 */ ?>
    <li class="xg_lightborder <%= $lastChild ? 'last-child' : '' %>">
        <div class="ib">
            <a href="<%= xnhtmlentities($this->_buildUrl('group', 'show', array('id' => $group->id))) %>"><img src="<%= xnhtmlentities(Group::iconUrl($group, $avatarWidth)) %>" width="<%= $avatarWidth %>" height="<%= $avatarWidth %>" alt="" /></a>
        </div>
        <div class="tb">
            <h3><a href="<%= xnhtmlentities($this->_buildUrl('group', 'show', array('id' => $group->id))) %>"><%= xnhtmlentities($group->title) %></a></h3>
            <p class="xg_lightfont"><%= xg_html('N_MEMBERS', $group->my->memberCount) %></p>
        </div>
    </li>