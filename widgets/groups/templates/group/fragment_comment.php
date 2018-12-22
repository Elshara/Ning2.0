<?php // used for displaying comments in Group search results ?>
<div class="bd">
    <div class="ib">
        <a href="/xn/detail/<%= xnhtmlentities($object->id) %>">
            <img src="<%= xnhtmlentities(Group::iconUrl($group, 82)) %>" width="82" height="82" alt="<%= xnhtmlentities($group->title) %>" />
        </a>
    </div>
    <div class="tb">
        <h3>
            <%= xg_html('COMMENT_ON') %> <a href="/xn/detail/<%= xnhtmlentities($object->id) %>"><%= xnhtmlentities($group->title) %></a>
        </h3>
        <p>
            <span class="item_quote"><%= xg_html('QUOTED_TEXT', xnhtmlentities(xg_excerpt($object->description, 80))) %></span>
            <span class="item_added"><%= xg_html('ADDED_BY_X_T', xg_userlink(XG_Cache::profiles($object->contributorName)), xnhtmlentities(xg_date(xg_text('F_J_Y'), $object->createdDate)), xnhtmlentities(xg_date(xg_text('G_IA'), $object->createdDate))) %></span>
        </p>
    </div>
</div>
