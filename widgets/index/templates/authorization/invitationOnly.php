<?php xg_header(null, xg_text('MEMBERSHIP_BY_INVITATION_ONLY'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <div class="xg_module_body pad">
                    <h3><%= xg_html('MEMBERSHIP_TO_APPNAME_BY_INVITATION_ONLY', xnhtmlentities(XN_Application::load()->name)) %></h3>
                    <?php XG_App::ningLoaderRequire('xg.shared.PostLink') ?>
                    <p><%= xg_html('HELLO_USERNAME_SIGN_OUT', xnhtmlentities(xg_username($this->_user)), 'href="#" dojoType="PostLink" _url="' . xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) . '"') %></p>
                    <p class="last-child"><%= xg_html('SORRY_BUT_ADMINISTRATOR_REQUIRES_INVITATION') %></p>
                </div>
                <?php $this->_widget->dispatch('authorization', 'footer', array(false)); ?>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
