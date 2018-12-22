<?php xg_header(null, $title = xg_text('APPLICATION_TERMS_OF_SERVICE'), null, array('displayHeader' => false, 'xgDivClass' => 'account legal', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
    <div class="xg_module xg_lightborder">
        <div class="xg_module_body pad">
            <div class="easyclear">
                <h3 class="left"><%= xnhtmlentities($title) %></h3>
            </div>
            <?php echo Index_AuthorizationHelper::appTermsOfServiceHtml($this->previousUrl); ?>
        </div>
    </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>