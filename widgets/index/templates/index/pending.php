<?php xg_header(null, xg_text('MEMBERSHIP_PENDING_APPROVAL'), null, array('displayHeader' => false, 'xgDivClass' => 'account')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child">
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <h3><%= xg_html('YOUR_PROFILE_IS_PENDING_APPROVAL') %></h3>
                    <?php XG_App::ningLoaderRequire('xg.shared.PostLink') ?>
                    <p><%= xg_html('HELLO_USERNAME_SIGN_OUT', xnhtmlentities(xg_username($this->_user)), 'href="#" dojoType="PostLink" _url="' . xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) . '"') %></p>
                    <p><%= xg_html('YOUR_PROFILE_DETAILS_MUST_BE_APPROVED_ON_X', xnhtmlentities(XN_Application::load()->name)) %></p>
                    <?php if ($this->continueUrl) { ?>
                    <p class="last-child"><big><a href="<%= xnhtmlentities($this->continueUrl) %>"><%= xg_html('CONTINUE_ARROW') %></a></big></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>#xn_bar_menu_search {display: none}</style>
<?php xg_footer(null, array('displayFooter' => false)) ?>
