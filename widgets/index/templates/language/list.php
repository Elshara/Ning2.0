<?php xg_header('manage', $title = xg_text('LANGUAGE_EDITOR')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <div class="xg_module">
				<%= xg_headline($title)%>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('EDIT_LANGUAGE_ON_NETWORK') %></h3>
                    <?php if (! XN_Profile::current()->isOwner()) { $linkStyle = 'style="display:none"'; } ?>
                    <p><%= xg_html('CUSTOMIZE_TEXT_ON_NETWORK_INCLUDING') %></p>
                    <p><%= xg_html('ACTIVE_LANGUAGE_IS_X', xnhtmlentities(XG_LanguageHelper::name(XG_LOCALE)), $linkStyle . ' href="' . xnhtmlentities($this->_widget->buildUrl('admin', 'appProfile')) . '"') %></p>
                    <h4><%= xg_html('CURRENT_LANGUAGE_OPTIONS') %></h4>
                    <?php $this->renderPartial('fragment_languageList', 'language', array('localesAndNames' => XG_LanguageHelper::nonCustomLocalesAndNames())); ?>
                    <?php
                    if (XG_LanguageHelper::customLocalesAndNames()) { ?>
                        <h4><%= xg_html('MY_LANGUAGES') %></h4>
                        <?php
                        $this->renderPartial('fragment_languageList', 'language', array('localesAndNames' => XG_LanguageHelper::customLocalesAndNames()));
                    } ?>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('CREATE_NEW_TRANSLATION') %></h3>
                    <p><%= xg_html('DONT_SEE_LANGUAGE_LISTED_CREATE', 'href="' . xnhtmlentities($this->_widget->buildUrl('language', 'new')) . '"') %></p>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('ADVANCED_IMPORT') %></h3>
                    <p><%= xg_html('IF_YOU_TRANSLATED_A_LANGUAGE', 'href="' . xnhtmlentities($this->_widget->buildUrl('language', 'upload')) . '"') %></p>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
