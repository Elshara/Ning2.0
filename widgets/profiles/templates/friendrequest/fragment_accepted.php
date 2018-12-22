<?php
/**
 * Displays an accepted friend request.
 *
 * @param $profile XN_Profile  the user whose friend request was accepted
 */ ?>
<p class="xg_lightfont last-child">
    <%= xg_avatar($profile, 64, 'xg_lightborder') %>
    <%= xg_userlink($profile, 'class="name"') %><br />
    <%= xg_html('IS_NOW_YOUR_FRIEND') %>
    <%= xg_send_message_link($profile->screenName, NULL, NULL, NULL, $this->_buildUrl('friendrequest', 'listReceived')) %>
</p>

