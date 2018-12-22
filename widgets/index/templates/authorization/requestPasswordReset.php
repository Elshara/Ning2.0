<?php xg_header(null, $title = xg_text('FORGOT_YOUR_PASSWORD_TITLE'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <form action="<%= xnhtmlentities($this->_buildUrl('authorization', 'doRequestPasswordReset')) %>" method="post" class="xg_module xg_lightborder">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <%= $this->form->hidden('previousUrl') %>
                <div class="xg_module_body pad">
                    <div class="easyclear">
                        <h3 class="left"><%= xnhtmlentities($title) %></h3>
                        <p class="right"><strong><%= xg_html('LARR_BACK', 'href="' . xnhtmlentities($this->previousUrl) . '"') %></strong></p>
                    </div>
                    <p><%= xg_html('ENTER_EMAIL_AND_CLICK_RESET_PASSWORD') %></p>
                    <div class="errordesc" <%= $this->errors ? '' : 'style="display: none"' %>>
                        <h4><%= xg_html('A_PROBLEM_OCCURRED') %></h4>
                        <p class="last-child"><%= reset($this->errors) %></p>
                    </div>
                    <fieldset class="nolegend account" id="signin">
                        <dl style="margin:0.6em 0;">
                            <dt><label for="signin_id"><%= xg_html('EMAIL_ADDRESS') %></label></dt>
                            <dd><%= $this->form->text('emailAddress', 'id="signin_id" class="textfield" size="20"') %></dd>
                            <dd><input type="submit" class="button" value="<%= xg_html('RESET_PASSWORD') %>" /></dd>
                        </dl>
                    </fieldset>
                </div>
            </form>
</div>
<script>document.getElementById('signin_id').focus();</script>
<?php xg_footer(null, array('displayFooter' => false)) ?>
