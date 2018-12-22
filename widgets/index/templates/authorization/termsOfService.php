<?php xg_header(null, $title = xg_text('TERMS_OF_SERVICE'), null, array('displayHeader' => false, 'xgDivClass' => 'account legal', 'hideNingbar' => true, 'noHead' => true)); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <?php
                if ($this->hasCustomTermsOfService) {
                    $this->_widget->includePlugin('termsOfService');
                } else { ?>
                    <div class="xg_module_body pad">
                        <div class="easyclear">
                            <h3 class="left"><%= xnhtmlentities($title) %></h3>
                            <p class="right"><strong><%= $this->noBack ? '<a href="#" onclick="if(window.opener)window.opener.focus(); window.close();return false;">' . xg_html('CLOSE') . '</a>' : xg_html('LARR_BACK', 'href="' . xnhtmlentities($this->previousUrl) . '"') %></strong></p>
                        </div>
                        <?php echo Index_AuthorizationHelper::termsOfServiceHtml($this->previousUrl); ?>
                    </div>
                <?php
                } ?>
            </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
