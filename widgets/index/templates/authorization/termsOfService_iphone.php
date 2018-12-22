<?php XG_IPhoneHelper::header(null, xg_text('TERMS_OF_SERVICE'), null, array('contentClass' => 'simple')); ?>
<div id="xg_body">
            <div class="xg_module xg_lightborder">
                <?php
                if ($this->hasCustomTermsOfService) {
                    $this->_widget->includePlugin('termsOfService');
                } else { ?>
                    <div class="xg_module_body pad">
                        <?php echo Index_AuthorizationHelper::termsOfServiceHtml($this->previousUrl); ?>
                    </div>
                <?php
                } ?>
            </div>
</div>
<?php xg_footer(NULL,array('contentClass' => 'simple')); ?>
