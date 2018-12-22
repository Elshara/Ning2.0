<?php XG_App::ningLoaderRequire('xg.index.facebook.instructions'); ?>
<p class="introduction"><%= $this->upgrade ? xg_html('IN_ORDER_TO_COMPLETE_UPDATE') : xg_html('IN_ORDER_TO_COMPLETE_SETUP') %></p>
<form id="setupNewEm" method="post" action="<%= xnhtmlentities($this->_buildUrl('facebook','createApp')) %>">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" name="appType" value="<%= $this->appType %>"/>
    <?php if ($this->upgrade) { ?>
        <input type="hidden" name="fbApiKey" id="fbApiKey" value="<%= qh($this->fbKey) %>" />
        <input type="hidden" name="fbApiSecret" id="fbApiSecret" value="<%= qh($this->fbSecret) %>" />
    <?php } ?>
    <dl id="setupNewEm_notify"<%= $this->error ? ' class="errordesc msg clear"' : ' style="display:none"' %>><%= $this->error ? '<dt>'. xg_html('THERE_IS_A_PROBLEM') .'</dt><dd><ol><li>' . $this->errorMessage . '</li></ol></dd>' : '' %></dl>
    <fieldset>
        <p>
            <label for="tabName"><%=xg_html('TAB_NAME')%></label><br />
            <%=xg_html('TAB_NAME_EXPLANATION')%>
			<br />
            <input name="tabName" id="tabName" type="text" value="<%= qh($this->tabName) %>" class="textfield required" maxlength="15" style="width:265px" />
        </p>
        <?php if (! $this->upgrade) { ?>
        <div class="legend"><%= xg_html('FACEBOOK_API_INFO_TITLE') %></div>
        <img class="right"src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/fb_new_steps3.gif') %>" width="370" height="280" alt="<%= xg_html('FACEBOOK_API_INFO_MY_APPLICATIONS_PAGE') %>" />
        <p><%= xg_html('FACEBOOK_API_INFO_FIND_KEY', 'href="http://www.facebook.com/developers/apps.php" target="_blank"') %></p>
        <p<%= $this->error && $this->errorKey === 'fbApiKey' ? ' class="error"' : '' %>>
            <label for="fbApiKey"><%= xg_html('FACEBOOK_API_INFO_KEY') %></label><br />
            <input name="fbApiKey" id="fbApiKey" type="text" value="<%= qh($this->fbKey) %>" class="textfield required error" size="50" style="width:265px" />
        </p>
        <p<%= $this->error && $this->errorKey === 'fbApiSecret' ? ' class="error"' : '' %>>
            <label for="fbApiSecret"><%= xg_html('FACEBOOK_API_INFO_SECRET') %></label><br />
            <input name="fbApiSecret" id="fbApiSecret" type="text" value="<%= qh($this->fbSecret) %>" class="textfield required" size="50" style="width:265px" />
        </p>
        <?php } ?>
    </fieldset>
        <?php if (! $this->upgrade) { ?>
	<h3><%=xg_html('ADDITIONAL_OPTIONS')%></h3>
	<p><%=xg_html('IF_YOU_D_LIKE_TO_FURTHER')%></p>
	<ol>
		<li><%=xg_html('CLICK_THE_EDIT_SETTINGS')%></li>
		<li><%=xg_html('CLICK_ON_CHANGE_YOUR')%></li>
		<li><%=xg_html('WRITE_AN_APPLICATION_DESCRIPTION')%></li>
	</ol>
	<p><%=xg_html('WHEN_YOU_RE_DONE')%></p>
        <?php } ?>
</form>
<p class="buttongroup">
    <?php if (! $this->upgrade) { ?>
	<a class="button" href="?appType=<%= $this->appType %>&step=2"><%= xg_html('BACK') %></a>
	<strong><a class="button" id="xj_fb_complete" href="#"><%= xg_html('COMPLETE_SETUP') %></a></strong>
    <?php } else { ?>
        <a class="button" href="<%= qh($this->_buildUrl('facebook', 'setup')) %>"><%= xg_html('CANCEL') %></a>
        <a class="button button-primary" id="xj_fb_complete" href="#"><%= xg_html('COMPLETE_UPDATE') %></a>
    <?php } ?>
</p>
