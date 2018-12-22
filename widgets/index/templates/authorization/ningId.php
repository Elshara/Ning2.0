<?php xg_header(null, $title = xg_text('WHAT_IS_A_NING_ID'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <div class="xg_module_body pad">
                    <div class="easyclear">
                        <h3 class="left"><%= xnhtmlentities($title) %></h3>
						<p class="right"><strong><%= $this->noBack ? '<a href="#" onclick="if(window.opener)window.opener.focus(); window.close();return false;">' . xg_html('CLOSE') . '</a>' : xg_html('LARR_BACK', 'href="' . xnhtmlentities($this->previousUrl) . '"') %></strong></p>
                    </div>
                    <p><%= xg_html('NING_ID_LETS_YOU_CHOOSE') %></p>
                    <p><%= xg_html('WHEN_YOU_CREATE_NING_ID') %></p>
                    <p><%= xg_html('SIGN_IN_WITH_NING_ID_WHENEVER', '<img src="' . xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/ning/ningid.gif'))) . '" alt="" style="vertical-align:middle" />') %></p>
                </div>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
