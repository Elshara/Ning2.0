<?php xg_header(null, $title = xg_text('PROBLEMS_SIGNING_UP_OR_SIGNING_IN'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <div class="xg_module_body pad">
                    <div class="easyclear">
                        <h3 class="left"><%= xnhtmlentities($title) %></h3>
                        <p class="right"><strong><%= $this->noBack ? '<a href="#" onclick="if(window.opener)window.opener.focus(); window.close();return false;">' . xg_html('CLOSE') . '</a>' : xg_html('LARR_BACK', 'href="' . xnhtmlentities($this->previousUrl) . '"') %></strong></p>
                    </div>
                    <h4><%= xg_html('FORGOT_YOUR_PASSWORD_TITLE') %></h4>
                    <p class="last-child"><a href="<%= xnhtmlentities($this->_buildUrl('authorization', 'requestPasswordReset', array('previousUrl' => $this->previousUrl))) %>"><%= xg_html('CLICK_HERE_TO_RESET_PASSWORD') %></a></p>
                </div>
                <div class="xg_module_body pad">
                    <h4><%= xg_html('SYSTEM_REQUIREMENTS') %></h4>
                    <p><%= xg_html('WE_SUPPORT_INTERNET_EXPLORER') %></p>
                    <p class="last-child"><%= xg_html('MAKE_SURE_COOKIES') %></p>
                </div>
                <div class="xg_module_body pad">
                    <h4><%= xg_html('WHAT_IS_MY_EMAIL') %></h4>
                    <p class="last-child"><%= xg_html('IF_JOINED_BEFORE_OCTOBER') %></p>
                </div>
                <div class="xg_module_body pad">
                    <h4><%= xg_html('STILL_HAVING_PROBLEMS') %></h4>
                    <p class="last-child"><%= xg_html('VISIT_NING_HELP_CENTER', 'href="http://help.ning.com/?page_id=19"') %></p>
                </div>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
