        <p>
            <a<?php if ($this->showLinks) { ?> href="<%= User::quickProfileUrl($this->invitingUser->screenName) %>"<?php } ?>><img class="photo" alt="<%= xnhtmlentities(XG_UserHelper::getFullName($this->invitingUser)) %>" src="<%= XG_UserHelper::getThumbnailUrl($this->invitingUser, 32, 32) %>" height="32" width="32" /></a>
            <%= xg_html('USER_HAS_INVITED_YOU_TO_JOIN_NETWORK',
            	'<strong>' . ($this->showLinks ? xg_userlink($this->invitingUser) : xnhtmlentities(xg_username($this->invitingUser))) . '</strong>',
            	XN_Application::load()->name) %>
        </p>
