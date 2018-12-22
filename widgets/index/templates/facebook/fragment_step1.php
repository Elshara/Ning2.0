<p class="introduction">
    <big><strong><a href="http://www.facebook.com/developers" target="_blank"><%= xg_html('FACEBOOK_INSTR_CLICK_HERE_TO_GO_TO_FACEBOOK') %></a></strong></big><br/>
    <%= xg_html('FACEBOOK_INSTR_OPEN_FACEBOOK_IN_NEW_WINDOW') %>
</p>
<ul class="easyclear">
    <li>
        <p><img class="right" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/adddeveloper.gif') %>" width="175" height="45" alt="<%= xg_html('FACEBOOK_ADD_DEVELOPER_BUTTON') %>" />
        <%= xg_html('IF_YOU_HAVENT_CREATE_AN_APPLICATION') %></p>
    </li>
    <li>
        <p><img class="right" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/setupnewapplication.gif') %>" width="175" height="45" alt="<%= xg_html('FACEBOOK_SETUP_NEW_APP_BUTTON') %>" />
        <%= xg_html('IF_YOUVE_ALREAY_CREATED_AN_APPLICATION') %></p>
    </li>
</ul>
<p class="buttongroup">
	<span class="nextstep"><%= xg_html('FACEBOOK_INSTR_FIRST_STEPS_NEXT', $this->pageTitle) %></span>
	<a class="button button-primary" href="?appType=<%= $this->appType %>&step=2"><%= xg_html('NEXT') %></a>
</p>
