<?php
//  Don't set Google ad colors on error pages - that action can result in a 404 loop - DC
xg_header('main',xg_text('PAGE_NOT_FOUND'), NULL, array('hideAdColors' => TRUE));
?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('THIS_PAGE_WAS_NOT_FOUND'))%>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <big>
                        <p>
                            <%= xg_html('FOLLOW_THESE_LINKS_TO') %>
                        </p>
                        <ul>
                    <?php foreach ($this->links as $label => $href) { ?>
                            <li><a href="<%= $href %>"><%= $label %></a></li>
                    <?php } ?>
                        </ul>
                        <p>
                            <form method="get" action="<%= $this->_widget->buildUrl('search', 'search') %>">
                                <label for="search"><%= xg_html('ALTERNATIVELY_YOU_CAN_SEARCH') %></label>
                                <input id="search" name="q" type="text" class="textfield" size="25" />
                                <input type="submit" class="button" value="<%= xg_html('SEARCH') %>" />
                            </form>
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