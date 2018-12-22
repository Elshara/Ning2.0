<?php xg_header('main',xg_text('OUR_APOLOGIES'), NULL); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('OUR_APOLOGIES'))%>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <big>
                            <p style="margin:0">
                                <%= xg_html('WE_ARE_SORRY_WE_ARE_HAVING') %>
                                <%= xg_html('FOLLOW_LINK_TO_HOMEPAGE', 'href="/"') %>
                            </p>
                        </big>
					</div>
					<div class="xg_module_body pad">
                        <p style="margin:0"><%= xg_html('IF_YOU_GET_THIS_MESSAGE', 'href="' . $this->_widget->buildUrl('index', 'feedback') . '"') %></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>