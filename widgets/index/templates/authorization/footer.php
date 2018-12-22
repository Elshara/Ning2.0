<div class="xg_module_body account_foot">
    <h4><%= xg_html('ABOUT_X', xnhtmlentities(XN_Application::load()->name)) %></h4>
    <img class="right" src="<%= xnhtmlentities(XN_Application::load()->iconUrl(72, 72)) %>" alt="" />
    <?php
    if (is_array($this->profilesToDisplay) && count($this->profilesToDisplay)) { ?>
        <div class="thumbs">
            <?php
            $i = 0;
            foreach ($this->profilesToDisplay as $profileToDisplay) { ?>
                <img class="<%= $i == 0 ? 'first-child ' : '' %>photo" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($profileToDisplay, 40, 40)) %>" height="40" width="40" alt="" />
                <?php
                $i++;
            } ?>
        </div>
    <?php
    } ?>
    <p class="small description"><%= xnhtmlentities(XG_MetatagHelper::appDescription()) %></p>
    <p class="small last-child ningid xg_lightfont">
        <%= xg_html('WE_USE_NING_ID', 'href="' . xnhtmlentities($this->_buildUrl('authorization', 'ningId', array('previousUrl' => XG_HttpHelper::currentUrl()))) . '" class="xg_lightfont"') %>
    </p>
</div>
