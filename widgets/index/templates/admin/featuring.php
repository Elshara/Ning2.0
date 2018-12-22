<?php xg_header('manage',xg_text('MANAGE')); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('FEATURING_CONTENT'))%>
                <div class="xg_colgroup">
                    <div class="xg_3col first-child">
                        <div class="xg_module">
                            <div class="xg_module_body">
                            <p><%= xg_html('YOU_CAN_USE_THE_FEATURE_OPTION') %></p>
                            <ol>
                                <li><%= xg_html('FEATURING_1') %></li>
                                <li><%= xg_html('FEATURING_2') %>
                                <p><br/><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/admin/omf-photo-feature.jpg') %>" width="445" height="297" /></p>
                                </li>
                                <li><%= xg_html('FEATURING_3') %></li>
                                <li><%= xg_html('FEATURING_4') %>
                                <p><br/><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/admin/omf-photo-featured.jpg') %>" width="470" height="191" /></p>
                                </li>
                            </ol>
                            <p><%= xg_html('TO_STOP_FEATURING') %></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this) ?>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
