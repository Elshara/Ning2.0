<?php xg_header(null, $title = xg_text('FORGOT_YOUR_PASSWORD_TITLE'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <div class="xg_module_body pad">
                    <div class="easyclear">
                        <h3 class="left"><%= xnhtmlentities($title) %></h3>
                        <p class="right"><strong><%= xg_html('LARR_BACK', 'href="' . xnhtmlentities($this->previousUrl) . '"') %></strong></p>
                    </div>
                    <div class="success">
                        <p class="last-child"><%= xg_html('WE_SENT_YOU_EMAIL') %></p>
                    </div>
                </div>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
