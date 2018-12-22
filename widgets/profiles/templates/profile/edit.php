<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('APPEARANCE'), null);
XG_App::ningLoaderRequire("xg.profiles.profile.edit"); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="xg_module">
              <div class="xg_module_body pad">
                    <?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'appearance')); ?>
                    <div class="right page_ticker_content">
						<form method="post" action="<%= xnhtmlentities($this->_buildUrl('profile', 'resetProfilePage')) %>" id="xg_return_to_default_form">
							<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
							<fieldset class="nolegend profile">
								<h3><%= xg_html('APPEARANCE') %></h3>
								<?php if (XG_App::membersCanCustomizeTheme()) {?>
									<p><%=xg_html('TO_CHANGE_THE_THEME', 'href="' . qh(W_Cache::getWidget('profiles')->buildUrl('appearance', 'edit')) . '"')%></p>
								<?php }?>
								<p><%= xg_html('TO_RETURN_TO_DEFAULT_CLICK') %></p>
								<p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('RETURN_MY_PAGE_TO_DEFAULT') %>" /></p>
							</fieldset>
						</form>
                    </div>
              </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
