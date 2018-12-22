<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('PROFILE')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title) %>
            <div class="xg_module">
                <form class="xg_module_body pad" method="post" action="<%= xnhtmlentities($this->_buildUrl('settings', 'updatePassword')) %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
					<?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'profile')); ?>
                    <div class="right page_ticker_content">
                        <fieldset class="nolegend">
                            <h3><%= xg_html('CHANGE_PASSWORD') %></h3>
                            <dl class="errordesc msg" <%= $this->errors ? '' : 'style="display: none"' %>>
                                <dt><%= xg_html('A_PROBLEM_OCCURRED') %></dt>
                                <dd>
                                <ol>
                                        <?php
                                        foreach ($this->errors as $error) { ?>
                                            <li><%= $error %></li>
                                        <?php
                                        } ?>
                                    </ol>
                                </dd>
                            </dl>
                            <dl<%= $this->errors['currentPassword'] ? ' class="error"' : '' %>>
                                <dt><label for="current-password"><%= xg_html('CURRENT_PASSWORD') %></label></dt>
                                <dd><%= $this->form->password('currentPassword', 'id="current-password" class="textfield" style="width:250px"') %></dd>
                            </dl>
                            <dl<%= $this->errors['newPassword'] ? ' class="error"' : '' %>>
                                <dt><label for="new-password"><%= xg_html('NEW_PASSWORD') %></label></dt>
                                <dd><%= $this->form->password('newPassword', 'id="new-password" class="textfield" style="width:250px"') %></dd>
                            </dl>
                            <dl<%= $this->errors['confirmPassword'] ? ' class="error"' : '' %>>
                                <dt><label for="confirm-password"><%= xg_html('CONFIRM_PASSWORD') %></label></dt>
                                <dd><%= $this->form->password('confirmPassword', 'id="confirm-password" class="textfield" style="width:250px"') %></dd>
                            </dl>
                            <p class="buttongroup">
                                <input type="submit" class="button button-primary" value="<%= xg_html('SAVE') %>" />
                                <a class="button" href="<%= xnhtmlentities($this->_buildUrl('settings', 'editProfileInfo')) %>"><%= xg_html('CANCEL') %></a>
                            </p>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<script>document.getElementById('current-password').focus();</script>
<?php xg_footer(); ?>
