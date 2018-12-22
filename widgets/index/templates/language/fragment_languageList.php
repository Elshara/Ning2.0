<?php
/**
 * An unordered list of locales.
 *
 * @param $localesAndNames array  mapping of locale codes to their names, e.g., en_US => English (U.S.)
 */ ?>
<ul class="languages">
    <?php
    foreach ($localesAndNames as $locale => $name) { ?>
        <li class="clear">
            <span><%= $name %></span>
            <a href="<%= xnhtmlentities($this->_widget->buildUrl('language', 'edit', array('locale' => $locale))) %>" class="desc edit"><%= xg_html('EDIT') %></a>
            <?php
            if ($locale != XG_LOCALE && XG_LanguageHelper::isCustomLocale($locale)) {
                XG_App::ningLoaderRequire('xg.shared.PostLink'); ?>
                <a dojoType="PostLink" _url="<%= xnhtmlentities($this->_widget->buildUrl('language', 'delete', array('locale' => $locale, 'removeFromLocaleList' => 1, 'target' => $this->_widget->buildUrl('language', 'list')))) %>" _confirmTitle="<%= xg_html('DELETE_LANGUAGE') %>" _confirmQuestion="<%= xg_html('ARE_YOU_SURE_DELETE_LANGUAGE') %>" _confirmOkButtonText="<%= xg_html('DELETE') %>" href="#" class="desc delete"><%= xg_html('DELETE') %></a>
            <?php
            } ?>
        </li>
    <?php
    } ?>
</ul>
