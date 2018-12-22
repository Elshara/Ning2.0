<?php XG_IPhoneHelper::header(null, $title = xg_text('WHAT_IS_A_NING_ID'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true, 'noHead' => true)); ?>
<?php XG_App::addToSection('<meta http-equiv="refresh" content="0;url=' . xnhtmlentities($this->signOutUrl) . '" />'); ?>
<div id="xg_body">
    <div class="xg_module xg_lightborder">
        <div class="xg_module_body pad">
            <img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/spinner.gif'))) %>" width="20" height="20" alt="" class="left" style="margin-right:5px" />
            <p class="last-child"><%= xg_html('TAKING_YOU_TO_MAIN_PAGE', 'href="' . xnhtmlentities($this->signOutUrl) . '"', xnhtmlentities(XN_Application::load()->name)) %></p>
        </div>
    </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
