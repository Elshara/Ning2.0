<?php xg_header('manage', $title = xg_text('DOMAIN_REDIRECTION')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <h1><%= xnhtmlentities($title) %></h1>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <?php
                    if ($this->errors) { ?>
                        <p class="errordesc"><%= reset($this->errors) %></p>
                    <?php
                    } elseif ($this->saved) { ?>
                        <p class="success"><%= xg_html('CHANGES_SAVED_SUCCESSFULLY') %></p>
                    <?php
                    } ?>
                    <form method="post" action="<%= xnhtmlentities($this->_buildUrl('index', 'updateDomainRedirection')) %>">
                        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                        <p><%= xg_html('IF_YOU_HAVE_USE_DOMAIN_NAME') %></p>
                        <p>
                            <%= xg_html('REDIRECT_TO') %>
                            <%= $this->form->select('domainName', $this->domainNames, false) %>
                        </p>
                        <p><input type="submit" class="button" value="<%= xg_html('SAVE') %>"></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
