<?php xg_header(null, $title = xg_text('CREATE_NEW_PASSWORD'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <form class="xg_module_body pad" action="<%= xnhtmlentities($this->_buildUrl('authorization', 'updatePassword')) %>" method="post">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <h3><%= xnhtmlentities($title) %></h3>
                    <p><%= xg_html('SET_NEW_PASSWORD') %></p>
                    <div class="errordesc" <%= $this->errors ? '' : 'style="display: none"' %>>
                        <h4><%= xg_html('A_PROBLEM_OCCURRED') %></h4>
                        <p class="last-child"><%= reset($this->errors) %></p>
                    </div>
                    <fieldset class="nolegend account" id="signin">
                        <dl>
                            <dt><label><big><%= xg_html('EMAIL_ADDRESS') %></big></label></dt>
                            <dd class="prefilled"><big><%= xnhtmlentities(XN_Profile::current()->email) %></big></dd>
                        </dl>
                        <dl>
                            <dt><label for="signin_password"><big><%= xg_html('NEW_PASSWORD') %></big></label></dt>
                            <dd><%= $this->form->password('password', 'id="signin_password" class="password large" size="20"') %></dd>
                        </dl>
                        <dl>
                            <dd><big><input type="submit" class="button" value="<%= xg_html('SET_PASSWORD') %>" /></big></dd>
                        </dl>
                    </fieldset>
                </form>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
