<?php
$len		 = XG_FacebookHelper::FACEBOOK_MAX_CANVAS_URL_LENGTH - mb_strlen($this->appType) - 1;
$suggestName = mb_substr(mb_strtolower(XN_Application::load()->relativeUrl),0,$len) . '-' . mb_strtolower($this->appType);
$suggestName = preg_replace('@[^a-z_-]@u', '', $suggestName);
?>
<p class="introduction"><%= xg_html('FACEBOOK_INSTR_SETUP_1') %></p>
<img class="right" height="305" width="370" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/fb_new_steps2.gif') %>" alt="<%= xg_html('FACEBOOK_INSTR_APPLICATION_SETUP_PAGE') %>" />
<h3><%=xg_html('REQUIRED_STEPS')%></h3>
<ol>
	<li><%=xg_html('CHOOSE_AN_APPLICATION_NAME')%></li>
	<li><%=xg_html('CHECK_TO_INDICATE')%></li>
	<li><%=xg_html('OPEN_AN_OPTIONAL_FIELDS')%></li>
	<li><%=xg_html('CHOOSE_A_CANVAS_PAGE_URL', xnhtmlentities($suggestName))%></li>
</ol>
<p><%= xg_html('WHEN_YOU_DONE_CLICK_SUBMIT') %></p>
<p class="buttongroup clear">
	<span class="nextstep"><%= xg_html('FACEBOOK_INSTR_SETUP_NEXT') %></span>
    <a class="button" href="?appType=<%= $this->appType %>&step=1"><%= xg_html('BACK') %></a>
    <a class="button button-primary" href="?appType=<%= $this->appType %>&step=3"><%= xg_html('NEXT') %></a>
</p>
