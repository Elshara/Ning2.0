<?php xg_header('profile', $title = xg_text('MY_SETTINGS') . ' - ' . xg_text('PROFILE')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <%= xg_headline($title) %>
            <div class="xg_module">
                <form class="xg_module_body pad" method="post" action="<%= xnhtmlentities($this->_buildUrl('settings', 'updateProfileAddress')) %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <?php $this->renderPartial('fragment_settingsNavigation', '_shared', array('selected' => 'profile')); ?>
                    <div class="right page_ticker_content">
                        <fieldset class="nolegend">
                            <h3><%= xg_html('CHANGE_PAGE_ADDRESS') %></h3>
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
                            <dl>
                                <dt><label for="profile-address"><%= xg_html('PAGE_ADDRESS') %></label></dt>
                                <dd>http://<%= xnhtmlentities(xg_excerpt($_SERVER['HTTP_HOST'], 30)) %>/profile/<%= $this->form->text('profileAddress', 'id="profile-address" class="textfield" style="width:100px" maxlength="' . User::MAX_PROFILE_ADDRESS_LENGTH . '"') %></dd>
                            </dl>
                            <p class="buttongroup">
                                <input type="submit" class="button button-primary" value="<%= xg_html('SAVE') %>" />
                                <a href="<%= xnhtmlentities($this->_buildUrl('settings', 'editProfileInfo')) %>" class="button"><%= xg_html('CANCEL') %></a>
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
<script>document.getElementById('profile-address').focus();</script>
<?php xg_footer(); ?>
