<?php xg_header('manage',xg_text('FACEBOOK_SETUP')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('FACEBOOK_SETUP') .': '. $this->pageTitle)%>
            <div class="easyclear">
                <ul class="backlink navigation">
					<li><a href="<%=qh($this->_buildUrl('facebook','setup'))%>">&larr; <%= xg_html('FACEBOOK_BACK_TO_EMBEDDING') %></a></li>
                </ul>
            </div>
            <div class="xg_module instructions">
				<div class="xg_module_body pad">
                <?php if (! $this->upgrade) { ?>
                    <ul class="page_tabs">
                        <li <%= $this->currentStep == 1 ? 'class="this"' : ''%>><a href="?appType=<%= $this->appType %>&step=1">1. <%= xg_html('FACEBOOK_INSTR_FIRST_STEPS') %></a></li>
                        <li <%= $this->currentStep == 2 ? 'class="this"' : ''%>><a href="?appType=<%= $this->appType %>&step=2">2. <%= xg_html('FACEBOOK_INSTR_SETUP') %></a></li>
                        <li <%= $this->currentStep == 3 ? 'class="this"' : ''%>><a href="?appType=<%= $this->appType %>&step=3">3. <%= xg_html('TAB_AND_API_INFO') %></a></li>
                    </ul>
                <?php } ?>
					<?php $this->renderPartial("fragment_step" . $this->currentStep); ?>
				</div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
