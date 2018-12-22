<?php xg_header(null, $title = xg_text('PAGES')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
			<div class="xg_module">
				<div class="xg_module_body">
					<form _url="<%= xnhtmlentities($this->_buildUrl('instance', 'update', array('xn_out' => 'json'))) %>" method="post">
						<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
						<div class="xg_module">
							<div class="xg_module_body pad">
								<p class="success" <%= $this->saved ? '' : 'style="display:none"' %>><%= xg_html('CHANGES_SAVED_SUCCESSFULLY') %></p>
								<p>
									<%= xg_html('BETA_FEATURE_RECOMMENDED') %><br />
									<%= xg_html('IF_NOT_USED_FEATURE', 'href="/notes"') %>
								</p>
								<div id="xg_pages_container"></div>
							</div>
						</div>
						<p>
							<input type="submit" class="button" value="<%= xg_html('SAVE') %>" style="display:none" />
							<img id="spinner" src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/spinner.gif')) %>" alt="" width="20" height="20" style="display:none" />
						</p>
					</form>
					<input type="hidden" id="data" value="<%= xnhtmlentities($this->data) %>" />
	            </div>
            </div>
        </div>
        <?php /* TODO: Is the xg_1col block needed? [Jon Aquino 2008-04-16] */ ?>
        <div class="xg_1col">
            <div class="xg_1col first-child">
	            <?php xg_sidebar($this); ?>
            </div>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.page.instance.edit'); ?>
<?php xg_footer(); ?>
